@extends('layouts.app')

@section('title', __('Edit Kenderaan :plate', ['plate' => $vehicle->plate_number]))
@section('page-title', __('Kemaskini Kenderaan :plate', ['plate' => $vehicle->plate_number]))
@section('page-subtitle', __('Kemaskini butiran kenderaan, status penyelenggaraan atau bacaan odometer.'))
@section('skeleton', 'form')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.vehicles._form')

                        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light border">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">{{ __('Kemaskini Kenderaan') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
