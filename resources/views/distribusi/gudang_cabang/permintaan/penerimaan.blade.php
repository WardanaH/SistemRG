@extends('distribusi.layouts.app')

@section('title', 'Penerimaan Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Daftar Barang Dalam Perjalanan (Siap Diterima)</h3>
        </div>
        <div class="card-body">
            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success text-white">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-white">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode
                                Permintaan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tgl Dikirim
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Total Item</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permintaan as $index => $item)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 ps-2">{{ $index + 1 }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $item->kode_permintaan }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y, H:i') }}</p>
                                </td>
                                <td class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">{{ $item->detailPermintaan->count() }} Macam
                                        Barang</p>
                                </td>
                                <td class="text-center">
                                    {{-- Tombol Detail --}}
                                    <button type="button" class="btn btn-info btn-sm mb-0 me-1" data-bs-toggle="modal"
                                        data-bs-target="#modalDetail{{ $item->id }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>

                                    {{-- Tombol Buka Modal Terima --}}
                                    <button type="button" class="btn btn-success btn-sm mb-0" data-bs-toggle="modal"
                                        data-bs-target="#modalTerima{{ $item->id }}">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <p class="text-xs font-weight-bold mb-0 py-3">Kadada barang nang sadang dikirim ka
                                        cabang ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =======================================
     MODAL DETAIL & MODAL TERIMA
     ======================================= --}}
    @foreach ($permintaan as $item)
        {{-- 1. Modal Detail Biasa (Malihat Haja) --}}
        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cek Detail Barang: {{ $item->kode_permintaan }}</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                            Barang</th>
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
                                            <td class="text-sm ps-3">
                                                {{ $detail->barangPusat->nama_bahan ?? 'Barang Dihapus' }}</td>
                                            <td class="text-sm text-center">{{ $detail->jumlah_diminta }}</td>
                                            <td class="text-sm text-center font-weight-bold text-success">
                                                {{ $detail->jumlah_disetujui }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Modal Form Penerimaan (Input Fisik) --}}
        <div class="modal fade" id="modalTerima{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('gudang-cabang.permintaan.terima', $item->id) }}" method="POST"
                        class="form-konfirmasi-terima">
                        @csrf
                        <div class="modal-header bg-gradient-success">
                            <h5 class="modal-title text-white">Input Fisik Barang: {{ $item->kode_permintaan }}</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Pemohon:</strong> {{ $item->gudang->nama ?? 'Cabang Dihapus' }}</p>
                            <p><strong>Keterangan Cabang:</strong> {{ $item->keterangan ?? 'Kadada keterangan' }}</p>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama Barang</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                                Jml Diminta</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                                Jml Dikirim</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                                style="width: 150px;">Barang Diterima <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->detailPermintaan as $detail)
                                            <tr>
                                                <td class="text-sm ps-3 align-middle">
                                                    {{ $detail->barangPusat->nama_bahan ?? 'Barang Dihapus' }}</td>
                                                <td class="text-sm text-center align-middle">
                                                    {{ $detail->jumlah_diminta }}</td>
                                                <td class="text-sm text-center align-middle font-weight-bold">
                                                    {{ $detail->jumlah_disetujui }}</td>
                                                <td class="text-center">
                                                    {{-- Hidden input gasan ID detailnya --}}
                                                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">

                                                    {{-- Input fisik barang nang diterima --}}
                                                    <div class="input-group input-group-outline">
                                                        <input type="number" name="jumlah_diterima[]"
                                                            class="form-control text-center"
                                                            value="{{ $detail->jumlah_disetujui }}" min="0"
                                                            required>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-sm text-dark mb-3">Pastikan Jumlah barang dikirim sesuai dengan jumlah diminta. Jika kurang dari jumlah yang diminta maka status permintaan akan berubah menjadi <span class="text-danger">Tidak Lengkap</span>.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn bg-gradient-success">Simpan & Terima</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.form-konfirmasi-terima').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'Konfirmasi Pengiriman?',
                    text: "Pastikan jumlah barang dikirim sesuai dengan jumlah diminta.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Konfirmasi!',
                    cancelButtonText: 'Cek Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
