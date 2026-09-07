@extends('distribusi.layouts.app')

@section('title', 'Laporan Distribusi Barang')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Laporan Permintaan Barang</h3>
        </div>
        <div class="card-body">
            {{-- FORM FILTER & PENCARIAN TERPADU --}}
            <form action="{{ route('gudang-pusat.laporan') }}" method="GET" class="mb-4">

                <div class="row align-items-end mb-3">
                    <!-- Pencarian Kode Permintaan -->
                    <div class="col-md-12">
                        <label class="form-label text-sm fw-bold">Cari Kode Permintaan</label>
                        <input type="text" name="search" class="form-control border px-3 py-2"
                            placeholder="Contoh: REQ-20260903..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="row align-items-end">
                    <!-- Filter Dropdown -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-sm fw-bold">Periode Laporan</label>
                        <select name="periode" id="periode" class="select2 form-select border px-3 py-2"
                            onchange="toggleCustomDate()">
                            <option value="1_bulan" {{ request('periode') == '1_bulan' ? 'selected' : '' }}>1 Bulan Terakhir
                            </option>
                            <option value="3_bulan" {{ request('periode') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir
                            </option>
                            <option value="6_bulan" {{ request('periode') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir
                            </option>
                            <option value="1_tahun" {{ request('periode') == '1_tahun' ? 'selected' : '' }}>1 Tahun Terakhir
                            </option>
                            <option value="custom" {{ request('periode') == 'custom' ? 'selected' : '' }}>Pilih Tanggal
                                Sendiri</option>
                        </select>
                    </div>

                    <!-- Custom Date -->
                    <div class="col-md-6 mb-3" id="custom_date_wrapper"
                        style="display: {{ request('periode') == 'custom' ? 'block' : 'none' }};">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <label class="form-label text-sm fw-bold">Dari</label>
                                <input type="date" name="start_date" class="form-control border px-3 py-2"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="mt-4">-</div>
                            <div>
                                <label class="form-label text-sm fw-bold">Sampai</label>
                                <input type="date" name="end_date" class="form-control border px-3 py-2"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Filter & Cari -->
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0 px-4 py-2">
                            <i class="fas fa-search me-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            {{-- TABEL DATA PERMINTAAN --}}
            <div class="table-responsive mt-3">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode Permintaan
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pemohon</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaan as $item)
                            <tr>
                                <td class="text-sm text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="text-sm align-middle">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                <td class="text-sm fw-bold align-middle">{{ $item->kode_permintaan }}</td>
                                <td class="text-sm align-middle">{{ $item->gudang->nama ?? '-' }}</td>
                                <td class="text-sm text-center align-middle">
                                    @if ($item->status_permintaan === 'menunggu_acc')
                                        <span class="badge bg-warning text-dark">Menunggu ACC</span>
                                    @elseif ($item->status_permintaan == 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($item->status_permintaan == 'dikirim')
                                        <span class="badge bg-primary">Dikirim</span>
                                    @elseif ($item->status_permintaan == 'diterima')
                                        <span class="badge bg-success">Diterima</span>
                                    @elseif ($item->status_permintaan == 'tidak_lengkap')
                                        <span class="badge bg-danger">Tidak Lengkap</span>
                                    @elseif ($item->status_permintaan == 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-info mb-0 btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalDetail{{ $item->id }}">
                                        <i class="material-icons-round">visibility</i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm py-4">Tidak ada data laporan untuk periode
                                    yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $permintaan->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL PERMINTAAN LOOP --}}
    @foreach ($permintaan as $item)
        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info">
                        <h5 class="modal-title text-white">Detail Permintaan: {{ $item->kode_permintaan }}</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-sm mb-1"><strong>Pemohon:</strong> {{ $item->gudang->nama ?? '-' }}</p>
                                <p class="text-sm mb-1"><strong>Tanggal:</strong>
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d F Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="text-sm mb-1"><strong>Status:</strong> {{ $item->status }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama Barang</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Jml Diminta</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Jml Dikirim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->detailPermintaan as $detail)
                                        <tr>
                                            <td class="text-sm text-center align-middle">{{ $loop->iteration }}</td>
                                            <td class="text-sm ps-3 align-middle">
                                                {{ $detail->barangPusat->nama_bahan ?? 'Barang Dihapus' }}</td>
                                            <td class="text-sm text-center align-middle fw-bold">
                                                {{ $detail->jumlah_diminta }}</td>
                                            <td class="text-sm text-center align-middle text-primary fw-bold">
                                                {{ $detail->jumlah_disetujui ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary mb-0"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        function toggleCustomDate() {
            let periode = document.getElementById('periode').value;
            let customDateWrapper = document.getElementById('custom_date_wrapper');

            if (periode === 'custom') {
                customDateWrapper.style.display = 'block';
            } else {
                customDateWrapper.style.display = 'none';
            }
        }

        // Jalankan sekali saat halaman dimuat amun nyawa merefresh pas lagi milih custom
        document.addEventListener('DOMContentLoaded', function() {
            toggleCustomDate();
        });
    </script>
@endpush
