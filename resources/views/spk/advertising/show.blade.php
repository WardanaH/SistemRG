@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">
        {{-- CARD UTAMA --}}
        <div class="card my-4">

            {{-- HEADER: Info SPK & Pelanggan --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white text-capitalize mb-0">Detail SPK Advertising</h6>
                        <p class="text-white text-xs mb-0 opacity-8">No: {{ $spk->no_spk }} | Tgl: {{ $spk->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- INFO PELANGGAN --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="mb-1 text-dark font-weight-bold">Informasi Pelanggan</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Nama:</strong> &nbsp; {{ $spk->nama_pelanggan }}</li>
                            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">No. Telp:</strong> &nbsp; {{ $spk->no_telepon ?? '-' }}</li>
                            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Cabang:</strong> &nbsp; {{ $spk->cabang->nama }} (Pusat)</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-md-end text-start">
                        <h6 class="mb-1 text-dark font-weight-bold">Designer (PIC)</h6>
                        <p class="text-sm text-secondary mb-0">{{ $spk->designer->nama }}</p>

                        <div class="mt-3">
                            <a href="{{ route('advertising.print', $spk->id) }}" target="_blank" class="btn btn-outline-dark btn-sm mb-0">
                                <i class="material-icons text-sm me-1">print</i> Cetak Nota
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="dark horizontal my-4">

                {{-- TABEL ITEM PRODUKSI --}}
                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Detail Item & Progress Produksi</h6>

                <div class="table-responsive">
                    <table class="table align-items-center mb-0 table-striped">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">File & Catatan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Spesifikasi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catatan Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Produksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spk->items as $index => $item)
                            <tr>
                                <td class="ps-3 text-xs">{{ $index + 1 }}</td>

                                {{-- Kolom File --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm">{{ $item->nama_file }}</h6>
                                        <span class="text-xs text-secondary">Note: {{ $item->catatan ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- Kolom Spesifikasi --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-xs font-weight-bold mb-0">
                                            {{ ucfirst($item->jenis_order) }}
                                        </span>
                                        <span class="text-xs text-secondary">
                                            {{ $item->p }} x {{ $item->l }} cm | {{ $item->bahan->nama_bahan ?? '-' }}
                                        </span>
                                        <span class="text-xxs text-secondary">
                                            Finishing: {{ $item->finishing }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Kolom Qty --}}
                                <td class="align-middle text-center text-sm">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $item->qty }}</span>
                                </td>

                                {{-- Kolom Operator --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2 bg-gradient-dark rounded-circle">
                                            <span class="text-white text-xxs">{{ substr($item->operator->nama ?? 'X', 0, 1) }}</span>
                                        </div>
                                        <span class="text-xs font-weight-bold">{{ $item->operator->nama ?? 'Belum dipilih' }}</span>
                                    </div>
                                </td>

                                {{-- Kolom Catatan Operator --}}
                                <td class="align-middle text-center">
                                    <span class="text-xs font-weight-bold">
                                        {{ $item->catatan_operator ?? '-' }}
                                    </span>
                                </td>

                                {{-- Kolom Status Produksi (Live) --}}
                                <td class="align-middle text-center">
                                    @php
                                        $statusColor = [
                                            'pending' => 'secondary',
                                            'ripping' => 'warning', // Sedang diproses file
                                            'ongoing' => 'info',    // Sedang cetak
                                            'finishing'=> 'primary', // Sedang finishing
                                            'completed'=> 'success', // Selesai
                                        ];
                                        $statusLabel = [
                                            'pending' => 'Menunggu',
                                            'ripping' => 'Persiapan',
                                            'ongoing' => 'Sedang Cetak',
                                            'finishing'=> 'Finishing',
                                            'completed'=> 'Selesai',
                                        ];
                                    @endphp
                                    <span class="badge badge-sm bg-gradient-{{ $statusColor[$item->status_produksi] ?? 'secondary' }}">
                                        {{ $statusLabel[$item->status_produksi] ?? strtoupper($item->status_produksi) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- TOMBOL BACK --}}
                <div class="row mt-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('advertising.dashboard') }}" class="btn btn-secondary mb-0">Kembali</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
