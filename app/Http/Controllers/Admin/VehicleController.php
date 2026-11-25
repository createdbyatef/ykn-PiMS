<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vehicle::withCount([
            'bookings as active_bookings_count' => fn ($q) => $q->where('status', Booking::STATUS_APPROVED)
                ->where('end_datetime', '>=', now()),
            'bookings as completed_trips_count' => fn ($q) => $q->where('status', Booking::STATUS_COMPLETED),
        ])->orderBy('plate_number');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $vehicles = $query->paginate(12)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('admin.vehicles.create');
    }

    public function store(VehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', __('Kenderaan :name telah berjaya didaftarkan.', ['name' => $vehicle->displayName()]));
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', __('Maklumat kenderaan :plate telah dikemaskini.', ['plate' => $vehicle->plate_number]));
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $hasUpcomingApproved = $vehicle->bookings()
            ->where('status', Booking::STATUS_APPROVED)
            ->where('end_datetime', '>=', now())
            ->exists();

        if ($hasUpcomingApproved) {
            return back()->with('error', __('Kenderaan :plate masih mempunyai tempahan aktif yang diluluskan dan tidak boleh dipadam.', ['plate' => $vehicle->plate_number]));
        }

        $label = $vehicle->displayName();
        $vehicle->delete();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', __('Kenderaan :name telah dipadam daripada sistem.', ['name' => $label]));
    }
}
