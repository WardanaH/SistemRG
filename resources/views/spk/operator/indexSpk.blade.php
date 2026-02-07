@extends('spk.layout.app')

@section('content')

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "{{ session('success') }}",
        timer: 1500,
        showConfirmButton: false
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER WARNA-WARNI SESUAI STATUS --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Antrian {{ $title }}</h6>
                    </div>

                    {{-- SEARCH BAR --}}
                    <div>
                        <form action="{{ route('spk.produksi') }}" method="GET">
                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>
                                <input type="text" name="search" class="form-control border-0 ps-2"
                                    placeholder="Cari File / No SPK..." value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Info SPK (Parent)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail Item (File)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Spesifikasi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Item</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                {{-- KOLOM 1: INFO SPK PARENT --}}
                                <td class="ps-3">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm text-primary font-weight-bold">{{ $item->spk->no_spk }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $item->spk->nama_pelanggan }}</p>
                                        <span class="text-xxs text-muted">
                                            {{ \Carbon\Carbon::parse($item->spk->tanggal_spk)->format('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- KOLOM 2: NAMA FILE & JENIS --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm text-truncate" style="max-width: 200px;">
                                            {{ $item->nama_file }}
                                        </h6>
                                        <div>
                                            {{-- Badge Jenis Order --}}
                                            @if($item->jenis_order == 'outdoor')
                                            <span class="badge badge-sm bg-gradient-warning text-xxs me-1">OUT</span>
                                            @else
                                            <span class="badge badge-sm bg-gradient-success text-xxs me-1">IN</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 3: SPESIFIKASI --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Bahan: {{ $item->bahan->nama_bahan ?? '-' }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $item->p }} x {{ $item->l }} cm</p>
                                    <p class="text-xs text-secondary mb-0">Fin: {{ $item->finishing ?? '-' }}</p>
                                </td>

                                {{-- KOLOM 4: QTY --}}
                                <td class="align-middle text-center">
                                    <h6 class="mb-0 text-sm">{{ $item->qty }}</h6>
                                </td>

                                {{-- KOLOM 5: STATUS ITEM --}}
                                <td class="align-middle text-center text-sm">
                                    @php
                                    $statusClass = 'secondary';
                                    if($item->status_produksi == 'pending') $statusClass = 'warning';
                                    elseif($item->status_produksi == 'ripping') $statusClass = 'info';
                                    elseif($item->status_produksi == 'ongoing') $statusClass = 'primary';
                                    elseif($item->status_produksi == 'finishing') $statusClass = 'info';
                                    elseif($item->status_produksi == 'done') $statusClass = 'success';
                                    @endphp
                                    <span class="badge badge-sm bg-gradient-{{ $statusClass }}">
                                        {{ ucfirst($item->status_produksi) }}
                                    </span>
                                </td>

                                {{-- KOLOM 6: AKSI --}}
                                <td class="align-middle text-end pe-4">
                                    <button type="button"
                                        class="btn btn-sm btn-info btn-update-status mb-0"
                                        data-id="{{ $item->id }}"
                                        data-file="{{ $item->nama_file }}"
                                        data-status="{{ $item->status_produksi }}"
                                        data-catatan="{{ $item->catatan_operator }}">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="material-icons text-secondary text-4xl mb-2">check_circle</i>
                                        <h6 class="text-secondary font-weight-normal">Tidak ada antrian pekerjaan untuk Anda.</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer py-3">
                {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPDATE STATUS (ITEM) --}}
<form id="formUpdateStatus" method="POST" action="#">
    @csrf @method('PUT')
    <div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal">Update Item: <br><small id="txtNamaFile" class="font-weight-bold text-info"></small></h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- Status Dropdown --}}
                    <div class="input-group input-group-outline mb-4 is-filled" >
                        <label class="form-label">Status Produksi</label>
                        <select name="status_produksi" id="selectStatusProduksi" class="form-control" >
                            <option value="pending">Pending (Menunggu)</option>
                            <option value="ripping">Ripping (Persiapan)</option>
                            <option value="ongoing">Ongoing (Sedang Cetak)</option>
                            <option value="finishing">Finishing (Potong/Lipat)</option>
                            <option value="done">Done (Selesai)</option>
                        </select>
                    </div>

                    {{-- Catatan --}}
                    <div class="input-group input-group-outline mb-2">
                        <textarea name="catatan_operator" id="txtCatatanOperator" class="form-control" rows="2" placeholder="Catatan Operator"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-success" onclick="this.closest('form').submit()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('modalUpdateStatus');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('formUpdateStatus');

        // URL Template dengan placeholder 'PH_ID'
        // Route ini harus mengarah ke update status ITEM (bukan SPK Header)
        // Pastikan Anda membuat route baru untuk update item
        const urlTemplate = "{{ route('spk.update-produksi', 'PH_ID') }}";

        document.querySelectorAll(".btn-update-status").forEach(btn => {
            btn.addEventListener("click", function() {
                let id = this.getAttribute('data-id');
                let file = this.getAttribute('data-file');
                let status = this.getAttribute('data-status');
                let catatan = this.getAttribute('data-catatan');

                // Isi Data ke Modal
                document.getElementById('txtNamaFile').innerText = file;
                document.getElementById('selectStatusProduksi').value = status;
                document.getElementById('txtCatatanOperator').value = catatan ? catatan : '';

                // Handle label floating animation
                if (catatan) document.getElementById('txtCatatanOperator').parentElement.classList.add('is-filled');
                else document.getElementById('txtCatatanOperator').parentElement.classList.remove('is-filled');

                // Update Action Form dengan ID Item yang benar
                let finalUrl = urlTemplate.replace('PH_ID', id);
                form.action = finalUrl;

                modal.show();
            });
        });
    });
</script>
@endpush
