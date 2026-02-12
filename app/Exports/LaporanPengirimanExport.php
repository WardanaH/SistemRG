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
    protected $filterPeriode;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $bulan;
    protected $tahun;
    protected $transaksi;

    public function __construct(
        $transaksi,
        $rekap,
        $semuaCabang,
        $filterPeriode = null,
        $tanggalAwal = null,
        $tanggalAkhir = null,
        $bulan = null,
        $tahun = null
    ){
        $this->transaksi = $transaksi;
        $this->rekap = $rekap;
        $this->semuaCabang = $semuaCabang;
        $this->filterPeriode = $filterPeriode;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        return view('inventaris.gudangpusat.laporan_excel', [
            'transaksi'      => $this->transaksi,
            'rekap'           => $this->rekap,
            'semuaCabang'     => $this->semuaCabang,
            'filterPeriode'   => $this->filterPeriode,
            'tanggal_awal'    => $this->tanggalAwal,
            'tanggal_akhir'   => $this->tanggalAkhir,
            'bulan'           => $this->bulan,
            'tahun'           => $this->tahun,
        ]);
    }
}
