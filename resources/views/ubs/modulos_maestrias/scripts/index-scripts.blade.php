<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('modulos_maestrias.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.modulos, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></button>
                                        @endcan
                                        @can('ver_correlatividades_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#showCorrelatividadesModal" id="show-correlatividades-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Correlatividades"><i class="ri-asterisk align-bottom"></i></button>
                                        @endcan
                                        @can('inactivar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                        @endcan
                                        @can('eliminar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}"><i class="ri-edit-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"></i></button>
                                        @endcan
                                        @can('ver_correlatividades_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#showCorrelatividadesModal" id="show-correlatividades-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="VerCorrelatividades"><i class="ri-asterisk align-bottom"></i></button>
                                        @endcan
                                        @can('activar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}"><i class="ri-close-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"></i></button>
                                        @endcan
                                        @can('eliminar_modulos_maestrias_ubs')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    }
                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="nombre_fantasia">${value.nombre_fantasia}</td>
                            <td class="nombre_real">${value.nombre_real}</td>
                            <td class="codigo">${value.codigo}</td>
                            <td class="carga_horaria">${value.carga_horaria}</td>
                            <td>${value.correlativas.length}</td>
                            <td class="estado">${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['nombre_fantasia', 'nombre_real', 'codigo', 'carga_horaria'],
                    page: 50,
                    pagination: true
                };
                var modulosList = new List('modulos-list', options);
                modulosList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar el nuevo módulo?',
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
        var url = "{{route('modulos_maestrias.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre_fantasia-show').val(response.modulo.nombre_fantasia);
            $('#nombre_real-show').val(response.modulo.nombre_real);
            $('#codigo-show').val(response.modulo.codigo);
            $('#carga_horaria-show').val(response.modulo.carga_horaria);
            if (response.modulo.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.modulo.estado == 'IN') {
                $('#estado-show').val('INACTIVO');
            }
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        const id = $(this).data('id');
        var url = "{{route('modulos_maestrias.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre_fantasia-edit').val(response.modulo.nombre_fantasia);
            $('#nombre_real-edit').val(response.modulo.nombre_real);
            $('#codigo-edit').val(response.modulo.codigo);
            $('#carga_horaria-edit').val(response.modulo.carga_horaria);
            $('.update-btn').val(response.modulo.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('modulos_maestrias.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar el módulo ' + response.modulo.nombre_fantasia + '?');
            $('.unactivate-btn').val(response.modulo.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('modulos_maestrias.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar el módulo ' + response.modulo.nombre_fantasia + '?');
            $('.activate-btn').val(response.modulo.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('modulos_maestrias.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar el módulo ' + response.modulo.nombre_fantasia + '?');
            $('.delete-btn').val(response.modulo.id)
        }).fail(function(response) {

        })
    });

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
            title: '¿Está seguro de actualizar el módulo ' + nombre + '?',
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
        var url = "{{route('modulos_maestrias.destroy', ":id")}}"
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
        var url = "{{route('modulos_maestrias.unactivate', ":id")}}"
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
        var url = "{{route('modulos_maestrias.activate', ":id")}}"
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
            url: '{{route('modulos_maestrias.store')}}',
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
        var url = "{{route('modulos_maestrias.update', ":id")}}"
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

    $(document).on('click', '#show-correlatividades-btn', function () {
        var id = $(this).data('id');
        var url = "{{route('modulos_correlatividades.show', ":id")}}"
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
            var correlativas = response.modulo.correlativas;
            var id = response.modulo.id;
            if (correlativas.length > 0) {
                editar = 1;
            } else {
                editar = 0;
            }

            $('#title-modulo').html(response.modulo.nombre_fantasia + ' - <span class="text-muted">' + response.modulo.nombre_real + '</span>');
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
                                                @can('editar_correlatividades_modulos_maestrias_ubs')
                                                    ${editar === 1 ? '<a type="button" class="btn btn-danger me-2" id="delete-all-correlatividades-btn" data-id="' + id + '" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Todas las Correlatividades"><i class="ri-delete-bin-fill align-bottom"></i></a>' : ''}
                                                @endcan
                                                <span>Módulos Correlativas</span>
                                                @can('editar_correlatividades_modulos_maestrias_ubs')
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
                body +=`<li class="list-group-item text-center">Este módulo no posee ninguna correlatividad activa.</li>`
            }

                            body += `</ul>
                                </div>
                            </div>
                        </div>`

            $('#body').append(body);
        }).fail(function(response) {

        })

    })

    $(document).on('click', '#edit-correlatividad-btn', function () {
        var id = $(this).data('id');
        var url = "{{route('modulos_correlatividades.edit', ":id")}}"
        url = url.replace(':id', id);
        window.location.href = url;
    })

    $(document).on('click', '#delete-all-correlatividades-btn', function () {
        var nombre = $('#title-modulo').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar todas las correlatividades del módulo ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = "{{route('modulos_correlatividades.destroy', ":id")}}"
                url = url.replace(':id', id);
                window.location.href = url;
            }
        })
    })
</script>
