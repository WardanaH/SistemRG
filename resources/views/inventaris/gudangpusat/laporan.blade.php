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
                                    <td>
                                        {{ \Carbon\Carbon::create()->month((int) $row->bulan)->translatedFormat('F') }}
                                    </td>
                                    <td>
                                        {{ $row->tahun }}
                                    </td>
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

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
