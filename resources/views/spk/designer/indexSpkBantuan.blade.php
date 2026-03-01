@extends('spk.layout.app')

@section('content')

{{-- SWEETALERT --}}
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

            {{-- HEADER: JUDUL & TOMBOL BUAT --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 mb-3">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Daftar SPK Bantuan (Eksternal)</h6>

                        @hasrole('manajemen|designer')
                        <a href="{{ route('spk-bantuan') }}" class="btn btn-sm btn-white text-dark ms-3 mb-0 d-flex align-items-center">
                            <i class="material-icons text-sm me-1">add</i> Input Bantuan Baru
                        </a>
                        @endhasrole
                    </div>
                </div>
            </div>

            <div class="card-body px-3 pb-2">

                {{-- AREA FILTER & PENCARIAN --}}
                {{-- AREA FILTER & PENCARIAN --}}
                <form action="{{ route('spk-bantuan.index') }}" method="GET" class="mb-4 bg-gray-100 p-3 border-radius-lg">
                    <div class="row align-items-end">
                        {{-- Filter Tanggal Awal --}}
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="text-xs font-weight-bold mb-0">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control border px-2 text-sm bg-white" value="{{ request('start_date') }}">
                        </div>

                        {{-- Filter Tanggal Akhir --}}
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="text-xs font-weight-bold mb-0">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control border px-2 text-sm bg-white" value="{{ request('end_date') }}">
                        </div>

                        {{-- Filter Status --}}
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="text-xs font-weight-bold mb-0">Status SPK</label>
                            <select name="status_filter" class="form-control border px-2 text-sm bg-white" style="appearance: auto;">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="acc" {{ request('status_filter') == 'acc' ? 'selected' : '' }}>ACC</option>
                                <option value="rejected" {{ request('status_filter') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Filter Designer (BARU) --}}
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="text-xs font-weight-bold mb-0">Designer</label>
                            <select name="designer_filter" class="form-control border px-2 text-sm bg-white" style="appearance: auto;">
                                <option value="">Semua Designer</option>
                                @foreach($listDesigners as $des)
                                    <option value="{{ $des->id }}" {{ request('designer_filter') == $des->id ? 'selected' : '' }}>
                                        {{ $des->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kolom Pencarian --}}
                        <div class="col-md-2 col-sm-6 mb-2">
                            <label class="text-xs font-weight-bold mb-0">Pencarian</label>
                            <input type="text" name="search" class="form-control border px-2 text-sm bg-white" placeholder="No SPK / Nama..." value="{{ request('search') }}">
                        </div>

                        {{-- Tombol Submit / Reset --}}
                        <div class="col-md-2 col-sm-12 mb-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-info w-100 mb-0" title="Terapkan Filter">
                                <i class="material-icons text-sm">search</i>
                            </button>
                            @if(request('search') || request('start_date') || request('status_filter') || request('designer_filter'))
                            <a href="{{ route('spk-bantuan.index') }}" class="btn btn-sm btn-outline-danger w-100 mb-0" title="Reset Filter">
                                <i class="material-icons text-sm">close</i>
                            </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- TABEL DATA SPK --}}
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Asal Cabang</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Item</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Penerima (Designer)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status SPK</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                {{-- KOLOM 1: NO SPK --}}
                                <td class="ps-3">
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-secondary d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">handshake</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y H:i:s') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 2: ASAL CABANG (PENTING BUAT BANTUAN) --}}
                                <td>
                                    <h6 class="mb-0 text-sm font-weight-bold text-info">
                                        {{ $spk->cabangAsal->nama ?? 'Unknown' }}
                                    </h6>
                                </td>

                                {{-- KOLOM 3: PELANGGAN --}}
                                <td>
                                    <h6 class="mb-0 text-sm">{{ Str::limit($spk->nama_pelanggan, 20) }}</h6>
                                    @if($spk->no_telepon)
                                    <p class="text-xs text-secondary mb-0">{{ $spk->no_telepon }}</p>
                                    @endif
                                </td>

                                {{-- KOLOM 4: TOTAL ITEM --}}
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-info">{{ $spk->items_count ?? 0 }} Item</span>
                                </td>

                                {{-- KOLOM 5: DESIGNER (PENERIMA) --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->designer->nama ?? '-' }}</p>
                                </td>

                                {{-- KOLOM 6: STATUS SPK --}}
                                <td class="align-middle text-center text-sm">
                                    @if($spk->status_spk == 'pending')
                                    <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    @elseif($spk->status_spk == 'acc')
                                    <span class="badge badge-sm bg-gradient-success">Acc</span>
                                    @elseif($spk->status_spk == 'rejected')
                                    <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                                    @endif
                                </td>

                                {{-- KOLOM 8: AKSI --}}
                                <td class="align-middle text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">

                                        {{-- 1. LIHAT DETAIL --}}
                                        <a href="{{ route('spk-bantuan.show', $spk->id) }}" class="badge bg-gradient-info text-white text-xs" data-toggle="tooltip" title="Lihat Detail">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">visibility</i>
                                        </a>

                                        @hasrole('manajemen|admin')
                                        {{-- 2. UPDATE STATUS (TIDAK DIKUNCI) --}}
                                        <button type="button"
                                            class="badge bg-gradient-dark border-0 text-white text-xs btn-modal-status cursor-pointer"
                                            data-bs-toggle="modal" data-bs-target="#modalUpdateStatus"
                                            data-id="{{ $spk->id }}"
                                            data-no="{{ $spk->no_spk }}"
                                            data-status="{{ $spk->status_spk }}"
                                            data-toggle="tooltip" title="Ubah Status SPK">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">verified_user</i>
                                        </button>
                                        @endhasrole

                                        {{-- 3. CETAK --}}
                                        <a href="{{ route('spk-bantuan.cetak-spk-bantuan', $spk->id) }}" target="_blank" class="badge bg-gradient-primary text-white text-xs" data-toggle="tooltip" title="Cetak Nota">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">print</i>
                                        </a>

                                        @hasrole('manajemen|admin')
                                        {{-- 4. HAPUS --}}
                                        <form action="{{ route('spk.destroy', $spk->id) }}" method="POST" class="d-inline delete-form m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="badge bg-gradient-danger border-0 text-white text-xs btn-delete cursor-pointer" data-toggle="tooltip" title="Hapus">
                                                <i class="material-icons text-xs position-relative" style="top: 1px;">delete</i>
                                            </button>
                                        </form>
                                        @endhasrole

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <h6 class="text-secondary font-weight-normal">Belum ada data SPK Bantuan yang sesuai filter.</h6>
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

{{-- MODAL UPDATE STATUS --}}
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-labelledby="modalStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="modalStatusLabel">Update Status SPK Bantuan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form tanpa action dulu, diisi via JS --}}
            <form id="formUpdateStatus" method="POST" action="#">
                @csrf @method('PUT')
                <div class="modal-body">
                    <p class="text-sm mb-3">Update status untuk No. SPK: <strong id="spkNoDisplay"></strong></p>
                    <div class="input-group input-group-outline mb-3 is-filled">
                        <select name="status_spk" id="selectStatus" class="form-control" style="appearance: auto; padding-left: 10px;">
                            <option value="pending">Pending (Kembalikan ke awal)</option>
                            <option value="acc">ACC (Setujui)</option>
                            <option value="rejected">Reject (Tolak)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-success" onclick="this.closest('form').submit()">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. DELETE BUTTON LOGIC
        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");
                Swal.fire({
                    title: "Hapus SPK Bantuan ini?",
                    text: "Seluruh data item di dalamnya akan ikut terhapus!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // 2. MODAL STATUS LOGIC (Fix URL Replacement)
        const modalElement = document.getElementById('modalUpdateStatus');
        if (modalElement) {
            // Gunakan placeholder 'PH_ID' agar aman dari url encoding
            const urlTemplate = "{{ route('manajemen.spk.update-status', 'PH_ID') }}";

            document.querySelectorAll(".btn-modal-status").forEach(btn => {
                btn.addEventListener("click", function() {
                    let id = this.getAttribute('data-id');
                    let no = this.getAttribute('data-no');
                    let status = this.getAttribute('data-status');

                    // Isi Teks
                    document.getElementById('spkNoDisplay').textContent = no;
                    document.getElementById('selectStatus').value = status;

                    // Replace placeholder dengan ID asli
                    let finalUrl = urlTemplate.replace('PH_ID', id);
                    document.getElementById('formUpdateStatus').action = finalUrl;
                });
            });
        }
    });
</script>
@endpush
