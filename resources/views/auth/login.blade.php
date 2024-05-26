<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  
  <title>Login Form</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
</head>
<body>
  <div class="container p-5">
    <div class="row">
      <div class="col-md-4 offset-md-4">
        <h1>Login RNG</h1>
        <form method="POST" action="{{ route('login') }}">
        @csrf
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email address" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          
            <div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        Recordar sesión
                    </label>
                </div>
            </div>
                       
          <button type="submit" class="btn btn-light btn-block">Login</button>
          @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
        </form>
      </div>
    </div>
  </div>
</body>
</html>

<style>
   .container {
  margin-top: 80px;
  background-color: #00000090;
  max-width: 800px;
}
h1 {
  text-align: center;
}
form {
  width: 100%;
  max-width: 300px;
  margin: 0 auto;
}
.form-group {
  margin-bottom: 10px;
}

body {
  background-image: url("https://www.researchgate.net/publication/2528586/figure/fig3/AS:652236501839874@1532516708726/Relative-neighborhood-graph-for-graph-in-Fig-1.png");
  color: white;
}


</style>