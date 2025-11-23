const CTRL_PACIENTE = 'app/controllers/pacienteController.php';

$(document).ready(function () {

  const modalEditarEl = document.getElementById('modalPaciente');
  const modalVerEl    = document.getElementById('modalPacienteVer');

  const modalEditar = new bootstrap.Modal(modalEditarEl);
  const modalVer    = new bootstrap.Modal(modalVerEl);

  // ================== DataTable ==================
  const tabla = $('#tablaPacientes').DataTable({
    ajax: {
      url: `${CTRL_PACIENTE}?opcion=listar`,
      type: 'get',
      dataType: 'json',
      dataSrc: function (json) {
        if (json.status === 'success') return json.data;
        console.warn(json.message || 'Sin datos');
        return [];
      },
      error: e => console.error(e.responseText)
    },
    language: { url: 'app/ajax/idioma.json' },
    dom: "<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
       "<'row'<'col-12'tr>>" +
       "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
    aaSorting: [],
    lengthMenu: [[5, 12, 18, -1], [5, 12, 18, 'Todos']],
    pageLength: 5,
    responsive: true,
    columns: [
      { data: 'nombre' },
      { data: 'fecha_nacimiento' },
      { data: 'telefono' },
      { data: 'direccion' },
      { data: 'dui' },
      { data: 'sexo' },
      { data: 'notas'},
      {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: function (row) {
          return `
            <button type="button" class="btn btn-sm btn-warning mb-2 btn-editar" data-id="${row.id_paciente}">
              <i class="bx bx-edit"></i>Editar
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_paciente}">
              <i class="bx bx-trash"></i>Eliminar
            </button>`;
        }
      }
    ]
  });

  const $modal  = $('#modalPaciente');
  const $titulo = $('#tituloPaciente');
  const $form   = $('#formPaciente');
  const $id     = $('#id_paciente');

  // ================== Helpers de validación ==================

  function calcularEdad(fechaStr) {
    const hoy = new Date();
    const fechaNac = new Date(fechaStr);

    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const m = hoy.getMonth() - fechaNac.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
      edad--;
    }
    return edad;
  }

  function validarFormularioPaciente() {
    const nombre           = $('#nombre').val().trim();
    const fecha_nacimiento = $('#fecha_nacimiento').val();
    const sexo             = $('#sexo').val();
    const telefono         = $('#telefono').val().trim();
    const dui              = $('#dui').val().trim();
    const notas            = $('#notas').val().trim();

    if (nombre.length < 5) {
      Swal.fire('Error', 'El nombre debe tener al menos 5 caracteres.', 'error');
      return false;
    }

    if (!fecha_nacimiento) {
      Swal.fire('Error', 'Debe seleccionar la fecha de nacimiento.', 'error');
      return false;
    }

    const edad = calcularEdad(fecha_nacimiento);
    if (edad < 0 || edad > 120) {
      Swal.fire('Error', 'Verifique la fecha de nacimiento (edad inválida).', 'error');
      return false;
    }

    // Regla: si es mayor o igual a 18, DUI OBLIGATORIO
    if (edad >= 18) {
      if (!dui) {
        Swal.fire('Error', 'El DUI es obligatorio para pacientes mayores de 18 años.', 'error');
        return false;
      }
    }
    // Si es menor de 18 se permite DUI vacío

    // Si hay DUI, validar formato
    if (dui && !/^[0-9]{8}-[0-9]{1}$/.test(dui)) {
      Swal.fire('Error', 'Formato de DUI inválido. Use 00000000-0.', 'error');
      return false;
    }

    // Teléfono obligatorio + formato válido
    if (!telefono) {
      Swal.fire('Error', 'Debe ingresar un número de teléfono.', 'error');
      return false;
    }

    if (!/^[0-9]{4}-[0-9]{4}$/.test(telefono)) {
      Swal.fire('Error', 'Formato de teléfono inválido. Use 0000-0000.', 'error');
      return false;
    }

    if (notas.length > 255) {
      Swal.fire('Error', 'Las notas no deben superar los 255 caracteres.', 'error');
      return false;
    }

    return true;
  }

  // Autoformato de teléfono 0000-0000
  $('#telefono').on('input', function () {
    let val = this.value.replace(/[^0-9]/g, '');
    if (val.length > 8) val = val.slice(0, 8);
    if (val.length > 4) {
      this.value = val.slice(0, 4) + '-' + val.slice(4);
    } else {
      this.value = val;
    }
  });

  // Autoformato DUI 00000000-0
  $('#dui').on('input', function () {
    let val = this.value.replace(/[^0-9]/g, '');
    if (val.length > 9) val = val.slice(0, 9);
    if (val.length > 8) {
      this.value = val.slice(0, 8) + '-' + val.slice(8);
    } else {
      this.value = val;
    }
  });

  // ================== Nuevo paciente ==================
  $('#btnNuevoPaciente').on('click', () => {
    $titulo.text('Nuevo paciente');
    $form[0].reset();
    $id.val('');
    modalEditar.show();   
  });

  // ================== Guardar / Actualizar ==================
  $form.on('submit', function (e) {
    e.preventDefault();

    if (!validarFormularioPaciente()) return;

    const id = $id.val();
    const isEdit = !!id;
    const opcion = isEdit ? 'actualizar' : 'agregar';

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_PACIENTE}?opcion=${opcion}`,
      type: 'post',
      data: fd,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (r) {
        if (r.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: r.message,
            timer: 1200,
            showConfirmButton: false
          });
          modalEditar.hide();
          tabla.ajax.reload(null, false);
        } else {
          if (r.errors && Array.isArray(r.errors)) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              html: r.errors.join('<br>')
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: r.message || 'Operación fallida'
            });
          }
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: xhr.responseText || 'Error AJAX'
        });
      }
    });
  });

  // ================== Editar paciente ==================
  $('#tablaPacientes').on('click', '.btn-editar', function (e) {
    e.preventDefault();

    const id = $(this).data('id');
    $.getJSON(`${CTRL_PACIENTE}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'No encontrado' });
        return;
      }
      const c = r.data;
      $titulo.text('Editar paciente');
      $id.val(c.id_paciente);
      $('#nombre').val(c.nombre);
      $('#fecha_nacimiento').val(c.fecha_nacimiento);
      $('#telefono').val(c.telefono);
      $('#dui').val(c.dui);
      $('#direccion').val(c.direccion);
      $('#sexo').val(c.sexo);
      $('#notas').val(c.notas);

      modalVer.hide();
      modalEditar.show();
    });
  });

  // ================== Eliminar paciente ==================
  $('#tablaPacientes').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: '¿Eliminar paciente?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;
      $.post(
        `${CTRL_PACIENTE}?opcion=eliminar`,
        { id },
        function (r) {
          if (r.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: r.message,
              timer: 1200,
              showConfirmButton: false
            });
            tabla.ajax.reload(null, false);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: r.message || 'No se pudo eliminar'
            });
          }
        },
        'json'
      );
    });
  });

  

});
