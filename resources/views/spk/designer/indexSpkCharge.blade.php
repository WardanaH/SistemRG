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

            {{-- HEADER: JUDUL & PENCARIAN --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                    <div class="d-flex align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Manajemen SPK Charge Desain</h6>

                        {{-- Badge Info Jumlah --}}
                        <span class="badge badge-sm bg-outline-white ms-3">{{ $spks->total() }} Data</span>
                    </div>

                    {{-- SEARCH BAR --}}
                    <div>
                        <form action="{{ route('spk-charge.index') }}" method="GET">
                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                <i class="material-icons text-secondary text-sm">search</i>
                                <input type="text" name="search" class="form-control border-0 ps-2"
                                    placeholder="Cari No SPK / Pelanggan..." value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                <a href="{{ route('spk-charge.index') }}" class="text-danger d-flex align-items-center cursor-pointer" title="Reset">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- BODY: TABEL DATA SPK --}}
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty Charge</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Designer</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Approval</th>
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
                                                <i class="material-icons text-white text-sm">payments</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM 2: PELANGGAN --}}
                                <td>
                                    <h6 class="mb-0 text-sm">{{ Str::limit($spk->nama_pelanggan, 25) }}</h6>
                                    @if($spk->no_telepon)
                                    <p class="text-xs text-secondary mb-0"><i class="fa fa-whatsapp me-1"></i> {{ $spk->no_telepon }}</p>
                                    @endif
                                </td>

                                {{-- KOLOM 3: JUMLAH ITEM CHARGE --}}
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-light text-dark">{{ $spk->items_count ?? 0 }} Item Charge</span>
                                </td>

                                {{-- KOLOM 4: DESIGNER --}}
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $spk->designer->nama ?? '-' }}</p>
                                    <p class="text-xxs text-secondary mb-0">{{ $spk->cabang->nama }}</p>
                                </td>

                                {{-- KOLOM 5: STATUS --}}
                                <td class="align-middle text-center text-sm">
                                    @if($spk->status_spk == 'pending')
                                        <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                    @elseif($spk->status_spk == 'acc')
                                        <span class="badge badge-sm bg-gradient-success">Disetujui</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                                    @endif
                                </td>

                                {{-- KOLOM 6: AKSI --}}
                                <td class="align-middle text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        {{-- LIHAT DETAIL --}}
                                        <a href="{{ route('spk.show', $spk->id) }}" class="badge bg-gradient-info text-white text-xs" title="Lihat Detail">
                                            <i class="material-icons text-xs position-relative" style="top: 1px;">visibility</i>
                                        </a>

                                        <a href="{{ route('spk-charge.cetak', $spk->id) }}" target="_blank" class="badge bg-gradient-dark text-white text-xs">
                                            <i class="material-icons text-xs">print</i>
                                        </a>

                                        @hasrole('manajemen|admin')
                                            {{-- UPDATE STATUS --}}
                                            @php $isFinal = in_array($spk->status_spk, ['acc', 'rejected']); @endphp
                                            <button type="button"
                                                class="badge {{ $isFinal ? 'bg-secondary' : 'bg-gradient-success' }} border-0 text-white text-xs btn-modal-status"
                                                {{ $isFinal ? 'disabled' : '' }}
                                                data-bs-toggle="modal" data-bs-target="#modalUpdateStatus"
                                                data-id="{{ $spk->id }}" data-no="{{ $spk->no_spk }}" data-status="{{ $spk->status_spk }}">
                                                <i class="material-icons text-xs">{{ $isFinal ? 'lock' : 'verified' }}</i>
                                            </button>

                                            {{-- HAPUS --}}
                                            <form action="{{ route('spk.destroy', $spk->id) }}" method="POST" class="d-inline delete-form m-0">
                                                @csrf @method('DELETE')
                                                <button type="button" class="badge bg-gradient-danger border-0 text-white text-xs btn-delete">
                                                    <i class="material-icons text-xs position-relative" style="top: 1px;">delete</i>
                                                </button>
                                            </form>
                                        @endhasrole
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <h6 class="text-secondary font-weight-normal">Belum ada data SPK Charge Desain.</h6>
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

{{-- MODAL UPDATE STATUS (Reuse logika yang sudah ada di index reguler) --}}
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" role="dialog" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="modalUpdateStatusLabel">Update Status SPK</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUpdateStatus" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="p-3 bg-light border-radius-lg mb-3">
                        <p class="text-sm mb-0">No. SPK: <b id="spkNoDisplay" class="text-dark"></b></p>
                    </div>

                    <div class="input-group input-group-static mb-4">
                        <label for="selectStatus" class="ms-0">Pilih Status</label>
                        <select class="form-control" id="selectStatus" name="status_spk" required>
                            <option value="pending">Pending (Menunggu)</option>
                            <option value="acc">ACC (Disetujui)</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>

                    <div class="input-group input-group-static mb-2">
                        <label>Catatan Admin (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Masukkan alasan jika ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-secondary ml-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Logika Tombol Delete
        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");
                Swal.fire({
                    title: "Hapus SPK Charge?",
                    text: "Data akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Ya, hapus!"
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });

        // Logika Modal Status
        $('.btn-modal-status').on('click', function() {
            let id = $(this).data('id');
            let no = $(this).data('no');
            let status = $(this).data('status');

            $('#spkNoDisplay').text(no);
            $('#selectStatus').val(status);

            let url = "{{ route('manajemen.spk.update-status', ':id') }}";
            $('#formUpdateStatus').attr('action', url.replace(':id', id));
        });
    });
</script>
@endpush
