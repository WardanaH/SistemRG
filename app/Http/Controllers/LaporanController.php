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
        // Fungsi ini digunakan untuk menghitung data user agar kode tidak berulang
        $mapUserData = function ($users, $roleType) use ($startDate, $endDate) {
            return $users->map(function ($u) use ($startDate, $endDate, $roleType) {

                // A. Hitung Capaian (Actual)
                if ($roleType == 'designer') {
                    $u->capaian = MSpk::where('designer_id', $u->id)
                        ->whereBetween('created_at', [$startDate, $endDate])->count();
                    $targetType = 'input';
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

                // B. Hitung Target (Akumulasi target dalam rentang tanggal filter)
                // Misal filter 1 tahun, maka target Jan + Feb + ... + Des dijumlahkan
                $u->target = MTarget::where('user_id', $u->id)
                    ->where('jenis_target', $targetType)
                    ->whereBetween('bulan', [$startDate, $endDate])
                    ->sum('jumlah');

                // C. Hitung Persentase
                if ($u->target > 0) {
                    $u->persentase = round(($u->capaian / $u->target) * 100);
                } else {
                    $u->persentase = 0;
                }

                return $u;
            });
        };

        // --- 4. LOGIKA PENGAMBILAN DATA BERDASARKAN ROLE LOGIN ---

        // A. Jika ADMIN/MANAJEMEN (Lihat Semua)
        if ($user->hasRole(['admin', 'manajemen'])) {
            $rawDesigners = User::role('designer')->get();
            $designers = $mapUserData($rawDesigners, 'designer');

            $rawAdmins = User::role(['admin', 'manajemen'])->get();
            $admins = $mapUserData($rawAdmins, 'admin');

            $rawOperators = User::role(['operator indoor', 'operator outdoor', 'operator multi'])->get();
            $operators = $mapUserData($rawOperators, 'operator');
        }

        // B. Jika DESIGNER (Lihat Diri Sendiri)
        elseif ($user->hasRole('designer')) {
            $designers = $mapUserData(collect([$user]), 'designer');
        }

        // C. Jika OPERATOR (Lihat Diri Sendiri)
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
}
