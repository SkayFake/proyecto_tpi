const CTRL_PACIENTE = 'app/controllers/pacienteController.php';

$(document).ready(function () {

  const modalEditarEl = document.getElementById('modalPaciente');
  const modalVerEl    = document.getElementById('modalPacienteVer');

  const modalEditar = new bootstrap.Modal(modalEditarEl);
  const modalVer    = new bootstrap.Modal(modalVerEl);

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
            <button type="button" class="btn btn-sm btn-warning me-1 btn-editar" data-id="${row.id_paciente}">
              <i class="bx bx-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_paciente}">
              <i class="bx bx-trash"></i>
            </button>`;
        }
      }
    ]
  });

  const $modal  = $('#modalPaciente');
  const $titulo = $('#tituloPaciente');
  const $form   = $('#formPaciente');
  const $id     = $('#id_paciente'); 

  $('#btnNuevoPaciente').on('click', () => {
    $titulo.text('Nuevo paciente');
    $form[0].reset();
    $id.val('');
    modalEditar.show();   
  });

  $form.on('submit', function (e) {
    e.preventDefault();

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
        // console.log(r); 
        if (r.status === 'success') {
          Swal.fire({ icon: 'success', title: r.message, timer: 1200, showConfirmButton: false });
          modalEditar.hide();
          tabla.ajax.reload(null, false);
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: r.message || 'Operación fallida' });
        }
      },
      error: function (xhr) {
        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText || 'Error AJAX' });
      }
    });
  });

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
      $.post(`${CTRL_PACIENTE}?opcion=eliminar`, { id }, function (r) {
        if (r.status === 'success') {
          Swal.fire({ icon: 'success', title: r.message, timer: 1200, showConfirmButton: false });
          tabla.ajax.reload(null, false);
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: r.message || 'No se pudo eliminar' });
        }
      }, 'json');
    });
  });

});
