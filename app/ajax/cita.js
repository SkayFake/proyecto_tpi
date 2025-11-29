const CTRL_CITA = "app/controllers/citaController.php";

$(document).ready(function () {


  function limpiarErroresFormularioCita() {
    $('#correo_paciente, #cita_id_odontologo, #fecha_cita, #hora_cita, #cita_estado, #motivo')
      .removeClass("is-invalid");
  }


  async function validarFormularioCita(isEdit) {

    limpiarErroresFormularioCita();

    const correo = $("#correo_paciente").val().trim();
    const idOdontologo = $("#cita_id_odontologo").val();
    const fechaCita = $("#fecha_cita").val();
    const horaCita = $("#hora_cita").val();
    const motivo = $("#motivo").val().trim();
    const estado = $("#cita_estado").val();

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);


    if (!emailRegex.test(correo)) {
      $("#correo_paciente").addClass("is-invalid");
      Swal.fire("Correo inválido", "Debe ingresar un correo válido", "warning");
      return false;
    }

    if (!idOdontologo) {
      $("#cita_id_odontologo").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un odontólogo", "warning");
      return false;
    }

    if (motivo === "") {
      $("#motivo").addClass("is-invalid");
      Swal.fire("Motivo requerido", "Debe ingresar el motivo de la cita", "warning");
      return false;
    }

    if (!fechaCita) {
      $("#fecha_cita").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar una fecha", "warning");
      return false;
    }

    const fechaObj = new Date(fechaCita);
    if (fechaObj <= hoy) {
      $("#fecha_cita").addClass("is-invalid");
      Swal.fire("Fecha inválida", "La fecha debe ser mayor a hoy", "warning");
      return false;
    }

    if (fechaObj.getDay() === 0) {
      $("#fecha_cita").addClass("is-invalid");
      Swal.fire("Fecha inválida", "No se permiten citas los domingos", "warning");
      return false;
    }

    if (!horaCita) {
      $("#hora_cita").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar una hora", "warning");
      return false;
    }

    const [hh, mm] = horaCita.split(":").map(Number);

    if (mm !== 0) {
      $("#hora_cita").addClass("is-invalid");
      Swal.fire(
        "Hora inválida",
        "Las citas solo se permiten en horas exactas (ejemplo: 09:00, 10:00).",
        "warning"
      );
      return false;
    }


    if (hh < 7 || hh > 17) {
      $("#hora_cita").addClass("is-invalid");
      Swal.fire("Hora inválida", "Debe estar entre 07:00 y 17:00", "warning");
      return false;
    }

    if (!estado) {
      $("#cita_estado").addClass("is-invalid");
      Swal.fire("Dato requerido", "Debe seleccionar un estado", "warning");
      return false;
    }


    const esDuplicada = await $.ajax({
      url: `${CTRL_CITA}?opcion=listar`,
      dataType: "json"
    }).then(r => {
      if (r.status !== "success") return false;

      return r.data.some(c =>
        c.id_odontologo == idOdontologo &&
        c.fecha_cita === fechaCita &&
        c.hora_cita.substring(0, 5) === horaCita &&
        (!isEdit || c.id_cita != $("#id_cita").val())
      );
    });

    if (esDuplicada) {
      Swal.fire("Cita duplicada", "Ya existe una cita con ese odontólogo en ese horario.", "error");
      return false;
    }


    const disponible = await $.ajax({
      url: `${CTRL_CITA}?opcion=validar_disponibilidad`,
      type: "get",
      data: {
        id_odontologo: idOdontologo,
        fecha_cita: fechaCita,
        hora_cita: horaCita
      },
      dataType: "json"
    }).catch(() => ({ status: "error", message: "Error al validar disponibilidad" }));

    if (disponible.status !== "success") {
      Swal.fire("Sin disponibilidad", disponible.message, "error");
      return false;
    }

    return true;
  }

  function cargarOdontologosSelect(selectedId = null) {
    const $sel = $("#cita_id_odontologo");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_CITA}?opcion=listar_odontologos`,
      type: "get",
      dataType: "json",
      success: function (r) {

        $sel.empty();

        if (!r || r.status !== "success") {
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

  $("#cita_id_odontologo, #fecha_cita").on("change", function () {

    const id = $("#cita_id_odontologo").val();
    const fecha = $("#fecha_cita").val();

    if (!id || !fecha) return;

    $.getJSON(
      `${CTRL_CITA}?opcion=horas_disponibles&id_odontologo=${id}&fecha=${fecha}`,
      function (r) {

        const $hora = $("#hora_cita");
        $hora.empty();

        if (r.status !== "success" || r.data.length === 0) {
          $hora.append(`<option value="">No hay horas disponibles</option>`);
          return;
        }

        $hora.append(`<option value="">-- Selecciona una hora --</option>`);

        r.data.forEach(h => {
          $hora.append(`<option value="${h}">${h}</option>`);
        });
      }
    );
  });


  const modalHorarios = new bootstrap.Modal(document.getElementById("modalHorarios"));

  $("#btnVerHorarios").on("click", function () {

    const idOdont = $("#cita_id_odontologo").val();
    const fecha = $("#fecha_cita").val();

    if (!idOdont || !fecha) {
      Swal.fire("Faltan datos", "Selecciona odontólogo y fecha", "warning");
      return;
    }

    $.ajax({
      url: `${CTRL_CITA}?opcion=horarios_disponibles`,
      type: "get",
      data: { id_odontologo: idOdont, fecha },
      dataType: "json",
      success: function (r) {

        const $lista = $("#listaHorarios");
        $lista.empty();

        if (r.status !== "success" || r.data.length === 0) {
          $lista.append(`<li class="list-group-item text-center">No hay horarios disponibles</li>`);
        } else {
          r.data.forEach(hora => {
            $lista.append(`<li class="list-group-item">${hora}</li>`);
          });
        }

        modalHorarios.show();
      }
    });

  });


  $("#correo_paciente").on("blur", function () {
    const correo = $(this).val().trim();
    if (correo === "") return;

    $.getJSON(
      `${CTRL_CITA}?opcion=buscar_paciente_correo&correo=${encodeURIComponent(correo)}`,
      function (r) {
        if (r.status === "success") {
          $("#nombre_paciente").val(r.data.nombre);
          $("#id_paciente").val(r.data.id_paciente);
        } else {
          $("#nombre_paciente").val("");
          $("#id_paciente").val("");
          Swal.fire("Paciente no encontrado", "Verifique el correo ingresado", "error");
        }
      }
    );
  });

  function formatearSoloFecha(fecha) {

    if (!fecha) return "N/A";

    const partes = fecha.split("-"); // yyyy-mm-dd
    if (partes.length !== 3) return fecha;

    const año = partes[0];
    const mes = parseInt(partes[1]) - 1;
    const dia = partes[2];

    const meses = [
      "enero", "febrero", "marzo", "abril", "mayo", "junio",
      "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];

    return `${dia} de ${meses[mes]} de ${año}`;
  }

  const modalEditar = new bootstrap.Modal(document.getElementById("modalCita"));
  const modalVer = new bootstrap.Modal(document.getElementById("modalCitaVer"));

  $("#modalCita").on("shown.bs.modal", function () {
    cargarOdontologosSelect();
  });


  let tabla = $("#tablaCitas").DataTable({
    ajax: {
      url: `${CTRL_CITA}?opcion=listar`,
      dataSrc: function (json) {
        console.log("### JSON COMPLETO DE CITAS ###", json);
        return json.status === "success" ? json.data : [];
      }
    },
    language: { url: "app/ajax/idioma.json" },
    responsive: true,
    columns: [
      { data: "nombre_paciente" },
      { data: "correo_paciente" },
      { data: "nombre_odontologo" },
      {
  data: "fecha_cita",
  render: function (v) {
    return formatearSoloFecha(v);
  }
},

      { data: "hora_cita" },
      { data: "motivo" },

      {
        data: "estado",
        render: function (estado) {

          if (!estado) {
            return `<span class="badge bg-secondary">Sin estado</span>`;
          }

          estado = estado.toLowerCase();

          if (estado === "programada") {
            return `<span class="badge bg-primary">Programada</span>`;
          }

          if (estado === "confirmada") {
            return `<span class="badge bg-info text-dark">Confirmada</span>`;
          }

          if (estado === "atendida") {
            return `<span class="badge bg-success">Atendida</span>`;
          }

          if (estado === "cancelada") {
            return `<span class="badge bg-danger">Cancelada</span>`;
          }

          return `<span class="badge bg-secondary">${estado}</span>`;
        }
      },

      {
        data: null,
        className: "text-center",
        render: row => `
          <button class="btn mb-2 btn-warning btn-editar" data-id="${row.id_cita}">Editar</button>
          <button class="btn btn-danger btn-eliminar" data-id="${row.id_cita}">Eliminar</button>`
      }
    ]
  });


  $("#btnNuevaCita").on("click", function () {
    $("#tituloCita").text("Nueva Cita");
    $("#formCita")[0].reset();
    $("#id_cita").val("");
    $("#id_paciente").val("");

    limpiarErroresFormularioCita();

    $("#correo_paciente").prop("readonly", false);
    $("#nombre_paciente").prop("readonly", true);

    cargarOdontologosSelect();
    modalEditar.show();
  });


  $("#formCita").on("submit", async function (e) {
    e.preventDefault();

    const isEdit = $("#id_cita").val() !== "";
    const opcion = isEdit ? "actualizar" : "agregar";

    if (!(await validarFormularioCita(isEdit))) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_CITA}?opcion=${opcion}`,
      type: "post",
      data: fd,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {
        if (r.status === "success") {
          Swal.fire("Éxito", r.message, "success");
          modalEditar.hide();
          tabla.ajax.reload(null, false);
        } else {
          Swal.fire("Error", r.message, "error");
        }
      }
    });
  });


  $("#tablaCitas").on("click", ".btn-editar", function () {
    const id = $(this).data("id");

    $.getJSON(`${CTRL_CITA}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== "success") {
        Swal.fire("Error", "No se encontró la cita", "error");
        return;
      }

      const c = r.data;

      if (c.estado === "atendida") {
        Swal.fire("No permitido", "No se puede editar una cita ya atendida", "error");
        return;
      }

      $("#tituloCita").text("Editar Cita");
      $("#id_cita").val(c.id_cita);

      $("#correo_paciente").val(c.correo_paciente).prop("readonly", true);
      $("#nombre_paciente").val(c.nombre_paciente).prop("readonly", true);
      $("#id_paciente").val(c.id_paciente);

      $("#fecha_cita").val(c.fecha_cita);
      $("#hora_cita").val(c.hora_cita.substring(0, 5));
      $("#motivo").val(c.motivo);
      $("#cita_estado").val(c.estado);

      cargarOdontologosSelect(c.id_odontologo);

      modalVer.hide();
      modalEditar.show();
    });
  });

  $("#tablaCitas").on("click", ".btn-eliminar", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "¿Eliminar cita?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Eliminar"
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_CITA}?opcion=eliminar`,
        { id },
        function (r) {
          if (r.status === "success") {
            Swal.fire("Eliminada", r.message, "success");
            tabla.ajax.reload(null, false);
          } else {
            Swal.fire("Error", r.message, "error");
          }
        },
        "json"
      );
    });
  });

});
