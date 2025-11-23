<div id="modalOdontologo"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloOdontologo"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloOdontologo">Odontólogo</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="post" id="formOdontologo" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_odontologo" name="id_odontologo">

          <div class="row g-3">
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="nombreOdontologo" name="nombreOdontologo"
                       type="text" placeholder=" " maxlength="100" required>
                <label for="nombreOdontologo">Nombre completo</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="correo" name="correo"
                       type="email" placeholder=" " required>
                <label for="correo">Correo electrónico</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="password" name="password"
                       type="password" placeholder=" " required>
                <label for="password">Contraseña</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="telefonoOdontologo" name="telefonoOdontologo"
                       type="text" placeholder="0000-0000"
                       maxlength="9" pattern="^[0-9]{4}-[0-9]{4}$" required>
                <label for="telefonoOdontologo">Teléfono (0000-0000)</label>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="especialidad" name="especialidad"
                        class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="Ortodoncia">Ortodoncia</option>
                  <option value="Endodoncia">Endodoncia</option>
                  <option value="Periodoncia">Periodoncia</option>
                </select>
                <label for="especialidad">Especialidad</label>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <select id="es_admin" name="es_admin"
                        class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
                <label for="es_admin">¿Es administrador?</label>
              </div>
            </div>
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <select id="estado" name="estado"
                        class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
                <label for="estado">Estado</label>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarOdontologo" class="btn btn-success">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>
