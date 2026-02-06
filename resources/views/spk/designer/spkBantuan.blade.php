@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert untuk Flash Message --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    Swal.fire({ icon: "success", title: "Berhasil!", text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
</script>
@endif
@if (session('error'))
<script>
    Swal.fire({ icon: "error", title: "Gagal!", text: "{{ session('error') }}", showConfirmButton: true });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Input SPK Bantuan (Multi Item)</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk-bantuan.store') }}" method="POST" id="formSpk">
                    @csrf

                    {{-- HEADER: INFO CABANG & TANGGAL --}}
                    <div class="alert alert-info text-white text-sm mb-3" role="alert">
                        <strong>Mode Multi-Item:</strong> Anda dapat memasukkan banyak file dalam satu Nomor SPK.
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Asal Order (Dari Cabang Mana?)</label>
                                <select name="asal_cabang_id" class="form-control" required style="appearance: auto;">
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
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y') }}" readonly>
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
                                    <tr id="row-kosong">
                                        <td colspan="6" class="text-center text-secondary text-sm py-4">
                                            <i class="material-icons opacity-10" style="font-size: 3rem;">shopping_cart</i><br>
                                            Belum ada item. Klik tombol <b>+ Tambah Item</b> di atas kanan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark btn-lg" id="btnSimpan">
                            <i class="material-icons text-sm">save</i> Simpan SPK Lengkap
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
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline is-filled">
                            <label class="form-label">Operator (Penanggung Jawab Item Ini)</label>
                            <select id="modal_operator" class="form-control" style="appearance: auto;">
                                <option value="" disabled selected>Pilih Operator...</option>
                                @foreach($operators as $op)
                                    <option value="{{ $op->id }}" data-nama="{{ $op->nama }}">{{ $op->nama }}</option>
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
                            <input type="number" id="modal_qty" class="form-control" value="1">
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

    function tambahItem() {
        // AMBIL DATA DARI MODAL
        let jenis = document.querySelector('input[name="modal_jenis"]:checked').value;
        let operatorSelect = document.getElementById('modal_operator');
        let operatorId = operatorSelect.value;
        let operatorNama = operatorSelect.options[operatorSelect.selectedIndex]?.text; // Pakai ?.text jaga-jaga null

        let file = document.getElementById('modal_nama_file').value;
        let p = document.getElementById('modal_p').value;
        let l = document.getElementById('modal_l').value;

        let bahanSelect = document.getElementById('modal_bahan');
        let bahanId = bahanSelect.value;
        let bahanNama = bahanSelect.options[bahanSelect.selectedIndex]?.text;

        let qty = document.getElementById('modal_qty').value;
        let finishing = document.getElementById('modal_finishing').value;
        let catatan = document.getElementById('modal_catatan').value;

        // VALIDASI
        if(!file || !p || !l || !bahanId || !operatorId || !qty) {
            Swal.fire("Data Belum Lengkap", "Pastikan Operator, Nama File, Ukuran, Bahan, dan Qty sudah diisi.", "warning");
            return;
        }

        // HAPUS ROW KOSONG
        let rowKosong = document.getElementById('row-kosong');
        if(rowKosong) rowKosong.remove();

        // RENDER KE TABEL
        let badgeColor = (jenis === 'outdoor') ? 'danger' : 'success';

        let html = `
            <tr id="item-${itemIndex}">
                <td>
                    <span class="badge bg-gradient-${badgeColor} mb-1">${jenis.toUpperCase()}</span><br>
                    <span class="text-xs font-weight-bold text-dark"><i class="fa fa-user me-1"></i> ${operatorNama}</span>

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

        document.getElementById('tabelItemBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;

        // RESET & TUTUP MODAL
        document.getElementById('modal_nama_file').value = "";
        document.getElementById('modal_catatan').value = "";

        // Tutup Modal
        var modalEl = document.getElementById('modalTambahItem');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        Swal.fire({
            icon: 'success',
            title: 'Item Ditambahkan',
            text: 'Silakan tambah item lagi atau simpan SPK.',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function hapusItem(id) {
        document.getElementById('item-' + id).remove();
        // Cek jika kosong, tampilkan row kosong lagi
        if(document.getElementById('tabelItemBody').children.length === 0) {
            document.getElementById('tabelItemBody').innerHTML = `
                <tr id="row-kosong">
                    <td colspan="6" class="text-center text-secondary text-sm py-4">
                        <i class="material-icons opacity-10" style="font-size: 3rem;">shopping_cart</i><br>
                        Belum ada item. Klik tombol <b>+ Tambah Item</b> di atas kanan.
                    </td>
                </tr>
            `;
        }
    }

    // Validasi Submit agar tidak mengirim tabel kosong
    document.getElementById('formSpk').addEventListener('submit', function(e) {
        let items = document.querySelectorAll('#tabelItemBody tr');
        let hasData = false;

        items.forEach(tr => {
            if(tr.id !== 'row-kosong') hasData = true;
        });

        if(!hasData) {
            e.preventDefault();
            Swal.fire("Gagal", "Anda belum memasukkan Item apapun ke dalam tabel.", "error");
        }
    });
</script>
@endpush
