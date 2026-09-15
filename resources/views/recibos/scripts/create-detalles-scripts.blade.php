<script type="module">
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

    var ventas;
    $('#cliente').on('change', function () {
        var seleccionado = $(this).val();
        var clientes = {!!json_encode($clientes, JSON_HEX_TAG) !!}
        $.each(clientes, function (index, value) {
            if (seleccionado == value.id) {
                $('#numero_documento').val(value.numero_documento);
            }
        })

        var url = "{{ route('recibos.get_ventas', ":id") }}";
        url = url.replace(':id', seleccionado);
        
        get_ventas(url, 'SI');
    })

    var venta_accion_add;
    var ventasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.venta-' + nro_ultima_fila + ' option:selected').val() != '' && $('.monto-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-3 mb-2 text-center" id="div-venta-${nro_nueva_fila}">
                                        <select class="selectpicker form-control venta-${nro_nueva_fila} venta @error('detalles.${nro_nueva_fila}.venta') is-invalid @enderror" id="venta-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][venta]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-venta-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-saldo_pendiente-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center saldo_pendiente-${nro_nueva_fila} saldo_pendiente" id="saldo_pendiente-${nro_nueva_fila} data-id="${nro_nueva_fila}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-monto-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center monto-${nro_nueva_fila} monto @error('detalles.${nro_nueva_fila}.monto') is-invalid @enderror" id="detalles[${nro_nueva_fila}][monto]" name="detalles[${nro_nueva_fila}][monto]" value="{{old('detalles.${nro_nueva_fila}.monto', 0)}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-monto-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            venta_accion_add = $('#btn-add-' + fila).detach();

            $('#venta-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            ventasSelected.forEach(item => {
                for (let index in ventas) {
                    if (ventas[index].id == item) {
                        ventas.splice(index, 1);
                    }
                }
            });

            $('.venta-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(ventas, function (i, val) {
                $('.venta-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
            })
            $('.venta-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            venta_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.monto-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.venta', function () {
        var cliente = $('#cliente option:selected').val();
        var url = "{{ route('recibos.get_ventas', ":id") }}";
        url = url.replace(':id', cliente);
        get_ventas(url, 'NO');

        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;
        var valor = parseInt($(this).val());

        $.each(ventas, function (index, value) {
            if (value.id == valor) {
                $('.saldo_pendiente-' + fila).val(Intl.NumberFormat('de-DE').format(parseInt(value.saldo)));
            }
        })

        ventasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.venta-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                ventasSelected.push(parseInt(valor));
            }
        }
        ventasSelected.sort();

        ventasSelected.forEach(item => {
            for (let index in ventas) {
                if (ventas[index].id == item) {
                    ventas.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.venta-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.venta-' + index + ' option:selected').text();
                $('.venta-' + index).selectpicker('destroy');
                $('.venta-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.venta-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.venta-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(ventas, function (i, val) {
                    $('.venta-' + index).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                })
                $('.venta-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.venta-' + index).selectpicker('destroy');
                $('.venta-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.venta-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(ventas, function (i, val) {
                    $('.venta-' + index).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                })
                $('.venta-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        calcularTotal();
    })

    $(document).on('keyup', '.monto', function () {
        var fila = $(this).data('id');
        calcularTotal();
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var cliente = $('#cliente option:selected').val();
        var url = "{{ route('recibos.get_ventas', ":id") }}";
        url = url.replace(':id', cliente);
        get_ventas(url, 'NO');

        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.venta-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        venta_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = ventasSelected.indexOf(parseInt(valor));
        if (ventasSelected.length != 0) {
            ventasSelected.splice(indice_eliminar, 1);
        }
        ventasSelected.sort();

        if (ventasSelected.length != 0) {
            ventasSelected.forEach(item => {
                for (let index in ventas) {
                    if (ventas[index].id == item) {
                        ventas.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.venta-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.venta-' + nro_ultima_fila + ' option:selected').text();
                $('.venta-' + nro_ultima_fila).selectpicker('destroy');
                $('.venta-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.venta-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.venta-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(ventas, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.venta-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                    }
                })
                $('.venta-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.venta-' + nro_ultima_fila).selectpicker('destroy');
                $('.venta-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.venta-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(ventas, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.venta-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                    }
                })
                $('.venta-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }


        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.venta-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.venta-' + index + ' option:selected').text();
                    $('.venta-' + index).selectpicker('destroy');
                    $('.venta-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.venta-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.venta-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(ventas, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.venta-' + index).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                        }
                    })
                    $('.venta-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.venta-' + index).selectpicker('destroy');
                    $('.venta-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.venta-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(ventas, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.venta-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.numero_factura + '</option>');
                        }
                    })
                    $('.venta-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotal();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-venta').text() == '') {
            $('<label class="form-label label-venta">Venta <span class="text-danger">(*)</span></label>').insertBefore('.venta-' + primera_fila);
        }
        if ($('.label-saldo_pendiente').text() == '') {
            $('<label class="form-label label-saldo_pendiente">Saldo Pendiente</label>').insertBefore('.monto-' + primera_fila);
        }
        if ($('.label-monto').text() == '') {
            $('<label class="form-label label-monto">Monto a Cobrar <span class="text-danger">(*)</span></label>').insertBefore('.monto-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotal() {
        var total = 0;
        $('.monto:visible').each(function () {
            var valor = $(this).val();
            if (valor) {
                valor = valor.replace(/\./g, '');
                total += parseInt(valor);
            }
        })

        $('.total').val(Intl.NumberFormat('de-DE').format(parseInt(total)))
    }

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    var monto_0 = new Cleave ('.monto-0', formatoSeparadorMiles);
    $(document).ready(function () {
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.venta-' + index).val(arrayFilas[index].venta);
                    $('.venta-' + index).selectpicker('val', arrayFilas[index].venta);
                    $('.monto-' + index).val(arrayFilas[index].monto);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                ventasSelected = [];
                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.venta-' + index + ' option:selected').val();
                    if ((parseInt(valor) || 0) != 0) {
                        ventasSelected.push(parseInt(valor));
                    }
                }
                ventasSelected.sort();

                ventasSelected.forEach(item => {
                    for (let index in ventas) {
                        if (ventas[index].id == item) {
                            ventas.splice(index, 1);
                        }
                    }
                });

                $('.venta').trigger('change');
            })

            var metodo_pago = @json(old('metodo_pago'));
            if (metodo_pago == 2) {
                $('.div-debito').removeClass('d-none');
            } else if (metodo_pago == 3) {
                $('.div-credito').removeClass('d-none');
            } else if (metodo_pago == 4) {
                $('.div-transferencia').removeClass('d-none');
            } else if (metodo_pago == 5) {
                $('.div-deposito').removeClass('d-none');
            }
        }

        //obtener los errors de los detalles
        var arrayFilasErrors = {!! json_encode($errors->get('detalles.*'), JSON_HEX_TAG) !!};
        $.each(arrayFilasErrors, function (index, value) {
            if (index != '') {
                var numero = index.split('.')[1];
                var tipoError = index.split('.')[2];
                $('.' + tipoError + '-' + numero).addClass('is-invalid');
                $('.error-' + tipoError + '-' + numero).append('<strong>' + value + '</strong>');
            }
        })
    })

    function get_ventas(url, tipo) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if (response.ventas.length == 0) {
                $('#cliente').selectpicker('val', '');
                $('#numero_documento').val('');
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
                    text: 'El cliente no cuenta con ninguna factura pendiente de pago, por favor seleccione otro de la lista.',
                })
            } else {
                if (tipo == 'SI') {
                    $('#venta-0').selectpicker('destroy');
                    $('#venta-0').empty();
                    $('#venta-0').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(response.ventas, function (index, value) {
                        $('#venta-0').append('<option value="' + value.id + '">' + value.numero_factura + '</option>');
                        if (response.ventas.length == 1) {
                            $('#venta-0').val(value.id);
                            $('.saldo_pendiente-0').val(Intl.NumberFormat('de-DE').format(parseInt(value.saldo)));
                            $('.btn-add').prop('disabled', true);
                            calcularTotal();
                        }
                    })
                    $('#venta-0').prop('disabled', false);
                    $('#venta-0').selectpicker('render');
                }
                ventas = response.ventas;
            }
        })
    }
</script>
