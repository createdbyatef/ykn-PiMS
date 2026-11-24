@extends('layouts.app')

@section('title', 'Papan Pemuka')
@section('page-title', 'Papan Pemuka')
@section('page-subtitle', 'Semak ketersediaan kenderaan & Pilot HQ serta urus permohonan anda.')

@section('content')
    {{-- Welcome banner --}}
    <div class="card brand-hero mb-4">
        <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="hero-title mb-1">Selamat datang, {{ auth()->user()->name }}</div>
                <div class="hero-sub">
                    {{ auth()->user()->jawatan }} &middot; {{ auth()->user()->unit }}
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge rounded-pill" style="background: rgba(255,255,255,.16); font-weight: 500;">
                        <i class="bi bi-car-front me-1"></i>{{ $availableVehiclesCount }}/{{ $totalVehiclesCount }} kenderaan tersedia
                    </span>
                    <span class="badge rounded-pill" style="background: rgba(255,255,255,.16); font-weight: 500;">
                        <i class="bi bi-person-badge me-1"></i>{{ $availablePilotsCount }}/{{ $totalPilotsCount }} Pilot HQ tersedia
                    </span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2" style="position: relative; z-index: 1;">
                <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-calendar3 me-1"></i> Kalendar
                </a>
                <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-warning fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Permohonan Baharu
                </a>
            </div>
        </div>
    </div>

    {{-- Personal stats --}}
    <div class="row g-3 mb-4">
        @php
            $statCards = [
                ['label' => 'Menunggu', 'value' => $stats['pending'], 'icon' => 'bi-hourglass-split', 'cls' => 'bg-warning-subtle text-warning-emphasis', 'status' => 'pending'],
                ['label' => 'Diluluskan', 'value' => $stats['approved'], 'icon' => 'bi-check2-circle', 'cls' => 'bg-primary-subtle text-primary', 'status' => 'approved'],
                ['label' => 'Selesai', 'value' => $stats['completed'], 'icon' => 'bi-flag', 'cls' => 'bg-success-subtle text-success', 'status' => 'completed'],
                ['label' => 'Tidak Diluluskan', 'value' => $stats['rejected'], 'icon' => 'bi-x-circle', 'cls' => 'bg-danger-subtle text-danger', 'status' => 'rejected'],
            ];
        @endphp
        @foreach($statCards as $card)
            <div class="col-6 col-md-3">
                <a href="{{ route('bookings.index', ['status' => $card['status']]) }}" class="text-decoration-none">
                    <div class="card kpi-card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="kpi-label mb-1">{{ $card['label'] }}</div>
                                <div class="kpi-value">{{ $card['value'] }}</div>
                            </div>
                            <span class="kpi-icon {{ $card['cls'] }}"><i class="bi {{ $card['icon'] }}"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Recent bookings --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title-sm">Permohonan Terkini</h2>
            <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-light">Lihat semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tempahan</th>
                        <th>Jenis &amp; Destinasi</th>
                        <th>Tarikh Perjalanan</th>
                        <th>Kenderaan / Pilot</th>
                        <th>Status</th>
                        <th class="text-end">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr>
                            <td class="text-nowrap">
                                <a href="{{ route('bookings.show', $booking) }}" class="fw-semibold text-decoration-none">{{ $booking->booking_code }}</a>
                                @if($booking->is_urgent)
                                    <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Segera</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $booking->requestTypeBadgeClass() }}">{{ $booking->requestTypeLabel() }}</span>
                                <div class="mt-1">{{ $booking->destination }}</div>
                            </td>
                            <td class="text-nowrap">
                                <div>{{ $booking->start_datetime->format('d/m/Y, h:i A') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->end_datetime->format('d/m/Y, h:i A') }}</div>
                            </td>
                            <td>
                                @if($booking->vehicle)
                                    <div><i class="bi bi-car-front text-violet me-1"></i>{{ $booking->vehicle->plate_number }}</div>
                                @endif
                                @if($booking->pilot)
                                    <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-1"></i>{{ $booking->pilot->name }}</div>
                                @elseif($booking->isSelfDrive() && $booking->isApproved())
                                    <div class="text-info-emphasis" style="font-size: 0.75rem;"><i class="bi bi-key me-1"></i>Pandu sendiri</div>
                                @elseif(!$booking->vehicle && !$booking->pilot)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $booking->statusBadgeClass() }}">{{ $booking->statusLabel() }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bookings.pdf.borang', $booking) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Borang Kebenaran Kenderaan">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @if($booking->canDownloadMemo())
                                    <a href="{{ route('bookings.pdf.memo', $booking) }}" target="_blank" class="btn btn-sm btn-info" title="Memo Kebenaran Memandu">
                                        <i class="bi bi-file-earmark-text me-1"></i>Memo
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-journal-plus"></i>
                                    Anda belum membuat sebarang permohonan.
                                    <div class="mt-3"><a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">Buat permohonan</a></div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
