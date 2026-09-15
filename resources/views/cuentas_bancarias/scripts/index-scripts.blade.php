<script type="module">
    $(document).ready(function() {
        fetch();
    });

    function fetch() {
        $.ajax({
            type: 'GET',
            url: '{{route('cuentas_bancarias.index_ajax')}}',
            success: function (response) {
                $('tbody').html('');
                $.each(response.cuentas_bancarias, function (index, value) {
                    if (value.estado == 'AC') {
                        var estado = `<span class="badge bg-success-subtle text-success text-uppercase">ACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></button>
                                        @endcan
                                        @can('inactivar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal" id="unactivate-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                        @endcan
                                        @can('eliminar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    } else if (value.estado == 'IN') {
                        var estado = `<span class="badge bg-danger-subtle text-danger text-uppercase">INACTIVO<span>`;
                        var botones = `<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal" id="show-btn" data-id="${value.id}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                        @can('editar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" id="edit-btn" data-id="${value.id}"><i class="ri-edit-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"></i></button>
                                        @endcan
                                        @can('activar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal" id="activate-btn" data-id="${value.id}"><i class="ri-close-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"></i></button>
                                        @endcan
                                        @can('eliminar_cuentas_bancarias')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal" id="destroy-btn" data-id="${value.id}"><i class="ri-delete-bin-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"></i></button>
                                        @endcan`
                    }
                    if (value.moneda_id == 1) {
                        var monto = Intl.NumberFormat('de-DE').format(parseInt(value.monto));
                    } else {
                        var monto = Intl.NumberFormat('us-US').format(parseFloat(value.monto));
                    }
                    if (value.tipo_cuenta == 'CA') {
                        var tipo_cuenta = 'CAJA DE AHORRO';
                    } else {
                        var tipo_cuenta = 'CUENTA CORRIENTE';
                    }
                    $('tbody').append(
                        `<tr>
                            <td>${value.id}</td>
                            <td class="banco">${value.banco.nombre}</td>
                            <td>${value.numero_cuenta}</td>
                            <td class="tipo_cuenta">${tipo_cuenta}</td>
                            <td class="monto">${monto}</td>
                            <td class="moneda">${value.moneda.codigo}</td>
                            <td class="estado">${estado}</td>
                            <td>${botones}</td>`
                    )
                });
                var options = {
                    valueNames: ['banco', 'tipo_cuenta', 'monto', 'moneda'],
                    page: 50,
                    pagination: true
                };
                var cuentas_bancariasList = new List('cuentas_bancarias-list', options);
                cuentas_bancariasList.on('updated', function(list) {
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
            title: '¿Está seguro de guardar la nueva cuenta bancaria?',
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
        var url = "{{route('cuentas_bancarias.show', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#banco-show').val(response.cuenta_bancaria.banco.nombre);
            $('#numero_cuenta-show').val(response.cuenta_bancaria.numero_cuenta);
            if (response.cuenta_bancaria.tipo_cuenta == 'CA') {
                var tipo_cuenta = 'CAJA DE AHORRO';
            } else {
                var tipo_cuenta = 'CUENTA CORRIENTE';
            }
            $('#tipo_cuenta-show').val(tipo_cuenta);
            $('#titular-show').val(response.cuenta_bancaria.titular);
            $('#documento_titular-show').val(response.cuenta_bancaria.documento_titular);
            $('#monto-show').val(Intl.NumberFormat('de-DE').format(parseInt(response.cuenta_bancaria.monto)));
            if (response.cuenta_bancaria.estado == 'AC') {
                $('#estado-show').val('ACTIVO');
            } else if (response.cuenta_bancaria.estado == 'IN') {
                $('#estado-show').val('INACTIVO');
            }
            $('#moneda-show').val(response.cuenta_bancaria.moneda.nombre);
            if (response.cuenta_bancaria.acredita_tarjeta == false) {
                $('#acredita_tarjeta1-show').prop('checked', true);
                $('#acredita_tarjeta2-show').prop('checked', false);
            } else {
                $('#acredita_tarjeta1-show').prop('checked', false);
                $('#acredita_tarjeta2-show').prop('checked', true);
            }

            $('#cuenta_ingreso-show').val(response.cuenta_bancaria.cuenta_ingreso.nombre + ' - ' + response.cuenta_bancaria.cuenta_ingreso.cuenta);
            $('#cuenta_egreso-show').val(response.cuenta_bancaria.cuenta_egreso.nombre + ' - ' + response.cuenta_bancaria.cuenta_egreso.cuenta);

        }).fail(function(response) {

        })
    });

    $(document).on('click', '#edit-btn', function() {
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
        const id = $(this).data('id');
        var url = "{{route('cuentas_bancarias.edit', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#banco-edit').selectpicker('destroy');
            $('#banco-edit').empty();
            $('#banco-edit').append('<option value="" disabled>Seleccionar...</option>');
            $.each(response.bancos, function (index, value) {
                if (value.id == response.cuenta_bancaria.banco.id) {
                    $('#banco-edit').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                } else {
                    $('#banco-edit').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            });
            $('#banco-edit').selectpicker('render');
            $('#numero_cuenta-edit').val(response.cuenta_bancaria.numero_cuenta);
            $('#tipo_cuenta-edit').selectpicker('destroy');
            $('#tipo_cuenta-edit').empty();
            $('#tipo_cuenta-edit').append('<option value="" disabled>Seleccionar...</option>');
            if (response.cuenta_bancaria.tipo_cuenta == 'CA') {
                $('#tipo_cuenta-edit').append('<option value="CA" selected>CAJA DE AHORRO</option>');
                $('#tipo_cuenta-edit').append('<option value="CC">CUENTA CORRIENTE</option>');
            } else {
                $('#tipo_cuenta-edit').append('<option value="CA">CAJA DE AHORRO</option>');
                $('#tipo_cuenta-edit').append('<option value="CC" selected>CUENTA CORRIENTE</option>');
            }
            $('#tipo_cuenta-edit').selectpicker('render');
            $('#monto-edit').val(Intl.NumberFormat('de-DE').format(parseInt(response.cuenta_bancaria.monto)));
            $('#moneda-edit').selectpicker('destroy');
            $('#moneda-edit').empty();
            $('#moneda-edit').append('<option value="" disabled>Seleccionar...</option>');
            $.each(response.monedas, function (index, value) {
                if (value.id == response.cuenta_bancaria.moneda.id) {
                    $('#moneda-edit').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                } else {
                    $('#moneda-edit').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            });
            $('#moneda-edit').selectpicker('render');
            $('#titular-edit').val(response.cuenta_bancaria.titular);
            $('#documento_titular-edit').val(response.cuenta_bancaria.documento_titular);

            if (response.cuenta_bancaria.acredita_tarjeta == false) {
                $('#acredita_tarjeta1-edit').prop('checked', true);
                $('#acredita_tarjeta2-edit').prop('checked', false);
            } else {
                $('#acredita_tarjeta1-edit').prop('checked', false);
                $('#acredita_tarjeta2-edit').prop('checked', true);
            }

            $('#cuenta_ingreso-edit').selectpicker('destroy');
            $('#cuenta_ingreso-edit').empty();
            $('#cuenta_ingreso-edit').append('<option value="" disabled>Seleccionar...</option>');
            $.each(response.cuentas_contables, function (index, value) {
                if (value.id == response.cuenta_bancaria.cuenta_ingreso_id) {
                    $('#cuenta_ingreso-edit').append('<option value="' + value.id + '" selected data-subtext="' + value.cuenta + '">' + value.nombre + '</option>');
                } else {
                    $('#cuenta_ingreso-edit').append('<option value="' + value.id + '" data-subtext="' + value.cuenta + '">' + value.nombre + '</option>');
                }
            });
            $('#cuenta_ingreso-edit').selectpicker('render');

            $('#cuenta_egreso-edit').selectpicker('destroy');
            $('#cuenta_egreso-edit').empty();
            $('#cuenta_egreso-edit').append('<option value="" disabled>Seleccionar...</option>');
            $.each(response.cuentas_contables, function (index, value) {
                if (value.id == response.cuenta_bancaria.cuenta_egreso_id) {
                    $('#cuenta_egreso-edit').append('<option value="' + value.id + '" selected data-subtext="' + value.cuenta + '">' + value.nombre + '</option>');
                } else {
                    $('#cuenta_egreso-edit').append('<option value="' + value.id + '" data-subtext="' + value.cuenta + '">' + value.nombre + '</option>');
                }
            });
            $('#cuenta_egreso-edit').selectpicker('render');

            $('.update-btn').val(response.cuenta_bancaria.id);
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#unactivate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('cuentas_bancarias.get_unactivate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de inactivar la cuenta bancaria?');
            $('.unactivate-btn').val(response.cuenta_bancaria.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#activate-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('cuentas_bancarias.get_activate', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de activar la cuenta bancaria?');
            $('.activate-btn').val(response.cuenta_bancaria.id)
        }).fail(function(response) {

        })
    });

    $(document).on('click', '#destroy-btn', function() {
        const id = $(this).data('id');
        var url = "{{route('cuentas_bancarias.get_destroy', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('.mensaje').html('Está seguro de eliminar la cuenta bancaria?');
            $('.delete-btn').val(response.cuenta_bancaria.id)
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
            title: '¿Está seguro de actualizar la cuenta bancaria?',
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
        var url = "{{route('cuentas_bancarias.destroy', ":id")}}"
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
        var url = "{{route('cuentas_bancarias.unactivate', ":id")}}"
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
        var url = "{{route('cuentas_bancarias.activate', ":id")}}"
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
            url: '{{route('cuentas_bancarias.store')}}',
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
            $('#banco').selectpicker('val', '');
            $('#tipo_cuenta').selectpicker('val', '');
            $('#moneda').selectpicker('val', '');
            $('#cuenta_ingreso').selectpicker('val', '');
            $('#cuenta_egreso').selectpicker('val', '');
            $('#createModal').modal('hide');
            fetch();
            message(response.message, type);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                if (key == 'acredita_tarjeta') {
                    $('.div-error-acredita_tarjeta').addClass('is-invalid');
                    $('.error-acredita_tarjeta').addClass('invalid-feedback').html(value.join('<strong>'));
                } else {
                    input.addClass('is-invalid');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    function update(id) {
        const formData = new FormData(document.getElementById('update-form'));
        var url = "{{route('cuentas_bancarias.update', ":id")}}"
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
                if (key == 'acredita_tarjeta') {
                    $('.div-error-acredita_tarjeta').addClass('is-invalid');
                    $('.error-acredita_tarjeta').addClass('invalid-feedback').html(value.join('<strong>'));
                } else {
                    input.addClass('is-invalid');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }
</script>
