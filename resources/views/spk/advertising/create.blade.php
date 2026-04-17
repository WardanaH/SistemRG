@extends('spk.layout.app')
@section('content')
{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Form SPK Advertising</h6>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('advertising.store') }}" method="POST" id="formAdv">
                    @csrf

                    {{-- INFO PELANGGAN --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No Telepon (Opsional)</label>
                                <input type="text" name="no_telepon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Folder</label>
                                <input type="text" name="folder" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-auto" type="checkbox" id="is_bantuan" name="is_bantuan" value="1">
                                <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="is_bantuan">
                                    <span class="badge bg-gradient-warning text-xxs me-2">SPK Bantuan</span>
                                    Tandai ini sebagai pekerjaan bantuan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <h6>List Item</h6>
                        <button type="button" class="btn btn-info btn-sm" onclick="openModal()">+ Tambah Item</button>
                    </div>

                    {{-- TABEL ITEM --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>File / Keterangan</th>
                                    <th>Operator</th>
                                    <th>Spek (Bahan / Ukuran)</th>
                                    <th class="text-center">Qty</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelItemBody">
                                <tr id="row-kosong"><td colspan="5" class="text-center">Belum ada item</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn bg-gradient-success w-100 mt-4">Terbitkan SPK (Langsung Operator)</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ITEM --}}
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item Advertising</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <input type="hidden" id="edit_index" value="">
                    <div class="col-md-6">
                        <label>Jenis Order</label>
                        <select id="m_jenis" class="form-control border px-2">
                            <option value="outdoor">Outdoor</option>
                            <option value="indoor">Indoor</option>
                            <option value="multi">Multi</option>
                            <option value="dtf">DTF UV</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Pilih Operator</label>
                        <select id="m_operator" class="form-control border px-2 select2">
                            <option value="">-- Pilih --</option>
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}">{{ $op->nama }} ({{ $op->roles->pluck('name')->first() }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Nama File</label>
                    <input type="text" id="m_file" class="form-control border px-2">
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><label>P (cm)</label><input type="number" id="m_p" class="form-control border px-2" value="0"></div>
                    <div class="col-md-3"><label>L (cm)</label><input type="number" id="m_l" class="form-control border px-2" value="0"></div>
                    <div class="col-md-4">
                        <label>Bahan</label>
                        <select id="m_bahan" class="form-control border px-2 select2">
                            @foreach($bahans as $b) <option value="{{ $b->id }}">{{ $b->nama_bahan }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><label>Qty</label><input type="number" id="m_qty" class="form-control border px-2" value="1"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Finishing</label>
                        <select id="m_finishing" class="form-control border px-2 select2">
                            @foreach($finishings as $f) <option value="{{ $f->nama_finishing }}">{{ $f->nama_finishing }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"><label>Catatan</label><input type="text" id="m_catatan" class="form-control border px-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" onclick="simpanItem()">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let idx = 0; // Counter untuk ID baris unik

    $(document).ready(function() {
        // Inisialisasi Select2 di dalam modal agar responsif
        $('.select2').select2({
            dropdownParent: $('#modalAdd'),
            width: '100%'
        });
    });

    // 1. FUNGSI BUKA MODAL UNTUK TAMBAH
    function openModal() {
        resetModal(); // Bersihkan form
        $('#modalLabel').text('Tambah Item Advertising'); // Ubah judul
        new bootstrap.Modal(document.getElementById('modalAdd')).show();
    }

    // 2. FUNGSI SIMPAN (BISA UNTUK BARU ATAU EDIT)
    function simpanItem() {
        // Ambil Value dari Input
        let data = {
            jenis: $('#m_jenis').val(),
            opId: $('#m_operator').val(),
            opName: $('#m_operator option:selected').text(),
            file: $('#m_file').val(),
            p: $('#m_p').val() || 0,
            l: $('#m_l').val() || 0,
            bahanId: $('#m_bahan').val(),
            bahanName: $('#m_bahan option:selected').text(),
            qty: $('#m_qty').val(),
            finishing: $('#m_finishing').val(),
            catatan: $('#m_catatan').val()
        };

        // Validasi Sederhana
        if (!data.opId || !data.file || !data.qty) {
            Swal.fire('Eror', 'Mohon lengkapi Operator, Nama File, dan Qty!', 'error');
            return;
        }

        // Cek apakah mode Edit atau Tambah Baru
        let editIdx = $('#edit_index').val();

        if (editIdx !== "") {
            // MODE EDIT: Update baris yang sudah ada
            updateRow(editIdx, data);
        } else {
            // MODE TAMBAH: Buat baris baru
            createRow(data);
        }

        // Tutup Modal
        bootstrap.Modal.getInstance(document.getElementById('modalAdd')).hide();
    }

    // 3. GENERATE HTML BARIS BARU
    function createRow(data) {
        $('#row-kosong').remove(); // Hapus tulisan "Belum ada item"

        let html = generateRowHtml(idx, data);
        $('#tabelItemBody').append(html);
        idx++; // Increment counter
    }

    // 4. UPDATE HTML BARIS LAMA
    function updateRow(id, data) {
        let html = generateRowHtml(id, data, true); // True = ambil isinya saja (td), bukan tr
        $(`#row-${id}`).html(html);
    }

    // 5. TEMPLATE HTML (Digunakan Create & Update agar konsisten)
    function generateRowHtml(id, data, innerOnly = false) {
        let content = `
            <td>
                <b>${data.file}</b><br>
                <small class="text-muted">${data.catatan}</small>
                <span class="badge bg-gradient-light text-dark border border-secondary mt-1">${data.jenis.toUpperCase()}</span>

                {{-- HIDDEN INPUTS UNTUK DIKIRIM KE CONTROLLER --}}
                <input type="hidden" name="items[${id}][file]" value="${data.file}" class="val-file">
                <input type="hidden" name="items[${id}][catatan]" value="${data.catatan}" class="val-catatan">
                <input type="hidden" name="items[${id}][jenis]" value="${data.jenis}" class="val-jenis">
            </td>
            <td>
                ${data.opName}
                <input type="hidden" name="items[${id}][operator_id]" value="${data.opId}" class="val-op-id">
            </td>
            <td>
                ${data.p} x ${data.l} cm <br>
                <span class="text-xs text-secondary">${data.bahanName}</span>
                <input type="hidden" name="items[${id}][p]" value="${data.p}" class="val-p">
                <input type="hidden" name="items[${id}][l]" value="${data.l}" class="val-l">
                <input type="hidden" name="items[${id}][bahan_id]" value="${data.bahanId}" class="val-bahan-id">
            </td>
            <td class="text-center">
                <strong>${data.qty}</strong>
                <div class="text-xxs">${data.finishing}</div>
                <input type="hidden" name="items[${id}][qty]" value="${data.qty}" class="val-qty">
                <input type="hidden" name="items[${id}][finishing]" value="${data.finishing}" class="val-finishing">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-warning px-2 mb-0" onclick="editItem(${id})">
                    <i class="material-icons text-sm">edit</i>
                </button>
                <button type="button" class="btn btn-link text-danger px-2 mb-0" onclick="hapusItem(${id})">
                    <i class="material-icons text-sm">delete</i>
                </button>
            </td>
        `;

        return innerOnly ? content : `<tr id="row-${id}">${content}</tr>`;
    }

    // 6. FUNGSI EDIT (Tarik data dari tabel ke modal)
    function editItem(id) {
        let row = $(`#row-${id}`);

        // Ambil data dari hidden input yang ada di row tersebut
        let file = row.find('.val-file').val();
        let catatan = row.find('.val-catatan').val();
        let jenis = row.find('.val-jenis').val();
        let opId = row.find('.val-op-id').val();
        let p = row.find('.val-p').val();
        let l = row.find('.val-l').val();
        let bahanId = row.find('.val-bahan-id').val();
        let qty = row.find('.val-qty').val();
        let finishing = row.find('.val-finishing').val();

        // Isi ke Form Modal
        $('#m_file').val(file);
        $('#m_catatan').val(catatan);
        $('#m_jenis').val(jenis).trigger('change');
        $('#m_operator').val(opId).trigger('change');
        $('#m_p').val(p);
        $('#m_l').val(l);
        $('#m_bahan').val(bahanId).trigger('change');
        $('#m_qty').val(qty);
        $('#m_finishing').val(finishing).trigger('change');

        // Set ID yang sedang diedit
        $('#edit_index').val(id);

        // Ubah Judul Modal
        $('#modalLabel').text('Edit Item Advertising');

        // Tampilkan Modal
        new bootstrap.Modal(document.getElementById('modalAdd')).show();
    }

    // 7. FUNGSI HAPUS
    function hapusItem(id) {
        $(`#row-${id}`).remove();
        // Jika kosong, tampilkan row default lagi
        if ($('#tabelItemBody tr').length === 0) {
            $('#tabelItemBody').html('<tr id="row-kosong"><td colspan="5" class="text-center">Belum ada item</td></tr>');
        }
    }

    // 8. RESET FORM MODAL
    function resetModal() {
        $('#edit_index').val(''); // Kosongkan index edit
        $('#m_file').val('');
        $('#m_catatan').val('');
        $('#m_p').val('0');
        $('#m_l').val('0');
        $('#m_qty').val('1');

        // Reset Select2
        $('#m_jenis').val('outdoor').trigger('change');
        $('#m_operator').val('').trigger('change');
        $('#m_bahan').val($('#m_bahan option:first').val()).trigger('change');
        $('#m_finishing').val($('#m_finishing option:first').val()).trigger('change');
    }
</script>
@endpush
