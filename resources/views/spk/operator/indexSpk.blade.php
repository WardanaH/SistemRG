@extends('spk.layout.app')

@section('content')

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({ icon: "success", title: "Berhasil!", text: "{{ session('success') }}", showConfirmButton: false, timer: 1500 });
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Antrian Produksi (Ongoing)</h6>
                    </div>

                    {{-- SEARCH BAR --}}
                    <div>
                        <form action="{{ route('spk.index') }}" method="GET">
                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>
                                <input type="text" name="search" class="form-control border-0 ps-2" placeholder="Cari SPK..." value="{{ request('search') }}" style="box-shadow: none !important; height: 100%; background: transparent;">
                                @if(request('search'))
                                    <a href="{{ route('spk.index') }}" class="text-danger d-flex align-items-center cursor-pointer"><i class="material-icons text-sm">close</i></a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail File</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Bahan & Ukuran</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Finishing</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $spk->nama_pelanggan }}</p>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->nama_file }}</p>
                                    <p class="text-xs text-secondary mb-0">Qty: {{ $spk->kuantitas }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->bahan->nama_bahan ?? '-' }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $spk->ukuran_panjang }} x {{ $spk->ukuran_lebar }} cm</p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-gradient-{{ $spk->jenis_order_spk == 'outdoor' ? 'success' : ($spk->jenis_order_spk == 'indoor' ? 'info' : 'warning') }}">
                                        {{ ucfirst($spk->jenis_order_spk) }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $spk->finishing ?? '-' }}</span>
                                </td>
                                <td class="align-middle text-end pe-4">

                                    {{-- TOMBOL LIHAT NOTA --}}
                                    <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="btn btn-link text-dark px-2 mb-0" title="Lihat Detail">
                                        <i class="material-icons text-sm">visibility</i>
                                    </a>

                                    {{-- TOMBOL SELESAI (TRIGGER MODAL) --}}
                                    <button type="button"
                                            class="btn btn-sm bg-gradient-success mb-0 btn-selesai"
                                            data-id="{{ $spk->id }}"
                                            data-no="{{ $spk->no_spk }}">
                                        <i class="material-icons text-sm me-1">check_circle</i> Selesai
                                    </button>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <h6 class="text-secondary font-weight-normal">Tidak ada antrian produksi saat ini.</h6>
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

{{-- MODAL KONFIRMASI SELESAI --}}
<form id="formSelesai" method="POST" action="">
    @csrf @method('PUT')
    <div class="modal fade" id="modalSelesai" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Produksi Selesai</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <i class="material-icons text-success text-5xl mb-3">task_alt</i>
                    <p>Apakah Anda yakin pesanan <strong><span id="txtNoSpk"></span></strong> sudah selesai dicetak/dikerjakan?</p>
                    <p class="text-xs text-secondary">Status akan berubah menjadi 'Done' dan menghilang dari daftar ini.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-success">Ya, Selesai</button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btns = document.querySelectorAll(".btn-selesai");
        const modal = new bootstrap.Modal(document.getElementById('modalSelesai'));
        const form = document.getElementById('formSelesai');
        const txtNoSpk = document.getElementById('txtNoSpk');

        btns.forEach(btn => {
            btn.addEventListener("click", function() {
                let id = this.getAttribute('data-id');
                let no = this.getAttribute('data-no');

                txtNoSpk.innerText = no;
                // Update action URL
                let url = "{{ route('spk.selesai', ':id') }}";
                form.action = url.replace(':id', id);

                modal.show();
            });
        });
    });
</script>
@endpush
