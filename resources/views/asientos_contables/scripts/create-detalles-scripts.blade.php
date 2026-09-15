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

    $(document).on('change', '.debe', function () {
        calcularDiferencia();
    })

    $(document).on('change', '.haber', function () {
        calcularDiferencia();
    })

    var cuentas_contables = {!!json_encode($cuentas_contables, JSON_HEX_TAG) !!}
    var asiento_accion_add;
    var cuentasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var cuentas_contables = {!!json_encode($cuentas_contables, JSON_HEX_TAG) !!}
        var centros_costos = {!!json_encode($centros_costos, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.cuenta_contable-' + nro_ultima_fila + ' option:selected').val() != '' && $('.descripcion-' + nro_ultima_fila).val() != '' && $('.centro_costo-' + nro_ultima_fila + ' option:selected').val() != '' && $('.subcentro_costo-' + nro_ultima_fila + ' option:selected').val() != '' && ($('.debe-' + nro_ultima_fila).val() != '' || $('.haber-' + nro_ultima_fila).val() != '')) {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-1 mb-2 text-center" id="div-numero-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center " id="numero-${nro_nueva_fila}" value="${nro_nueva_fila + 1}" readonly>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-cuenta_contable-${nro_nueva_fila}">
                                        <select class="selectpicker form-control cuenta_contable-${nro_nueva_fila} cuenta_contable @error('detalles.${nro_nueva_fila}.cuenta_contable') is-invalid @enderror" id="cuenta_contable-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][cuenta_contable]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-cuenta_contable-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-descripcion-${nro_nueva_fila}">
                                        <textarea class="form-control text-center descripcion" id="descripcion-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][descripcion]" cols="30" rows="3" data-id="${nro_nueva_fila}"></textarea>
                                        <span class="invalid-feedback error-descripcion-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-centro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control centro_costo-${nro_nueva_fila} centro_costo" id="centro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][centro_costo]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-centro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subcentro_costo-${nro_nueva_fila}">
                                        <select class="selectpicker form-control subcentro_costo-${nro_nueva_fila} subcentro_costo" id="subcentro_costo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][subcentro_costo]" data-live-search="true" data-id="${nro_nueva_fila}" disabled>
                                            <option value="" selected disabled>Seleccionar...</option>
                                        </select>
                                        <span class="invalid-feedback error-subcentro_costo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-debe-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center debe-${nro_nueva_fila} debe" id="debe-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][debe]" placeholder="0" data-id="${nro_nueva_fila}" placeholder="0">
                                        <span class="invalid-feedback error-debe-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-haber-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center haber-${nro_nueva_fila} haber" id="haber-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][haber]" placeholder="0" data-id="${nro_nueva_fila}" placeholder="0">
                                        <span class="invalid-feedback error-haber-${nro_nueva_fila}" role="alert">

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

            asiento_accion_add = $('#btn-add-' + fila).detach();

            $('#asiento-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            cuentasSelected.forEach(item => {
                for (let index in cuentas_contables) {
                    if (cuentas_contables[index].id == item) {
                        cuentas_contables.splice(index, 1);
                    }
                }
            });

            $('.cuenta_contable-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_contables, function (i, val) {
                $('.cuenta_contable-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
            })
            $('.cuenta_contable-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.centro_costo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(centros_costos, function (i, val) {
                $('.centro_costo-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.centro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            $('.subcentro_costo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            

            asiento_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.debe-' + nro_nueva_fila, formatoSeparadorMiles);
            new Cleave ('.haber-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $('#unidad_negocio').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('asientos_contables.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    $(document).on('change', '.centro_costo', function () {

        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (!errors) {
            var id = $(this).val();
            var fila = $(this).data('id');
            var url = "{{ route('asientos_contables.get_subcentros_costos', ":id") }}";
            url = url.replace(':id', id);

            get_subcentros_costos(url, fila);
        }
    });

    $(document).on('change', '.cuenta_contable', function () {
        var cuentas_contables = {!!json_encode($cuentas_contables, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.cuenta_contable-' + fila + ' option:selected').val();

        cuentasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.cuenta_contable-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                cuentasSelected.push(parseInt(valor));
            }
        }
        cuentasSelected.sort();

        cuentasSelected.forEach(item => {
            for (let index in cuentas_contables) {
                if (cuentas_contables[index].id == item) {
                    cuentas_contables.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.cuenta_contable-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.cuenta_contable-' + index + ' option:selected').text();
                $('.cuenta_contable-' + index).selectpicker('destroy');
                $('.cuenta_contable-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.cuenta_contable-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.cuenta_contable-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(cuentas_contables, function (i, val) {
                    $('.cuenta_contable-' + index).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                })
                $('.cuenta_contable-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.cuenta_contable-' + index).selectpicker('destroy');
                $('.cuenta_contable-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.cuenta_contable-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(cuentas_contables, function (i, val) {
                    $('.cuenta_contable-' + index).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                })
                $('.cuenta_contable-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var cuentas_contables = {!!json_encode($cuentas_contables, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.cuenta_contable-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        asiento_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = cuentasSelected.indexOf(parseInt(valor));
        if (cuentasSelected.length != 0) {
            cuentasSelected.splice(indice_eliminar, 1);
        }
        cuentasSelected.sort();

        if (cuentasSelected.length != 0) {
            cuentasSelected.forEach(item => {
                for (let index in cuentas_contables) {
                    if (cuentas_contables[index].id == item) {
                        cuentas_contables.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.cuenta_contable-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.cuenta_contable-' + nro_ultima_fila + ' option:selected').text();
                $('.cuenta_contable-' + nro_ultima_fila).selectpicker('destroy');
                $('.cuenta_contable-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.cuenta_contable-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.cuenta_contable-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(cuentas_contables, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.cuenta_contable-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                    }
                })
                $('.cuenta_contable-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.cuenta_contable-' + nro_ultima_fila).selectpicker('destroy');
                $('.cuenta_contable-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.cuenta_contable-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_contables, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.cuenta_contable-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                    }
                })
                $('.cuenta_contable-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.cuenta_contable-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.cuenta_contable-' + index + ' option:selected').text();
                    $('.cuenta_contable-' + index).selectpicker('destroy');
                    $('.cuenta_contable-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.cuenta_contable-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.cuenta_contable-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(cuentas_contables, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.cuenta_contable-' + index).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.cuenta_contable-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.cuenta_contable-' + index).selectpicker('destroy');
                    $('.cuenta_contable-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.cuenta_contable-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_contables, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.cuenta_contable-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.cuenta + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.cuenta_contable-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-numero').text() == '') {
            $('<label class="form-label label-numero">N°</label>').insertBefore('#numero-' + primera_fila);
        }
        if ($('.label-cuenta_contable').text() == '') {
            $('<label class="form-label label-cuenta_contable">Cuenta Contable</label>').insertBefore('#cuenta_contable-' + primera_fila);
        }
        if ($('.label-descripcion').text() == '') {
            $('<label class="form-label label-descripcion">Descripción</label>').insertBefore('.descripcion-' + primera_fila);
        }
        if ($('.label-centro_costo').text() == '') {
            $('<label class="form-label label-centro_costo">CC1</label>').insertBefore('.centro_costo-' + primera_fila);
        }
        if ($('.label-subcentro_costo').text() == '') {
            $('<label class="form-label label-subcentro_costo">CC2</label>').insertBefore('.subcentro_costo-' + primera_fila);
        }
        if ($('.label-debe').text() == '') {
            $('<label class="form-label label-debe">Debe</label>').insertBefore('.debe-' + primera_fila);
        }
        if ($('.label-haber').text() == '') {
            $('<label class="form-label label-haber">Haber</label>').insertBefore('.haber-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    const today = new Date();
    const mes = today.getMonth() + 1;
    const año = today.getFullYear();

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        defaultDate: today,
        minDate: `${año}-${mes.toString().padStart(2, '0')}-01`,
        maxDate: today,
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
        var calendario = flatpickr('.flatpickr', flatpickrOptions);

        $('.debe').each(function () {
            new Cleave ($(this), formatoSeparadorMiles);
        })

        $('.haber').each(function () {
            new Cleave ($(this), formatoSeparadorMiles);
        })
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            var id = $('#unidad_negocio').val();
            var url = "{{ route('articulos.get_subunidades_negocios', ":id") }}";
            url = url.replace(':id', id);
            get_subunidades_negocios(url);

            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    setTimeout(() => {
                        $('.btn-add').trigger('click');
                    }, 2000);
                }

                setTimeout(() => {
                    $('.cuenta_contable-' + index).val(arrayFilas[index].cuenta_contable);
                    $('.cuenta_contable-' + index).selectpicker('val', arrayFilas[index].cuenta_contable);
                    $('.descripcion-' + index).val(arrayFilas[index].descripcion);
                    $('.centro_costo-' + index).val(arrayFilas[index].centro_costo);
                    $('.centro_costo-' + index).selectpicker('val', arrayFilas[index].centro_costo);
                    var id = $('.centro_costo-' + index + ' option:selected').val();
                    var url = "{{ route('asientos_contables.get_subcentros_costos', ":id") }}";
                    url = url.replace(':id', id);
                    get_subcentros_costos(url, index);
                    setTimeout(() => {
                        $('.subcentro_costo-' + index).val(arrayFilas[index].subcentro_costo);
                        $('.subcentro_costo-' + index).selectpicker('val', arrayFilas[index].subcentro_costo);
                    }, 1000);
                    $('.debe-' + index).val(arrayFilas[index].debe);
                    $('.haber-' + index).val(arrayFilas[index].haber);
                }, 1000);

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                cuentasSelected = [];
                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.cuenta_contable-' + index + ' option:selected').val();
                    if ((parseInt(valor) || 0) != 0) {
                        cuentasSelected.push(parseInt(valor));
                    }
                }
                cuentasSelected.sort();

                cuentasSelected.forEach(item => {
                    for (let index in cuentas_contables) {
                        if (cuentas_contables[index].id == item) {
                            cuentas_contables.splice(index, 1);
                        }
                    }
                });

                $('.cuenta_contable').trigger('change');
                $('.centro_costo').trigger('change');
                $('.subcentro_costo').trigger('change');

                calcularDiferencia();
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

    function calcularDiferencia() {
        var diferencia = 0;
        var total_debe = 0;
        var total_haber = 0;
        $('.debe:not([type="hidden"])').each(function () {
            total_debe += parseInt($(this).val().replace(/\./g, '') || 0);
        });

        $('.haber:not([type="hidden"])').each(function () {
            total_haber += parseInt($(this).val().replace(/\./g, '') || 0);
        });

        diferencia = total_debe - total_haber;
        $('#diferencia_send').val(diferencia);

        $('#diferencia').val(Intl.NumberFormat('de-DE').format(diferencia));
    }

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
