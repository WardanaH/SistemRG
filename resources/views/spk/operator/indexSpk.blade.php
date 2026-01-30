@extends('spk.layout.app')

@section('content')

{{-- SweetAlert --}}
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
@if (session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER ORANGE (WARNING/ONGOING) --}}
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
                                <input type="text"
                                    name="search"
                                    class="form-control border-0 ps-2"
                                    placeholder="Cari SPK..."
                                    value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                <a href="{{ route('spk.index') }}" class="text-danger d-flex align-items-center cursor-pointer">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail Produksi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catatan</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spks as $spk)
                            <tr>
                                {{-- Kolom 1: SPK --}}
                                <td class="ps-3">
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-warning d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">precision_manufacturing</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2: Pelanggan --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->nama_pelanggan }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $spk->no_telepon }}</p>
                                </td>

                                {{-- Kolom 3: Detail File & Bahan --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width: 150px;">{{ $spk->nama_file }}</p>
                                    <span class="text-xs text-secondary">
                                        {{ $spk->bahan->nama ?? '-' }}
                                        ({{ $spk->ukuran_panjang }}x{{ $spk->ukuran_lebar }})
                                    </span>
                                    <div class="text-xs text-secondary">Qty: <strong>{{ $spk->kuantitas }}</strong> | Fin: {{ $spk->finishing ?? '-' }}</div>
                                </td>

                                {{-- Kolom 4: Status Badge --}}
                                <td class="align-middle text-center text-sm">
                                    @php
                                    $badges = [
                                    'pending' => 'warning',
                                    'ripping' => 'info',
                                    'ongoing' => 'info',
                                    'finishing' => 'info',
                                    'done' => 'success'
                                    ];
                                    @endphp
                                    <span class="badge badge-sm bg-gradient-{{ $badges[$spk->status_produksi] ?? 'secondary' }}">
                                        {{ ucfirst($spk->status_produksi) }}
                                    </span>
                                </td>

                                {{-- Kolom 5: Catatan --}}
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs">
                                        {{ Str::limit($spk->catatan_operator ?? '-') }}
                                    </span>
                                </td>

                                {{-- Kolom 6: Aksi --}}
                                <td class="align-middle text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">

                                        {{-- Tombol Cetak --}}
                                        <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="badge bg-gradient-primary text-white text-xs" data-toggle="tooltip" title="Cetak SPK" style="text-decoration: none;">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">print</i>
                                        </a>

                                        {{-- Tombol Update Status --}}
                                        <button type="button"
                                            class="badge bg-gradient-info border-0 text-white text-xs btn-update-status"
                                            data-id="{{ $spk->id }}"
                                            data-no="{{ $spk->no_spk }}"
                                            data-status="{{ $spk->status_produksi }}"
                                            data-catatan="{{ $spk->catatan_operator }}"
                                            data-toggle="tooltip"
                                            title="Update Produksi">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">edit_note</i> Status
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="material-icons text-secondary text-4xl mb-2">assignment_turned_in</i>
                                        <h6 class="text-secondary font-weight-normal">Tidak ada antrian produksi saat ini.</h6>
                                    </div>
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

{{-- MODAL UPDATE STATUS PRODUKSI --}}
<form id="formUpdateStatus" method="POST" action="">
    @csrf @method('PUT')
    <div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal">Update Produksi: <span id="txtNoSpk" class="font-weight-bold"></span></h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    {{-- Dropdown Status --}}
                    <div class="input-group input-group-outline mb-4 is-filled">
                        <label class="form-label">Status Produksi</label>
                        <select name="status_produksi" id="selectStatusProduksi" class="form-control" style="appearance: auto; padding-left: 10px;">
                            <option value="pending">Pending</option>
                            <option value="ripping">Ripping (Persiapan)</option>
                            <option value="ongoing">Ongoing (Cetak)</option>
                            <option value="finishing">Finishing</option>
                            <option value="done">Done (Selesai)</option>
                        </select>
                    </div>

                    {{-- Textarea Catatan --}}
                    <div class="input-group input-group-outline">
                        <label class="form-label"></label>
                        <textarea name="catatan_operator" id="txtCatatanOperator" class="form-control" rows="3"></textarea>
                    </div>
                    <small class="text-xs text-muted ms-1">*Catatan ini akan tersimpan di riwayat SPK.</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-success">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btns = document.querySelectorAll(".btn-update-status");
        const modal = new bootstrap.Modal(document.getElementById('modalUpdateStatus'));
        const form = document.getElementById('formUpdateStatus');

        const txtNoSpk = document.getElementById('txtNoSpk');
        const selectStatus = document.getElementById('selectStatusProduksi');
        const txtCatatan = document.getElementById('txtCatatanOperator');

        btns.forEach(btn => {
            btn.addEventListener("click", function() {
                // Ambil Data dari Atribut Tombol
                let id = this.getAttribute('data-id');
                let no = this.getAttribute('data-no');
                let status = this.getAttribute('data-status');
                let catatan = this.getAttribute('data-catatan');

                // Isi Data ke Modal
                txtNoSpk.innerText = no;
                selectStatus.value = status;
                txtCatatan.value = catatan ? catatan : '';

                // Handle Input Outline Animation (Material Dashboard)
                // Agar label tidak menumpuk saat ada isi
                if (catatan) {
                    txtCatatan.parentElement.classList.add('is-filled');
                } else {
                    txtCatatan.parentElement.classList.remove('is-filled');
                }

                // Update Action URL
                // Pastikan route ini ada di web.php
                let url = "{{ route('spk.update-produksi', ':id') }}";
                form.action = url.replace(':id', id);

                modal.show();
            });
        });
    });
</script>
@endpush
