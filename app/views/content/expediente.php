    <div class="container main-container">
        <!-- Card Principal -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-file-medical me-2"></i>
                    Expediente Médico - Sistema de Gestión de Pacientes
                </h4>
            </div>
            <div class="card-body p-4">
                <!-- Botón para abrir modal -->
                <button type="button" class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#modalBuscarPaciente">
                    <i class="fas fa-search me-2"></i>Buscar Expediente
                </button>
            </div>
        </div>

        <!-- Sección de Resultados -->
        <div id="resultadosSection" class="mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Expediente de: <span id="nombrePacienteHeader"></span>
                    </h5>
                    <div>
                        <button type="button" id="btnExportarPDF" class="btn btn-light btn-custom">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </button>
                        <button type="button" id="btnNuevaBusqueda" class="btn btn-outline-light btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Nueva Búsqueda
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Datos del Paciente -->
                    <div class="info-card">
                        <h5><i class="fas fa-id-card me-2"></i>Datos Personales</h5>
                        <div class="row" id="datosPaciente"></div>
                    </div>

                    <!-- Historial de Odontogramas -->
                    <div class="info-card">
                        <h5><i class="fas fa-tooth me-2"></i>Historial de Odontogramas</h5>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover" id="tablaOdontogramas">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tratamientos -->
                    <div class="info-card">
                        <h5><i class="fas fa-procedures me-2"></i>Tratamientos</h5>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover" id="tablaTratamientos">
                                <thead>
                                    <tr>
                                        <th>Tratamiento</th>
                                        <th>Estado</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buscar Paciente -->
    <div id="modalBuscarPaciente"
         class="modal fade"
         data-bs-keyboard="false"
         data-bs-backdrop="static"
         tabindex="-1"
         aria-labelledby="tituloBuscarPaciente"
         role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloBuscarPaciente">
                        <i class="fas fa-search me-2"></i>Buscar Paciente
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formBuscarPaciente" autocomplete="off">
                    <div class="modal-body py-4 px-4">
                        <div class="form-floating mb-3">
                            <input class="form-control" 
                                   id="nombre_paciente1" 
                                   name="nombre_paciente1"
                                   type="text" 
                                   placeholder=" " 
                                   maxlength="100" 
                                   required>
                            <label for="nombre_paciente1">
                                <i class="fas fa-user me-2"></i>Nombre del Paciente
                            </label>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Ingrese el nombre completo del paciente para buscar su expediente.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger btn-custom" type="button" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" id="btnBuscar" class="btn btn-success btn-custom">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>