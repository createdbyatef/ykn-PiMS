@extends('layouts.app')

@section('title', 'Tugasan Pilot HQ')
@section('page-title', 'Papan Pemuka Pilot HQ')
@section('page-subtitle', 'Senarai tugasan perjalanan harian, akan datang dan log bacaan odometer.')

@section('content')
    {{-- Mobile-friendly Summary KPI Strip --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card kpi-card h-100">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label mb-1">Hari Ini</div>
                        <div class="kpi-value text-orchid">{{ $todayTrips->count() }}</div>
                    </div>
                    <span class="kpi-icon bg-primary-subtle text-primary"><i class="bi bi-sun"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card h-100">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label mb-1">Akan Datang</div>
                        <div class="kpi-value text-violet">{{ $upcomingTrips->count() }}</div>
                    </div>
                    <span class="kpi-icon bg-info-subtle text-info-emphasis"><i class="bi bi-calendar-event"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card h-100">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label mb-1">Selesai</div>
                        <div class="kpi-value text-success-emphasis">{{ $completedTrips->count() }}</div>
                    </div>
                    <span class="kpi-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card h-100">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="kpi-label mb-1">Jumlah Jarak</div>
                        <div class="kpi-value">{{ number_format($totalDistanceKm) }} <span class="fw-normal text-muted" style="font-size: 0.78rem;">km</span></div>
                    </div>
                    <span class="kpi-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-speedometer2"></i></span>
                </div>
            </div>
        </div>
    </div>

    @if($overdueTrips->isNotEmpty())
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Tugasan Lepas Menunggu Rekod Odometer ({{ $overdueTrips->count() }})</div>
            <div class="d-grid gap-2">
                @foreach($overdueTrips as $trip)
                    <div class="bg-white rounded p-3 border d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="small">
                            <strong>{{ $trip->booking_code }}</strong> &mdash; {{ $trip->destination }}
                            <div class="text-muted">{{ $trip->start_datetime->format('d/m/Y, h:i A') }} &middot; {{ $trip->vehicle?->plate_number ?? 'Pilot Sahaja' }}</div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm btn-success open-complete-modal"
                            data-action="{{ route('bookings.complete', $trip) }}"
                            data-code="{{ $trip->booking_code }}"
                            data-destination="{{ $trip->destination }}"
                            data-vehicle="{{ $trip->vehicle ? $trip->vehicle->displayName() : 'Tiada (Pilot Sahaja)' }}"
                            data-has-vehicle="{{ $trip->vehicle ? '1' : '0' }}"
                            data-current-odo="{{ $trip->vehicle?->current_odometer ?? '' }}"
                        >
                            <i class="bi bi-speedometer2 me-1"></i> Rekod Odometer
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Mobile-Friendly Tabs --}}
    <ul class="nav nav-pills nav-fill bg-white p-1 rounded-3 border mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#paneToday" type="button">
                <i class="bi bi-sun me-1"></i> Hari Ini <span class="badge bg-white text-dark ms-1">{{ $todayTrips->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#paneUpcoming" type="button">
                <i class="bi bi-calendar-event me-1"></i> Akan Datang <span class="badge bg-light text-dark ms-1">{{ $upcomingTrips->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#paneCompleted" type="button">
                <i class="bi bi-check2-circle me-1"></i> Selesai <span class="badge bg-light text-dark ms-1">{{ $completedTrips->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Today's Trips --}}
        <div class="tab-pane fade show active" id="paneToday">
            <div class="row g-3">
                @forelse($todayTrips as $trip)
                    <div class="col-12 col-lg-6">
                        <div class="card border-primary-subtle h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ $trip->booking_code }}</span>
                                        <span class="badge {{ $trip->requestTypeBadgeClass() }}">{{ $trip->requestTypeLabel() }}</span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success-emphasis"><span class="status-dot bg-success"></span>Hari Ini</span>
                                </div>

                                <h3 class="h6 fw-semibold mb-2">
                                    <i class="bi bi-geo-alt text-orchid me-1"></i>{{ $trip->destination }}
                                </h3>
                                <p class="small text-muted mb-3">{{ $trip->purpose }}</p>

                                <div class="rounded p-3 small mb-3 border" style="background: #faf9fc;">
                                    <div class="mb-1"><i class="bi bi-clock text-violet me-2"></i><strong>Tempoh:</strong> {{ $trip->start_datetime->format('d/m/Y, h:i A') }} &ndash; {{ $trip->end_datetime->format('d/m/Y, h:i A') }}</div>
                                    <div class="mb-1"><i class="bi bi-person text-violet me-2"></i><strong>Pegawai:</strong> {{ $trip->applicant->name }} ({{ $trip->applicant->phone ?? '-' }})</div>
                                    <div class="mb-0">
                                        <i class="bi bi-car-front text-violet me-2"></i><strong>Kenderaan:</strong>
                                        @if($trip->vehicle)
                                            {{ $trip->vehicle->displayName() }} ({{ number_format($trip->vehicle->current_odometer) }} km)
                                        @else
                                            Pilot Sahaja (Tiada Kenderaan Pejabat)
                                        @endif
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-sm-flex">
                                    <button
                                        type="button"
                                        class="btn btn-primary flex-grow-1 open-complete-modal"
                                        data-action="{{ route('bookings.complete', $trip) }}"
                                        data-code="{{ $trip->booking_code }}"
                                        data-destination="{{ $trip->destination }}"
                                        data-vehicle="{{ $trip->vehicle ? $trip->vehicle->displayName() : 'Tiada (Pilot Sahaja)' }}"
                                        data-has-vehicle="{{ $trip->vehicle ? '1' : '0' }}"
                                        data-current-odo="{{ $trip->vehicle?->current_odometer ?? '' }}"
                                    >
                                        <i class="bi bi-speedometer2 me-1"></i> Log Odometer &amp; Selesai
                                    </button>
                                    <a href="{{ route('bookings.show', $trip) }}" class="btn btn-light">Butiran</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="empty-state">
                                <i class="bi bi-calendar-check"></i>
                                Tiada tugasan perjalanan dijadualkan untuk hari ini.
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Trips --}}
        <div class="tab-pane fade" id="paneUpcoming">
            <div class="row g-3">
                @forelse($upcomingTrips as $trip)
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-violet">{{ $trip->booking_code }}</span>
                                    <span class="badge {{ $trip->requestTypeBadgeClass() }}">{{ $trip->requestTypeLabel() }}</span>
                                </div>
                                <h3 class="h6 fw-semibold mb-2">{{ $trip->destination }}</h3>
                                <div class="small text-muted mb-3">
                                    <div><i class="bi bi-calendar3 me-1"></i> {{ $trip->start_datetime->format('d/m/Y, h:i A') }} &ndash; {{ $trip->end_datetime->format('d/m/Y, h:i A') }}</div>
                                    <div><i class="bi bi-person me-1"></i> {{ $trip->applicant->name }} ({{ $trip->applicant->unit }})</div>
                                    <div><i class="bi bi-car-front me-1"></i> {{ $trip->vehicle ? $trip->vehicle->displayName() : 'Pilot Sahaja' }}</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bookings.show', $trip) }}" class="btn btn-sm btn-outline-primary">Butiran</a>
                                    <a href="{{ route('bookings.pdf.borang', $trip) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Borang PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="empty-state">
                                <i class="bi bi-calendar2"></i>
                                Tiada tugasan akan datang buat masa ini.
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Completed Trips --}}
        <div class="tab-pane fade" id="paneCompleted">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kod &amp; Tarikh</th>
                                <th>Destinasi &amp; Pegawai</th>
                                <th>Kenderaan</th>
                                <th>Odometer &amp; Jarak</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedTrips as $trip)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $trip->booking_code }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $trip->start_datetime->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $trip->destination }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $trip->applicant->name }}</div>
                                    </td>
                                    <td>{{ $trip->vehicle ? $trip->vehicle->plate_number : 'Pilot Sahaja' }}</td>
                                    <td>
                                        @if($trip->totalDistance() !== null)
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                                {{ number_format($trip->totalDistance()) }} km
                                            </span>
                                            <div class="text-muted mt-1" style="font-size: 0.74rem;">
                                                {{ number_format($trip->odometer_start) }} &rarr; {{ number_format($trip->odometer_end) }} km
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('bookings.show', $trip) }}" class="btn btn-sm btn-light">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state"><i class="bi bi-clock-history"></i>Tiada rekod perjalanan selesai lagi.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile-Friendly Odometer Logging Modal --}}
    <div class="modal fade" id="completeTripModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" id="completeTripForm">
                    @csrf
                    <div class="modal-header bg-orchid-subtle">
                        <div>
                            <h5 class="modal-title fw-semibold text-primary-emphasis mb-0"><i class="bi bi-speedometer2 me-2"></i>Rekod Selesai Perjalanan</h5>
                            <div id="modalTripSubtitle" class="text-muted" style="font-size: 0.75rem;"></div>
                        </div>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-3 rounded border small mb-3" style="background: #faf9fc;">
                            <div><strong>Destinasi:</strong> <span id="modalTripDest"></span></div>
                            <div><strong>Kenderaan:</strong> <span id="modalTripVehicle"></span></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="modalOdoStart" class="form-label fw-semibold small">Odometer Mula (km)</label>
                                <input type="number" name="odometer_start" id="modalOdoStart" class="form-control" min="0">
                            </div>
                            <div class="col-6">
                                <label for="modalOdoEnd" class="form-label fw-semibold small">Odometer Tamat (km)</label>
                                <input type="number" name="odometer_end" id="modalOdoEnd" class="form-control" min="1">
                            </div>
                        </div>

                        <div id="modalDistanceBox" class="alert alert-success py-2 text-center fw-semibold d-none mb-3 border-0">
                            Jumlah Jarak Perjalanan: <span id="modalDistanceKm">0</span> km
                        </div>

                        <div class="mb-0">
                            <label for="modalPilotRemarks" class="form-label fw-semibold small">Catatan Perjalanan / Keadaan Kenderaan</label>
                            <textarea name="pilot_remarks" id="modalPilotRemarks" rows="3" class="form-control" placeholder="Contoh: Perjalanan selamat, tiada isu teknikal..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary px-3">
                            <i class="bi bi-check2-all me-1"></i> Sahkan &amp; Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('completeTripModal');
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('completeTripForm');
            const odoStart = document.getElementById('modalOdoStart');
            const odoEnd = document.getElementById('modalOdoEnd');
            const distBox = document.getElementById('modalDistanceBox');
            const distKm = document.getElementById('modalDistanceKm');

            function calcModalDistance() {
                const s = parseInt(odoStart.value, 10);
                const e = parseInt(odoEnd.value, 10);
                if (!isNaN(s) && !isNaN(e) && e > s) {
                    distKm.textContent = (e - s).toLocaleString();
                    distBox.classList.remove('d-none');
                } else {
                    distBox.classList.add('d-none');
                }
            }

            odoStart.addEventListener('input', calcModalDistance);
            odoEnd.addEventListener('input', calcModalDistance);

            document.querySelectorAll('.open-complete-modal').forEach(btn => {
                btn.addEventListener('click', function () {
                    form.action = this.dataset.action;
                    document.getElementById('modalTripSubtitle').textContent = this.dataset.code;
                    document.getElementById('modalTripDest').textContent = this.dataset.destination;
                    document.getElementById('modalTripVehicle').textContent = this.dataset.vehicle;

                    const hasVehicle = this.dataset.hasVehicle === '1';
                    odoStart.required = hasVehicle;
                    odoEnd.required = hasVehicle;
                    odoStart.value = this.dataset.currentOdo || '';
                    odoEnd.value = '';
                    document.getElementById('modalPilotRemarks').value = '';
                    calcModalDistance();
                    modal.show();
                });
            });
        });
    </script>
@endpush
