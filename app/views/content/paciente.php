<div id="modalPaciente"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloPaciente"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="tituloPaciente"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="formPaciente" enctype="multipart/form-data" autocomplete="off">

                <div class="modal-body py-4 px-4">

                    <input type="hidden" id="id_paciente" name="id_paciente">

                    <div class="row g-3">

                        <!-- NOMBRE -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="nombre" name="nombre"
                                    type="text" placeholder=" " maxlength="200" required>
                                <label for="nombre">Nombre completo</label>
                            </div>
                        </div>

                        <!-- FECHA NACIMIENTO -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                    type="date" placeholder=" " required>
                                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                            </div>
                        </div>

                        <!-- SEXO (M, F, O) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <select id="sexo" name="sexo" class="form-control" required>
                                    <option value="" disabled selected>-- Selecciona un género --</option>
                                    <option value="M">Femenino</option>
                                    <option value="F">Masculino</option>
                                    <option value="O">Prefiero no decirlo</option>
                                </select>
                                <label for="sexo">Sexo</label>
                            </div>
                        </div>

                        <!-- TELÉFONO -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="telefono" name="telefono"
                                    type="text" maxlength="9"
                                    placeholder="0000-0000"
                                    pattern="^[0-9]{4}-[0-9]{4}$"
                                    required>
                                <label for="telefono">Teléfono (0000-0000)</label>
                            </div>
                        </div>

                        <!-- DUI (OPCIONAL, VALIDADO EN EL TRIGGER) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="dui" name="dui"
                                    type="text" maxlength="10"
                                    placeholder="00000000-0"
                                    pattern="^[0-9]{8}-[0-9]{1}$">
                                <label for="dui">DUI (00000000-0)</label>
                            </div>
                        </div>

                        <!-- CORREO NUEVO -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="correo" name="correo"
                                    type="email" placeholder="correo@ejemplo.com"
                                    maxlength="200"
                                    required>
                                <label for="correo">Correo electrónico</label>
                            </div>
                        </div>

                        <!-- DIRECCIÓN -->
                        <div class="col-lg-12 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="direccion" name="direccion"
                                    type="text" placeholder=" ">
                                <label for="direccion">Dirección</label>
                            </div>
                        </div>

                        <!-- NOTAS -->
                        <div class="col-lg-12 col-md-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="notas" name="notas"
                                    placeholder=" " style="height:150px"></textarea>
                                <label for="notas">Descripción de alergias</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarPaciente" class="btn btn-success">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>
