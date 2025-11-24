const CTRL_CITA = 'app/controllers/citaController.php';

$(document).ready(function () {

  console.log('== cita.js cargado ==');

  // =========================
  // VALIDACIÓN
  // =========================

  function limpiarErroresFormularioCita() {
    $('#dui, #cita_id_odontologo, #fecha_cita, #hora_cita, #estado')
      .removeClass('is-invalid');
  }

  function validarFormularioCita(isEdit) {
    limpiarErroresFormularioCita();

    const dui          = $('#dui').val().trim();
    const idOdontologo = $("#cita_id_odontologo").val();
    const fechaCita    = $('#fecha_cita').val();
    const horaCita     = $('#hora_cita').val();
    const estado       = $('#estado').val();

    console.log('Validando cita:', { isEdit, dui, idOdontologo, fechaCita, horaCita, estado });

    // DUI
    const duiRegex = /^[0-9]{8}-[0-9]{1}$/;
    if (!duiRegex.test(dui)) {
      $('#dui').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'DUI no válido',
        text: 'Debe tener el formato 00000000-0'
      });
      return false;
    }

    // Odontólogo
    if (!idOdontologo) {
      $('#cita_id_odontologo').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Seleccione un odontólogo',
      });
      return false;
    }

    // Fecha
    if (!fechaCita) {
      $('#fecha_cita').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        text: 'Debe seleccionar la fecha'
      });
      return false;
    }

    // Hora
    if (!horaCita) {
      $('#hora_cita').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        text: 'Debe seleccionar la hora'
      });
      return false;
    }

    // Estado
    if (!estado) {
      $('#estado').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        text: 'Debe seleccionar el estado'
      });
      return false;
    }

    return true;
  }


  // =========================
  // CARGAR ODONTÓLOGOS
  // =========================

  function cargarOdontologosSelect(selectedId = null) {
    const $sel = $('#cita_id_odontologo'); // <- CORRECTO

    if ($sel.length === 0) {
      console.error("ERROR: No existe el select #cita_id_odontologo en el DOM");
      return;
    }

    console.log("Cargando odontólogos...");

    $sel.empty().append('<option value="" disabled selected>Cargando...</option>');

    $.ajax({
      url: `${CTRL_CITA}?opcion=listar_odontologos`,
      type: 'get',
      dataType: 'json',
      success: function (r) {
        console.log("Respuesta listar odontólogos:", r);

        $sel.empty();

        if (!r || r.status !== 'success') {
          $sel.append('<option value="" disabled selected>Sin odontólogos</option>');
          return;
        }

        $sel.append('<option value="" disabled selected>-- Selecciona un odontólogo --</option>');

        r.data.forEach(o => {
          $sel.append(`<option value="${o.id_odontologo}">${o.nombre}</option>`);
        });

        if (selectedId) {
          $sel.val(String(selectedId));
        }
      },
      error: function (xhr) {
        console.error('ERROR AJAX odontólogos:', xhr.responseText);
        $sel.empty().append('<option value="" disabled selected>Error al cargar</option>');
      }
    });
  }


  // =========================
  // MODALES
  // =========================

  const modalEditarEl = document.getElementById('modalCita');
  const modalVerEl    = document.getElementById('modalCitaVer');

  let modalEditar = modalEditarEl ? new bootstrap.Modal(modalEditarEl) : null;
  let modalVer    = modalVerEl    ? new bootstrap.Modal(modalVerEl)    : null;

  $('#modalCita').on('shown.bs.modal', function () {
    cargarOdontologosSelect();
  });


  // =========================
  // DATATABLE
  // =========================

  let tabla = $('#tablaCitas').DataTable({
    ajax: {
      url: `${CTRL_CITA}?opcion=listar`,
      type: 'get',
      dataType: 'json',
      dataSrc: json => json.status === 'success' ? json.data : []
    },
    language: { url: 'app/ajax/idioma.json' },
    responsive: true,
    columns: [
      { data: 'nombre_paciente' },
      { data: 'dui_paciente' },
      { data: 'nombre_odontologo' },
      { data: 'fecha_cita' },
      { data: 'hora_cita' },
      { data: 'motivo' },
      { data: 'estado' },
      {
        data: null,
        className: 'text-center',
        render: row => `
          <button class="btn btn-warning btn-editar" data-id="${row.id_cita}">Editar</button>
          <button class="btn btn-danger btn-eliminar" data-id="${row.id_cita}">Eliminar</button>`
      }
    ]
  });


  // =========================
  // NUEVA CITA
  // =========================

  $('#btnNuevaCita').on('click', function () {
    $('#tituloCita').text('Nueva Cita');
    $('#formCita')[0].reset();
    $('#id_cita').val('');
    limpiarErroresFormularioCita();

    cargarOdontologosSelect();

    modalEditar.show();
  });


  // =========================
  // SUBMIT GUARDAR / EDITAR
  // =========================

  $('#formCita').on('submit', function (e) {
    e.preventDefault();

    const isEdit = $('#id_cita').val() !== "";
    const opcion = isEdit ? 'actualizar' : 'agregar';

    if (!validarFormularioCita(isEdit)) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_CITA}?opcion=${opcion}`,
      type: 'post',
      data: fd,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (r) {
        if (r.status === 'success') {
          Swal.fire({ icon: 'success', title: r.message, timer: 1200, showConfirmButton: false });
          modalEditar.hide();
          tabla.ajax.reload(null, false);
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: r.message });
        }
      }
    });
  });


  // =========================
  // EDITAR CITA
  // =========================

  $('#tablaCitas').on('click', '.btn-editar', function () {
    const id = $(this).data('id');

    $.getJSON(`${CTRL_CITA}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se encontró la cita' });
        return;
      }

      const c = r.data;

      $('#tituloCita').text('Editar Cita');
      $('#id_cita').val(c.id_cita);
      $('#dui').val(c.dui_paciente);
      $('#nombre_paciente').val(c.nombre_paciente);
      $('#fecha_cita').val(c.fecha_cita);
      $('#hora_cita').val(c.hora_cita.substring(0,5));
      $('#motivo').val(c.motivo);
      $('#estado').val(c.estado);

      cargarOdontologosSelect(c.id_odontologo);

      modalVer.hide();
      modalEditar.show();
    });
  });


  // =========================
  // ELIMINAR CITA
  // =========================

  $('#tablaCitas').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: '¿Eliminar cita?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(`${CTRL_CITA}?opcion=eliminar`, { id }, function (r) {
        if (r.status === 'success') {
          Swal.fire({ icon: 'success', title: r.message, timer: 1200, showConfirmButton: false });
          tabla.ajax.reload(null, false);
        }
      }, 'json');
    });
  });

});
