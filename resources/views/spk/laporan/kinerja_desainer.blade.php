@extends('spk.layout.app')

@section('content')

<div class="container-fluid py-4">

    {{-- 1. CARD FILTER --}}
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6><i class="material-icons text-sm me-1">date_range</i> Filter Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" id="filterForm">
                <div class="row align-items-end">

                    {{-- Pilihan Rentang Waktu --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pilih Rentang Waktu</label>
                        <div class="input-group input-group-outline is-filled">
                            <select name="rentang" id="filterType" class="form-control" onchange="toggleCustomDate()">
                                <option value="bulan_ini" {{ request('rentang') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="3_bulan" {{ request('rentang') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                                <option value="6_bulan" {{ request('rentang') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="tahun_ini" {{ request('rentang') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ request('rentang') == 'custom' ? 'selected' : '' }}>Atur Tanggal Sendiri</option>
                            </select>
                        </div>
                    </div>

                    {{-- INPUT TANGGAL CUSTOM --}}
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ request('rentang') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Dari Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ request('rentang') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Sampai Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                    </div>

                    {{-- Tombol Tampil --}}
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0">
                            <i class="material-icons text-sm me-1">search</i> Tampil
                        </button>
                    </div>

                </div>
            </form>

            <div class="mt-2 text-xs text-secondary">
                <i class="material-icons text-xs me-1">info</i>
                Menampilkan data dari: <b>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</b> s/d <b>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</b>
            </div>
        </div>
    </div>

    {{-- SETUP 3 KATEGORI TABEL --}}
    @php
        $kategoris = [
            'reguler' => ['judul' => 'SPK Reguler', 'bg' => 'bg-gradient-info', 'icon' => 'analytics', 'desc' => 'Kinerja input pesanan pada jam kerja normal'],
            'lembur'  => ['judul' => 'SPK Lembur', 'bg' => 'bg-gradient-warning', 'icon' => 'more_time', 'desc' => 'Kinerja input pesanan di luar jam kerja (Overtime)'],
            'bantuan' => ['judul' => 'SPK Bantuan', 'bg' => 'bg-gradient-success', 'icon' => 'handshake', 'desc' => 'Kinerja pengerjaan order lemparan dari cabang lain']
        ];
    @endphp

    {{-- 2. LOOPING CETAK 3 TABEL KINERJA --}}
    <div class="row">
        @foreach($kategoris as $key => $kat)
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header p-3 {{ $kat['bg'] }} d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h6 class="text-white mb-0">
                            <i class="material-icons text-sm me-2">{{ $kat['icon'] }}</i>
                            Detail Kinerja - {{ $kat['judul'] }}
                        </h6>
                        <span class="text-xs opacity-8">{{ $kat['desc'] }}</span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" width="5%">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Desainer</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Cabang</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-9 text-center bg-gray-100 border-end border-start">Total Nota (SPK)</th>
                                    <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-9 text-center">Total Item<br>(Sub SPK)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Indoor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Outdoor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Multi</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DTF</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Charge</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataDesainer as $index => $desainer)
                                <tr>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-sm font-weight-bold">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm font-weight-bold">{{ $desainer->nama }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold text-secondary">{{ $desainer->cabang->nama ?? 'Pusat' }}</span>
                                    </td>

                                    {{-- Kolom Total SPK Induk (Sesuai Kategori) --}}
                                    <td class="align-middle text-center bg-gray-100 border-end border-start">
                                        <span class="text-dark text-sm font-weight-bold">{{ $desainer->stats[$key]['spk'] }}</span>
                                    </td>

                                    {{-- Kolom Total Sub SPK (Sesuai Kategori) --}}
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm {{ $kat['bg'] }}">{{ $desainer->stats[$key]['total_sub'] }}</span>
                                    </td>

                                    {{-- Rincian per Jenis --}}
                                    <td class="align-middle text-center"><span class="text-secondary text-sm">{{ $desainer->stats[$key]['indoor'] }}</span></td>
                                    <td class="align-middle text-center"><span class="text-secondary text-sm">{{ $desainer->stats[$key]['outdoor'] }}</span></td>
                                    <td class="align-middle text-center"><span class="text-secondary text-sm">{{ $desainer->stats[$key]['multi'] }}</span></td>
                                    <td class="align-middle text-center"><span class="text-secondary text-sm">{{ $desainer->stats[$key]['dtf'] }}</span></td>
                                    <td class="align-middle text-center"><span class="text-secondary text-sm">{{ $desainer->stats[$key]['charge'] }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center text-secondary">
                                            <i class="material-icons mb-2" style="font-size: 32px;">person_off</i>
                                            <span class="text-sm">Tidak ada data desainer pada periode ini.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- SCRIPT TOGGLE FILTER --}}
<script>
    function toggleCustomDate() {
        var type = document.getElementById('filterType').value;
        var customInputs = document.querySelectorAll('.custom-date');

        if (type === 'custom') {
            customInputs.forEach(el => el.style.display = 'block');
        } else {
            customInputs.forEach(el => el.style.display = 'none');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleCustomDate();
    });
</script>

@endsection
