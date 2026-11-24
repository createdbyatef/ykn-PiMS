@extends('layouts.app')

@section('title', 'Daftar Kenderaan Baharu')
@section('page-title', 'Daftar Kenderaan Pejabat Baharu')
@section('page-subtitle', 'Tambahkan kenderaan rasmi baharu ke dalam armada e-Pilot YKN.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.vehicles.store') }}">
                        @csrf
                        @include('admin.vehicles._form')

                        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">Simpan Kenderaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
