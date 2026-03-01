<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\MTarget; // Pastikan model ini sudah dibuat
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // --- 1. SETUP FILTER TANGGAL ---
        $filterType = $request->input('filter_type', 'bulan_ini');
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        switch ($filterType) {
            case 'bulan_ini':
                $startDate = Carbon::now()->startOfMonth();
                $endDate   = Carbon::now()->endOfMonth();
                break;
            case 'tri_wulan':
                $startDate = Carbon::now()->subMonths(3)->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
                break;
            case 'semester':
                $startDate = Carbon::now()->subMonths(6)->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
                break;
            case 'tahun_ini':
                $startDate = Carbon::now()->startOfYear();
                $endDate   = Carbon::now()->endOfYear();
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
                    $chargeData = MSubSpk::whereHas('spk', function($q) use ($u, $startDate, $endDate) {
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
        }
        elseif ($user->hasRole('designer')) {
            $designers = $mapUserData(collect([$user]), 'designer');
        }
        elseif ($user->hasRole(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])) {
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

        // Ubah format YYYY-MM menjadi Tanggal 1 bulan tersebut
        $date = Carbon::createFromFormat('Y-m', $request->bulan)->startOfMonth();

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

        $date = Carbon::createFromFormat('Y-m', $request->bulan)->startOfMonth();
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
    // 1. HELPER FUNGSI UNTUK MENGAMBIL QUERY CHARGE
    private function getChargeQueryData(Request $request)
    {
        $user = Auth::user();
        $filterType = $request->input('filter_type', 'bulan_ini');
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        switch ($filterType) {
            case 'tri_wulan':
                $startDate = Carbon::now()->subMonths(3)->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
                break;
            case 'semester':
                $startDate = Carbon::now()->subMonths(6)->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
                break;
            case 'tahun_ini':
                $startDate = Carbon::now()->startOfYear();
                $endDate   = Carbon::now()->endOfYear();
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
            'title'        => 'Laporan Pendapatan Charge Desain',
            'items'        => $items,
            'totalItem'    => $totalItem,
            'totalNominal' => $totalNominal,
            'filterType'   => $data['filterType'],
            'startDate'    => $data['startDate'],
            'endDate'      => $data['endDate'],
            'listDesigners'=> $listDesigners, // Kirim daftar designer ke view
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
        return $pdf->download('Laporan_Charge_Desain_'.$data['startDate']->format('d-M-Y').'.pdf');
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
            'Laporan_Charge_Desain_'.$data['startDate']->format('d-M-Y').'.xlsx'
        );
    }
}
