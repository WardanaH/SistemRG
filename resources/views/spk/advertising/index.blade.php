@extends('spk.layout.app')
@section('content')

{{-- NOTIFIKASI SUKSES --}}
@if(session('success'))
<div class="alert alert-success text-white mb-3 fade show" role="alert">
    <span class="alert-icon align-middle"><i class="material-icons text-md">thumb_up</i></span>
    <span class="alert-text"><strong>Berhasil!</strong> {{ session('success') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- BAGIAN 1: STATISTIK CARDS --}}
<div class="row mb-4">
    {{-- Card 1: Total SPK --}}
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">receipt_long</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Total SPK Dibuat</p>
                    <h4 class="mb-0">{{ $stats['total_spk'] }}</h4>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
                <p class="mb-0 text-xs">Total SPK Advertising</p>
            </div>
        </div>
    </div>

    {{-- Card 2: Sedang Proses --}}
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">precision_manufacturing</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Item Sedang Proses</p>
                    <h4 class="mb-0">{{ $stats['item_proses'] }}</h4>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
                <p class="mb-0 text-xs">Menunggu / Sedang dicetak operator</p>
            </div>
        </div>
    </div>

    {{-- Card 3: Selesai --}}
    <div class="col-xl-4 col-sm-6">
        <div class="card">
            <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">check_circle</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Item Selesai</p>
                    <h4 class="mb-0">{{ $stats['item_selesai'] }}</h4>
                </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
                <p class="mb-0 text-xs">Siap diambil / dikirim</p>
            </div>
        </div>
    </div>
</div>

{{-- BAGIAN 2: TABEL SPK TERBARU --}}
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <h6 class="text-white text-capitalize mb-0">Daftar SPK Advertising</h6>

                    <div class="d-flex gap-2">
                        {{-- Search --}}
                        <form action="{{ route('advertising.dashboard') }}" method="GET" class="me-2">
                            <div class="input-group input-group-sm bg-white rounded px-2">
                                <span class="input-group-text border-0"><i class="material-icons text-body">search</i></span>
                                <input type="text" name="search" class="form-control border-0" placeholder="Cari SPK..." value="{{ request('search') }}">
                            </div>
                        </form>

                        {{-- Tombol Tambah --}}
                        <a href="{{ route('advertising.create') }}" class="btn btn-sm btn-white text-primary mb-0 d-flex align-items-center">
                            <i class="material-icons text-sm me-1">add</i> Buat SPK
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-3">No SPK / Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Pelanggan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jml Item</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-dark">
                                                <i class="material-icons text-white text-sm">campaign</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $spk->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0 text-sm">{{ $spk->nama_pelanggan }}</h6>
                                    @if($spk->no_telepon)
                                    <p class="text-xs text-secondary mb-0"><i class="fa fa-phone"></i> {{ $spk->no_telepon }}</p>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $spk->items_count }} Item</span>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <a href="{{ route('advertising.show', $spk->id) }}" class="text-secondary font-weight-bold text-xs me-3" data-toggle="tooltip" title="Lihat Detail">
                                        <i class="material-icons text-sm">visibility</i>
                                    </a>
                                    <a href="{{ route('advertising.print', $spk->id) }}" target="_blank" class="text-secondary font-weight-bold text-xs me-3" data-toggle="tooltip" title="Print SPK">
                                        <i class="material-icons text-sm">print</i>
                                    </a>
                                    {{-- Hapus hanya jika belum dikerjakan operator (opsional logic) --}}
                                    <form action="{{ route('advertising.destroy', $spk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus SPK ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="border-0 bg-transparent text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus">
                                            <i class="material-icons text-sm text-danger">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="material-icons opacity-6 text-4xl">folder_open</i>
                                        <h6 class="text-secondary mt-2">Belum ada SPK Advertising dibuat.</h6>
                                        <a href="{{ route('advertising.create') }}" class="btn btn-sm btn-info mt-2">Buat Sekarang</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="card-footer py-3">
                {{ $spks->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
