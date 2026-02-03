<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GudangCabangLaporanExport implements FromView, ShouldAutoSize
{
    protected $pengiriman, $rekap, $bulan, $tahun, $cabang;

    public function __construct($pengiriman, $rekap, $bulan, $tahun, $cabang)
    {
        $this->pengiriman = $pengiriman;
        $this->rekap      = $rekap;
        $this->bulan      = $bulan;
        $this->tahun      = $tahun;
        $this->cabang     = $cabang;
    }

    public function view(): View
    {
        return view('inventaris.gudangcabang.laporan.laporan_excel', [
            'pengiriman' => $this->pengiriman,
            'rekap'      => $this->rekap,
            'bulan'      => $this->bulan,
            'tahun'      => $this->tahun,
            'cabang'     => $this->cabang
        ]);
    }
}
