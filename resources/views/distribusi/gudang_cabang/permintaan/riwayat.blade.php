@extends('distribusi.layouts.app')

@section('title', 'Riwayat Permintaan Barang')

@section('content')
    <div class="card">
        <div class="card-header">

            @php
                $user = Auth::user();
                $gudang = $user->cabang;
            @endphp

            <h3 class="mb-0">Data Riwayat Permintaan Barang {{ $gudang->nama ?? 'Cabang Dihapus' }}</h3>
        </div>
        <div class="card-body">
            {{-- FORM FILTER & PENCARIAN TERPADU --}}
            <form action="{{ route('gudang-cabang.permintaan.riwayat') }}" method="GET" class="mb-4">

                <div class="row align-items-end mb-3">
                    <!-- Pencarian Kode Permintaan -->
                    <div class="col-md-12">
                        <label class="form-label text-sm fw-bold">Cari Kode Permintaan</label>
                        <input type="text" name="search" class="form-control border px-3 py-2"
                            placeholder="Masukkan kode permintaan..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="row align-items-end">
                    <!-- Filter Status -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-sm fw-bold">Status Permintaan</label>
                        <select name="status" class="form-select border px-3 py-2 select2">
                            <option value="">Semua Status</option>
                            <option value="menunggu_acc" {{ request('status') == 'menunggu_acc' ? 'selected' : '' }}>
                                Menunggu ACC</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="tidak_lengkap" {{ request('status') == 'tidak_lengkap' ? 'selected' : '' }}>Tidak
                                Lengkap</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-sm fw-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control border px-3 py-2"
                            value="{{ request('start_date') }}">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-sm fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control border px-3 py-2"
                            value="{{ request('end_date') }}">
                    </div>

                    <!-- Tombol Filter -->
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0 px-4 py-2">
                            <i class="fas fa-search me-1"></i> Terapkan
                        </button>
                    </div>

                </div>
            </form>

            {{-- pembatas --}}
            <hr>

            {{-- area table --}}
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kode
                                Permintaan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal
                                Request</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Total
                                Item</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $item)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 ps-2">{{ $riwayat->firstItem() + $index }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $item->kode_permintaan }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">
                                        {{ \Carbon\Carbon::parse($item->tanggal_request)->format('d M Y, H:i') }}</p>
                                </td>
                                <td class="text-center">
                                    <p class="text-xs font-weight-bold mb-0">{{ $item->detailPermintaan->count() }} Macam
                                        Barang</p>
                                </td>
                                <td class="text-center">
                                    @if ($item->status_permintaan == 'menunggu_acc')
                                        <span class="badge badge-sm bg-gradient-warning">Menunggu ACC</span>
                                    @elseif($item->status_permintaan == 'disetujui')
                                        <span class="badge badge-sm bg-gradient-info">Disetujui</span>
                                    @elseif($item->status_permintaan == 'dikirim')
                                        <span class="badge badge-sm bg-gradient-primary">Dikirim</span>
                                    @elseif($item->status_permintaan == 'diterima')
                                        <span class="badge badge-sm bg-gradient-success">Diterima</span>
                                    @elseif($item->status_permintaan == 'tidak_lengkap')
                                        <span class="badge badge-sm bg-gradient-danger">Tidak Lengkap</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm mb-0" data-bs-toggle="modal"
                                        data-bs-target="#modalDetail{{ $item->id }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <p class="text-xs font-weight-bold mb-0 py-3">Belum ada riwayat permintaan dari cabang
                                        ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- paginasi --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $riwayat->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- =======================================
     MODAL DETAIL PERMINTAAN (Di Luar Tabel)
     ======================================= --}}
    @foreach ($riwayat as $item)
        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Permintaan: {{ $item->kode_permintaan }}</h5>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm"><strong>Keterangan:</strong> {{ $item->keterangan ?? 'Kada ada keterangan' }}
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                            Barang</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Stok Cabang</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Jml Diminta</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Jml Di Kirim</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center text-success">
                                            Jml Diterima</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Keterangan Barang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item->detailPermintaan as $detail)
                                        <tr>
                                            <td class="text-sm ps-3">
                                                {{ $detail->barangPusat->nama_bahan ?? 'Barang Dihapus' }}</td>
                                            <td class="text-sm text-center">{{ $detail->sisa_stok_cabang }}</td>
                                            <td class="text-sm text-center">{{ $detail->jumlah_diminta }}</td>

                                            {{-- JML DI-ACC --}}
                                            <td class="text-sm text-center">
                                                @if ($item->status_permintaan == 'menunggu_acc' || $item->status_permintaan == 'ditolak')
                                                    -
                                                @else
                                                    <strong>{{ $detail->jumlah_disetujui }}</strong>
                                                @endif
                                            </td>

                                            {{-- JML DITERIMA (Baru ditambah) --}}
                                            <td class="text-sm text-center text-success">
                                                <strong>{{ $detail->jumlah_diterima }}</strong>
                                            </td>

                                            {{-- bungkus teks nya biar rapi --}}
                                            <td class="text-sm text-center">
                                                <div class="text-wrap" style="width: 200px;">
                                                    {{ $detail->keterangan_barang ?? '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
