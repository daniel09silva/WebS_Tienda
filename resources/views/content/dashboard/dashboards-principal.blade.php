@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Principal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="d-flex align-items-start row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">¡Bienvenido al sistema, {{ Auth::user()->name }}! 🎉</h5>
                        <p class="mb-6">Inicia sesión correctamente y desde aquí puedes acceder a los módulos disponibles en el menú lateral.</p>

                        <a href="{{ route('pages-account-settings-account') }}" class="btn btn-sm btn-outline-primary">Ver mi perfil</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-6">
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="175" alt="Bienvenida" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
