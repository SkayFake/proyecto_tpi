<!-- ============================================================
     MODAL CITA (AGREGAR / EDITAR)
=============================================================== -->
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
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" id="formCita" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <!-- Hidden IDs -->
          <input type="hidden" id="id_cita" name="id_cita">
          <input type="hidden" id="id_paciente" name="id_paciente">

          <div class="row g-3">

            <!-- CORREO DEL PACIENTE -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="correo_paciente"
                       name="correo_paciente"
                       type="email"
                       placeholder="correo@ejemplo.com"
                       required>
                <label for="correo_paciente">Correo del paciente</label>
              </div>
            </div>

            <!-- NOMBRE DEL PACIENTE (READONLY) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="nombre_paciente"
                       name="nombre_paciente"
                       type="text"
                       placeholder=""
                       readonly>
                <label for="nombre_paciente">Nombre del paciente</label>
              </div>
            </div>

            <!-- SELECT ODONTÓLOGO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="cita_id_odontologo"
                        name="id_odontologo"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona un odontólogo --</option>
                </select>
                <label for="cita_id_odontologo">Odontólogo</label>
              </div>
            </div>

            <!-- FECHA -->
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="fecha_cita"
                       name="fecha_cita"
                       type="date"
                       required>
                <label for="fecha_cita">Fecha de la cita</label>
              </div>
            </div>

            <!-- HORA -->
            <div class="col-lg-3 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="hora_cita"
                       name="hora_cita"
                       type="time"
                       required>
                <label for="hora_cita">Hora de la cita</label>
              </div>
            </div>

            <!-- ESTADO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="cita_estado"
                        name="estado"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona estado --</option>
                  <option value="programada">Programada</option>
                  <option value="confirmada">Confirmada</option>
                  <option value="atendida">Atendida</option>
                  <option value="cancelada">Cancelada</option>
                </select>
                <label for="cita_estado">Estado de la cita</label>
              </div>
            </div>

            <!-- MOTIVO -->
            <div class="col-lg-12 col-md-12">
              <div class="form-floating mb-3">
                <textarea class="form-control"
                          id="motivo"
                          name="motivo"
                          style="height: 140px;"></textarea>
                <label for="motivo">Motivo / notas de la cita</label>
              </div>
            </div>

          </div><!-- /.row -->
        </div><!-- /.modal-body -->

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- ============================================================
     MODAL VER CITAS
=============================================================== -->
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
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body py-4 px-4">

        <table id="tablaCitas"
               class="table table-bordered border-primary table-striped nowrap"
               width="100%">
          <thead class="table-primary">
            <tr class="text-center">
              <th>Paciente</th>
              <th>Correo</th>
              <th>Odontólogo</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Motivo</th>
              <th>Estado</th>
              <th class="notexport">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>

    </div>
  </div>
</div>
