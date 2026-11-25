@extends('layouts.app')

@section('title', __('Papan Pemuka'))
@section('page-title', __('Selamat kembali, :name!', ['name' => auth()->user()->name]))
@section('page-subtitle', auth()->user()->jawatan . ' · ' . auth()->user()->unit)

@section('content')
    {{-- FinSet Sub-header Action Row --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="btn btn-sm btn-light pe-none">
                <i class="bi bi-calendar4 me-1 text-orchid"></i>
                {{ now()->locale(app()->getLocale())->translatedFormat('F Y') }}
            </span>
            <span class="badge bg-success-subtle text-success-emphasis py-2 px-3">
                <i class="bi bi-car-front me-1"></i>{{ __(':avail/:total Kenderaan Tersedia', ['avail' => $availableVehiclesCount, 'total' => $totalVehiclesCount]) }}
            </span>
            <span class="badge bg-primary-subtle text-primary-emphasis py-2 px-3">
                <i class="bi bi-person-badge me-1"></i>{{ __(':avail/:total Pilot HQ Tersedia', ['avail' => $availablePilotsCount, 'total' => $totalPilotsCount]) }}
            </span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-light">
                <i class="bi bi-calendar3 me-1"></i> {{ __('Kalendar') }}
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Permohonan Baharu') }}
            </a>
        </div>
    </div>

    {{-- Personal FinSet KPI Cards --}}
    <div class="row g-3 mb-3">
        @php
            $statCards = [
                ['label' => __('Menunggu'), 'value' => $stats['pending'], 'pill' => __('Dalam semakan'), 'pillCls' => 'bg-warning-subtle text-warning-emphasis', 'variant' => 'kpi-card--amber', 'icon' => 'bi-hourglass-split', 'status' => 'pending'],
                ['label' => __('Diluluskan'), 'value' => $stats['approved'], 'pill' => __('Sedia bertolak'), 'pillCls' => 'bg-primary-subtle text-primary-emphasis', 'variant' => 'kpi-card--orchid', 'icon' => 'bi-check2-circle', 'status' => 'approved'],
                ['label' => __('Selesai'), 'value' => $stats['completed'], 'pill' => __('Direkodkan'), 'pillCls' => 'bg-success-subtle text-success-emphasis', 'variant' => 'kpi-card--emerald', 'icon' => 'bi-flag-fill', 'status' => 'completed'],
                ['label' => __('Tidak Diluluskan'), 'value' => $stats['rejected'], 'pill' => __('Ditolak'), 'pillCls' => 'bg-danger-subtle text-danger-emphasis', 'variant' => 'kpi-card--rose', 'icon' => 'bi-x-circle', 'status' => 'rejected'],
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="col-6 col-md-3">
                <a href="{{ route('bookings.index', ['status' => $card['status']]) }}" class="text-decoration-none">
                    <div class="card kpi-card {{ $card['variant'] }} h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="kpi-mini-icon"><i class="bi {{ $card['icon'] }}"></i></span>
                                    <span class="kpi-label">{{ $card['label'] }}</span>
                                </div>
                                <span class="kpi-arrow-btn"><i class="bi bi-arrow-up-right"></i></span>
                            </div>
                            <div class="kpi-value mb-2">{{ $card['value'] }}</div>
                            <span class="kpi-trend-pill {{ $card['pillCls'] }}">{{ $card['pill'] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Recent Bookings (FinSet Soft Lavender Table Header) --}}
    <div class="card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title-sm">{{ __('Permohonan Terkini') }}</h2>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-light">
                    {{ __('Lihat semua') }} <i class="bi bi-chevron-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-finset mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Tempahan') }}</th>
                            <th>{{ __('Jenis & Destinasi') }}</th>
                            <th>{{ __('Tarikh Perjalanan') }}</th>
                            <th>{{ __('Kenderaan & Pilot') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Dokumen') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td class="text-nowrap">
                                    <a href="{{ route('bookings.show', $booking) }}" class="fw-semibold text-decoration-none">{{ $booking->booking_code }}</a>
                                    @if($booking->is_urgent)
                                        <span class="badge bg-danger-subtle text-danger-emphasis ms-1">{{ __('Segera') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $booking->requestTypeBadgeClass() }}">{{ $booking->requestTypeLabel() }}</span>
                                    <div class="mt-1">{{ $booking->destination }}</div>
                                </td>
                                <td class="text-nowrap">
                                    <div>{{ $booking->start_datetime->format('d/m/Y, h:i A') }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $booking->end_datetime->format('d/m/Y, h:i A') }}</div>
                                </td>
                                <td>
                                    @if($booking->vehicle)
                                        <div><i class="bi bi-car-front text-violet me-1"></i>{{ $booking->vehicle->plate_number }}</div>
                                    @endif
                                    @if($booking->pilot)
                                        <div class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-person-badge me-1"></i>{{ $booking->pilot->name }}</div>
                                    @elseif($booking->isSelfDrive() && $booking->isApproved())
                                        <div class="text-info-emphasis" style="font-size: 0.72rem;"><i class="bi bi-key me-1"></i>{{ __('Pandu sendiri') }}</div>
                                    @elseif(!$booking->vehicle && !$booking->pilot)
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="badge {{ $booking->statusBadgeClass() }}">{{ $booking->statusLabel() }}</span></td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('bookings.pdf.borang', $booking) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="{{ __('Borang Kebenaran Kenderaan') }}">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @if($booking->canDownloadMemo())
                                        <a href="{{ route('bookings.pdf.memo', $booking) }}" target="_blank" class="btn btn-sm btn-info" title="{{ __('Memo Kebenaran Memandu') }}">
                                            <i class="bi bi-file-earmark-text me-1"></i>{{ __('Memo') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-journal-plus"></i>
                                        {{ __('Anda belum membuat sebarang permohonan.') }}
                                        <div class="mt-3"><a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">{{ __('Buat permohonan') }}</a></div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
