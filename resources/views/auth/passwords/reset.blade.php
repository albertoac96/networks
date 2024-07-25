<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Shaping Archeology</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="fullscreen-section d-flex flex-column justify-content-center align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-1 order-md-1">
                    <div class="reset-box p-4 bg-dark text-white rounded">
                        <div class="logo mb-3">
                            <img src="{{ asset('path/to/logo.png') }}" alt="logo" class="img-fluid rounded-circle">
                        </div>
                        <h2 class="mb-4">Reset Password</h2>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Confirm Password</label>
                                <input type="password" id="password-confirm" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 order-2 order-md-2 text-center text-md-start">
                    <div class="background-map">
                        <div class="shaping-archeology">
                            <h1 class="display-4 text-white">Shaping Archeology</h1>
                            <a href="{{ route('acercade') }}" class="btn btn-outline-light mt-3">About the project</a>
                        </div>
                        <div class="button-group mt-4">
                            <button class="btn btn-light btn-lg"></button>
                            <button class="btn btn-light btn-lg"></button>
                            <button class="btn btn-light btn-lg"></button>
                            <button class="btn btn-light btn-lg"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    scroll-behavior: smooth;
}

.fullscreen-section {
    height: 100vh;
    background: url('path/to/background-map.jpg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    align-items: center;
}

.login-box, .reset-box {
    max-width: 400px;
    margin: auto;
}

.background-map {
    position: relative;
}

.shaping-archeology h1 {
    margin-top: 0;
}

.button-group button {
    width: 50px;
    height: 50px;
    margin: 0 5px;
    border-radius: 5px;
}

.content-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.other-section {
    max-width: 800px;
    margin: auto;
}

</style>
