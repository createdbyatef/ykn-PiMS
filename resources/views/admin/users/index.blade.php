@extends('layouts.app')

@section('title', __('Pengguna & Pilot HQ'))
@section('page-title', __('Pengguna & Pilot HQ'))
@section('page-subtitle', __('Urus akaun Staf YKN, Pentadbir Unit Pentadbiran dan 4 Pilot HQ.'))
@section('skeleton', 'table')

@section('content')
    <div class="card mb-4">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-primary' : 'btn-light' }}">
                    {{ __('Semua') }} ({{ $roleCounts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'pilot']) }}" class="btn btn-sm {{ request('role') === 'pilot' ? 'btn-info' : 'btn-light' }}">
                    {{ __('Pilot HQ') }} ({{ $roleCounts['pilot'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'staff']) }}" class="btn btn-sm {{ request('role') === 'staff' ? 'btn-primary' : 'btn-light' }}">
                    {{ __('Staf YKN') }} ({{ $roleCounts['staff'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="btn btn-sm {{ request('role') === 'admin' ? 'btn-primary' : 'btn-light' }}">
                    {{ __('Admin') }} ({{ $roleCounts['admin'] }})
                </a>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('Cari nama, emel, unit...') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Cari') }}</button>
                </form>

                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus me-1"></i> {{ __('Tambah Pengguna') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover table-finset mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Nama & Emel') }}</th>
                            <th>{{ __('Peranan') }}</th>
                            <th>{{ __('Jawatan & Unit') }}</th>
                            <th>{{ __('Telefon') }}</th>
                            <th>{{ __('Aktiviti') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $user->roleBadgeClass() }}">{{ $user->roleLabel() }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $user->jawatan }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $user->unit }}</div>
                                </td>
                                <td class="text-nowrap">{{ $user->phone ?? '-' }}</td>
                                <td class="text-muted" style="font-size: 0.76rem;">
                                    @if($user->isPilot())
                                        <div>{{ __(':count tugasan aktif', ['count' => $user->active_pilot_trips_count]) }}</div>
                                        <div>{{ __(':count selesai', ['count' => $user->completed_pilot_trips_count]) }}</div>
                                    @else
                                        <div>{{ __(':count permohonan', ['count' => $user->bookings_count]) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle"><span class="status-dot bg-success"></span>{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ __('Tidak aktif') }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-pencil me-1"></i>{{ __('Edit') }}
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $user->is_active ? __('Nyahaktifkan') : __('Aktifkan') }}">
                                                <i class="bi {{ $user->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state"><i class="bi bi-people"></i>{{ __('Tiada pengguna ditemui.') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white py-3">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
