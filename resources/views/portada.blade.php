@extends('layouts.app')

@section('content')
<div class="container-sm">
    <h4>Explorando el México Colonial Temprano: Un análisis computacional a gran escala de fuentes históricas del siglo XVI</h4>
    <p>Versión 1 BETA</p>
    <p>Este sitio aloja de manera temporal la base de datos del proyecto <a href="https://decm.arqueodata.com" target="_blank">(decm.arqueodata.com)</a>. Por ahora la versión BETA se utiliza para realizar pruebas y alimentación de 
        datos. Se esta llevando a cabo la gestión para la versión Alfa en los servidores del INAH. La máquina local que alberga este sistema 
        tiene la IP 172.16.4.115 y se encuentra en las oficinas del INAH en Hamburgo 135.
    </p>

    
    <button type="button" class="btn btn-outline-warning">Explora la version Beta del sitio web</button>

    <hr class="my-4">

    


    <hr class="my-4">

    <div class="card" style="width: 100%;">
    <div class="card-body">
    <p><h5><b>Ingresa al sistema de administración</b></h5></p>

       
       <div class="card-body container-md">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Correo</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Contraseña</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        Recordar sesión
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Entrar
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
</div>

</div>
    </div>
@endsection