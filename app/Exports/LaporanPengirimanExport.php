<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPengirimanExport implements FromView, ShouldAutoSize
{
    protected $pengiriman;
    protected $rekap;
    protected $semuaCabang;
    protected $bulan;
    protected $tahun;

    public function __construct($pengiriman, $rekap, $semuaCabang, $bulan, $tahun)
    {
        $this->pengiriman = $pengiriman;
        $this->rekap = $rekap;
        $this->semuaCabang = $semuaCabang;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        return view('inventaris.gudangpusat.laporan_excel', [
            'pengiriman' => $this->pengiriman,
            'rekap' => $this->rekap,
            'semuaCabang' => $this->semuaCabang,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }
}
