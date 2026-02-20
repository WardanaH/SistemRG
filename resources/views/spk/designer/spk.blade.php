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
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">{{ $title }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk.store') }}" method="POST" id="formSpk">
                    @csrf

                    {{-- Toggle Mode Lembur --}}
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-auto" type="checkbox" id="toggleLembur" name="is_lembur" value="1">
                                <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="toggleLembur">
                                    <span class="font-weight-bold text-warning">Mode Lembur (Pindah Cabang)</span>
                                    <small class="d-block text-xs text-muted">Centang jika Anda sedang bekerja di cabang lain hari ini.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Pilihan Cabang Lembur (Hidden by Default) --}}
                    <div class="row mb-4" id="divCabangLembur" style="display: none;">
                        <div class="col-md-12">
                            <div class="input-group input-group-outline">
                                <select name="cabang_lembur_id" id="cabang_lembur_id" class="form-control" style="appearance: auto;">
                                    <option value="" disabled selected>Pilih Cabang Lokasi Lembur...</option>
                                    @foreach(\App\Models\MCabang::where('id', '!=', Auth::user()->cabang_id)->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-xs text-info">* SPK akan masuk ke Admin cabang yang dipilih di atas.</small>
                        </div>
                    </div>

                    {{-- I. HEADER & DATA PELANGGAN --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Data Umum & Pelanggan</p>
                    <div class="row mb-4">
                        {{-- Tanggal --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        {{-- Nama Pelanggan --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline @error('nama_pelanggan') is-invalid @enderror">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan') }}" required>
                            </div>
                            @error('nama_pelanggan') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>

                        {{-- No Telepon (Opsional untuk Reguler) --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">No. Telepon (Opsional)</label>
                                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}">
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- II. DAFTAR ITEM (TABEL) --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-sm text-uppercase font-weight-bold mb-0">II. Detail Item Pesanan</p>
                        <button type="button" class="btn btn-sm btn-info mb-0" onclick="prepareTambah()" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
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
                                    {{-- Baris Default Kosong --}}
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

                    {{-- III. DESIGNER (GLOBAL) --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Designer (Anda)</label>
                                <select class="form-control" disabled style="appearance: auto; padding-left: 10px;">
                                    <option>{{ Auth::user()->nama }}</option>
                                </select>
                                {{-- Input Hidden untuk ID Designer --}}
                                <input type="hidden" name="designer_id" value="{{ Auth::id() }}">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn bg-gradient-primary btn-lg w-100">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Simpan SPK Reguler
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ITEM (POPUP) --}}
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
                        <div class="d-flex flex-wrap gap-3 mt-1">
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
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="modal_jenis" id="m_charge" value="charge">
                                <label class="custom-control-label" for="m_charge">Charge Desain</label>
                            </div>
                        </div>
                    </div>
                    {{-- Operator Dipilih Disini (Per Item) --}}
                    <div class="col-md-6">
                        <div class="input-group input-group-outline">
                            <select id="modal_operator"
                                class="form-control select2"
                                data-placeholder="Cari & Pilih Operator Produksi..."
                                style="appearance: auto;">
                                <option value="" disabled selected>Pilih Operator...</option>
                                @foreach($operators as $op)
                                <option value="{{ $op->id }}" data-nama="{{ $op->nama }}">{{ $op->nama }} - {{ $op->roles()->pluck('name')->implode(', ') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. Nama File & Harga (Khusus Charge) --}}
                <div class="row mb-3">
                    <div class="col-md-12 mb-3">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Nama File / Keterangan Desain</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>

                    {{-- Input Harga (Disembunyikan default, hanya muncul jika Charge) --}}
                    <div class="col-md-12" id="sec_harga" style="display: none;">
                        <div class="input-group input-group-outline">
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
                            <input type="number" step="0.01" id="modal_p" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">L (cm)</label>
                            <input type="number" step="0.01" id="modal_l" class="form-control" value="0">
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
                            <input type="number" id="modal_qty" class="form-control" value="1" min="1">
                        </div>
                    </div>
                </div>

                {{-- 4. Finishing & Catatan --}}
                <div class="row">
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

    function prepareTambah() {
        resetModal(); // Membersihkan form dan me-set editId = null
    }

    function tambahItem() {
        let jenis = document.querySelector('input[name="modal_jenis"]:checked').value;

        // Ambil value
        let operatorSelect = document.getElementById('modal_operator');
        let operatorId = operatorSelect.value || null;
        let operatorNama = operatorSelect.options[operatorSelect.selectedIndex]?.text || '-';

        let file = document.getElementById('modal_nama_file').value;
        let p = document.getElementById('modal_p').value || 0;
        let l = document.getElementById('modal_l').value || 0;

        let bahanSelect = document.getElementById('modal_bahan');
        let bahanId = bahanSelect.value || null;
        let bahanNama = bahanSelect.options[bahanSelect.selectedIndex]?.text || '-';

        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value || '-';
        let catatan = document.getElementById('modal_catatan').value;

        // AMBIL VALUE HARGA
        let harga = document.getElementById('modal_harga').value || 0;

        // VALIDASI KHUSUS CHARGE DESAIN
        if (jenis === 'charge') {
            if (!file || !qty || !harga) {
                Swal.fire("Data Belum Lengkap", "Mohon isi Nama File, Qty, dan Nominal Harga!", "warning");
                return;
            }
        } else {
            // Validasi Normal
            if (!file || !p || !l || !bahanId || !operatorId || !qty) {
                Swal.fire("Data Belum Lengkap", "Mohon lengkapi data item.", "warning");
                return;
            }
        }

        // Tentukan Warna Badge
        let colors = { 'outdoor': 'warning', 'indoor': 'success', 'multi': 'info', 'dtf': 'primary', 'charge': 'dark' };
        let badgeColor = colors[jenis] || 'secondary';

        if (editId !== null) {
            let row = document.getElementById(`item-${editId}`);
            row.innerHTML = buatHtmlRow(editId, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga);
            editId = null;
        } else {
            let rowKosong = document.getElementById('row-kosong');
            if (rowKosong) rowKosong.remove();
            let html = `<tr id="item-${itemIndex}">${buatHtmlRow(itemIndex, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga)}</tr>`;
            document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
            itemIndex++;
        }

        resetModal();
        bootstrap.Modal.getInstance(document.getElementById('modalTambahItem')).hide();
    }

    // Fungsi Helper buat isi Row (agar bisa dipakai Tambah & Edit)
    // Tambahkan parameter harga di fungsi ini
    function buatHtmlRow(idx, jenis, badgeColor, operatorId, operatorNama, file, catatan, p, l, bahanId, bahanNama, qty, finishing, harga) {
        const displayUkuran = (jenis === 'charge') ? '-' : `${p} x ${l}`;
        const displayBahan = (jenis === 'charge') ? '-' : bahanNama;
        const displayOperator = (jenis === 'charge') ? '<i class="fa fa-paint-brush me-1"></i> Biaya Desain' : `<i class="fa fa-user me-1"></i> ${operatorNama}`;

        // Format angka jadi Rupiah
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
                <small class="text-xxs text-secondary">${catatan || '-'}</small>
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

    function resetModal() {
        editId = null;

        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";
        document.getElementById('modal_p').value = "0";
        document.getElementById('modal_l').value = "0";
        document.getElementById('modal_qty').value = "1";
        document.getElementById('modal_harga').value = ""; // Reset harga

        $('#modal_operator').val('').trigger('change');
        $('#modal_bahan').val('').trigger('change');
        $('#modal_finishing').val('').trigger('change');

        const operatorSection = document.getElementById('modal_operator').closest('.col-md-6');
        const specSection = document.getElementById('modal_p').closest('.row');
        const finishingSection = document.getElementById('modal_finishing').closest('.col-md-6');
        const hargaSection = document.getElementById('sec_harga'); // Bagian Harga

        operatorSection.style.display = 'block';
        specSection.querySelectorAll('.col-md-3, .col-md-4, .col-md-2').forEach(el => el.style.display = 'block');
        finishingSection.style.display = 'block';
        hargaSection.style.display = 'none'; // Sembunyikan harga default

        document.getElementById('m_outdoor').checked = true;
        document.getElementById('modalLabel').innerText = "Tambah Detail Item";
    }

    function editItem(id) {
        editId = id;
        let row = document.getElementById(`item-${id}`);

        let jenis = row.querySelector(`input[name="items[${id}][jenis]"]`).value;
        let opId = row.querySelector(`input[name="items[${id}][operator_id]"]`).value;
        let file = row.querySelector(`input[name="items[${id}][file]"]`).value;
        let p = row.querySelector(`input[name="items[${id}][p]"]`).value;
        let l = row.querySelector(`input[name="items[${id}][l]"]`).value;
        let bahanId = row.querySelector(`input[name="items[${id}][bahan_id]"]`).value;
        let qty = row.querySelector(`input[name="items[${id}][qty]"]`).value;
        let catatan = row.querySelector(`input[name="items[${id}][catatan]"]`).value;
        let finishing = row.querySelector(`input[name="items[${id}][finishing]"]`).value;
        let harga = row.querySelector(`input[name="items[${id}][harga]"]`) ? row.querySelector(`input[name="items[${id}][harga]"]`).value : '';

        // T-rigger event change pada radio untuk mengubah tampilan form
        let radioJenis = document.querySelector(`input[name="modal_jenis"][value="${jenis}"]`);
        radioJenis.checked = true;
        radioJenis.dispatchEvent(new Event('change')); // Memicu script show/hide di bawah

        document.getElementById('modal_nama_file').value = file;
        document.getElementById('modal_p').value = p;
        document.getElementById('modal_l').value = l;
        document.getElementById('modal_qty').value = qty;
        document.getElementById('modal_catatan').value = catatan;
        document.getElementById('modal_harga').value = harga;

        // Fix Material Dashboard floating label
        if(harga) document.getElementById('modal_harga').parentElement.classList.add('is-filled');

        $('#modal_operator').val(opId).trigger('change');
        $('#modal_bahan').val(bahanId).trigger('change');
        $('#modal_finishing').val(finishing).trigger('change');

        document.getElementById('modalLabel').innerText = "Edit Detail Item";
        new bootstrap.Modal(document.getElementById('modalTambahItem')).show();
    }

    function hapusItem(id) {
        document.getElementById('item-' + id).remove();

        // Jika tabel kosong, kembalikan baris default
        if (document.getElementById('tabelItemBody').children.length === 0) {
            document.getElementById('tabelItemBody').innerHTML = `
                <tr id="row-kosong">
                    <td colspan="6" class="text-center text-secondary text-sm py-4">
                        <i class="material-icons opacity-10" style="font-size: 3rem;">add_shopping_cart</i><br>
                        Belum ada item. Klik tombol <b>+ Tambah Item</b> di atas kanan.
                    </td>
                </tr>
            `;
        }
    }

    // Validasi saat Tombol Simpan ditekan
    document.getElementById('formSpk').addEventListener('submit', function(e) {
        let items = document.querySelectorAll('#tabelItemBody tr');
        let hasData = false;

        // Cek apakah ada row selain row-kosong
        items.forEach(tr => {
            if (tr.id !== 'row-kosong') hasData = true;
        });

        if (!hasData) {
            e.preventDefault();
            Swal.fire("Tabel Kosong", "Anda belum menambahkan item pesanan apapun.", "error");
        }
    });

    document.querySelectorAll('input[name="modal_jenis"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isCharge = this.value === 'charge';
            const operatorSection = document.getElementById('modal_operator').closest('.col-md-6');
            const specSection = document.getElementById('modal_p').closest('.row'); // Baris P, L, Bahan, Qty
            const finishingSection = document.getElementById('modal_finishing').closest('.col-md-6');

            if (isCharge) {
                operatorSection.style.display = 'none';
                specSection.querySelectorAll('.col-md-3, .col-md-4').forEach(el => el.style.display = 'none'); // Sembunyikan P, L, Bahan
                finishingSection.style.display = 'none';
            } else {
                operatorSection.style.display = 'block';
                specSection.querySelectorAll('.col-md-3, .col-md-4, .col-md-2').forEach(el => el.style.display = 'block');
                finishingSection.style.display = 'block';
            }
        });
    });

    // EVENT LISTENER RADIO BUTTON (Tampilkan/Sembunyikan Field)
    document.querySelectorAll('input[name="modal_jenis"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isCharge = this.value === 'charge';
            const operatorSection = document.getElementById('modal_operator').closest('.col-md-6');
            const specSection = document.getElementById('modal_p').closest('.row');
            const finishingSection = document.getElementById('modal_finishing').closest('.col-md-6');
            const hargaSection = document.getElementById('sec_harga'); // Bagian Harga

            if (isCharge) {
                operatorSection.style.display = 'none';
                specSection.querySelectorAll('.col-md-3, .col-md-4').forEach(el => el.style.display = 'none');
                finishingSection.style.display = 'none';
                hargaSection.style.display = 'block'; // Tampilkan Harga
            } else {
                operatorSection.style.display = 'block';
                specSection.querySelectorAll('.col-md-3, .col-md-4, .col-md-2').forEach(el => el.style.display = 'block');
                finishingSection.style.display = 'block';
                hargaSection.style.display = 'none'; // Sembunyikan Harga
            }
        });
    });
</script>

<script>
    const userCabangId = "{{ Auth::user()->cabang_id }}"; // Cabang Asal

    // Toggle Tampilan Dropdown Cabang
    document.getElementById('toggleLembur').addEventListener('change', function() {
        const divLembur = document.getElementById('divCabangLembur');
        const selectLembur = document.getElementById('cabang_lembur_id');

        if (this.checked) {
            divLembur.style.display = 'block';
            selectLembur.setAttribute('required', 'required');
        } else {
            divLembur.style.display = 'none';
            selectLembur.removeAttribute('required');
            selectLembur.value = ""; // Reset pilihan

            // Kembalikan list operator ke cabang asal
            fetchOperators(userCabangId);
        }
    });

    // Event Listener saat Cabang Lembur dipilih
    document.getElementById('cabang_lembur_id').addEventListener('change', function() {
        const selectedCabangId = this.value;
        if (selectedCabangId) {
            fetchOperators(selectedCabangId); // Load operator cabang tersebut
        }
    });

    // Fungsi AJAX untuk ambil data operator berdasarkan cabang
    function fetchOperators(cabangId) {
        // Tampilkan loading di dropdown
        const opSelect = document.getElementById('modal_operator');
        opSelect.innerHTML = '<option disabled selected>Loading...</option>';

        // Panggil API (Buat route baru di web.php nanti)
        fetch(`/api/get-operators/getall`)
            .then(response => response.json())
            .then(data => {
                let html = '<option value="" disabled selected>Pilih Operator...</option>';
                data.forEach(op => {
                    html += `<option value="${op.id}" data-nama="${op.nama}">${op.nama} - ${op.roles}</option>`;
                });
                opSelect.innerHTML = html;
            })
            .catch(err => {
                console.error('Error fetching operators:', err);
                opSelect.innerHTML = '<option disabled>Gagal memuat operator</option>';
            });
    }
</script>
@endpush
