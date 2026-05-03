@extends('profil.admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
    // ===== Dummy angka (nanti tinggal ganti dari DB/Analytics) =====
    $kpi = [
        'day'   => ['label'=>'Kunjungan Hari Ini',  'value'=>128,   'sub'=>'+8.2% dari kemarin',     'icon'=>'bi-calendar-day',     'accent'=>'purple'],
        'week'  => ['label'=>'Kunjungan Minggu Ini','value'=>912,   'sub'=>'+3.4% dari minggu lalu', 'icon'=>'bi-calendar-week',    'accent'=>'blue'],
        'month' => ['label'=>'Kunjungan Bulan Ini', 'value'=>3820,  'sub'=>'+5.1% dari bulan lalu',  'icon'=>'bi-calendar-month',   'accent'=>'pink'],
        'year'  => ['label'=>'Kunjungan Tahun Ini', 'value'=>46520, 'sub'=>'+12.0% dari tahun lalu', 'icon'=>'bi-calendar2-range',  'accent'=>'orange'],
    ];

    // dummy kunjungan harian (0-100 untuk tinggi bar)
    $vals = [52, 68, 40, 74, 58, 62, 90];
    $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
@endphp

<div class="rg-admin-container">
    {{-- Header mini --}}
    <div class="rg-dash-head">
        <div>
            <div class="rg-dash-title">Hiring Statistics (versi RG)</div>
            <div class="rg-dash-sub">Ringkasan kunjungan (dummy dulu). Nanti tinggal sambung ke data asli.</div>
        </div>

        <div class="rg-dash-actions">
            <span class="rg-pill">
                <i class="bi bi-lightning-charge"></i>
                Free Plan
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- KPI cards --}}
        @foreach($kpi as $key => $it)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="rg-card rg-kpi-card anim-in">
                    <div class="rg-kpi">
                        <div class="rg-kpi-ico rg-accent-{{ $it['accent'] }}">
                            <i class="bi {{ $it['icon'] }}"></i>
                        </div>

                        <div class="rg-kpi-meta">
                            <div class="rg-kpi-label">{{ $it['label'] }}</div>
                            <div class="rg-kpi-value"
                                 data-count="{{ (int)$it['value'] }}"
                                 data-format="number">0</div>
                            <div class="rg-kpi-sub">{{ $it['sub'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Grafik --}}
        <div class="col-12 col-xl-8">
            <div class="rg-card rg-card-pad anim-in">
                <div class="rg-card-head">
                    <div>
                        <div class="rg-card-title">Kunjungan 7 Hari Terakhir</div>
                        <div class="rg-card-sub">Bar chart sederhana (tanpa library). Dummy data.</div>
                    </div>

                    <div class="rg-chip-soft">
                        <i class="bi bi-bar-chart"></i>
                        Weekly
                    </div>
                </div>

                <div class="rg-simple-chart" aria-label="Simple bar chart">
                    @foreach($days as $i => $d)
                        <div class="rg-day">
                            <div class="rg-bars">
                                <div class="rg-bar" style="height: {{ $vals[$i] }}%;"></div>
                            </div>
                            <div class="rg-day-label">{{ $d }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="rg-note">
                    *Nanti kalau mau, ini bisa diganti Chart.js / ApexCharts + data DB.
                </div>
            </div>
        </div>

        {{-- Panel kanan: ringkasan kecil (tanpa aktivitas) --}}
        <div class="col-12 col-xl-4">
            <div class="rg-card rg-card-pad anim-in">
                <div class="rg-card-head">
                    <div>
                        <div class="rg-card-title">Ringkasan</div>
                        <div class="rg-card-sub">Panel kecil untuk info cepat.</div>
                    </div>
                    <div class="rg-chip-soft">
                        <i class="bi bi-info-circle"></i>
                        Info
                    </div>
                </div>

                <div class="rg-summary">
                    <div class="rg-summary-item">
                        <div class="rg-summary-left">
                            <div class="rg-dot rg-accent-purple"></div>
                            <div>
                                <div class="rg-summary-title">Puncak kunjungan</div>
                                <div class="rg-summary-sub">Hari ini (dummy)</div>
                            </div>
                        </div>
                        <div class="rg-summary-right">90</div>
                    </div>

                    <div class="rg-summary-item">
                        <div class="rg-summary-left">
                            <div class="rg-dot rg-accent-blue"></div>
                            <div>
                                <div class="rg-summary-title">Rata-rata 7 hari</div>
                                <div class="rg-summary-sub">Estimasi (dummy)</div>
                            </div>
                        </div>
                        <div class="rg-summary-right">63</div>
                    </div>

                    <div class="rg-summary-item">
                        <div class="rg-summary-left">
                            <div class="rg-dot rg-accent-pink"></div>
                            <div>
                                <div class="rg-summary-title">Trend</div>
                                <div class="rg-summary-sub">Meningkat (dummy)</div>
                            </div>
                        </div>
                        <div class="rg-summary-right">
                            <span class="rg-trend-up"><i class="bi bi-arrow-up-right"></i> +5%</span>
                        </div>
                    </div>
                </div>

                <div class="rg-note mt-3">
                    Aktivitas terakhir admin sudah dihapus sesuai request.
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
