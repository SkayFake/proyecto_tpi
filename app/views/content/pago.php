<div id="modalPago"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloPago"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloPago">Detalles de Pago</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" id="formPago" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <!-- Hidden IDs -->
          <input type="hidden" id="id_pago" name="id_pago">
          <input type="hidden" id="id_paciente" name="id_paciente">
          <input type="hidden" id="id_tratamiento" name="id_tratamiento">

          <div class="row g-3">

            <!-- CORREO DEL PACIENTE - SIN READONLY INICIAL -->
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

            <!-- NOMBRE DEL TRATAMIENTO (SELECT) -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select class="form-select"
                        id="nombre_tratamiento"
                        name="nombre_tratamiento"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona un tratamiento --</option>
                </select>
                <label for="nombre_tratamiento">Nombre del tratamiento</label>
              </div>
            </div>

            <!-- METODO DE PAGO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="metodo_pago"
                        name="metodo_pago"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona un método de pago --</option>
                  <option value="efectivo">Efectivo</option>
                  <option value="tarjeta">Tarjeta</option>
                  <option value="transferencia">Transferencia</option>
                </select>
                <label for="metodo_pago">Método de pago</label>
              </div>
            </div>

            <!-- MONTO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="monto_pago"
                       name="monto"
                       type="number"
                       step="0.01"
                       min="0.01"
                       placeholder="Monto"
                       required>
                <label for="monto_pago">Monto</label>
              </div>
            </div>

            <!-- ESTADO DEL PAGO -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="estado_pago"
                        name="estado_pago"
                        class="form-select"
                        required>
                  <option value="" disabled hidden selected>-- Selecciona el estado del pago --</option>
                  <option value="pagado">Pagado</option>
                  <option value="no pagado">No pagado</option>
                </select>
                <label for="estado_pago">Estado del pago</label>
              </div>
            </div>

            <!-- REFERENCIA -->
            <div class="col-lg-12 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="referencia_pago"
                       name="referencia"
                       type="text"
                       placeholder="Referencia del pago">
                <label for="referencia_pago">Referencia (opcional)</label>
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



<div id="modalPagoVer"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloPagoVer"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloPagoVer">Listado de Pagos</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body py-4 px-4">

        <table id="tablaPagos"
               class="table table-bordered border-primary table-striped nowrap"
               width="100%">
          <thead class="table-primary">
            <tr class="text-center">
              <th>Paciente</th>
              <th>Tratamiento</th>
              <th>Método de pago</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Referencia</th>
              <th>Fecha</th>
              <th class="notexport">Acciones</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

      </div>

    </div>
  </div>
</div>