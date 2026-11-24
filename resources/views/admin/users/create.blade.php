@extends('layouts.app')

@section('title', 'Tambah Pengguna Baharu')
@section('page-title', 'Daftar Pengguna / Pilot HQ Baharu')
@section('page-subtitle', 'Cipta akaun baharu bagi Staf YKN, Admin Unit Pentadbiran atau Pilot HQ.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        @include('admin.users._form')

                        <div class="d-flex justify-content-between pt-4 mt-4 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4">Simpan Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
