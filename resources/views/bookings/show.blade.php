@extends('layouts.app')

@section('title', __('Tempahan :code', ['code' => $booking->booking_code]))
@section('page-title', __('Tempahan :code', ['code' => $booking->booking_code]))
@section('page-subtitle', __('Semakan butiran permohonan, kelulusan Unit Pentadbiran, log odometer dan cetakan PDF rasmi.'))
@section('skeleton', 'form')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <a href="{{ auth()->user()->isPilot() ? route('pilot.trips.index') : route('bookings.index') }}" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left me-1"></i> {{ __('Kembali') }}
        </a>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('bookings.pdf.borang', $booking) }}" target="_blank" class="btn btn-sm btn-dark">
                <i class="bi bi-file-earmark-pdf me-1"></i> {{ __('Borang Kenderaan (PDF)') }}
            </a>
            @if($booking->canDownloadMemo())
                <a href="{{ route('bookings.pdf.memo', $booking) }}" target="_blank" class="btn btn-sm btn-info">
                    <i class="bi bi-file-earmark-text me-1"></i> {{ __('Memo Memandu (PDF)') }}
                </a>
            @endif
            @if(($booking->isPending() && (auth()->user()->isAdmin() || $booking->user_id === auth()->id())) || (auth()->user()->isAdmin() && $booking->isRejected()))
                <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm(@json(__('Adakah anda pasti ingin membatalkan/memadam tempahan ini?')));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> {{ __('Batal / Padam') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Complete Booking Summary --}}
        <div class="col-12 col-lg-7">
            <div class="card mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">{{ $booking->booking_code }}</span>
                        <span class="badge {{ $booking->requestTypeBadgeClass() }}">{{ $booking->requestTypeLabel() }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        @if($booking->is_urgent)
                            <span class="badge bg-danger-subtle text-danger-emphasis">{{ __('Segera (< 3 Hari)') }}</span>
                        @endif
                        <span class="badge {{ $booking->statusBadgeClass() }}">{{ $booking->statusLabel() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="section-label mb-3">{{ __('Maklumat Pemohon') }}</div>
                    <div class="row g-3 small mb-4">
                        <div class="col-sm-6">
                            <div class="detail-label">{{ __('Nama Pemohon') }}</div>
                            <div class="detail-value">{{ $booking->applicant->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">{{ __('Jawatan & Unit') }}</div>
                            <div class="detail-value">{{ $booking->applicant->jawatan }}</div>
                            <div class="text-muted">{{ $booking->applicant->unit }}</div>
                        </div>
                        @if($booking->isSelfDrive())
                            <div class="col-sm-6">
                                <div class="detail-label">{{ __('No. Lesen Memandu') }}</div>
                                <div class="detail-value text-primary-emphasis">{{ $booking->driver_license_no ?? '-' }}</div>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <div class="detail-label">{{ __('Tarikh Permohonan') }}</div>
                            <div class="detail-value">{{ $booking->created_at->format('d/m/Y, h:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="section-label mb-3">{{ __('Maklumat Perjalanan') }}</div>
                    <div class="row g-3 small mb-4">
                        <div class="col-sm-6">
                            <div class="detail-label">{{ __('Tarikh & Masa Mula') }}</div>
                            <div class="detail-value text-violet">{{ $booking->start_datetime->format('d/m/Y, h:i A') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">{{ __('Tarikh & Masa Tamat') }}</div>
                            <div class="detail-value text-violet">{{ $booking->end_datetime->format('d/m/Y, h:i A') }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">{{ __('Destinasi') }}</div>
                            <div class="detail-value">{{ $booking->destination }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label mb-1">{{ __('Tujuan Rasmi') }}</div>
                            <div class="p-3 rounded border" style="background: #faf9fc;">{{ $booking->purpose }}</div>
                        </div>
                    </div>

                    @if($booking->isApproved() || $booking->isCompleted())
                        <hr>
                        <div class="section-label mb-3">{{ __('Tugasan Kenderaan & Pilot') }}</div>
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <div class="detail-label">{{ __('Kenderaan Ditugaskan') }}</div>
                                @if($booking->vehicle)
                                    <div class="detail-value">{{ $booking->vehicle->plate_number }}</div>
                                    <div class="text-muted">{{ $booking->vehicle->model }} ({{ $booking->vehicle->type }})</div>
                                @else
                                    <div class="text-muted">{{ __('Tiada (Pilot Sahaja)') }}</div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-label">{{ __('Pilot HQ Ditugaskan') }}</div>
                                @if($booking->pilot)
                                    <div class="detail-value">{{ $booking->pilot->name }}</div>
                                    <div class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->pilot->phone ?? '-' }}</div>
                                @elseif($booking->isSelfDrive())
                                    <div class="detail-value text-primary-emphasis">{{ __('Pandu sendiri oleh pemohon') }}</div>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-label">{{ __('Diluluskan Oleh') }}</div>
                                <div class="detail-value">{{ $booking->approver?->name ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-label">{{ __('Tarikh Kelulusan') }}</div>
                                <div class="detail-value">{{ $booking->approved_at?->format('d/m/Y, h:i A') ?? '-' }}</div>
                            </div>
                        </div>
                    @endif

                    @if($booking->isRejected())
                        <hr>
                        <div class="alert alert-danger mb-0 border-0">
                            <div class="fw-semibold mb-1"><i class="bi bi-x-circle me-1"></i> {{ __('Permohonan Tidak Dapat Dipenuhi') }}</div>
                            <div><strong>{{ __('Sebab Rasmi:') }}</strong> {{ $booking->rejectionReasonLabel() }}</div>
                            @if($booking->rejection_remarks)
                                <div class="mt-1"><strong>{{ __('Catatan Admin:') }}</strong> {{ $booking->rejection_remarks }}</div>
                            @endif
                            <div class="text-muted mt-2" style="font-size: 0.75rem;">
                                {{ __('Diproses oleh :name pada :date', ['name' => $booking->approver?->name ?? __('Unit Pentadbiran'), 'date' => $booking->approved_at?->format('d/m/Y, h:i A')]) }}
                            </div>
                        </div>
                    @endif

                    @if($booking->isCompleted())
                        <hr>
                        <div class="section-label text-success-emphasis mb-3"><i class="bi bi-speedometer2 me-1"></i> {{ __('Log Odometer & Rekod Selesai') }}</div>
                        <div class="row g-3 small">
                            <div class="col-4">
                                <div class="p-3 rounded border text-center" style="background: #faf9fc;">
                                    <div class="detail-label">{{ __('Odometer Mula') }}</div>
                                    <div class="fw-bold fs-6">{{ $booking->odometer_start !== null ? number_format($booking->odometer_start) : '-' }} <span class="fw-normal text-muted" style="font-size: 0.74rem;">km</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded border text-center" style="background: #faf9fc;">
                                    <div class="detail-label">{{ __('Odometer Tamat') }}</div>
                                    <div class="fw-bold fs-6">{{ $booking->odometer_end !== null ? number_format($booking->odometer_end) : '-' }} <span class="fw-normal text-muted" style="font-size: 0.74rem;">km</span></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success-subtle rounded border border-success-subtle text-center">
                                    <div class="detail-label text-success-emphasis">{{ __('Jumlah Jarak') }}</div>
                                    <div class="fw-bold fs-6 text-success-emphasis">{{ $booking->totalDistance() !== null ? number_format($booking->totalDistance()) : '-' }} <span class="fw-normal" style="font-size: 0.74rem;">km</span></div>
                                </div>
                            </div>
                            @if($booking->pilot_remarks)
                                <div class="col-12">
                                    <div class="detail-label mb-1">{{ __('Catatan Pilot / Pemandu') }}</div>
                                    <div class="p-3 rounded border" style="background: #faf9fc;">{{ $booking->pilot_remarks }}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Action Panels --}}
        <div class="col-12 col-lg-5">
            {{-- Admin Pending Approval / Rejection Panel --}}
            @if(auth()->user()->isAdmin() && $booking->isPending())
                <div class="card border-primary-subtle mb-4">
                    <div class="card-header bg-primary-subtle">
                        <h2 class="card-title-sm text-primary-emphasis"><i class="bi bi-shield-check me-2"></i>{{ __('Tindakan Unit Pentadbiran') }}</h2>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill border mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabApprove" type="button">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('Luluskan') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabReject" type="button">
                                    <i class="bi bi-x-circle me-1"></i> {{ __('Tolak') }}
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- Approve Form --}}
                            <div class="tab-pane fade show active" id="tabApprove">
                                <form method="POST" action="{{ route('bookings.approve', $booking) }}">
                                    @csrf

                                    @if($booking->requiresVehicle())
                                        <div class="mb-3">
                                            <label for="vehicle_id" class="form-label fw-semibold small">
                                                {{ __('Tugaskan Kenderaan') }} <span class="text-danger">*</span>
                                            </label>
                                            <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                                <option value="">{{ __('-- Pilih Kenderaan Tersedia --') }}</option>
                                                @foreach($vehiclesAvailability as $item)
                                                    @php $v = $item['vehicle']; @endphp
                                                    <option
                                                        value="{{ $v->id }}"
                                                        @disabled(!$item['available'])
                                                        @selected(old('vehicle_id') == $v->id)
                                                    >
                                                        {{ $v->plate_number }} — {{ $v->model }} ({{ $v->type }}, {{ $v->capacity }} pax)
                                                        @if(!$item['available'])
                                                            [{{ __('TIDAK TERSEDIA') }}: {{ $item['reason'] }}]
                                                        @else
                                                            [{{ __('TERSEDIA') }}]
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">{{ __('Kenderaan diselenggara atau bertindih jadual disekat secara automatik.') }}</div>
                                            @error('vehicle_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    @if($booking->requiresPilot())
                                        <div class="mb-3">
                                            <label for="pilot_id" class="form-label fw-semibold small">
                                                {{ __('Tugaskan Pilot HQ') }} <span class="text-danger">*</span>
                                            </label>
                                            <select name="pilot_id" id="pilot_id" class="form-select @error('pilot_id') is-invalid @enderror" required>
                                                <option value="">{{ __('-- Pilih Pilot HQ Tersedia --') }}</option>
                                                @foreach($pilotsAvailability as $item)
                                                    @php $p = $item['pilot']; @endphp
                                                    <option
                                                        value="{{ $p->id }}"
                                                        @disabled(!$item['available'])
                                                        @selected(old('pilot_id') == $p->id)
                                                    >
                                                        {{ $p->name }} ({{ $p->phone ?? '-' }})
                                                        @if(!$item['available'])
                                                            [{{ __('BERTINDIH') }}: {{ $item['reason'] }}]
                                                        @else
                                                            [{{ __('TERSEDIA') }}]
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">{{ __('Semakan pertindihan bagi :start — :end.', ['start' => $booking->start_datetime->format('d/m H:i'), 'end' => $booking->end_datetime->format('d/m H:i')]) }}</div>
                                            @error('pilot_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    @if($booking->isSelfDrive())
                                        <div class="alert alert-info py-2 mb-3 border-0">
                                            <i class="bi bi-file-earmark-text me-1"></i>
                                            {{ __('Meluluskan permohonan ini akan mengaktifkan Memo Kebenaran Memandu secara automatik.') }}
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-lg me-1"></i> {{ __('Luluskan & Sahkan Tugasan') }}
                                    </button>
                                </form>
                            </div>

                            {{-- Reject Form --}}
                            <div class="tab-pane fade" id="tabReject">
                                <form method="POST" action="{{ route('bookings.reject', $booking) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">{{ __('Sebab Penolakan Rasmi (Borang YKN)') }} <span class="text-danger">*</span></label>
                                        @foreach(\App\Models\Booking::translatedRejectionReasons() as $key => $label)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="rejection_reason" id="reason_{{ $key }}" value="{{ $key }}"
                                                    @checked(old('rejection_reason') === $key) required>
                                                <label class="form-check-label small" for="reason_{{ $key }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mb-3">
                                        <label for="rejection_remarks" class="form-label fw-semibold small">{{ __('Catatan Tambahan') }}</label>
                                        <textarea name="rejection_remarks" id="rejection_remarks" rows="3" class="form-control form-control-sm" placeholder="{{ __('Nyatakan ulasan tambahan (wajib jika memilih Lain-lain)...') }}">{{ old('rejection_remarks') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-x-circle me-1"></i> {{ __('Sahkan Penolakan') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Odometer & Trip Completion Form --}}
            @if($booking->isApproved() && (auth()->user()->isAdmin() || (auth()->user()->isPilot() && $booking->pilot_id === auth()->id())))
                <div class="card border-success-subtle mb-4">
                    <div class="card-header bg-success-subtle">
                        <h2 class="card-title-sm text-success-emphasis"><i class="bi bi-speedometer2 me-2"></i>{{ __('Rekod Odometer & Selesai') }}</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('bookings.complete', $booking) }}">
                            @csrf
                            @if($booking->vehicle)
                                <div class="p-3 rounded border small mb-3" style="background: #faf9fc;">
                                    <div><strong>{{ __('Kenderaan:') }}</strong> {{ $booking->vehicle->displayName() }}</div>
                                    <div><strong>{{ __('Odometer Semasa:') }}</strong> {{ number_format($booking->vehicle->current_odometer) }} km</div>
                                </div>
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="odometer_start" class="form-label small fw-semibold">{{ __('Odometer Mula (km)') }} {!! $booking->vehicle ? '<span class="text-danger">*</span>' : '' !!}</label>
                                    <input
                                        type="number"
                                        name="odometer_start"
                                        id="odometer_start"
                                        min="0"
                                        value="{{ old('odometer_start', $booking->vehicle?->current_odometer) }}"
                                        class="form-control @error('odometer_start') is-invalid @enderror"
                                        @required($booking->vehicle !== null)
                                    >
                                </div>
                                <div class="col-6">
                                    <label for="odometer_end" class="form-label small fw-semibold">{{ __('Odometer Tamat (km)') }} {!! $booking->vehicle ? '<span class="text-danger">*</span>' : '' !!}</label>
                                    <input
                                        type="number"
                                        name="odometer_end"
                                        id="odometer_end"
                                        min="1"
                                        value="{{ old('odometer_end') }}"
                                        class="form-control @error('odometer_end') is-invalid @enderror"
                                        @required($booking->vehicle !== null)
                                    >
                                </div>
                            </div>

                            <div id="distancePreview" class="alert alert-success py-2 d-none mb-3 border-0">
                                <i class="bi bi-signpost-2 me-1"></i> {{ __('Anggaran Jarak:') }} <strong id="distancePreviewVal">0</strong> km
                            </div>

                            <div class="mb-3">
                                <label for="pilot_remarks" class="form-label small fw-semibold">{{ __('Catatan Pilot / Keadaan Kenderaan') }}</label>
                                <textarea name="pilot_remarks" id="pilot_remarks" rows="3" class="form-control form-control-sm" placeholder="{{ __('Contoh: Perjalanan lancar, minyak diisi penuh...') }}">{{ old('pilot_remarks') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-all me-1"></i> {{ __('Simpan Odometer & Selesai') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Official Documents Card --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title-sm"><i class="bi bi-printer me-2 text-violet"></i>{{ __('Dokumen Rasmi YKN (PDF)') }}</h2>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('bookings.pdf.borang', $booking) }}" target="_blank" class="btn btn-light text-start d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                            <span class="fw-medium">{{ __('Borang Kebenaran Kenderaan') }}</span>
                        </span>
                        <i class="bi bi-download text-muted"></i>
                    </a>

                    @if($booking->canDownloadMemo())
                        <a href="{{ route('bookings.pdf.memo', $booking) }}" target="_blank" class="btn btn-light text-start d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-file-earmark-text text-violet me-2"></i>
                                <span class="fw-medium">{{ __('Memo Kebenaran Memandu') }}</span>
                            </span>
                            <i class="bi bi-download text-muted"></i>
                        </a>
                    @elseif($booking->isSelfDrive())
                        <div class="text-muted p-2 rounded border" style="font-size: 0.76rem; background: #faf9fc;">
                            <i class="bi bi-lock me-1"></i> {{ __('Memo Kebenaran Memandu diaktifkan sebaik sahaja permohonan diluluskan.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startOdo = document.getElementById('odometer_start');
            const endOdo = document.getElementById('odometer_end');
            const preview = document.getElementById('distancePreview');
            const previewVal = document.getElementById('distancePreviewVal');

            function updateDistance() {
                if (!startOdo || !endOdo || !preview) return;
                const s = parseInt(startOdo.value, 10);
                const e = parseInt(endOdo.value, 10);
                if (!isNaN(s) && !isNaN(e) && e > s) {
                    previewVal.textContent = (e - s).toLocaleString();
                    preview.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }
            }

            if (startOdo && endOdo) {
                startOdo.addEventListener('input', updateDistance);
                endOdo.addEventListener('input', updateDistance);
                updateDistance();
            }
        });
    </script>
@endpush
