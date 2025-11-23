const CTRL_CITA = 'app/controllers/citaController.php';

$(document).ready(function () {

  console.log('== cita.js cargado ==');

  // =========================
  //  Helpers de validación
  // =========================

  function limpiarErroresFormularioCita() {
    $('#dui, #id_odontologo, #fecha_cita, #hora_cita, #estado')
      .removeClass('is-invalid');
  }

  function validarFormularioCita(isEdit) {
    limpiarErroresFormularioCita();

    const dui          = $('#dui').val().trim();
    const idOdontologo = $('#id_odontologo').val();
    const fechaCita    = $('#fecha_cita').val();
    const horaCita     = $('#hora_cita').val();
    const estado       = $('#estado').val();

    console.log('Validando cita:', { isEdit, dui, idOdontologo, fechaCita, horaCita, estado });

    // DUI
    const duiRegex = /^[0-9]{8}-[0-9]{1}$/;
    if (dui === '' || !duiRegex.test(dui)) {
      $('#dui').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'DUI no válido',
        text: 'El DUI debe tener el formato 00000000-0'
      });
      return false;
    }

    // Odontólogo
    if (!idOdontologo) {
      $('#id_odontologo').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Dato requerido',
        text: 'Debe seleccionar un odontólogo'
      });
      return false;
    }

    // Fecha
    if (!fechaCita) {
      $('#fecha_cita').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Dato requerido',
        text: 'Debe seleccionar la fecha de la cita'
      });
      return false;
    }

    // Hora
    if (!horaCita) {
      $('#hora_cita').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Dato requerido',
        text: 'Debe seleccionar la hora de la cita'
      });
      return false;
    }

    // Estado
    if (!estado) {
      $('#estado').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Dato requerido',
        text: 'Debe seleccionar el estado de la cita'
      });
      return false;
    }

    return true;
  }

  // =========================
  //  Cargar odontólogos en el select
  // =========================

  function cargarOdontologosSelect(selectedId = null) {
    const $sel = $('#id_odontologo');

    if ($sel.length === 0) {
      console.warn('Select #id_odontologo no encontrado en el DOM');
      return;
    }

    console.log('Cargando odontólogos... selectedId =', selectedId);

    // Placeholder mientras carga
    $sel.empty().append('<option value="" disabled selected>Cargando odontólogos...</option>');

    $.ajax({
      url: `${CTRL_CITA}?opcion=listar_odontologos`,
      type: 'get',
      dataType: 'json',
      success: function (r) {
        console.log('Respuesta listar_odontologos:', r);

        if (!r || r.status !== 'success') {
          console.warn(r && r.message ? r.message : 'No se pudieron cargar los odontólogos');
          $sel.empty().append('<option value="" disabled selected>Sin odontólogos disponibles</option>');
          return;
        }

        $sel.empty();
        $sel.append('<option value="" disabled selected>-- Selecciona un odontólogo --</option>');

        r.data.forEach(o => {
          // Se asume que el JSON trae id_odontologo y nombre
          $sel.append(
            `<option value="${o.id_odontologo}">${o.nombre}</option>`
          );
        });

        if (selectedId) {
          $sel.val(String(selectedId));
        }
      },
      error: function (xhr) {
        console.error('Error cargando odontólogos (AJAX error):', xhr.status, xhr.responseText);
        $sel.empty().append('<option value="" disabled selected>Error al cargar</option>');
      }
    });
  }

  // =========================
  //  Modales
  // =========================

  const modalEditarEl = document.getElementById('modalCita');
  const modalVerEl    = document.getElementById('modalCitaVer');

  let modalEditar = null;
  let modalVer    = null;

  if (modalEditarEl) {
    modalEditar = new bootstrap.Modal(modalEditarEl);
    console.log('Modal modalCita inicializado');
  } else {
    console.warn('Elemento #modalCita no encontrado');
  }

  if (modalVerEl) {
    modalVer = new bootstrap.Modal(modalVerEl);
    console.log('Modal modalCitaVer inicializado');
  } else {
    console.warn('Elemento #modalCitaVer no encontrado');
  }

  // Cuando se muestre el modal de Cita (ya sea por botón o por data-bs-target)
  $('#modalCita').on('shown.bs.modal', function () {
    const idCita = $('#id_cita').val();
    console.log('modalCita mostrado; id_cita =', idCita);

    // Si es NUEVA cita (sin id), cargamos lista de odontólogos
    if (!idCita) {
      cargarOdontologosSelect();
    }
  });

  // =========================
  //  DataTable Citas
  // =========================

  let tabla = null;

  if ($('#tablaCitas').length) {
    tabla = $('#tablaCitas').DataTable({
      ajax: {
        url: `${CTRL_CITA}?opcion=listar`,
        type: 'get',
        dataType: 'json',
        dataSrc: function (json) {
          console.log('Respuesta listar citas:', json);
          if (json && json.status === 'success') return json.data;
          console.warn(json && json.message ? json.message : 'Sin datos');
          return [];
        },
        error: e => console.error('Error cargando citas (DataTable AJAX):', e.responseText)
      },
      language: { url: 'app/ajax/idioma.json' },
      dom:
        "<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
      aaSorting: [],
      lengthMenu: [[5, 12, 18, -1], [5, 12, 18, 'Todos']],
      pageLength: 5,
      responsive: true,
      columns: [
        { data: 'nombre_paciente' },
        { data: 'dui_paciente' },
        { data: 'nombre_odontologo' },
        { data: 'fecha_cita' },
        { data: 'hora_cita' },
        { data: 'motivo' },
        {
          data: 'estado',
          className: 'text-center',
          render: function (estado) {
            let badgeClass = 'bg-secondary';
            let texto      = estado;

            switch (estado) {
              case 'programada':
                badgeClass = 'bg-primary';
                texto      = 'Programada';
                break;
              case 'confirmada':
                badgeClass = 'bg-info text-dark';
                texto      = 'Confirmada';
                break;
              case 'atendida':
                badgeClass = 'bg-success';
                texto      = 'Atendida';
                break;
              case 'cancelada':
                badgeClass = 'bg-danger';
                texto      = 'Cancelada';
                break;
            }

            return `<span class="badge ${badgeClass} mb-0">${texto}</span>`;
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          render: function (row) {
            return `
              <button type="button" class="btn btn-sm btn-warning mb-2 btn-editar" data-id="${row.id_cita}">
                <i class="bx bx-edit"></i> Editar
              </button>
              <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_cita}">
                <i class="bx bx-trash"></i> Eliminar
              </button>`;
          }
        }
      ]
    });

    console.log('DataTable de citas inicializado');
  } else {
    console.warn('Tabla #tablaCitas no encontrada. DataTable no inicializado.');
  }

  const $form   = $('#formCita');
  const $id     = $('#id_cita');
  const $titulo = $('#tituloCita');

  // =========================
  //  Botones principales
  // =========================

  // Botón para ver listado de citas
  $('#btnVerCitas').on('click', function () {
    if (!modalVer || !tabla) {
      console.warn('No se puede mostrar listado: modalVer o tabla no inicializados');
      return;
    }
    $('#tituloCitaVer').text('Listado de citas');
    console.log('Abriendo modalCitaVer, recargando tablaCitas...');
    tabla.ajax.reload(null, false);
    modalVer.show();
  });

  // Botón para abrir modal de nueva cita
  $('#btnNuevaCita').on('click', () => {
    if (!modalEditar) {
      console.warn('modalEditar no inicializado');
      return;
    }
    console.log('Preparando modal para NUEVA cita');

    $titulo.text('Nueva cita');
    if ($form.length) $form[0].reset();
    $id.val('');
    $('#nombre_paciente').val('');
    limpiarErroresFormularioCita();

    cargarOdontologosSelect(); // llena el combo de odontólogos

    modalEditar.show();
  });

  // =========================
  //  Submit: Guardar / Actualizar
  // =========================

  $form.on('submit', function (e) {
    e.preventDefault();

    const id     = $id.val();
    const isEdit = !!id;
    const opcion = isEdit ? 'actualizar' : 'agregar';

    console.log('Submit cita. isEdit =', isEdit, 'opcion =', opcion);

    // Validaciones frontend
    if (!validarFormularioCita(isEdit)) {
      console.warn('Validación de formulario de cita falló');
      return;
    }

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_CITA}?opcion=${opcion}`,
      type: 'post',
      data: fd,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (r) {
        console.log('Respuesta guardar/actualizar cita:', r);

        if (r.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: r.message,
            timer: 1200,
            showConfirmButton: false
          });
          if (modalEditar) modalEditar.hide();
          if (tabla) tabla.ajax.reload(null, false);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: r.message || 'Operación fallida'
          });
        }
      },
      error: function (xhr) {
        console.error('Error AJAX guardar/actualizar cita:', xhr.status, xhr.responseText);

        let msg = 'Error AJAX';
        try {
          const res = xhr.responseJSON || JSON.parse(xhr.responseText);
          if (res && res.message) msg = res.message;
        } catch (e) {
          if (xhr.responseText) msg = xhr.responseText;
        }

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: msg
        });
      }
    });
  });

  // =========================
  //  Editar cita
  // =========================

  $('#tablaCitas').on('click', '.btn-editar', function (e) {
    e.preventDefault();

    const id = $(this).data('id');
    console.log('Click editar cita, id =', id);

    $.getJSON(`${CTRL_CITA}?opcion=obtener&id=${id}`, function (r) {
      console.log('Respuesta obtener cita:', r);

      if (r.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'No encontrado' });
        return;
      }

      const c = r.data;

      $titulo.text('Editar cita');
      $id.val(c.id_cita);

      $('#dui').val(c.dui_paciente);
      $('#nombre_paciente').val(c.nombre_paciente || '');
      $('#fecha_cita').val(c.fecha_cita);
      if (c.hora_cita) {
        $('#hora_cita').val(c.hora_cita.substring(0, 5)); // HH:MM
      } else {
        $('#hora_cita').val('');
      }
      $('#motivo').val(c.motivo || '');
      $('#estado').val(c.estado);

      limpiarErroresFormularioCita();

      // cargar odontólogos y seleccionar el correspondiente
      cargarOdontologosSelect(c.id_odontologo);

      if (modalVer) modalVer.hide();
      if (modalEditar) modalEditar.show();
    }).fail(function (xhr) {
      console.error('Error obteniendo cita:', xhr.status, xhr.responseText);
      Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener la cita' });
    });
  });

  // =========================
  //  Eliminar cita
  // =========================

  $('#tablaCitas').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    console.log('Click eliminar cita, id =', id);

    Swal.fire({
      title: '¿Eliminar cita?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_CITA}?opcion=eliminar`,
        { id },
        function (r) {
          console.log('Respuesta eliminar cita:', r);

          if (r.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: r.message,
              timer: 1200,
              showConfirmButton: false
            });
            if (tabla) tabla.ajax.reload(null, false);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: r.message || 'No se pudo eliminar la cita'
            });
          }
        },
        'json'
      ).fail(function (xhr) {
        console.error('Error AJAX eliminar cita:', xhr.status, xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo eliminar la cita (error AJAX)'
        });
      });
    });
  });

});
