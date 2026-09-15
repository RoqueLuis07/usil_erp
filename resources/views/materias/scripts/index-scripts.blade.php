<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('materias.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.materias, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        @can('editar_materias')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        @endcan
                                        @can('ver_correlatividades_materias')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#showCorrelatividadesModal" id="show-correlatividades-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="VerCorrelatividades"><i class="ri-asterisk align-bottom"></i></button>
                                        @endcan
                                        @can('inactivar_materias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar" data-id="${value.id}"><i class="ri-close-fill align-bottom"></i></button>
                                        @endcan
                                        @can('eliminar_materias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        @endcan`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill align-bottom"></i></button>
                                        @can('editar_materias')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill align-bottom"></i></button>
                                        @endcan
                                        @can('ver_correlatividades_materias')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#showCorrelatividadesModal" id="show-correlatividades-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="VerCorrelatividades"><i class="ri-asterisk align-bottom"></i></button>
                                        @endcan
                                        @can('activar_materias')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill align-bottom"></i></button>
                                        @endcan
                                        @can('eliminar_materias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                        @endcan`
                    }

                    if (value.codigo == null) {
                        value.codigo = '';
                    }

                    $('tbody').append(
                        `<tr>
                            <td class="id">${value.id}</td>
                            <td class="codigo">${value.codigo}</td>
                            <td class="nombre_fantasia">${value.nombre_fantasia}</td>
                            <td class="nombre_real">${value.nombre_real}</td>
                            <td class="carga_horaria">${value.carga_horaria}</td>
                            <td>${value.correlativas.length}</td>
                            <td class="estado">${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['id', 'codigo', 'nombre_fantasia', 'nombre_real', 'carga_horaria'],
                    page: 50,
                    pagination: true
                };
                var materiasList = new List('materias-list', options);
                materiasList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar la nueva materia?',
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
        var url = "{{route('materias.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#codigo-show').val(response.materia.codigo);
            $('#nombre_fantasia-show').val(response.materia.nombre_fantasia);
            $('#nombre_real-show').val(response.materia.nombre_real);
            $('#carga_horaria-show').val(response.materia.carga_horaria);
            if (response.materia.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.materia.estado == 'IN') {
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

    $(document).on('click', '#show-correlatividades-btn', function () {
        var id = $(this).data('id');
        var url = "{{route('correlatividades.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#body').html('');
            var editar;
            var correlativas = response.materia.correlativas;
            var id = response.materia.id;
            if (correlativas.length > 0) {
                editar = 1;
            } else {
                editar = 0;
            }

            $('#title-materia').html(response.materia.nombre_fantasia + ' - <span class="text-muted">' + response.materia.nombre_real + '</span>');
            var body = `<div class="col-lg-12 mt-3">
                            <div class="row">
                                <div class="col-lg-12 text-center mb-3">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-info active">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                                @can('editar_correlatividades_materias')
                                                    ${editar === 1 ? '<a type="button" class="btn btn-danger me-2" id="delete-all-correlatividades-btn" data-id="' + id + '" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Todas las Correlatividades"><i class="ri-delete-bin-fill align-bottom"></i></a>' : ''}
                                                @endcan
                                                <span>Materias Correlativas</span>
                                                @can('editar_correlatividades_materias')
                                                    <a type="button" class="btn btn-warning me-2" id="edit-correlatividad-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="${editar === 1 ? 'Editar Correlatividades' : 'Agregar Correlatividades'}" data-id="${id}">${editar === 1 ? '<i class="ri-edit-fill align-bottom"></i>' : '<i class="ri-add-line align-bottom"></i>'}</a>
                                                @endcan
                                            </div>
                                        </li>`;

            if (correlativas.length > 0) {
                correlativas.forEach(function (correlativa) {
                    var nombre_fantasia = correlativa.nombre_fantasia;
                    var nombre_real = correlativa.nombre_real;
                    body += `<li class="list-group-item text-center">${nombre_fantasia} - ${nombre_real}</li>`
                })
            } else {
                body +=`<li class="list-group-item text-center">Esta materia no posee ninguna correlatividad activa.</li>`
            }

                            body += `</ul>
                                </div>
                            </div>
                        </div>`

            $('#body').append(body);
        }).fail(function(response) {

        })

    })

    $(document).on('click', '#edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        const id = $(this).data('id');
        var url = "{{route('materias.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#codigo-edit').val(response.materia.codigo);
            $('#nombre_fantasia-edit').val(response.materia.nombre_fantasia);
            $('#nombre_real-edit').val(response.materia.nombre_real);
            $('#carga_horaria-edit').val(response.materia.carga_horaria);
            $('.update-btn').val(response.materia.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('materias.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar la materia ' + response.materia.nombre_fantasia + '?');
            $('.unactivate-btn').val(response.materia.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('materias.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar la materia ' + response.materia.nombre_fantasia + '?');
            $('.activate-btn').val(response.materia.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('materias.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar la materia ' + response.materia.nombre_fantasia + '?');
            $('.delete-btn').val(response.materia.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#edit-correlatividad-btn', function () {
        var id = $(this).data('id');
        var url = "{{route('correlatividades.edit', ":id")}}"
        url = url.replace(':id', id);
        window.location.href = url;
    })

    $(document).on('click', '#delete-all-correlatividades-btn', function () {
        var nombre = $('#title-materia').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar todas las correlatividades de la materia ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = "{{route('correlatividades.destroy', ":id")}}"
                url = url.replace(':id', id);
                window.location.href = url;
            }
        })
    })

    $('.update-btn').click(function () {
        var nombre = $('#nombre_fantasia-edit').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar la materia ' + nombre + '?',
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
        var url = "{{route('materias.destroy', ":id")}}"
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
        var url = "{{route('materias.unactivate', ":id")}}"
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
        var url = "{{route('materias.activate', ":id")}}"
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
            url: '{{route('materias.store')}}',
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
        var url = "{{route('materias.update', ":id")}}"
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
