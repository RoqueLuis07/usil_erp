<script type="module">
    //Inicio formatos para CleaveJS
    const formatoCodigo = {
        blocks: [3, 3, 0],
        delimiter: '-',
        numericOnly: true
    };

    const formatoNumero = {
        blocks: [7],
        numericOnly: true
    };

    var codigo_edit = new Cleave('#codigo', formatoCodigo);
    var desde_edit = new Cleave('#numero_desde', formatoNumero);
    var hasta_edit = new Cleave('#numero_hasta', formatoNumero);

    $(document).ready(function() {
        fetch();

        new Cleave('#codigo', formatoCodigo);
        new Cleave('#numero_desde', formatoNumero);
        new Cleave('#numero_hasta', formatoNumero);
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('puntos_impresiones.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.puntos_impresiones, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        {{-- @can('editar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        {{-- @endcan --}}
                                        {{-- @can('inactivar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar" data-id="${value.id}"><i class="ri-close-fill align-bottom"></i></button>
                                        {{-- @endcan --}}
                                        {{-- @can('eliminar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        {{-- @endcan --}}`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        {{-- @can('editar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        {{-- @endcan --}}
                                        {{-- @can('activar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill align-bottom"></i></button>
                                        {{-- @endcan --}}
                                        {{-- @can('eliminar puntos de impresiones') --}}
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        {{-- @endcan --}}`
                    }
                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="nombre">${value.nombre}</td>
                            <td class="codigo">${value.codigo}</td>
                            <td class="tipo_documento">${value.tipo_documento.nombre}</td>
                            <td class="timbrado">${value.timbrado.numero}</td>
                            <td>${value.numero_desde}</td>
                            <td>${value.numero_hasta}</td>
                            <td>${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['nombre', 'codigo', 'tipo_documento', 'timbrado'],
                    page: 50,
                    pagination: true
                };
                var puntos_impresionesList = new List('puntos_impresiones-list', options);
                puntos_impresionesList.on('updated', function(list) {
                    if (list.matchingItems.length > 0) {
                        $('.noresults').hide()
                    } else {
                        $('.noresults').show()
                    }
                });
            }
        });
    }

    if (document.querySelector(".pagination-next"))
    document.querySelector(".pagination-next").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : '' : '';
    });

    if (document.querySelector(".pagination-prev"))
    document.querySelector(".pagination-prev").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : '' : '';
    });

    if ($('#success-message').val() != null) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        })
        Toast.fire({
            icon: 'success',
            text: $('#success-message').val(),
        })
    };

    if ($('#error-message').val() != null) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        })
        Toast.fire({
            icon: 'error',
            text: $('#error-message').val(),
        })
    };

    function message(message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        })
        Toast.fire({
            icon: type,
            text: message,
        })
    }

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $(document).on('click', '#add-btn', function () {
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
    })

    $('#save-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el nuevo punto de impresión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                save();
            }
        })
    });

    $(document).on('click', '#show-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('puntos_impresiones.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre-show').val(response.punto_impresion.nombre);
            $('#codigo-show').val(response.punto_impresion.codigo);
            $('#tipo_documento-show').val(response.punto_impresion.tipo_documento.nombre);
            $('#timbrado-show').val(response.punto_impresion.timbrado.numero);
            $('#numero_desde-show').val(response.punto_impresion.numero_desde);
            $('#numero_hasta-show').val(response.punto_impresion.numero_hasta);
            if (response.punto_impresion.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.punto_impresion.estado == 'IN') {
                $('#estado-show').val('INACTIVO');
            }
            $('#span-cargado').html('');
            $('#span-actualizado').html('');
            $('#actualizado').prop('hidden', true);

            var cargado = `${response.cargado.name}, en fecha ${response.created_at}`
            $('#span-cargado').html(cargado);
            if (response.actualizado != 0) {
                $('#actualizado').prop('hidden', false);
                var actualizado = `${response.actualizado.name}, en fecha ${response.updated_at}`
                $('#span-actualizado').html(actualizado);
            }
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        const id = $(this).data('id');
        var url = "{{route('puntos_impresiones.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre-edit').val(response.punto_impresion.nombre);
            $('#codigo-edit').val(response.punto_impresion.codigo);
            $('#tipo_documento-edit').selectpicker('destroy');
            $('#tipo_documento-edit').empty();
            $('#tipo_documento-edit').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.tipos_documentos, function (index, value) {
                if (value.id == response.punto_impresion.tipo_documento.id) {
                    $('#tipo_documento-edit').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                } else {
                    $('#tipo_documento-edit').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            })
            $('#tipo_documento-edit').selectpicker('render');

            $('#timbrado-edit').selectpicker('destroy');
            $('#timbrado-edit').empty();
            $('#timbrado-edit').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.timbrados, function (index, value) {
                if (value.id == response.punto_impresion.timbrado.id) {
                    $('#timbrado-edit').append('<option value="' + value.id + '" selected>' + value.numero + '</option>');
                } else {
                    $('#timbrado-edit').append('<option value="' + value.id + '">' + value.numero + '</option>');
                }
            })
            $('#timbrado-edit').selectpicker('render');
            $('#numero_desde-edit').val(response.punto_impresion.numero_desde);
            $('#numero_hasta-edit').val(response.punto_impresion.numero_hasta);
            $('.update-btn').val(response.punto_impresion.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('puntos_impresiones.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar el punto de impresión ' + response.punto_impresion.nombre + '?');
            $('.unactivate-btn').val(response.punto_impresion.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('puntos_impresiones.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar el punto de impresión ' + response.punto_impresion.nombre + '?');
            $('.activate-btn').val(response.punto_impresion.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('puntos_impresiones.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar el punto de impresión ' + response.punto_impresion.nombre + '?');
            $('.delete-btn').val(response.punto_impresion.id)
        }).fail(function(response) {

        })
    });

    $('.update-btn').click(function () {
        var nombre = $('#nombre-edit').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el punto de impresión ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).val();
                update(id);
            }
        })
    });

    $('.delete-btn').click(function (e) {
        e.preventDefault();
        var id = $(this).val();
        const formData = new FormData(document.getElementById('destroy-form'));
        var url = "{{route('puntos_impresiones.destroy', ":id")}}"
        url = url.replace(':id', id);
        var type = 'error';
        $.ajax({
            url: url,
            data: formData,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#destroyModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {

        })
    });

    $('.unactivate-btn').click(function (e) {
        e.preventDefault();
        var id = $(this).val();
        const formData = new FormData(document.getElementById('unactivate-form'));
        var url = "{{route('puntos_impresiones.unactivate', ":id")}}"
        url = url.replace(':id', id);
        var type = 'success';
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#unactivateModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {

        })
    });

    $('.activate-btn').click(function (e) {
        e.preventDefault();
        var id = $(this).val();
        const formData = new FormData(document.getElementById('activate-form'));
        var url = "{{route('puntos_impresiones.activate', ":id")}}"
        url = url.replace(':id', id);
        var type = 'success';
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#activateModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {

        })
    });

    function save() {
        const formData = new FormData(document.getElementById('store-form'));
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: '{{route('puntos_impresiones.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#store-form').trigger('reset');
            $('#createModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    function update(id) {
        const formData = new FormData(document.getElementById('update-form'));
        var url = "{{route('puntos_impresiones.update', ":id")}}"
        url = url.replace(':id', id);
        var type = 'success';
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#update-form').trigger('reset');
            $('#editModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }
</script>
