<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('examenes_suficiencias_fechas_solicitudes.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.examenes_suficiencias_fechas_solicitudes, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        @can('editar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        @endcan
                                        @can('inactivar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar" data-id="${value.id}"><i class="ri-close-fill align-bottom"></i></button>
                                        @endcan
                                        @can('eliminar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        @endcan`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        @can('editar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        @endcan
                                        @can('activar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill align-bottom"></i></button>
                                        @endcan
                                        @can('eliminar_fechas_solicitudes_examenes_suficiencia')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        @endcan`
                    }

                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="semestre">${value.semestre.nombre}</td>
                            <td class="programa">${value.programa.nombre}</td>
                            <td class="fecha_inicio">${value.fecha_inicio}</td>
                            <td class="fecha_fin">${value.fecha_fin}</td>
                            <td>${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['semestre', 'programa', 'fecha_inicio', 'fecha_fin'],
                    page: 50,
                    pagination: true
                };
                var examenes_suficiencias_fechas_solicitudesList = new List('examenes_suficiencias_fechas_solicitudes-list', options);
                examenes_suficiencias_fechas_solicitudesList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar la nueva fecha de desmatriculación?',
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#semestre-show').val(response.examen_suficiencia_fecha_solicitud.semestre.nombre);
            $('#programa-show').val(response.examen_suficiencia_fecha_solicitud.programa.nombre);
            $('#fecha_inicio-show').val(response.examen_suficiencia_fecha_solicitud.fecha_inicio);
            $('#fecha_fin-show').val(response.examen_suficiencia_fecha_solicitud.fecha_fin);
            if (response.examen_suficiencia_fecha_solicitud.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.examen_suficiencia_fecha_solicitud.estado == 'IN') {
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#semestre-edit').selectpicker('destroy');
            $('#semestre-edit').val(response.examen_suficiencia_fecha_solicitud.semestre_id);
            $('#semestre-edit').selectpicker('render');
            $('#programa-edit').selectpicker('destroy');
            $('#programa-edit').val(response.examen_suficiencia_fecha_solicitud.programa_id);
            $('#programa-edit').selectpicker('render');
            $('#fecha_inicio-edit').val(response.examen_suficiencia_fecha_solicitud.fecha_inicio);
            $('#fecha_fin-edit').val(response.examen_suficiencia_fecha_solicitud.fecha_fin);
            $('.update-btn').val(response.examen_suficiencia_fecha_solicitud.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar la fecha de solicitud de examenes de suficiencia del semestre ' + response.examen_suficiencia_fecha_solicitud.semestre.nombre + ' del programa ' + response.examen_suficiencia_fecha_solicitud.programa.nombre + '?');
            $('.unactivate-btn').val(response.examen_suficiencia_fecha_solicitud.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar la fecha de solicitud de examenes de suficiencia del semestre ' + response.examen_suficiencia_fecha_solicitud.semestre.nombre + ' del programa ' + response.examen_suficiencia_fecha_solicitud.programa.nombre + '?');
            $('.activate-btn').val(response.examen_suficiencia_fecha_solicitud.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar la fecha de solicitud de examenes de suficiencia del semestre ' + response.examen_suficiencia_fecha_solicitud.semestre.nombre + ' del programa ' + response.examen_suficiencia_fecha_solicitud.programa.nombre + '?');
            $('.delete-btn').val(response.examen_suficiencia_fecha_solicitud.id)
        }).fail(function(response) {

        })
    });

    $('.update-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar la fecha de solicitud de examenes de suficiencia?',
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.destroy', ":id")}}"
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.unactivate', ":id")}}"
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.activate', ":id")}}"
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
            url: '{{route('examenes_suficiencias_fechas_solicitudes.store')}}',
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
        var url = "{{route('examenes_suficiencias_fechas_solicitudes.update', ":id")}}"
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
