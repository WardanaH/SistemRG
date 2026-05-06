<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\MTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // Helper function KECIL HANYA untuk memudahkan penentuan siklus 27-26
    private function getSiklus($date)
    {
        if ($date->day >= 27) {
            $start = $date->copy()->startOfDay()->day(27);
            $end   = $date->copy()->addMonth()->endOfDay()->day(26);
        } else {
            $start = $date->copy()->subMonth()->startOfDay()->day(27);
            $end   = $date->copy()->endOfDay()->day(26);
        }
        return ['start' => $start, 'end' => $end];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // --- 1. SETUP FILTER TANGGAL (SIKLUS 27-26) ---
        $filterType = $request->input('filter_type', 'bulan_ini');
        $now = Carbon::now();

        // Default: Siklus bulan berjalan
        $siklusSekarang = $this->getSiklus($now);
        $startDate = $siklusSekarang['start'];
        $endDate   = $siklusSekarang['end'];

        switch ($filterType) {
            case 'bulan_ini':
                // Sudah diset di default atas
                break;
            case 'tri_wulan':
                // Tarik start date mundur 2 siklus (total 3 siklus)
                $startDate = $this->getSiklus($now->copy()->subMonths(2))['start'];
                break;
            case 'semester':
                // Tarik start date mundur 5 siklus (total 6 siklus)
                $startDate = $this->getSiklus($now->copy()->subMonths(5))['start'];
                break;
            case 'tahun_ini':
                // Dari 27 Des tahun lalu s/d 26 Des tahun ini
                $startDate = Carbon::create($now->year - 1, 12, 27)->startOfDay();
                $endDate   = Carbon::create($now->year, 12, 26)->endOfDay();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate   = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        // --- 2. INISIALISASI VARIABEL ---
        $designers = collect();
        $admins    = collect();
        $operators = collect();

        // --- 3. HELPER CLOSURE UNTUK QUERY ---
        $mapUserData = function ($users, $roleType) use ($startDate, $endDate) {
            return $users->map(function ($u) use ($startDate, $endDate, $roleType) {

                if ($roleType == 'designer') {
                    // A. Capaian Input SPK Normal
                    $u->capaian = MSpk::where('designer_id', $u->id)
                        ->whereBetween('created_at', [$startDate, $endDate])->count();
                    $targetType = 'input';

                    // B. TAMBAHAN: Laporan Charge Desain Khusus Designer
                    // Hitung jumlah item yang jenisnya 'charge' dan total nominal harganya
                    $chargeData = MSubSpk::whereHas('spk', function ($q) use ($u, $startDate, $endDate) {
                        $q->where('designer_id', $u->id)
                            ->whereBetween('created_at', [$startDate, $endDate]);
                    })
                        ->where('jenis_order', 'charge')
                        ->selectRaw('COUNT(id) as total_item, SUM(harga) as total_nominal')
                        ->first();

                    $u->charge_count = $chargeData->total_item ?? 0;
                    $u->charge_nominal = $chargeData->total_nominal ?? 0;
                } elseif ($roleType == 'admin') {
                    $u->capaian = MSpk::where('admin_id', $u->id)
                        ->where('status_spk', 'acc')
                        ->whereBetween('updated_at', [$startDate, $endDate])->count();
                    $targetType = 'acc';
                } else { // Operator
                    $u->capaian = MSubSpk::where('operator_id', $u->id)
                        ->where('status_produksi', 'done')
                        ->whereBetween('updated_at', [$startDate, $endDate])->count();
                    $targetType = 'produksi';
                }

                // C. Hitung Target Bulanan
                $u->target = MTarget::where('user_id', $u->id)
                    ->where('jenis_target', $targetType)
                    ->whereBetween('bulan', [$startDate, $endDate])
                    ->sum('jumlah');

                // D. Hitung Persentase
                if ($u->target > 0) {
                    $u->persentase = round(($u->capaian / $u->target) * 100);
                } else {
                    $u->persentase = 0;
                }

                return $u;
            });
        };

        // --- 4. LOGIKA PENGAMBILAN DATA BERDASARKAN ROLE LOGIN ---
        if ($user->hasRole(['admin', 'manajemen'])) {
            $rawDesigners = User::role('designer')->get();
            $designers = $mapUserData($rawDesigners, 'designer');

            $rawAdmins = User::role(['admin', 'manajemen'])->get();
            $admins = $mapUserData($rawAdmins, 'admin');

            // Include DTF if necessary
            $rawOperators = User::role(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])->get();
            $operators = $mapUserData($rawOperators, 'operator');
        } elseif ($user->hasRole('designer')) {
            $designers = $mapUserData(collect([$user]), 'designer');
        } elseif ($user->hasRole(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])) {
            $operators = $mapUserData(collect([$user]), 'operator');
        }

        return view('spk.laporan.index', [
            'title'      => 'Laporan Kinerja & Target',
            'designers'  => $designers,
            'admins'     => $admins,
            'operators'  => $operators,
            'filterType' => $filterType,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }

    // --- METHOD SIMPAN TARGET (Hanya untuk Admin/Manajemen) ---
    public function storeTarget(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan'   => 'required|date_format:Y-m', // Format input type="month"
            'jumlah'  => 'required|integer|min:1',
            'jenis'   => 'required|in:input,acc,produksi'
        ]);

        // Ubah format YYYY-MM menjadi Tanggal 27 di bulan tersebut
        // Ini agar cocok dengan pencarian whereBetween('bulan', [$startDate, $endDate]) di atas
        $date = Carbon::createFromFormat('Y-m', $request->bulan)->startOfDay()->day(27);

        MTarget::updateOrCreate(
            [
                'user_id'      => $request->user_id,
                'jenis_target' => $request->jenis,
                'bulan'        => $date,
            ],
            [
                'jumlah'       => $request->jumlah
            ]
        );

        return back()->with('success', 'Target berhasil diperbarui!');
    }

    public function storeTargetByRole(Request $request)
    {
        $request->validate([
            'role_target' => 'required', // designer, admin, atau operator
            'bulan'       => 'required|date_format:Y-m',
            'jumlah'      => 'required|integer|min:1',
        ]);

        $date = Carbon::createFromFormat('Y-m', $request->bulan)->startOfDay()->day(27);
        $targetAmount = $request->jumlah;
        $users = collect();
        $jenisTarget = '';

        // 1. Tentukan User & Jenis Target berdasarkan Role yang dipilih
        switch ($request->role_target) {
            case 'designer':
                $users = User::role('designer')->get();
                $jenisTarget = 'input';
                break;

            case 'admin':
                // Target Admin & Manajemen biasanya sama (ACC)
                $users = User::role(['admin', 'manajemen'])->get();
                $jenisTarget = 'acc';
                break;

            case 'operator':
                // Ambil semua jenis operator
                $users = User::role(['operator indoor', 'operator outdoor', 'operator multi'])->get();
                $jenisTarget = 'produksi';
                break;
        }

        // 2. Loop Semua User & Simpan Target
        foreach ($users as $user) {
            MTarget::updateOrCreate(
                [
                    'user_id'      => $user->id,
                    'jenis_target' => $jenisTarget,
                    'bulan'        => $date,
                ],
                [
                    'jumlah'       => $targetAmount
                ]
            );
        }

        return back()->with('success', "Berhasil mengatur target untuk " . $users->count() . " pegawai role " . ucfirst($request->role_target) . "!");
    }

    // 1. HELPER FUNGSI UNTUK MENGAMBIL QUERY CHARGE (Agar Web, PDF, & Excel filternya sama)
    private function getChargeQueryData(Request $request)
    {
        $user = Auth::user();
        $filterType = $request->input('filter_type', 'bulan_ini');
        $now = Carbon::now();

        // Terapkan Logika 27-26 di sini juga
        $siklusSekarang = $this->getSiklus($now);
        $startDate = $siklusSekarang['start'];
        $endDate   = $siklusSekarang['end'];

        switch ($filterType) {
            case 'tri_wulan':
                $startDate = $this->getSiklus($now->copy()->subMonths(2))['start'];
                break;
            case 'semester':
                $startDate = $this->getSiklus($now->copy()->subMonths(5))['start'];
                break;
            case 'tahun_ini':
                $startDate = Carbon::create($now->year - 1, 12, 27)->startOfDay();
                $endDate   = Carbon::create($now->year, 12, 26)->endOfDay();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate   = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        $query = MSubSpk::with(['spk', 'spk.designer'])
            ->where('jenis_order', 'charge')
            ->whereHas('spk', function ($q) use ($user, $startDate, $endDate, $request) {
                // Filter Tanggal
                $q->whereBetween('created_at', [$startDate, $endDate]);

                // --- 1. FILTER BERDASARKAN ROLE / CABANG ---
                if ($user->hasRole('designer')) {
                    // Designer hanya bisa melihat miliknya sendiri
                    $q->where('designer_id', $user->id);
                } elseif ($user->hasRole('admin') && $user->cabang->jenis !== 'pusat') {
                    // Admin hanya bisa melihat data di cabangnya sendiri
                    $q->where('cabang_id', $user->cabang_id);
                }
                // Manajemen (Pusat) otomatis lolos tanpa pembatasan cabang/designer

                // --- 2. FILTER BERDASARKAN INPUT DROPDOWN DESIGNER ---
                if ($request->filled('designer_filter')) {
                    $q->where('designer_id', $request->designer_filter);
                }
            });

        return compact('query', 'startDate', 'endDate', 'filterType');
    }

    // 2. TAMPILAN HALAMAN WEB
    public function laporanCharge(Request $request)
    {
        $data = $this->getChargeQueryData($request);

        $totalItem = $data['query']->count();
        $totalNominal = $data['query']->sum('harga');
        $items = $data['query']->latest()->paginate(20);

        // --- AMBIL DAFTAR DESIGNER UNTUK DROPDOWN FILTER ---
        $user = Auth::user();
        $listDesigners = collect(); // Default kosong (untuk desainer)

        // Hanya ambil daftar desainer jika role admin/manajemen
        if ($user->hasRole(['admin', 'manajemen'])) {
            $designersQuery = \App\Models\User::role('designer');

            // Jika admin cabang, batas daftar desainer hanya yang ada di cabangnya
            if ($user->hasRole('admin') && $user->cabang->jenis !== 'pusat') {
                $designersQuery->where('cabang_id', $user->cabang_id);
            }
            $listDesigners = $designersQuery->get();
        }

        return view('spk.laporan.charge', [
            'title'         => 'Laporan Pendapatan Charge Desain',
            'items'         => $items,
            'totalItem'     => $totalItem,
            'totalNominal'  => $totalNominal,
            'filterType'    => $data['filterType'],
            'startDate'     => $data['startDate'],
            'endDate'       => $data['endDate'],
            'listDesigners' => $listDesigners, // Kirim daftar designer ke view
        ]);
    }

    // 3. FUNGSI DOWNLOAD PDF
    public function exportChargePdf(Request $request)
    {
        $data = $this->getChargeQueryData($request);

        $items = $data['query']->latest()->get(); // Get semua (tanpa paginate)
        $totalItem = $items->count();
        $totalNominal = $items->sum('harga');

        $viewData = [
            'items' => $items,
            'totalItem' => $totalItem,
            'totalNominal' => $totalNominal,
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate']
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('spk.laporan.export_charge', $viewData);
        return $pdf->download('Laporan_Charge_Desain_' . $data['startDate']->format('d-M-Y') . '.pdf');
    }

    // 4. FUNGSI DOWNLOAD EXCEL
    public function exportChargeExcel(Request $request)
    {
        $data = $this->getChargeQueryData($request);

        $items = $data['query']->latest()->get();
        $totalItem = $items->count();
        $totalNominal = $items->sum('harga');

        $viewData = [
            'items' => $items,
            'totalItem' => $totalItem,
            'totalNominal' => $totalNominal,
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate']
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ChargeExport($viewData),
            'Laporan_Charge_Desain_' . $data['startDate']->format('d-M-Y') . '.xlsx'
        );
    }

    // LAPORAN BAHAN BAKU
    public function laporanBahanBaku(Request $request)
    {
        // 1. Tangkap input dropdown 'rentang', defaultnya 'bulan_ini'
        $rentang = $request->input('rentang', 'bulan_ini');
        $now = Carbon::now();

        // 2. Set Default (Bulan Ini) pakai siklus 27-26
        $siklusSekarang = $this->getSiklus($now);
        $startDate = $siklusSekarang['start'];
        $endDate   = $siklusSekarang['end'];

        // 3. Logika untuk mengubah tanggal berdasarkan pilihan dropdown
        switch ($rentang) {
            case '3_bulan':
                // Mundur 2 siklus + siklus saat ini = 3 bulan
                $startDate = $this->getSiklus($now->copy()->subMonths(2))['start'];
                break;
            case '6_bulan':
                // Mundur 5 siklus + siklus saat ini = 6 bulan
                $startDate = $this->getSiklus($now->copy()->subMonths(5))['start'];
                break;
            case 'tahun_ini':
                $startDate = Carbon::create($now->year - 1, 12, 27)->startOfDay();
                $endDate   = Carbon::create($now->year, 12, 26)->endOfDay();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate   = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        // 4. Query Database berdasarkan tanggal yang sudah difilter dan digabung per cabang
        $dataLaporan = MSubSpk::join('m_spks', 'm_sub_spks.spk_id', '=', 'm_spks.id')
            ->join('m_cabangs', 'm_spks.cabang_id', '=', 'm_cabangs.id') // Tambahkan Join ke tabel cabang
            ->with('bahan') // Cukup load relasi bahan saja
            ->select(
                'm_cabangs.nama as nama_cabang', // Langsung ambil nama cabang dari database
                'm_sub_spks.bahan_id',
                DB::raw('SUM(CASE WHEN m_sub_spks.p > 0 AND m_sub_spks.l > 0 THEN (m_sub_spks.p * m_sub_spks.l * m_sub_spks.qty) / 10000 ELSE 0 END) as total_meter'),
                DB::raw('SUM(CASE WHEN m_sub_spks.p <= 0 OR m_sub_spks.l <= 0 OR m_sub_spks.p IS NULL OR m_sub_spks.l IS NULL THEN m_sub_spks.qty ELSE 0 END) as total_pcs')
            )
            ->whereNotNull('m_sub_spks.bahan_id')
            ->whereBetween('m_sub_spks.created_at', [$startDate, $endDate])
            ->groupBy('m_cabangs.nama', 'm_sub_spks.bahan_id')
            ->get()
            ->groupBy('nama_cabang'); // Langsung kelompokkan pakai nama cabang
            // dd($dataLaporan);
        // 5. Kembalikan ke View
        return view('spk.laporan.laporanBahanBaku', [
            'title'       => 'Laporan Penggunaan Bahan Baku',
            'dataLaporan' => $dataLaporan,
            'startDate'   => $startDate->format('Y-m-d'),
            'endDate'     => $endDate->format('Y-m-d')
        ]);
    }

    // LAPORAN KINERJA DESAINER (DETAIL SPK & SUB SPK)
    public function laporanKinerjaDesainerDetail(Request $request)
    {
        $user = Auth::user();

        // 1. Tangkap input dropdown 'rentang', defaultnya 'bulan_ini'
        $rentang = $request->input('rentang', 'bulan_ini');
        $now = Carbon::now();

        // 2. Set Default (Bulan Ini) pakai siklus 27-26
        $siklusSekarang = $this->getSiklus($now);
        $startDate = $siklusSekarang['start'];
        $endDate   = $siklusSekarang['end'];

        // 3. Logika Filter Tanggal
        switch ($rentang) {
            case '3_bulan':
                $startDate = $this->getSiklus($now->copy()->subMonths(2))['start'];
                break;
            case '6_bulan':
                $startDate = $this->getSiklus($now->copy()->subMonths(5))['start'];
                break;
            case 'tahun_ini':
                $startDate = Carbon::create($now->year - 1, 12, 27)->startOfDay();
                $endDate   = Carbon::create($now->year, 12, 26)->endOfDay();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate   = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        // 4. Batasi siapa yang bisa dilihat berdasarkan Role
        if ($user->hasRole(['admin', 'manajemen'])) {
            $designersQuery = User::role('designer');
            // Jika admin cabang, hanya tampilkan desainer cabangnya saja
            if ($user->hasRole('admin') && $user->cabang->jenis !== 'pusat') {
                $designersQuery->where('cabang_id', $user->cabang_id);
            }
            $rawDesigners = $designersQuery->get();
        } elseif ($user->hasRole('designer')) {
            // Desainer cuma bisa lihat datanya sendiri
            $rawDesigners = collect([$user]);
        } else {
            // Role lain (misal operator) tidak punya akses data desainer, kembalikan kosong
            $rawDesigners = collect();
        }

        // 5. Olah data (Hitung SPK Induk dan Sub SPK per Jenis Order)
        $dataDesainer = $rawDesigners->map(function ($u) use ($startDate, $endDate) {

            // Hitung Total SPK Induk
            $u->total_spk_induk = MSpk::where('designer_id', $u->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Hitung Detail Sub SPK (dikelompokkan berdasarkan jenis_order)
            $subSpks = MSubSpk::whereHas('spk', function ($q) use ($u, $startDate, $endDate) {
                    $q->where('designer_id', $u->id)
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->select('jenis_order', DB::raw('count(*) as total'))
                ->groupBy('jenis_order')
                ->pluck('total', 'jenis_order');

            // Masukkan data sub spk ke object user (gunakan fallback 0 jika tidak ada)
            $u->indoor  = $subSpks['indoor'] ?? 0;
            $u->outdoor = $subSpks['outdoor'] ?? 0;
            $u->multi   = $subSpks['multi'] ?? 0;
            $u->dtf     = $subSpks['dtf'] ?? 0;
            $u->charge  = $subSpks['charge'] ?? 0;

            // Total Keseluruhan Item (Sub SPK)
            $u->total_sub_spk = $u->indoor + $u->outdoor + $u->multi + $u->dtf + $u->charge;

            return $u;
        });

        // 6. Kembalikan ke View
        return view('spk.laporan.kinerja_desainer', [
            'title'        => 'Detail Kinerja Desainer',
            'dataDesainer' => $dataDesainer,
            'startDate'    => $startDate->format('Y-m-d'),
            'endDate'      => $endDate->format('Y-m-d'),
        ]);
    }
}
