<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

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

    $(document).ready(function () {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

    $('.btn-check').on('click', function () {
        $('.btn-group').removeClass('is-invalid');
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
                window.location.href = '{{route('cajero.create')}}';
            }
        })
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
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success me-2',
                        cancelButton: 'btn btn-light',
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: '¿Quiere imprimir la factura?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, imprimir!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#input-imprimir').val('SI');
                        // $('#store-form').submit();
                        save();
                    } else {
                        // $('#store-form').submit();
                        save();
                    }
                })
            }
        })
    });

    $('#alumno').on('change', function () {
        $('#a_cobrar').html('');
        $('#total_pagar').html('Gs. 0');
        $('#total_pagar_pago').html('Gs. 0');
        $('#saldo_pago').html('Gs. 0');
        $('#btn-add').prop('disabled', true);
        $('#cobrar-btn').prop('disabled', true);

        $('#razon_social').html('');
        $('#ruc').html('');

        const alumno = $(this).val();
        var url = "{{route('cajero.get_clientes', ":alumno")}}"
        url = url.replace(':alumno', alumno);

        var url_pagos = "{{route('cajero.get_pagos', ":alumno")}}"
        url_pagos = url_pagos.replace(':alumno', alumno);

        get_pagos(url_pagos, url);
    })

    $('#cliente').on('change', function () {
        var razon_social = $('#cliente option:selected').text();
        var ruc = $('#cliente option:selected').data('subtext');
        $('#razon_social').html(razon_social);
        $('#ruc').html(ruc);
    })

    var nueva_fila = 0;
    $('#btn-add').on('click', function () {
        var alumno = $('#alumno option:selected').val();
        var cliente = $('#cliente option:selected').val();
        var monto = $('#pagos option:selected').val();
        var descripcion = $('#pagos option:selected').text();
        var id = $('#pagos option:selected').data('id');
        var tipo = $('#pagos option:selected').data('tipo');
        var bruto = $('#pagos option:selected').data('bruto');
        var convenio = $('#pagos option:selected').data('convenio');

        if (monto && descripcion) {
            if (!$('.alert').hasClass('d-none')) {
                $('.alert').addClass('d-none');
            }

            var add_pago = `<tr class="fila" id="fila-${nueva_fila}">
                                <td class="d-none"><input type="hidden" id="id-${nueva_fila}" name="detalles[${nueva_fila}][id]" value="${id}"></td>
                                <td><input type"text" class="form-control text-center" id="descripcion-${nueva_fila}" name="detalles[${nueva_fila}][descripcion]" value="${descripcion}" readonly></td>
                                <td><input type"text" class="form-control text-center" id="bruto-${nueva_fila}" name="detalles[${nueva_fila}][bruto]" value="${bruto}" readonly></td>
                                <td><input type"text" class="form-control descuento text-center" id="descuento-${nueva_fila}" name="detalles[${nueva_fila}][descuento]" value="${convenio}" data-id="${nueva_fila}" readonly></td>
                                <td><input type"text" class="form-control text-center a_pagar a_pagar-${nueva_fila}" id="a_pagar-${nueva_fila}" name="detalles[${nueva_fila}][a_pagar]" value="${monto}" data-id="${nueva_fila}""></td>
                                <td class="d-none"><input type="hidden" id="tipo-${nueva_fila}" name="detalles[${nueva_fila}][tipo]" value="${tipo}"></td>
                                <td><button type="button" class="btn btn-icon btn-danger btn-erase" id="erase-${nueva_fila}" data-id="${nueva_fila}"><i class="ri-subtract-fill"></i></button></td>
                            </tr>`

            $('#a_cobrar').append(add_pago);

            $('#pagos').selectpicker('destroy');
            $('#pagos option').each(function () {
                if ($(this).val() == monto && $(this).text() == descripcion) {
                    $(this).remove();
                }
            })
            $('#pagos').selectpicker('render');

            calcularTotalPagar();
            new Cleave('#descuento-' + nueva_fila, formatoSeparadorMiles);
            new Cleave('#a_pagar-' + nueva_fila, formatoSeparadorMiles);
            nueva_fila += 1;

            if (alumno && cliente) {
                $('#cobrar-btn').prop('disabled', false);
                $('#descuento_aplicado').selectpicker('destroy');
                $('#descuento_aplicado').prop('disabled', false);
                $('#descuento_aplicado').selectpicker('render');
                $('#btn-add-descuento').prop('disabled', false);
            }
        } else {
            if ($('.alert').hasClass('d-none')) {
                $('.alert').removeClass('d-none');
            }
        }
    })

    $(document).on('click', '#btn-add-descuento', function () {
        var seleccionado = $('#descuento_aplicado option:selected').val();
        var descuentos = {!!json_encode($descuentos, JSON_HEX_TAG) !!}
        $('#descuento_aplicado').selectpicker('destroy');
        $('#descuento_aplicado').prop('disabled', true);
        $('#descuento_aplicado').selectpicker('render');
        $('#input-descuento_aplicado').val(seleccionado);
        $.each(descuentos, function (index, value) {
            if (value.id == seleccionado) {
                $('.descuento:visible').each(function () {
                    var id = $(this).data('id');
                    var valor_bruto = $('#bruto-' + id).val();
                    valor_bruto = valor_bruto.replace(/\./g, '');
                    if (value.detalle.tipo_descuento == 'VA') {
                        var valor_actual = $(this).val();
                        valor_actual = valor_actual.replace(/\./g, '');
                        var descuento = value.detalle.precio_descuento;
                        $(this).val(Intl.NumberFormat('de-DE').format(parseInt(descuento)));
                        var a_pagar = $('.a_pagar-' + id);
                        if (a_pagar.is(':visible')) {
                            var valor_a_pagar = a_pagar.val();
                            valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                            a_pagar.val(Intl.NumberFormat('de-DE').format(parseInt(valor_a_pagar - descuento)))
                        }
                    } else if (value.detalle.tipo_descuento == 'PO') {
                        var valor_actual = $(this).val();
                        valor_actual = valor_actual.replace(/\./g, '');
                        var descuento = (value.detalle.porcentaje_descuento / 100) * valor_bruto;
                        $(this).val(Intl.NumberFormat('de-DE').format(parseInt(descuento)));
                        var a_pagar = $('.a_pagar-' + id);
                        if (a_pagar.is(':visible')) {
                            var valor_a_pagar = a_pagar.val();
                            valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                            a_pagar.val(Intl.NumberFormat('de-DE').format(parseInt(valor_a_pagar - descuento)))
                        }
                    }
                })

                $('.descuento:hidden').each(function () {
                    var id = $(this).data('id');
                    var valor_bruto = $('#bruto-' + id).val();
                    valor_bruto = valor_bruto.replace(/\./g, '');
                    if (value.detalle.tipo_descuento == 'VA') {
                        var valor_actual = $(this).val();
                        var descuento = value.detalle.precio_descuento;
                        $(this).val(parseInt(descuento));
                        var a_pagar = $('#a_pagar-' + id);
                        if (a_pagar.si(':hidden')) {
                            valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                            a_pagar.val(parseInt(valor_a_pagar));
                        }
                    } else if (value.detalle.tipo_descuento == 'PO') {
                        var valor_actual = $(this).val();
                        var descuento = (value.detalle.porcentaje_descuento / 100) * valor_bruto;
                        $(this).val(parseInt(descuento));
                        var a_pagar = $('#a_pagar-' + id);
                        if (a_pagar.is(':hidden')) {
                            var valor_a_pagar = a_pagar.val();
                            valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                            a_pagar.val(parseInt(valor_a_pagar));
                        }
                    }
                })
            }
        })
        calcularTotalPagar();
        $(this).detach();
        $('#div-btn-descuento').append('<button type="button" class="btn btn-icon btn-danger" id="btn-erase-descuento"><i class="ri-subtract-fill"></i></button>');
    })

    $(document).on('click', '#btn-erase-descuento', function () {
        var descuentos = {!!json_encode($descuentos, JSON_HEX_TAG) !!}

        $('#descuento_aplicado').selectpicker('destroy');
        $('#descuento_aplicado').val('');
        $('#descuento_aplicado').trigger('change');
        $('#descuento_aplicado').prop('disabled', false);
        $('#descuento_aplicado').selectpicker('render');
        $('#input-descuento_aplicado').val('');

        $('.descuento:visible').each(function () {
            $(this).val(0);
            var id = $(this).data('id');
            var valor_bruto = $('#bruto-' + id).val();
            valor_bruto = valor_bruto.replace(/\./g, '');
            var a_pagar = $('.a_pagar-' + id);
            if (a_pagar.is(':visible')) {
                var valor_a_pagar = a_pagar.val();
                valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                a_pagar.val(Intl.NumberFormat('de-DE').format(parseInt(valor_bruto)));
            }
        })

        $('.descuento:hidden').each(function () {
            $(this).val(0);
            var id = $(this).data('id');
            var valor_bruto = $('#bruto-' + id).val();
            valor_bruto = valor_bruto.replace(/\./g, '');
            var a_pagar = $('#a_pagar-' + id);
            if (a_pagar.is(':hidden')) {
                var valor_a_pagar = a_pagar.val();
                valor_a_pagar = valor_a_pagar.replace(/\./g, '');
                a_pagar.val(parseInt(valor_bruto));
            }
        })
        calcularTotalPagar();
        $(this).detach();
        $('#div-btn-descuento').append('<button type="button" class="btn btn-icon btn-success" id="btn-add-descuento"><i class="ri-add-fill"></i></button>');
    })

    $(document).on('change', '#pagos', function () {
        var cliente = $('#cliente option:selected').val();
        if (cliente) {
            $('#btn-add').prop('disabled', false);
        }
    })

    $(document).on('click', '.btn-erase', function () {
        var fila = $(this).data('id');
        var monto = $('#monto-' + fila).val();
        var descripcion = $('#descripcion-' + fila).val();

        $('#pagos').selectpicker('destroy');
        $('#pagos').append('<option value="' + monto + '" data-subtext="' + monto + '">' + descripcion + '</option>');
        $('#pagos').selectpicker('render');

        $('#fila-' + fila).remove();

        var cantidad_filas = $('#a_cobrar tr').length;
        if (cantidad_filas <= 0) {
            $('#cobrar-btn').prop('disabled', true);
            $('#total_pagar').html('Gs. 0');
            $('#total_pagar_pago').html('Gs. 0');
            $('#saldo_pago').html('Gs. 0');
        } else {
            calcularTotalPagar();
        }
    })

    $(document).on('focus', '.descuento', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^(100|[0-9]?[0-9])$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).on('keyup', '.descuento', function () {
        var fila = $(this).data('id');
        var descuento = $(this).val();
        var monto = $('#monto-' + fila).val();

        descuento = descuento / 100;
        monto = parseInt(monto.replace(/\./g, ''));


        if (descuento > 0 && descuento != 1) {
            var a_pagar = monto * descuento;
            $('#a_pagar-' + fila).val(Intl.NumberFormat('de-DE').format(parseInt(a_pagar)));
        } else if (descuento == 0) {
            $('#a_pagar-' + fila).val(Intl.NumberFormat('de-DE').format(parseInt(monto)));
        } else if (descuento == 1) {
            $('#a_pagar-' + fila).val(0);
        }
        calcularTotalPagar();
    })

    $(document).on('keyup', '.a_pagar', function () {
        $('#cobrar-btn').prop('disabled', true);

        var id = $(this).data('id');
        var monto = $('#monto-' + id).val();

        if (monto) {
            monto = parseInt(monto.replace(/\./g, ''));
            var a_pagar = $(this).val();
            a_pagar = parseInt(a_pagar.replace(/\./g, ''));

            if (a_pagar >= monto) {
                $(this).val(monto);
                a_pagar = monto;
                $('#cobrar-btn').prop('disabled', false);
            }

            if (a_pagar < 0) {
                $(this).val(0);
                a_pagar = 0;
            }

            if (isNaN(a_pagar)) {
                $(this).val(0);
                a_pagar = 0;
            }

            $(this).val(Intl.NumberFormat('de-DE').format(parseInt(a_pagar)));
        }

        calcularTotalPagar();
    })

    function get_clientes(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#cliente').selectpicker('destroy');
            $('#cliente').empty();
            $('#cliente').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.clientes, function (index, value) {
                if (value.es_principal) {
                    $('#cliente').append('<option value="' + value.cliente.id + '" selected data-subtext="' + value.cliente.numero_documento + '">' + value.cliente.nombre + '</option>');
                    $('#razon_social').html(value.cliente.razon_social);
                    $('#ruc').html(value.cliente.numero_documento);
                } else {
                    $('#cliente').append('<option value="' + value.cliente.id + '" data-subtext="' + value.cliente.numero_documento + '">' + value.cliente.nombre + '</option>');
                }
            })
            $('#cliente').prop('disabled', false);
            $('#cliente').selectpicker('render');
        })
    }

    function get_pagos(url, url_clientes) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#pagos').selectpicker('destroy');
            $('#pagos').empty();
            $('#pagos').append('<option value="" selected disabled>Seleccionar...</option>');

            if (response.pagos.length == 0) {
                $('#cliente').selectpicker('destroy');
                $('#cliente').empty();
                $('#cliente').append('<option value="" selected disabled>Seleccionar...</option>');
                $('#cliente').prop('disabled', true);
                $('#cliente').selectpicker('render');

                $('#pagos').selectpicker('destroy');
                $('#pagos').empty();
                $('#pagos').append('<option value="" selected disabled>Seleccionar...</option>');
                $('#pagos').prop('disabled', true);
                $('#pagos').selectpicker('render');

                $('#alumno').selectpicker('val', '');

                message('El alumno seleccionado no dispone de ninún pago pendiente. Por favor seleccione otro.', 'error')
            } else {
                get_clientes(url_clientes);

                $.each(response.pagos, function (index, value) {
                    $('#pagos').append('<option value="' + value.saldo + '" data-subtext="' + value.saldo + '" data-id="' + value.id + '" data-tipo="' + value.tipo + '" data-bruto="' + value.bruto + '" data-convenio="' + value.convenio + '">' + value.descripcion + '</option>');
                })
                $('#pagos').prop('disabled', false);
                $('#pagos').selectpicker('render');
            }
        })
    }

    //Modal
    $(document).on('change', '.forma_pago', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var fila = $(this).data('id');
        var seleccionado = $(this).val();

        $('#forma-pago-campos-' + fila).html('');
        $('#btn-add-pago-' + fila).prop('disabled', false);

        if (seleccionado == 1) {
            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="efectivo-${fila}">Efectivo</label>
                                            <input type="text" class="form-control efectivo pagado text-center" id="efectivo-${fila}" name="pagos[${fila}][efectivo]" data-id="${fila}">
                                            <span class="error-efectivo-${fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="vuelto-${fila}">Vuelto</label>
                                            <div class="text-center mt-2">
                                                <h5 class="text-success" id="vuelto">Gs. 0</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <span class="text-danger error-efectivo">

                                        </span>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);
        } else if (seleccionado == 2) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-DD');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="banco-debito-${fila}">Banco</label>
                                            <select class="form-control selectpicker" id="banco-debito-${fila}" name="pagos[${fila}][banco_debito]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-banco_debito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="monto-debito-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-debito pagado" id="monto-debito-${fila}" name="pagos[${fila}][monto_debito]" data-id="${fila}">
                                            <span class="text-danger error-monto_debito">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion-debito${fila}">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion-debito-${fila}" name="pagos[${fila}][numero_transaccion_debito]" data-id="${fila}">
                                            <span class="text-danger error-numero_transaccion_debito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion-debito${fila}">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion-debito-${fila}" name="pagos[${fila}][fecha_transaccion_debito]" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_debito">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);

            $('#banco-debito-' + fila).selectpicker('destroy');
            $('#banco-debito-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco-debito-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco-debito-' + fila).selectpicker('render');
        } else if (seleccionado == 3) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-DD');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="banco-credito-${fila}">Banco</label>
                                            <select class="form-control selectpicker" id="banco-credito-${fila}" name="pagos[${fila}][banco_credito]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-banco_credito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="monto-credito-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-credito pagado" id="monto-credito-${fila}" name="pagos[${fila}][monto_credito]" data-id="${fila}">
                                            <span class="text-danger error-monto_credito">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion-credito${fila}">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion-credito-${fila}" name="pagos[${fila}][numero_transaccion_credito]" data-id="${fila}">
                                            <span class="text-danger error-numero_transaccion_credito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="fecha_transaccion-credito${fila}">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion-credito-${fila}" name="pagos[${fila}][fecha_transaccion_credito]" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_credito">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);

            $('#banco-credito-' + fila).selectpicker('destroy');
            $('#banco-credito-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco-credito-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco-credito-' + fila).selectpicker('render');
        } else if (seleccionado == 4) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var cuentas_bancarias = {!!json_encode($cuentas_bancarias, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-D');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="banco-transferencia-${fila}">Banco Origen</label>
                                            <select class="form-control selectpicker" id="banco-transferencia-${fila}" name="pagos[${fila}][banco_transferencia]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-banco_transferencia">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="cuenta_bancaria-transferencia-${fila}">Cuenta Destino</label>
                                            <select class="form-control selectpicker" id="cuenta_bancaria-transferencia-${fila}" name="pagos[${fila}][cuenta_bancaria_transferencia]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-cuenta_bancaria_transferencia">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion-transferencia-${fila}">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion-transferencia-${fila}" name="pagos[${fila}][numero_transaccion_transferencia]" data-id="${fila}">
                                            <span class="text-danger error-numero_transaccion_transferencia">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion-transferencia-${fila}">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion-transferencia-${fila}" name="pagos[${fila}][fecha_transaccion_transferencia]" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_transferencia">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3 text-center">
                                            <label class="form-label" for="monto-transferencia-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-transferencia pagado" id="monto-transferencia-${fila}" name="pagos[${fila}][monto_transferencia]" data-id="${fila}">
                                            <span class="text-danger error-monto_transferencia">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);

            $('#banco-transferencia-' + fila).selectpicker('destroy');
            $('#banco-transferencia-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco-transferencia-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco-transferencia-' + fila).selectpicker('render');

            $('#cuenta_bancaria-transferencia-' + fila).selectpicker('destroy');
            $('#cuenta_bancaria-transferencia-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria-transferencia-' + fila).append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            })
            $('#cuenta_bancaria-transferencia-' + fila).selectpicker('render');
        } else if (seleccionado == 5) {
            var cuentas_bancarias = {!!json_encode($cuentas_bancarias, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-D');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="cuenta_bancaria-deposito-${fila}">Cuenta Destino</label>
                                            <select class="form-control selectpicker" id="cuenta_bancaria-deposito-${fila}" name="pagos[${fila}][cuenta_bancaria_deposito]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-cuenta_bancaria_deposito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="monto-deposito-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-deposito pagado" id="monto-deposito-${fila}" name="pagos[${fila}][monto_deposito]" data-id="${fila}">
                                            <span class="text-danger error-monto_deposito">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_transaccion-deposito-${fila}">N° de Transacción</label>
                                            <input type="text" class="form-control text-center" id="numero_transaccion-deposito-${fila}" name="pagos[${fila}][numero_transaccion_deposito]" data-id="${fila}">
                                            <span class="text-danger error-numero_transaccion_deposito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="fecha_transaccion-deposito-${fila}">Fecha de Transacción</label>
                                            <input type="date" class="form-control text-center" id="fecha_transaccion-deposito-${fila}" name="pagos[${fila}][fecha_transaccion_deposito]" value="${fecha}">
                                            <span class="text-danger error-fecha_transaccion_deposito">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);

            $('#cuenta_bancaria-deposito-' + fila).selectpicker('destroy');
            $('#cuenta_bancaria-deposito-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria-deposito-' + fila).append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            })
            $('#cuenta_bancaria-deposito-' + fila).selectpicker('render');
        } else if (seleccionado == 6) {
            var cliente = $('#cliente option:selected').val();
            var url = "{{ route('cajero.get_notas_creditos', ":cliente") }}";
            url = url.replace(':cliente', cliente);

            get_notas_creditos(url, fila);
        } else if (seleccionado == 7) {
            var bancos = {!!json_encode($bancos, JSON_HEX_TAG) !!}
            var fecha = moment().format('Y-MM-D');

            var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="banco-cheque-${fila}">Banco Origen</label>
                                            <select class="form-control selectpicker" id="banco-cheque-${fila}" name="pagos[${fila}][banco_cheque]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-banco_cheque">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="monto-cheque-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-cheque pagado" id="monto-cheque-${fila}" name="pagos[${fila}][monto_cheque]" data-id="${fila}">
                                            <span class="text-danger error-monto_cheque">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero-cheque-${fila}">N° de Cheque</label>
                                            <input type="text" class="form-control text-center" id="numero-cheque-${fila}" name="pagos[${fila}][numero_cheque]" data-id="${fila}">
                                            <span class="text-danger error-numero_cheque">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="numero_serie-cheque-${fila}">N° de Serie</label>
                                            <input type="date" class="form-control text-center" id="numero_serie-cheque-${fila}" name="pagos[${fila}][numero_serie_cheque]" value="${fecha}">
                                            <span class="text-danger error-numero_serie_cheque">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="emisor-cheque-${fila}">Emisor</label>
                                            <input type="date" class="form-control text-center" id="emisor-cheque-${fila}" name="pagos[${fila}][emisor_cheque]" value="${fecha}">
                                            <span class="text-danger error-emisor_cheque">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

            $('#forma-pago-campos-' + fila).append(datos_forma_pago);

            $('#banco-cheque-' + fila).selectpicker('destroy');
            $('#banco-cheque-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(bancos, function (index, value) {
                $('#banco-cheque-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
            })
            $('#banco-cheque-' + fila).selectpicker('render');
        }

        calcularPendienteVuelto();
    })

    $(document).on('focus', '.efectivo', function () {
        var fila = $(this).data('id');
        new Cleave('#efectivo-' + fila, formatoSeparadorMiles);
    })

    $(document).on('focus', '.monto-debito', function () {
        var fila = $(this).data('id');
        new Cleave('#monto-debito-' + fila, formatoSeparadorMiles);
    })

    $(document).on('focus', '.monto-credito', function () {
        var fila = $(this).data('id');
        new Cleave('#monto-credito-' + fila, formatoSeparadorMiles);
    })

    $(document).on('focus', '.monto-transferencia', function () {
        var fila = $(this).data('id');
        new Cleave('#monto-transferencia-' + fila, formatoSeparadorMiles);
    })

    $(document).on('focus', '.monto-deposito', function () {
        var fila = $(this).data('id');
        new Cleave('#monto-deposito-' + fila, formatoSeparadorMiles);
    })

    $(document).on('focus', '.monto-cheque', function () {
        var fila = $(this).data('id');
        new Cleave('#monto-cheque-' + fila, formatoSeparadorMiles);
    })

    $(document).on('keyup', '.efectivo', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('keyup', '.monto-debito', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('keyup', '.monto-credito', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('keyup', '.monto-transferencia', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('keyup', '.monto-deposito', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('keyup', '.monto-cheque', function () {
        $('.btn-add-pago').prop('disabled', true);

        var fila = $(this).data('id');
        var ingresado = $(this).val();
        ingresado = parseInt(ingresado.replace(/\./g, ''));

        calcularPendienteVuelto();
    })

    $(document).on('click', '.btn-add-pago', function () {
        var ultima_fila = $('.forma-pago-fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[3]);
        var fila = nro_ultima_fila + 1;
        var formas_pagos = {!!json_encode($formas_pagos, JSON_HEX_TAG) !!}

        var nueva_fila = `<div class="forma-pago-fila forma-pago-fila-${fila}" id="forma-pago-fila-${fila}">
                            <div class="row">
                                <div class="col-lg-10 mb-3">
                                    <label class="form-label" for="forma-pago-${fila}">Forma de Pago <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker forma_pago" id="forma_pago-${fila}" name="pagos[${fila}][forma_pago]" data-live-search="true" data-id="${fila}">

                                    </select>
                                    <span class="text-danger error-pagos-${fila}">

                                    </span>
                                </div>
                                <div class="col-lg-2 mb-3 text-center div-btn-add-pago-${fila}" style="margin-top: 30px;">
                                    <button type="button" class="btn btn-icon btn-danger btn-erase-pago" id="btn-erase-pago-${fila}" data-id="${fila}"><i class="ri-subtract-fill"></i></button>
                                </div>
                                <hr>
                            </div>
                            <div id="forma-pago-campos-${fila}">

                            </div>
                        </div>
                        `;

        var primera_fila = $('.forma-pago-fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[3];

        $('.div-btn-add-pago-' + nro_primera_fila).html('');
        $('.div-btn-add-pago-' + nro_primera_fila).append('<button type="button" class="btn btn-icon btn-danger btn-erase-pago" id="btn-erase-pago-' + nro_primera_fila + '" data-id="'+ nro_primera_fila + '"><i class="ri-subtract-fill"></i></button>');

        $('#forma-pago-fila').append(nueva_fila);

        var forma_pago_seleccionada = $('#forma_pago-' + nro_primera_fila).val();

        $('#forma_pago-' + fila).selectpicker('destroy');
        $('#forma_pago-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
        $.each(formas_pagos, function (index, value) {
            if (forma_pago_seleccionada != null) {
                if (value.id != forma_pago_seleccionada) {
                    $('#forma_pago-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
                }
            }
        })
        $('#forma_pago-' + fila).selectpicker('render');
    })

    $(document).on('click', '.btn-erase-pago', function () {
        var fila = $(this).data('id');
        $('.forma-pago-fila-' + fila).remove();

        var primera_fila = $('.forma-pago-fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[3];

        // var ultima_fila = $('.forma-pago-fila:last').prop('id');
        // var nro_ultima_fila = ultima_fila.split('-')[3];

        $('.div-btn-add-pago-' + nro_primera_fila).html('');
        $('.div-btn-add-pago-' + nro_primera_fila).append('<button type="button" class="btn btn-icon btn-success btn-add-pago" id="btn-add-pago-' + nro_primera_fila + '" data-id="' + nro_primera_fila + '"><i class="ri-add-fill"></i></button>');

        var formas_pagos = {!!json_encode($formas_pagos, JSON_HEX_TAG) !!}
        var forma_pago_seleccionada = $('#forma_pago-' + nro_primera_fila).val();
        $('#forma_pago-' + nro_primera_fila).selectpicker('destroy');
        $('#forma_pago-' + nro_primera_fila).empty();
        $('#forma_pago-' + nro_primera_fila).append('<option value="" selected disabled>Seleccionar...</option>');
        $.each(formas_pagos, function (index, value) {
            if (forma_pago_seleccionada != null) {
                if (value.id == forma_pago_seleccionada) {
                    $('#forma_pago-' + nro_primera_fila).append('<option value="' + value.id + '" selected>' + value.nombre + '</option>')
                } else {
                    $('#forma_pago-' + nro_primera_fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
                }
            } else {
                $('#forma_pago-' + nro_primera_fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
            }
        })
        $('#forma_pago-' + nro_primera_fila).selectpicker('render');

        calcularPendienteVuelto();
    })

    $(document).on('change', '.forma_pago', function () {
        var cantidad = $('.forma_pago').length;

        var primera_fila = $('.forma-pago-fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[3];
        var ultima_fila = $('.forma-pago-fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[3];

        var formas_pagos = {!!json_encode($formas_pagos, JSON_HEX_TAG) !!}
        var fila_seleccionado = $(this).data('id');
        var nuevo_seleccionado = $(this).val();

        if (fila_seleccionado == nro_primera_fila) {
            var otro_seleccionado = $('#forma_pago-' + nro_ultima_fila).val();
            $('#forma_pago-' + nro_ultima_fila).selectpicker('destroy');
            $('#forma_pago-' + nro_ultima_fila).empty();
            $('#forma_pago-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(formas_pagos, function (index, value) {
                if (otro_seleccionado) {
                    if (otro_seleccionado == value.id) {
                        $('#forma_pago-' + nro_ultima_fila).append('<option value="' + value.id + '" selected>' + value.nombre + '</option>')
                    } else if (nuevo_seleccionado != value.id) {
                        $('#forma_pago-' + nro_ultima_fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
                    }
                }
            })
            $('#forma_pago-' + nro_ultima_fila).selectpicker('render');
        } else {
            var otro_seleccionado = $('#forma_pago-' + nro_primera_fila).val();
            $('#forma_pago-' + nro_primera_fila).selectpicker('destroy');
            $('#forma_pago-' + nro_primera_fila).empty();
            $('#forma_pago-' + nro_primera_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(formas_pagos, function (index, value) {
                if (otro_seleccionado) {
                    if (otro_seleccionado == value.id) {
                        $('#forma_pago-' + nro_primera_fila).append('<option value="' + value.id + '" selected>' + value.nombre + '</option>')
                    } else if (nuevo_seleccionado != value.id) {
                        $('#forma_pago-' + nro_primera_fila).append('<option value="' + value.id + '">' + value.nombre + '</option>')
                    }
                }
            })
            $('#forma_pago-' + nro_primera_fila).selectpicker('render');
        }
    })

    $(document).on('change', '.nota_credito', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var fila = $(this).data('id');
        var monto = $('#nota_credito-' + fila + ' option:selected').data('subtext');
        $('#monto-nota_credito-' + fila).val(monto);

        calcularPendienteVuelto();
    })

    function save() {
        const formData = new FormData(document.getElementById('store-form'));
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: '{{route('cajero.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            if (response.mensaje) {
                window.localStorage.setItem('message', response.mensaje);
                window.localStorage.setItem('type', 'error');
            } else {
                window.localStorage.setItem('message', response.message);
                window.localStorage.setItem('type', 'success');
            }
            window.location.href = '{{route('cajero.create')}}';
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                if (key == 'pagos') {
                    $('.error-' + key + '-0').html('<strong class="small">El campo forma de pago es requerido.</strong>');
                }


                var partes = key.split('.');
                if (key.includes('forma_pago')) {
                    $('.error-' + partes[2] + '-' + partes[1]).html('<strong class="small">El campo forma de pago es requerido.</strong>');
                } else if (key.includes('efectivo')) {
                    if (partes[2] == 'efectivo') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo efectivo es requerido.</strong>');
                    }
                } else if (key.includes('debito')) {
                        if (partes[2] == 'banco_debito') {
                            $('.error-' + partes[2]).html('<strong class="small">El campo banco es requerido.');
                        } else if (partes[2] == 'monto_debito') {
                            $('.error-' + partes[2]).html('<strong class="small">El campo monto es requerido.');
                        } else if (partes[2] == 'numero_transaccion_debito') {
                            $('.error-' + partes[2]).html('<strong class="small">El campo número de transacción es requerido.');
                        } else if (partes[2] == 'fecha_transaccion_debito') {
                            $('.error-' + partes[2]).html('<strong class="small">El campo fecha de transacción es requerido.');
                        }
                } else if (key.includes('credito')) {
                    if (partes[2] == 'banco_credito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo banco es requerido.');
                    } else if (partes[2] == 'monto_credito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo monto es requerido.');
                    } else if (partes[2] == 'numero_transaccion_credito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo número de transacción es requerido.');
                    } else if (partes[2] == 'fecha_transaccion_credito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo fecha de transacción es requerido.');
                    }
                } else if (key.includes('transferencia')) {
                    if (partes[2] == 'banco_transferencia') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo banco de origen es requerido.');
                    } else if (partes[2] == 'cuenta_bancaria_transferencia') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo cuenta bancaria es requerido.');
                    } else if (partes[2] == 'numero_transaccion_transferencia') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo número de transacción es requerido.');
                    } else if (partes[2] == 'fecha_transaccion_transferencia') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo fecha de transacción es requerido.');
                    } else if (partes[2] == 'monto_transferencia') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo monto es requerido.');
                    }
                } else if (key.includes('deposito')) {
                    if (partes[2] == 'cuenta_bancaria_deposito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo cuenta bancaria es requerido.');
                    } else if (partes[2] == 'monto_deposito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo monto es requerido.');
                    } else if (partes[2] == 'numero_transaccion_deposito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo número de transacción es requerido.');
                    } else if (partes[2] == 'fecha_transaccion_deposito') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo fecha de transacción es requerido.');
                    }
                } else if (key.includes('cheque')) {
                    if (partes[2] == 'banco_cheque') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo banco de origen es requerido.');
                    } else if (partes[2] == 'monto_cheque') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo monto es requerido.');
                    } else if (partes[2] == 'numero_cheque') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo número de cheque es requerido.');
                    } else if (partes[2] == 'numero_serie_cheque') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo número de serie es requerido.');
                    } else if (partes[2] == 'emisor_cheque') {
                        $('.error-' + partes[2]).html('<strong class="small">El campo emisor es requerido.');
                    }
                }
            })
        })
    }

    var total_pagar = 0;
    function calcularTotalPagar() {
        var suma = 0;

        $('#cobros-list .a_pagar').each(function () {
            if ($(this).attr('type') != 'hidden') {
                var monto = $(this).val();
                monto = parseInt(monto.replace(/\./g, ''));
                suma += monto;
                total_pagar = suma;
                $('#monto_total').val(suma);

                $('#total_pagar').html('Gs. ' + Intl.NumberFormat('de-DE').format(parseInt(suma)));
                $('#total_pagar_pago').html('Gs. ' + Intl.NumberFormat('de-DE').format(parseInt(suma)));
                $('#saldo_pago').html('Gs. ' + Intl.NumberFormat('de-DE').format(parseInt(suma)));
            }
        })
    }

    function calcularPendienteVuelto() {
        var total_pagado = 0;
        $('.pagado').each(function () {
            var monto = $(this).val();
            monto = monto.replace(/\./g, '');
            monto = monto ? parseInt(monto) : 0;
            if ($(this).attr('type') != 'hidden') {
                total_pagado += monto;
            }
        })

        if (total_pagado < total_pagar) {
            $('#saldo_pago').html('Gs. ' + Intl.NumberFormat('de-DE').format(parseInt(total_pagar - total_pagado)));
            $('.btn-add-pago').prop('disabled', false);
        } else {
            $('#saldo_pago').html('Gs. 0');
        }

        var pendiente = $('#saldo_pago').text();
        pendiente = pendiente.replace('Gs. ', '');
        pendiente = parseInt(pendiente.replace(/\./g, ''));

        if (pendiente == 0) {
            $('#facturar-btn').prop('disabled', false);
        } else {
            $('#facturar-btn').prop('disabled', true);
        }

        var vuelto = total_pagado - total_pagar;
        if (vuelto < 0) {
            vuelto = 0;
        }
        $('#vuelto').html('');
        $('#vuelto').html('Gs. ' + Intl.NumberFormat('de-DE').format(parseInt(vuelto)));
    }

    function get_notas_creditos(url, fila) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if (response.notas_creditos.length > 0) {
                var datos_forma_pago = `<div class="row">
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="nota_credito-${fila}">Nota de Crédito</label>
                                            <select class="form-control selectpicker nota_credito" id="nota_credito-${fila}" name="pagos[${fila}][nota_credito]" data-live-search="true" data-id="${fila}">

                                            </select>
                                            <span class="text-danger error-nota_credito">

                                            </span>
                                        </div>
                                        <div class="col-lg-6 mb-3 text-center">
                                            <label class="form-label" for="monto-nota_credito-${fila}">Monto</label>
                                            <input type="text" class="form-control text-center monto-nota_credito pagado" id="monto-nota_credito-${fila}" name="pagos[${fila}][monto_nota_credito]" data-id="${fila}" readonly>
                                            <span class="text-danger error-monto_nota_credito">

                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    `;

                $('#forma-pago-campos-' + fila).append(datos_forma_pago);

                $('#nota_credito-' + fila).selectpicker('destroy');
                $('#nota_credito-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(response.notas_creditos, function (index, value) {
                    $('#nota_credito-' + fila).append('<option value="' + value.id + '" data-subtext="' + value.monto_total + '">' + value.numero_nota_credito + '</option>')
                })
                $('#nota_credito-' + fila).selectpicker('render');
            } else {
                $('#forma_pago-' + fila).selectpicker('val', '');
                $('#forma-pago-campos-' + fila).html('');
                $('#forma-pago-campos-' + fila).append('<div class="alert alert-warning text-center">No hay notas de crédito disponibles para este cliente. Favor seleccione otra forma de pago.</div>');
            }
        })
    }
</script>
