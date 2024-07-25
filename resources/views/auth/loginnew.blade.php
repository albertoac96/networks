<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaping Archeology</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="fullscreen-section d-flex flex-column" id="fullscreen">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 order-1 order-lg-1">
                    <div class="login-box p-4 text-white">
                        <div class="logo mb-3">
                            <img src="{{ asset('logo.png') }}" alt="logo" class="img-fluid rounded-circle">
                        </div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" id="email" name="email" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-light w-100">Login</button>
                            <div class="mt-3">
                                <a href="{{ route('password.request') }}" class="text-white">Forgot your password?</a>
                            </div>
                            <a href="{{ route('register') }}" class="btn btn-primary w-100 mt-3">Create an account</a>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 order-2 order-lg-2 text-center text-lg-start">
                    <div class="background-map">
                        <div class="shaping-archeology">
                            <h1 class="display-4 text-white">Shaping Archeology</h1>
                            <a href="/" class="btn btn-outline-light mt-3">About the project</a>
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
    <div class="bg-light p-4">
        <section class="other-section container my-5 p-4 bg-white rounded shadow">
            <h2 class="h4">Welcome to the next section</h2>
            <p>This is where the additional content will go.</p>
        </section>
    </div>
     <div class="bg-light p-4">
        <section class="other-section container my-5 p-4 bg-white rounded shadow">
            <h2 class="h4">Welcome to the next section</h2>
            <p>This is where the additional content will go.</p>
        </section>
    </div>
    <button id="backToTopBtn" class="btn btn-primary position-fixed bottom-0 start-0 m-3" style="display:none;">Top</button>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Obtener el botón
    const backToTopBtn = document.getElementById("backToTopBtn");
 console.log("enjs");
     // Mostrar el botón cuando el usuario haga scroll hacia abajo 20px desde la parte superior de la página
    window.addEventListener('scroll', function () {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            backToTopBtn.style.display = "block";
        } else {
            backToTopBtn.style.display = "none";
        }
    });

    // Cuando el usuario haga clic en el botón, se desplaza hacia la parte superior de la página
    $('#backToTopBtn').click(function () {
        $('html, body').animate({
            scrollTop: $("#fullscreen").offset().top
        }, 'slow');
    });
});

</script>


<style>
body, html {
    height: 100%;
    margin: 0;
     font-family: "Outfit", sans-serif;
      font-size: 18px;
    scroll-behavior: smooth;
    overflow-y: scroll; /* Ensure scrolling is enabled */
}

.fullscreen-section {
    height: 100vh;
    background: url('/images/fondo.jpg') no-repeat center center fixed;
    background-color: #37474F;
    background-size: cover;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-box {
    max-width: 400px;
    margin: auto;
    background: rgba(33, 37, 41, 0.8); /* Usando el color Dark de Bootstrap con transparencia */
    border-radius: 15px; /* Aplica bordes redondeados */
}

.background-map {
    position: relative;
}

.shaping-archeology h1 {
    margin-top: 0;
     font-family: "Outfit", sans-serif;
  font-optical-sizing: auto;
  font-weight: 700;
  font-style: normal;
      font-size: 3rem; /* Ajusta el tamaño de la fuente */
}

/* Tamaños de fuente responsivos */
@media (min-width: 576px) {
    .shaping-archeology h1 {
        font-size: 3rem; /* Ajusta el tamaño de la fuente para pantallas pequeñas */
    }
}

@media (min-width: 768px) {
    .shaping-archeology h1 {
        font-size: 3.5rem; /* Ajusta el tamaño de la fuente para pantallas medianas */
    }
}

@media (min-width: 992px) {
    .shaping-archeology h1 {
        font-size: 6rem; /* Ajusta el tamaño de la fuente para pantallas grandes */
    }
}

@media (min-width: 1200px) {
    .shaping-archeology h1 {
        font-size: 6rem; /* Ajusta el tamaño de la fuente para pantallas extra grandes */
    }
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
}

.other-section {
    max-width: 800px;
    margin: auto;
}

/* Estilos para el botón flotante */
#backToTopBtn {
    display: none;
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 99;
    border: none;
    outline: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    padding: 15px;
    border-radius: 10px;
}

#backToTopBtn:hover {
    background-color: #0056b3;
}

</style>