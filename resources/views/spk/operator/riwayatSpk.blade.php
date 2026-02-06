@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER HIJAU (SUKSES/SELESAI) --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">{{ $title }}</h6>
                    </div>

                    {{-- SEARCH BAR --}}
                    <div>
                        {{-- CEK ROUTE: Jika sedang di halaman bantuan, action ke bantuan. Jika tidak, ke reguler --}}
                        <form action="{{ request()->routeIs(['spk-bantuan.riwayat', 'spk.riwayat']) ? route('spk-bantuan.riwayat') : route('spk.riwayat') }}" method="GET">

                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>

                                <input type="text" name="search" class="form-control border-0 ps-2"
                                    placeholder="Cari File / SPK..." value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                {{-- TOMBOL RESET JUGA HARUS DINAMIS --}}
                                <a href="{{ request()->routeIs(['spk-bantuan.riwayat', 'spk.riwayat']) ? route('spk-bantuan.riwayat') : route('spk.riwayat') }}"
                                    class="text-danger d-flex align-items-center cursor-pointer">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Info SPK (Parent)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">File Item</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Spesifikasi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Selesai</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                {{-- Kolom 1: INFO SPK PARENT --}}
                                <td class="ps-3">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm font-weight-bold">{{ $item->spk->no_spk }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $item->spk->nama_pelanggan }}</p>
                                        {{-- Tanda Bantuan jika ada --}}
                                        @if($item->spk->is_bantuan)
                                        <span class="badge badge-sm bg-gradient-info text-xxs mt-1 w-auto">BANTUAN</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 2: ITEM SELESAI --}}
                                <td>
                                    <h6 class="mb-0 text-sm text-truncate" style="max-width: 200px;">{{ $item->nama_file }}</h6>
                                    <div>
                                        @if($item->jenis_order == 'outdoor') <span class="badge badge-sm bg-gradient-warning text-xxs">OUT</span>
                                        @else <span class="badge badge-sm bg-gradient-success text-xxs">IN</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 3: SPESIFIKASI --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Bahan: {{ $item->bahan->nama_bahan ?? '-' }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $item->p }} x {{ $item->l }} cm</p>
                                </td>

                                {{-- Kolom 4: QTY --}}
                                <td class="align-middle text-center">
                                    <h6 class="mb-0 text-sm">{{ $item->qty }}</h6>
                                </td>

                                {{-- Kolom 5: STATUS --}}
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-gradient-success">Done</span>
                                </td>

                                {{-- Kolom 6: TANGGAL SELESAI (UPDATED AT) --}}
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{ $item->updated_at->format('d M Y H:i') }}
                                    </span>
                                </td>

                                {{-- Kolom 7: AKSI --}}
                                <td class="align-middle text-end pe-4">
                                    <a href="{{ $item->spk->is_bantuan ? route('spk-bantuan.cetak-spk-bantuan', $item->spk->id) : route('manajemen.spk.cetak-spk', $item->spk->id) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary mb-0"
                                        data-toggle="tooltip"
                                        title="Cetak Ulang Nota">
                                        <i class="material-icons text-sm">print</i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="material-icons text-secondary text-4xl mb-2">history</i>
                                        <h6 class="text-secondary font-weight-normal">Belum ada riwayat produksi selesai.</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer py-3">
                {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
