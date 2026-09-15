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

    $(document).on('change', '.hora_inicio', function () {
        var fila = $(this).data('id');
        if ($('.hora_fin-' + fila).val() != '' && $('#cantidad_clases').val() != '') {
            calcularHoras();
        }
    })

    $(document).on('change', '.hora_fin', function () {
        var fila = $(this).data('id');
        if ($('.hora_inicio-' + fila).val() != '' && $('#cantidad_clases').val() != '') {
            calcularHoras();
        }
    })

    $(document).on('keyup', '#cantidad_clases', function () {
        calcularHoras();
    })

    var dias = {!!json_encode($dias, JSON_HEX_TAG) !!}
    var dia_accion_add;
    var diasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var dias = {!!json_encode($dias, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.dia-' + nro_ultima_fila + ' option:selected').val() != '' && $('.hora_inicio-' + nro_ultima_fila).val() != '' && $('.hora_fin-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-dia-${nro_nueva_fila}">
                                        <select class="selectpicker form-control dia-${nro_nueva_fila} dia @error('detalles.${nro_nueva_fila}.dia') is-invalid @enderror" id="dia-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][dia]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-dia-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-hora_inicio-${nro_nueva_fila}">
                                        <input type="text" class="timepickr form-control text-center hora_inicio-${nro_nueva_fila} hora_inicio @error('detalles.${nro_nueva_fila}.hora_inicio') is-invalid @enderror" id="detalles[${nro_nueva_fila}][hora_inicio]" name="detalles[${nro_nueva_fila}][hora_inicio]" value="{{old('detalles.${nro_nueva_fila}.hora_inicio')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-hora_inicio-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-hora_fin-${nro_nueva_fila}">
                                        <input type="text" class="timepickr form-control text-center hora_fin-${nro_nueva_fila} hora_fin @error('detalles.${nro_nueva_fila}.hora_fin') is-invalid @enderror" id="detalles[${nro_nueva_fila}][hora_fin]" name="detalles[${nro_nueva_fila}][hora_fin]" value="{{old('detalles.${nro_nueva_fila}.hora_fin')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-hora_fin-${nro_nueva_fila}" role="alert">

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

            dia_accion_add = $('#btn-add-' + fila).detach();

            $('#dia-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            diasSelected.forEach(item => {
                for (let index in dias) {
                    if (dias[index].id == item) {
                        dias.splice(index, 1);
                    }
                }
            });

            $('.dia-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(dias, function (i, val) {
                $('.dia-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.dia-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            dia_accion_add = $('#btn-add-' + nro_nueva_fila);

            flatpickr($('.timepickr'), timepickrOptions);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.dia', function () {
        var dias = {!!json_encode($dias, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.dia-' + fila + ' option:selected').val();

        diasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.dia-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                diasSelected.push(parseInt(valor));
            }
        }
        diasSelected.sort();

        diasSelected.forEach(item => {
            for (let index in dias) {
                if (dias[index].id == item) {
                    dias.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.dia-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.dia-' + index + ' option:selected').text();
                $('.dia-' + index).selectpicker('destroy');
                $('.dia-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.dia-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.dia-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(dias, function (i, val) {
                    $('.dia-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                })
                $('.dia-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.dia-' + index).selectpicker('destroy');
                $('.dia-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.dia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(dias, function (i, val) {
                    $('.dia-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                })
                $('.dia-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var dias = {!!json_encode($dias, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.dia-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        dia_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = diasSelected.indexOf(parseInt(valor));
        if (diasSelected.length != 0) {
            diasSelected.splice(indice_eliminar, 1);
        }
        diasSelected.sort();

        if (diasSelected.length != 0) {
            diasSelected.forEach(item => {
                for (let index in dias) {
                    if (dias[index].id == item) {
                        dias.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.dia-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.dia-' + nro_ultima_fila + ' option:selected').text();
                $('.dia-' + nro_ultima_fila).selectpicker('destroy');
                $('.dia-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.dia-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.dia-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(dias, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.dia-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                    }
                })
                $('.dia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.dia-' + nro_ultima_fila).selectpicker('destroy');
                $('.dia-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.dia-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(dias, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.dia-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                    }
                })
                $('.dia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.dia-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.dia-' + index + ' option:selected').text();
                    $('.dia-' + index).selectpicker('destroy');
                    $('.dia-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.dia-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.dia-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(dias, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.dia-' + index).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.dia-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.dia-' + index).selectpicker('destroy');
                    $('.dia-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.dia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(dias, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.dia-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.dia-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularHoras();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-dia').text() == '') {
            $('<label class="form-label label-dia">Día <span class="text-danger">(*)</span></label>').insertBefore('#dia-' + primera_fila);
        }
        if ($('.label-hora_inicio').text() == '') {
            $('<label class="form-label label-hora_inicio">Hora Inicio <span class="text-danger">(*)</span></label>').insertBefore('.hora_inicio-' + primera_fila);
        }
        if ($('.label-hora_fin').text() == '') {
            $('<label class="form-label label-hora_fin">Hora Fin <span class="text-danger">(*)</span></label>').insertBefore('.hora_fin-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    var total_horas = 0;
    function calcularHoras() {
        var cantidad_clases = parseInt($('#cantidad_clases').val());
        total_horas = 0;
        for (let index = 0; index < cantidad_clases; index++) {
            $('.hora_inicio').each(function () {
                var fila = $(this).data('id');
                var inicio = $(this).val();
                var fin = $('.hora_fin-' + fila).val();

                var partes1 = inicio.split(':');
                var partes2 = fin.split(':');

                var fecha1 = new Date(0, 0, 0, partes1[0], partes1[1]);
                var fecha2 = new Date(0, 0, 0, partes2[0], partes2[1]);

                var diferencia = fecha2 - fecha1;

                // Convertir la diferencia de milisegundos a horas y minutos
                var horas = Math.floor(diferencia / (1000 * 60 * 60));
                // var minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
                horas = parseInt(Math.abs(horas));

                total_horas = parseInt(total_horas) + parseInt(horas);
            })
        }

        if (!isNaN(total_horas)) {
            $('#cantidad_horas').val(total_horas);
        }
    }

    //Inicio formatos para flatPickr
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
            longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            },
        },
    }

    const timepickrOptions = {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
    };

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function () {
        flatpickr($('.flatpickr'), flatpickrOptions);
        flatpickr($('.timepickr'), timepickrOptions);

        new Cleave('#cantidad_clases', formatoSeparadorMiles);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;
        diasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.dia-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                diasSelected.push(parseInt(valor));
            }
        }
        diasSelected.sort();

        diasSelected.forEach(item => {
            for (let index in dias) {
                if (dias[index].id == item) {
                    dias.splice(index, 1);
                }
            }
        });
        dias.sort();

        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.dia-' + index).val(arrayFilas[index].dia);
                    $('.dia-' + index).selectpicker('val', arrayFilas[index].dia);
                    $('.hora_inicio-' + index).val(arrayFilas[index].hora_inicio);
                    $('.hora_fin-' + index).val(arrayFilas[index].hora_fin);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                diasSelected = [];
                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.dia-' + index + ' option:selected').val();
                    if ((parseInt(valor) || 0) != 0) {
                        diasSelected.push(parseInt(valor));
                    }
                }
                diasSelected.sort();

                diasSelected.forEach(item => {
                    for (let index in dias) {
                        if (dias[index].id == item) {
                            dias.splice(index, 1);
                        }
                    }
                });

                $('.dia').trigger('change');
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
</script>
