const CTRL_TRATAMIENTO = "app/controllers/tratamientoController.php";

$(document).ready(function () {

 
  function limpiarErroresFormularioTratamiento() {
    $('#trat_correo_paciente, #trat_id_odontologo, #trat_id_servicio, #fecha_fin, #trat_estado, #trat_notas')
      .removeClass("is-invalid");
  }

  async function validarFormularioTratamiento(isEdit) {

    limpiarErroresFormularioTratamiento();

    const correo        = $("#trat_correo_paciente").val().trim();
    const idOdontologo  = $("#trat_id_odontologo").val();
    const idServicio    = $("#trat_id_servicio").val();
    const fechaInicio   = $("#trat_fecha_inicio").val().trim(); // solo lectura (created_at)
    const fechaFin      = $("#fecha_fin").val().trim();
    const estado        = $("#trat_estado").val();
    const notas         = $("#trat_notas").val().trim();
    let   idPaciente    = $("#id_paciente_trat").val();

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    

    // Correo
    if (!emailRegex.test(correo)) {
      $("#trat_correo_paciente").addClass("is-invalid");
      Swal.fire("Correo inválido", "Debe ingresar un correo válido", "warning");
      return false;
    }

    // Odontólogo
    if (!idOdontologo) {
      $("#trat_id_odontologo").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un odontólogo", "warning");
      return false;
    }

    // Servicio
    if (!idServicio) {
      $("#trat_id_servicio").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un servicio / tratamiento", "warning");
      return false;
    }

    // Estado
    if (!estado) {
      $("#trat_estado").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un estado", "warning");
      return false;
    }

    // Validar fecha fin (opcional)
    if (fechaFin !== "") {
      const fFin = new Date(fechaFin);
      if (isNaN(fFin.getTime())) {
        $("#fecha_fin").addClass("is-invalid");
        Swal.fire("Fecha inválida", "La fecha fin no tiene un formato válido", "warning");
        return false;
      }

      // Si hay fecha de inicio (created_at) la usamos como mínimo
      if (fechaInicio !== "") {
        const fIni = new Date(fechaInicio.substring(0, 10));
        if (!isNaN(fIni.getTime()) && fFin < fIni) {
          $("#fecha_fin").addClass("is-invalid");
          Swal.fire("Fecha inválida", "La fecha fin no puede ser menor que la fecha inicio", "warning");
          return false;
        }
      }
    }

    // Notas opcionales, pero si está finalizado o cancelado, pedimos algo
    if ((estado === "finalizado" || estado === "cancelado") && notas.length < 5) {
      $("#trat_notas").addClass("is-invalid");
      Swal.fire(
        "Información requerida",
        "Para estados 'Finalizado' o 'Cancelado' debe escribir un breve detalle en notas.",
        "warning"
      );
      return false;
    }

    if (!idPaciente) {
      const respPaciente = await $.ajax({
        url: `${CTRL_TRATAMIENTO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
        dataType: "json"
      }).catch(() => ({ status: "error", message: "No se pudo buscar el paciente." }));

      if (respPaciente.status !== "success") {
        $("#trat_correo_paciente").addClass("is-invalid");
        Swal.fire("Paciente no encontrado", respPaciente.message || "Verifique el correo ingresado", "error");
        return false;
      }

      idPaciente = respPaciente.data.id_paciente;
      $("#id_paciente_trat").val(idPaciente);
      $("#trat_nombre_paciente").val(respPaciente.data.nombre);
    }

   
    const esDuplicado = await $.ajax({
      url: `${CTRL_TRATAMIENTO}?opcion=listar`,
      dataType: "json"
    }).then(r => {
      if (r.status !== "success") return false;

      return r.data.some(t =>
        t.id_paciente == idPaciente &&
        t.id_odontologo == idOdontologo &&
        t.id_servicio == idServicio &&
        t.estado && t.estado.toLowerCase() === "en curso" &&
        (!isEdit || t.id_tratamiento != $("#id_tratamiento").val())
      );
    }).catch(() => false);

    if (esDuplicado) {
      Swal.fire(
        "Tratamiento duplicado",
        "Ya existe un tratamiento EN CURSO para este paciente, servicio y odontólogo.",
        "error"
      );
      return false;
    }

    return true;
  }


  function cargarOdontologosSelectTrat(selectedId = null) {
    const $sel = $("#trat_id_odontologo");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_TRATAMIENTO}?opcion=listar_odontologos`,
      type: "get",
      dataType: "json",
      success: function (r) {
        $sel.empty();

        if (!r || r.status !== "success" || !Array.isArray(r.data) || r.data.length === 0) {
          $sel.append('<option value="" disabled selected>Sin odontólogos disponibles</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un odontólogo --</option>');
        r.data.forEach(od => {
          $sel.append(`<option value="${od.id_odontologo}">${od.nombre}</option>`);
        });

        if (selectedId) $sel.val(String(selectedId));
      }
    });
  }

  function cargarServiciosSelect(selectedId = null) {
    const $sel = $("#trat_id_servicio");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_TRATAMIENTO}?opcion=listar_servicios`,
      type: "get",
      dataType: "json",
      success: function (r) {
        $sel.empty();

        if (!r || r.status !== "success" || !Array.isArray(r.data) || r.data.length === 0) {
          $sel.append('<option value="" disabled selected>Sin servicios disponibles</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un servicio --</option>');
        r.data.forEach(s => {
          $sel.append(`<option value="${s.id_servicio}">${s.nombre_servicio}</option>`);
        });

        if (selectedId) $sel.val(String(selectedId));
      }
    });
  }


  $("#trat_correo_paciente").on("blur", function () {
    const correo = $(this).val().trim();
    if (correo === "") return;

    $.getJSON(
      `${CTRL_TRATAMIENTO}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
      function (r) {
        if (r.status === "success") {
          $("#trat_nombre_paciente").val(r.data.nombre);
          $("#id_paciente_trat").val(r.data.id_paciente);
        } else {
          $("#trat_nombre_paciente").val("");
          $("#id_paciente_trat").val("");
          Swal.fire("Paciente no encontrado", r.message || "Verifique el correo ingresado", "error");
        }
      }
    );
  });


  const modalTratamiento    = new bootstrap.Modal(document.getElementById("modalTratamiento"));
  const modalTratamientoVer = new bootstrap.Modal(document.getElementById("modalTratamientoVer"));

  $("#modalTratamiento").on("shown.bs.modal", function () {
    cargarOdontologosSelectTrat();
    cargarServiciosSelect();
  });


  let tablaTratamientos = $("#tablaTratamientos").DataTable({
  ajax: {
    url: `${CTRL_TRATAMIENTO}?opcion=listar`,
    type: "get",
    dataType: "json",
    dataSrc: function (json) {
      console.log("Respuesta listar tratamientos:", json);
      if (!json || json.status !== "success") {
        console.warn("Error en listar tratamientos:", json);
        return [];
      }
      return json.data;
    },
    error: function (xhr, status, error) {
      console.error("AJAX error listar tratamientos:", status, error);
      console.error("Respuesta del servidor:", xhr.responseText);
    }
  },
  language: { url: "app/ajax/idioma.json" },
  responsive: true,
  columns: [
    { data: "nombre_paciente", defaultContent: "" },
    { data: "correo_paciente", defaultContent: "" },
    { data: "nombre_odontologo", defaultContent: "" },
    { data: "nombre_servicio", defaultContent: "" },

    {
      data: "created_at",
      defaultContent: "",
      render: v => v ? v.substring(0, 10) : ""
    },
    {
      data: "fecha_fin",
      defaultContent: "",
      render: v => v ? v.substring(0, 10) : ""
    },
    {
      data: "estado",
      defaultContent: "",
      render: function (estado) {
        if (!estado) return `<span class="badge bg-secondary">Sin estado</span>`;
        estado = estado.toLowerCase();

        if (estado === "en curso")   return `<span class="badge bg-primary">En curso</span>`;
        if (estado === "finalizado") return `<span class="badge bg-success">Finalizado</span>`;
        if (estado === "cancelado")  return `<span class="badge bg-danger">Cancelado</span>`;

        return `<span class="badge bg-secondary">${estado}</span>`;
      }
    },
    {
      data: "notas",
      defaultContent: "",
      render: v => v && v.length > 60 ? v.substring(0, 57) + "..." : (v || "")
    },
    {
      data: null,
      className: "text-center",
      render: row => `
        <button class="btn mb-2 btn-warning btn-editar-trat" data-id="${row.id_tratamiento}">Editar</button>
        <button class="btn btn-danger btn-eliminar-trat" data-id="${row.id_tratamiento}">Eliminar</button>`
    }
  ]
});

  $("#btnNuevoTratamiento").on("click", function () {
    $("#tituloTratamiento").text("Nuevo Tratamiento");
    $("#formTratamiento")[0].reset();
    $("#id_tratamiento").val("");
    $("#id_paciente_trat").val("");

    limpiarErroresFormularioTratamiento();

    $("#trat_correo_paciente").prop("readonly", false);
    $("#trat_nombre_paciente").prop("readonly", true);

    // fecha inicio solo visual, la BD la pone con current_timestamp()
    const hoyStr = new Date().toISOString().substring(0, 10);
    $("#trat_fecha_inicio").val(hoyStr);

    cargarOdontologosSelectTrat();
    cargarServiciosSelect();

    modalTratamiento.show();
  });


  $("#formTratamiento").on("submit", async function (e) {
    e.preventDefault();

    const isEdit = $("#id_tratamiento").val() !== "";
    const opcion = isEdit ? "actualizar" : "agregar";

    if (!(await validarFormularioTratamiento(isEdit))) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_TRATAMIENTO}?opcion=${opcion}`,
      type: "post",
      data: fd,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        if (r.status === "success") {
          Swal.fire("Éxito", r.message, "success");
          modalTratamiento.hide();
          tablaTratamientos.ajax.reload(null, false);
        } else {
          Swal.fire("Error", r.message, "error");
        }
      }
    });
  });


  $("#tablaTratamientos").on("click", ".btn-editar-trat", function () {
    const id = $(this).data("id");

    $.getJSON(`${CTRL_TRATAMIENTO}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== "success") {
        Swal.fire("Error", "No se encontró el tratamiento", "error");
        return;
      }

      const t = r.data;

      $("#tituloTratamiento").text("Editar Tratamiento");
      $("#id_tratamiento").val(t.id_tratamiento);

      $("#trat_correo_paciente").val(t.correo_paciente).prop("readonly", true);
      $("#trat_nombre_paciente").val(t.nombre_paciente).prop("readonly", true);
      $("#id_paciente_trat").val(t.id_paciente);

      $("#trat_fecha_inicio").val(t.created_at ? t.created_at.substring(0, 10) : "");
      $("#fecha_fin").val(t.fecha_fin ? t.fecha_fin.substring(0, 10) : "");
      $("#trat_estado").val(t.estado);
      $("#trat_notas").val(t.notas || "");

      cargarOdontologosSelectTrat(t.id_odontologo);
      cargarServiciosSelect(t.id_servicio);

      modalTratamiento.show();
    });
  });


  $("#tablaTratamientos").on("click", ".btn-eliminar-trat", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "¿Eliminar tratamiento?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Eliminar"
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_TRATAMIENTO}?opcion=eliminar`,
        { id },
        function (r) {
          if (r.status === "success") {
            Swal.fire("Eliminado", r.message, "success");
            tablaTratamientos.ajax.reload(null, false);
          } else {
            Swal.fire("Error", r.message, "error");
          }
        },
        "json"
      );
    });
  });

});
