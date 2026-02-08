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
                        <button type="button" class="btn btn-sm btn-info mb-0" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
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

{{-- INCLUDE MODAL TAMBAH ITEM (Sama persis dengan create.blade.php) --}}
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
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Operator (Penanggung Jawab)</label>
                            <select id="modal_operator" class="form-control" style="appearance: auto;">
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
                            <select id="modal_bahan" class="form-control" style="appearance: auto;">
                                <option value="" disabled selected>Pilih Bahan</option>
                                @foreach($bahans as $b)
                                <option value="{{ $b->id }}" data-nama="{{ $b->nama_bahan }}">{{ $b->nama_bahan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Qty</label>
                            <input type="number" id="modal_qty" class="form-control" value="1" min="1">
                        </div>
                    </div>
                </div>

                {{-- 4. Finishing & Catatan --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline">
                            <select id="modal_finishing" class="form-control" style="appearance: auto;">
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

    // Data existing dari database (dikirim controller)
    const existingItems = @json($spk -> items);

    document.addEventListener("DOMContentLoaded", function() {
        // Load data lama ke tabel
        existingItems.forEach(item => {

            let opNama = 'Unknown';
            if (item.operator) {
                opNama = item.operator.nama; // Sesuaikan dengan kolom nama di tabel users
            }

            let bhnNama = 'Unknown';
            if (item.bahan) {
                bhnNama = item.bahan.nama_bahan;
            }
            addItemToTable({
                jenis: item.jenis_order,
                file: item.nama_file,
                p: item.p,
                l: item.l,
                bahanId: item.bahan_id,
                bahanNama: item.bahan ? item.bahan.nama_bahan : 'Unknown', // Handle null check
                qty: item.qty,
                finishing: item.finishing,
                catatan: item.catatan,
                operatorId: item.operator_id,
                operatorNama: item.operator ? item.operator.nama : 'Unknown' // Handle null check
            });
        });
    });

    // Fungsi Render Baris (Dipakai saat Load Awal & Saat Tambah Baru)
    function addItemToTable(data) {
        let badgeColor = (data.jenis === 'outdoor') ? 'warning' : 'success';

        let html = `
            <tr id="item-${itemIndex}">
                <td>
                    <span class="badge bg-gradient-${badgeColor} mb-1">${data.jenis.toUpperCase()}</span><br>
                    <strong>${data.file}</strong><br>
                    <small class="text-xs text-secondary"><i class="fa fa-user me-1"></i> ${data.operatorNama}</small>

                    <input type="hidden" name="items[${itemIndex}][jenis]" value="${data.jenis}">
                    <input type="hidden" name="items[${itemIndex}][file]" value="${data.file}">
                    <input type="hidden" name="items[${itemIndex}][operator_id]" value="${data.operatorId}">
                    <input type="hidden" name="items[${itemIndex}][catatan]" value="${data.catatan || ''}">
                </td>
                <td class="text-xs">
                    ${data.p} x ${data.l} cm <br>
                    Bahan: <strong>${data.bahanNama}</strong> <br>
                    Fin: ${data.finishing || '-'}

                    <input type="hidden" name="items[${itemIndex}][p]" value="${data.p}">
                    <input type="hidden" name="items[${itemIndex}][l]" value="${data.l}">
                    <input type="hidden" name="items[${itemIndex}][bahan_id]" value="${data.bahanId}">
                    <input type="hidden" name="items[${itemIndex}][finishing]" value="${data.finishing || ''}">
                </td>
                <td class="text-center text-sm font-weight-bold">
                    ${data.qty}
                    <input type="hidden" name="items[${itemIndex}][qty]" value="${data.qty}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger px-3 mb-0" onclick="hapusItem(${itemIndex})">
                        <i class="material-icons text-sm">delete</i>
                    </button>
                </td>
            </tr>
        `;

        document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;
    }

    // Fungsi Hapus Baris
    function hapusItem(id) {
        document.getElementById('item-' + id).remove();
    }

    // Fungsi Tambah dari Modal (Panggil ini di tombol Simpan Modal)
    function tambahItem() {
        // ... (Logika ambil value dari modal sama persis dengan create.blade.php) ...
        // Bedanya panggil addItemToTable() di akhir

        let jenis = document.querySelector('input[name="modal_jenis"]:checked').value;
        let operatorSelect = document.getElementById('modal_operator');
        let file = document.getElementById('modal_nama_file').value;
        let p = document.getElementById('modal_p').value;
        let l = document.getElementById('modal_l').value;
        let bahanSelect = document.getElementById('modal_bahan');
        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value;
        let catatan = document.getElementById('modal_catatan').value;

        if (!file || !p || !l || !bahanSelect.value || !operatorSelect.value) {
            Swal.fire("Data Belum Lengkap", "Mohon lengkapi data item.", "warning");
            return;
        }

        addItemToTable({
            jenis: jenis,
            file: file,
            p: p,
            l: l,
            bahanId: bahanSelect.value,
            bahanNama: bahanSelect.options[bahanSelect.selectedIndex].text,
            qty: qty,
            finishing: finishing,
            catatan: catatan,
            operatorId: operatorSelect.value,
            operatorNama: operatorSelect.options[operatorSelect.selectedIndex].text
        });

        // Tutup Modal & Reset
        var modalEl = document.getElementById('modalTambahItem');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Reset form modal manual jika perlu
    }
</script>
@endpush
