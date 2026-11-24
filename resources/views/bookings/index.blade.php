@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Semua Tempahan' : 'Tempahan Saya')
@section('page-title', auth()->user()->isAdmin() ? 'Pengurusan Tempahan' : 'Tempahan Saya')
@section('page-subtitle', auth()->user()->isAdmin() ? 'Semak, luluskan atau tolak permohonan serta cetak borang rasmi YKN.' : 'Pantau status permohonan dan muat turun borang atau memo kelulusan anda.')

@section('content')
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('bookings.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-medium mb-1">Carian</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Kod, destinasi, tujuan, pemohon...">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-medium mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\Booking::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-medium mb-1">Jenis Permohonan</label>
                    <select name="request_type" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Booking::REQUEST_TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(request('request_type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="urgent" value="1" id="urgentOnly" @checked(request()->boolean('urgent'))>
                        <label class="form-check-label small fw-medium text-danger" for="urgentOnly">Segera Sahaja</label>
                    </div>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i> Tapis</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="text-muted small">Menunjukkan <strong>{{ $bookings->total() }}</strong> rekod tempahan</span>
            <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Permohonan Baharu
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kod Tempahan</th>
                        @if(auth()->user()->isAdmin())
                            <th>Pemohon</th>
                        @endif
                        <th>Jenis &amp; Destinasi</th>
                        <th>Tarikh &amp; Masa</th>
                        <th>Kenderaan &amp; Pilot</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan / PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="text-nowrap">
                                <a href="{{ route('bookings.show', $booking) }}" class="fw-semibold text-decoration-none">
                                    {{ $booking->booking_code }}
                                </a>
                                @if($booking->is_urgent)
                                    <div class="mt-1"><span class="badge bg-danger-subtle text-danger-emphasis">Segera (&lt; 3 Hari)</span></div>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td>
                                    <div class="fw-medium">{{ $booking->applicant->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->applicant->unit }}</div>
                                </td>
                            @endif
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
                                    <div><i class="bi bi-car-front text-violet me-1"></i><span class="fw-medium">{{ $booking->vehicle->plate_number }}</span></div>
                                @endif
                                @if($booking->pilot)
                                    <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-1"></i>{{ $booking->pilot->name }}</div>
                                @elseif($booking->isSelfDrive())
                                    <div class="text-info-emphasis" style="font-size: 0.75rem;"><i class="bi bi-key me-1"></i>Pandu sendiri</div>
                                @elseif(!$booking->vehicle)
                                    <span class="text-muted">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $booking->statusBadgeClass() }}">{{ $booking->statusLabel() }}</span>
                                @if($booking->totalDistance() !== null)
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ number_format($booking->totalDistance()) }} km</div>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm {{ auth()->user()->isAdmin() && $booking->isPending() ? 'btn-primary' : 'btn-light' }}">
                                    {{ auth()->user()->isAdmin() && $booking->isPending() ? 'Proses' : 'Butiran' }}
                                </a>
                                <a href="{{ route('bookings.pdf.borang', $booking) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Borang Kebenaran Kenderaan (PDF)">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @if($booking->canDownloadMemo())
                                    <a href="{{ route('bookings.pdf.memo', $booking) }}" target="_blank" class="btn btn-sm btn-info" title="Memo Kebenaran Memandu (PDF)">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}">
                                <div class="empty-state">
                                    <i class="bi bi-journal-x"></i>
                                    Tiada rekod tempahan ditemui mengikut tapisan semasa.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
