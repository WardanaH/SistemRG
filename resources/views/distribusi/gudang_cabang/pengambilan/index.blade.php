@extends('distribusi.layouts.app')

@section('title', 'Riwayat Pengambilan Barang')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center">
            <h6 class="text-white mb-0">Riwayat Pengambilan Barang</h6>
            <a href="{{ route('gudang-cabang.pengambilan.create') }}" class="btn btn-sm btn-light mb-0">
                <i class="fas fa-plus me-1"></i> Input Baru
            </a>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <!-- TABEL UTAMA -->
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nomor
                                Pengambilan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cabang Pengambil
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat as $index => $item)
                            <tr>
                                <td class="text-sm text-center align-middle">{{ $riwayat->firstItem() + $index }}</td>
                                <td class="text-sm fw-bold align-middle">{{ $item->nomor_pengambilan }}</td>
                                <td class="text-sm align-middle">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</td>
                                <td class="text-sm align-middle">{{ $item->cabang->nama ?? '-' }}</td>
                                <td class="text-sm text-center align-middle">
                                    <button type="button" class="btn btn-sm bg-gradient-info mb-0" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $item->id }}">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-sm">Balum ada riwayat pengambilan barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3 px-3">
                {{ $riwayat->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- KUMPULAN MODAL (Posisinya di luar dari Card/Table) -->
    @foreach ($riwayat as $item)
        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary">
                        <h5 class="modal-title text-white">Detail Pengambilan: {{ $item->nomor_pengambilan }}</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-xs font-weight-bold">Nama Barang</th>
                                        <th class="text-xs font-weight-bold">Ukuran</th>
                                        <th class="text-xs font-weight-bold text-center">Jumlah</th>
                                        <th class="text-xs font-weight-bold">Atas Nama</th>
                                        <th class="text-xs font-weight-bold">Ambil Ke (Tempat)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($item->detail as $dt)
                                        <tr>
                                            <td class="text-sm">{{ $dt->nama_barang }}</td>
                                            <td class="text-sm">{{ $dt->ukuran_barang ?? '-' }}</td>
                                            <td class="text-sm text-center">{{ $dt->jumlah_barang }}</td>
                                            <td class="text-sm">{{ $dt->atas_nama ?? '-' }}</td>
                                            <td class="text-sm">{{ $dt->ambil_ke }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-sm">Kada ada detail barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
