@extends('spk.layout.app')
@section('content')

{{-- Notifikasi Flash Message --}}
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
            {{-- HEADER --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <h6 class="text-white text-capitalize mb-0">Antrean Produksi (Advertising)</h6>
                    <span class="badge bg-white text-dark">{{ $items->total() }} Tugas</span>
                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0 table-hover">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-3">SPK / Tgl</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">File / Folder</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Spek & Bahan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Catatan Operator</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                {{-- KOLOM 1: Info SPK --}}
                                <td class="ps-3 align-middle">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm">{{ $item->spk->no_spk }}</h6>
                                        <span class="text-xs text-secondary">{{ $item->spk->created_at->format('d/m/y H:i') }}</span>
                                        <span class="badge badge-sm bg-primary mt-1" style="width:fit-content">{{ $item->spk->nama_pelanggan }}</span>
                                    </div>
                                </td>

                                {{-- KOLOM 2: File & Folder --}}
                                <td class="align-middle">
                                    <h6 class="mb-0 text-sm text-truncate" style="max-width: 200px;" title="{{ $item->nama_file }}">
                                        {{ $item->nama_file }}
                                    </h6>
                                    @if($item->spk->link_folder)
                                    <p class="text-xs text-info mb-0 font-weight-bold cursor-pointer" onclick="copyToClipboard('{{ $item->spk->link_folder }}')">
                                        <i class="material-icons text-xs align-middle">folder</i> {{ Str::limit($item->spk->link_folder, 25) }}
                                    </p>
                                    @endif
                                    @if($item->catatan && $item->catatan != '-')
                                        <span class="text-xs text-danger fst-italic">Note: {{ $item->catatan }}</span>
                                    @endif
                                </td>

                                {{-- KOLOM 3: Spesifikasi --}}
                                <td class="align-middle">
                                    <div class="d-flex flex-column">
                                        <span class="text-xs font-weight-bold text-uppercase">{{ $item->jenis_order }}</span>
                                        <span class="text-xs text-dark">{{ $item->p }} x {{ $item->l }} cm</span>
                                        <span class="text-xs text-secondary">{{ $item->bahan->nama_bahan ?? '-' }}</span>
                                        <span class="text-xxs text-secondary">Finishing: {{ $item->finishing }}</span>
                                    </div>
                                </td>

                                {{-- KOLOM 4: Qty --}}
                                <td class="align-middle text-center">
                                    <h5 class="font-weight-bolder mb-0 text-dark">{{ $item->qty }}</h5>
                                </td>

                                {{-- KOLOM 5: Status Badge --}}
                                <td class="align-middle text-center">
                                    @php
                                        $colors = [
                                            'pending' => 'secondary',
                                            'ripping' => 'warning',
                                            'ongoing' => 'info',
                                            'finishing' => 'primary',
                                            'done' => 'success'
                                        ];
                                        $labels = [
                                            'pending' => 'Menunggu',
                                            'ripping' => 'Ripping',
                                            'ongoing' => 'Cetak',
                                            'finishing' => 'Finishing',
                                            'done' => 'Selesai'
                                        ];
                                    @endphp
                                    <span class="badge bg-gradient-{{ $colors[$item->status_produksi] }} btn-status"
                                        style="cursor: pointer;"
                                        data-id="{{ $item->id }}"
                                        data-status="{{ $item->status_produksi }}"
                                        data-file="{{ $item->nama_file }}">
                                        {{ $labels[$item->status_produksi] }} <i class="material-icons text-xs ms-1">edit</i>
                                    </span>
                                </td>

                                {{-- KOLOM 6: Catatan Operator --}}
                                <td class="align-middle text-end pe-4">
                                    <span class="text-xs font-weight-bold">
                                        {{ $item->catatan_operator ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="material-icons text-secondary text-4xl mb-2">assignment_turned_in</i>
                                        <h6 class="text-secondary font-weight-normal">Tidak ada tugas baru.</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPDATE STATUS --}}
<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Progres</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="formStatus" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="text-sm">Update status untuk file: <br><b id="displayFile"></b></p>

                    <div class="input-group input-group-static mb-4">
                        <label for="selectStatus" class="ms-0">Status Produksi</label>
                        <select class="form-control" id="selectStatus" name="status_produksi">
                            <option value="pending">Pending (Menunggu)</option>
                            <option value="ripping">Ripping (Persiapan File)</option>
                            <option value="ongoing">Ongoing (Sedang Cetak)</option>
                            <option value="finishing">Finishing</option>
                            <option value="done">Completed (Selesai)</option>
                        </select>
                    </div>

                    <div class="input-group input-group-static mb-4">
                        <label for="catatan" class="ms-0">Catatan</label>
                        <input type="text" class="form-control" id="catatan" name="catatan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // 1. Logic Copy Folder
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({ icon: 'success', title: 'Copied!', text: 'Link folder disalin.', timer: 800, showConfirmButton: false, toast: true, position: 'top-end' });
        });
    }

    // 2. Logic Modal Status
    document.querySelectorAll('.btn-status').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            let status = this.dataset.status;
            let file = this.dataset.file;

            document.getElementById('displayFile').innerText = file;
            document.getElementById('selectStatus').value = status;

            let url = "{{ route('advertising.produksi-update', ':id') }}";
            document.getElementById('formStatus').action = url.replace(':id', id);

            new bootstrap.Modal(document.getElementById('modalStatus')).show();
        });
    });

    // 3. Logic Quick Finish (Tombol Centang Hijau)
    document.querySelectorAll('.btn-quick-finish').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            Swal.fire({
                title: 'Selesaikan Item?',
                text: "Status akan berubah menjadi Completed.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesai!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form hidden dinamis untuk submit
                    let form = document.createElement('form');
                    form.method = 'POST';
                    let url = "{{ route('advertising.produksi-update', ':id') }}";
                    form.action = url.replace(':id', id);

                    let csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);

                    let method = document.createElement('input');
                    method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
                    form.appendChild(method);

                    let status = document.createElement('input');
                    status.type = 'hidden'; status.name = 'status_produksi'; status.value = 'completed';
                    form.appendChild(status);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
