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
        var url = "{{ route('ventas.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    $(document).on('change', '.centro_costo', function () {
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (!errors) {
            var id = $(this).val();
            var fila = $(this).data('id');
            var url = "{{ route('ventas.get_subcentros_costos', ":id") }}";
            url = url.replace(':id', id);

            get_subcentros_costos(url, fila);
        }
    });

    var articulo_accion_add;
    $(document).on('click', '.btn-add', function () {
        var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
        var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
        var centros_costos = {!!json_encode($centros_costos, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.alumno-' + nro_ultima_fila + ' option:selected').val() != '' && $('.articulo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.centro_costo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.subcentro_costo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.precio-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-2 mb-2 text-center" id="div-alumno-${nro_nueva_fila}">
                                        <select class="selectpicker form-control alumno-${nro_nueva_fila} alumno @error('detalles.${nro_nueva_fila}.alumno') is-invalid @enderror" id="alumno-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][alumno]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-alumno-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-3 mb-2 text-center" id="div-articulo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control articulo-${nro_nueva_fila} articulo @error('detalles.${nro_nueva_fila}.articulo') is-invalid @enderror" id="articulo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][articulo]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-articulo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-centro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control centro_costo-${nro_nueva_fila} centro_costo @error('detalles.${nro_nueva_fila}.centro_costo') is-invalid @enderror" id="centro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][centro_costo]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-centro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subcentro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control subcentro_costo-${nro_nueva_fila} subcentro_costo @error('detalles.${nro_nueva_fila}.subcentro_costo') is-invalid @enderror" id="subcentro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][subcentro_costo]" data-live-search="true" data-id="${nro_nueva_fila}" disabled>
                                            <option value="" selected disabled>Seleccionar...</option>
                                        </select>
                                        <span class="invalid-feedback error-subcentro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-precio-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center precio-${nro_nueva_fila} precio @error('detalles.${nro_nueva_fila}.precio') is-invalid @enderror" id="detalles[${nro_nueva_fila}][precio]" name="detalles[${nro_nueva_fila}][precio]" value="{{old('detalles.${nro_nueva_fila}.precio', 0)}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-precio-${nro_nueva_fila}" role="alert">

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

            articulo_accion_add = $('#btn-add-' + fila).detach();

            $('#articulo-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            $('.alumno-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(alumnos, function (i, val) {
                $('.alumno-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + value.numero_documento + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
            })
            $('.alumno-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.articulo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(articulos, function (i, val) {
                $('.articulo-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.articulo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.centro_costo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(centros_costos, function (i, val) {
                $('.centro_costo-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.centro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            $('.subcentro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            articulo_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.precio-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.articulo', function () {
        var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var valor = parseInt($(this).val());

        $.each(articulos, function (index, value) {
            if (value.id == valor) {
                $('.precio-' + fila).val(Intl.NumberFormat('de-DE').format(parseInt(value.detalle.precio_contado)));
            }
        })

        calcularTotal();
    })

    $(document).on('keyup', '.precio', function () {
        var fila = $(this).data('id');

        calcularTotal();
    })

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

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotal();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-alumno').text() == '') {
            $('<label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label>').insertBefore('.alumno-' + primera_fila);
        }
        if ($('.label-articulo').text() == '') {
            $('<label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>').insertBefore('.articulo-' + primera_fila);
        }
        if ($('.label-precio').text() == '') {
            $('<label class="form-label label-precio">Precio <span class="text-danger">(*)</span></label>').insertBefore('.precio-' + primera_fila);
        }
        if ($('.label-subtotal').text() == '') {
            $('<label class="form-label label-subtotal">Subtotal <span class="text-danger">(*)</span></label>').insertBefore('.subtotal-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotal() {
        var total = 0;
        $('.precio').each(function () {
            if ($(this).attr('type') != 'hidden') {
                var valor = $(this).val();
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

    $(document).ready(function () {
        new Cleave ('.precio-0', formatoSeparadorMiles);
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.alumno-' + index).val(arrayFilas[index].alumno);
                    $('.alumno-' + index).selectpicker('val', arrayFilas[index].alumno);
                    $('.articulo-' + index).val(arrayFilas[index].articulo);
                    $('.articulo-' + index).selectpicker('val', arrayFilas[index].articulo);
                    $('.precio-' + index).val(arrayFilas[index].precio);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;
            })

            var forma_pago = @json(old('forma_pago'));
            if (forma_pago == 'CR') {
                $('#div-credito_a').removeClass('d-none');
                $('#metodo_pago').selectpicker('val', '');
            } else {
                $('#credito_a').selectpicker('val', '');
                $('#div-metodo_pago').removeClass('d-none');
            }

            var metodo_pago = @json(old('metodo_pago'));
            if (metodo_pago == 2) {
                $('#row-debito').removeClass('d-none');
            } else if (metodo_pago == 3) {
                $('#row-credito').removeClass('d-none');
            } else if (metodo_pago == 4) {
                $('#row-transferencia').removeClass('d-none');
            } else if (metodo_pago == 5) {
                $('#row-deposito').removeClass('d-none');
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
</script>
