@extends('distribusi.layouts.app')

@section('title', 'Laporan Per Barang')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Laporan Distribusi Barang</h3>
        </div>
        <div class="card-body">
            {{-- Form Pencarian --}}
            <form action="{{ route('gudang-pusat.laporan.barang') }}" method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-9 mb-3">
                        <label class="form-label text-sm fw-bold">Cari Nama Barang</label>
                        <input type="text" name="search" class="form-control border px-3 py-2"
                            placeholder="Contoh: flexy 280..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0 px-4 py-2">
                            <i class="fas fa-search me-1"></i> Cari Barang
                        </button>
                    </div>
                </div>
            </form>

            {{-- KOTAK TOTAL (Tampil amun ada pencarian) --}}
            @if (request('search'))
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 bg-gradient-info text-white">
                            <div class="card-body p-3 text-center">
                                <h6 class="text-white mb-0">Total Diminta Cabang</h6>
                                <h3 class="text-white mb-0 mt-2">{{ $totalDiminta }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                            <div class="card-body p-3 text-center">
                                <h6 class="text-white mb-0">Total Dikirim Pusat</h6>
                                <h3 class="text-white mb-0 mt-2">{{ $totalDikirim }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 bg-gradient-success text-white">
                            <div class="card-body p-3 text-center">
                                <h6 class="text-white mb-0">Total Diterima Cabang</h6>
                                <h3 class="text-white mb-0 mt-2">{{ $totalDiterima }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- REKAP PER CABANG --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 text-sm">Rekapitulasi Per Cabang</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                        Cabang
                                    </th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Total Diminta</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Total Dikirim</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Total Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapPerCabang as $namaCabang => $data)
                                    <tr>
                                        <td class="text-sm fw-bold ps-3">{{ $namaCabang }}</td>
                                        <td class="text-sm text-center">{{ $data->diminta }}</td>
                                        <td class="text-sm text-center text-primary fw-bold">{{ $data->dikirim }}</td>
                                        <td class="text-sm text-center text-success fw-bold">{{ $data->diterima }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-sm">Belum ada data rekap cabang.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- TABEL RINCIAN TRANSAKSI --}}
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-dark">
                    <h6 class="text-white mb-0">Rincian Transaksi Barang</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        No
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cabang
                                        Peminta
                                    </th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Diminta</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Dikirim</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporan as $index => $item)
                                    <tr>
                                        <td class="text-sm text-center align-middle">{{ $laporan->firstItem() + $index }}
                                        </td>
                                        <td class="text-sm align-middle">
                                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                        <td class="text-sm fw-bold align-middle">
                                            {{ $item->barangPusat->nama_bahan ?? '-' }}
                                        </td>
                                        <td class="text-sm align-middle">
                                            {{ optional($item->permintaan->gudang)->nama ?? '-' }}
                                        </td>
                                        <td class="text-sm text-center align-middle fw-bold">{{ $item->jumlah_diminta }}
                                        </td>
                                        <td class="text-sm text-center align-middle text-primary fw-bold">
                                            {{ $item->jumlah_disetujui ?? 0 }}</td>
                                        <td class="text-sm text-center align-middle text-success fw-bold">
                                            {{ $item->jumlah_diterima ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-sm">Barang kada ditemukan atau balum
                                            ada
                                            transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $laporan->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
