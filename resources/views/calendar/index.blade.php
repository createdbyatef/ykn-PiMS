@extends('layouts.app')

@section('title', __('Kalendar Ketersediaan'))
@section('page-title', __('Kalendar Ketersediaan'))
@section('page-subtitle', __('Semak jadual penggunaan kenderaan pejabat dan 4 Pilot HQ secara masa nyata.'))
@section('skeleton', 'calendar')

@section('content')
    {{-- Live Availability Snapshot Strip --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="section-label"><i class="bi bi-car-front me-1 text-violet"></i> {{ __('Kenderaan Pejabat (Semasa)') }}</span>
                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ __(':count Tersedia', ['count' => count($availableVehicleIds)]) }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($vehicles as $vehicle)
                            @php $free = in_array($vehicle->id, $availableVehicleIds, true); @endphp
                            <span class="badge rounded-pill px-2 py-1 {{ $vehicle->status === 'maintenance' ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : ($vehicle->status === 'inactive' ? 'bg-secondary-subtle text-secondary-emphasis' : ($free ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle')) }}">
                                <strong>{{ $vehicle->plate_number }}</strong> ({{ $vehicle->type }}) &middot;
                                @if($vehicle->status === 'maintenance')
                                    {{ __('Selenggara') }}
                                @elseif($vehicle->status === 'inactive')
                                    {{ __('Tidak aktif') }}
                                @elseif($free)
                                    {{ __('Tersedia') }}
                                @else
                                    {{ __('Digunakan') }}
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="section-label"><i class="bi bi-person-badge me-1 text-orchid"></i> {{ __('4 Pilot HQ (Semasa)') }}</span>
                        <span class="badge bg-info-subtle text-info-emphasis">{{ __(':avail / :total Tersedia', ['avail' => count($availablePilotIds), 'total' => $pilots->count()]) }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($pilots as $pilot)
                            @php $free = in_array($pilot->id, $availablePilotIds, true); @endphp
                            <span class="badge rounded-pill px-2 py-1 {{ $free ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' }}">
                                <span class="status-dot {{ $free ? 'bg-success' : 'bg-primary' }}"></span>
                                <strong>{{ $pilot->name }}</strong> &middot; {{ $free ? __('Tersedia') : __('Bertugas') }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters + Legend + CTA --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="filterVehicle" class="form-label small fw-medium mb-1">{{ __('Kenderaan') }}</label>
                    <select id="filterVehicle" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Kenderaan') }}</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} — {{ $vehicle->model }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="filterPilot" class="form-label small fw-medium mb-1">{{ __('Pilot HQ') }}</label>
                    <select id="filterPilot" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Pilot HQ') }}</option>
                        @foreach($pilots as $pilot)
                            <option value="{{ $pilot->id }}">{{ $pilot->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="filterType" class="form-label small fw-medium mb-1">{{ __('Jenis Permohonan') }}</label>
                    <select id="filterType" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        @foreach(\App\Models\Booking::translatedRequestTypes() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                    <button type="button" id="resetFiltersBtn" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset') }}
                    </button>
                    @if(auth()->user()->isStaff() || auth()->user()->isAdmin())
                        <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Permohonan') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-3 pt-2 border-top text-muted" style="font-size: 0.75rem;">
                <span><span class="legend-dot me-1" style="background: #7b6fb5;"></span> {{ __('Kenderaan + Pilot') }}</span>
                <span><span class="legend-dot me-1" style="background: #a855d6;"></span> {{ __('Pandu Sendiri') }}</span>
                <span><span class="legend-dot me-1" style="background: #e0910d;"></span> {{ __('Pilot Sahaja') }}</span>
                <span><span class="legend-dot me-1" style="background: #1f9d6b;"></span> {{ __('Selesai') }}</span>
                <span><span class="legend-dot me-1" style="background: #f5a623;"></span> {{ __('Menunggu Kelulusan') }}</span>
            </div>
        </div>
    </div>

    {{-- FullCalendar Container + Live AJAX Skeleton Overlay --}}
    <div class="card">
        <div class="card-body p-3 p-md-4">
            <div class="calendar-stage">
                <div id="calendarGridSkeleton" class="calendar-loading-skeleton is-active" aria-hidden="true">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="ykn-skel ykn-skel-pill" style="width: 112px; height: 28px;"></span>
                        <span class="ykn-skel ykn-skel-line" style="width: 145px; height: 18px;"></span>
                        <span class="ykn-skel ykn-skel-pill" style="width: 164px; height: 28px;"></span>
                    </div>
                    <div class="ykn-skel-cal-grid">
                        @for($c = 0; $c < 28; $c++)
                            <div class="ykn-skel-cal-cell">
                                <span class="ykn-skel ykn-skel-line mb-2" style="width: 22px; height: 10px;"></span>
                                @if(in_array($c, [1, 4, 8, 11, 15, 18, 22, 25], true))
                                    <span class="ykn-skel ykn-skel-pill" style="width: 84%; height: 16px;"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
                <div id="yknCalendar"></div>
            </div>
        </div>
    </div>

    {{-- Event Detail Modal --}}
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-semibold mb-0" id="modalBookingCode">{{ __('Butiran Tempahan') }}</h5>
                        <div class="text-muted" style="font-size: 0.75rem;" id="modalRequestType"></div>
                    </div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-flex gap-2 align-items-center">
                        <span class="badge" id="modalStatusBadge"></span>
                        <span class="badge bg-danger-subtle text-danger-emphasis d-none" id="modalUrgentBadge">{{ __('Segera (< 3 Hari)') }}</span>
                    </div>
                    <dl class="row small mb-0">
                        <dt class="col-4 text-muted fw-normal">{{ __('Pemohon') }}</dt>
                        <dd class="col-8 fw-semibold" id="modalApplicant"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Unit') }}</dt>
                        <dd class="col-8" id="modalUnit"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Tempoh') }}</dt>
                        <dd class="col-8" id="modalPeriod"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Kenderaan') }}</dt>
                        <dd class="col-8 fw-semibold text-violet" id="modalVehicle"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Pilot HQ') }}</dt>
                        <dd class="col-8 fw-semibold" id="modalPilot"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Destinasi') }}</dt>
                        <dd class="col-8" id="modalDestination"></dd>

                        <dt class="col-4 text-muted fw-normal">{{ __('Tujuan') }}</dt>
                        <dd class="col-8 mb-0" id="modalPurpose"></dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ __('Tutup') }}</button>
                    <a href="#" id="modalDetailLink" class="btn btn-sm btn-primary d-none">
                        <i class="bi bi-box-arrow-up-right me-1"></i> {{ __('Butiran Penuh') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('yknCalendar');
            const calSkeleton = document.getElementById('calendarGridSkeleton');
            const filterVehicle = document.getElementById('filterVehicle');
            const filterPilot = document.getElementById('filterPilot');
            const filterType = document.getElementById('filterType');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const detailModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            let calLoadingTimer = null;

            function setCalendarLoading(isLoading) {
                if (!calSkeleton) return;
                clearTimeout(calLoadingTimer);
                if (isLoading) {
                    calSkeleton.classList.add('is-active');
                } else {
                    calLoadingTimer = setTimeout(() => calSkeleton.classList.remove('is-active'), 160);
                }
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
                locale: @json(app()->getLocale()),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: @json(__('Hari Ini')),
                    month: @json(__('Bulan')),
                    week: @json(__('Minggu')),
                    day: @json(__('Hari')),
                    list: @json(__('Senarai'))
                },
                height: 'auto',
                navLinks: true,
                dayMaxEvents: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                loading: function (isLoading) {
                    setCalendarLoading(isLoading);
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                    });
                    if (filterVehicle.value) params.append('vehicle_id', filterVehicle.value);
                    if (filterPilot.value) params.append('pilot_id', filterPilot.value);
                    if (filterType.value) params.append('request_type', filterType.value);

                    fetch(`{{ route('calendar.events') }}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' }
                    })
                        .then(res => res.json())
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                },
                eventClick: function (info) {
                    const props = info.event.extendedProps;
                    document.getElementById('modalBookingCode').textContent = props.booking_code;
                    document.getElementById('modalRequestType').textContent = props.request_type;
                    document.getElementById('modalApplicant').textContent = props.applicant;
                    document.getElementById('modalUnit').textContent = props.unit;
                    document.getElementById('modalPeriod').textContent = `${props.start_formatted} — ${props.end_formatted}`;
                    document.getElementById('modalVehicle').textContent = props.vehicle;
                    document.getElementById('modalPilot').textContent = props.pilot;
                    document.getElementById('modalDestination').textContent = props.destination;
                    document.getElementById('modalPurpose').textContent = props.purpose;

                    const statusBadge = document.getElementById('modalStatusBadge');
                    statusBadge.textContent = props.status_label;
                    statusBadge.className = 'badge ' + ({
                        pending: 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                        approved: 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
                        completed: 'bg-success-subtle text-success-emphasis border border-success-subtle',
                        rejected: 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'
                    }[props.status] || 'bg-secondary-subtle text-secondary-emphasis');

                    const urgentBadge = document.getElementById('modalUrgentBadge');
                    urgentBadge.classList.toggle('d-none', !props.is_urgent);

                    const link = document.getElementById('modalDetailLink');
                    if (props.detail_url) {
                        link.href = props.detail_url;
                        link.classList.remove('d-none');
                    } else {
                        link.classList.add('d-none');
                    }

                    detailModal.show();
                }
            });

            calendar.render();
            setTimeout(() => setCalendarLoading(false), 3500);

            [filterVehicle, filterPilot, filterType].forEach(el => {
                el.addEventListener('change', () => calendar.refetchEvents());
            });

            resetBtn.addEventListener('click', () => {
                filterVehicle.value = '';
                filterPilot.value = '';
                filterType.value = '';
                calendar.refetchEvents();
            });
        });
    </script>
@endpush
