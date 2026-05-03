@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Edit Bahan Baku: {{ $bahan->nama_bahan }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('manajemen.bahanbaku.update', $bahan->id) }}">
                    @csrf
                    @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Kode Bahan</label>
                                <input type="text" name="kode" class="form-control" value="{{ old('kode', $bahan->kode_bahan) }}" required>
                            </div>
                            @error('kode')
                                <small class="text-danger text-xs">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Bahan</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $bahan->nama_bahan) }}" required>
                            </div>
                            @error('nama')
                                <small class="text-danger text-xs">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <a href="{{ route('manajemen.bahanbaku') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn bg-gradient-warning">
                            <i class="material-icons text-sm">save</i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
