<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <main>

        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="<?php echo APP_URL; ?>app/views/assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">INICIO</span>
                                </a>
                            </div>

                            <div class="card shadow-lg border-0 rounded-4 text-center mb-3">
                                <div class="card-body">

                                    <div class="pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Inicio de Sesión</h5>
                                        <p class="text-center small">Entre con su email y contraseña</p>
                                    </div>

                                    <form class="row g-3" method="post" id="formLogin" autocomplete="off">
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input class="form-control" id="email" name="email" type="email" placeholder=" " required />
                                                <label for="email">Email</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating">
                                                    <input class="form-control" id="clave" name="clave" type="password" placeholder=" " maxlength="16" minlength="8" required autocomplete="off" />
                                                    <label for="clave">Contraseña</label>
                                                </div>
                                                <div class="input-group-text" id="showPassword">
                                                    <span id="iconPassword" class="ri-eye-fill"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="submit">Entrar</button>
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-toggle="modal" data-bs-target="#modalRegistro">
                                                Registrarse
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>
        </div>
    </main>


    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegistroLabel">Registrar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formRegistro" method="post" autocomplete="off">
                    <div class="modal-body">
                        <div class="mb-3 form-floating">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder=" " maxlength="100" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="mb-3 form-floating">
                            <input type="email" class="form-control" id="reg_email" name="email" placeholder=" " required>
                            <label for="reg_email">Email</label>
                        </div>
                        <div class="mb-3 form-floating">
                            <input type="password" class="form-control" id="reg_clave" name="clave" placeholder=" " minlength="8" maxlength="16" required>
                            <label for="reg_clave">Contraseña</label>
                        </div>
                        <div class="mb-3 form-floating">
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="" selected disabled>Seleccione…</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Empleado">Empleado</option>
                            </select>
                            <label for="rol">Rol</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            <label for="estado">Estado</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.33/dist/sweetalert2.all.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo APP_URL; ?>app/ajax/login.js"></script>

</body>
</html>