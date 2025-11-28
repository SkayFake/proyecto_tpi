const CTRL_PAGO = "app/controllers/pagoController.php";

$(document).ready(function () {

  //==========================================================
  // INICIALIZAR MODAL DE BOOTSTRAP 5
  //==========================================================
  const modalPago = new bootstrap.Modal(document.getElementById('modalPago'));

  //==========================================================
  // LIMPIAR ERRORES
  //==========================================================
  function limpiarErrores() {
    $('#correo_pacientepa, #nombre_tratamientota, #metodo_pago, #monto_pago, #estado_pago')
      .removeClass("is-invalid");
  }

  //==========================================================
  // VALIDAR FORMULARIO
  //==========================================================
  async function validarFormulario(isEdit) {

    console.log("%c[Validación] Iniciando", "color: purple");
    limpiarErrores();

    const correo = $("#correo_pacientepa").val().trim();
    const idTratamiento = $("#nombre_tratamientota").val(); // ← CAMBIADO
    const metodo = $("#metodo_pago").val();
    const monto = parseFloat($("#monto_pago").val()) || 0;
    const estado = $("#estado_pago").val();
    let idPaciente = $("#id_paciente").val();

    console.log("[Validación] Datos obtenidos:", {
      correo, idTratamiento, metodo, monto, estado, idPaciente
    });

    // Correo
    if (!correo || !correo.includes("@")) {
      $("#correo_pacientepa").addClass("is-invalid");
      Swal.fire("Correo requerido", "Debe ingresar un correo válido", "warning");
      return false;
    }

    // Tratamiento
    if (!idTratamiento) {
      console.log("[Validación] Tratamiento vacío");
      $("#nombre_tratamientota").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un tratamiento", "warning");
      return false;
    }

    // Método
    if (!metodo) {
      $("#metodo_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un método de pago", "warning");
      return false;
    }

    // Monto
    if (monto <= 0) {
      $("#monto_pago").addClass("is-invalid");
      Swal.fire("Monto inválido", "Debe ser mayor a 0", "warning");
      return false;
    }

    // Estado
    if (!estado) {
      $("#estado_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar el estado del pago", "warning");
      return false;
    }

    //==========================================================
    // BUSCAR PACIENTE SI NO EXISTE (por correo)
    //==========================================================
    if (!idPaciente) {
      console.log("[Validación] Buscando paciente por correo:", correo);

      const r = await $.ajax({
        url: `${CTRL_PAGO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
        dataType: "json"
      }).catch(() => ({ status: "error" }));

      console.log("[Validación] Respuesta del backend:", r);

      if (r.status !== "success") {
        $("#correo_pacientepa").addClass("is-invalid");
        Swal.fire("Paciente no encontrado", "Revise el correo ingresado", "error");
        return false;
      }

      idPaciente = r.data.id_paciente;

      $("#id_paciente").val(idPaciente);
      $("#nombre_pacientepa").val(r.data.nombre_pacientepa);
    }

    //==========================================================
    // VERIFICACIÓN FINAL
    //==========================================================
    console.log("[Validación] FINAL → id_paciente:", $("#id_paciente").val());
    console.log("[Validación] FINAL → id_tratamiento:", $("#nombre_tratamientota").val()); // ← CAMBIADO

    if (!$("#id_paciente").val()) {
      console.log("[ERROR] id_paciente sigue vacío antes de enviar");
      Swal.fire("Error", "Debe seleccionar un paciente.", "error");
      return false;
    }

    console.log("%c[Validación] COMPLETADA OK", "color: green; font-weight:bold");
    return true;
  }

  //==========================================================
  // CARGAR TRATAMIENTOS
  //==========================================================
  function cargarTratamientosSelect(selectedId = null) {
    const $sel = $("#nombre_tratamientota");

    console.log("[Tratamiento] Cargando tratamientos...");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_PAGO}?opcion=listar_tratamientos`,
      type: "get",
      dataType: "json",
      success: function (r) {
        console.log("[Tratamiento] Respuesta:", r);

        $sel.empty();

        if (r.status !== "success") {
          $sel.append('<option value="" disabled selected>Error al cargar</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un tratamiento --</option>');

        r.data.forEach(t => {
          $sel.append(`
            <option value="${t.id_tratamiento}">
              ${t.nombre_tratamiento}
            </option>
          `);
        });

        if (selectedId) {
          console.log("[Tratamiento] Seleccionando:", selectedId);
          $sel.val(String(selectedId)); // ← SIMPLIFICADO
        }
      }
    });
  }

  // ← ELIMINADO: Ya no necesitas sincronizar con campo hidden

  //==========================================================
  // BUSCAR PACIENTE EN BLUR
  //==========================================================
  $("#correo_pacientepa").on("blur", function () {
    const correo = $(this).val().trim();
    console.log("[Paciente] BLUR →", correo);
    if (!correo) return;

    $.getJSON(
      `${CTRL_PAGO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
      function (r) {
        console.log("[Paciente] Respuesta buscar:", r);

        if (r.status === "success") {
          $("#nombre_pacientepa").val(r.data.nombre_pacientepa);
          $("#id_paciente").val(r.data.id_paciente);
        } else {
          $("#nombre_pacientepa").val("");
          $("#id_paciente").val("");
          Swal.fire("Paciente no encontrado", r.message, "error");
        }
      }
    );
  });

  //==========================================================
  // NUEVO PAGO
  //==========================================================
  $("#btnNuevoPago").on("click", function () {
    console.log("[Nuevo Pago] Abriendo modal");

    $("#tituloPago").text("Nuevo Pago");
    $("#formPago")[0].reset();

    $("#id_pago").val("");
    $("#id_paciente").val("");

    limpiarErrores();

    $("#correo_pacientepa").prop("readonly", false);
    $("#nombre_pacientepa").prop("readonly", true);

    cargarTratamientosSelect();

    modalPago.show();
  });

  //==========================================================
// SUBMIT FORM
//==========================================================
$("#formPago").on("submit", async function (e) {
  e.preventDefault();

  console.log("[Submit] Iniciando guardado...");

  const isEdit = $("#id_pago").val() !== "";
  const opcion = isEdit ? "actualizar" : "agregar";

  if (!(await validarFormulario(isEdit))) {
    console.log("[Submit] ❌ Validación fallida");
    return;
  }

  console.log("[Submit] Enviando datos...");

  const fd = new FormData(this);
  
  // FORZAR el id_paciente si no se incluyó
  const idPaciente = $("#id_paciente").val();
  if (idPaciente) {
    fd.set('id_paciente', idPaciente);
  }

  // DEBUG: Ver qué se está enviando
  console.log("[Submit] FormData content:");
  for (let pair of fd.entries()) {
    console.log(pair[0] + ': ' + pair[1]);
  }

  $.ajax({
    url: `${CTRL_PAGO}?opcion=${opcion}`,
    type: "post",
    data: fd,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (r) {
      console.log("[Submit] Respuesta guardar:", r);
      if (r.status === "success") {
        Swal.fire("Éxito", r.message, "success");
        modalPago.hide();
        tablaPagos.ajax.reload(null, false);
      } else {
        Swal.fire("Error", r.message, "error");
      }
    },
    error: function(xhr, status, error) {
      console.error("[Submit] Error AJAX:", error);
      Swal.fire("Error", "Hubo un problema al guardar", "error");
    }
  });
});

  //==========================================================
  // EDITAR PAGO
  //==========================================================
  $("#tablaPagos").on("click", ".btn-editar-pago", function () {

    const id = $(this).data("id");
    console.log("[Editar Pago] ID:", id);

    $.getJSON(`${CTRL_PAGO}?opcion=obtener&id=${id}`, function (r) {

      if (r.status !== "success") {
        Swal.fire("Error", "No se encontró el pago", "error");
        return;
      }

      const p = r.data;
      console.log("[Editar Pago] Datos:", p);

      $("#tituloPago").text("Editar Pago");

      $("#id_pago").val(p.id_pago);
      $("#id_paciente").val(p.id_paciente);

      $("#correo_pacientepa").val(p.correo_pacientepa).prop("readonly", true);
      $("#nombre_pacientepa").val(p.nombre_pacientepa).prop("readonly", true);

      $("#metodo_pago").val(p.metodo_pago);
      $("#monto_pago").val(p.monto);
      $("#estado_pago").val(p.estado_pago);
      $("#referencia_pago").val(p.referencia || "");

      cargarTratamientosSelect(p.id_tratamiento);

      modalPago.show();
    });

  });

  //==========================================================
  // ELIMINAR
  //==========================================================
  $("#tablaPagos").on("click", ".btn-eliminar-pago", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "¿Eliminar pago?",
      text: "No se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Eliminar"
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_PAGO}?opcion=eliminar`,
        { id },
        function (r) {
          if (r.status === "success") {
            Swal.fire("Eliminado", r.message, "success");
            tablaPagos.ajax.reload(null, false);
          } else {
            Swal.fire("Error", r.message, "error");
          }
        },
        "json"
      );
    });
  });

  //==========================================================
  // INICIALIZAR DATATABLE
  //==========================================================
  const tablaPagos = $("#tablaPagos").DataTable({
    ajax: {
      url: `${CTRL_PAGO}?opcion=listar`,
      dataSrc: function(json) {
        console.log("Datos tabla recibidos:", json);
        if (json.status === "success") {
          return json.data || [];
        }
        return [];
      },
      error: function(xhr, error, thrown) {
        console.error("Error cargando tabla:", error);
      }
    },
    columns: [
      { data: "nombre_pacientepa", defaultContent: "N/A" },
      { data: "nombre_tratamientota", defaultContent: "N/A" },
      { data: "metodo_pago" },
      { 
        data: "monto",
        render: function(data) {
          return "$" + parseFloat(data).toFixed(2);
        }
      },
      { data: "estado_pago" },
      { data: "referencia", defaultContent: "Sin Datos" },
      { data: 'created_at' },
      
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    },
    responsive: true,
    order: [[0, 'desc']]
  });

}); // ← Este cierra el $(document).ready

  

