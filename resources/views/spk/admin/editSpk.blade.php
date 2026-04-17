@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row">
    <div class="col-12">

        {{-- TOMBOL KEMBALI & HEADER --}}
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm mb-0 me-3">
                <i class="material-icons text-sm">arrow_back</i> Kembali
            </a>
            <h5 class="mb-0 text-capitalize">Detail SPK: {{ $spk->no_spk }}</h5>
        </div>

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

                        {{-- Harga Design Global --}}
                        <!-- <div class="col-md-3 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Harga Desain (Opsional)</label>
                                {{-- Tampilan User --}}
                                <input type="text" class="form-control" id="harga_design_tampil" value="{{ old('harga_design', $spk->harga) }}">
                                {{-- Hidden input untuk DB --}}
                                <input type="hidden" name="harga_design" id="harga_design_asli" value="{{ old('harga_design', $spk->harga) }}">
                            </div>
                        </div> -->
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
                                        @if($spk->is_lembur && $op->cabang)
                                            - {{ $op->cabang->nama }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. Nama File & Jenis File --}}
                <div class="row mb-3">
                    <div class="col-md-8 mb-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Nama File</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_jenis_file" class="form-control select2" data-placeholder="Pilih Jenis File..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Jenis File...</option>
                                <option value="offline">Offline</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Input Harga (Disembunyikan default, hanya muncul jika Charge) --}}
                <div class="row mb-3" id="sec_harga" style="display: none;">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Nominal Harga (Rp)</label>
                            <input type="text" class="form-control" id="modal_harga_tampil">
                            <input type="hidden" id="modal_harga_asli">
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
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_finishing" class="form-control select2" data-placeholder="Cari & Pilih Finishing..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Finishing 1...</option>
                                @foreach($finishings as $f)
                                <option value="{{ $f->nama_finishing }}">{{ $f->nama_finishing }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_finishing_2" class="form-control select2" data-placeholder="Cari & Pilih Finishing..." style="appearance: auto;">
                                <option value="" disabled selected>Pilih Finishing 2...</option>
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

    // --- FORMAT RUPIAH ---
    function formatRupiah(angka, prefix) {
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split    = number_string.split(','),
            sisa     = split[0].length % 3,
            rupiah   = split[0].substr(0, sisa),
            ribuan   = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. LOAD EXISTING DATA DARI DB KE TABEL ---
        const existingItems = @json($spk->items);

        existingItems.forEach(item => {
            let opNama = item.operator ? item.operator.nama : 'Tidak Ada (Charge)';
            let bhnNama = item.bahan ? item.bahan.nama_bahan : '-';

            addItemToTable({
                jenis: item.jenis_order,
                file: item.nama_file,
                jenis_file: item.jenis_file || '',
                p: item.p || 0,
                l: item.l || 0,
                bahanId: item.bahan_id || '',
                bahanNama: bhnNama,
                qty: item.qty,
                finishing: item.finishing || '-',
                finishing_2: item.finishing_2 || '-',
                catatan: item.catatan || '-',
                operatorId: item.operator_id || '',
                operatorNama: opNama,
                harga: item.harga || 0
            });
        });

        // --- 2. FORMAT RUPIAH GLOBAL (DATA PELANGGAN) ---
        const globalHargaTampil = document.getElementById('harga_design_tampil');
        const globalHargaAsli = document.getElementById('harga_design_asli');

        if (globalHargaTampil) {
            if (globalHargaAsli && globalHargaAsli.value && parseFloat(globalHargaAsli.value) > 0) {
                globalHargaTampil.value = formatRupiah(globalHargaAsli.value, 'Rp. ');
                globalHargaTampil.parentElement.classList.add('is-filled');
            }
            globalHargaTampil.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, 'Rp. ');
                let cleanNumber = this.value.replace(/[^0-9]/g, '');
                if(globalHargaAsli) globalHargaAsli.value = cleanNumber;
            });
        }

        // --- 3. FORMAT RUPIAH ITEM (DALAM MODAL) ---
        const modalHargaTampil = document.getElementById('modal_harga_tampil');
        const modalHargaAsli = document.getElementById('modal_harga_asli');

        if (modalHargaTampil) {
            modalHargaTampil.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, 'Rp. ');
                let cleanNumber = this.value.replace(/[^0-9]/g, '');
                if(modalHargaAsli) modalHargaAsli.value = cleanNumber;
            });
        }

        // --- 4. LISTENER RADIO BUTTON JENIS ORDER ---
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
        const finishingSection = document.getElementById('modal_finishing').closest('.col-md-3');
        const finishingSection2 = document.getElementById('modal_finishing_2').closest('.col-md-3');
        const hargaSection = document.getElementById('sec_harga');
        const jenisFileSection = document.getElementById('modal_jenis_file').closest('.col-md-4');

        if (isCharge) {
            operatorSection.style.display = 'none';
            specSection.querySelectorAll('.col-md-3, .col-md-4').forEach(el => el.style.display = 'none');
            finishingSection.style.display = 'none';
            finishingSection2.style.display = 'none';
            if(jenisFileSection) jenisFileSection.style.display = 'none';
            hargaSection.style.display = 'block';
        } else {
            operatorSection.style.display = 'block';
            specSection.querySelectorAll('.col-md-3, .col-md-4, .col-md-2').forEach(el => el.style.display = 'block');
            finishingSection.style.display = 'block';
            finishingSection2.style.display = 'block';
            if(jenisFileSection) jenisFileSection.style.display = 'block';
            hargaSection.style.display = 'none';
        }
    }

    function openModalTambah() {
        resetModal();
        document.getElementById('modalLabel').innerText = 'Tambah Detail Item';
        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    function generateRowHtml(id, data, innerOnly = false) {
        let badgeColor = 'secondary';
        if(data.jenis === 'outdoor') badgeColor = 'warning';
        else if(data.jenis === 'indoor') badgeColor = 'success';
        else if(data.jenis === 'multi') badgeColor = 'info';
        else if(data.jenis === 'dtf') badgeColor = 'primary';
        else if(data.jenis === 'charge') badgeColor = 'dark';

        let infoOperator = (data.jenis === 'charge')
            ? '<span class="text-danger"><i class="fa fa-paint-brush me-1"></i> Biaya Desain</span>'
            : `<i class="fa fa-user me-1 text-secondary"></i> ${data.operatorNama}`;

        let spesifikasiHtml = '';
        if (data.jenis === 'charge') {
            let formatVal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.harga);
            spesifikasiHtml = `<span class="text-success font-weight-bold text-sm">${formatVal}</span>`;
        } else {
            spesifikasiHtml = `
                <p class="text-xs font-weight-bold mb-0">Bahan: ${data.bahanNama}</p>
                <p class="text-xs text-secondary mb-0">Ukuran: ${data.p} x ${data.l} cm</p>
                <p class="text-xs text-secondary mb-0">Finishing 1: ${data.finishing || '-'}</p>
                <p class="text-xs text-secondary mb-0">Finishing 2: ${data.finishing_2 || '-'}</p>
            `;
        }

        let content = `
            <td class="align-middle">
                <h6 class="mb-1 text-sm text-wrap" style="max-width: 200px;">Nama File : ${data.file}</h6>
                ${data.jenis !== 'charge' ? `
                <span class="text-xs font-weight-bold text-dark">
                    Jenis File : <span class="badge bg-gradient-info mb-2">${data.jenis_file || '-'}</span>
                </span><br>
                ` : ''}
                <span class="text-xs font-weight-bold text-dark">Operator : ${infoOperator}</span><br>
                <span class="text-xs font-weight-bold text-dark">
                    Jenis Order : <span class="badge bg-gradient-${badgeColor} mb-2">${data.jenis.toUpperCase()}</span>
                </span><br>

                <input type="hidden" name="items[${id}][jenis]" value="${data.jenis}" class="val-jenis">
                <input type="hidden" name="items[${id}][jenis_file]" value="${data.jenis_file || ''}" class="val-jenis_file">
                <input type="hidden" name="items[${id}][operator_id]" value="${data.operatorId}" class="val-op-id">
                <input type="hidden" class="val-op-nama" value="${data.operatorNama}">
                <input type="hidden" name="items[${id}][file]" value="${data.file}" class="val-file">
                <input type="hidden" name="items[${id}][harga]" value="${data.harga || 0}" class="val-harga">
            </td>
            <td class="align-middle">
                ${spesifikasiHtml}
                <input type="hidden" name="items[${id}][p]" value="${data.p}" class="val-p">
                <input type="hidden" name="items[${id}][l]" value="${data.l}" class="val-l">
                <input type="hidden" name="items[${id}][bahan_id]" value="${data.bahanId}" class="val-bahan-id">
                <input type="hidden" name="items[${id}][finishing]" value="${data.finishing || ''}" class="val-finishing">
                <input type="hidden" name="items[${id}][finishing_2]" value="${data.finishing_2 || ''}" class="val-finishing_2">
                <input type="hidden" class="val-bahan-nama" value="${data.bahanNama}">
            </td>
            <td class="text-center align-middle text-sm font-weight-bold">
                ${data.qty}
                <input type="hidden" name="items[${id}][qty]" value="${data.qty}" class="val-qty">
            </td>
            <td class="text-center align-middle">
                <h6 class="text-xs text-secondary font-weight-normal mb-0 text-wrap" style="max-width: 150px;">
                    ${data.catatan || '-'}
                </h6>
                <input type="hidden" name="items[${id}][catatan]" value="${data.catatan || ''}" class="val-catatan">
            </td>
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
        let jenis_file = document.getElementById('modal_jenis_file').value;
        let p = document.getElementById('modal_p').value;
        let l = document.getElementById('modal_l').value;
        let bahanSelect = document.getElementById('modal_bahan');
        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value;
        let finishing_2 = document.getElementById('modal_finishing_2').value;
        let catatan = document.getElementById('modal_catatan').value;

        let hargaElem = document.getElementById('modal_harga_asli');
        let harga = hargaElem ? (parseFloat(hargaElem.value) || 0) : 0;

        if (jenis === 'charge') {
            if (!file || !qty || harga <= 0) {
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
            jenis_file: (jenis === 'charge') ? '' : jenis_file,
            p: (jenis === 'charge') ? 0 : p,
            l: (jenis === 'charge') ? 0 : l,
            bahanId: (jenis === 'charge') ? '' : bahanSelect.value,
            bahanNama: (jenis === 'charge') ? '-' : bhnNama,
            qty: qty,
            finishing: (jenis === 'charge') ? '-' : finishing,
            finishing_2: (jenis === 'charge') ? '-' : finishing_2,
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
        let jenis_file = row.querySelector('.val-jenis_file').value;
        let catatan = row.querySelector('.val-catatan').value;
        let jenis = row.querySelector('.val-jenis').value;
        let opId = row.querySelector('.val-op-id').value;
        let p = row.querySelector('.val-p').value;
        let l = row.querySelector('.val-l').value;
        let bahanId = row.querySelector('.val-bahan-id').value;
        let qty = row.querySelector('.val-qty').value;
        let finishing = row.querySelector('.val-finishing').value;
        let finishing_2 = row.querySelector('.val-finishing_2').value;
        let harga = row.querySelector('.val-harga') ? row.querySelector('.val-harga').value : 0;

        document.getElementById('modal_nama_file').value = file;
        document.getElementById('modal_jenis_file').value = jenis_file;
        document.getElementById('modal_catatan').value = catatan;
        document.getElementById('modal_p').value = p || "0";
        document.getElementById('modal_l').value = l || "0";
        document.getElementById('modal_qty').value = qty;

        let mHargaTampil = document.getElementById('modal_harga_tampil');
        let mHargaAsli = document.getElementById('modal_harga_asli');
        if(mHargaAsli && mHargaTampil) {
            mHargaAsli.value = harga;
            mHargaTampil.value = harga > 0 ? formatRupiah(harga, 'Rp. ') : '';
            if(harga > 0) mHargaTampil.parentElement.classList.add('is-filled');
        }

        let radioJenis = document.querySelector(`input[name="modal_jenis"][value="${jenis}"]`);
        radioJenis.checked = true;
        toggleModalFields(jenis);

        $('#modal_operator').val(opId).trigger('change');
        $('#modal_bahan').val(bahanId).trigger('change');
        $('#modal_finishing').val(finishing).trigger('change');
        $('#modal_finishing_2').val(finishing_2).trigger('change');

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
        document.getElementById('modal_jenis_file').value = "";
        document.getElementById('modal_catatan').value = "";
        document.getElementById('modal_p').value = "0";
        document.getElementById('modal_l').value = "0";
        document.getElementById('modal_qty').value = "1";

        if(document.getElementById('modal_harga_tampil')) document.getElementById('modal_harga_tampil').value = "";
        if(document.getElementById('modal_harga_asli')) document.getElementById('modal_harga_asli').value = "";

        $('#modal_operator').val('').trigger('change');
        $('#modal_bahan').val('').trigger('change');
        $('#modal_finishing').val('').trigger('change');
        $('#modal_finishing_2').val('').trigger('change');

        document.getElementById('m_outdoor').checked = true;
        toggleModalFields('outdoor');
    }

    document.getElementById('formSpk').addEventListener('submit', function(e) {
        if (!document.querySelector('#tabelItemBody tr:not(#row-kosong)')) {
            e.preventDefault();
            Swal.fire("Tabel Kosong", "Anda belum menambahkan item pesanan apapun.", "error");
        }
    });
</script>
@endpush
