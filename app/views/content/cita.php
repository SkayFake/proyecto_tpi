<!-- =========================
     MODAL CITA (AGREGAR / EDITAR)
     ========================== -->
<div id="modalCita"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloCita"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloCita"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" id="formCita" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_cita" name="id_cita">

          <div class="row g-3">

            <!-- DUI del paciente (se usará para resolver el id_paciente en el modelo) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="dui"
                       name="dui"
                       type="text"
                       placeholder="00000000-0"
                       maxlength="10"
                       pattern="^[0-9]{8}-[0-9]{1}$"
                       required>
                <label for="dui">DUI del paciente (00000000-0)</label>
              </div>
            </div>

            <!-- Nombre del paciente (solo lectura, opcional para mostrar al usuario) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="nombre_paciente"
                       name="nombre_paciente"
                       type="text"
                       placeholder=" "
                       readonly>
                <label for="nombre_paciente">Nombre del paciente</label>
              </div>
            </div>

            <!-- Odontólogo (se llena por AJAX desde citaController.php?opcion=listar_odontologos) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="id_odontologo"
                        name="id_odontologo"
                        class="form-select"
                        required>
                  <option value="" disabled selected>-- Selecciona un odontólogo --</option>
                  <!-- opciones generadas por JS -->
                </select>
                <label for="id_odontologo">Odontólogo</label>
              </div>
            </div>

            <!-- Fecha de la cita -->
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="fecha_cita"
                       name="fecha_cita"
                       type="date"
                       placeholder=" "
                       required>
                <label for="fecha_cita">Fecha de la cita</label>
              </div>
            </div>

            <!-- Hora de la cita -->
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="hora_cita"
                       name="hora_cita"
                       type="time"
                       placeholder=" "
                       required>
                <label for="hora_cita">Hora de la cita</label>
              </div>
            </div>

            <!-- Estado de la cita -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="estado"
                        name="estado"
                        class="form-select"
                        required>
                  <option value="" disabled selected>-- Selecciona estado --</option>
                  <option value="programada">Programada</option>
                  <option value="confirmada">Confirmada</option>
                  <option value="atendida">Atendida</option>
                  <option value="cancelada">Cancelada</option>
                </select>
                <label for="estado">Estado de la cita</label>
              </div>
            </div>

            <!-- Motivo / notas de la cita -->
            <div class="col-lg-12 col-md-12">
              <div class="form-floating mb-3">
                <textarea class="form-control"
                          id="motivo"
                          name="motivo"
                          placeholder=" "
                          style="height: 140px;"></textarea>
                <label for="motivo">Motivo / notas de la cita</label>
              </div>
            </div>

          </div><!-- /.row -->

        </div><!-- /.modal-body -->

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarCita" class="btn btn-success">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- =========================
     MODAL CITA VER (LISTADO)
     ========================== -->
<div id="modalCitaVer"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloCitaVer"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloCitaVer">Listado de citas</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" id="formCitaVer" enctype="multipart/form-data" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <div style="padding:10px;">
            <table id="tablaCitas"
                   class="table table-bordered border-primary table-striped nowrap"
                   width="100%"
                   cellspacing="0">
              <thead class="table-primary">
                <tr class="p-3 mb-2 bg-secondary text-white text-center">
                  <th class="text-center">Paciente</th>
                  <th class="text-center">DUI</th>
                  <th class="text-center">Odontólogo</th>
                  <th class="text-center">Fecha</th>
                  <th class="text-center">Hora</th>
                  <th class="text-center">Motivo</th>
                  <th class="text-center">Estado</th>
                  <th class="text-center notexport">Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

        </div>
      </form>

    </div>
  </div>
</div>
