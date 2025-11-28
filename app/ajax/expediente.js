const CTRL_EXPEDIENTE = "app/controllers/expedienteController.php";


$(document).ready(function () {

    let pacienteActual = null;
    const modalBuscar = new bootstrap.Modal(document.getElementById("modalBuscarPaciente"));

    /* ==========================================================
        LIMPIAR ERRORES
    ========================================================== */
    function limpiarErrores() {
        $("#nombre_paciente1").removeClass("is-invalid");
    }

    /* ==========================================================
        VALIDAR FORMULARIO
    ========================================================== */
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

    /* ==========================================================
        BUSCAR EXPEDIENTE (AJAX)
    ========================================================== */
    $("#formBuscarPaciente").on("submit", function (e) {
        e.preventDefault();

        if (!validarFormulario()) return;

        const nombre = $("#nombre_paciente1").val().trim();
        console.log("Buscando expediente para:", nombre);

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
    console.log("ERROR AJAX => ", xhr.responseText);
    Swal.fire("Error", xhr.responseText, "error");
}

        });
    });

    /* ==========================================================
        MOSTRAR RESULTADOS
    ========================================================== */
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

    /* ==========================================================
        MOSTRAR DATOS PERSONALES
    ========================================================== */
    function mostrarDatosPaciente(p) {

        const campos = [
            { label: "DUI", value: p.dui },
            { label: "Fecha de Nacimiento", value: p.fecha_nacimiento },
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

    /* ==========================================================
        MOSTRAR ODONTOGRAMAS
    ========================================================== */
    function mostrarOdontogramas(lista) {
        const $tbody = $("#tablaOdontogramas tbody");

        if (lista.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="2" class="text-center text-muted py-3">
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
                    <td>${o.odontograma_fecha}</td>
                    <td>${o.odontograma_observaciones || "Sin observaciones"}</td>
                </tr>`;
        });

        $tbody.html(html);
    }

    /* ==========================================================
        MOSTRAR TRATAMIENTOS
    ========================================================== */
    function mostrarTratamientos(lista) {
        const $tbody = $("#tablaTratamientos tbody");

        if (lista.length === 0) {
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

    /* ==========================================================
        EXPORTAR PDF
    ========================================================== */
    $("#btnExportarPDF").on("click", function () {

        if (!pacienteActual) {
            Swal.fire("Advertencia", "Primero busque un expediente", "warning");
            return;
        }

        location.href = `${CTRL_EXPEDIENTE}?action=exportar&nombre=${encodeURIComponent(pacienteActual.nombre)}`;
    });

    /* ==========================================================
        NUEVA BÚSQUEDA
    ========================================================== */
    $("#btnNuevaBusqueda").on("click", function () {
        $("#resultadosSection").slideUp();
        pacienteActual = null;
        $('html, body').animate({ scrollTop: 0 }, 300);
    });

});

