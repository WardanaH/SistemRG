@extends('spk.layout.app')

@section('content')

{{-- ALERT SUCCESS --}}
@if(session('success'))
<div class="alert alert-success text-white alert-dismissible fade show" role="alert">
    <span class="alert-icon align-middle"><i class="material-icons text-md">thumb_up</i></span>
    <span class="alert-text"><strong>Berhasil!</strong> {{ session('success') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="container-fluid py-4">

    {{-- 1. CARD FILTER --}}
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6><i class="material-icons text-sm me-1">date_range</i> Filter Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pilih Rentang Waktu</label>
                        <div class="input-group input-group-outline is-filled">
                            <select name="filter_type" id="filterType" class="form-control" onchange="toggleCustomDate()">
                                <option value="bulan_ini" {{ $filterType == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="tri_wulan" {{ $filterType == 'tri_wulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                                <option value="semester" {{ $filterType == 'semester' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="tahun_ini" {{ $filterType == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Atur Tanggal Sendiri</option>
                            </select>
                        </div>
                    </div>

                    {{-- INPUT TANGGAL CUSTOM (Hidden by default) --}}
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ $filterType == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Dari Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ $filterType == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Sampai Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3 d-flex gap-2">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0">
                            <i class="material-icons text-sm me-1">search</i> Tampil
                        </button>

                        {{-- Tombol Atur Target Massal (Hanya Admin/Manajemen) --}}
                        @hasrole('manajemen|admin')
                        <button type="button" class="btn bg-gradient-dark w-100 mb-0" data-bs-toggle="modal" data-bs-target="#modalTargetMassal">
                            <i class="material-icons text-sm me-1">ads_click</i> Atur Target
                        </button>
                        @endhasrole
                    </div>
                </div>
            </form>
            <div class="mt-2 text-xs text-secondary">
                <i class="material-icons text-xs me-1">info</i>
                Menampilkan data dari: <b>{{ $startDate->format('d M Y') }}</b> s/d <b>{{ $endDate->format('d M Y') }}</b>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- A. TABEL DESIGNER --}}
        @if($designers->isNotEmpty())
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header p-3 bg-gradient-info d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h6 class="text-white mb-0"><i class="material-icons text-sm me-2">palette</i>Designer</h6>
                        <span class="text-xs opacity-8">KPI: Input SPK</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @foreach($designers as $user)
                        @include('spk.laporan.partials.user_kpi_item', ['user' => $user, 'type' => 'input'])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- B. TABEL ADMIN --}}
        @if($admins->isNotEmpty())
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header p-3 bg-gradient-success d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h6 class="text-white mb-0"><i class="material-icons text-sm me-2">verified_user</i>Admin</h6>
                        <span class="text-xs opacity-8">KPI: ACC SPK</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @foreach($admins as $user)
                        @include('spk.laporan.partials.user_kpi_item', ['user' => $user, 'type' => 'acc'])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- C. TABEL OPERATOR --}}
        @if($operators->isNotEmpty())
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header p-3 bg-gradient-warning d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h6 class="text-white mb-0"><i class="material-icons text-sm me-2">engineering</i>Operator</h6>
                        <span class="text-xs opacity-8">KPI: Produksi Selesai</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @foreach($operators as $user)
                        @include('spk.laporan.partials.user_kpi_item', ['user' => $user, 'type' => 'produksi'])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- MODAL ATUR TARGET (Hanya Muncul untuk Admin/Manajemen) --}}
@hasrole('manajemen|admin')
<div class="modal fade" id="modalSetTarget" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal">Atur Target Bulanan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('laporan.storeTarget') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="target_user_id">
                    <input type="hidden" name="jenis" id="target_jenis">

                    <div class="alert alert-info text-white text-xs mb-3">
                        <i class="material-icons text-xs me-1">info</i>
                        Mengatur target untuk: <strong id="target_user_name"></strong>
                    </div>

                    <div class="input-group input-group-outline mb-4 is-filled">
                        <label class="form-label">Bulan Target</label>
                        {{-- Input type month memudahkan user memilih Bulan & Tahun --}}
                        <input type="month" name="bulan" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>

                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Jumlah Target (Item/SPK)</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-primary">Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endhasrole

@hasrole('manajemen|admin')
{{-- MODAL ATUR TARGET MASSAL (PER ROLE) --}}
<div class="modal fade" id="modalTargetMassal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal">Atur Target Massal (Semua Pegawai)</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('laporan.storeTargetByRole') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning text-white text-xs mb-3">
                        <i class="material-icons text-xs me-1">warning</i>
                        <b>Perhatian:</b> Tindakan ini akan menimpa (overwrite) target individu yang sudah diatur sebelumnya untuk bulan yang dipilih.
                    </div>

                    <div class="input-group input-group-outline mb-4 is-filled">
                        <label class="form-label">Pilih Role / Divisi</label>
                        <select name="role_target" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Divisi --</option>
                            <option value="designer">Designer (Target Input SPK)</option>
                            <option value="admin">Admin & Manajemen (Target ACC)</option>
                            <option value="operator">Semua Operator (Target Produksi)</option>
                        </select>
                    </div>

                    <div class="input-group input-group-outline mb-4 is-filled">
                        <label class="form-label">Bulan Target</label>
                        <input type="month" name="bulan" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>

                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Jumlah Target (Dipukul Rata)</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-dark">Simpan ke Semua User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endhasrole

<script>
    // Logic Toggle Filter Tanggal
    function toggleCustomDate() {
        var type = document.getElementById('filterType').value;
        var customInputs = document.querySelectorAll('.custom-date');

        if (type === 'custom') {
            customInputs.forEach(el => el.style.display = 'block');
        } else {
            customInputs.forEach(el => el.style.display = 'none');
        }
    }

    // Logic Modal Target
    function openTargetModal(userId, userName, jenis) {
        document.getElementById('target_user_id').value = userId;
        document.getElementById('target_user_name').innerText = userName;
        document.getElementById('target_jenis').value = jenis;

        var myModal = new bootstrap.Modal(document.getElementById('modalSetTarget'));
        myModal.show();
    }
</script>
@endsection
