@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Edit SPK: {{ $spk->no_spk }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk.update', $spk->id) }}" method="POST" id="formSpk">
                    @csrf
                    @method('PUT')

                    {{-- I. HEADER --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Data Umum</p>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. SPK</label>
                                <input type="text" class="form-control" value="{{ $spk->no_spk }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', $spk->nama_pelanggan) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $spk->no_telepon) }}">
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- II. ITEM EDITOR --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-sm text-uppercase font-weight-bold mb-0">II. Edit Detail Item</p>
                        <button type="button" class="btn btn-sm btn-info mb-0" onclick="openModalTambah()">
                            <i class="fa fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="card border mb-3">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Info Item</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Spesifikasi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Catatan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelItemBody">
                                    {{-- Data Existing akan di-render via Javascript di bawah --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn bg-gradient-info">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT ITEM (POPUP) --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-normal" id="modalLabel">Tambah Detail Item</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- input hidden untuk menandai baris yang sedang di-edit --}}
                <input type="hidden" id="edit_index" value="">

                {{-- 1. Jenis Order & Operator --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold">Jenis Order:</label>
                        <div class="d-flex gap-3 mt-1 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_outdoor" value="outdoor" checked>
                                <label class="custom-control-label" for="m_outdoor">Outdoor</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_indoor" value="indoor">
                                <label class="custom-control-label" for="m_indoor">Indoor</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_multi" value="multi">
                                <label class="custom-control-label" for="m_multi">Multi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_dtf" value="dtf">
                                <label class="custom-control-label" for="m_dtf">DTF</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_charge" value="charge">
                                <label class="custom-control-label text-danger" for="m_charge">Charge Desain</label>
                            </div>
                        </div>
                    </div>
                    {{-- Operator Dipilih Disini (Per Item) --}}
                    <div class="col-md-6">
                        <div class="input-group input-group-outline">
                            <select id="modal_operator" class="form-control select2" data-placeholder="Cari & Pilih Operator..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Operator...</option>
                                @foreach($operators as $op)
                                    <option value="{{ $op->id }}" data-nama="{{ $op->nama }}">
                                        {{ $op->nama }}
                                        ({{ $op->roles()->pluck('name')->implode(', ') }})
                                        {{-- Info tambahan jika lembur --}}
                                        @if($spk->is_lembur && $op->cabang)
                                            - {{ $op->cabang->nama }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. Nama File --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Nama File</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- 3. Spesifikasi --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">P (cm)</label>
                            <input type="number" step="0.01" id="modal_p" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">L (cm)</label>
                            <input type="number" step="0.01" id="modal_l" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_bahan" class="form-control select2" data-placeholder="Cari & Pilih Bahan..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Bahan</option>
                                @foreach($bahans as $b)
                                <option value="{{ $b->id }}" data-nama="{{ $b->nama_bahan }}">{{ $b->nama_bahan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Qty</label>
                            <input type="number" id="modal_qty" class="form-control" value="1" min="1">
                        </div>
                    </div>
                </div>

                {{-- 4. Finishing & Catatan --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_finishing" class="form-control select2" data-placeholder="Cari & Pilih Finishing..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Finishing...</option>
                                @foreach($finishings as $f)
                                <option value="{{ $f->nama_finishing }}">{{ $f->nama_finishing }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Catatan Item</label>
                            <input type="text" id="modal_catatan" class="form-control">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn bg-gradient-info" onclick="simpanItem()">Simpan ke Daftar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let itemIndex = 0;

    // Data existing dari database (dikirim controller)
    const existingItems = @json($spk->items);

    document.addEventListener("DOMContentLoaded", function() {
        // Load data lama ke tabel
        existingItems.forEach(item => {
            let opNama = item.operator ? item.operator.nama : 'Tidak Ada';
            let bhnNama = item.bahan ? item.bahan.nama_bahan : '-';

            addItemToTable({
                jenis: item.jenis_order,
                file: item.nama_file,
                p: item.p || 0,
                l: item.l || 0,
                bahanId: item.bahan_id || '',
                bahanNama: bhnNama,
                qty: item.qty,
                finishing: item.finishing || '-',
                catatan: item.catatan || '-',
                operatorId: item.operator_id || '',
                operatorNama: opNama
            });
        });
    });

    // --- 1. BUKA MODAL TAMBAH BARU ---
    function openModalTambah() {
        resetModal();
        document.getElementById('modalLabel').innerText = 'Tambah Detail Item';
        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    // --- 2. TEMPLATE RENDER BARIS HTML ---
    function generateRowHtml(id, data, innerOnly = false) {
        // Penyesuaian warna badge
        let badgeColor = 'secondary';
        if(data.jenis === 'outdoor') badgeColor = 'warning';
        else if(data.jenis === 'indoor') badgeColor = 'success';
        else if(data.jenis === 'multi') badgeColor = 'info';
        else if(data.jenis === 'dtf') badgeColor = 'primary';
        else if(data.jenis === 'charge') badgeColor = 'danger';

        let content = `
            <td>
                <span class="badge bg-gradient-${badgeColor} mb-1">${data.jenis.toUpperCase()}</span><br>
                <strong>${data.file}</strong><br>
                <small class="text-xs text-secondary"><i class="fa fa-user me-1"></i> ${data.operatorNama}</small>

                <input type="hidden" name="items[${id}][jenis]" value="${data.jenis}" class="val-jenis">
                <input type="hidden" name="items[${id}][file]" value="${data.file}" class="val-file">
                <input type="hidden" name="items[${id}][operator_id]" value="${data.operatorId}" class="val-op-id">
                <input type="hidden" name="items[${id}][catatan]" value="${data.catatan || ''}" class="val-catatan">
                <input type="hidden" class="val-op-nama" value="${data.operatorNama}">
            </td>
            <td class="text-xs">
                ${data.jenis === 'charge' ? '<span class="text-danger font-weight-bold">Biaya Desain (Charge)</span>' : `${data.p} x ${data.l} cm <br> Bahan: <strong>${data.bahanNama}</strong> <br> Fin: ${data.finishing || '-'}`}

                <input type="hidden" name="items[${id}][p]" value="${data.p}" class="val-p">
                <input type="hidden" name="items[${id}][l]" value="${data.l}" class="val-l">
                <input type="hidden" name="items[${id}][bahan_id]" value="${data.bahanId}" class="val-bahan-id">
                <input type="hidden" name="items[${id}][finishing]" value="${data.finishing || ''}" class="val-finishing">
                <input type="hidden" class="val-bahan-nama" value="${data.bahanNama}">
            </td>
            <td class="text-center text-sm font-weight-bold">
                ${data.qty}
                <input type="hidden" name="items[${id}][qty]" value="${data.qty}" class="val-qty">
            </td>
            <td class="text-center text-sm font-weight-bold">
                ${data.catatan || '-'}
                <input type="hidden" name="items[${id}][catatan]" value="${data.catatan}" class="val-catatan">
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
        return innerOnly ? content : `<tr id="item-${id}">${content}</tr>`;
    }

    // --- 3. TAMBAH BARIS BARU KE TABEL ---
    function addItemToTable(data) {
        let html = generateRowHtml(itemIndex, data);
        document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;
    }

    // --- 4. UPDATE BARIS LAMA DI TABEL ---
    function updateItemInTable(id, data) {
        let row = document.getElementById('item-' + id);
        row.innerHTML = generateRowHtml(id, data, true); // True = update inner HTML-nya saja
    }

    // --- 5. LOGIKA SIMPAN (BARU / UPDATE) ---
    function simpanItem() {
        let jenis = document.querySelector('input[name="modal_jenis"]:checked').value;
        let operatorSelect = document.getElementById('modal_operator');
        let file = document.getElementById('modal_nama_file').value;
        let p = document.getElementById('modal_p').value;
        let l = document.getElementById('modal_l').value;
        let bahanSelect = document.getElementById('modal_bahan');
        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value;
        let catatan = document.getElementById('modal_catatan').value;

        // --- VALIDASI CONDITIONAL ---
        if (jenis === 'charge') {
            // Jika Charge, yang wajib cuma File & Qty
            if (!file || !qty) {
                Swal.fire("Data Belum Lengkap", "Mohon lengkapi Nama File dan Qty untuk Charge Desain.", "warning");
                return;
            }
        } else {
            // Jika selain Charge, wajib semua (kecuali catatan & finishing bisa '-')
            if (!file || !p || !l || !bahanSelect.value || !operatorSelect.value) {
                Swal.fire("Data Belum Lengkap", "Mohon lengkapi Nama File, Ukuran, Bahan, dan Operator.", "warning");
                return;
            }
        }

        // Ambil nama dari dropdown (Handle kemungkinan null/tidak dipilih)
        let opNama = operatorSelect.value ? operatorSelect.options[operatorSelect.selectedIndex].text : 'Tidak Ada (Charge)';
        let bhnNama = bahanSelect.value ? bahanSelect.options[bahanSelect.selectedIndex].text : '-';

        let data = {
            jenis: jenis,
            file: file,
            // Paksa set 0/kosong jika jenisnya charge agar bersih
            p: (jenis === 'charge') ? 0 : p,
            l: (jenis === 'charge') ? 0 : l,
            bahanId: (jenis === 'charge') ? '' : bahanSelect.value,
            bahanNama: (jenis === 'charge') ? '-' : bhnNama,
            qty: qty,
            finishing: (jenis === 'charge') ? '-' : finishing,
            catatan: catatan,
            operatorId: (jenis === 'charge') ? '' : operatorSelect.value,
            operatorNama: opNama
        };

        let editIdx = document.getElementById('edit_index').value;

        if (editIdx !== "") {
            // Jika ada isinya, berarti UPDATE data
            updateItemInTable(editIdx, data);
        } else {
            // Jika kosong, berarti TAMBAH BARU
            addItemToTable(data);
        }

        // Tutup Modal
        bootstrap.Modal.getInstance(document.getElementById('modalTambahItem')).hide();
    }

    // --- 6. TARIK DATA DARI TABEL KE MODAL (EDIT) ---
    function editItem(id) {
        let row = document.getElementById('item-' + id);

        // Ambil data dari hidden input yang ada di dalam row tersebut
        let file = row.querySelector('.val-file').value;
        let catatan = row.querySelector('.val-catatan').value;
        let jenis = row.querySelector('.val-jenis').value;
        let opId = row.querySelector('.val-op-id').value;
        let p = row.querySelector('.val-p').value;
        let l = row.querySelector('.val-l').value;
        let bahanId = row.querySelector('.val-bahan-id').value;
        let qty = row.querySelector('.val-qty').value;
        let finishing = row.querySelector('.val-finishing').value;

        // Isi form modal dengan data yang ditarik
        document.getElementById('modal_nama_file').value = file;
        document.getElementById('modal_catatan').value = catatan;
        document.querySelector(`input[name="modal_jenis"][value="${jenis}"]`).checked = true;

        // Handle select jika kosong (karena sebelumnya charge)
        document.getElementById('modal_operator').value = opId || "";
        document.getElementById('modal_p').value = p || "";
        document.getElementById('modal_l').value = l || "";
        document.getElementById('modal_bahan').value = bahanId || "";
        document.getElementById('modal_qty').value = qty;
        document.getElementById('modal_finishing').value = finishing || "";

        // Tandai bahwa ini adalah proses Edit (Simpan ID ke hidden input modal)
        document.getElementById('edit_index').value = id;
        document.getElementById('modalLabel').innerText = 'Edit Detail Item';

        // Tampilkan Modal
        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    // --- 7. HAPUS BARIS ---
    function hapusItem(id) {
        document.getElementById('item-' + id).remove();
    }

    // --- 8. RESET MODAL FORM ---
    function resetModal() {
        document.getElementById('edit_index').value = ""; // Kosongkan state edit
        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";
        document.getElementById('modal_p').value = "";
        document.getElementById('modal_l').value = "";
        document.getElementById('modal_qty').value = "1";
        document.getElementById('modal_operator').selectedIndex = 0;
        document.getElementById('modal_bahan').selectedIndex = 0;
        document.getElementById('modal_finishing').selectedIndex = 0;
        document.getElementById('m_outdoor').checked = true;
    }
</script>
@endpush
