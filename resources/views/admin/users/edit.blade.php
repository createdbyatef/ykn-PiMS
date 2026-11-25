@extends('layouts.app')

@section('title', __('Edit Pengguna :name', ['name' => $user->name]))
@section('page-title', __('Kemaskini Pengguna: :name', ['name' => $user->name]))
@section('page-subtitle', __('Kemaskini maklumat peranan, jawatan, unit atau status akaun.'))
@section('skeleton', 'form')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.users._form')

                        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">{{ __('Kemaskini Pengguna') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
