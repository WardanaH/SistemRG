@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">KELOLA EVENT SPESIAL</h1>

    <button type="button" class="brutal-btn" style="background: var(--neon-m); color: #fff;" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa-solid fa-plus me-1"></i> Tambah Desain Event
    </button>
</div>

@if(session('success'))
<div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background-color: var(--neon-g);">
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
                    <th>TEMA EVENT</th>
                    <th>NAMA PRODUK</th>
                    <th>BADGE KATEGORI</th>
                    <th width="15%">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center bg-light">
                        @if($item->gambar)
                        <img src="{{ asset(str_replace('\\', '/', $item->gambar)) }}" alt="{{ $item->nama_produk }}" class="img-fluid border border-2 border-dark" style="max-height: 80px;">
                        @else
                        <span class="badge bg-secondary">Kada bagambar</span>
                        @endif
                    </td>
                    <td class="text-uppercase text-center">
                        <span class="px-2 py-1 border border-2 border-dark" style="background: var(--neon-c);">{{ $item->tema }}</span>
                    </td>
                    <td>{{ $item->nama_produk }}</td>
                    <td>{{ $item->badge_kategori }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-dark mb-0 border border-2 border-dark" style="box-shadow: 2px 2px 0px #000;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <form action="{{ route('profil2.event.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bujuran handak mahapus desain event ini?');">
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
                                <h5 class="modal-title fw-bold">EDIT DESAIN EVENT</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('profil2.event.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-body p-4 bg-light">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Tema Event (URL Param)</label>
                                            <select name="tema" class="form-control brutal-input" required>
                                                <option value="">-- Pilih Tema --</option>
                                                <option value="ramadhan">Tema Ramadhan</option>
                                                <option value="imlek">Tema Imlek</option>
                                                <option value="natal">Tema Natal</option>
                                                <option value="kemerdekaan">Tema 17 Agustus (Kemerdekaan)</option>
                                            </select>
                                            <small class="fw-bold text-secondary">*Pilih sasuai menu dropdown di web.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Badge Kategori</label>
                                            <input type="text" name="badge_kategori" class="form-control brutal-input" placeholder="Cth: SPANDUK UCAPAN" value="{{ $item->badge_kategori }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Nama Produk Event</label>
                                        <input type="text" name="nama_produk" class="form-control brutal-input" placeholder="Cth: MUG CUSTOM KEMERDEKAAN" value="{{ $item->nama_produk }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Deskripsi Singkat</label>
                                        <textarea name="deskripsi" class="form-control brutal-input" rows="2">{{ $item->deskripsi }}</textarea>
                                    </div>
                                    <div class="mb-3 p-3 border border-dark border-2 bg-white">
                                        <label class="fw-bold mb-2">Ganti Gambar Desain</label>
                                        <input type="file" name="gambar" class="form-control brutal-input" accept="image/*">
                                        <small class="fw-bold text-secondary mt-1 d-block">*Kusungakan amun kada handak mangganti gambar.</small>
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
                    <td colspan="6" class="text-center py-4 fs-5 text-secondary">Balum ada data event nang di-input.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content brutal-card" style="border-width: 4px;">
            <div class="modal-header border-bottom border-dark border-4" style="background: var(--neon-c);">
                <h5 class="modal-title fw-bold">TAMBAH DESAIN EVENT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profil2.event.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Tema Event (URL Param)</label>
                            <select name="tema" class="form-control brutal-input" required>
                                <option value="">-- Pilih Tema --</option>
                                <option value="ramadhan">Tema Ramadhan</option>
                                <option value="imlek">Tema Imlek</option>
                                <option value="natal">Tema Natal</option>
                                <option value="kemerdekaan">Tema 17 Agustus (Kemerdekaan)</option>
                            </select>
                            <small class="fw-bold text-secondary">*Pilih sasuai menu dropdown di web.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Badge Kategori</label>
                            <input type="text" name="badge_kategori" class="form-control brutal-input" placeholder="Cth: SPANDUK UCAPAN, SOUVENIR MUG" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nama Produk Event</label>
                        <input type="text" name="nama_produk" class="form-control brutal-input" placeholder="Cth: MUG CUSTOM KEMERDEKAAN" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control brutal-input" rows="2" placeholder="Penjelasan handap..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Gambar Desain / Mockup</label>
                        <input type="file" name="gambar" class="form-control brutal-input" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-top border-dark border-4 p-3" style="background: #fff;">
                    <button type="button" class="brutal-btn" style="background: #ccc; color: #000;" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="brutal-btn" style="background: var(--neon-c); color: #000;">SIMPAN EVENT</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
