const CTRL_DISPONIBILIDAD = "app/controllers/disponibilidadController.php";

$(document).ready(function () {

  function limpiarErroresDisponibilidad() {
    $('#id_odontologo_disp, #fecha_inicio, #fecha_fin, #hora_inicio, #hora_fin, #notas')
      .removeClass("is-invalid");
  }


function validarFormularioDisponibilidad() {
  limpiarErroresDisponibilidad();

  const idOdontologo = $("#id_odontologo_disp").val();
  const fechaInicio = $("#fecha_inicio").val();
  const fechaFin = $("#fecha_fin").val();
  const horaInicio = $("#hora_inicio").val();
  const horaFin = $("#hora_fin").val();
  const cupo = $("#cupo").val(); 

  const hoy = new Date().toISOString().split("T")[0];

  /* ===== Odontólogo ===== */
  if (!idOdontologo) {
    $("#id_odontologo_disp").addClass("is-invalid");
    Swal.fire("Dato requerido", "Debe seleccionar un odontólogo.", "warning");
    return false;
  }

  /* ===== Fechas ===== */
  if (!fechaInicio) {
    $("#fecha_inicio").addClass("is-invalid");
    Swal.fire("Dato requerido", "Debe elegir la fecha inicio.", "warning");
    return false;
  }

  if (!fechaFin) {
    $("#fecha_fin").addClass("is-invalid");
    Swal.fire("Dato requerido", "Debe elegir la fecha fin.", "warning");
    return false;
  }

  if (fechaInicio < hoy) {
    $("#fecha_inicio").addClass("is-invalid");
    Swal.fire("Fecha inválida", "La fecha inicio debe ser igual o mayor a hoy.", "warning");
    return false;
  }

  const diaInicio = new Date(fechaInicio).getDay();
  const diaFin = new Date(fechaFin).getDay();

  let fi = new Date(fechaInicio);
  let ff = new Date(fechaFin);

  while (fi <= ff) {
    if (fi.getDay() === 0) {
      Swal.fire("Fecha inválida", "El rango contiene un domingo. Selecciona un rango válido.", "warning");
      return false;
    }
    fi.setDate(fi.getDate() + 1);
  }

  if (fechaInicio > fechaFin) {
    $("#fecha_inicio, #fecha_fin").addClass("is-invalid");
    Swal.fire("Fechas incorrectas", "La fecha inicio no puede ser mayor que la fecha fin.", "warning");
    return false;
  }

  /* ===== Horas ===== */
  if (!horaInicio) {
    $("#hora_inicio").addClass("is-invalid");
    Swal.fire("Dato requerido", "Debe elegir hora inicio.", "warning");
    return false;
  }

  if (!horaFin) {
    $("#hora_fin").addClass("is-invalid");
    Swal.fire("Dato requerido", "Debe elegir hora fin.", "warning");
    return false;
  }

  if (!horaInicio.endsWith(":00") || !horaFin.endsWith(":00")) {
    Swal.fire("Hora inválida", "Las horas deben ser exactas (ej: 09:00, 14:00).", "warning");
    $("#hora_inicio, #hora_fin").addClass("is-invalid");
    return false;
  }

  if (horaInicio >= horaFin) {
    $("#hora_inicio, #hora_fin").addClass("is-invalid");
    Swal.fire("Horas incorrectas", "La hora inicio debe ser menor a la hora fin.", "warning");
    return false;
  }

  const hi = parseInt(horaInicio.split(":")[0]);
  const hf = parseInt(horaFin.split(":")[0]);

  if (hi < 7 || hf > 17) {
    Swal.fire("Horario inválido", "La disponibilidad debe estar entre 07:00 y 17:00.", "warning");
    return false;
  }

  /* ===== Validar el campo Cupo ===== */
  if (!cupo || parseInt(cupo) <= 0) {
    $("#cupo").addClass("is-invalid");
    Swal.fire("Cupo inválido", "El cupo debe ser un número mayor que 0.", "warning");
    return false;
  }

  return true;
}


  function cargarOdontologosDisponibilidad(selected = null) {

    $.ajax({
      url: `${CTRL_DISPONIBILIDAD}?opcion=listar_odontologos`,
      type: "get",
      dataType: "json",
      success: function (r) {

        const $sel = $("#id_odontologo_disp");
        $sel.empty();

        if (!r || r.status !== "success") {
          $sel.append(`<option value="" disabled selected>No hay odontólogos</option>`);
          return;
        }

        $sel.append(`<option value="" disabled selected>-- Selecciona un odontólogo --</option>`);

        r.data.forEach(o => {
          $sel.append(`<option value="${o.id_odontologo}">${o.nombre}</option>`);
        });

        if (selected) $sel.val(String(selected));
      }
    });

  }

  let tablaDisponibilidad = $("#tablaDisponibilidad").DataTable({
    ajax: {
      url: `${CTRL_DISPONIBILIDAD}?opcion=listar`,
      dataSrc: json => json.status === "success" ? json.data : []
    },
    language: { url: "app/ajax/idioma.json" },
    responsive: true,
    columns: [
  { data: "nombre_odontologo" },
  { data: "fecha_inicio" },
  { data: "fecha_fin" },
  { data: "hora_inicio" },
  { data: "hora_fin" },
  { data: "notas" }, 
  { data: "cupo" },
  {
    data: null,
    className: "text-center",
    render: row => `
      <button class="btn mb-1 btn-warning btn-editar" data-id="${row.id_disponibilidad}">
        Editar
      </button>
      <button class="btn btn-danger btn-eliminar" data-id="${row.id_disponibilidad}">
        Eliminar
      </button>
    `
  }
]

  });

  const modalDisponibilidad = new bootstrap.Modal(document.getElementById("modalDisponibilidad"));
const modalDisponibilidadVer = new bootstrap.Modal(document.getElementById("modalDisponibilidadVer"));

  $("#btnNuevaDisponibilidad").on("click", function () {

    $("#tituloDisponibilidad").text("Nueva Disponibilidad");

    $("#formDisponibilidad")[0].reset();
    $("#id_disponibilidad").val("");

    limpiarErroresDisponibilidad();
    cargarOdontologosDisponibilidad();

    modalDisponibilidad.show();
  });


  $("#formDisponibilidad").on("submit", function (e) {
    e.preventDefault();

    const isEdit = $("#id_disponibilidad").val() !== "";
    const opcion = isEdit ? "actualizar" : "agregar";

    if (!validarFormularioDisponibilidad()) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_DISPONIBILIDAD}?opcion=${opcion}`,
      type: "post",
      data: fd,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (r) {

        if (r.status === "success") {
          Swal.fire("Éxito", r.message, "success");

          modalDisponibilidad.hide();
          tablaDisponibilidad.ajax.reload(null, false);
        } else {
          Swal.fire("Error", r.message, "error");
        }
      }
    });

  });


$("#tablaDisponibilidad").on("click", ".btn-editar", function () {

  const id = $(this).data("id");

  $.getJSON(`${CTRL_DISPONIBILIDAD}?opcion=obtener&id=${id}`, function (r) {

    if (r.status !== "success") {
      Swal.fire("Error", "No se encontró la disponibilidad.", "error");
      return;
    }

    const d = r.data;

    $("#tituloDisponibilidad").text("Editar Disponibilidad");

    $("#id_disponibilidad").val(d.id_disponibilidad);
    $("#fecha_inicio").val(d.fecha_inicio);
    $("#fecha_fin").val(d.fecha_fin);
    $("#hora_inicio").val(d.hora_inicio.substring(0, 5));
    $("#hora_fin").val(d.hora_fin.substring(0, 5));
    $("#notas").val(d.notas);
    $("#cupo").val(d.cupo); 

    cargarOdontologosDisponibilidad(d.id_odontologo);


 modalDisponibilidadVer.hide();
    modalDisponibilidad.show();
  });
});


  $("#fecha_inicio, #fecha_fin").on("change", function () {
    $(this).removeClass("is-invalid");
});


  $("#tablaDisponibilidad").on("click", ".btn-eliminar", function () {

    const id = $(this).data("id");

    Swal.fire({
      title: "¿Eliminar disponibilidad?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Eliminar"
    }).then(res => {

      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_DISPONIBILIDAD}?opcion=eliminar`,
        { id },
        function (r) {

          if (r.status === "success") {
            Swal.fire("Eliminada", r.message, "success");
            tablaDisponibilidad.ajax.reload(null, false);
          } else {
            Swal.fire("Error", r.message, "error");
          }

        },
        "json"
      );

    });

  });

});
