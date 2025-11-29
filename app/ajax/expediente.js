const CTRL_EXPEDIENTE = "app/controllers/expedienteController.php";

$(document).ready(function () {

    console.log("➡️ JS Expediente cargado correctamente");

    let pacienteActual = null;
    const modalBuscar = new bootstrap.Modal(document.getElementById("modalBuscarPaciente"));

    function limpiarErrores() {
        $("#nombre_paciente1").removeClass("is-invalid");
    }

    function validarFormulario() {
        limpiarErrores();

        const nombre = $("#nombre_paciente1").val().trim();

        if (nombre === "") {
            $("#nombre_paciente1").addClass("is-invalid");
            Swal.fire("Campo requerido", "Debe ingresar un nombre de paciente", "warning");
            return false;
        }

        return true;
    }

    function formatearFecha(fecha) {

        if (!fecha) return "N/A";

        // convierte a objeto fecha válido
        const d = new Date(fecha.replace(" ", "T"));

        const meses = [
            "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
        ];

        return `${d.getDate()} de ${meses[d.getMonth()]} de ${d.getFullYear()}`;
    }

    $("#formBuscarPaciente").on("submit", function (e) {
        e.preventDefault();

        if (!validarFormulario()) return;

        const nombre = $("#nombre_paciente1").val().trim();

        Swal.fire({
            title: "Buscando...",
            text: "Consultando expediente del paciente",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `${CTRL_EXPEDIENTE}?action=buscar`,
            method: "POST",
            data: JSON.stringify({ nombre_paciente: nombre }),
            contentType: "application/json",
            dataType: "json",

            success: function (r) {

                if (!r.success) {
                    Swal.fire("No encontrado", r.message, "error");
                    return;
                }

                pacienteActual = r.paciente;

                mostrarResultados(r);

                modalBuscar.hide();
                $("#formBuscarPaciente")[0].reset();
                limpiarErrores();

                Swal.close();
            },

            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error en la búsqueda",
                    text: "Hubo un problema al consultar el expediente"
                });
            }
        });
    });

    function mostrarResultados({ paciente, odontogramas, tratamientos }) {
        $("#resultadosSection").slideDown();

        $("#nombrePacienteHeader").text(paciente.nombre);

        mostrarDatosPaciente(paciente);
        mostrarOdontogramas(odontogramas);
        mostrarTratamientos(tratamientos);

        $('html, body').animate({
            scrollTop: $("#resultadosSection").offset().top - 15
        }, 400);
    }

    function mostrarDatosPaciente(p) {

        const campos = [
            { label: "DUI", value: p.dui },
            {
                label: "Fecha de Nacimiento",
                value: formatearFecha(p.fecha_nacimiento)
            },
            { label: "Sexo", value: p.sexo },
            { label: "Teléfono", value: p.telefono },
            { label: "Correo", value: p.correo },
            { label: "Dirección", value: p.direccion }
        ];

        let html = "";
        campos.forEach(c => {
            html += `
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="info-item">
                        <strong>${c.label}:</strong><br>
                        ${c.value || "N/A"}
                    </div>
                </div>`;
        });

        if (p.notas) {
            html += `
                <div class="col-12">
                    <div class="info-item">
                        <strong>Notas:</strong><br>
                        ${p.notas.replace(/\n/g, "<br>")}
                    </div>
                </div>`;
        }

        $("#datosPaciente").html(html);
    }



    function mostrarOdontogramas(lista) {
        const $tbody = $("#tablaOdontogramasex tbody");

        if (!lista || lista.length === 0) {
            $tbody.html(`
            <tr>
                <td colspan="3" class="text-center text-muted py-3">
                    <i class="fas fa-inbox fa-2x"></i><br>
                    No hay odontogramas registrados
                </td>
            </tr>`);
            return;
        }

        let html = "";
        lista.forEach(o => {

            html += `
            <tr>
                <td>${formatearFecha(o.odontograma_fecha)}</td>

                <td>${o.odontograma_observaciones || "Sin observaciones"}</td>
               
                <td class="text-center">
                    ${o.odontograma_imagen
                    ? `
                            <img src="${o.odontograma_imagen}"
                                style="width:70px; cursor:pointer; border-radius:4px;"
                                onclick="verOdontograma('${o.odontograma_imagen}')">
                          `
                    : `<span class="text-muted">Sin imagen</span>`
                }
                </td>
            </tr>`;
        });

        $tbody.html(html);
    }

    window.verOdontograma = function (src) {
        Swal.fire({
            title: "Imagen del Odontograma",
            imageUrl: src,
            imageWidth: 500,
            imageAlt: "Imagen del odontograma"
        });
    };

    window.verOdontograma = function (src) {
        Swal.fire({
            title: "Odontograma",
            imageUrl: src,
            imageWidth: 450,
            imageAlt: "Imagen del odontograma"
        });
    };

    function mostrarTratamientos(lista) {
        const $tbody = $("#tablaTratamientosex tbody");

        if (!lista || lista.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x"></i><br>
                        No hay tratamientos registrados
                    </td>
                </tr>`);
            return;
        }

        let html = "";
        lista.forEach(t => {
            html += `
                <tr>
                    <td>${t.tratamiento_nombre}</td>
                    <td>
                        <span class="badge bg-${t.tratamiento_estado === "completado" ? "success" : "warning"}">
                            ${t.tratamiento_estado}
                        </span>
                    </td>
                    <td>${t.tratamiento_notas || "Sin notas"}</td>
                </tr>`;
        });

        $tbody.html(html);
    }

    $("#btnExportarPDF").on("click", function () {

        if (!pacienteActual) {
            Swal.fire("Advertencia", "Primero busque un expediente", "warning");
            return;
        }

        location.href = `${CTRL_EXPEDIENTE}?action=exportar&nombre=${encodeURIComponent(pacienteActual.nombre)}`;
    });

    $("#btnNuevaBusqueda").on("click", function () {

        $("#resultadosSection").slideUp();
        pacienteActual = null;

        $('html, body').animate({ scrollTop: 0 }, 300);
    });

});


