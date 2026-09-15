<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    var precio = new Cleave('#precio', formatoSeparadorMiles);
    var precio_edit = new Cleave('#precio-edit', formatoSeparadorMiles);

    $(document).ready(function () {
        fetch();
    })

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('tutorias_precios.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.precios, function (index, value) {
                    var botones = `<button class="btn btn-sm btn-primary show-btn" data-bs-toggle="modal" data-bs-target="#showModal" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                    @can('editar_precios_tutorias')
                                        <button class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></button>
                                    @endcan
                                    @can('eliminar_precios_tutorias')
                                        <button class="btn btn-sm btn-danger destroy-btn" data-bs-toggle="modal" data-bs-target="#destroyModal" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                    @endcan`

                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="modalidad">${value.modalidad.nombre}</td>
                            <td class="carrera">${value.carrera.nombre_fantasia}</td>
                            <td class="precio">${value.articulo.detalle.precio_contado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['modalidad', 'carrera', 'precio'],
                    page: 50,
                    pagination: true
                };
                var preciosList = new List('precios-list', options);
                preciosList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar el nuevo precio?',
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

    $('#articulo').on('change', function () {
        var id = $(this).val();
        var url = "{{route('tutorias_precios.get_precio', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            precio.setRawValue(response.precio);
        })
    })

    $('#articulo-edit').on('change', function () {
        var id = $(this).val();
        var url = "{{route('tutorias_precios.get_precio', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            precio_edit.setRawValue(response.precio);
        })
    })

    $(document).on('click', '.show-btn', function() {
        var id = $(this).data('id');
        var url = "{{route('tutorias_precios.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#modalidad-show').val(response.precio.modalidad.nombre);
            $('#carrera-show').val(response.precio.carrera.nombre_fantasia);
            $('#precio-show').val(response.precio.articulo.detalle.precio_contado);
            $('#articulo-show').val(response.precio.articulo.nombre);
        })
    });

    $(document).on('click', '.edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        var id = $(this).data('id');
        var url = "{{route('tutorias_precios.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#modalidad-edit').selectpicker('destroy');
            $('#modalidad-edit').empty();
            $('#modalidad-edit').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.modalidades, function (i, val) {
                if (response.precio.modalidad_id == val.id) {
                    $('#modalidad-edit').append('<option value="' + val.id + '" selected>' + val.nombre + '</option>');
                } else {
                    $('#modalidad-edit').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                }
            })
            $('#modalidad-edit').selectpicker('render');

            $('#carrera-edit').selectpicker('destroy');
            $('#carrera-edit').empty();
            $('#carrera-edit').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.carreras, function (i, val) {
                if (response.precio.carrera_id == val.id) {
                    $('#carrera-edit').append('<option value="' + val.id + '" selected>' + val.nombre_fantasia + '</option>');
                } else {
                    $('#carrera-edit').append('<option value="' + val.id + '">' + val.nombre_fantasia + '</option>');
                }
            })
            $('#carrera-edit').selectpicker('render');

            $('#articulo-edit').selectpicker('destroy');
            $('#articulo-edit').empty();
            $('#articulo-edit').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.articulos, function (i, val) {
                if (response.precio.articulo_id == val.id) {
                    $('#articulo-edit').append('<option value="' + val.id + '" selected>' + val.nombre + '</option>');
                } else {
                    $('#articulo-edit').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                }
            })
            $('#articulo-edit').selectpicker('render');

            precio_edit.setRawValue(response.precio.articulo.detalle.precio_contado);
            $('.update-btn').val(response.precio.id);
        })
    });

    $(document).on('click', '.destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('tutorias_precios.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar el precio de tutoría de la carrera ' + response.precio.carrera.nombre_fantasia + ' en la modalidad ' + response.precio.modalidad.nombre + '?');
            $('.delete-btn').val(response.precio.id)
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
            title: '¿Está seguro de actualizar el precio?',
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
        var url = "{{route('tutorias_precios.destroy', ":id")}}"
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
            url: '{{route('tutorias_precios.store')}}',
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
        var url = "{{route('tutorias_precios.update', ":id")}}"
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
