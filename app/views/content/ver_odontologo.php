<div id="modalOdontologoVer"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloOdontologoVer"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloOdontologoVer"></h4>
                <h5 class="modal-title">Listado de odontologos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            

            <form method="post" id="formOdontologoVer" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body py-4 px-4">

                    <div style="padding:10px;">
                        <table id="tablaOdontologos" class="table table-bordered border-primary table-striped nowrap table-bordered" width="100%" cellspacing="0">
                            <thead class="table-primary">
                                <tr class="p-3 mb-2 bg-secondary text-white text-center">
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Correo</th>
                                    <th class="text-center">Teléfono</th>
                                    <th class="text-center">Especialidad</th>
                                    <th class="text-center">Administrador</th>
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