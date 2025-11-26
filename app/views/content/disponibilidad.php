<!-- ============================================================
     MODAL DISPONIBILIDAD (AGREGAR / EDITAR)
=============================================================== -->
<div id="modalDisponibilidad"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloDisponibilidad"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloDisponibilidad"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" id="formDisponibilidad" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_disponibilidad" name="id_disponibilidad">

          <div class="row g-3">

            <!-- ODONTÓLOGO -->
            <div class="col-lg-6">
              <div class="form-floating mb-3">
                <select id="id_odontologo_disp" name="id_odontologo" class="form-select" required>
                  <option value="" disabled hidden selected>-- Selecciona un odontólogo --</option>
                </select>
                <label for="id_odontologo_disp">Odontólogo</label>
              </div>
            </div>

            <!-- FECHA INICIO -->
            <div class="col-lg-3">
              <div class="form-floating mb-3">
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                <label for="fecha_inicio">Fecha inicio</label>
              </div>
            </div>

            <!-- FECHA FIN -->
            <div class="col-lg-3">
              <div class="form-floating mb-3">
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                <label for="fecha_fin">Fecha fin</label>
              </div>
            </div>

            <!-- HORA INICIO -->
            <div class="col-lg-3">
              <div class="form-floating mb-3">
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                <label for="hora_inicio">Hora inicio</label>
              </div>
            </div>

            <!-- HORA FIN -->
            <div class="col-lg-3">
              <div class="form-floating mb-3">
                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                <label for="hora_fin">Hora fin</label>
              </div>
            </div>

            

            <!-- NOTAS -->
            <div class="col-lg-12">
              <div class="form-floating mb-3">
                <textarea class="form-control" id="notas" name="notas" style="height:120px"></textarea>
                <label for="notas">Notas</label>
              </div>
            </div>

          </div> <!-- row -->
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" type="submit">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>
<!-- ============================================================
     MODAL VER DISPONIBILIDAD
=============================================================== -->
<div id="modalDisponibilidadVer"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloDisponibilidadVer"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloDisponibilidadVer">Listado de disponibilidad</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body py-4 px-4">

        <table id="tablaDisponibilidad"
               class="table table-bordered border-primary table-striped nowrap"
               width="100%">
          <thead class="table-primary text-center">
            <tr>
              <th>Odontólogo</th>
              <th>Fecha inicio</th>
              <th>Fecha fin</th>
              <th>Hora inicio</th>
              <th>Hora fin</th>
              
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
