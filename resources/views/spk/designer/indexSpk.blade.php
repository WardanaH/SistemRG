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
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">

                        <div class="d-flex align-items-center">
                            <h6 class="text-white text-capitalize mb-0">Daftar SPK (Surat Perintah Kerja)</h6>

                            @hasrole('manajemen|designer')
                            <a href="{{ route('spk') }}" class="btn btn-sm btn-white text-primary ms-3 mb-0 d-flex align-items-center">
                                <i class="material-icons text-sm me-1">add</i> Buat SPK Baru
                            </a>
                            @endhasrole
                        </div>

                        {{-- SEARCH BAR --}}
                        <div>
                            <form action="{{ route('spk.index') }}" method="GET">
                                <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 250px;">
                                    <i class="material-icons text-secondary text-sm">search</i>
                                    <input type="text" name="search" class="form-control border-0 ps-2"
                                        placeholder="Cari No SPK / Pelanggan..." value="{{ request('search') }}"
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

                {{-- BODY: TABEL DATA SPK --}}
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No. SPK / Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Item</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Designer</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Produksi</th>
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
                                        <h6 class="mb-0 text-sm">{{ Str::limit($spk->nama_pelanggan, 25) }}</h6>
                                        @if($spk->no_telepon)
                                        <p class="text-xs text-secondary mb-0"></i> {{ $spk->no_telepon }}</p>
                                        @endif
                                    </td>

                                    {{-- KOLOM 3: TOTAL ITEM --}}
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-info">{{ $spk->items_count ?? 0 }} Item</span>
                                    </td>

                                    {{-- KOLOM 4: DESIGNER --}}
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $spk->designer->nama ?? '-' }}</p>
                                    </td>

                                    {{-- KOLOM 5: STATUS SPK (ACC/PENDING) --}}
                                    <td class="align-middle text-center text-sm">
                                        @if($spk->status_spk == 'pending')
                                        <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                        @elseif($spk->status_spk == 'acc')
                                        <span class="badge badge-sm bg-gradient-success">Acc</span>
                                        @elseif($spk->status_spk == 'reject')
                                        <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                                        @endif
                                    </td>

                                    {{-- KOLOM 7: AKSI --}}
                                    <td class="align-middle text-end pe-4">
                                        <div class="d-flex justify-content-end align-items-center gap-2">

                                            {{-- TOMBOL LIHAT DETAIL (MATA) --}}
                                            <a href="{{ route('spk.show', $spk->id) }}" class="badge bg-gradient-info text-white text-xs" data-toggle="tooltip" title="Lihat Detail Item">
                                                <i class="material-icons text-xs position-relative" style="top: 1px;">visibility</i>
                                            </a>

                                            @hasrole('manajemen|admin')
                                            {{-- TOMBOL UPDATE STATUS (ACC) --}}
                                            @php $isFinal = in_array($spk->status_spk, ['acc', 'reject']); @endphp
                                            <button type="button"
                                                class="badge {{ $isFinal ? 'bg-secondary' : 'bg-gradient-success' }} border-0 text-white text-xs btn-modal-status"
                                                {{ $isFinal ? 'disabled' : 'data-bs-toggle=modal data-bs-target=#modalUpdateStatus' }}
                                                data-id="{{ $spk->id }}"
                                                data-no="{{ $spk->no_spk }}"
                                                data-status="{{ $spk->status_spk }}"
                                                data-toggle="tooltip" title="Approval Status">
                                                <i class="material-icons text-xs position-relative" style="top: 1px;">
                                                    {{ $isFinal ? 'lock' : 'verified' }}
                                                </i>
                                            </button>
                                            @endhasrole

                                            {{-- TOMBOL CETAK --}}
                                            <a href="{{ route('manajemen.spk.cetak-spk', $spk->id) }}" target="_blank" class="badge bg-gradient-primary text-white text-xs" data-toggle="tooltip" title="Cetak SPK">
                                                <i class="material-icons text-xs position-relative" style="top: 1px;">print</i>
                                            </a>

                                            @hasrole('manajemen|admin')
                                            {{-- TOMBOL HAPUS --}}
                                            <form action="{{ route('spk.destroy', $spk->id) }}" method="POST" class="d-inline delete-form m-0">
                                                @csrf @method('DELETE')
                                                <button type="button" class="badge bg-gradient-danger border-0 text-white text-xs btn-delete cursor-pointer" data-toggle="tooltip" title="Hapus SPK">
                                                    <i class="material-icons text-xs position-relative" style="top: 1px;">delete</i>
                                                </button>
                                            </form>
                                            @endhasrole

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <h6 class="text-secondary font-weight-normal">Belum ada data SPK.</h6>
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
            // DELETE BUTTON
            document.querySelectorAll(".btn-delete").forEach(btn => {
                btn.addEventListener("click", function() {
                    let form = this.closest("form");
                    Swal.fire({
                        title: "Hapus SPK ini?",
                        text: "Data dan seluruh item di dalamnya akan dihapus!",
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
