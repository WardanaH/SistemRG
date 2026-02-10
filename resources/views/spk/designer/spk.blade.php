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
                        <button type="button" class="btn btn-sm btn-info mb-0" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
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

                {{-- 2. Nama File --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Nama File</label>
                            <input type="text" id="modal_nama_file" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- 3. Spesifikasi --}}
                <div class="row mb-3">
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

    function tambahItem() {
        // 1. AMBIL VALUE DARI MODAL
        let jenis = document.querySelector('input[name="modal_jenis"]:checked').value;

        let operatorSelect = document.getElementById('modal_operator');
        let operatorId = operatorSelect.value;
        let operatorNama = operatorSelect.options[operatorSelect.selectedIndex]?.text;

        let file = document.getElementById('modal_nama_file').value;
        let p = document.getElementById('modal_p').value;
        let l = document.getElementById('modal_l').value;

        let bahanSelect = document.getElementById('modal_bahan');
        let bahanId = bahanSelect.value;
        let bahanNama = bahanSelect.options[bahanSelect.selectedIndex]?.text;

        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value;
        let catatan = document.getElementById('modal_catatan').value;

        // 2. VALIDASI INPUT
        if (!file || !p || !l || !bahanId || !operatorId || !qty) {
            Swal.fire("Data Belum Lengkap", "Mohon lengkapi Operator, Nama File, Ukuran, Bahan, dan Qty.", "warning");
            return;
        }

        // 3. HAPUS BARIS "BELUM ADA ITEM"
        let rowKosong = document.getElementById('row-kosong');
        if (rowKosong) rowKosong.remove();

        // 4. BUAT HTML ROW BARU
        // Warna badge pembeda
        let badgeColor = (jenis === 'outdoor') ? 'warning' : 'success';

        let html = `
            <tr id="item-${itemIndex}">
                <td>
                    <span class="badge bg-gradient-${badgeColor} mb-1">${jenis.toUpperCase()}</span><br>
                    <span class="text-xs font-weight-bold text-dark"><i class="fa fa-user me-1"></i> ${operatorNama}</span>

                    {{-- Input Hidden untuk dikirim ke Controller --}}
                    <input type="hidden" name="items[${itemIndex}][jenis]" value="${jenis}">
                    <input type="hidden" name="items[${itemIndex}][operator_id]" value="${operatorId}">
                </td>
                <td>
                    <h6 class="mb-0 text-sm text-truncate" style="max-width: 150px;">${file}</h6>
                    <small class="text-xxs text-secondary">${catatan || '-'}</small>
                    <input type="hidden" name="items[${itemIndex}][file]" value="${file}">
                    <input type="hidden" name="items[${itemIndex}][catatan]" value="${catatan}">
                </td>
                <td class="text-xs font-weight-bold">
                    ${p} x ${l}
                    <input type="hidden" name="items[${itemIndex}][p]" value="${p}">
                    <input type="hidden" name="items[${itemIndex}][l]" value="${l}">
                </td>
                <td class="text-xs font-weight-bold">
                    ${bahanNama}
                    <input type="hidden" name="items[${itemIndex}][bahan_id]" value="${bahanId}">
                </td>
                <td class="text-center text-sm">
                    ${qty}
                    <input type="hidden" name="items[${itemIndex}][qty]" value="${qty}">
                    <input type="hidden" name="items[${itemIndex}][finishing]" value="${finishing}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="hapusItem(${itemIndex})">
                        <i class="material-icons text-sm">delete</i>
                    </button>
                </td>
            </tr>
        `;

        // 5. MASUKKAN KE TABEL
        document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;

        // 6. RESET FORM MODAL
        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";
        // Opsional: Reset ukuran ke 0 atau kosong
        // document.getElementById('modal_p').value = "";

        // 7. TUTUP MODAL
        var modalEl = document.getElementById('modalTambahItem');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Notif Kecil
        Swal.fire({
            icon: 'success',
            title: 'Item Ditambahkan',
            text: 'Item berhasil masuk ke daftar sementara.',
            timer: 1000,
            showConfirmButton: false
        });
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
