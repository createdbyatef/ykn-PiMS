@extends('layouts.app')

@section('title', __('Daftar Kenderaan Baharu'))
@section('page-title', __('Daftar Kenderaan Pejabat Baharu'))
@section('page-subtitle', __('Tambahkan kenderaan rasmi baharu ke dalam senarai kenderaan e-Pilot YKN.'))
@section('skeleton', 'form')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.vehicles.store') }}">
                        @csrf
                        @include('admin.vehicles._form')

                        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light border">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">{{ __('Simpan Kenderaan') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
