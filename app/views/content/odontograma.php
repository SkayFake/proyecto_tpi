<!-- MODAL CREAR / EDITAR ODONTOGRAMA -->
<div id="modalOdontograma"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloOdontograma"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-3 shadow-lg">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloOdontograma"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" id="formOdontograma" autocomplete="off">
        <div class="modal-body py-4 px-4">

          <input type="hidden" id="id_odontograma" name="id_odontograma">
          <input type="hidden" id="id_paciente" name="id_paciente">
          <input type="hidden" id="borrar_foto" name="borrar_foto">

          <div class="row g-3">
            <!-- Nombre / correo paciente -->
            <div class="col-lg-6 col-md-12">
              <div class="form-floating mb-3">
                <input class="form-control"
                       id="name_paciente"
                       name="name_paciente"
                       type="text"
                       placeholder="Nombre del paciente"
                       required>
                <label for="name_paciente">Nombre del paciente</label>
              </div>
            </div>

            <!-- Foto del odontograma -->
            <div class="col-12">
              <label for="imagen" class="form-label mb-1">
                Foto del odontograma (JPG o PNG, máx. 2 MB)
              </label>
              <div class="d-flex align-items-center gap-3">
                <img id="previewFoto"
                     src=""
                     alt=""
                     class="img-thumbnail d-none"
                     style="max-height:110px; object-fit:cover;">
                <div class="flex-grow-1">
                  <input type="file"
                         id="imagen"
                         name="imagen"
                         accept="image/jpeg,image/png"
                         class="form-control">
                  <button class="btn btn-sm btn-outline-danger mt-2"
                          type="button"
                          id="btnBorrarFoto"
                          disabled>
                    <i class="bi bi-x-circle"></i> Quitar foto
                  </button>
                </div>
              </div>
            </div>

            <!-- Observaciones -->
            <div class="col-12">
              <div class="form-floating mb-3">
                <textarea class="form-control"
                          id="observaciones"
                          name="observaciones"
                          style="height: 140px;"></textarea>
                <label for="observaciones">Observaciones del odontólogo</label>
              </div>
            </div>
          </div><!-- /.row -->

        </div><!-- /.modal-body -->

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- MODAL VER IMAGEN -->
<div id="modalVerImagen"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-3 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa-solid fa-image me-2"></i> Imagen del odontograma
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0 text-center">
        <img id="imagenOdontogramaModal"
             src=""
             alt="Imagen del odontograma"
             class="img-fluid rounded-3 shadow-sm m-3"
             style="max-height:75vh; object-fit:contain;">
      </div>
    </div>
  </div>
</div>

<!-- MODAL DE ODONTOGRAMAS -->
<div id="modalOdontogramaVer"
     class="modal fade"
     data-bs-keyboard="false"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="tituloOdontogramaVer"
     role="dialog">

  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content rounded-3 shadow-lg">

      <div class="modal-header">
        <h4 class="modal-title" id="tituloOdontogramaVer">Listado de odontogramas</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body py-4 px-4">
        <table id="tablaOdontogramas"
               class="table table-bordered border-primary table-striped table-hover align-middle nowrap"
               width="100%">
          <thead class="table-primary">
            <tr class="text-center">
              <th>Paciente</th>
              <th>Odontograma</th>
              <th>Observación médica</th>
              <th class="notexport">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>
