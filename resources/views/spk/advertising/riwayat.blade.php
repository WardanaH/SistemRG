@extends('spk.layout.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header bg-gradient-info shadow-info pt-4 pb-3">
                <h6 class="text-white ps-3">Riwayat Produksi (Advertising)</h6>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3 text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">File</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Operator</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td class="ps-3 text-xs">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="text-xs font-weight-bold">{{ $item->nama_file }}<br><span class="text-muted fw-normal">{{ $item->spk->no_spk }}</span></td>
                                <td class="text-xs">{{ $item->operator->nama ?? '-' }}</td>
                                <td class="align-middle text-center">
                                    @php
                                        $badges = ['pending'=>'secondary', 'ripping'=>'warning', 'ongoing'=>'info', 'finishing'=>'primary', 'completed'=>'success'];
                                    @endphp
                                    <span class="badge bg-gradient-{{ $badges[$item->status_produksi] ?? 'dark' }}">
                                        {{ strtoupper($item->status_produksi) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-sm">Belum ada riwayat produksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">{{ $items->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
