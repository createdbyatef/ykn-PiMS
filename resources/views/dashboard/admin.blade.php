@extends('layouts.app')

@section('title', 'Papan Pemuka')
@section('page-title', 'Papan Pemuka Unit Pentadbiran')
@section('page-subtitle', 'Ringkasan tempahan, status Pilot HQ dan armada kenderaan semasa.')

@section('content')
    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100">
                    <div class="card-body d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="kpi-label mb-1">Menunggu Kelulusan</div>
                            <div class="kpi-value">{{ $pendingCount }}</div>
                            @if($urgentPendingCount > 0)
                                <span class="badge bg-danger-subtle text-danger-emphasis mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $urgentPendingCount }} segera
                                </span>
                            @else
                                <div class="text-muted small mt-1">Tiada permohonan segera</div>
                            @endif
                        </div>
                        <span class="kpi-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <div class="kpi-label mb-1">Perjalanan Aktif</div>
                        <div class="kpi-value">{{ $activeTripsCount }}</div>
                        <div class="text-muted small mt-1">{{ $upcomingApprovedCount }} dijadualkan akan datang</div>
                    </div>
                    <span class="kpi-icon bg-primary-subtle text-primary"><i class="bi bi-geo-alt"></i></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <div class="kpi-label mb-1">Pilot HQ Tersedia</div>
                        <div class="kpi-value">{{ $availablePilots->count() }}<span class="text-muted fw-normal" style="font-size: 0.9rem;"> / {{ $totalActivePilots }}</span></div>
                        <div class="small mt-1 text-success-emphasis"><span class="status-dot bg-success"></span>Status semasa</div>
                    </div>
                    <span class="kpi-icon bg-info-subtle text-info-emphasis"><i class="bi bi-person-badge"></i></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.vehicles.index', ['status' => 'maintenance']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100">
                    <div class="card-body d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="kpi-label mb-1">Dalam Penyelenggaraan</div>
                            <div class="kpi-value">{{ $maintenanceVehiclesCount }}<span class="text-muted fw-normal" style="font-size: 0.9rem;"> / {{ $totalVehiclesCount }}</span></div>
                            <div class="text-muted small mt-1">Kenderaan sedang diservis</div>
                        </div>
                        <span class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-tools"></i></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            {{-- Pending approvals --}}
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h2 class="card-title-sm">Permohonan Menunggu Kelulusan</h2>
                        <div class="text-muted" style="font-size: 0.75rem;">Permohonan segera (&lt; 3 hari) dipaparkan dahulu.</div>
                    </div>
                    <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-light">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tempahan</th>
                                <th>Jenis</th>
                                <th>Tarikh Perjalanan</th>
                                <th>Destinasi</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingBookings as $booking)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $booking->booking_code }}
                                            @if($booking->is_urgent)
                                                <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Segera</span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->applicant->name }} &middot; {{ $booking->applicant->unit }}</div>
                                    </td>
                                    <td><span class="badge {{ $booking->requestTypeBadgeClass() }}">{{ $booking->requestTypeLabel() }}</span></td>
                                    <td class="text-nowrap">
                                        <div>{{ $booking->start_datetime->format('d/m/Y, h:i A') }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">hingga {{ $booking->end_datetime->format('d/m/Y, h:i A') }}</div>
                                    </td>
                                    <td style="max-width: 220px;">
                                        <div class="text-truncate" title="{{ $booking->destination }}">{{ $booking->destination }}</div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-primary">Proses</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state"><i class="bi bi-check2-circle"></i>Tiada permohonan tertunggak.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Active & upcoming --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title-sm">Perjalanan Aktif &amp; Akan Datang</h2>
                    <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-light"><i class="bi bi-calendar3 me-1"></i> Kalendar</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tempahan</th>
                                <th>Kenderaan &amp; Pilot</th>
                                <th>Tempoh</th>
                                <th class="text-end">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeAndUpcomingTrips as $trip)
                                <tr>
                                    <td>
                                        <a href="{{ route('bookings.show', $trip) }}" class="fw-semibold text-decoration-none">{{ $trip->booking_code }}</a>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $trip->applicant->name }}</div>
                                    </td>
                                    <td>
                                        @if($trip->vehicle)
                                            <div><i class="bi bi-car-front text-violet me-1"></i><span class="fw-medium">{{ $trip->vehicle->plate_number }}</span> <span class="text-muted">· {{ $trip->vehicle->model }}</span></div>
                                        @endif
                                        @if($trip->pilot)
                                            <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-1"></i>{{ $trip->pilot->name }}</div>
                                        @elseif($trip->isSelfDrive())
                                            <div class="text-info-emphasis" style="font-size: 0.75rem;"><i class="bi bi-key me-1"></i>Pandu sendiri</div>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <div>{{ $trip->start_datetime->format('d/m/Y, h:i A') }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $trip->end_datetime->format('d/m/Y, h:i A') }}</div>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('bookings.pdf.borang', $trip) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Borang Kebenaran Kenderaan">
                                            <i class="bi bi-file-earmark-pdf"></i> Borang
                                        </a>
                                        @if($trip->canDownloadMemo())
                                            <a href="{{ route('bookings.pdf.memo', $trip) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Memo Kebenaran Memandu">
                                                <i class="bi bi-file-earmark-text"></i> Memo
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"><div class="empty-state"><i class="bi bi-calendar-x"></i>Tiada perjalanan dijadualkan.</div></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title-sm">Status Pilot HQ</h2>
                    <a href="{{ route('admin.users.index', ['role' => 'pilot']) }}" class="small text-decoration-none">Urus</a>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($pilots as $pilot)
                        @php $isFree = in_array($pilot->id, $availablePilotIds, true); @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2-5 px-3" style="padding-top: .7rem; padding-bottom: .7rem;">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <span class="avatar" style="width: 30px; height: 30px; font-size: .68rem;">
                                    {{ collect(explode(' ', preg_replace('/^(Encik|Puan|Cik)\s+/i', '', $pilot->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') }}
                                </span>
                                <div class="min-w-0">
                                    <div class="fw-medium text-truncate">{{ $pilot->name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $pilot->phone ?? '-' }}</div>
                                </div>
                            </div>
                            @if($isFree)
                                <span class="badge bg-success-subtle text-success-emphasis"><span class="status-dot bg-success"></span>Tersedia</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary-emphasis"><span class="status-dot bg-primary"></span>Bertugas</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title-sm">Status Kenderaan</h2>
                    <a href="{{ route('admin.vehicles.index') }}" class="small text-decoration-none">Urus</a>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($vehicles as $vehicle)
                        @php $isVehicleFree = in_array($vehicle->id, $availableVehicleIds, true); @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3" style="padding-top: .7rem; padding-bottom: .7rem;">
                            <div class="min-w-0">
                                <div class="fw-medium">{{ $vehicle->plate_number }} <span class="text-muted fw-normal">· {{ $vehicle->type }}</span></div>
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $vehicle->model }} · {{ number_format($vehicle->current_odometer) }} km</div>
                            </div>
                            @if($vehicle->status === 'maintenance')
                                <span class="badge bg-warning-subtle text-warning-emphasis"><span class="status-dot bg-warning"></span>Selenggara</span>
                            @elseif($vehicle->status === 'inactive')
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Tidak aktif</span>
                            @elseif($isVehicleFree)
                                <span class="badge bg-success-subtle text-success-emphasis"><span class="status-dot bg-success"></span>Tersedia</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary-emphasis"><span class="status-dot bg-primary"></span>Digunakan</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
