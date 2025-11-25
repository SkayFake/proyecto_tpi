const CTRL_SERVICIO = 'app/controllers/servicioController.php';

$(document).ready(function () {

 
  function limpiarErroresFormulario() {
    $('#nombre_servicio, #precio_base, #duracion_dias, #fecha_vencimiento')
      .removeClass('is-invalid');
  }

  function validarFormularioServicio(isEdit) {
    limpiarErroresFormulario();

    const nombre = $('#nombre_servicio').val().trim();
    const precio = parseFloat($('#precio_base').val());
    const duracion = $('#duracion_dias').val();

    if (nombre === '') {
      $('#nombre_servicio').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Campo requerido',
        text: 'Debe ingresar el nombre del servicio.'
      });
      return false;
    }

    if (isNaN(precio) || precio <= 0) {
      $('#precio_base').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Precio inválido',
        text: 'Ingrese un precio mayor a 0.'
      });
      return false;
    }

    if (duracion !== '' && isNaN(parseInt(duracion))) {
      $('#duracion_dias').addClass('is-invalid');
      Swal.fire({
        icon: 'warning',
        title: 'Duración inválida',
        text: 'La duración debe ser un número.'
      });
      return false;
    }

    return true;
  }


  const modalEditarEl = document.getElementById('modalServicio');
  const modalVerEl    = document.getElementById('modalServiciosVer');

  const modalEditar = new bootstrap.Modal(modalEditarEl);
  const modalVer    = new bootstrap.Modal(modalVerEl);



  const tabla = $('#tablaServicios').DataTable({
    ajax: {
      url: `${CTRL_SERVICIO}?opcion=listar`,
      type: 'get',
      dataType: 'json',
      dataSrc: function (json) {
        if (json.status === 'success') return json.data;
        console.warn(json.message || 'Sin datos');
        return [];
      }
    },
    language: { url: 'app/ajax/idioma.json' },
    dom: "<'row mb-3'<'col-md-6'l><'col-md-6 text-end'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
    aaSorting: [],
    lengthMenu: [[5, 12, 18, -1], [5, 12, 18, 'Todos']],
    pageLength: 5,
    responsive: true,

    rowCallback: function (row, data) {
      if (!data.fecha_vencimiento) return;

      const ahora = new Date();
      const fechaVenc = new Date(data.fecha_vencimiento);
      const diffHoras = (fechaVenc - ahora) / (1000 * 60 * 60);

      if (fechaVenc < ahora) {
        $(row).addClass('table-danger'); 
      } else if (diffHoras <= 48) {
        $(row).addClass('table-warning');
      }
    },

    columns: [
      { data: 'nombre_servicio' },
      { 
        data: 'precio_base',
        render: d => `$${parseFloat(d).toFixed(2)}`
      },
      { 
        data: 'descripcion',
        render: d => d ? d : '<em class="text-muted">Sin descripción</em>'
      },
      { 
        data: 'activo',
        className: 'text-center',
        render: function (data, type, row) {
          const badgeClass = data == 1 ? 'bg-success' : 'bg-danger';
          const badgeText  = data == 1 ? 'Activo' : 'Inactivo';
          const checked    = data == 1 ? 'checked' : '';

          return `
            <div class="d-inline-flex align-items-center gap-2">
              <span class="badge ${badgeClass} mb-0">${badgeText}</span>
              <div class="form-check form-switch m-0">
                <input class="form-check-input switch-estado"
                       type="checkbox"
                       data-id="${row.id_servicio}"
                       ${checked}>
              </div>
            </div>
          `;
        }
      },
      { data: 'created_at' },

      { 
        data: 'fecha_vencimiento',
        render: function (d) {
          if (!d)
            return '<span class="badge bg-secondary">Sin vencimiento</span>';

          const ahora = new Date();
          const fecha = new Date(d);
          const diffHoras = (fecha - ahora) / (1000 * 60 * 60);

          if (fecha < ahora) {
            return `<span class="badge bg-danger">VENCIDO</span><br><small>${fecha.toLocaleString()}</small>`;
          }

          if (diffHoras <= 48) {
            return `<span class="badge bg-warning text-dark">Por vencer</span><br><small>${fecha.toLocaleString()}</small>`;
          }

          return `<span class="badge bg-info text-dark">Vigente</span><br><small>${fecha.toLocaleString()}</small>`;
        }
      },

      {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: function (row) {
          return `
            <button type="button" class="btn btn-sm btn-warning mb-2 btn-editar" data-id="${row.id_servicio}">
              <i class="bx bx-edit"></i> Editar
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_servicio}">
              <i class="bx bx-trash"></i> Eliminar
            </button>
          `;
        }
      }
    ]
  });


  $('#btnVerServicios').on('click', function () {
    $('#tituloServiciosVer').text('Listado de Servicios');
    tabla.ajax.reload(null, false);
    modalVer.show();
  });

  $('#btnNuevoServicio').on('click', () => {
    $('#tituloServicio').text('Nuevo Servicio');
    $('#formServicio')[0].reset();
    $('#id_servicio').val('');
    limpiarErroresFormulario();
    modalEditar.show();
  });

 
  $('#formServicio').on('submit', function (e) {
    e.preventDefault();

    const id = $('#id_servicio').val();
    const isEdit = !!id;
    const opcion = isEdit ? 'actualizar' : 'agregar';

    if (!validarFormularioServicio(isEdit)) return;

    const fd = new FormData(this);

    $.ajax({
      url: `${CTRL_SERVICIO}?opcion=${opcion}`,
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
            text: r.message
          });
        }
      }
    });
  });

  $('#tablaServicios').on('click', '.btn-editar', function () {
    const id = $(this).data('id');

    $.getJSON(`${CTRL_SERVICIO}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'No encontrado' });
        return;
      }

      const c = r.data;

      $('#tituloServicio').text('Editar Servicio');
      $('#id_servicio')

        .val(c.id_servicio);
      $('#nombre_servicio').val(c.nombre_servicio);
      $('#precio_base').val(c.precio_base);
      $('#descripcion').val(c.descripcion);
      $('#activo').val(c.activo);

      if (c.fecha_vencimiento) {
        const f = new Date(c.fecha_vencimiento);
        $('#fecha_vencimiento').val(f.toISOString().slice(0, 16));
      } else {
        $('#fecha_vencimiento').val('');
      }

      modalVer.hide();
      modalEditar.show();
    });
  });


  $('#tablaServicios').on('change', '.switch-estado', function () {
    const id = $(this).data('id');
    const nuevoEstado = this.checked ? 1 : 0;
    const switchEl = this;

    if (nuevoEstado === 1) {
      actualizarEstadoServicio(id, nuevoEstado, switchEl);
      return;
    }

    Swal.fire({
      title: '¿Desactivar servicio?',
      text: 'El servicio quedará inactivo.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, desactivar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (res.isConfirmed) {
        actualizarEstadoServicio(id, nuevoEstado, switchEl);
      } else {
        $(switchEl).prop('checked', true);
      }
    });
  });

  function actualizarEstadoServicio(id, estado, switchEl) {
    $.post(
      `${CTRL_SERVICIO}?opcion=actualizar`,
      { id_servicio: id, activo: estado },
      function (r) {
        if (r.status !== 'success') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: r.message
          });
          $(switchEl).prop('checked', estado === 1 ? false : true);
          tabla.ajax.reload(null, false);
        }
      },
      'json'
    );
  }

  $('#tablaServicios').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: '¿Eliminar servicio?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(
        `${CTRL_SERVICIO}?opcion=eliminar`,
        { id_servicio: id },
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
