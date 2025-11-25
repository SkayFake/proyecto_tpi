const CTRL_ODONTOGRAMA = 'app/controllers/odontogramaController.php';

$(document).ready(function () {

  let volverALaListaDesdeImagen = false;
  let volverALaListaDesdeEliminar = false;
  let volverALaListaDesdeEditar = false;

  let indiceFilaEditando = null;

  const $form        = $('#formOdontograma');
  const $idOdonto    = $('#id_odontograma');
  const $idPaciente  = $('#id_paciente');
  const $nombrePac   = $('#name_paciente').length ? $('#name_paciente') : $('#nombre_paciente');
  const $obs         = $('#observaciones');
  const $imagen      = $('#imagen');
  const $preview     = $('#previewFoto');
  const $rmFlag      = $('#borrar_foto');
  const $rmBtn       = $('#btnBorrarFoto');

  const modalForm   = new bootstrap.Modal(document.getElementById('modalOdontograma'));
  const modalLista  = new bootstrap.Modal(document.getElementById('modalOdontogramaVer'));
  const modalImagen = new bootstrap.Modal(document.getElementById('modalVerImagen'));

  

  function limpiarFormularioCompleto() {
    $form[0].reset();
    $idOdonto.val('');
    $idPaciente.val('');
    $rmFlag.val('0');
    $preview.attr('src', '').addClass('d-none');
    $rmBtn.prop('disabled', true);
    
  }

  function validarFormularioOdontograma() {

    if (!$nombrePac.val() || !$nombrePac.val().trim()) {
      $nombrePac.addClass('is-invalid');
      Swal.fire('Dato requerido', 'El nombre del paciente es obligatorio', 'warning');
      return false;
    }

    if (!$idPaciente.val()) {
      Swal.fire('Paciente no asignado', 'Debe asignar un paciente válido', 'warning');
      return false;
    }

    const f = $imagen[0].files[0];
    if (f) {
      const okType = ['image/jpeg', 'image/png'].includes(f.type);
      if (!okType) {
        Swal.fire('Imagen inválida', 'Solo se permiten imágenes JPG o PNG', 'warning');
        return false;
      }
      if (f.size > 2 * 1024 * 1024) {
        Swal.fire('Archivo muy grande', 'La imagen no debe superar los 2 MB', 'warning');
        return false;
      }
    }

    return true;
  }

 
  let tabla = $('#tablaOdontogramas').DataTable({
    ajax: {
      url: `${CTRL_ODONTOGRAMA}?opcion=listar`,
      dataSrc: json => {
        console.log('Respuesta listar odontograma:', json);
        return (json && json.status === 'success') ? json.data : [];
      },
      error: function (xhr, status, error) {
        console.error('Error AJAX listar odontograma:', status, error);
        console.error('Respuesta del servidor:', xhr.responseText);
      }
    },
    language: { url: 'app/ajax/idioma.json' },
    responsive: true,
    aaSorting: [],
    columns: [
      { data: 'name_paciente', title: 'Paciente' },
      {
        data: null,
        title: 'Odontograma',
        className: 'text-center',
        render: row => {
          const tiene = parseInt(row.tiene_foto, 10) === 1;
          return tiene
            ? '<span class="badge bg-success">Con imagen</span>'
            : '<span class="badge bg-secondary">Sin imagen</span>';
        }
      },
      {
        data: 'observaciones',
        title: 'Observación médica',
        render: v => v
          ? `<span class="small">${v}</span>`
          : '<span class="text-muted">—</span>'
      },
      {
        data: null,
        title: 'Acciones',
        className: 'text-center',
        render: row => `
          <button class="btn btn-sm btn-outline-primary btn-verimg me-1" data-id="${row.id_odontograma}">
            <i class="bi bi-image"></i>
          </button>
          <button class="btn btn-sm btn-warning btn-editar me-1" data-id="${row.id_odontograma}">
            Editar
          </button>
          <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_odontograma}">
            Eliminar
          </button>`
      }
    ]
  });


  $('#btnNuevoOdontograma, .btn-nuevo-odontograma').on('click', function () {
    const idPac  = $(this).data('id_paciente')   || '';
    const nomPac = $(this).data('name_paciente') || '';

    limpiarFormularioCompleto();
    $('#tituloOdontograma').text('Nuevo odontograma');

    indiceFilaEditando = null; 

    if (idPac)  $idPaciente.val(idPac);
    if (nomPac) $nombrePac.val(nomPac);

    modalForm.show();
  });

  $('#btnVerOdontogramas').on('click', function () {
    tabla.ajax.reload(null, false);
    modalLista.show();
  });

  
  $form.on('submit', function (e) {
    e.preventDefault();

    const isEdit = $idOdonto.val() !== "";
    const opcion = isEdit ? 'actualizar' : 'agregar'; 

    if (!validarFormularioOdontograma()) return;

    const fd = new FormData(this);
    const tieneNuevaFoto = !!$imagen[0].files[0];
    const borrarFoto     = $rmFlag.val() === '1';

    $.ajax({
      url: `${CTRL_ODONTOGRAMA}?opcion=${opcion}`,
      type: 'post',
      data: fd,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (r) {
        if (r.status === 'success') {
          Swal.fire('Éxito', r.message || 'Odontograma guardado', 'success');
          modalForm.hide();

          if (isEdit && indiceFilaEditando !== null) {
            
            const rowData = tabla.row(indiceFilaEditando).data();
            if (rowData) {
              rowData.name_paciente = $nombrePac.val();
              rowData.observaciones = $obs.val();
              if (borrarFoto) {
                rowData.tiene_foto = 0;
              } else if (tieneNuevaFoto) {
                rowData.tiene_foto = 1;
              }
              tabla.row(indiceFilaEditando).data(rowData).draw(false);
            } else {
              tabla.ajax.reload(null, false);
            }
          } else {
            tabla.ajax.reload(null, false);
          }

        } else {
          Swal.fire('Error', r.message || 'No se pudo guardar', 'error');
        }
      },
      error: function (xhr) {
        Swal.fire('Error', xhr.responseText || 'Error en la petición', 'error');
      }
    });
  });

 
  $('#tablaOdontogramas').on('click', '.btn-editar', function () {
    const id = $(this).data('id');

    const $tr = $(this).closest('tr');
    indiceFilaEditando = tabla.row($tr).index();

    limpiarFormularioCompleto();
    $('#tituloOdontograma').text('Editar odontograma');


    if ($('#modalOdontogramaVer').hasClass('show')) {
      volverALaListaDesdeEditar = true;
      modalLista.hide();
    } else {
      volverALaListaDesdeEditar = false;
    }

    modalForm.show();

    $.getJSON(`${CTRL_ODONTOGRAMA}?opcion=obtener&id=${id}`, function (r) {
      if (r.status !== 'success') {
        Swal.fire('Error', 'No se encontró el odontograma', 'error');
        return;
      }

      const o = r.data;

      $idOdonto.val(o.id_odontograma);
      $idPaciente.val(o.id_paciente);
      $nombrePac.val(o.name_paciente|| '');
      $obs.val(o.observaciones || '');

      if (parseInt(o.tiene_foto, 10) === 1) {
        $preview
          .attr('src', `${CTRL_ODONTOGRAMA}?opcion=foto&id=${o.id_odontograma}`)
          .removeClass('d-none');
        $rmBtn.prop('disabled', false);
      }
    }).fail(function (xhr, status, error) {
      console.error('Error AJAX obtener odontograma:', status, error, xhr.responseText);
      Swal.fire('Error', 'No se pudo cargar el odontograma', 'error');
    });
  });

  $('#modalOdontograma').on('hidden.bs.modal', function () {
    if (volverALaListaDesdeEditar) {
      modalLista.show();
      volverALaListaDesdeEditar = false;
    }
  });


  $('#tablaOdontogramas').on('click', '.btn-eliminar', function () {
    const id = $(this).data('id'); 
    
    Swal.fire({
      title: '¿Eliminar odontograma?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then(res => {
      if (!res.isConfirmed) return;

      $.post(`${CTRL_ODONTOGRAMA}?opcion=eliminar`, 
        { id_odontograma: id }, 
        function (r) {
          if (r.status === 'success') {
            Swal.fire('Eliminado', r.message || 'Odontograma eliminado', 'success');
            tabla.ajax.reload(null, false);
          } else {
            Swal.fire('Error', r.message || 'No se pudo eliminar', 'error');
          }
        }, 'json')
        .fail(function(xhr) {
          console.error('Error al eliminar:', xhr.responseText);
          Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        });
    });
  });

 
  $('#tablaOdontogramas').on('click', '.btn-verimg', function () {
    const id = $(this).data('id');


    $('#imagenOdontogramaModal')
      .attr('src', `${CTRL_ODONTOGRAMA}?opcion=foto&id=${id}`);

    if ($('#modalOdontogramaVer').hasClass('show')) {
      volverALaListaDesdeImagen = true;
      modalLista.hide();
    } else {
      volverALaListaDesdeImagen = false;
    }

    modalImagen.show();
  });

  $('#modalVerImagen').on('hidden.bs.modal', function () {
    if (volverALaListaDesdeImagen) {
      modalLista.show();
      volverALaListaDesdeImagen = false;
    }
  });

  $imagen.on('change', function () {
    const f = this.files[0];

    if (!f) {
      $preview.attr('src', '').addClass('d-none');
      $rmFlag.val('0');
      $rmBtn.prop('disabled', true);
      return;
    }

    const okType = ['image/jpeg', 'image/png'].includes(f.type);
    if (!okType) {
      Swal.fire('Imagen inválida', 'Solo JPG o PNG', 'warning');
      $(this).val('');
      return;
    }
    if (f.size > 2 * 1024 * 1024) {
      Swal.fire('Archivo muy grande', 'Máximo 2 MB', 'warning');
      $(this).val('');
      return;
    }

    const reader = new FileReader();
    reader.onload = e => {
      $preview.attr('src', e.target.result).removeClass('d-none');
      $rmFlag.val('0');
      $rmBtn.prop('disabled', false);
    };
    reader.readAsDataURL(f);
  });

  $rmBtn.on('click', function () {
    $rmFlag.val('1');
    $preview.attr('src', '').addClass('d-none');
    $imagen.val('');
    $(this).prop('disabled', true);
  });


  $nombrePac.on('blur', function () {
  const nombre = $(this).val().trim();
  if (nombre === "") return;

  $.getJSON(
    `${CTRL_ODONTOGRAMA}?opcion=buscar_paciente_nombre&nombre=${encodeURIComponent(nombre)}`,
    function (r) {

      if (r.status === 'success' && r.data) {
        $idPaciente.val(r.data.id_paciente);
    
      } else {
        $idPaciente.val('');
      }

    }
  );
});



});
