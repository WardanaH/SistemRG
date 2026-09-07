@extends('distribusi.layouts.app') {{-- Sesuaikan lawan layout gudang pusat nyawa --}}

@section('title', 'Permintaan Masuk')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="mb-0">Daftar Permintaan Masuk dari Cabang</h3>
        </div>
        <div class="card-body">
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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cabang</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode Request
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tgl Request
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permintaan as $item)
                            <tr>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0 ps-2">
                                        {{ $item->gudang->nama ?? 'Cabang Kada Diketahui' }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $item->kode_permintaan }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($item->tanggal_request)->format('d M Y, H:i') }}</p>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm mb-0" data-bs-toggle="modal"
                                        data-bs-target="#modalProses{{ $item->id }}">
                                        <i class="fas fa-edit"></i> Proses ACC
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <p class="text-xs font-weight-bold mb-0">Kadada permintaan hanyar nang masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL PROSES ACC --}}
    @foreach ($permintaan as $item)
        <div class="modal fade" id="modalProses{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <form action="{{ route('gudang-pusat.permintaan.proses', $item->id) }}" method="POST"
                        class="form-proses-acc">
                        @csrf
                        <div class="modal-header bg-gradient-primary">
                            <h5 class="modal-title text-white">Proses Permintaan: {{ $item->kode_permintaan }}</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-sm"><strong>Pemohon:</strong> {{ $item->gudang->nama ?? '-' }}</p>
                            <p class="text-sm"><strong>Keterangan Cabang:</strong>
                                {{ $item->keterangan ?? 'Kadada keterangan' }}</p>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama Barang</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                                Stok Cabang</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                                Jml Diminta</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                                Keterangan Barang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->detailPermintaan as $detail)
                                            <tr>
                                                <td class="text-sm ps-3 align-middle">
                                                    {{ $detail->barangPusat->nama_bahan ?? 'Barang Dihapus' }}</td>
                                                <td class="text-sm text-center align-middle">
                                                    {{ $detail->sisa_stok_cabang }}</td>
                                                <td class="text-sm text-center align-middle font-weight-bold">
                                                    {{ $detail->jumlah_diminta }}</td>
                                                <td class="text-sm text-center align-middle">
                                                    <div class="text-wrap mx-auto text-center" style="width: 200px;">
                                                        {{ $detail->keterangan_barang ?? '-' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="submit" name="action" value="tolak"
                                class="btn btn-outline-danger mb-0 btn-tolak">Tolak Permintaan</button>
                            <div>
                                <button type="button" class="btn bg-gradient-secondary mb-0"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="action" value="acc"
                                    class="btn bg-gradient-primary mb-0 btn-acc">ACC & Kirim Barang</button>
                            </div>
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
            // Konfirmasi SweetAlert gasan ACC / Tolak
            $('.btn-acc').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                // Sisipkan input hidden action=acc supaya tabaca di controller
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'acc'
                }).appendTo(form);

                Swal.fire({
                    title: 'Setujui & Kirim Barang?',
                    text: "Barang akan dikirim ke cabang.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#42a5f5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ACC & Kirim!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('.btn-tolak').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: 'tolak'
                }).appendTo(form);

                Swal.fire({
                    title: 'Tolak Permintaan?',
                    text: "Permintaan ngini bakal dibatalkan sepenuhnya.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal',
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
