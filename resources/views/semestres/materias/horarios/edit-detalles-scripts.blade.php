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

    var dias = {!!json_encode($dias_semana, JSON_HEX_TAG) !!}
    var dia_accion_add;
    var diasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var dias = {!!json_encode($dias_semana, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.dia-' + nro_ultima_fila + ' option:selected').val() != '' && $('.hora_inicio-' + nro_ultima_fila).val() != '' && $('.hora_fin-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-dia-${nro_nueva_fila}">
                                            <label class="form-label label-dia">Día de la Semana <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control dia-${nro_nueva_fila} dia @error('detalles.${nro_nueva_fila}.dia') is-invalid @enderror" id="dia-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][dia]" data-live-search="false" data-id="${nro_nueva_fila}">

                                            </select>
                                            <span class="invalid-feedback error-dia-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-hora_inicio-${nro_nueva_fila}">
                                            <label class="form-label label-hora_inicio">Hora de Inicio <span class="text-danger">(*)</span></label>
                                            <input type="text" class="timepickr form-control text-center hora_inicio-${nro_nueva_fila} hora_inicio @error('detalles.${nro_nueva_fila}.hora_inicio') is-invalid @enderror" id="detalles[${nro_nueva_fila}][hora_inicio]" name="detalles[${nro_nueva_fila}][hora_inicio]" data-id="${nro_nueva_fila}">
                                            <span class="invalid-feedback error-hora_inicio-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-hora_fin-${nro_nueva_fila}">
                                            <label class="form-label label-hora_fin">Hora de Fin <span class="text-danger">(*)</span></label>
                                            <input type="text" class="timepickr form-control text-center hora_fin-${nro_nueva_fila} hora_fin @error('detalles.${nro_nueva_fila}.hora_fin') is-invalid @enderror" id="detalles[${nro_nueva_fila}][hora_fin]" name="detalles[${nro_nueva_fila}][hora_fin]" data-id="${nro_nueva_fila}">
                                            <span class="invalid-feedback error-hora_fin-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-1 col-sm-2 text-center">
                                            <label class="form-label label-acciones">Acciones</label>
                                            <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                            </div>
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
            dias.sort();


            $('.dia-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(dias, function (i, val) {
                $('.dia-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.dia-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            dia_accion_add = $('#btn-add-' + nro_nueva_fila);

            flatpickr($('.timepickr'), formatoTimepickr);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.dia', function () {
        var dias = {!!json_encode($dias_semana, JSON_HEX_TAG) !!}
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
        dias.sort();

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

        calcularTotalDias();
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var dias = {!!json_encode($dias_semana, JSON_HEX_TAG) !!}
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
        dias.sort();

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
                            $('.dia-' + nro_ultima_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
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

        calcularTotalDias();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-dia').text() == '') {
            $('<label class="form-label label-dia">Materia <span class="text-danger">(*)</span></label>').insertBefore('#dia-' + primera_fila);
        }
        if ($('.label-hora_inicio').text() == '') {
            $('<label class="form-label label-hora_inicio">Semestre <span class="text-danger">(*)</span></label>').insertBefore('.hora_inicio-' + primera_fila);
        }
        if ($('.label-hora_fin').text() == '') {
            $('<label class="form-label label-hora_fin">Carga Horaria <span class="text-danger">(*)</span></label>').insertBefore('.hora_fin-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotalDias() {
        var total_dias = $('.fila').length;
        $('#cantidad_dias').val(Intl.NumberFormat('de-DE').format(total_dias));
    }

    //Inicio formatos para timePickr
    const formatoTimepickr = {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
    };

    $(document).ready(function () {
        flatpickr($('.timepickr'), formatoTimepickr);

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

                    $('.dia').trigger('change');
                }
                calcularTotalDias();
            })
        }

        //obtener los errors de dias
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
