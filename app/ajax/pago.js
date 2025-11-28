const CTRL_PAGO = "app/controllers/pagoController.php";

$(document).ready(function () {

  function limpiarErroresFormularioPago() {
    $('#correo_paciente, #nombre_tratamiento, #metodo_pago, #monto_pago, #estado_pago')
      .removeClass("is-invalid");
  }

  
  async function validarFormularioPago(isEdit) {
    limpiarErroresFormularioPago();

    const correo        = $("#correo_paciente").val().trim();
    const idTratamiento = $("#id_tratamiento").val();
    const metodoPago    = $("#metodo_pago").val();
    const monto         = parseFloat($("#monto_pago").val()) || 0;
    const estadoPago    = $("#estado_pago").val();
    let   idPaciente    = $("#id_paciente").val();

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


    

    // Tratamiento
    if (!idTratamiento) {
      $("#nombre_tratamiento").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un tratamiento", "warning");
      return false;
    }

    // Método de pago
    if (!metodoPago) {
      $("#metodo_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un método de pago", "warning");
      return false;
    }

    // Monto
    if (monto <= 0) {
      $("#monto_pago").addClass("is-invalid");
      Swal.fire("Monto inválido", "El monto debe ser mayor que cero", "warning");
      return false;
    }

    // Estado del pago
    if (!estadoPago) {
      $("#estado_pago").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar el estado del pago", "warning");
      return false;
    }

    /* ======= ASEGURAR QUE EL PACIENTE EXISTA (BACKEND) ======= */
    if (!idPaciente) {
      const respPaciente = await $.ajax({
        url: `${CTRL_PAGO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
        dataType: "json"
      }).catch(() => ({ status: "error", message: "No se pudo buscar el paciente." }));

      if (respPaciente.status !== "success") {
        $("#correo_paciente").addClass("is-invalid");
        Swal.fire("Paciente no encontrado", respPaciente.message || "Verifique el correo ingresado", "error");
        return false;
      }

      idPaciente = respPaciente.data.id_paciente;
      $("#id_paciente").val(idPaciente);
      $("#nombre_paciente").val(respPaciente.data.nombre);
    }

    return true;
  }

  
  function cargarTratamientosSelect(selectedId = null) {
    const $sel = $("#nombre_tratamiento");
    
    console.log("🔄 Iniciando carga de tratamientos...");
    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_PAGO}?opcion=listar_tratamientos`,
      type: "get",
      dataType: "json",
      success: function (r) {
        console.log("✅ Respuesta listar_tratamientos:", r);
        $sel.empty();

        if (!r || r.status !== "success") {
          console.error("❌ Error en respuesta:", r);
          $sel.append('<option value="" disabled selected>Error al cargar</option>');
          return;
        }

        if (!Array.isArray(r.data) || r.data.length === 0) {
          console.warn("⚠️ No hay tratamientos disponibles");
          $sel.append('<option value="" disabled selected>Sin tratamientos disponibles</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un tratamiento --</option>');
        r.data.forEach(t => {
          console.log(`📌 Agregando tratamiento: ID=${t.id_tratamiento}, Nombre=${t.nombre}`);
          $sel.append(`<option value="${t.id_tratamiento}">${t.nombre}</option>`);
        });

        if (selectedId) {
          console.log(`🎯 Seleccionando tratamiento ID: ${selectedId}`);
          $sel.val(String(selectedId));
        }

        console.log("✅ Tratamientos cargados exitosamente");
      },
      error: function(xhr, status, error) {
        console.error("❌ Error AJAX al cargar tratamientos:");
        console.error("Status:", status);
        console.error("Error:", error);
        console.error("Response:", xhr.responseText);
        $sel.empty().append('<option value="" disabled selected>Error al cargar tratamientos</option>');
      }
    });
  }

 
  $("#pago_correo_paciente").on("blur", function () {
    const correo = $(this).val().trim();
    if (correo === "") return;

    console.log("🔍 Buscando paciente con correo:", correo);

    $.getJSON(
      `${CTRL_PAGO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
      function (r) {
        console.log("✅ Respuesta buscar paciente:", r);
        if (r.status === "success") {
          $("#nombre_pacientep").val(r.data.nombre);
          $("#id_paciente").val(r.data.id_paciente);
          console.log("✅ Paciente encontrado:", r.data.nombre);
        } else {
          $("#nombre_pacientep").val("");
          $("#id_paciente").val("");
          Swal.fire("Paciente no encontrado", r.message || "Verifique el correo ingresado", "error");
        }
      }
    ).fail(function(xhr, status, error) {
      console.error("❌ Error al buscar paciente:", status, error);
      $("#nombre_pacientep").val("");
      $("#id_paciente").val("");
      Swal.fire("Error", "No se pudo buscar el paciente", "error");
    });
  });


  const modalPago = new bootstrap.Modal(document.getElementById("modalPago"));
  const modalPagoVer = new bootstrap.Modal(document.getElementById("modalPagoVer"));

  $("#modalPago").on("shown.bs.modal", function () {
    console.log("📂 Modal de pago abierto");
    cargarTratamientosSelect();
  });

  
  let tablaPagos = $("#tablaPagos").DataTable({
    ajax: {
      url: `${CTRL_PAGO}?opcion=listar`,
      type: "get",
      dataType: "json",
      dataSrc: function (json) {
        console.log("📊 Respuesta listar pagos:", json);
        if (!json || json.status !== "success") {
          console.warn("⚠️ Error en listar pagos:", json);
          return [];
        }
        return json.data;
      },
      error: function (xhr, status, error) {
        console.error("❌ AJAX error listar pagos:", status, error);
        console.error("Response:", xhr.responseText);
      }
    },
    language: { url: "app/ajax/idioma.json" },
    responsive: true,
    columns: [
      { data: "nombre_paciente", defaultContent: "" },
      { data: "nombre_tratamiento", defaultContent: "" },
      { 
        data: "metodo_pago",
        defaultContent: "",
        render: function(metodo) {
          if (!metodo) return "";
          metodo = metodo.toLowerCase();
          
          if (metodo === "efectivo") return `<span class="badge bg-success">Efectivo</span>`;
          if (metodo === "tarjeta") return `<span class="badge bg-primary">Tarjeta</span>`;
          if (metodo === "transferencia") return `<span class="badge bg-info">Transferencia</span>`;
          
          return `<span class="badge bg-secondary">${metodo}</span>`;
        }
      },
      { 
        data: "monto",
        defaultContent: "0.00",
        render: v => `$${parseFloat(v || 0).toFixed(2)}`
      },
      {
        data: "estado_pago",
        defaultContent: "",
        render: function(estado) {
          if (!estado) return `<span class="badge bg-secondary">Sin estado</span>`;
          estado = estado.toLowerCase();

          if (estado === "pagado") return `<span class="badge bg-success">Pagado</span>`;
          if (estado === "no pagado") return `<span class="badge bg-danger">No pagado</span>`;

          return `<span class="badge bg-secondary">${estado}</span>`;
        }
      },
      {
        data: "referencia",
        defaultContent: "",
        render: v => v || "N/A"
      },
      {
        data: "created_at",
        defaultContent: "",
        render: v => v ? v.substring(0, 16).replace('T', ' ') : ""
      },
      {
        data: null,
        className: "text-center",
        render: row => `
          <button class="btn mb-2 btn-warning btn-editar-pago" data-id="${row.id_pago}">Editar</button>
          <button class="btn btn-danger btn-eliminar-pago" data-id="${row.id_pago}">Eliminar</button>
        `
      }
    ]
  });


  $("#btnNuevoPago").on("click", function () {
    console.log("➕ Abriendo modal para nuevo pago");
    $("#tituloPago").text("Nuevo Pago");
    $("#formPago")[0].reset();
    $("#id_pago").val("");
    $("#id_paciente").val("");
    $("#id_tratamiento").val("");

    limpiarErroresFormularioPago();

    $("#correo_paciente").prop("readonly", false);
    $("#nombre_paciente").val("").prop("readonly", true);
    $("#created_at_pago").val("").prop("readonly", true);

    cargarTratamientosSelect();

    modalPago.show();
  });

  $("#formPago").on("submit", async function (e) {
    e.preventDefault();

    const isEdit = $("#id_pago").val() !== "";
    const opcion = isEdit ? "actualizar" : "agregar";

    console.log(`💾 Guardando pago (${opcion})...`);

    if (!(await validarFormularioPago(isEdit))) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_PAGO}?opcion=${opcion}`,
      type: "post",
      data: fd,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        console.log("✅ Respuesta guardar:", r);
        if (r.status === "success") {
          Swal.fire("Éxito", r.message, "success");
          modalPago.hide();
          tablaPagos.ajax.reload(null, false);
        } else {
          Swal.fire("Error", r.message, "error");
        }
      },
      error: function(xhr) {
        console.error("❌ Error en submit:", xhr.responseText);
        Swal.fire("Error", "No se pudo procesar la solicitud", "error");
      }
    });
  });

  
  $("#tablaPagos").on("click", ".btn-editar-pago", function () {
    const id = $(this).data("id");
    console.log("✏️ Editando pago ID:", id);

    $.getJSON(`${CTRL_PAGO}?opcion=obtener&id=${id}`, function (r) {
      console.log("📄 Datos del pago:", r);
      if (r.status !== "success") {
        Swal.fire("Error", "No se encontró el pago", "error");
        return;
      }

      const p = r.data;

      $("#tituloPago").text("Editar Pago");
      $("#id_pago").val(p.id_pago);
      $("#id_paciente").val(p.id_paciente);
      $("#id_tratamiento").val(p.id_tratamiento);

      $("#correo_paciente").val("").prop("readonly", true);
      $("#nombre_paciente").val(p.nombre_paciente).prop("readonly", true);

      $("#metodo_pago").val(p.metodo_pago);
      $("#monto_pago").val(p.monto);
      $("#estado_pago").val(p.estado_pago);
      $("#referencia_pago").val(p.referencia || "");
      $("#created_at_pago").val(p.created_at ? p.created_at.substring(0, 16).replace('T', ' ') : "");

      cargarTratamientosSelect(p.id_tratamiento);

      modalPago.show();
    }).fail(function() {
      Swal.fire("Error", "No se pudo cargar la información del pago", "error");
    });
  });

  $("#tablaPagos").on("click", ".btn-eliminar-pago", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "¿Eliminar pago?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Eliminar",
      cancelButtonText: "Cancelar"
    }).then(res => {
      if (!res.isConfirmed) return;

      console.log("🗑️ Eliminando pago ID:", id);

      $.post(
        `${CTRL_PAGO}?opcion=eliminar`,
        { id },
        function (r) {
          console.log("✅ Respuesta eliminar:", r);
          if (r.status === "success") {
            Swal.fire("Eliminado", r.message, "success");
            tablaPagos.ajax.reload(null, false);
          } else {
            Swal.fire("Error", r.message, "error");
          }
        },
        "json"
      ).fail(function() {
        Swal.fire("Error", "No se pudo eliminar el pago", "error");
      });
    });
  });

});