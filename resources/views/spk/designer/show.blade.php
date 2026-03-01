@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">

        {{-- TOMBOL KEMBALI & HEADER --}}
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm mb-0 me-3">
                <i class="material-icons text-sm">arrow_back</i> Kembali
            </a>
            <h5 class="mb-0 text-capitalize">Detail SPK: {{ $spk->no_spk }}</h5>
        </div>

        {{-- KARTU 1: INFORMASI PELANGGAN (HEADER) --}}
        <div class="card mb-4">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="material-icons text-sm me-1">person</i> Informasi Pelanggan</h6>

                    {{-- Status Badge Global --}}
                    <div>
                        <span class="badge bg-gradient-{{ $spk->status_spk == 'acc' ? 'success' : ($spk->status_spk == 'reject' ? 'danger' : 'warning') }}">
                            Status SPK: {{ strtoupper($spk->status_spk) }}
                        </span>
                    </div>
                </div>
                <hr class="horizontal dark mt-2 mb-0">
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-3">
                        <p class="text-xs mb-0 text-secondary font-weight-bold">Tanggal Order</p>
                        <h6 class="text-sm font-weight-normal">{{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d F Y') }}</h6>
                    </div>
                    <div class="col-md-3">
                        <p class="text-xs mb-0 text-secondary font-weight-bold">Nama Pelanggan</p>
                        <h6 class="text-sm font-weight-normal">{{ $spk->nama_pelanggan }}</h6>
                    </div>
                    <div class="col-md-3">
                        <p class="text-xs mb-0 text-secondary font-weight-bold">No. Telepon</p>
                        <h6 class="text-sm font-weight-normal">{{ $spk->no_telepon ?? '-' }}</h6>
                    </div>
                    <div class="col-md-3">
                        <p class="text-xs mb-0 text-secondary font-weight-bold">Designer (Admin)</p>
                        <h6 class="text-sm font-weight-normal">{{ $spk->designer->nama ?? 'User Terhapus' }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 2: DAFTAR ITEM (DETAIL) --}}
        <div class="card">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-0"><i class="material-icons text-sm me-1">shopping_cart</i> Daftar Item Pesanan</h6>
                    <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="btn btn-sm bg-gradient-primary mb-0">
                        <i class="material-icons text-sm">print</i> Cetak Nota
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">File & Catatan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Spesifikasi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis Order</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catatan Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Produksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spk->items as $index => $item)
                            <tr>
                                <td class="ps-3 text-sm text-secondary">{{ $index + 1 }}</td>

                                {{-- Kolom File --}}
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $item->nama_file }}</h6>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $item->catatan ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Kolom Spesifikasi --}}
                                <td>
                                    @if ($item->jenis_order === 'charge')
                                        {{-- Tampilan Jika Charge Desain --}}
                                        <p class="text-sm text-success font-weight-bold mb-0">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </p>
                                    @else
                                        {{-- Tampilan Jika Cetakan Fisik Normal --}}
                                        <p class="text-xs font-weight-bold mb-0">Bahan: {{ $item->bahan->nama_bahan ?? '-' }}</p>
                                        <p class="text-xs text-secondary mb-0">
                                            Ukuran: {{ $item->p }} x {{ $item->l }} cm
                                        </p>
                                        <p class="text-xs text-secondary mb-0">Fin: {{ $item->finishing ?? '-' }}</p>
                                    @endif
                                </td>

                                {{-- Kolom Qty --}}
                                <td class="align-middle text-center text-sm">
                                    <span class="text-dark font-weight-bold">{{ $item->qty }}</span>
                                </td>

                                {{-- Kolom Operator --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $item->operator->nama ?? 'Belum Ada' }}</h6>
                                            <p class="text-xs text-secondary mb-0">Operator</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom Jenis Order --}}
                                <td class="align-middle text-center text-sm">
                                    @if($item->jenis_order == 'outdoor') <span class="badge badge-sm bg-gradient-warning">OUTDOOR</span>
                                    @elseif($item->jenis_order == 'indoor') <span class="badge badge-sm bg-gradient-warning">INDOOR</span>
                                    @elseif($item->jenis_order == 'multi') <span class="badge badge-sm bg-gradient-warning">MULTI</span>
                                    @elseif($item->jenis_order == 'dtf') <span class="badge badge-sm bg-gradient-warning">DTF UV</span>
                                    @elseif($item->jenis_order == 'charge') <span class="badge badge-sm bg-gradient-warning">CHARGE DESAIN</span>
                                    @endif
                                </td>

                                {{-- Kolom Catatan Operator --}}
                                <td class="align-middle text-center text-sm">
                                    {{ $item->catatan_operator ?? '-' }}
                                </td>

                                {{-- Kolom Status Produksi --}}
                                <td class="align-middle text-center text-sm">
                                    @php
                                    $statusClass = 'secondary';
                                    if($item->status_produksi == 'pending') $statusClass = 'warning';
                                    elseif($item->status_produksi == 'ongoing' || $item->status_produksi == 'ripping') $statusClass = 'info';
                                    elseif($item->status_produksi == 'done') $statusClass = 'success';
                                    @endphp
                                    <span class="badge badge-sm bg-gradient-{{ $statusClass }}">
                                        {{ ucfirst($item->status_produksi) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Footer Total --}}
            <div class="card-footer text-end pt-0">
                <h6 class="mb-0 text-dark">Total Item: {{ $spk->items->count() }}</h6>
            </div>
        </div>

    </div>
</div>
@endsection
