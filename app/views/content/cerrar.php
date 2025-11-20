<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <main>

        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
            <div class="card shadow-lg border-0 rounded-4 text-center p-4" style="max-width: 420px; width: 100%;">

                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-box-arrow-right fs-2"></i>
                    </div>
                </div>

                <h4 class="fw-semibold mb-2">Confirmar cierre de sesión</h4>
                <hr>
                <p class="fs-5 text-muted">
                    <strong class="text-dark"><?php echo $_SESSION['usuario']['nombre']; ?></strong>, actualmente tienes una sesión activa.
                    ¿Deseas cerrar sesión para ingresar con otro usuario?
                </p>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                    <a href="inicio" class="btn btn-secondary px-4">
                        Cancelar
                    </a>
                    <button onclick="confirmCierre();" class="btn btn-primary bg-gradient px-4">
                        Cerrar sesión
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.33/dist/sweetalert2.all.min.js"></script>
    <script src="<?php echo APP_URL; ?>app/ajax/cerrar.js"></script>
</body>

</html>