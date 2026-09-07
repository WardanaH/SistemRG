@extends('profil2.layout.app')

@section('content')

<div class="row justify-content-center mb-5">
    <div class="col-lg-10">

        <div class="brutal-card p-4 p-md-5 mb-4 text-center" style="background-color: var(--neon-y);">
            <h1 class="display-4 fw-bold mb-0" style="text-shadow: 3px 3px 0px #fff;">{{ $judul }}</h1>
        </div>

        <div class="brutal-card p-4 p-md-5 bg-white">
            <div class="d-flex align-items-center mb-4 border-bottom border-dark border-4 pb-3">
                @if($judul == 'SYARAT & KETENTUAN')
                    <i class="fa-solid fa-file-contract fa-3x me-3"></i>
                @else
                    <i class="fa-solid fa-shield-halved fa-3x me-3"></i>
                @endif
                <h3 class="mb-0 fw-bold">Informasi Palanggan</h3>
            </div>

            <div class="fs-5 fw-bold text-dark lh-lg">
                @if($legal)
                    {!! nl2br(e($legal->isi_konten)) !!}
                @else
                    <span class="text-secondary">Data balum disadiakan ulih admin.</span>
                @endif
            </div>

            <div class="mt-5 pt-4 border-top border-dark border-3">
                <a href="{{ route('profil2.beranda') }}" class="brutal-btn"><i class="fa-solid fa-arrow-left me-2"></i> KEMBALI KE BERANDA</a>
            </div>
        </div>

    </div>
</div>

@endsection
