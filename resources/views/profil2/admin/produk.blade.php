@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">KELOLA PRODUK</h1>

    <button type="button" class="brutal-btn" style="background: var(--neon-g); color: #000;" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa-solid fa-plus me-1"></i> Tambah Produk
    </button>
</div>

@if(session('success'))
    <div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background-color: var(--neon-c);">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="brutal-card p-4" style="background: #fff;">
    <div class="table-responsive">
        <table class="table table-bordered border-dark align-middle fw-bold" style="border-width: 3px;">
            <thead class="text-center" style="background: var(--neon-y);">
                <tr style="border-width: 3px;">
                    <th width="5%">NO</th>
                    <th width="15%">GAMBAR</th>
                    <th>NAMA PRODUK</th>
                    <th>KATEGORI</th>
                    <th>DIVISI</th>
                    <th width="15%">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        @if($item->gambar_produk)
                            <img src="{{ asset($item->gambar_produk) }}" alt="{{ $item->nama_produk }}" class="img-fluid border border-2 border-dark" style="max-height: 60px;">
                        @else
                            <span class="badge bg-secondary">Kada bagambar</span>
                        @endif
                    </td>
                    <td>
                        {{ $item->nama_produk }}
                        @if($item->is_tampil_beranda)
                            <span class="badge ms-2 border border-dark text-dark" style="background: var(--neon-c);">Tampil di Beranda</span>
                        @endif
                    </td>
                    <td>{{ $item->kategori_produk }}</td>
                    <td class="text-uppercase text-center">
                        <span class="px-2 py-1 border border-2 border-dark" style="background: {{ $item->kategori_layanan == 'dtf' ? 'var(--neon-m)' : ($item->kategori_layanan == 'indoor' ? 'var(--neon-c)' : 'var(--neon-g)') }}; color: {{ $item->kategori_layanan == 'dtf' ? '#fff' : '#000' }};">
                            {{ $item->kategori_layanan }}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-dark mb-0 border border-2 border-dark" style="box-shadow: 2px 2px 0px #000;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <form action="{{ route('profil2.produk.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bujuran handak mahapus produk ini?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm mb-0 border border-2 border-dark" style="background: #ff4444; color: white; box-shadow: 2px 2px 0px #000;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content brutal-card" style="border-width: 4px;">
                            <div class="modal-header border-bottom border-dark border-4" style="background: var(--neon-y);">
                                <h5 class="modal-title fw-bold">EDIT PRODUK</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('profil2.produk.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-body p-4 bg-light">
                                    <div class="mb-3">
                                        <label class="fw-bold">Nama Produk</label>
                                        <input type="text" name="nama_produk" class="form-control brutal-input" value="{{ $item->nama_produk }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Kategori Produk</label>
                                            <input type="text" name="kategori_produk" class="form-control brutal-input" placeholder="Cth: Spanduk, Kaos..." value="{{ $item->kategori_produk }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Divisi Layanan</label>
                                            <select name="kategori_layanan" class="form-control brutal-input" required>
                                                <option value="indoor" {{ $item->kategori_layanan == 'indoor' ? 'selected' : '' }}>Indoor</option>
                                                <option value="outdoor" {{ $item->kategori_layanan == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                                                <option value="multi" {{ $item->kategori_layanan == 'multi' ? 'selected' : '' }}>Multi</option>
                                                <option value="dtf" {{ $item->kategori_layanan == 'dtf' ? 'selected' : '' }}>Sablon DTF</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Deskripsi Singkat (Gasan Kartu Produk)</label>
                                        <textarea name="deskripsi_singkat" class="form-control brutal-input" rows="2">{{ $item->deskripsi_singkat }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Deskripsi Lengkap (Gasan Detail Produk)</label>
                                        <textarea name="deskripsi_lengkap" class="form-control brutal-input" rows="4">{{ $item->deskripsi_lengkap }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Ganti Gambar Produk</label>
                                        <input type="file" name="gambar_produk" class="form-control brutal-input" accept="image/*">
                                        <small class="fw-bold text-secondary">*Kusungakan amun kada handak mangganti gambar.</small>
                                    </div>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input border border-dark" type="checkbox" name="is_tampil_beranda" value="1" id="checkTampilEdit{{ $item->id }}" {{ $item->is_tampil_beranda ? 'checked' : '' }} style="border-width: 2px;">
                                        <label class="form-check-label fw-bold" for="checkTampilEdit{{ $item->id }}">
                                            Tampilakan di halaman depan (Beranda)
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer border-top border-dark border-4 p-3" style="background: #fff;">
                                    <button type="button" class="brutal-btn" style="background: #ccc; color: #000;" data-bs-dismiss="modal">BATAL</button>
                                    <button type="submit" class="brutal-btn" style="background: var(--neon-m); color: #fff;">SIMPAN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 fs-5 text-secondary">Balum ada produk nang di-input.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content brutal-card" style="border-width: 4px;">
            <div class="modal-header border-bottom border-dark border-4" style="background: var(--neon-g);">
                <h5 class="modal-title fw-bold">TAMBAH PRODUK HANYAR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profil2.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="fw-bold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control brutal-input" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kategori Produk</label>
                            <input type="text" name="kategori_produk" class="form-control brutal-input" placeholder="Cth: Spanduk, Kartu Nama, Kaos...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Divisi Layanan</label>
                            <select name="kategori_layanan" class="form-control brutal-input" required>
                                <option value="indoor">Indoor</option>
                                <option value="outdoor">Outdoor</option>
                                <option value="multi">Multi</option>
                                <option value="dtf">Sablon DTF</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi Singkat (Gasan Kartu Produk)</label>
                        <textarea name="deskripsi_singkat" class="form-control brutal-input" rows="2" placeholder="Muncul di bawah ngaran produk..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi Lengkap (Gasan Detail Produk)</label>
                        <textarea name="deskripsi_lengkap" class="form-control brutal-input" rows="4" placeholder="Penjelasan lengkap spesifikasi produk..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Gambar Produk</label>
                        <input type="file" name="gambar_produk" class="form-control brutal-input" accept="image/*">
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input border border-dark" type="checkbox" name="is_tampil_beranda" value="1" id="checkTampil" style="border-width: 2px;">
                        <label class="form-check-label fw-bold" for="checkTampil">
                            Tampilakan produk ini di halaman utama (Beranda)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top border-dark border-4 p-3" style="background: #fff;">
                    <button type="button" class="brutal-btn" style="background: #ccc; color: #000;" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="brutal-btn" style="background: var(--neon-c); color: #000;">SIMPAN PRODUK</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
