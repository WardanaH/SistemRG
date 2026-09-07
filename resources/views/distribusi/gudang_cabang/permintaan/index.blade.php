@extends('distribusi.layouts.app') {{-- Sesuaikan amun ngaran file layout-nya beda --}}

@section('title', 'Permintaan Barang ke Gudang Utama')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Form Permintaan Barang ke Gudang Utama</h3>
        </div>
        <div class="card-body">
            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success text-white">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-white">{{ session('error') }}</div>
            @endif

            {{-- Form Request --}}
            <form action="{{ route('gudang-cabang.permintaan.store') }}" method="POST" id="form-permintaan">
                @csrf

                <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                <div class="input-group input-group-outline mb-4">
                    <textarea name="keterangan" class="form-control" rows="4" placeholder="contoh: Stok gasan minggu depan..."
                        required></textarea>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="tabel-barang">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama
                                    Barang <span class="text-danger">*</span></th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Sisa Stok Cabang <span class="text-danger">*</span></th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Jumlah Diminta <span class="text-danger">*</span></th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Keterangan Barang</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{-- Tambah class select2 supaya rapi --}}
                                    <select name="barang_id[]" class="form-control select2" required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($bahanBakus as $barang)
                                            {{-- Diubah ka nama_bahan sasuai model MGudangBarang --}}
                                            <option value="{{ $barang->id }}">{{ $barang->nama_bahan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-outline">
                                        <input type="number" name="sisa_stok_cabang[]" class="form-control text-center"
                                            min="0" placeholder="0" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-outline">
                                        <input type="number" name="jumlah_diminta[]" class="form-control text-center"
                                            min="1" placeholder="0" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="keterangan_barang[]" class="form-control"
                                            placeholder="contoh: Urgent....">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm mb-0 btn-hapus">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn bg-gradient-success btn-sm" id="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>

                <hr class="horizontal dark my-4">

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-primary">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btn-tambah').click(function() {
                // 1. Ambil baris terakhir gasan di-clone
                let row = $('#tabel-barang tbody tr:last').clone();

                // 2. Buang container Select2 hasil renderan di baris clone
                row.find('.select2-container').remove();

                // 3. Bersihkan atribut bawaan Select2 di tag <select>
                row.find('select')
                    .removeClass('select2-hidden-accessible')
                    .removeAttr('data-select2-id tabindex aria-hidden');

                // 4. PENTING: Bersihkan atribut id di tag <option> supaya kada bentrok
                row.find('select option').removeAttr('data-select2-id');

                // 5. Reset inputan jadi kosong
                row.find('select').val(null);
                row.find('input').val('');

                // 6. Masukkan baris hanyar ka tabel
                $('#tabel-barang tbody').append(row);

                // 7. Inisialisasi Select2 HANYA di baris nang hanyar ditambah
                row.find('.select2').select2({
                    width: '100%',
                    placeholder: 'Pilih data',
                    allowClear: true
                });
            });

            // Script gasan mahapus baris
            $(document).on('click', '.btn-hapus', function() {
                if ($('#tabel-barang tbody tr').length > 1) {
                    // Langsung hapus barisnya, otomatis Select2 di baris itu umpat tabuang
                    $(this).closest('tr').remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Minimal harus ada 1 barang nang di-request!'
                    });
                }
            });
        });

        // Script gasan konfirmasi sabalum submit form
        $('#form-permintaan').on('submit', function(e) {
            e.preventDefault(); // Tahan form supaya kada langsung te-kirim
            let form = this;

            Swal.fire({
                title: 'Apakah Sudah Benar?',
                text: "Pastikan sisa stok dan jumlah yang diminta sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#42a5f5', // Warna biru sasuai tema nyawa
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Amun diklik Ya, hanyar form-nya ditarusakan (submit)
                    form.submit();
                }
            });
        });
    </script>
@endpush
