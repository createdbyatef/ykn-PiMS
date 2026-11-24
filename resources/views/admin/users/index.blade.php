@extends('layouts.app')

@section('title', 'Pengguna & Pilot HQ')
@section('page-title', 'Pengguna & Pilot HQ')
@section('page-subtitle', 'Urus akaun Staf YKN, Pentadbir Unit Pentadbiran dan 4 Pilot HQ.')

@section('content')
    <div class="card mb-4">
        <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-primary' : 'btn-light' }}">
                    Semua ({{ $roleCounts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'pilot']) }}" class="btn btn-sm {{ request('role') === 'pilot' ? 'btn-info' : 'btn-light' }}">
                    Pilot HQ ({{ $roleCounts['pilot'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'staff']) }}" class="btn btn-sm {{ request('role') === 'staff' ? 'btn-primary' : 'btn-light' }}">
                    Staf YKN ({{ $roleCounts['staff'] }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="btn btn-sm {{ request('role') === 'admin' ? 'btn-primary' : 'btn-light' }}">
                    Admin ({{ $roleCounts['admin'] }})
                </a>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama, emel, unit...">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Cari</button>
                </form>

                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama &amp; Emel</th>
                        <th>Peranan</th>
                        <th>Jawatan &amp; Unit</th>
                        <th>Telefon</th>
                        <th>Aktiviti</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan</th>
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
                                    <div>{{ $user->active_pilot_trips_count }} tugasan aktif</div>
                                    <div>{{ $user->completed_pilot_trips_count }} selesai</div>
                                @else
                                    <div>{{ $user->bookings_count }} permohonan</div>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle"><span class="status-dot bg-success"></span>Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Tidak aktif</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $user->is_active ? 'Nyahaktifkan' : 'Aktifkan' }}">
                                            <i class="bi {{ $user->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state"><i class="bi bi-people"></i>Tiada pengguna ditemui.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white py-3">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
