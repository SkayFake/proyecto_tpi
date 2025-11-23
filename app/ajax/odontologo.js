const CTRL_ODONTOLOGO = 'app/controllers/odontologoController.php';

$(document).ready(function () {

  // abrrir y cerrar modal para editar
  const modalEditarEl = document.getElementById('modalOdontologo');
  const modalVerEl    = document.getElementById('modalOdontologoVer');

  const modalEditar = new bootstrap.Modal(modalEditarEl);
  const modalVer    = new bootstrap.Modal(modalVerEl);


  const tabla = $('#tablaOdontologos').DataTable({
    ajax: {
      url: `${CTRL_ODONTOLOGO}?opcion=listar`,
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
      { data: 'correo' },
      { data: 'telefono' },
      { data: 'especialidad' },
      { 
        data: 'es_admin',
        className: 'text-center',
        render: function (data) {
          return data == 1 
            ? '<span class="badge bg-primary">Administrador</span>' 
            : '<span class="badge bg-secondary">Odontologo</span>';
        }
      },
      { 
  data: 'estado',
  className: 'text-center',
  render: function (data, type, row) {
    const isActive   = data == 1;
    const badgeClass = isActive ? 'bg-success' : 'bg-danger';
    const badgeText  = isActive ? 'Activo' : 'Inactivo';
    const checked    = isActive ? 'checked' : '';

    return `
      <div class="d-inline-flex align-items-center gap-2">
        <span class="badge ${badgeClass} mb-0">${badgeText}</span>

        <div class="form-check form-switch m-0">
          <input class="form-check-input switch-estado"
                 type="checkbox"
                 data-id="${row.id_odontologo}"
                 ${checked}>
        </div>
      </div>
    `;
  }
},

      {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: function (row) {
          return `
            <button type="button" class="btn btn-sm btn-warning mb-2 btn-editar" data-id="${row.id_odontologo}">
              <i class="bx bx-edit"></i>Editar
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_odontologo}">
              <i class="bx bx-trash"></i>Eliminar
            </button>`;
        }
      }
    ]
  });

  const $form   = $('#formOdontologo');
  const $id     = $('#id_odontologo');
  const $titulo = $('#tituloOdontologo');

  $('#btnVerOdontologos').on('click', function () {
    $titulo.text('Listado de odontólogos');
    tabla.ajax.reload(null, false);
    modalVer.show();
  });

  $('#btnNuevoOdontologo').on('click', () => {
    $titulo.text('Nuevo odontólogo');
    $form[0].reset();
    $id.val('');

    $('#password').closest('.col-lg-6').show();
    $('#password').prop('required', true).val('');

    modalEditar.show();
  });

  $form.on('submit', function (e) {
    e.preventDefault();

    const id     = $id.val();
    const isEdit = !!id;
    const opcion = isEdit ? 'actualizar' : 'agregar';

    const fd = new FormData(this);

    if (fd.has('nombreOdontologo')) {
      fd.set('nombre', fd.get('nombreOdontologo'));
      fd.delete('nombreOdontologo');
    }

    if (fd.has('telefonoOdontologo')) {
      fd.set('telefono', fd.get('telefonoOdontologo'));
      fd.delete('telefonoOdontologo');
    }

    const pwd = fd.get('password');
    if (!isEdit || (pwd && pwd.trim() !== '')) {
      fd.set('contrasenia', pwd);
    }
    fd.delete('password');

    $.ajax({
      url: `${CTRL_ODONTOLOGO}?opcion=${opcion}`,
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
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: r.message || 'Operación fallida'
          });
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

  $('#tablaOdontologos').on('click', '.btn-editar', function (e) {
    e.preventDefault();

    const id = $(this).data('id');

    $.getJSON(`${CTRL_ODONTOLOGO}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'No encontrado' });
        return;
      }

      const c = r.data;

      $titulo.text('Editar odontólogo');
      $id.val(c.id_odontologo);
      $('#nombreOdontologo').val(c.nombre);
      $('#correo').val(c.correo);
      $('#telefonoOdontologo').val(c.telefono);
      $('#especialidad').val(c.especialidad);
      $('#es_admin').val(c.es_admin);
      $('#estado').val(c.estado);
      $('#password').closest('.col-lg-6').hide();
      $('#password').prop('required', false).val('');

      modalVer.hide();
      modalEditar.show();
    });
  });

  $('#tablaOdontologos').on('click', '.btn-estado', function () {
    const id   = $(this).data('id');
    const next = $(this).data('next');
    
    Swal.fire({
      title: '¿Cambiar estado?',
      text: next == 1 ? 'Se activará este odontólogo' : 'Se desactivará este odontólogo',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, cambiar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_ODONTOLOGO}?opcion=estado`,
        { id, estado: next },
        function (r) {
          if (r.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: r.message || 'Estado actualizado',
              timer: 1000,
              showConfirmButton: false
            });
            tabla.ajax.reload(null, false);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: r.message || 'No se pudo cambiar el estado'
            });
          }
        },
        'json'
      );
    });
  });

  $('#tablaOdontologos').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: '¿Eliminar odontólogo?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_ODONTOLOGO}?opcion=eliminar`,
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
