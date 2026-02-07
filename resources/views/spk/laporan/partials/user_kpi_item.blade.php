<li class="list-group-item border-0 ps-0 mb-3 border-radius-lg bg-gray-100 p-2">
    <div class="d-flex justify-content-between align-items-center">

        {{-- Kiri: Nama & Tombol Edit --}}
        <div class="d-flex align-items-center ms-2">
            <div class="d-flex flex-column">
                <h6 class="mb-0 text-dark text-sm font-weight-bold">
                    {{ $user->nama }}

                    {{-- Tombol Edit Target (Hanya Admin) --}}
                    @hasrole('manajemen|admin')
                    <a href="javascript:;" class="text-secondary ms-2 cursor-pointer"
                        onclick="openTargetModal('{{ $user->id }}', '{{ $user->nama }}', '{{ $type }}')"
                        data-bs-toggle="tooltip" title="Atur Target">
                        <i class="material-icons text-xs">edit</i>
                    </a>
                    @endhasrole
                </h6>
                <span class="text-xs text-secondary">
                    {{ $user->cabang->nama ?? 'Pusat' }}
                </span>
            </div>
        </div>

        {{-- Kanan: Angka Capaian --}}
        <div class="text-end me-2">
            <span class="text-xs font-weight-bold">Capaian:</span>
            <span class="text-dark text-sm font-weight-bolder">{{ $user->capaian }}</span>
            <span class="text-xs text-secondary"> / {{ $user->target > 0 ? $user->target : '-' }}</span>
        </div>
    </div>

    {{-- Progress Bar Section --}}
    <div class="mt-2 ms-2 me-2">
        <div class="d-flex justify-content-between mb-1">
            <span class="text-xxs font-weight-bold">Progress</span>
            @if($user->target > 0)
            @if($user->capaian >= $user->target)
            <span class="badge badge-sm bg-gradient-success" style="font-size: 8px; padding: 4px 6px;">Tercapai</span>
            @else
            <span class="text-xxs font-weight-bold">{{ $user->persentase }}%</span>
            @endif
            @else
            <span class="badge badge-sm bg-secondary" style="font-size: 8px; padding: 4px 6px;">Target Belum Diatur</span>
            @endif
        </div>

        @if($user->target > 0)
        <div class="progress" style="height: 6px;">
            <div class="progress-bar {{ $user->capaian >= $user->target ? 'bg-gradient-success' : 'bg-gradient-info' }}"
                role="progressbar"
                style="width: {{ $user->persentase > 100 ? 100 : $user->persentase }}%"
                aria-valuenow="{{ $user->persentase }}"
                aria-valuemin="0"
                aria-valuemax="100">
            </div>
        </div>
        @endif
    </div>
</li>