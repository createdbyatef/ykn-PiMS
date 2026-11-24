@extends('layouts.app')

@section('title', 'Pengurusan Kenderaan')
@section('page-title', 'Pengurusan Kenderaan')
@section('page-subtitle', 'Daftar, kemaskini status penyelenggaraan dan pantau bacaan odometer armada HQ.')

@section('content')
    <div class="card mb-4">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Vehicle::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Semua Jenis</option>
                    @foreach(\App\Models\Vehicle::TYPES as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Tapis</button>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-sm btn-light">Reset</a>
            </form>

            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Daftar Kenderaan
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Model &amp; Jenis</th>
                        <th>Kapasiti</th>
                        <th>Odometer Semasa</th>
                        <th>Rekod Perjalanan</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td class="fw-semibold text-violet">{{ $vehicle->plate_number }}</td>
                            <td>
                                <div class="fw-medium">{{ $vehicle->model }}</div>
                                <span class="badge bg-light text-dark border mt-1">{{ $vehicle->type }}</span>
                            </td>
                            <td>{{ $vehicle->capacity }} penumpang</td>
                            <td class="fw-medium">{{ number_format($vehicle->current_odometer) }} km</td>
                            <td class="text-muted" style="font-size: 0.76rem;">
                                <div>{{ $vehicle->active_bookings_count }} tempahan aktif</div>
                                <div>{{ $vehicle->completed_trips_count }} selesai</div>
                            </td>
                            <td>
                                <span class="badge {{ $vehicle->statusBadgeClass() }}">{{ $vehicle->statusLabel() }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('Padam kenderaan {{ $vehicle->plate_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Padam">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state"><i class="bi bi-car-front"></i>Tiada kenderaan didaftarkan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehicles->hasPages())
            <div class="card-footer bg-white py-3">{{ $vehicles->links() }}</div>
        @endif
    </div>
@endsection
