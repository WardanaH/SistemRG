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

                {{-- 2. Nama File & Harga --}}
                <div class="row mb-3">
                    <div class="col-md-12 mb-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Nama File</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>

                    {{-- Input Harga (Disembunyikan default, hanya muncul jika Charge) --}}
                    <div class="col-md-12" id="sec_harga" style="display: none;">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Nominal Harga (Rp)</label>
                            <input type="number" id="modal_harga" class="form-control" min="0">
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
        // Load data lama ke tabel saat halaman dimuat
        existingItems.forEach(item => {
            let opNama = item.operator ? item.operator.nama : 'Tidak Ada (Charge)';
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
                operatorNama: opNama,
                harga: item.harga || 0 // <--- PASTIKAN HARGA DILOAD
            });
        });

        // Event listener saat modal radio jenis berubah
        document.querySelectorAll('input[name="modal_jenis"]').forEach(radio => {
            radio.addEventListener('change', function() {
                toggleModalFields(this.value);
            });
        });
    });

    // FUNGSI UNTUK MENAMPILKAN/MENYEMBUNYIKAN FIELD BERDASARKAN JENIS ORDER
    function toggleModalFields(jenis) {
        const isCharge = jenis === 'charge';
        const operatorSection = document.getElementById('modal_operator').closest('.col-md-6');
        const specSection = document.getElementById('modal_p').closest('.row');
        const finishingSection = document.getElementById('modal_finishing').closest('.col-md-6');
        const hargaSection = document.getElementById('sec_harga');

        if (isCharge) {
            operatorSection.style.display = 'none';
            specSection.querySelectorAll('.col-md-3, .col-md-4').forEach(el => el.style.display = 'none');
            finishingSection.style.display = 'none';
            hargaSection.style.display = 'block';
        } else {
            operatorSection.style.display = 'block';
            specSection.querySelectorAll('.col-md-3, .col-md-4, .col-md-2').forEach(el => el.style.display = 'block');
            finishingSection.style.display = 'block';
            hargaSection.style.display = 'none';
        }
    }

    function openModalTambah() {
        resetModal();
        document.getElementById('modalLabel').innerText = 'Tambah Detail Item';
        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    function generateRowHtml(id, data, innerOnly = false) {
        // Penentuan Warna Badge
        let badgeColor = 'secondary';
        if(data.jenis === 'outdoor') badgeColor = 'warning';
        else if(data.jenis === 'indoor') badgeColor = 'success';
        else if(data.jenis === 'multi') badgeColor = 'info';
        else if(data.jenis === 'dtf') badgeColor = 'primary';
        else if(data.jenis === 'charge') badgeColor = 'dark';

        // Logika Teks Operator
        let infoOperator = (data.jenis === 'charge')
            ? '<span class="text-danger"><i class="fa fa-paint-brush me-1"></i> Biaya Desain</span>'
            : `<i class="fa fa-user me-1 text-secondary"></i> ${data.operatorNama}`;

        // Logika Teks Spesifikasi / Harga
        let spesifikasiHtml = '';
        if (data.jenis === 'charge') {
            let formatRupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.harga);
            spesifikasiHtml = `<span class="text-success font-weight-bold text-sm">${formatRupiah}</span>`;
        } else {
            spesifikasiHtml = `
                <p class="text-xs font-weight-bold mb-0">Bahan: ${data.bahanNama}</p>
                <p class="text-xs text-secondary mb-0">Ukuran: ${data.p} x ${data.l} cm</p>
                <p class="text-xs text-secondary mb-0">Finishing: ${data.finishing || '-'}</p>
            `;
        }

        // Tepat 5 Kolom Sesuai Header Tabel HTML Anda
        let content = `
            {{-- 1. INFO ITEM (Jenis, File, Operator) --}}
            <td class="align-middle">
                <span class="badge bg-gradient-${badgeColor} mb-2">${data.jenis.toUpperCase()}</span><br>
                <h6 class="mb-1 text-sm text-wrap" style="max-width: 200px;">${data.file}</h6>
                <span class="text-xs font-weight-bold text-dark">${infoOperator}</span>

                {{-- Hidden Inputs (Wajib untuk dikirim ke Controller) --}}
                <input type="hidden" name="items[${id}][jenis]" value="${data.jenis}" class="val-jenis">
                <input type="hidden" name="items[${id}][operator_id]" value="${data.operatorId}" class="val-op-id">
                <input type="hidden" class="val-op-nama" value="${data.operatorNama}">
                <input type="hidden" name="items[${id}][file]" value="${data.file}" class="val-file">
                <input type="hidden" name="items[${id}][harga]" value="${data.harga || 0}" class="val-harga">
            </td>

            {{-- 2. SPESIFIKASI --}}
            <td class="align-middle">
                ${spesifikasiHtml}

                {{-- Hidden Inputs --}}
                <input type="hidden" name="items[${id}][p]" value="${data.p}" class="val-p">
                <input type="hidden" name="items[${id}][l]" value="${data.l}" class="val-l">
                <input type="hidden" name="items[${id}][bahan_id]" value="${data.bahanId}" class="val-bahan-id">
                <input type="hidden" name="items[${id}][finishing]" value="${data.finishing || ''}" class="val-finishing">
                <input type="hidden" class="val-bahan-nama" value="${data.bahanNama}">
            </td>

            {{-- 3. QTY --}}
            <td class="text-center align-middle text-sm font-weight-bold">
                ${data.qty}
                <input type="hidden" name="items[${id}][qty]" value="${data.qty}" class="val-qty">
            </td>

            {{-- 4. CATATAN --}}
            <td class="text-center align-middle">
                <h6 class="text-xs text-secondary font-weight-normal mb-0 text-wrap" style="max-width: 150px;">
                    ${data.catatan || '-'}
                </h6>
                <input type="hidden" name="items[${id}][catatan]" value="${data.catatan || ''}" class="val-catatan">
            </td>

            {{-- 5. AKSI --}}
            <td class="text-center align-middle">
                <button type="button" class="btn btn-link text-warning px-2 mb-0" onclick="editItem(${id})" data-toggle="tooltip" title="Edit Item">
                    <i class="material-icons text-sm">edit</i>
                </button>
                <button type="button" class="btn btn-link text-danger px-2 mb-0" onclick="hapusItem(${id})" data-toggle="tooltip" title="Hapus Item">
                    <i class="material-icons text-sm">delete</i>
                </button>
            </td>
        `;

        return innerOnly ? content : `<tr id="item-${id}" style="border-bottom: 1px solid #f0f2f5;">${content}</tr>`;
    }

    function addItemToTable(data) {
        let html = generateRowHtml(itemIndex, data);
        document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;
    }

    function updateItemInTable(id, data) {
        let row = document.getElementById('item-' + id);
        row.innerHTML = generateRowHtml(id, data, true);
    }

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
        let harga = document.getElementById('modal_harga').value || 0;

        if (jenis === 'charge') {
            if (!file || !qty || !harga) {
                Swal.fire("Data Belum Lengkap", "Mohon isi Nama File, Qty, dan Nominal Harga!", "warning");
                return;
            }
        } else {
            if (!file || !p || !l || !bahanSelect.value || !operatorSelect.value || !qty) {
                Swal.fire("Data Belum Lengkap", "Mohon lengkapi Nama File, Ukuran, Bahan, Qty, dan Operator.", "warning");
                return;
            }
        }

        let opNama = operatorSelect.value ? operatorSelect.options[operatorSelect.selectedIndex].text : 'Tidak Ada (Charge)';
        let bhnNama = bahanSelect.value ? bahanSelect.options[bahanSelect.selectedIndex].text : '-';

        let data = {
            jenis: jenis,
            file: file,
            p: (jenis === 'charge') ? 0 : p,
            l: (jenis === 'charge') ? 0 : l,
            bahanId: (jenis === 'charge') ? '' : bahanSelect.value,
            bahanNama: (jenis === 'charge') ? '-' : bhnNama,
            qty: qty,
            finishing: (jenis === 'charge') ? '-' : finishing,
            catatan: catatan,
            operatorId: (jenis === 'charge') ? '' : operatorSelect.value,
            operatorNama: opNama,
            harga: (jenis === 'charge') ? harga : 0
        };

        let editIdx = document.getElementById('edit_index').value;

        if (editIdx !== "") updateItemInTable(editIdx, data);
        else addItemToTable(data);

        bootstrap.Modal.getInstance(document.getElementById('modalTambahItem')).hide();
    }

    function editItem(id) {
        let row = document.getElementById('item-' + id);

        let file = row.querySelector('.val-file').value;
        let catatan = row.querySelector('.val-catatan').value;
        let jenis = row.querySelector('.val-jenis').value;
        let opId = row.querySelector('.val-op-id').value;
        let p = row.querySelector('.val-p').value;
        let l = row.querySelector('.val-l').value;
        let bahanId = row.querySelector('.val-bahan-id').value;
        let qty = row.querySelector('.val-qty').value;
        let finishing = row.querySelector('.val-finishing').value;
        let harga = row.querySelector('.val-harga') ? row.querySelector('.val-harga').value : 0;

        document.getElementById('modal_nama_file').value = file;
        document.getElementById('modal_catatan').value = catatan;
        document.getElementById('modal_p').value = p || "0";
        document.getElementById('modal_l').value = l || "0";
        document.getElementById('modal_qty').value = qty;
        document.getElementById('modal_harga').value = harga;

        let radioJenis = document.querySelector(`input[name="modal_jenis"][value="${jenis}"]`);
        radioJenis.checked = true;
        toggleModalFields(jenis); // Trigger form styling

        $('#modal_operator').val(opId).trigger('change');
        $('#modal_bahan').val(bahanId).trigger('change');
        $('#modal_finishing').val(finishing).trigger('change');

        document.getElementById('edit_index').value = id;
        document.getElementById('modalLabel').innerText = 'Edit Detail Item';

        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    function hapusItem(id) {
        document.getElementById('item-' + id).remove();
    }

    function resetModal() {
        document.getElementById('edit_index').value = "";
        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";
        document.getElementById('modal_p').value = "0";
        document.getElementById('modal_l').value = "0";
        document.getElementById('modal_qty').value = "1";
        document.getElementById('modal_harga').value = "";

        $('#modal_operator').val('').trigger('change');
        $('#modal_bahan').val('').trigger('change');
        $('#modal_finishing').val('').trigger('change');

        document.getElementById('m_outdoor').checked = true;
        toggleModalFields('outdoor'); // Set styling form ke default
    }
</script>
@endpush
