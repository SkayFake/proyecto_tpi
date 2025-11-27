<!-- ================== MODAL TRATAMIENTO (AGREGAR / EDITAR) ================== -->
<div id="modalTratamiento"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloTratamiento"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloTratamiento"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" id="formTratamiento" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <!-- Hidden IDs -->
          <input type="hidden" id="id_tratamiento" name="id_tratamiento">
          <input type="hidden" id="id_paciente_trat" name="id_paciente">

          <div class="row g-3">

            <!-- CORREO DEL PACIENTE -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="trat_correo_paciente"
                       name="correo_paciente"
                       type="email"
                       placeholder="correo@ejemplo.com"
                       required>
                <label for="trat_correo_paciente">Correo del paciente</label>
              </div>
            </div>

            <!-- NOMBRE DEL PACIENTE (READONLY) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="trat_nombre_paciente"
                       type="text"
                       placeholder=""
                       readonly>
                <label for="trat_nombre_paciente">Nombre del paciente</label>
              </div>
            </div>

            <!-- SELECT ODONTÓLOGO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="trat_id_odontologo"
                        name="id_odontologo"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona un odontólogo --</option>
                </select>
                <label for="trat_id_odontologo">Odontólogo</label>
              </div>
            </div>

            <!-- SELECT SERVICIO / TIPO TRATAMIENTO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="trat_id_servicio"
                        name="id_servicio"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona un servicio --</option>
                </select>
                <label for="trat_id_servicio">Servicio / tratamiento</label>
              </div>
            </div>

            <!-- FECHA INICIO (created_at - SOLO LECTURA) -->
            <div class="col-lg-4 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="trat_fecha_inicio"
                       type="text"
                       placeholder=""
                       readonly>
                <label for="trat_fecha_inicio">Fecha inicio</label>
              </div>
            </div>

            <!-- FECHA FIN DEL TRATAMIENTO -->
            <div class="col-lg-4 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="fecha_fin"
                       name="fecha_fin"
                       type="date">
                <label for="fecha_fin">Fecha fin</label>
              </div>
            </div>

            <!-- ESTADO -->
            <div class="col-lg-4 col-md-6">
              <div class="form-floating mb-3">
                <select id="trat_estado"
                        name="estado"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona estado --</option>
                  <option value="en curso">En curso</option>
                  <option value="finalizado">Finalizado</option>
                  <option value="cancelado">Cancelado</option>
                </select>
                <label for="trat_estado">Estado del tratamiento</label>
              </div>
            </div>

            <!-- NOTAS -->
            <div class="col-lg-12 col-md-12">
              <div class="form-floating mb-3">
                <textarea class="form-control"
                          id="trat_notas"
                          name="notas"
                          style="height: 140px;"></textarea>
                <label for="trat_notas">Notas / detalles del tratamiento</label>
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

<!-- ================== MODAL LISTADO TRATAMIENTOS ================== -->
<div id="modalTratamientoVer"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloTratamientoVer"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloTratamientoVer">Listado de tratamientos</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body py-4 px-4">

        <table id="tablaTratamientos"
               class="table table-bordered border-primary table-striped nowrap"
               width="100%">
          <thead class="table-primary">
            <tr class="text-center">
              <th>Paciente</th>
              <th>Correo</th>
              <th>Odontólogo</th>
              <th>Servicio</th>
              <th>Fecha inicio</th>  <!-- created_at -->
              <th>Fecha fin</th>
              <th>Estado</th>
              <th>Notas</th>
              <th class="notexport">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>

    </div>
  </div>
</div>
