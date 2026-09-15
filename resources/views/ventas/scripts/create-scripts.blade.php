<script type="module">
    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
            shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            },
            months: {
            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
            longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            },
        },
    }

    $(document).ready(function () {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);
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

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).removeClass('is-invalid');
    })

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

    $('#cliente').on('change', function () {
        if ($('#metodo_pago option:selected').val() == 6) {
            var id = $(this).val();
            var url = "{{ route('ventas.get_notas_creditos', ":id") }}";
            url = url.replace(':id', id);

            get_notas_creditos(url);
        }
    })

    $('#forma_pago').on('change', function () {
        if ($(this).val() == 'CR') {
            $('#div-credito_a').removeClass('d-none');
            $('#credito_a').selectpicker('val', '');
            $('#div-metodo_pago').addClass('d-none');
            $('#metodo_pago').selectpicker('val', '');
        } else {
            $('#div-credito_a').addClass('d-none');
            $('#credito_a').selectpicker('val', '');
            $('#div-metodo_pago').removeClass('d-none');
            $('#metodo_pago').selectpicker('val', '');
        }
    })

    $('#metodo_pago').on('change', function () {
        if ($(this).val() == 1) {
            $('#row-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#numero_transaccion_debito').val('');
            $('#fecha_transaccion_debito').val('');

            $('#row-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#numero_transaccion_credito').val('');
            $('#fecha_transaccion_credito').val('');

            $('#row-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#row-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');

            $('#div-nota_credito').addClass('d-none');
            $('#div-monto_nota_credito').addClass('d-none');
            $('#nota_credito').selectpicker('val', '');
        } else if ($(this).val() == 2) {
            $('#row-debito').removeClass('d-none');

            $('#row-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#numero_transaccion_credito').val('');
            $('#fecha_transaccion_credito').val('');

            $('#row-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#row-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');

            $('#div-nota_credito').addClass('d-none');
            $('#div-monto_nota_credito').addClass('d-none');
            $('#nota_credito').selectpicker('val', '');
        } else if ($(this).val() == 3) {
            $('#row-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#numero_transaccion_debito').val('');
            $('#fecha_transaccion_debito').val('');

            $('#row-credito').removeClass('d-none');

            $('#row-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#row-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');

            $('#div-nota_credito').addClass('d-none');
            $('#div-monto_nota_credito').addClass('d-none');
            $('#nota_credito').selectpicker('val', '');
        } else if ($(this).val() == 4) {
            $('#row-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#numero_transaccion_debito').val('');
            $('#fecha_transaccion_debito').val('');

            $('#row-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#numero_transaccion_credito').val('');
            $('#fecha_transaccion_credito').val('');

            $('#row-transferencia').removeClass('d-none');

            $('#row-deposito').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#div-nota_credito').addClass('d-none');
            $('#div-monto_nota_credito').addClass('d-none');
            $('#nota_credito').selectpicker('val', '');
        } else if ($(this).val() == 5) {
            $('#row-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#numero_transaccion_debito').val('');
            $('#fecha_transaccion_debito').val('');

            $('#row-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#numero_transaccion_credito').val('');
            $('#fecha_transaccion_credito').val('');

            $('#row-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#row-deposito').removeClass('d-none');

            $('#div-nota_credito').addClass('d-none');
            $('#div-monto_nota_credito').addClass('d-none');
            $('#nota_credito').selectpicker('val', '');
        } else if ($(this).val() == 6) {
            $('#row-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#numero_transaccion_debito').val('');
            $('#fecha_transaccion_debito').val('');

            $('#row-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#numero_transaccion_credito').val('');
            $('#fecha_transaccion_credito').val('');

            $('#row-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('#row-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');

            $('#div-nota_credito').removeClass('d-none');
            if ($('#cliente option:selected').val() != '') {

                var id = $('#cliente option:selected').val();
                var url = "{{ route('ventas.get_notas_creditos', ":id") }}";
                url = url.replace(':id', id);

                get_notas_creditos(url);
            }
            $('#div-monto_nota_credito').removeClass('d-none');
        }
    })

    $('#cancel-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la operación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{route('ventas.index')}}';
            }
        })
    });

    $('#cancel-modal-btn').click(function () {
        $('#banco_debito_dos').selectpicker('val', '');
        $('#numero_transaccion_debito_dos').val('');
        $('#fecha_transaccion_debito_dos').val('');
        $('#banco_credito_dos').selectpicker('val', '');
        $('#numero_transaccion_credito_dos').val('');
        $('#fecha_transaccion_credito_dos').val('');
        $('#banco_transferencia_dos').selectpicker('val', '');
        $('#cuenta_bancaria_transferencia_dos').selectpicker('val', '');
        $('#numero_transaccion_transferencia_dos').val('');
        $('#fecha_transaccion_transferencia_dos').val('');
        $('#cuenta_bancaria_deposito_dos').selectpicker('val', '');
        $('#numero_transaccion_deposito_dos').val('');
        $('#fecha_transaccion_deposito_dos').val('');
        $('#metodo_pago_dos').selectpicker('val', '');
        $('#metodo-pago-campos').html('');
    });

    $('#save-btn').click(function () {
        var monto_nota_credito = $('#monto_nota_credito').val();
        monto_nota_credito = monto_nota_credito.replace(/\./g, '');

        var total = $('.total').val();
        total = total.replace(/\./g, '');
        console.log(total, monto_nota_credito);

        if ((total - monto_nota_credito) != total && $('#forma_pago option:selected').val() == 'CO') {
            $('#total_pagar_pago').text(Intl.NumberFormat('de-DE').format(parseInt(total)));
            $('#saldo_pago').text(Intl.NumberFormat('de-DE').format(parseInt(total - monto_nota_credito)));

            $('#metodoPagoModal').modal('show');
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-light',
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: '¿Está seguro de guardar la nueva venta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar!',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#store-form').submit();
                }
            })
        }
    });

    $('#facturar-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la nueva venta?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    });

    $(document).on('focus', '.numero_transaccion', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $('#metodo_pago_dos').on('change', function () {
        var seleccionado = $(this).val();
        $('#metodo-pago-campos').html('');

        if (seleccionado == 2) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-DD');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-12 mb-3 text-center">
                                            <label class="form-label" for="banco_debito_dos">Banco</label>
                                            <select class="form-control selectpicker" id="banco_debito_dos" name="banco_debito_dos" data-live-search="true">

                                            </select>
                                            <span class="text-danger error-banco_debito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion_debito_dos">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion_debito_dos" name="numero_transaccion_debito_dos">
                                            <span class="text-danger error-numero_transaccion_debito_dos">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion_debito_dos">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion_debito_dos" name="fecha_transaccion_debito_dos" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_debito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#metodo-pago-campos').append(datos_forma_pago);

            $('#banco_debito_dos').selectpicker('destroy');
            $('#banco_debito_dos').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco_debito_dos').append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco_debito_dos').selectpicker('render');
        } else if (seleccionado == 3) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-DD');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-12 mb-3 text-center">
                                            <label class="form-label" for="banco_credito_dos">Banco</label>
                                            <select class="form-control selectpicker" id="banco_credito_dos" name="banco_credito_dos" data-live-search="true">

                                            </select>
                                            <span class="text-danger error-banco_credito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion_credito_dos">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion_credito_dos" name="numero_transaccion_credito_dos">
                                            <span class="text-danger error-numero_transaccion_credito_dos">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="fecha_transaccion_credito_dos">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion_credito_dos" name="fecha_transaccion_credito_dos" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_credito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    `;

            $('#metodo-pago-campos').append(datos_forma_pago);

            $('#banco_credito_dos').selectpicker('destroy');
            $('#banco_credito_dos').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco_credito_dos').append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco_credito_dos').selectpicker('render');
        } else if (seleccionado == 4) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var cuentas_bancarias = {!!json_encode($cuentas_bancarias, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-D');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="banco_transferencia_dos">Banco Origen</label>
                                            <select class="form-control selectpicker" id="banco_transferencia_dos" name="banco_transferencia_dos" data-live-search="true">

                                            </select>
                                            <span class="text-danger error-banco_transferencia_dos">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="cuenta_bancaria_transferencia_dos">Cuenta Destino</label>
                                            <select class="form-control selectpicker" id="cuenta_bancaria_transferencia_dos" name="cuenta_bancaria_transferencia_dos" data-live-search="true">

                                            </select>
                                            <span class="text-danger error-cuenta_bancaria_transferencia_dos">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion_transferencia_dos">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion_transferencia_dos" name="numero_transaccion_transferencia_dos">
                                            <span class="text-danger error-numero_transaccion_transferencia_dos">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion_transferencia_dos">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion_transferencia_dos" name="fecha_transaccion_transferencia_dos" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_transferencia_dos">

                                            </span>
                                        </div>
                                    </div>
                                    `;

            $('#metodo-pago-campos').append(datos_forma_pago);

            $('#banco_transferencia_dos').selectpicker('destroy');
            $('#banco_transferencia_dos').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco_transferencia_dos').append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco_transferencia_dos').selectpicker('render');

            $('#cuenta_bancaria_transferencia_dos').selectpicker('destroy');
            $('#cuenta_bancaria_transferencia_dos').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria_transferencia_dos').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            })
            $('#cuenta_bancaria_transferencia_dos').selectpicker('render');
        } else if (seleccionado == 5) {
            var cuentas_bancarias = {!!json_encode($cuentas_bancarias, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-D');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-12 mb-3 text-center">
                                            <label class="form-label" for="cuenta_bancaria_deposito_dos">Cuenta Destino</label>
                                            <select class="form-control selectpicker" id="cuenta_bancaria_deposito_dos" name="cuenta_bancaria_deposito_dos" data-live-search="true">

                                            </select>
                                            <span class="text-danger error-cuenta_bancaria_deposito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion_deposito_dos">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion_deposito_dos" name="numero_transaccion_deposito_dos">
                                            <span class="text-danger error-numero_transaccion_deposito_dos">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion_deposito_dos">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion_deposito_dos" name="fecha_transaccion_deposito_dos" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_deposito_dos">

                                            </span>
                                        </div>
                                    </div>
                                    `;

            $('#metodo-pago-campos').append(datos_forma_pago);

            $('#cuenta_bancaria_deposito_dos').selectpicker('destroy');
            $('#cuenta_bancaria_deposito_dos').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria_deposito_dos').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            })
            $('#cuenta_bancaria_deposito_dos').selectpicker('render');
        }
    })

    function get_notas_creditos(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#nota_credito').selectpicker('destroy');
            $('#nota_credito').empty();
            $('#nota_credito').append('<option value="" selected disabled>Seleccionar...</option>');

            if (response.notas_creditos.length == 0) {
                $('#metodo_pago').selectpicker('val', '');
                $('#div-nota_credito').addClass('d-none');
                $('#div-monto_nota_credito').addClass('d-none');
                $('#nota_credito').selectpicker('val', '');
                $('#monto_nota_credito').val('');
                message('El cliente no cuenta con notas de crédito disponibles. Favor seleccione otra forma de pago.', 'error');
            } else {
                $.each(response.notas_creditos, function (index, value) {
                    $('#nota_credito').append('<option value="' + value.id + '" data-subtext="' + value.monto_total + '">' + value.numero_nota_credito + '</option>');
                    if (response.notas_creditos.length == 1) {
                        $('#nota_credito').val(value.id);
                        $('#monto_nota_credito').val(value.monto_total);
                    }
                })
                $('#nota_credito').prop('disabled', false);
                $('#nota_credito').selectpicker('render');
            }
        })
    }
</script>
