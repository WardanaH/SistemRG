@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert untuk Flash Message --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "{{ session('success') }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif
@if (session('error'))
<script>
    Swal.fire({
        icon: "error",
        title: "Gagal!",
        text: "{{ session('error') }}",
        showConfirmButton: true
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">{{ $title }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk-bantuan.store') }}" method="POST" id="formSpk">
                    @csrf

                    {{-- HEADER: INFO CABANG & TANGGAL --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <select name="asal_cabang_id"
                                    class="form-control select2"
                                    data-placeholder="Cari & Pilih Cabang..."
                                    style="appearance: auto;"
                                    required>
                                    <option value="" disabled selected>-- Pilih Cabang Pengirim --</option>
                                    @foreach($cabangLain as $cb)
                                    <option value="{{ $cb->id }}">{{ $cb->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y H:i:s') }}" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION I: DATA PELANGGAN (HEADER SPK) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2 border-bottom">I. Data Pelanggan</p>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">No. Telepon (WA)</label>
                                <input type="number" name="no_telepon" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION II: DETAIL ITEM (TABEL) --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-sm text-uppercase font-weight-bold mb-0">II. Daftar Item Pesanan</p>
                        <button type="button" class="btn btn-sm btn-info mb-0" data-bs-toggle="modal" data-bs-target="#modalTambahItem" onclick="prepareTambah()">
                            <i class="fa fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="card border mb-3">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Jenis & Operator</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">File & Ket</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Ukuran</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Bahan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelItemBody">
                                    <tr id="row-kosong">
                                        <td colspan="6" class="text-center text-secondary text-sm py-4">
                                            <i class="material-icons opacity-10" style="font-size: 3rem;">add_shopping_cart</i><br>
                                            Belum ada item. Klik tombol <b>+ Tambah Item</b> di atas kanan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark btn-lg" id="btnSimpan">
                            <i class="material-icons text-sm">save</i> Simpan SPK Bantuan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ITEM --}}
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

                {{-- 1. Jenis Order & Operator --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold">Jenis Order:</label>
                        <div class="d-flex gap-3 mt-1">
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
                                <label class="custom-control-label" for="m_dtf">DTF UV</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline is-filled">
                            <select id="modal_operator"
                                class="form-control select2"
                                data-placeholder="Cari & Pilih Operator Produksi..."
                                style="appearance: auto;">
                                <option value="" disabled selected>Loading Operator...</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. Nama File & Harga --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Nama File / Keterangan Desain</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Input Harga (Disembunyikan default, hanya muncul jika Charge) --}}
                <div class="row mb-3" id="sec_harga" style="display: none;">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Nominal Harga (Rp)</label>
                            <input type="number" id="modal_harga" class="form-control" min="0">
                        </div>
                    </div>
                </div>

                {{-- 3. Spesifikasi --}}
                <div class="row mb-3" id="sec_spesifikasi">
                    <div class="col-md-3">
                        <div class="input-group input-group-outline">
                            <label class="form-label">P (cm)</label>
                            <input type="number" step="0.01" id="modal_p" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline">
                            <label class="form-label">L (cm)</label>
                            <input type="number" step="0.01" id="modal_l" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-outline">
                            <select id="modal_bahan"
                                class="form-control select2"
                                data-placeholder="Cari & Pilih Bahan..."
                                style="appearance: auto;">
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
                            <input type="number" id="modal_qty" class="form-control" value="1">
                        </div>
                    </div>
                </div>

                {{-- 4. Finishing & Catatan --}}
                <div class="row" id="sec_finishing">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline">
                            <select id="modal_finishing"
                                class="form-control select2"
                                data-placeholder="Cari & Pilih Finishing..."
                                style="appearance: auto;">
                                <option value="" disabled selected>Pilih Finishing...</option>
                                @foreach($finishings as $f)
                                <option value="{{ $f->nama_finishing }}">{{ $f->nama_finishing }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Catatan Item</label>
                            <input type="text" id="modal_catatan" class="form-control">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn bg-gradient-info" onclick="tambahItem()">Simpan ke Daftar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let itemIndex = 0;
    let editId = null;

    const userCabangId = "{{ Auth::user()->cabang_id }}";

    // --- FUNGSI HELPER ---
    function getActiveCabang() {
        return userCabangId;
    }

    function getActiveJenis() {
        let radio = document.querySelector('input[name="modal_jenis"]:checked');
        return radio ? radio.value : 'outdoor';
    }

    // --- EVENT LISTENERS ---
    document.querySelectorAll('input[name="modal_jenis"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const jenis = this.value;
            const isCharge = jenis === 'charge';

            const operatorSection = document.getElementById('modal_operator').closest('.col-md-6');
            const specSection = document.getElementById('sec_spesifikasi');
            const finishingSection = document.getElementById('sec_finishing');
            const hargaSection = document.getElementById('sec_harga');

            if (isCharge) {
                operatorSection.style.display = 'none';
                specSection.style.display = 'none';
                finishingSection.style.display = 'none';
                hargaSection.style.display = 'flex';
            } else {
                operatorSection.style.display = 'block';
                specSection.style.display = 'flex';
                finishingSection.style.display = 'flex';
                hargaSection.style.display = 'none';

                fetchOperators(getActiveCabang(), jenis);
            }
        });
    });

    // --- AJAX FETCH OPERATOR ---
    function fetchOperators(cabangId, jenisOrder) {
        if (jenisOrder === 'charge') return;

        const opSelect = $('#modal_operator');
        opSelect.html('<option disabled selected>Loading...</option>').trigger('change');

        fetch(`/api/get-operators/${cabangId}?jenis=${jenisOrder}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                let html = '<option value="" disabled selected>Pilih Operator...</option>';
                data.forEach(op => {
                    html += `<option value="${op.id}">${op.nama} - ${op.roles}</option>`;
                });
                opSelect.html(html).trigger('change');
            })
            .catch(err => {
                console.error('Error fetching operators:', err);
                opSelect.html('<option disabled>Gagal memuat operator</option>').trigger('change');
            });
    }

    // --- FUNGSI CRUD ITEM ---
    function prepareTambah() {
        resetModal();
        document.getElementById('modalLabel').innerText = "Tambah Detail Item";
        editId = null;
        fetchOperators(getActiveCabang(), getActiveJenis());
    }

    function tambahItem() {
        let jenis = getActiveJenis();

        let operatorSelect = document.getElementById('modal_operator');
        let operatorId = operatorSelect.value || null;
        let operatorNama = operatorSelect.options[operatorSelect.selectedIndex]?.text || '-';

        let file = document.getElementById('modal_nama_file').value;
        let p = parseFloat(document.getElementById('modal_p').value) || 0;
        let l = parseFloat(document.getElementById('modal_l').value) || 0;

        let bahanSelect = document.getElementById('modal_bahan');
        let bahanId = bahanSelect.value || null;
        let bahanNama = bahanSelect.options[bahanSelect.selectedIndex]?.text || '-';

        let qty = parseInt(document.getElementById('modal_qty').value) || 1;
        let finishing = document.getElementById('modal_finishing').value || '-';
        let catatan = document.getElementById('modal_catatan').value || '-';

        let hargaElem = document.getElementById('modal_harga');
        let harga = hargaElem ? (parseFloat(hargaElem.value) || 0) : 0;

        // Validasi
        if (jenis === 'charge') {
            if (!file || !qty || !harga) {
                Swal.fire("Data Belum Lengkap", "Mohon isi Nama File, Qty, dan Nominal Harga!", "warning");
                return;
            }
        } else {
            if (!file || !bahanId || !operatorId) {
                Swal.fire("Data Belum Lengkap", "Pastikan Operator, Nama File, dan Bahan sudah diisi.", "warning");
                return;
            }
        }

        let colors = { 'outdoor': 'danger', 'indoor': 'success', 'multi': 'info', 'dtf': 'primary', 'charge': 'dark' };
        let badgeColor = colors[jenis] || 'secondary';

        if (editId !== null) {
            let row = document.getElementById(`item-${editId}`);
            if(row) {
                row.innerHTML = buatHtmlRow(editId, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga);
            }
            editId = null;
        } else {
            let rowKosong = document.getElementById('row-kosong');
            if (rowKosong) rowKosong.remove();

            let html = `<tr id="item-${itemIndex}">${buatHtmlRow(itemIndex, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga)}</tr>`;
            document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
            itemIndex++;
        }

        resetModal();

        let myModalEl = document.getElementById('modalTambahItem');
        let modal = bootstrap.Modal.getInstance(myModalEl);
        if(modal) {
            modal.hide();
        }

        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Item ditambahkan.', timer: 1000, showConfirmButton: false });
    }

    function buatHtmlRow(idx, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga) {
        const displayUkuran = (jenis === 'charge') ? '-' : `${p} x ${l}`;
        const displayBahan = (jenis === 'charge') ? '-' : bahanNama;
        const displayOperator = (jenis === 'charge') ? '<i class="fa fa-paint-brush me-1"></i> Biaya Desain' : `<i class="fa fa-user me-1"></i> ${operatorNama}`;
        const displayFinishing = (jenis === 'charge') ? '' : `<br><span class="text-xs font-weight-bold">Fin: ${finishing !== '-' ? finishing : ''}</span>`;

        let formatRupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(harga);
        const displayHarga = (jenis === 'charge') ? `<br><span class="text-success font-weight-bold text-xs">${formatRupiah}</span>` : '';

        return `
            <td>
                <span class="badge bg-gradient-${badgeColor} mb-1">${jenis.toUpperCase()}</span><br>
                <span class="text-xs font-weight-bold text-dark">${displayOperator}</span>
                <input type="hidden" name="items[${idx}][jenis]" value="${jenis}">
                <input type="hidden" name="items[${idx}][operator_id]" value="${operatorId}">
            </td>
            <td>
                <h6 class="mb-0 text-sm text-truncate" style="max-width: 150px;">${file}</h6>
                <small class="text-xxs text-secondary">${catatan}</small>
                ${displayHarga}
                <input type="hidden" name="items[${idx}][file]" value="${file}">
                <input type="hidden" name="items[${idx}][catatan]" value="${catatan}">
                <input type="hidden" name="items[${idx}][harga]" value="${harga}">
            </td>
            <td class="text-xs font-weight-bold">
                ${displayUkuran}
                <input type="hidden" name="items[${idx}][p]" value="${p}">
                <input type="hidden" name="items[${idx}][l]" value="${l}">
            </td>
            <td class="text-xs font-weight-bold">
                ${displayBahan}
                ${displayFinishing}
                <input type="hidden" name="items[${idx}][bahan_id]" value="${bahanId}">
            </td>
            <td class="text-center text-sm">
                ${qty}
                <input type="hidden" name="items[${idx}][qty]" value="${qty}">
                <input type="hidden" name="items[${idx}][finishing]" value="${finishing}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-info px-2 mb-0" onclick="editItem(${idx})"><i class="material-icons text-sm">edit</i></button>
                <button type="button" class="btn btn-link text-danger px-2 mb-0" onclick="hapusItem(${idx})"><i class="material-icons text-sm">delete</i></button>
            </td>
        `;
    }

    function editItem(id) {
        editId = id;
        let row = document.getElementById(`item-${id}`);
        if(!row) return;

        let jenis = row.querySelector(`input[name="items[${id}][jenis]"]`).value;
        let opId = row.querySelector(`input[name="items[${id}][operator_id]"]`).value;
        let file = row.querySelector(`input[name="items[${id}][file]"]`).value;
        let p = row.querySelector(`input[name="items[${id}][p]"]`).value;
        let l = row.querySelector(`input[name="items[${id}][l]"]`).value;
        let bahanId = row.querySelector(`input[name="items[${id}][bahan_id]"]`).value;
        let qty = row.querySelector(`input[name="items[${id}][qty]"]`).value;
        let catatan = row.querySelector(`input[name="items[${id}][catatan]"]`).value;
        let finishing = row.querySelector(`input[name="items[${id}][finishing]"]`).value;

        let hargaInput = row.querySelector(`input[name="items[${id}][harga]"]`);
        let harga = hargaInput ? hargaInput.value : '';

        let radioJenis = document.querySelector(`input[name="modal_jenis"][value="${jenis}"]`);
        if(radioJenis) {
            radioJenis.checked = true;
            radioJenis.dispatchEvent(new Event('change'));
        }

        document.getElementById('modal_nama_file').value = file;
        document.getElementById('modal_p').value = p;
        document.getElementById('modal_l').value = l;
        document.getElementById('modal_qty').value = qty;
        document.getElementById('modal_catatan').value = catatan !== '-' ? catatan : '';

        let modalHarga = document.getElementById('modal_harga');
        if(modalHarga) {
            modalHarga.value = harga;
            if(harga) modalHarga.parentElement.classList.add('is-filled');
        }

        $('#modal_bahan').val(bahanId).trigger('change');
        $('#modal_finishing').val(finishing !== '-' ? finishing : '').trigger('change');

        if (jenis !== 'charge') {
            fetch(`/api/get-operators/${getActiveCabang()}?jenis=${jenis}`)
                .then(res => res.json())
                .then(data => {
                    let html = '<option value="" disabled>Pilih Operator...</option>';
                    data.forEach(op => {
                        html += `<option value="${op.id}">${op.nama} - ${op.roles}</option>`;
                    });
                    $('#modal_operator').html(html).val(opId).trigger('change');
                });
        }

        document.getElementById('modalLabel').innerText = "Edit Detail Item";
        let myModal = new bootstrap.Modal(document.getElementById('modalTambahItem'));
        myModal.show();
    }

    function resetModal() {
        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";
        document.getElementById('modal_p').value = "0";
        document.getElementById('modal_l').value = "0";
        document.getElementById('modal_qty').value = "1";

        let modalHarga = document.getElementById('modal_harga');
        if(modalHarga) modalHarga.value = "";

        $('#modal_operator').val('').trigger('change');
        $('#modal_bahan').val('').trigger('change');
        $('#modal_finishing').val('').trigger('change');

        let radioOutdoor = document.getElementById('m_outdoor');
        if(radioOutdoor) radioOutdoor.checked = true;

        // Reset tampilan sections
        const operatorSection = document.getElementById('modal_operator')?.closest('.col-md-6');
        const specSection = document.getElementById('sec_spesifikasi');
        const finishingSection = document.getElementById('sec_finishing');
        const hargaSection = document.getElementById('sec_harga');

        if(operatorSection) operatorSection.style.display = 'block';
        if(specSection) specSection.style.display = 'flex';
        if(finishingSection) finishingSection.style.display = 'flex';
        if(hargaSection) hargaSection.style.display = 'none';
    }

    function hapusItem(id) {
        let row = document.getElementById('item-' + id);
        if(row) row.remove();

        let tbody = document.getElementById('tabelItemBody');
        if (tbody && tbody.children.length === 0) {
            tbody.innerHTML = `
                <tr id="row-kosong">
                    <td colspan="6" class="text-center text-secondary text-sm py-4">
                        <i class="material-icons opacity-10" style="font-size: 3rem;">add_shopping_cart</i><br>
                        Belum ada item pesanan.
                    </td>
                </tr>
            `;
        }
    }

    document.getElementById('formSpk').addEventListener('submit', function(e) {
        if (!document.querySelector('#tabelItemBody tr:not(#row-kosong)')) {
            e.preventDefault();
            Swal.fire("Tabel Kosong", "Anda belum menambahkan item pesanan apapun.", "error");
        }
    });
</script>
@endpush
