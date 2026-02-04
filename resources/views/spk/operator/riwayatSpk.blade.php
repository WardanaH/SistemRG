@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER HIJAU (SUKSES/SELESAI) --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Riwayat Produksi Bantuan Selesai</h6>
                    </div>

                    {{-- SEARCH BAR --}}
                    <div>
                        <form action="{{ route('spk.riwayat') }}" method="GET">
                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>
                                <input type="text"
                                    name="search"
                                    class="form-control border-0 ps-2"
                                    placeholder="Cari SPK Selesai..."
                                    value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                <a href="{{ route('spk.riwayat') }}" class="text-danger d-flex align-items-center cursor-pointer">
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tgl Order</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail Produksi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                {{-- Kolom 1: SPK --}}
                                <td class="ps-3">
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-success d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">inventory</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2: Pelanggan --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->nama_pelanggan }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $spk->no_telepon }}</p>
                                </td>

                                {{-- Kolom 3: Detail --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width: 150px;">{{ $spk->nama_file }}</p>
                                    <span class="text-xs text-secondary">
                                        {{ $spk->bahan->nama ?? '-' }}
                                        ({{ $spk->ukuran_panjang }}x{{ $spk->ukuran_lebar }})
                                    </span>
                                    <div class="text-xs text-secondary">Qty: <strong>{{ $spk->kuantitas }}</strong></div>
                                </td>

                                {{-- Kolom 4: Operator --}}
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{ Str::limit($spk->operator->name ?? '-', 15) }}
                                    </span>
                                </td>

                                {{-- Kolom 5: Status Badge --}}
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-gradient-success">Selesai</span>
                                </td>

                                {{-- Kolom 6: Aksi --}}
                                <td class="align-middle text-end pe-4">
                                    <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="badge bg-gradient-primary text-white text-xs" data-toggle="tooltip" title="Cetak SPK" style="text-decoration: none;">
                                        <i class="material-icons text-xs position-relative" style="top: 1px;">print</i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
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
                {{ $spks->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
