<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('anulaciones_correlatividades.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.anulaciones, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></button>
                                        @endcan
                                        @can('inactivar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                        @endcan
                                        @can('eliminar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}"><i class="ri-edit-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"></i></button>
                                        @endcan
                                        @can('activar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}"><i class="ri-close-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"></i></button>
                                        @endcan
                                        @can('eliminar_anulaciones_correlatividades')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    }
                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="alumno">${value.alumno.primer_nombre} ${value.alumno.primer_apellido}</td>
                            <td class="alumno_documento">${value.alumno.numero_documento}</td>
                            <td class="semestre">${value.semestre.nombre}</td>
                            <td>${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['alumno', 'alumno_documento', 'semestre'],
                    page: 50,
                    pagination: true
                };
                var anulaciones_correlatividadesList = new List('anulaciones_correlatividades-list', options);
                anulaciones_correlatividadesList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar la nueva anulación de correlatividades?',
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
        var url = "{{route('anulaciones_correlatividades.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#alumno-show').val(response.anulacion.alumno.primer_nombre + ' ' + response.anulacion.alumno.primer_apellido);
            $('#alumno_documento-show').val(response.anulacion.alumno.numero_documento);
            $('#semestre-show').val(response.anulacion.semestre.nombre);
            if (response.anulacion.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.anulacion.estado == 'IN') {
                $('#estado-show').val('INACTIVO');
            }
            $('#motivo-show').html(response.anulacion.motivo)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('anulaciones_correlatividades.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar la anulación de correlatividades del alumno ' + response.anulacion.alumno.primer_nombre + ' ' + response.anulacion.alumno.primer_apellido + '?');
            $('.unactivate-btn').val(response.anulacion.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('anulaciones_correlatividades.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar la anulación de correlatividades del alumno ' + response.anulacion.alumno.primer_nombre + ' ' + response.anulacion.alumno.primer_apellido + '?');
            $('.activate-btn').val(response.anulacion.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('anulaciones_correlatividades.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar la anulación de correlatividades del alumno ' + response.anulacion.alumno.primer_nombre + ' ' + response.anulacion.alumno.primer_apellido + '?');
            $('.delete-btn').val(response.anulacion.id)
        }).fail(function(response) {

        })
    });

    $('.delete-btn').click(function (e) {
        e.preventDefault();
        var id = $(this).val();
        const formData = new FormData(document.getElementById('destroy-form'));
        var url = "{{route('anulaciones_correlatividades.destroy', ":id")}}"
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
        var url = "{{route('anulaciones_correlatividades.unactivate', ":id")}}"
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
        var url = "{{route('anulaciones_correlatividades.activate', ":id")}}"
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
            url: '{{route('anulaciones_correlatividades.store')}}',
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
            $('#alumno').selectpicker('val', '');
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
</script>
