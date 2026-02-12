<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GudangCabangLaporanExport implements FromView, ShouldAutoSize
{
    protected $transaksi, $rekap, $bulan, $tahun, $cabang;

    public function __construct($transaksi, $rekap, $bulan, $tahun, $cabang)
    {
        $this->transaksi = $transaksi;
        $this->rekap     = $rekap;
        $this->bulan     = $bulan;
        $this->tahun     = $tahun;
        $this->cabang    = $cabang;
    }

    public function view(): View
    {
        return view('inventaris.gudangcabang.laporan.laporan_excel', [
            'transaksi' => $this->transaksi, 
            'rekap'     => $this->rekap,
            'bulan'     => $this->bulan,
            'tahun'     => $this->tahun,
            'cabang'    => $this->cabang,
        ]);
    }
}
