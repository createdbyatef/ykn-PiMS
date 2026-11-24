<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount([
            'bookings',
            'assignedTrips as active_pilot_trips_count' => fn ($q) => $q->where('status', Booking::STATUS_APPROVED)
                ->where('end_datetime', '>=', now()),
            'assignedTrips as completed_pilot_trips_count' => fn ($q) => $q->where('status', Booking::STATUS_COMPLETED),
        ])->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'pilot' THEN 2 ELSE 3 END")
            ->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('search')) {
            $term = '%'.$request->string('search')->trim().'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('unit', 'like', $term)
                    ->orWhere('jawatan', 'like', $term);
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roleCounts = [
            'all' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'pilot' => User::where('role', User::ROLE_PILOT)->count(),
            'staff' => User::where('role', User::ROLE_STAFF)->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} ({$user->roleLabel()}) telah berjaya didaftarkan.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Prevent the currently logged-in admin from deactivating or demoting themselves accidentally
        if ($user->id === $request->user()->id) {
            $data['is_active'] = true;
            $data['role'] = User::ROLE_ADMIN;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Maklumat pengguna {$user->name} telah dikemaskini.");
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak boleh menyahaktifkan akaun anda sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinyahaktifkan';

        return back()->with('success', "Akaun {$user->name} telah {$statusText}.");
    }
}
