<div id="modalPacienteVer"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloPacienteVer"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="tituloPacienteVer">Listado de pacientes</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="formPacienteVer" enctype="multipart/form-data" autocomplete="off">

                <div class="modal-body py-4 px-4">

                    <div style="padding:10px;">
                        <table id="tablaPacientes" 
                               class="table table-bordered border-primary table-striped nowrap" 
                               width="100%" cellspacing="0">

                            <thead class="table-primary">
                                <tr class="bg-secondary text-white text-center">

                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Fecha nacimiento</th>
                                    <th class="text-center">Correo</th> 
                                    <th class="text-center">Teléfono</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">DUI</th>
                                    <th class="text-center">Género</th>
                                    <th class="text-center">Alergias</th>
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
