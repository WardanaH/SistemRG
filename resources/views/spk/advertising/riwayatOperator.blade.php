@extends('spk.layout.app')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            {{-- HEADER --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <h6 class="text-white text-capitalize mb-0">Riwayat Pekerjaan Saya (Advertising)</h6>

                    {{-- Tombol Kembali ke Antrean --}}
                    <a href="{{ route('advertising.produksi-index') }}" class="btn btn-sm btn-white text-success mb-0">
                        <i class="material-icons text-sm me-1">arrow_back</i> Kembali ke Antrean
                    </a>
                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0 table-hover">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-3">Waktu Selesai</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">File / SPK</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Spek & Bahan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                {{-- KOLOM 1: Waktu Selesai --}}
                                <td class="ps-3 align-middle">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm">{{ $item->updated_at->format('d M Y') }}</h6>
                                        <span class="text-xs text-secondary">{{ $item->updated_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>

                                {{-- KOLOM 2: File & SPK --}}
                                <td class="align-middle">
                                    <h6 class="mb-0 text-sm text-truncate" style="max-width: 250px;">{{ $item->nama_file }}</h6>
                                    <span class="text-xs text-secondary">
                                        {{ $item->spk->no_spk }} | {{ $item->spk->nama_pelanggan }}
                                    </span>
                                </td>

                                {{-- KOLOM 3: Spesifikasi --}}
                                <td class="align-middle">
                                    <div class="d-flex flex-column">
                                        <span class="text-xs font-weight-bold">{{ $item->jenis_order }}</span>
                                        <span class="text-xs text-dark">{{ $item->p }} x {{ $item->l }} cm</span>
                                        <span class="text-xs text-secondary">{{ $item->bahan->nama_bahan ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- KOLOM 4: Qty --}}
                                <td class="align-middle text-center">
                                    <h6 class="mb-0 text-sm">{{ $item->qty }}</h6>
                                </td>

                                {{-- KOLOM 5: Status Badge --}}
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-success">
                                        <i class="material-icons text-xxs me-1">check_circle</i> SELESAI
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="material-icons text-secondary text-4xl mb-2">history</i>
                                        <h6 class="text-secondary font-weight-normal">Belum ada riwayat pekerjaan selesai.</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
