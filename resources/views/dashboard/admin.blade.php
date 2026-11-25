@extends('layouts.app')

@section('title', __('Papan Pemuka'))
@section('page-title', __('Selamat kembali, :name!', ['name' => auth()->user()->name]))
@section('page-subtitle', __('Ringkasan pengurusan tempahan kenderaan, Pilot HQ dan status kenderaan pejabat semasa.'))

@section('content')
    {{-- FinSet Sub-header Action Row --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="btn btn-sm btn-light pe-none">
                <i class="bi bi-calendar4 me-1 text-orchid"></i>
                {{ now()->locale(app()->getLocale())->translatedFormat('F Y') }}
            </span>
            @if($urgentPendingCount > 0)
                <a href="{{ route('bookings.index', ['status' => 'pending', 'urgent' => 1]) }}" class="badge bg-danger-subtle text-danger-emphasis text-decoration-none py-2 px-3">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ __(':count Permohonan Segera (< 3 Hari)', ['count' => $urgentPendingCount]) }}
                </a>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-light">
                <i class="bi bi-calendar3 me-1"></i> {{ __('Kalendar Ketersediaan') }}
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Mohon Tempahan') }}
            </a>
        </div>
    </div>

    {{-- Row 1: 4 Colorful FinSet-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card kpi-card {{ $urgentPendingCount > 0 ? 'kpi-card--rose' : 'kpi-card--amber' }} h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="kpi-mini-icon"><i class="bi bi-hourglass-split"></i></span>
                                <span class="kpi-label">{{ __('Permohonan Menunggu') }}</span>
                            </div>
                            <span class="kpi-arrow-btn"><i class="bi bi-arrow-up-right"></i></span>
                        </div>
                        <div class="kpi-value mb-2">{{ $pendingCount }}</div>
                        <div class="d-flex align-items-center gap-2">
                            @if($urgentPendingCount > 0)
                                <span class="kpi-trend-pill bg-danger-subtle text-danger-emphasis">
                                    <i class="bi bi-arrow-up-short"></i>{{ $urgentPendingCount }} {{ __('Segera') }}
                                </span>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ __('kurang 3 hari') }}</span>
                            @else
                                <span class="kpi-trend-pill bg-warning-subtle text-warning-emphasis">{{ __('Menunggu') }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ __('semakan admin') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('calendar.index') }}" class="text-decoration-none">
                <div class="card kpi-card kpi-card--orchid h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="kpi-mini-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                <span class="kpi-label">{{ __('Perjalanan Aktif') }}</span>
                            </div>
                            <span class="kpi-arrow-btn"><i class="bi bi-arrow-up-right"></i></span>
                        </div>
                        <div class="kpi-value mb-2">{{ $activeTripsCount }}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="kpi-trend-pill bg-primary-subtle text-primary-emphasis">
                                <i class="bi bi-arrow-up-short"></i>{{ __(':count Akan Datang', ['count' => $upcomingApprovedCount]) }}
                            </span>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ __('dijadualkan') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.users.index', ['role' => 'pilot']) }}" class="text-decoration-none">
                <div class="card kpi-card kpi-card--emerald h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="kpi-mini-icon"><i class="bi bi-person-badge-fill"></i></span>
                                <span class="kpi-label">{{ __('Pilot HQ Tersedia') }}</span>
                            </div>
                            <span class="kpi-arrow-btn"><i class="bi bi-arrow-up-right"></i></span>
                        </div>
                        <div class="kpi-value mb-2">
                            {{ $availablePilots->count() }}<span class="kpi-sub-denom"> / {{ $totalActivePilots }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="kpi-trend-pill bg-success-subtle text-success-emphasis">
                                <span class="status-dot bg-success me-1"></span>{{ __('Tersedia') }}
                            </span>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ __('status semasa HQ') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.vehicles.index', ['status' => 'maintenance']) }}" class="text-decoration-none">
                <div class="card kpi-card kpi-card--amber h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="kpi-mini-icon"><i class="bi bi-tools"></i></span>
                                <span class="kpi-label">{{ __('Penyelenggaraan') }}</span>
                            </div>
                            <span class="kpi-arrow-btn"><i class="bi bi-arrow-up-right"></i></span>
                        </div>
                        <div class="kpi-value mb-2">
                            {{ $maintenanceVehiclesCount }}<span class="kpi-sub-denom"> / {{ $totalVehiclesCount }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="kpi-trend-pill {{ $maintenanceVehiclesCount > 0 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                                {{ $maintenanceVehiclesCount > 0 ? __('Servis Bengkel') : __('Semua Baik') }}
                            </span>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ __('kenderaan YKN') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Row 2: FinSet-Style Analytics Row (Bar Chart + Donut Breakdown) --}}
    @php
        $maxMonthVal = max(1, (int) $monthlyActivity->max('total'));
        $tTotal = max(1, $totalBookingsCount);
        $p1 = round(($typeCounts['vehicle_and_pilot'] / $tTotal) * 100);
        $p2 = round(($typeCounts['vehicle_only'] / $tTotal) * 100);
        $p3 = max(0, 100 - $p1 - $p2);
        $deg1 = round(($typeCounts['vehicle_and_pilot'] / $tTotal) * 360);
        $deg2 = $deg1 + round(($typeCounts['vehicle_only'] / $tTotal) * 360);
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h2 class="card-title-sm">{{ __('Aliran Tempahan Bulanan') }}</h2>
                        <div class="d-flex align-items-center gap-3" style="font-size: 0.72rem;">
                            <span class="d-inline-flex align-items-center gap-1 text-muted">
                                <span class="legend-dot" style="background: #a855d6;"></span> {{ __('Diluluskan / Selesai') }}
                            </span>
                            <span class="d-inline-flex align-items-center gap-1 text-muted">
                                <span class="legend-dot" style="background: #dcd3f6;"></span> {{ __('Jumlah Permohonan') }}
                            </span>
                        </div>
                    </div>

                    <div class="finset-bar-chart">
                        @foreach($monthlyActivity as $m)
                            @php
                                $totalH = $m['total'] > 0 ? max(18, (int) round(($m['total'] / $maxMonthVal) * 100)) : 8;
                                $appH = $m['approved'] > 0 ? max(16, (int) round(($m['approved'] / $maxMonthVal) * 100)) : 8;
                            @endphp
                            <div class="finset-bar-col" title="{{ $m['label'] }}: {{ $m['approved'] }} / {{ $m['total'] }}">
                                <div class="finset-bar-pair">
                                    <div class="finset-bar finset-bar-primary" style="height: {{ $appH }}%;"></div>
                                    <div class="finset-bar finset-bar-secondary" style="height: {{ $totalH }}%;"></div>
                                </div>
                                <div class="finset-bar-label">{{ $m['label'] }} ({{ $m['total'] }})</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="card-title-sm">{{ __('Kategori Permohonan') }}</h2>
                        <a href="{{ route('bookings.index') }}" class="kpi-arrow-btn" title="{{ __('Semua Tempahan') }}">
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-dot" style="background: #7b6fb5;"></span>
                                <span class="text-muted">{{ __('Kenderaan + Pilot') }}</span>
                                <strong class="ms-1 tabular-nums">{{ $typeCounts['vehicle_and_pilot'] }}</strong>
                                <span class="text-muted" style="font-size: 0.68rem;">({{ $p1 }}%)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-dot" style="background: #a855d6;"></span>
                                <span class="text-muted">{{ __('Pandu Sendiri') }}</span>
                                <strong class="ms-1 tabular-nums">{{ $typeCounts['vehicle_only'] }}</strong>
                                <span class="text-muted" style="font-size: 0.68rem;">({{ $p2 }}%)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-dot" style="background: #f5a623;"></span>
                                <span class="text-muted">{{ __('Pilot Sahaja') }}</span>
                                <strong class="ms-1 tabular-nums">{{ $typeCounts['pilot_only'] }}</strong>
                                <span class="text-muted" style="font-size: 0.68rem;">({{ $p3 }}%)</span>
                            </div>
                        </div>

                        <div class="donut-ring" style="background: conic-gradient(#7b6fb5 0deg {{ $deg1 }}deg, #a855d6 {{ $deg1 }}deg {{ $deg2 }}deg, #f5a623 {{ $deg2 }}deg 360deg);">
                            <div class="donut-ring-inner">
                                <span class="text-muted" style="font-size: 0.62rem;">{{ __('Jumlah') }}</span>
                                <span class="fw-bold tabular-nums" style="font-size: 1.15rem; line-height: 1.1;">{{ $totalBookingsCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Pending Queue & Active Trips + Pilot/Vehicle Status --}}
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            {{-- Pending Approvals (FinSet Soft Lavender Table Header) --}}
            <div class="card mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h2 class="card-title-sm">{{ __('Permohonan Menunggu Kelulusan') }}</h2>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ __('Permohonan segera (< 3 hari) dipaparkan di atas.') }}</div>
                        </div>
                        <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-light">
                            {{ __('Lihat semua') }} <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-finset mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Kod & Pemohon') }}</th>
                                    <th>{{ __('Jenis') }}</th>
                                    <th>{{ __('Tarikh') }}</th>
                                    <th>{{ __('Destinasi') }}</th>
                                    <th class="text-end">{{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingBookings as $booking)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $booking->booking_code }}
                                                @if($booking->is_urgent)
                                                    <span class="badge bg-danger-subtle text-danger-emphasis ms-1">{{ __('Segera') }}</span>
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $booking->applicant->name }} &middot; {{ $booking->applicant->unit }}</div>
                                        </td>
                                        <td><span class="badge {{ $booking->requestTypeBadgeClass() }}">{{ $booking->requestTypeLabel() }}</span></td>
                                        <td class="text-nowrap">
                                            <div>{{ $booking->start_datetime->format('d/m/Y, h:i A') }}</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">{{ __('hingga') }} {{ $booking->end_datetime->format('d/m/Y, h:i A') }}</div>
                                        </td>
                                        <td style="max-width: 210px;">
                                            <div class="text-truncate" title="{{ $booking->destination }}">{{ $booking->destination }}</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-primary">{{ __('Proses') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state"><i class="bi bi-check2-circle"></i>{{ __('Tiada permohonan tertunggak.') }}</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Active & Upcoming --}}
            <div class="card">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="card-title-sm">{{ __('Perjalanan Aktif & Akan Datang') }}</h2>
                        <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-light">
                            {{ __('Kalendar') }} <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-finset mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Tempahan') }}</th>
                                    <th>{{ __('Kenderaan & Pilot') }}</th>
                                    <th>{{ __('Tempoh') }}</th>
                                    <th class="text-end">{{ __('Dokumen') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeAndUpcomingTrips as $trip)
                                    <tr>
                                        <td>
                                            <a href="{{ route('bookings.show', $trip) }}" class="fw-semibold text-decoration-none">{{ $trip->booking_code }}</a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $trip->applicant->name }}</div>
                                        </td>
                                        <td>
                                            @if($trip->vehicle)
                                                <div><i class="bi bi-car-front text-violet me-1"></i><span class="fw-medium">{{ $trip->vehicle->plate_number }}</span> <span class="text-muted">&middot; {{ $trip->vehicle->model }}</span></div>
                                            @endif
                                            @if($trip->pilot)
                                                <div class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-person-badge me-1"></i>{{ $trip->pilot->name }}</div>
                                            @elseif($trip->isSelfDrive())
                                                <div class="text-info-emphasis" style="font-size: 0.72rem;"><i class="bi bi-key me-1"></i>{{ __('Pandu sendiri') }}</div>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <div>{{ $trip->start_datetime->format('d/m/Y, h:i A') }}</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">{{ $trip->end_datetime->format('d/m/Y, h:i A') }}</div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('bookings.pdf.borang', $trip) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="{{ __('Borang Kebenaran Kenderaan') }}">
                                                <i class="bi bi-file-earmark-pdf"></i> {{ __('Borang') }}
                                            </a>
                                            @if($trip->canDownloadMemo())
                                                <a href="{{ route('bookings.pdf.memo', $trip) }}" target="_blank" class="btn btn-sm btn-outline-info" title="{{ __('Memo Kebenaran Memandu') }}">
                                                    <i class="bi bi-file-earmark-text"></i> {{ __('Memo') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"><div class="empty-state"><i class="bi bi-calendar-x"></i>{{ __('Tiada perjalanan dijadualkan.') }}</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            {{-- Status 4 Pilot HQ --}}
            <div class="card mb-3">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="card-title-sm">{{ __('Status Pilot HQ') }}</h2>
                        <a href="{{ route('admin.users.index', ['role' => 'pilot']) }}" class="kpi-arrow-btn" title="{{ __('Pengguna & Pilot') }}">
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>

                    @php
                        $pilotReadyPct = $totalActivePilots > 0 ? round(($availablePilots->count() / $totalActivePilots) * 100) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">{{ __('Ketersediaan Pilot') }}</span>
                            <span class="fw-semibold tabular-nums">{{ $availablePilots->count() }}/{{ $totalActivePilots }} ({{ $pilotReadyPct }}%)</span>
                        </div>
                        <div class="progress-pill">
                            <div class="progress-pill-bar" style="width: {{ $pilotReadyPct }}%;"></div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @foreach($pilots as $pilot)
                            @php $isFree = in_array($pilot->id, $availablePilotIds, true); @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="avatar" style="width: 30px; height: 30px; font-size: .66rem;">
                                        {{ collect(explode(' ', preg_replace('/^(Encik|Puan|Cik)\s+/i', '', $pilot->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="fw-medium text-truncate" style="font-size: 0.78rem;">{{ $pilot->name }}</div>
                                        <div class="text-muted" style="font-size: 0.69rem;">{{ $pilot->phone ?? '-' }}</div>
                                    </div>
                                </div>
                                @if($isFree)
                                    <span class="badge bg-success-subtle text-success-emphasis"><span class="status-dot bg-success"></span>{{ __('Tersedia') }}</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary-emphasis"><span class="status-dot bg-primary"></span>{{ __('Bertugas') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Status Kenderaan --}}
            <div class="card">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="card-title-sm">{{ __('Status Kenderaan') }}</h2>
                        <a href="{{ route('admin.vehicles.index') }}" class="kpi-arrow-btn" title="{{ __('Pengurusan Kenderaan') }}">
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @foreach($vehicles as $vehicle)
                            @php $isVehicleFree = in_array($vehicle->id, $availableVehicleIds, true); @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <div class="min-w-0">
                                    <div class="fw-semibold" style="font-size: 0.78rem;">{{ $vehicle->plate_number }} <span class="text-muted fw-normal">&middot; {{ $vehicle->type }}</span></div>
                                    <div class="text-muted text-truncate" style="font-size: 0.69rem;">{{ $vehicle->model }} &middot; {{ number_format($vehicle->current_odometer) }} km</div>
                                </div>
                                @if($vehicle->status === 'maintenance')
                                    <span class="badge bg-warning-subtle text-warning-emphasis"><span class="status-dot bg-warning"></span>{{ __('Selenggara') }}</span>
                                @elseif($vehicle->status === 'inactive')
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ __('Tidak aktif') }}</span>
                                @elseif($isVehicleFree)
                                    <span class="badge bg-success-subtle text-success-emphasis"><span class="status-dot bg-success"></span>{{ __('Tersedia') }}</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary-emphasis"><span class="status-dot bg-primary"></span>{{ __('Digunakan') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
