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

    $('#unidad_negocio').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('compras.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    $(document).on('change', '.centro_costo', function () {
        var id = $(this).val();
        var fila = $(this).data('id');
        var url = "{{ route('compras.get_subcentros_costos', ":id") }}";
        url = url.replace(':id', id);

        get_subcentros_costos(url, fila);
    });

    $(document).on('change', '.precio_costo', function () {
        calcularSubtotales();
    })

    $(document).on('change', '.cantidad', function () {
        calcularSubtotales();
    })

    var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
    var articulo_accion_add;
    var articulosSelected = [];
    $(document).on('click', '.btn-add', function () {
        var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
        var centros_costos = {!!json_encode($centros_costos, JSON_HEX_TAG) !!};
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.articulo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.cantidad-' + nro_ultima_fila).val() != '' && $('.precio_costo-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-2 mb-2 text-center" id="div-articulo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control articulo-${nro_nueva_fila} articulo @error('detalles.${nro_nueva_fila}.articulo') is-invalid @enderror" id="articulo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][articulo]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-articulo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-descripcion-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center descripcion-${nro_nueva_fila} descripcion @error('detalles.${nro_nueva_fila}.descripcion') is-invalid @enderror" id="detalles[${nro_nueva_fila}][descripcion]" name="detalles[${nro_nueva_fila}][descripcion]" value="{{old('detalles.${nro_nueva_fila}.descripcion')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-descripcion-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-centro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control centro_costo-${nro_nueva_fila} centro_costo @error('detalles.${nro_nueva_fila}.centro_costo') is-invalid @enderror" id="centro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][centro_costo]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-centro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-subcentro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control subcentro_costo-${nro_nueva_fila} subcentro_costo @error('detalles.${nro_nueva_fila}.subcentro_costo') is-invalid @enderror" id="subcentro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][subcentro_costo]" data-live-search="true" data-id="${nro_nueva_fila}" disabled>
                                            <option value="" selected disabled>Seleccionar...</option>
                                        </select>
                                        <span class="invalid-feedback error-subcentro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-cantidad-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center cantidad-${nro_nueva_fila} cantidad @error('detalles.${nro_nueva_fila}.cantidad') is-invalid @enderror" id="detalles[${nro_nueva_fila}][cantidad]" name="detalles[${nro_nueva_fila}][cantidad]" value="{{old('detalles.${nro_nueva_fila}.cantidad')}}" data-id="${nro_nueva_fila}" placeholder="1">
                                        <span class="invalid-feedback error-cantidad-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-precio_costo-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center precio_costo-${nro_nueva_fila} precio_costo @error('detalles.${nro_nueva_fila}.precio_costo') is-invalid @enderror" id="detalles[${nro_nueva_fila}][precio_costo]" name="detalles[${nro_nueva_fila}][precio_costo]" value="{{old('detalles.${nro_nueva_fila}.precio_costo')}}" data-id="${nro_nueva_fila}" placeholder="100.000">
                                        <span class="invalid-feedback error-precio_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-iva-${nro_nueva_fila}">
                                        <select class="selectpicker form-control iva-${nro_nueva_fila} iva @error('detalles.0.iva') is-invalid @enderror" id="iva-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][iva]" data-live-search="true" data-id="${nro_nueva_fila}">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="10">10%</option>
                                            <option value="5">5%</option>
                                            <option value="0">EXENTO</option>
                                        </select>
                                        <span class="invalid-feedback error-iva-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subtotal-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center subtotal-${nro_nueva_fila} subtotal @error('detalles.${nro_nueva_fila}.subtotal') is-invalid @enderror" id="detalles[${nro_nueva_fila}][subtotal]" data-id="${nro_nueva_fila}" placeholder="${nro_nueva_fila}" readonly>
                                        <span class="invalid-feedback error-subtotal-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-2 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            articulo_accion_add = $('#btn-add-' + fila).detach();

            $('#articulo-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            // articulosSelected.forEach(item => {
            //     for (let index in articulos) {
            //         if (articulos[index].id == item) {
            //             articulos.splice(index, 1);
            //         }
            //     }
            // });

            $('.articulo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(articulos, function (i, val) {
                $('.articulo-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            })
            $('.articulo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.centro_costo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(centros_costos, function (i, val) {
                $('.centro_costo-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.centro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.subcentro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            $('.iva-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            articulo_accion_add = $('#btn-add-' + nro_nueva_fila);

            // new Cleave ('.cantidad-' + nro_nueva_fila, formatoSeparadorMiles);
            // new Cleave ('.precio_costo-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    // $(document).on('change', '.articulo', function () {
    //     var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
    //     var cantidad_filas = $('.fila').length;
    //     var fila = $(this).data('id');
    //     var primera_fila = $('.fila:first').prop('id');
    //     var nro_primera_fila = primera_fila.split('-')[1];
    //     var ultima_fila = $('.fila:last').prop('id');
    //     var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

    //     var valor = $('.articulo-' + fila + ' option:selected').val();

    //     articulosSelected = [];
    //     for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
    //         var valor = $('.articulo-' + index + ' option:selected').val();
    //         if ((parseInt(valor) || 0) != 0) {
    //             articulosSelected.push(parseInt(valor));
    //         }
    //     }
    //     articulosSelected.sort();

    //     articulosSelected.forEach(item => {
    //         for (let index in articulos) {
    //             if (articulos[index].id == item) {
    //                 articulos.splice(index, 1);
    //             }
    //         }
    //     });

    //     for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
    //         var valor = $('.articulo-' + index + ' option:selected').val();
    //         if ((parseInt(valor) || 0) != 0) {
    //             var texto = $('.articulo-' + index + ' option:selected').text();
    //             $('.articulo-' + index).selectpicker('destroy');
    //             $('.articulo-' + index + ' option').each(function () {
    //                 $(this).remove();
    //             });

    //             $('.articulo-' + index).append('<option value="" disabled>Seleccionar...</option>');
    //             $('.articulo-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
    //             $.each(articulos, function (i, val) {
    //                 $('.articulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
    //             })
    //             $('.articulo-' + index).addClass('selectpicker').selectpicker('render');
    //         } else {
    //             $('.articulo-' + index).selectpicker('destroy');
    //             $('.articulo-' + index + ' option').each(function () {
    //                 $(this).remove();
    //             });

    //             $('.articulo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
    //             $.each(articulos, function (i, val) {
    //                 $('.articulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
    //             })
    //             $('.articulo-' + index).addClass('selectpicker').selectpicker('render');
    //         }
    //     }

    //     var primera_fila = $('.fila:first').prop('id');
    //     var nro_primera_fila = primera_fila.split('-')[1];
    //     insertarLabels(nro_primera_fila);
    // })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.articulo-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        articulo_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        // var indice_eliminar = articulosSelected.indexOf(parseInt(valor));
        // if (articulosSelected.length != 0) {
        //     articulosSelected.splice(indice_eliminar, 1);
        // }
        // articulosSelected.sort();

        // if (articulosSelected.length != 0) {
        //     articulosSelected.forEach(item => {
        //         for (let index in articulos) {
        //             if (articulos[index].id == item) {
        //                 articulos.splice(index, 1);
        //             }
        //         }
        //     });
        // }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.articulo-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.articulo-' + nro_ultima_fila + ' option:selected').text();
                $('.articulo-' + nro_ultima_fila).selectpicker('destroy');
                $('.articulo-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.articulo-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.articulo-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(articulos, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                    }
                })
                $('.articulo-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.articulo-' + nro_ultima_fila).selectpicker('destroy');
                $('.articulo-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.articulo-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(articulos, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                    }
                })
                $('.articulo-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.articulo-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.articulo-' + index + ' option:selected').text();
                    $('.articulo-' + index).selectpicker('destroy');
                    $('.articulo-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.articulo-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.articulo-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(articulos, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.articulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                        }
                    })
                    $('.articulo-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.articulo-' + index).selectpicker('destroy');
                    $('.articulo-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.articulo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(articulos, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                        }
                    })
                    $('.articulo-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularSubtotales();
    })

    $('#orden_compra').change(function () {
        var id = $(this).val();
        var url = "{{ route('compras.get_orden_compra', ":id") }}";
        url = url.replace(':id', id);

        get_orden_compra(url);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-articulo').text() == '') {
            $('<label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>').insertBefore('#articulo-' + primera_fila);
        }
        if ($('.label-descripcion').text() == '') {
            $('<label class="form-label label-descripcion">Descripción</label>').insertBefore('.descripcion-' + primera_fila);
        }
        if ($('.label-centro_costo').text() == '') {
            $('<label class="form-label label-centro_costo">CC1 <span class="text-danger">(*)</span></label>').insertBefore('.centro_costo-' + primera_fila);
        }
        if ($('.label-SUBcentro_costo').text() == '') {
            $('<label class="form-label label-subcentro_costo">CC2 <span class="text-danger">(*)</span></label>').insertBefore('.SUBcentro_costo-' + primera_fila);
        }
        if ($('.label-cantidad').text() == '') {
            $('<label class="form-label label-cantidad">Cantidad <span class="text-danger">(*)</span></label>').insertBefore('.cantidad-' + primera_fila);
        }
        if ($('.label-iva').text() == '') {
            $('<label class="form-label label-iva">I.V.A. <span class="text-danger">(*)</span></label>').insertBefore('.iva-' + primera_fila);
        }
        if ($('.label-precio_costo').text() == '') {
            $('<label class="form-label label-precio_costo">Costo <span class="text-danger">(*)</span></label>').insertBefore('.precio_costo-' + primera_fila);
        }
        if ($('.label-subtotal').text() == '') {
            $('.div-label-subtotal-' + primera_fila).html('<label class="form-label label-subtotal">Subtotal</label>')
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularSubtotales() {
        $('.precio_costo').each(function () {
            var fila = $(this).data('id');
            var cantidad = parseFloat($('.cantidad-' + fila).val());
            var precio_costo = parseFloat($(this).val());
            var subtotal = cantidad * precio_costo;
            if (isNaN(subtotal) || subtotal == null || $('.articulo-' + fila + ' option:selected').val() == '') {
                subtotal = 0;
            }
            $('.subtotal-' + fila).val(Intl.NumberFormat('de-DE').format(subtotal));
        })

        calcularTotal();
    }

    function calcularTotal() {
        var total = 0;
        $('.subtotal').each(function () {
            var subtotal = $(this).val();
            subtotal = subtotal.replace(/\./g, '');
            total += parseInt(subtotal);
        })
        if (isNaN(total) || total == null) {
            total = 0;
        }
        $('#monto_total').val(Intl.NumberFormat('de-DE').format(total));
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

    const formatoNumero = {
        blocks: [3,3,7],
        delimiter: '-',
        numericOnly: true
    };

    $(document).ready(function () {
        // new Cleave ('.cantidad-0', formatoSeparadorMiles);
        // new Cleave ('.precio_costo-0', formatoSeparadorMiles);
        new Cleave ('#numero_factura', formatoNumero);
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            var proveedorSelected = $('#proveedor option:selected').val();
            if (proveedorSelected) {
                $('#proveedor').trigger('change');
                setTimeout(() => {
                    var orden_compra_error = @json(old('orden_compra'));
                    if (orden_compra_error) {
                        $('#orden_compra').selectpicker('destroy')
                        $('#orden_compra').val(orden_compra_error);
                        $('#orden_compra').selectpicker('render')
                        $('#orden_compra').trigger('change');
                    }
                }, 1000);
            }

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.articulo-' + index).val(arrayFilas[index].articulo);
                    $('.articulo-' + index).selectpicker('val', arrayFilas[index].articulo);
                    $('.descripcion-' + index).val(arrayFilas[index].descripcion);
                    $('.centro_costo-' + index).val(arrayFilas[index].centro_costo);
                    $('.centro_costo-' + index).selectpicker('val', arrayFilas[index].centro_costo);
                    $('.subcentro_costo-' + index).val(arrayFilas[index].subcentro_costo);
                    $('.subcentro_costo-' + index).selectpicker('val', arrayFilas[index].subcentro_costo);
                    $('.cantidad-' + index).val(arrayFilas[index].cantidad);
                    $('.precio_costo-' + index).val(arrayFilas[index].precio_costo);
                    $('.iva-' + index).val(arrayFilas[index].iva);
                    $('.iva-' + index).selectpicker('val', arrayFilas[index].iva);

                    var centro_costoSelected = $('.centro_costo-' + index + ' option:selected').val();
                    if (centro_costoSelected) {
                        $('#centro_costo-' + index).trigger('change');
                    }
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                var unidad_negocioSelected = $('#unidad_negocio option:selected').val();
                if (unidad_negocioSelected) {
                    $('#unidad_negocio').trigger('change');
                }

                var centro_costoSelected = $('#centro_costo-' + nro_primera_fila + ' option:selected').val();
                if (centro_costoSelected) {
                    $('#centro_costo-' + nro_primera_fila).trigger('change');
                }

                calcularSubtotales();
            })
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

    function get_subunidades_negocios(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subunidad_negocio').selectpicker('destroy');
            $('#subunidad_negocio').empty();
            $('#subunidad_negocio').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subunidades_negocios, function (index, value) {
                $('#subunidad_negocio').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subunidades_negocios.length == 1) {
                    $('#subunidad_negocio').val(value.id);
                }
            })
            $('#subunidad_negocio').prop('disabled', false);
            $('#subunidad_negocio').selectpicker('render');
        })
    }

    function get_subcentros_costos(url, fila) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subcentro_costo-' + fila).selectpicker('destroy');
            $('#subcentro_costo-' + fila).empty();
            $('#subcentro_costo-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subcentros_costos, function (index, value) {
                $('#subcentro_costo-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subcentros_costos.length == 1) {
                    $('#subcentro_costo-' + fila).val(value.id);
                }
            })
            $('#subcentro_costo-' + fila).prop('disabled', false);
            $('#subcentro_costo-' + fila).selectpicker('render');
        })
    }

    var filaAdd = '';
    function get_orden_compra(url) {
        filaAdd = '';
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#moneda').selectpicker('destroy');
            $('#moneda').val(response.orden_compra.moneda_id);
            $('#moneda').css('pointer-events', 'none');

            $('#condicion_compra').selectpicker('destroy');
            $('#condicion_compra').val(response.orden_compra.condicion_compra);
            $('#condicion_compra').css('pointer-events', 'none');

            if (response.orden_compra.condicion_compra == 'CR') {
                $('#div-credito_a').removeClass('d-none');
                $('#credito_a').selectpicker('destroy');
                $('#credito_a').val(response.orden_compra.credito_a);
                $('#credito_a').css('pointer-events', 'none');
            }

            $('.fila').html('');

            $.each(response.orden_compra.detalles, function (index, value) {
                filaAdd += `<div class="mb-2 fila" id="fila-${index}">
                                <div class="row">
                                    <div class="col-lg-2 mb-2 text-center" id="div-articulo-${index}">
                                        ${index == 0 ? '<label class="form-label label-articulo">Artículo</label>' : ''}
                                        <input type="hidden" name="detalles[${index}][articulo]" value="${value.articulo_id}"></input>
                                        <input type="text" class="form-control articulo-${index} articulo @error('detalles.${index}.articulo') is-invalid @enderror" value="${value.articulo_nombre}" data-id="${index}" readonly></input>
                                        <span class="invalid-feedback error-articulo-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-descripcion-${index}">
                                        ${index == 0 ? '<label class="form-label label-descripcion">Descripción</label>' : ''}
                                        <input type="text" class="form-control descripcion-${index} descripcion @error('detalles.${index}.descripcion') is-invalid @enderror" id="descripcion-${index}" name="detalles[${index}][descripcion]" value="${value.descripcion}" data-id="${index}" readonly></input>
                                        <span class="invalid-feedback error-descripcion-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-centro_costo-${index}">
                                        ${index == 0 ? '<label class="form-label label-centro_costo">CC1 <span class="text-danger">(*)</span></label>' : ''}
                                        <select class="selectpicker form-control centro_costo-${index} centro_costo @error('detalles.${index}.centro_costo') is-invalid @enderror" id="centro_costo-${index}" name="detalles[${index}][centro_costo]" data-live-search="true" data-id="${index}">

                                        </select>
                                        <span class="invalid-feedback error-centro_costo-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subcentro_costo-${index}">
                                        ${index == 0 ? '<label class="form-label label-subcentro_costo">CC2 <span class="text-danger">(*)</span></label>' : ''}
                                        <select class="selectpicker form-control subcentro_costo-${index} subcentro_costo @error('detalles.${index}.subcentro_costo') is-invalid @enderror" id="subcentro_costo-${index}" name="detalles[${index}][subcentro_costo]" data-live-search="true" data-id="${index}" disabled>
                                            <option value="" selected disabled>Seleccionar...</option>
                                        </select>
                                        <span class="invalid-feedback error-subcentro_costo-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-cantidad-${index}">
                                        ${index == 0 ? '<label class="form-label label-cantidad">Cantidad</label>' : ''}
                                        <input type="text" class="form-control text-center cantidad-${index} cantidad @error('detalles.${index}.cantidad') is-invalid @enderror" id="cantidad-${index}" name="detalles[${index}][cantidad]" value="${value.cantidad}" data-id="${index}" readonly></input>
                                        <span class="invalid-feedback error-cantidad-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-precio_costo-${index}">
                                        ${index == 0 ? '<label class="form-label label-precio_costo">Costo</label>' : ''}
                                        <input type="hidden" name="detalles[${index}][precio_costo]" value="${value.precio_costo}" data-id="${index}"></input>
                                        <input type="text" class="form-control text-center precio_costo-${index} precio_costo @error('detalles.${index}.precio_costo') is-invalid @enderror" value="${Intl.NumberFormat('de-DE').format(value.precio_costo)}" data-id="${index}" readonly></input>
                                        <span class="invalid-feedback error-precio_costo-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-iva-${index}">
                                        ${index == 0 ? '<label class="form-label label-iva">I.V.A. <span class="text-danger">(*)</span></label>' : ''}
                                        <select class="selectpicker form-control iva-${index} iva @error('detalles.${index}.iva') is-invalid @enderror" id="iva-${index}" name="detalles[${index}][iva]" data-live-search="true" data-id="${index}">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="10">10%</option>
                                            <option value="5">5%</option>
                                            <option value="0">EXENTO</option>
                                        </select>
                                        <span class="invalid-feedback error-iva-${index}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-subtotal-${index}">
                                        ${index == 0 ? '<label class="form-label label-subtotal">Subtotal</label>' : ''}
                                        <input type="text" class="form-control text-center subtotal-${index} subtotal @error('detalles.${index}.subtotal') is-invalid @enderror" value="${Intl.NumberFormat('de-DE').format(value.cantidad * value.precio_costo)}" data-id="${index}" readonly></input>
                                        <span class="invalid-feedback error-subtotal-${index}" role="alert">

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="articulo-fila-${index}">

                            </div>`;
            })

            $('#articulo-fila').append(filaAdd);

            var centros_costos = {!!json_encode($centros_costos, JSON_HEX_TAG) !!};
            $.each(response.orden_compra.detalles, function(index, value) {
                $('.centro_costo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(centros_costos, function (i, val) {
                    $('.centro_costo-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                })
                $('.centro_costo-' + index).addClass('selectpicker').selectpicker('render');

                $('.subcentro_costo-' + index).selectpicker('render');
                $('.iva-' + index).selectpicker('render');
            })

            calcularTotal();
        })
    }
</script>
