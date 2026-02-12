@extends('spk.layout.app')

@section('content')

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({ icon: "success", title: "Berhasil", text: "{{ session('success') }}", showConfirmButton: false, timer: 1500 });
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            {{-- Header --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <h6 class="text-white text-capitalize mb-0">Master Data Finishing</h6>
                    <button type="button" class="btn btn-sm btn-white text-primary mb-0" data-bs-toggle="modal" data-bs-target="#modalFinishing">
                        <i class="material-icons text-sm">add</i> Tambah
                    </button>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nama Finishing</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finishings as $item)
                            <tr>
                                <td class="align-middle text-sm ps-3">{{ $loop->iteration }}</td>
                                <td class="ps-3">
                                    <h6 class="mb-0 text-sm">{{ $item->nama_finishing }}</h6>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-link text-warning mb-0 px-2 btn-edit"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_finishing }}"
                                            data-harga="{{ $item->harga }}">
                                        <i class="material-icons text-sm">edit</i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('manajemen.finishing.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-link text-danger mb-0 px-2 btn-delete">
                                            <i class="material-icons text-sm">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary text-sm">Belum ada data finishing.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH & EDIT (Satu Modal untuk Semua) --}}
<div class="modal fade" id="modalFinishing" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Finishing</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formFinishing" action="{{ route('manajemen.finishing.store') }}" method="POST">
                @csrf
                <div id="methodUpdate"></div> {{-- Tempat naruh @method('PUT') lewat JS --}}

                <div class="modal-body">
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Nama Finishing</label>
                        <input type="text" name="nama" id="inputNama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('modalFinishing');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('formFinishing');
        const modalTitle = document.getElementById('modalTitle');
        const inputNama = document.getElementById('inputNama');
        const inputHarga = document.getElementById('inputHarga');
        const methodDiv = document.getElementById('methodUpdate');

        // Reset modal saat ditutup (supaya kembali ke mode Tambah)
        modalElement.addEventListener('hidden.bs.modal', function () {
            form.action = "{{ route('manajemen.finishing.store') }}";
            modalTitle.innerText = "Tambah Finishing";
            inputNama.value = "";
            inputHarga.value = "";
            methodDiv.innerHTML = ""; // Hapus @method('PUT')
            inputNama.parentElement.classList.remove('is-filled');
            inputHarga.parentElement.classList.remove('is-filled');
        });

        // Klik Tombol Edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                let nama = this.getAttribute('data-nama');
                let harga = this.getAttribute('data-harga');

                // Ubah ke Mode Edit
                let url = "{{ route('manajemen.finishing.update', ':id') }}";
                form.action = url.replace(':id', id);
                modalTitle.innerText = "Edit Finishing";

                inputNama.value = nama;
                inputHarga.value = harga;

                // Tambahkan Method PUT
                methodDiv.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                // Fix tampilan label floating Material Dashboard
                inputNama.parentElement.classList.add('is-filled');
                inputHarga.parentElement.classList.add('is-filled');

                modal.show();
            });
        });

        // SweetAlert Delete
        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");
                Swal.fire({
                    title: "Hapus data ini?",
                    text: "Data tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
