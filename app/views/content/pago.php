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
        <h4 class="modal-title" id="tituloPago">Registrar Pago</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="post" id="formPago" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_pago" name="id_pago">
          <input type="hidden" id="id_paciente" name="id_paciente">
          <input type="hidden" id="id_tratamiento" name="id_tratamiento">

          <div class="row g-3">
            
            <!-- Paciente -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="nombre_paciente" name="nombre_paciente" class="form-select" required>
                  <option value="" disabled selected></option>
                </select>
                <label for="nombre_paciente">Paciente</label>
              </div>
            </div>

            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select type="email" id="correo_paciente" name="correo_paciente" class="form-select" required>
                  <option value="" disabled selected></option>
                </select>
                <label for="correo_paciente">Correo</label>
              </div>
            </div>

            <!-- Tratamiento -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="nombre_tratamiento" name="nombre_tratamiento" class="form-select" required disabled>
                  <option value="" disabled selected>Seleccione un paciente primero</option>
                </select>
                <label for="nombre_tratamiento">Tratamiento</label>
              </div>
            </div>

            <!-- Información del Tratamiento -->
            <div class="col-12" id="infoTratamiento" style="display: none;">
              <div class="alert alert-info mb-3">
                <div class="row">
                  <div class="col-md-4">
                    <strong>Total Tratamiento:</strong>
                    <span id="totalTratamiento">$0.00</span>
                  </div>
                  <div class="col-md-4">
                    <strong>Total Pagado:</strong>
                    <span id="totalPagado">$0.00</span>
                  </div>
                  <div class="col-md-4">
                    <strong>Saldo Pendiente:</strong>
                    <span id="saldoPendiente" class="text-danger">$0.00</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fecha de Pago -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="fecha_pago" name="fecha_pago"
                       type="date" required>
                <label for="fecha_pago">Fecha de Pago</label>
              </div>
            </div>

            <!-- Método de Pago -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="metodo_pago" name="metodo_pago" class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="efectivo">Efectivo</option>
                  <option value="tarjeta">Tarjeta</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="cheque">Cheque</option>
                </select>
                <label for="metodo_pago">Método de Pago</label>
              </div>
            </div>

            <!-- Monto -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="monto" name="monto"
                       type="number" step="0.01" min="0.01" placeholder=" " required>
                <label for="monto">Monto ($)</label>
              </div>
            </div>

            <!-- Estado del Pago -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <select id="estado_pago" name="estado_pago" class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="pendiente">Pendiente</option>
                  <option value="completado">Completado</option>
                  <option value="rechazado">Rechazado</option>
                  <option value="reembolsado">Reembolsado</option>
                </select>
                <label for="estado_pago">Estado del Pago</label>
              </div>
            </div>

            <!-- Referencia -->
            <div class="col-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="referencia" name="referencia"
                       type="text" placeholder=" " maxlength="100">
                <label for="referencia">Referencia / Número de Transacción (Opcional)</label>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnGuardarPago" class="btn btn-success">
            <i class="bi bi-save"></i> Guardar Pago
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- MODAL DATOS DE TARJETA -->
<div id="modalTarjeta"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloTarjeta"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="tituloTarjeta">
          <i class="bi bi-credit-card-2-front"></i> Datos de la Tarjeta
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formTarjeta" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            <small>Los datos de la tarjeta son solo para registro interno. No se almacenarán datos sensibles completos.</small>
          </div>

          <div class="row g-3">
            
            <!-- Número de Tarjeta -->
            <div class="col-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="numero_tarjeta" name="numero_tarjeta"
                       type="text" placeholder=" " maxlength="19" 
                       pattern="[0-9\s]{13,19}" required>
                <label for="numero_tarjeta">Número de Tarjeta</label>
                <small class="text-muted">Formato: 1234 5678 9012 3456</small>
              </div>
            </div>

            <!-- Nombre en la Tarjeta -->
            <div class="col-12">
              <div class="form-floating mb-3">
                <input class="form-control" id="nombre_tarjeta" name="nombre_tarjeta"
                       type="text" placeholder=" " maxlength="100" 
                       style="text-transform: uppercase;" required>
                <label for="nombre_tarjeta">Nombre del Titular</label>
              </div>
            </div>

            <!-- Fecha de Vencimiento -->
            <div class="col-lg-6 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"
                       type="text" placeholder=" " maxlength="5" 
                       pattern="(0[1-9]|1[0-2])\/[0-9]{2}" required>
                <label for="fecha_vencimiento">Vencimiento (MM/AA)</label>
                <small class="text-muted">Ejemplo: 12/25</small>
              </div>
            </div>

            <!-- CVV -->
            <div class="col-lg-6 col-md-6">
              <div class="form-floating mb-3">
                <input class="form-control" id="cvv" name="cvv"
                       type="password" placeholder=" " maxlength="4" 
                       pattern="[0-9]{3,4}" required>
                <label for="cvv">CVV</label>
                <small class="text-muted">3 o 4 dígitos</small>
              </div>
            </div>

            <!-- Tipo de Tarjeta -->
            <div class="col-12">
              <div class="form-floating mb-3">
                <select id="tipo_tarjeta" name="tipo_tarjeta" class="form-select" required>
                  <option value="" disabled selected></option>
                  <option value="visa">Visa</option>
                  <option value="mastercard">Mastercard</option>
                  <option value="amex">American Express</option>
                  <option value="discover">Discover</option>
                </select>
                <label for="tipo_tarjeta">Tipo de Tarjeta</label>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cancelar
          </button>
          <button type="submit" id="btnConfirmarTarjeta" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Confirmar
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- MODAL VER PAGOS -->
<div id="modalPagoVer"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloPagoVer"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h4 class="modal-title" id="tituloPagoVer">
                        <i class="bi bi-cash-coin"></i> Listado de Pagos
                    </h4>
                    <p class="mb-0 small">Historial completo de transacciones</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="formPagoVer" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body py-4 px-4">

                    <div style="padding:10px;">
                        <table id="tablaPagos" class="table table-bordered border-primary table-striped table-hover nowrap" width="100%" cellspacing="0">
                            <thead class="table-primary">
                                <tr class="text-center">
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Paciente</th>
                                    <th class="text-center">Tratamiento</th>
                                    <th class="text-center">Método</th>
                                    <th class="text-center">Monto</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Referencia</th>
                                    <th class="text-center notexport">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="5" class="text-end">TOTAL:</td>
                                    <td class="text-center" id="totalTabla">$0.00</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-success" id="btnExportarExcel">
                        <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                    </button>
                    <button type="button" class="btn btn-danger" id="btnExportarPDF">
                        <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

