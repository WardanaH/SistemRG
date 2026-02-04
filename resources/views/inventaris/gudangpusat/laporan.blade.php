@extends('inventaris.layouts.app')

@section('title', 'Laporan Pengiriman')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12">
            <div class="card my-4">

                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Laporan Pengiriman Barang
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">

                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">
                                        Bulan
                                    </th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">
                                        Tahun
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($laporan as $row)
                                <tr>

                                    {{-- BULAN --}}
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-info d-flex align-items-center justify-content-center">
                                                    <i class="material-icons text-white text-sm">event</i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ \Carbon\Carbon::create()->month((int) $row->bulan)->translatedFormat('F') }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    Bulan Laporan
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- TAHUN --}}
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                    <i class="material-icons text-white text-sm">calendar_today</i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $row->tahun }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    Tahun Laporan
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
                                        <a href="{{ route('laporan.pengiriman.detail', [$row->bulan, $row->tahun]) }}"
                                        class="btn btn-sm bg-gradient-primary">
                                            <i class="material-icons text-sm">visibility</i>
                                            Detail
                                        </a>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Belum ada data laporan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div>
                                Menampilkan {{ $laporan->firstItem() }} - {{ $laporan->lastItem() }}
                                dari {{ $laporan->total() }} data
                            </div>
                            <div>
                                {{ $laporan->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
