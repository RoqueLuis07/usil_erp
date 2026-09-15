<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('barrios.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.barrios, function (index, value) {
                    $('tbody').append(
                        `<tr>
                            <td class="id">${value.id}</td>
                            <td class="nombre">${value.nombre}</td>
                            <td class="ciudad">${value.ciudad.nombre}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}"><i class="ri-eye-fill"></i></button>
                                @can('editar_barrios')
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}"><i class="ri-edit-fill"></i></button>
                                @endcan
                                @can('eliminar_barrios')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill"></i></button>
                                @endcan
                            </td>`
                    )
                });
                var options = {
                    valueNames: ['nombre', 'ciudad'],
                    page: 50,
                    pagination: true
                };
                var barriosList = new List('barrios-list', options);
                barriosList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar el nuevo barrio?',
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
        var url = "{{route('barrios.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre-show').val(response.barrio.nombre);
            $('#ciudad-show').val(response.barrio.ciudad.nombre);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        const id = $(this).data('id');
        var url = "{{route('barrios.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nombre-edit').val(response.barrio.nombre);
            $('#ciudad-edit').selectpicker('destroy');
            $('#ciudad-edit').empty();
            $('#ciudad-edit').append('<option value="" disabled>Seleccionar...</option>');
            $.each(response.ciudades, function (index, value) {
                if (value.id == response.barrio.ciudad.id) {
                    $('#ciudad-edit').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                } else {
                    $('#ciudad-edit').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            })
            $('#ciudad-edit').selectpicker('render');

            $('.update-btn').val(response.barrio.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('barrios.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            console.log(response.barrio);
            $('.mensaje').html('Está seguro de eliminar el barrio ' + response.barrio.nombre + '?');
            $('.delete-btn').val(response.barrio.id)
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
            title: '¿Está seguro de actualizar el barrio ' + nombre + '?',
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
        var url = "{{route('barrios.destroy', ":id")}}"
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

    function save() {
        const formData = new FormData(document.getElementById('store-form'));
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: '{{route('barrios.store')}}',
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
        var url = "{{route('barrios.update', ":id")}}"
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
