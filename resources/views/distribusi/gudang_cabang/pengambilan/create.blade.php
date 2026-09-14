@extends('distribusi.layouts.app')

@section('title', 'Form Pengambilan Barang')

@section('content')



    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary">
            <h6 class="text-white mb-0">Input Pengambilan Barang</h6>
        </div>
        <div class="card-body">
            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success text-white">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-white">{{ session('error') }}</div>
            @endif
            <form action="{{ route('gudang-cabang.pengambilan.store') }}" method="POST" class="mb-4">
                @csrf

                <h6 class="mb-3 text-sm">Detail Barang yang Diambil</h6>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="table-barang">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Barang
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ukuran</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jumlah</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Atas Nama
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ambil Ke
                                    (Tempat)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-barang">
                            <!-- Baris Input Pertama (Default) -->
                            <tr>
                                <td>
                                    <input type="text" name="nama_barang[]" class="form-control border px-2 py-1"
                                        required placeholder="Cth: Spanduk Flexy">
                                </td>
                                <td>
                                    <input type="text" name="ukuran_barang[]" class="form-control border px-2 py-1"
                                        placeholder="Cth: 2x3 M">
                                </td>
                                <td>
                                    <input type="number" name="jumlah_barang[]" class="form-control border px-2 py-1"
                                        required placeholder="Cth: 2" min="1">
                                </td>
                                <td>
                                    <input type="text" name="atas_nama[]" class="form-control border px-2 py-1"
                                        placeholder="Cth: PT Makmur">
                                </td>
                                <td>
                                    <input type="text" name="ambil_ke[]" class="form-control border px-2 py-1" required
                                        placeholder="Cth: Gudang Belakang">
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-hapus" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-sm btn-success mb-0 px-3 py-2" id="btn-tambah-barang">
                        <i class="fas fa-plus me-1"></i> Tambah Barang
                    </button>
                    <button type="submit" class="btn bg-gradient-primary mb-0 px-5 py-2">
                        <i class="fas fa-save me-1"></i> Simpan Data
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btn-tambah-barang').click(function() {
                let newRow = `
                <tr>
                    <td>
                        <input type="text" name="nama_barang[]" class="form-control border px-2 py-1" required placeholder="Cth: Spanduk Flexy">
                    </td>
                    <td>
                        <input type="text" name="ukuran_barang[]" class="form-control border px-2 py-1" placeholder="Cth: 2x3 M">
                    </td>
                    <td>
                        <input type="number" name="jumlah_barang[]" class="form-control border px-2 py-1" required placeholder="Cth: 2" min="1">
                    </td>
                    <td>
                        <input type="text" name="atas_nama[]" class="form-control border px-2 py-1" placeholder="Cth: PT Makmur">
                    </td>
                    <td>
                        <input type="text" name="ambil_ke[]" class="form-control border px-2 py-1" required placeholder="Cth: Gudang Belakang">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-danger mb-0 btn-hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#tbody-barang').append(newRow);
            });

            $(document).on('click', '.btn-hapus', function() {
                if ($('#tbody-barang tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        });
    </script>
@endpush
