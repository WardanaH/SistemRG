{{-- resources/views/profil/public/partials/footer.blade.php --}}
@php
    $footLayout = $footLayout ?? [];

    $brandParts = $footLayout['brand_parts'] ?? [
        ['text' => 'Restu',      'color' => 'var(--rg-blue)'],
        ['text' => ' Guru',      'color' => 'var(--rg-yellow)'],
        ['text' => ' Promosindo','color' => 'var(--rg-red)'],
    ];
    if (!is_array($brandParts)) $brandParts = [];

    $tagline = $footLayout['tagline'] ?? "Percetakan & Advertising\nOutdoor • Indoor • Multi";

    $services = $footLayout['services'] ?? [
        ['label' => 'Outdoor Printing', 'url' => route('profil.layanan').'#outdoor', 'hover' => 'blue'],
        ['label' => 'Indoor Printing',  'url' => route('profil.layanan').'#indoor',  'hover' => 'red'],
        ['label' => 'Multi (Stiker & Kecil)', 'url' => route('profil.layanan').'#multi', 'hover' => 'yellow'],
    ];
    if (!is_array($services)) $services = [];

    $branches = $footLayout['branches'] ?? [
        ['label' => 'Banjarmasin', 'url' => '#', 'hover' => 'blue'],
        ['label' => 'Martapura',   'url' => '#', 'hover' => 'yellow'],
        ['label' => 'Banjarbaru',  'url' => '#', 'hover' => 'red'],
        ['label' => 'Liang Anggang','url' => '#','hover' => 'blue'],
    ];
    if (!is_array($branches)) $branches = [];

    $wa = $footLayout['wa'] ?? 'https://wa.me/6281234567890';
    $email = $footLayout['email'] ?? 'info@promosindo.com';

    $socials = $footLayout['socials'] ?? [
        ['label'=>'Instagram','url'=>'#','icon'=>'instagram'],
    ];
    if (!is_array($socials)) $socials = [];

    $copyrightLeft = $footLayout['copyright_left'] ?? ('© '.date('Y').' Restu Guru Promosindo');
    $copyrightRight = $footLayout['copyright_right'] ?? 'Percetakan • Digital Printing • Advertising';

    $hoverClass = function($k){
        if ($k === 'red') return 'rg-hover-red';
        if ($k === 'yellow') return 'rg-hover-yellow';
        return 'rg-hover-blue';
    };
@endphp

<footer class="bg-light border-top">
    <div class="container py-5">
        <div class="row g-4">

            {{-- Brand --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="font-hero fs-5 lh-1 mb-3">
                    @foreach($brandParts as $p)
                        @php
                            $tx = $p['text'] ?? '';
                            $cl = $p['color'] ?? 'var(--rg-blue)';
                        @endphp
                        @if($tx !== '')
                            <span style="color: {{ $cl }}">{{ $tx }}</span>
                        @endif
                    @endforeach
                </div>

                <p class="small text-muted mb-0" style="line-height:1.75; white-space:pre-line;">
                    {{ $tagline }}
                </p>

                <div class="mt-3 d-flex gap-2">
                    @foreach($socials as $s)
                        @php
                            $label = $s['label'] ?? 'Social';
                            $url = $s['url'] ?? '#';
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                           class="d-inline-flex align-items-center justify-content-center rounded-circle p-2"
                           style="color: var(--rg-muted);" aria-label="{{ $label }}">
                            <i class="bi bi-link-45deg"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Services --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="fw-bold mb-3">Layanan</div>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    @foreach($services as $it)
                        @php
                            $label = $it['label'] ?? '';
                            $url = $it['url'] ?? '#';
                            $hover = $it['hover'] ?? 'blue';
                            if ($label === '') continue;
                        @endphp
                        <li><a class="rg-footer-link {{ $hoverClass($hover) }}" href="{{ $url }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Cabang --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="fw-bold mb-3">Cabang</div>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    @foreach($branches as $it)
                        @php
                            $label = $it['label'] ?? '';
                            $url = $it['url'] ?? '#';
                            $hover = $it['hover'] ?? 'blue';
                            if ($label === '') continue;
                        @endphp
                        <li><a class="rg-footer-link {{ $hoverClass($hover) }}" href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="fw-bold mb-3">Kontak</div>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a class="link" href="{{ $wa }}" target="_blank" rel="noopener">WhatsApp</a></li>
                    <li class="text-muted">Email: {{ $email }}</li>
                </ul>
            </div>

        </div>

        <div class="mt-4 pt-4 border-top small text-muted d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <div>{{ $copyrightLeft }}</div>
            <div>{{ $copyrightRight }}</div>
        </div>
    </div>
</footer>
