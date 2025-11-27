const CTRL_PAGO = "app/controllers/pagoController.php";
const CTRL_PACIENTE = "app/controllers/pacienteController.php";
const CTRL_TRATAMIENTO = "app/controllers/tratamientoController.php";

$(document).ready(function () {

  /* ==========================================================
        LIMPIAR ERRORES
  ========================================================== */
  function limpiarErroresFormularioPago() {
    $('#nombre_paciente, #correo_paciente, #nombre_tratamiento, #fecha_pago, #metodo_pago, #monto, #estado_pago')
      .removeClass("is-invalid");
  }

  /* ==========================================================
        VALIDAR FORMULARIO DE PAGO
  ========================================================== */
  async function validarFormularioPago() {
    limpiarErroresFormularioPago();

    const nombrePaciente    = $("#nombre_paciente").val().trim();
    const correoPaciente    = $("#correo_paciente").val().trim();
    const nombreTratamiento = $("#nombre_tratamiento").val().trim();
    const fechaPago         = $("#fecha_pago").val();
    const metodoPago        = $("#metodo_pago").val();
    const monto             = parseFloat($("#monto").val());
    const estadoPago        = $("#estado_pago").val();

    if (!nombrePaciente) {
      $("#nombre_paciente").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un paciente", "warning");
      return false;
    }

    if (!correoPaciente || !validateEmail(correoPaciente)) {
      $("#correo_paciente").addClass("is-invalid");
      Swal.fire("Correo inválido", "Debe ingresar un correo válido", "warning");
      return false;
    }

    if (!nombreTratamiento) {
      $("#nombre_tratamiento").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un tratamiento", "warning");
      return false;
    }

    if (!fechaPago) {
      $("#fecha_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe ingresar la fecha de pago", "warning");
      return false;
    }

    if (!metodoPago) {
      $("#metodo_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar el método de pago", "warning");
      return false;
    }

    if (isNaN(monto) || monto <= 0) {
      $("#monto").addClass("is-invalid");
      Swal.fire("Monto inválido", "Debe ingresar un monto mayor a 0", "warning");
      return false;
    }

    if (!estadoPago) {
      $("#estado_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar el estado del pago", "warning");
      return false;
    }

    return true;
  }

  /* ==========================================================
        VALIDAR CORREO
  ========================================================== */
  function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  /* ==========================================================
        CARGAR PACIENTES Y TRATAMIENTOS
  ========================================================== */
  function cargarPacientesSelect() {
    const $sel = $("#nombre_paciente");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_PACIENTE}?opcion=listar`,
      type: "get",
      dataType: "json",
      success: function (r) {
        $sel.empty();

        if (!r || r.status !== "success") {
          $sel.append('<option value="" disabled selected>Sin pacientes disponibles</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un paciente --</option>');

        r.data.forEach(p => {
          $sel.append(`<option value="${p.id_paciente}">${p.nombre}</option>`);
        });
      }
    });
  }

  function cargarTratamientosSelect(idPaciente) {
    const $sel = $("#nombre_tratamiento");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_TRATAMIENTO}?opcion=listar`,
      type: "get",
      data: { id_paciente: idPaciente },
      dataType: "json",
      success: function (r) {
        $sel.empty();

        if (!r || r.status !== "success") {
          $sel.append('<option value="" disabled selected>Sin tratamientos disponibles</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un tratamiento --</option>');

        r.data.forEach(t => {
          $sel.append(`<option value="${t.id_tratamiento}">${t.nombre}</option>`);
        });
      }
    });
  }

  /* ==========================================================
        SELECCION DE PACIENTE
  ========================================================== */
  $("#nombre_paciente").on("change", function () {
    const idPaciente = $(this).val();
    if (idPaciente) {
      cargarTratamientosSelect(idPaciente);
    } else {
      $("#nombre_tratamiento").prop("disabled", true);
    }
  });

  /* ==========================================================
        SELECCION DE METODO DE PAGO
  ========================================================== */
  $("#metodo_pago").on("change", function () {
    const metodo = $(this).val();
    if (metodo === "tarjeta") {
      $('#modalTarjeta').modal('show');
    } else {
      $('#modalTarjeta').modal('hide');
    }
  });

  /* ==========================================================
        GUARDAR PAGO
  ========================================================== */
  $("#formPago").on("submit", async function (e) {
    e.preventDefault();

    if (!(await validarFormularioPago())) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_PAGO}?opcion=guardar`,
      type: "post",
      data: fd,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        if (r.status === "success") {
          Swal.fire("Éxito", r.message, "success");
          $('#modalPago').modal('hide');
        } else {
          Swal.fire("Error", r.message, "error");
        }
      }
    });
  });

  /* ==========================================================
        INICIALIZAR MODAL
  ========================================================== */
  $("#modalPago").on("shown.bs.modal", function () {
    cargarPacientesSelect();
  });

});
