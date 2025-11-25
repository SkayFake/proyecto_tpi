<div id="modalServicio"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloServicio"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="tituloServicio"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="formServicio" autocomplete="off">

                <div class="modal-body py-4 px-4">

                    <input type="hidden" id="id_servicio" name="id_servicio">

                    <div class="row g-3">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="codigo" name="codigo"
                                       type="text" placeholder=" " maxlength="50">
                                <label for="codigo">Código del servicio</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="nombre_servicio" name="nombre_servicio"
                                    type="text" placeholder=" " maxlength="200" required>
                                <label for="nombre_servicio">Nombre del servicio</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="precio_base" name="precio_base"
                                    type="number" step="0.01" placeholder=" " required>
                                <label for="precio_base">Precio base ($)</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="duracion_dias" name="duracion_dias"
                                    type="number" min="1" placeholder=" ">
                                <label for="duracion_dias">Duración (en días, opcional)</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"
                                    type="datetime-local" placeholder=" ">
                                <label for="fecha_vencimiento">Fecha y hora de vencimiento</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-floating mb-3">
                                <select id="activo" name="activo" class="form-control" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                <label for="activo">Estado</label>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="descripcion" name="descripcion"
                                    placeholder=" " style="height:150px"></textarea>
                                <label for="descripcion">Descripción / Información adicional</label>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarServicio" class="btn btn-success">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>


<div id="modalServiciosVer"
    class="modal fade"
    data-bs-keyboard="false"
    data-bs-backdrop="static"
    tabindex="-1"
    aria-labelledby="tituloServiciosVer"
    role="dialog">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="tituloServiciosVer"></h4>
                <h5 class="modal-title">Listado de Servicios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="post" id="formServiciosVer" autocomplete="off">

                <div class="modal-body py-4 px-4">

                    <div style="padding:10px;">
                        <table id="tablaServicios" 
                               class="table table-bordered border-primary table-striped nowrap table-bordered" 
                               width="100%" cellspacing="0">

                            <thead class="table-primary">
                                <tr class="p-3 mb-2 bg-secondary text-white text-center">
                                    <th class="text-center">Codigo</th>
                                    <th class="text-center">Servicio</th>
                                    <th class="text-center">Precio</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Fecha creación</th>
                                    <th class="text-center">Fecha vencimiento</th>
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


<script>

document.addEventListener("DOMContentLoaded", () => {
    const duracion = document.getElementById("duracion_dias");
    const fechaVenc = document.getElementById("fecha_vencimiento");

    duracion.addEventListener("input", function () {
        let dias = parseInt(this.value);
        if (!isNaN(dias)) {
            let fecha = new Date();
            fecha.setDate(fecha.getDate() + dias);
            fechaVenc.value = fecha.toISOString().slice(0,16);
        }
    });
});
</script>
