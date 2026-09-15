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

    $(document).on('change', '.precio_costo', function () {
        calcularSubtotales();
    })

    $(document).on('change', '.cantidad', function () {
        calcularSubtotales();
    })

    // var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
    var articulo_accion_add;
    // var articulosSelected = [];
    $(document).on('click', '.btn-add', function () {
        var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.articulo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.cantidad-' + nro_ultima_fila).val() != '' && $('.precio_costo-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-3 mb-2 text-center" id="div-articulo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control articulo-${nro_nueva_fila} articulo @error('detalles.${nro_nueva_fila}.articulo') is-invalid @enderror" id="articulo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][articulo]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-articulo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-3 mb-2 text-center" id="div-descripcion-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center descripcion-${nro_nueva_fila} descripcion @error('detalles.${nro_nueva_fila}.descripcion') is-invalid @enderror" id="detalles[${nro_nueva_fila}][descripcion]" name="detalles[${nro_nueva_fila}][descripcion]" value="{{old('detalles.${nro_nueva_fila}.descripcion')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-descripcion-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-cantidad-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center cantidad-${nro_nueva_fila} cantidad @error('detalles.${nro_nueva_fila}.cantidad') is-invalid @enderror" id="detalles[${nro_nueva_fila}][cantidad]" name="detalles[${nro_nueva_fila}][cantidad]" value="{{old('detalles.${nro_nueva_fila}.cantidad')}}" data-id="${nro_nueva_fila}" placeholder="1">
                                        <span class="invalid-feedback error-cantidad-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-precio_costo-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center precio_costo-${nro_nueva_fila} precio_costo @error('detalles.${nro_nueva_fila}.precio_costo') is-invalid @enderror" id="detalles[${nro_nueva_fila}][precio_costo]" name="detalles[${nro_nueva_fila}][precio_costo]" value="{{old('detalles.${nro_nueva_fila}.precio_costo')}}" data-id="${nro_nueva_fila}" placeholder="100.000">
                                        <span class="invalid-feedback error-precio_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subtotal-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center subtotal-${nro_nueva_fila} subtotal @error('detalles.${nro_nueva_fila}.subtotal') is-invalid @enderror" id="detalles[${nro_nueva_fila}][subtotal]" value="{{old('detalles.${nro_nueva_fila}.subtotal')}}" data-id="${nro_nueva_fila}" placeholder="0" readonly>
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
                $('.articulo-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.articulo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            articulo_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.cantidad-' + nro_nueva_fila, formatoSeparadorMiles);
            new Cleave ('.precio_costo-' + nro_nueva_fila, formatoSeparadorMiles);
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
    //                 $('.articulo-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
    //             })
    //             $('.articulo-' + index).addClass('selectpicker').selectpicker('render');
    //         } else {
    //             $('.articulo-' + index).selectpicker('destroy');
    //             $('.articulo-' + index + ' option').each(function () {
    //                 $(this).remove();
    //             });

    //             $('.articulo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
    //             $.each(articulos, function (i, val) {
    //                 $('.articulo-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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
                        $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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
                        $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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
                            $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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
                            $('.articulo-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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

    function insertarLabels(primera_fila) {
        if ($('.label-articulo').text() == '') {
            $('<label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>').insertBefore('#articulo-' + primera_fila);
        }
        if ($('.label-descripcion').text() == '') {
            $('<label class="form-label label-descripcion">Descripción</label>').insertBefore('.descripcion-' + primera_fila);
        }
        if ($('.label-cantidad').text() == '') {
            $('<label class="form-label label-cantidad">Cantidad <span class="text-danger">(*)</span></label>').insertBefore('.cantidad-' + primera_fila);
        }
        if ($('.label-precio_costo').text() == '') {
            $('<label class="form-label label-precio_costo">Costo <span class="text-danger">(*)</span></label>').insertBefore('.precio_costo-' + primera_fila);
        }
        if ($('.label-subtotal').text() == '') {
            $('<label class="form-label label-subtotal">Subtotal</label>').insertBefore('.subtotal-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularSubtotales() {
        $('.precio_costo').each(function () {
                var fila = $(this).data('id');
                var cantidad = $('.cantidad-' + fila).val();
                cantidad = cantidad.replace(/\./g, '');
                var precio_costo = $(this).val();
                precio_costo = precio_costo.replace(/\./g, '');
                var subtotal = parseInt(cantidad) * parseInt(precio_costo);
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

    $(document).ready(function () {
        new Cleave ('.cantidad-0', formatoSeparadorMiles);
        new Cleave ('.precio_costo-0', formatoSeparadorMiles);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        // articulosSelected = [];
        // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
        //     var valor = $('.articulo-' + index + ' option:selected').val();
        //     if ((parseInt(valor) || 0) != 0) {
        //         articulosSelected.push(parseInt(valor));
        //     }
        // }
        // articulosSelected.sort();

        // articulosSelected.forEach(item => {
        //     for (let index in articulos) {
        //         if (articulos[index].id == item) {
        //             articulos.splice(index, 1);
        //         }
        //     }
        // });

        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};
            var arrayFilasLength = arrayFilas.length;
            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0 && index > arrayFilasLength) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.articulo-' + index).val(arrayFilas[index].articulo);
                    $('.articulo-' + index).selectpicker('val', arrayFilas[index].articulo);
                    $('.descripcion-' + index).val(arrayFilas[index].descripcion);
                    $('.cantidad-' + index).val(arrayFilas[index].cantidad);
                    $('.precio_costo-' + index).val(arrayFilas[index].precio_costo);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                // articulosSelected = [];
                // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                //     var valor = $('.articulo-' + index + ' option:selected').val();
                //     if ((parseInt(valor) || 0) != 0) {
                //         articulosSelected.push(parseInt(valor));
                //     }
                // }
                // articulosSelected.sort();

                // articulosSelected.forEach(item => {
                //     for (let index in articulos) {
                //         if (articulos[index].id == item) {
                //             articulos.splice(index, 1);
                //         }
                //     }
                // });

                // $('.articulo').trigger('change');

                calcularSubtotales();
            })
        }

        //obtener los errors de articulo
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
</script>
