@extends('layouts.app')

@section('title', __('Pengurusan Kenderaan'))
@section('page-title', __('Pengurusan Kenderaan'))
@section('page-subtitle', __('Daftar, kemaskini status penyelenggaraan dan pantau bacaan odometer kenderaan pejabat.'))
@section('skeleton', 'table')

@section('content')
    <div class="card mb-4">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="width: auto;">
                    <option value="">{{ __('Semua Status') }}</option>
                    @foreach(\App\Models\Vehicle::translatedStatuses() as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="type" class="form-select form-select-sm" style="width: auto;">
                    <option value="">{{ __('Semua Jenis') }}</option>
                    @foreach(\App\Models\Vehicle::TYPES as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Tapis') }}</button>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-sm btn-light">{{ __('Reset') }}</a>
            </form>

            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Daftar Kenderaan') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover table-finset mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('No. Pendaftaran') }}</th>
                            <th>{{ __('Model & Jenis') }}</th>
                            <th>{{ __('Kapasiti') }}</th>
                            <th>{{ __('Odometer Semasa') }}</th>
                            <th>{{ __('Rekod Perjalanan') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Tindakan') }}</th>
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
                                <td>{{ __(':count penumpang', ['count' => $vehicle->capacity]) }}</td>
                                <td class="fw-medium">{{ number_format($vehicle->current_odometer) }} km</td>
                                <td class="text-muted" style="font-size: 0.76rem;">
                                    <div>{{ __(':count tempahan aktif', ['count' => $vehicle->active_bookings_count]) }}</div>
                                    <div>{{ __(':count selesai', ['count' => $vehicle->completed_trips_count]) }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $vehicle->statusBadgeClass() }}">{{ $vehicle->statusLabel() }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm(@json(__('Padam kenderaan :plate?', ['plate' => $vehicle->plate_number])));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Padam') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state"><i class="bi bi-car-front"></i>{{ __('Tiada kenderaan didaftarkan.') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vehicles->hasPages())
            <div class="card-footer bg-white py-3">{{ $vehicles->links() }}</div>
        @endif
    </div>
@endsection
