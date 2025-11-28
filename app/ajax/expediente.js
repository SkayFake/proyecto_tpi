const CTRL_EXPEDIENTE = "app/controllers/expedienteController.php";;

$(document).ready(function () {

  /* ==========================================================
        VARIABLES GLOBALES
  ========================================================== */
  let pacienteActual = null;
  const modalBuscar = new bootstrap.Modal(document.getElementById("modalBuscarPaciente"));

  /* ==========================================================
        LIMPIAR ERRORES
  ========================================================== */
  function limpiarErroresFormulario() {
    $('#nombre_paciente1').removeClass("is-invalid");
  }

  /* ==========================================================
        VALIDAR FORMULARIO
  ========================================================== */
  function validarFormulario() {
    limpiarErroresFormulario();

    const nombrePaciente = $("#nombre_paciente1").val().trim();

    if (nombrePaciente === "") {
      $("#nombre_paciente1").addClass("is-invalid");
      Swal.fire("Campo requerido", "Debe ingresar el nombre del paciente", "warning");
      return false;
    }

    return true;
  }

  /* ==========================================================
        BUSCAR EXPEDIENTE
  ========================================================== */
  $("#formBuscarPaciente").on("submit", async function (e) {
    e.preventDefault();

    if (!validarFormulario()) return;

    const nombrePaciente = $("#nombre_paciente1").val().trim();

    // Mostrar loading
    Swal.fire({
      title: 'Buscando...',
      text: 'Consultando expediente del paciente',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    $.ajax({
      url: `${CTRL_EXPEDIENTE}?action=buscar`,
      type: "post",
      data: JSON.stringify({ nombre_paciente: nombrePaciente }),
      contentType: "application/json",
      dataType: "json",
      success: function (r) {
        if (r.success) {
          pacienteActual = r.paciente;
          mostrarResultados(r);
          
          modalBuscar.hide();
          $("#formBuscarPaciente")[0].reset();
          limpiarErroresFormulario();
          
          Swal.close();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'No encontrado',
            text: r.message || 'No se encontró el expediente del paciente',
            confirmButtonColor: '#667eea'
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Ocurrió un error al buscar el expediente',
          confirmButtonColor: '#667eea'
        });
      }
    });
  });

  /* ==========================================================
        MOSTRAR RESULTADOS
  ========================================================== */
  function mostrarResultados(data) {
    if (!data || !data.paciente) {
      console.error('Datos del paciente no disponibles');
      return;
    }

    // Mostrar sección de resultados
    $("#resultadosSection").show();

    // Actualizar nombre en header
    $("#nombrePacienteHeader").text(data.paciente.nombre || 'Sin nombre');

    // Mostrar datos del paciente
    mostrarDatosPaciente(data.paciente);

    // Mostrar odontogramas
    mostrarOdontogramas(data.odontogramas || []);

    // Mostrar tratamientos
    mostrarTratamientos(data.tratamientos || []);

    // Scroll suave a resultados
    $('html, body').animate({
      scrollTop: $("#resultadosSection").offset().top - 20
    }, 500);
  }

  /* ==========================================================
        MOSTRAR DATOS DEL PACIENTE
  ========================================================== */
  function mostrarDatosPaciente(paciente) {
    const campos = [
      { label: 'DUI', value: paciente.dui },
      { label: 'Fecha de Nacimiento', value: paciente.fecha_nacimiento },
      { label: 'Sexo', value: paciente.sexo },
      { label: 'Teléfono', value: paciente.telefono },
      { label: 'Correo', value: paciente.correo },
      { label: 'Dirección', value: paciente.direccion }
    ];

    let html = '';
    campos.forEach(campo => {
      html += `
        <div class="col-lg-4 col-md-6 mb-3">
          <div class="info-item">
            <strong>${campo.label}:</strong><br>
            ${campo.value || 'N/A'}
          </div>
        </div>
      `;
    });

    if (paciente.notas) {
      html += `
        <div class="col-12">
          <div class="info-item">
            <strong>Notas del Paciente:</strong><br>
            ${paciente.notas.replace(/\n/g, '<br>')}
          </div>
        </div>
      `;
    }

    $("#datosPaciente").html(html);
  }

  /* ==========================================================
        MOSTRAR ODONTOGRAMAS
  ========================================================== */
  function mostrarOdontogramas(odontogramas) {
    const $tbody = $("#tablaOdontogramas tbody");

    if (!odontogramas || odontogramas.length === 0) {
      $tbody.html(`
        <tr>
          <td colspan="2" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x mb-2"></i><br>
            No hay odontogramas registrados
          </td>
        </tr>
      `);
      return;
    }

    let html = '';
    odontogramas.forEach(odon => {
      html += `
        <tr>
          <td>${odon.odontograma_fecha || 'N/A'}</td>
          <td>${odon.odontograma_observaciones || 'Sin observaciones'}</td>
        </tr>
      `;
    });

    $tbody.html(html);
  }

  /* ==========================================================
        MOSTRAR TRATAMIENTOS
  ========================================================== */
  function mostrarTratamientos(tratamientos) {
    const $tbody = $("#tablaTratamientos tbody");

    if (!tratamientos || tratamientos.length === 0) {
      $tbody.html(`
        <tr>
          <td colspan="3" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x mb-2"></i><br>
            No hay tratamientos registrados
          </td>
        </tr>
      `);
      return;
    }

    let html = '';
    tratamientos.forEach(trat => {
      const estadoClass = trat.tratamiento_estado === 'completado' ? 'success' : 'warning';
      html += `
        <tr>
          <td>${trat.tratamiento_nombre || 'N/A'}</td>
          <td>
            <span class="badge bg-${estadoClass} badge-estado">
              ${trat.tratamiento_estado || 'Pendiente'}
            </span>
          </td>
          <td>${trat.tratamiento_notas || 'Sin notas'}</td>
        </tr>
      `;
    });

    $tbody.html(html);
  }

  /* ==========================================================
        EXPORTAR PDF
  ========================================================== */
  $("#btnExportarPDF").on("click", function () {
    if (!pacienteActual) {
      Swal.fire({
        icon: 'warning',
        title: 'Advertencia',
        text: 'No hay un expediente cargado para exportar',
        confirmButtonColor: '#667eea'
      });
      return;
    }

    window.location.href = `${CTRL_EXPEDIENTE}?action=exportar&nombre=${encodeURIComponent(pacienteActual.nombre)}`;
  });

  /* ==========================================================
        NUEVA BÚSQUEDA
  ========================================================== */
  $("#btnNuevaBusqueda").on("click", function () {
    $("#resultadosSection").hide();
    pacienteActual = null;
    $('html, body').animate({ scrollTop: 0 }, 500);
  });

  /* ==========================================================
        OCULTAR SECCIÓN DE RESULTADOS AL INICIO
  ========================================================== */
  $("#resultadosSection").hide();

});