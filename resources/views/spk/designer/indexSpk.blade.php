@extends('spk.layout.app')

@section('content')

{{-- ========================= --}}
{{-- SWEETALERT SESSION ALERT  --}}
{{-- ========================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- ========================= --}}
            {{-- HEADER: JUDUL & PENCARIAN --}}
            {{-- ========================= --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">

                    {{-- Judul & Tombol Tambah --}}
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Tabel Data SPK</h6>

                        @hasrole('manajemen|designer')
                        <a href="{{ route('spk') }}" class="btn btn-sm btn-white text-primary ms-3 mb-0 d-flex align-items-center">
                            <i class="material-icons text-sm me-1">add</i> Buat Baru
                        </a>
                        @endhasrole
                    </div>

                    {{-- SEARCH BAR (Desain Fixed / Anti-Numpuk) --}}
                    <div>
                        <form action="{{ route('spk.index') }}" method="GET">
                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>
                                <input type="text"
                                    name="search"
                                    class="form-control border-0 ps-2"
                                    placeholder="Cari No SPK / Pelanggan..."
                                    value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                <a href="{{ route('spk.index') }}" class="text-danger d-flex align-items-center cursor-pointer" title="Reset">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ========================= --}}
            {{-- BODY: TABEL DATA SPK      --}}
            {{-- ========================= --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail Order</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis Order</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">P. Jawab</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status SPK</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Produksi</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                {{-- KOLOM 1: INFO SPK --}}
                                <td class="ps-3">
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-dark d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">receipt_long</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 2: PELANGGAN --}}
                                <td>
                                    <h6 class="mb-0 text-sm">{{ Str::limit($spk->nama_pelanggan, 20) }}</h6>
                                    <p class="text-xs text-secondary mb-0 d-flex align-items-center">
                                        <i class="fa fa-whatsapp text-success text-xs me-1"></i> {{ $spk->no_telepon }}
                                    </p>
                                </td>

                                {{-- KOLOM 3: DETAIL ORDER --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 text-dark">{{ Str::limit($spk->nama_file, 25) }}</p>
                                    <span class="text-xs text-secondary">
                                        {{ $spk->bahan->nama ?? '-' }}
                                        <span class="text-dark font-weight-bold">({{ $spk->ukuran_panjang }}x{{ $spk->ukuran_lebar }})</span>
                                    </span>
                                    <div class="text-xs text-secondary">Qty: <span class="font-weight-bold">{{ $spk->kuantitas }}</span></div>
                                </td>

                                {{-- KOLOM 4: BADGE JENIS ORDER --}}
                                <td class="align-middle text-center text-sm">
                                    @if($spk->jenis_order_spk == 'outdoor')
                                    <span class="badge badge-sm bg-gradient-success">Outdoor</span>
                                    @elseif($spk->jenis_order_spk == 'indoor')
                                    <span class="badge badge-sm bg-gradient-success">Indoor</span>
                                    @else
                                    <span class="badge badge-sm bg-gradient-success">Multi</span>
                                    @endif
                                </td>

                                {{-- KOLOM 5: PENANGGUNG JAWAB --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-xs text-secondary mb-0">
                                            <i class="material-icons text-xs me-1" style="font-size: 10px;">palette</i>
                                            {{ Str::limit($spk->designer->nama ?? '-', 20) }}
                                        </span>
                                        <span class="text-xs text-secondary mb-0">
                                            <i class="material-icons text-xs me-1" style="font-size: 10px;">print</i>
                                            {{ Str::limit($spk->operator->nama ?? '-', 20) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- KOLOM 6: Status SPK --}}
                                <td class="align-middle text-center text-sm">
                                    @if($spk->status_spk == 'pending')
                                    <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    @elseif($spk->status_spk == 'acc')
                                    <span class="badge badge-sm bg-gradient-success">Acc</span>
                                    @elseif($spk->status_spk == 'reject')
                                    <span class="badge badge-sm bg-gradient-danger">Batal</span>
                                    @endif
                                </td>

                                {{-- KOLOM 7: Status Produksi --}}
                                <td class="align-middle text-center text-sm">
                                    @if($spk->status_produksi == 'pending')
                                    <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    @elseif($spk->status_produksi == 'ongoing')
                                    <span class="badge badge-sm bg-gradient-info">Proses</span>
                                    @elseif($spk->status_produksi == 'done')
                                    <span class="badge badge-sm bg-gradient-success">Selesai</span>
                                    @endif
                                </td>

                                {{-- KOLOM 8: AKSI (BUTTONS) --}}
                                <td class="align-middle text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">

                                        @hasrole('manajemen|admin')
                                        {{-- TOMBOL 1: UPDATE STATUS (MODAL) --}}
                                        <button type="button"
                                            class="badge bg-gradient-success border-0 text-white text-xs btn-modal-status"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalUpdateStatus"
                                            data-id="{{ $spk->id }}"
                                            data-no="{{ $spk->no_spk }}"
                                            data-status="{{ $spk->status_spk }}"
                                            data-toggle="tooltip"
                                            title="Update Status Approval">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">verified</i> Status
                                        </button>
                                        @endhasrole

                                        {{-- TOMBOL 2: CETAK --}}
                                        <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="badge bg-gradient-primary text-white text-xs" data-toggle="tooltip" title="Cetak SPK" style="text-decoration: none;">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">print</i>
                                        </a>

                                        {{-- TOMBOL 3: EDIT DATA --}}
                                        <a href="{{ route('spk.edit', $spk->id) }}" class="badge bg-gradient-warning text-white text-xs" data-toggle="tooltip" title="Edit Data" style="text-decoration: none;">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">edit</i>
                                        </a>

                                        {{-- TOMBOL 4: HAPUS --}}
                                        <form action="{{ route('spk.destroy', $spk->id) }}" method="POST" class="d-inline delete-form m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="badge bg-gradient-danger border-0 text-white text-xs btn-delete cursor-pointer">
                                                <i class="material-icons text-xs position-relative" style="top: 1px;">delete</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-gray-200 rounded-circle mb-3">
                                            <i class="material-icons text-secondary text-3xl">receipt_long</i>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Belum ada data SPK.</h6>
                                        @if(request('search'))
                                        <p class="text-xs text-secondary">Data "{{ request('search') }}" tidak ditemukan.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER: PAGINATION --}}
            <div class="card-footer py-3">
                {{ $spks->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-labelledby="modalStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="modalStatusLabel">Update Status SPK</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUpdateStatus" method="POST" action="">
                @csrf
                @method('PUT') <div class="modal-body">
                    <p class="text-sm mb-3">Update status untuk No. SPK: <strong id="spkNoDisplay"></strong></p>

                    <div class="input-group input-group-outline mb-3 is-filled">
                        <select name="status_spk" id="selectStatus" class="form-control" style="appearance: auto; padding-left: 10px;">
                            <option value="pending">Pending</option>
                            <option value="acc">ACC (Setujui)</option>
                            <option value="reject">Reject (Tolak)</option>
                        </select>
                    </div>

                    {{--
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Catatan / Alasan (Opsional)</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                    --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Logic SweetAlert Delete (Kode Lama Anda)
        const deleteButtons = document.querySelectorAll(".btn-delete");
        deleteButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");
                Swal.fire({
                    title: "Hapus SPK ini?",
                    text: "Data tidak bisa dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // 2. LOGIC MODAL STATUS (BARU)
        const statusButtons = document.querySelectorAll(".btn-modal-status");
        const modalForm = document.getElementById('formUpdateStatus');
        const spkNoDisplay = document.getElementById('spkNoDisplay');
        const selectStatus = document.getElementById('selectStatus');

        statusButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                // Ambil data dari tombol
                let id = this.getAttribute('data-id');
                let no = this.getAttribute('data-no');
                let currentStatus = this.getAttribute('data-status');

                // Update Teks di Modal
                spkNoDisplay.textContent = no;
                selectStatus.value = currentStatus; // Set dropdown sesuai status sekarang

                // Update Action URL Form secara dinamis
                // Ganti '0' dengan ID yang sebenarnya
                let url = "{{ route('manajemen.spk.update-status', ':id') }}";
                url = url.replace(':id', id);
                modalForm.action = url;
            });
        });
    });
</script>
@endpush
