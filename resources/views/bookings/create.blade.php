@extends('layouts.app')

@section('title', 'Permohonan Baharu')
@section('page-title', 'Permohonan Kenderaan & Pilot')
@section('page-subtitle', 'Permohonan hendaklah dikemukakan selewat-lewatnya 3 hari sebelum tarikh penggunaan.')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title-sm">Maklumat Permohonan Rasmi</h2>
                </div>
                <div class="card-body p-4">
                    {{-- 3-Day Notice Rule Dynamic Bootstrap Warning Alert --}}
                    <div id="urgentWarningAlert" class="alert alert-warning d-none align-items-start gap-2 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <div class="fw-semibold">Amaran Peraturan Notis 3 Hari YKN (Permohonan Segera)</div>
                            <div class="mt-1">
                                Tarikh mula perjalanan yang dipilih adalah <strong>kurang daripada 3 hari</strong> daripada tarikh hari ini.
                                Permohonan akan ditandakan secara automatik sebagai <span class="badge bg-danger-subtle text-danger-emphasis">Segera</span>
                                dan tertakluk kepada ketersediaan kenderaan/Pilot serta kelulusan Unit Pentadbiran.
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                        @csrf

                        {{-- Request Type Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">1. Jenis Permohonan <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="request_type" id="type_vehicle_pilot" value="vehicle_and_pilot"
                                        @checked(old('request_type', 'vehicle_and_pilot') === 'vehicle_and_pilot')>
                                    <label class="type-card p-3 d-block h-100" for="type_vehicle_pilot">
                                        <span class="type-icon bg-info-subtle text-info-emphasis mb-2">
                                            <i class="bi bi-car-front-fill"></i>
                                        </span>
                                        <div class="fw-semibold small">Kenderaan + Pilot</div>
                                        <div class="text-muted mt-1" style="font-size: 0.74rem;">Kenderaan rasmi YKN dipandu oleh Pilot HQ.</div>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="request_type" id="type_vehicle_only" value="vehicle_only"
                                        @checked(old('request_type') === 'vehicle_only')>
                                    <label class="type-card p-3 d-block h-100" for="type_vehicle_only">
                                        <span class="type-icon bg-primary-subtle text-primary mb-2">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                        <div class="fw-semibold small">Kenderaan Sahaja</div>
                                        <div class="text-muted mt-1" style="font-size: 0.74rem;">Pandu sendiri. Memo Kebenaran Memandu akan dijana.</div>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="request_type" id="type_pilot_only" value="pilot_only"
                                        @checked(old('request_type') === 'pilot_only')>
                                    <label class="type-card p-3 d-block h-100" for="type_pilot_only">
                                        <span class="type-icon bg-warning-subtle text-warning-emphasis mb-2">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </span>
                                        <div class="fw-semibold small">Pilot Sahaja</div>
                                        <div class="text-muted mt-1" style="font-size: 0.74rem;">Khidmat Pilot HQ tanpa kenderaan pejabat.</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Conditional Driver License Field for vehicle_only --}}
                        <div id="driverLicenseWrapper" class="mb-4 p-3 rounded-3 bg-orchid-subtle border border-primary-subtle d-none">
                            <label for="driver_license_no" class="form-label fw-semibold text-primary-emphasis mb-1">
                                <i class="bi bi-card-heading me-1"></i> No. Lesen Memandu Pemohon <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="driver_license_no"
                                id="driver_license_no"
                                value="{{ old('driver_license_no') }}"
                                class="form-control @error('driver_license_no') is-invalid @enderror"
                                placeholder="Contoh: 910412-08-5321 (Kelas D)"
                            >
                            <div class="form-text text-primary-emphasis">
                                Wajib diisi bagi permohonan <strong>Kenderaan Sahaja</strong> untuk penjanaan rasmi <em>Memo Kebenaran Memandu Kenderaan Pejabat</em>.
                            </div>
                            @error('driver_license_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date & Time Range --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="start_datetime" class="form-label fw-semibold">2. Tarikh &amp; Masa Mula <span class="text-danger">*</span></label>
                                <input
                                    type="datetime-local"
                                    name="start_datetime"
                                    id="start_datetime"
                                    value="{{ old('start_datetime', now()->addDays(4)->setTime(8, 30)->format('Y-m-d\TH:i')) }}"
                                    class="form-control @error('start_datetime') is-invalid @enderror"
                                    required
                                >
                                @error('start_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="end_datetime" class="form-label fw-semibold">3. Tarikh &amp; Masa Tamat <span class="text-danger">*</span></label>
                                <input
                                    type="datetime-local"
                                    name="end_datetime"
                                    id="end_datetime"
                                    value="{{ old('end_datetime', now()->addDays(4)->setTime(17, 30)->format('Y-m-d\TH:i')) }}"
                                    class="form-control @error('end_datetime') is-invalid @enderror"
                                    required
                                >
                                @error('end_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Destination --}}
                        <div class="mb-3">
                            <label for="destination" class="form-label fw-semibold">4. Destinasi Perjalanan <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="destination"
                                id="destination"
                                value="{{ old('destination') }}"
                                class="form-control @error('destination') is-invalid @enderror"
                                placeholder="Contoh: Anjung Kasih YKN, Hospital Serdang, Selangor"
                                required
                            >
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Official Purpose --}}
                        <div class="mb-4">
                            <label for="purpose" class="form-label fw-semibold">5. Tujuan Rasmi Perjalanan <span class="text-danger">*</span></label>
                            <textarea
                                name="purpose"
                                id="purpose"
                                rows="4"
                                class="form-control @error('purpose') is-invalid @enderror"
                                placeholder="Nyatakan mesyuarat, program kebajikan atau tugasan rasmi..."
                                required
                            >{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('bookings.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send me-1"></i> Hantar Permohonan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Sidebar Info --}}
        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title-sm">Maklumat Pemohon</h3>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted fw-normal">Nama</dt>
                        <dd class="col-8 fw-semibold">{{ auth()->user()->name }}</dd>
                        <dt class="col-4 text-muted fw-normal">Jawatan</dt>
                        <dd class="col-8">{{ auth()->user()->jawatan }}</dd>
                        <dt class="col-4 text-muted fw-normal">Unit</dt>
                        <dd class="col-8">{{ auth()->user()->unit }}</dd>
                        <dt class="col-4 text-muted fw-normal">Telefon</dt>
                        <dd class="col-8 mb-0">{{ auth()->user()->phone ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card bg-orchid-subtle border-primary-subtle">
                <div class="card-body">
                    <h3 class="card-title-sm text-primary-emphasis mb-2"><i class="bi bi-info-circle me-1"></i> Garis Panduan YKN</h3>
                    <ul class="small mb-3 ps-3" style="color: #42365c;">
                        <li class="mb-2">Permohonan hendaklah dikemukakan <strong>selewat-lewatnya 3 hari</strong> sebelum tarikh perjalanan.</li>
                        <li class="mb-2">Semak <a href="{{ route('calendar.index') }}" class="fw-semibold">Kalendar Ketersediaan</a> terlebih dahulu bagi mengelakkan pertindihan.</li>
                        <li class="mb-2">Bagi permohonan <strong>Kenderaan Sahaja</strong>, Memo Kebenaran Memandu akan dijana secara automatik setelah diluluskan.</li>
                        <li>Ketua Pegawai Eksekutif berhak menarik balik kelulusan pada bila-bila masa.</li>
                    </ul>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <strong>Armada Aktif:</strong> {{ $vehicles->count() }} kenderaan tersedia &middot; {{ $pilots->count() }} Pilot HQ aktif.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="request_type"]');
            const licenseWrapper = document.getElementById('driverLicenseWrapper');
            const licenseInput = document.getElementById('driver_license_no');
            const startInput = document.getElementById('start_datetime');
            const endInput = document.getElementById('end_datetime');
            const urgentAlert = document.getElementById('urgentWarningAlert');

            function toggleLicenseField() {
                const selected = document.querySelector('input[name="request_type"]:checked')?.value;
                const isSelfDrive = selected === 'vehicle_only';
                licenseWrapper.classList.toggle('d-none', !isSelfDrive);
                licenseInput.required = isSelfDrive;
            }

            function checkThreeDayNotice() {
                if (!startInput.value) {
                    urgentAlert.classList.add('d-none');
                    urgentAlert.classList.remove('d-flex');
                    return;
                }
                const selectedStart = new Date(startInput.value);
                const threeDaysFromNow = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
                const isUrgent = selectedStart < threeDaysFromNow;

                urgentAlert.classList.toggle('d-none', !isUrgent);
                urgentAlert.classList.toggle('d-flex', isUrgent);
            }

            radios.forEach(r => r.addEventListener('change', toggleLicenseField));
            startInput.addEventListener('change', function () {
                checkThreeDayNotice();
                if (endInput.value && endInput.value <= startInput.value) {
                    const startDate = new Date(startInput.value);
                    startDate.setHours(startDate.getHours() + 2);
                    const pad = n => String(n).padStart(2, '0');
                    endInput.value = `${startDate.getFullYear()}-${pad(startDate.getMonth() + 1)}-${pad(startDate.getDate())}T${pad(startDate.getHours())}:${pad(startDate.getMinutes())}`;
                }
            });

            toggleLicenseField();
            checkThreeDayNotice();
        });
    </script>
@endpush
