@extends('profil2.layout.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">KELOLA MESIN CETAK</h1>
    <button class="brutal-btn" style="background: var(--neon-y); color: #000;" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fa-solid fa-plus"></i> Tambah Mesin</button>
</div>

@if(session('success'))
    <div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background: var(--neon-g);"><i class="fa-solid fa-check"></i> {{ session('success') }}</div>
@endif

<div class="brutal-card p-4 bg-white">
    <table class="table table-bordered border-dark fw-bold align-middle" style="border-width: 3px;">
        <thead class="text-center" style="background: var(--neon-c);">
            <tr style="border-width: 3px;">
                <th>GAMBAR</th><th>NAMA MESIN</th><th>WARNA TEMA</th><th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mesins as $item)
            <tr>
                <td class="text-center">
                    @if($item->gambar) <img src="{{ asset(str_replace('\\', '/', $item->gambar)) }}" style="height: 60px;"> @endif
                </td>
                <td>{{ $item->nama_mesin }}</td>
                <td class="text-center text-uppercase">{{ $item->warna_tema }}</td>
                <td class="text-center">
                    <button class="btn btn-dark btn-sm border border-2 border-dark" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class="fa-solid fa-pen"></i></button>
                    <form action="{{ route('profil2.mesin.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm border border-2 border-dark"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>

            <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog"><div class="modal-content brutal-card border-4">
                    <form action="{{ route('profil2.mesin.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body bg-light p-4">
                            <label class="fw-bold">Nama Mesin</label>
                            <input type="text" name="nama_mesin" class="form-control brutal-input mb-3" value="{{ $item->nama_mesin }}" required>

                            <label class="fw-bold">Warna Tema Kotak</label>
                            <select name="warna_tema" class="form-control brutal-input mb-3" required>
                                <option value="cyan" {{ $item->warna_tema == 'cyan' ? 'selected' : '' }}>Cyan (Biru Muda)</option>
                                <option value="magenta" {{ $item->warna_tema == 'magenta' ? 'selected' : '' }}>Magenta (Pink)</option>
                                <option value="yellow" {{ $item->warna_tema == 'yellow' ? 'selected' : '' }}>Yellow (Kuning)</option>
                                <option value="green" {{ $item->warna_tema == 'green' ? 'selected' : '' }}>Green (Hijau)</option>
                            </select>

                            <label class="fw-bold">Gambar Mesin</label>
                            <input type="file" name="gambar" class="form-control brutal-input" accept="image/*">
                        </div>
                        <div class="modal-footer bg-white border-top border-4 border-dark"><button type="submit" class="brutal-btn w-100" style="background: var(--neon-m); color:#fff;">SIMPAN</button></div>
                    </form>
                </div></div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content brutal-card border-4">
        <form action="{{ route('profil2.mesin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body bg-light p-4">
                <label class="fw-bold">Nama Mesin</label>
                <input type="text" name="nama_mesin" class="form-control brutal-input mb-3" required>

                <label class="fw-bold">Warna Tema Kotak</label>
                <select name="warna_tema" class="form-control brutal-input mb-3" required>
                    <option value="cyan">Cyan (Biru Muda)</option>
                    <option value="magenta">Magenta (Pink)</option>
                    <option value="yellow">Yellow (Kuning)</option>
                    <option value="green">Green (Hijau)</option>
                </select>

                <label class="fw-bold">Gambar Mesin</label>
                <input type="file" name="gambar" class="form-control brutal-input" accept="image/*">
            </div>
            <div class="modal-footer bg-white border-top border-4 border-dark"><button type="submit" class="brutal-btn w-100" style="background: var(--neon-c); color:#000;">SIMPAN</button></div>
        </form>
    </div></div>
</div>
@endsection
