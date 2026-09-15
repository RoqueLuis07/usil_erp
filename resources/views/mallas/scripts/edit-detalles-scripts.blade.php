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

    $(document).on('click', '.btn-check', function () {
        var fila = $(this).data('id');
        $('.doble_grado-' + fila).val($(this).val());
    })

    $(document).on('change', '.carga_horaria', function () {
        calcularTotalesCargasCreditos();
    })

    $(document).on('change', '.cantidad_creditos', function () {
        calcularTotalesCargasCreditos();
    })

    // var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
    var materia_accion_add;
    // var materiasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        var doble_grado = $('.doble_grado-' + nro_ultima_fila).val();
        if ($('.materia-' + nro_ultima_fila + ' option:selected').val() != '' && $('.semestre-' + nro_ultima_fila).val() != '' && $('.carga_horaria-' + nro_ultima_fila).val() != '' && $('.cantidad_creditos-' + nro_ultima_fila).val() != '' && $('#area_curricular-' + nro_ultima_fila).val() && doble_grado != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-materia-${nro_nueva_fila}">
                                        <select class="selectpicker form-control materia-${nro_nueva_fila} materia @error('detalles.${nro_nueva_fila}.materia') is-invalid @enderror" id="materia-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-materia-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-semestre-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center semestre-${nro_nueva_fila} semestre @error('detalles.${nro_nueva_fila}.semestre') is-invalid @enderror" id="detalles[${nro_nueva_fila}][semestre]" name="detalles[${nro_nueva_fila}][semestre]" value="{{old('detalles.${nro_nueva_fila}.semestre')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-semestre-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-carga_horaria-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center carga_horaria-${nro_nueva_fila} carga_horaria @error('detalles.${nro_nueva_fila}.carga_horaria') is-invalid @enderror" id="detalles[${nro_nueva_fila}][carga_horaria]" name="detalles[${nro_nueva_fila}][carga_horaria]" value="{{old('detalles.${nro_nueva_fila}.carga_horaria')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-carga_horaria-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-12 mb-2 me-3 text-center" id="div-cantidad_creditos-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center cantidad_creditos-${nro_nueva_fila} cantidad_creditos @error('detalles.${nro_nueva_fila}.cantidad_creditos') is-invalid @enderror" id="detalles[${nro_nueva_fila}][cantidad_creditos]" name="detalles[${nro_nueva_fila}][cantidad_creditos]" value="{{old('detalles.${nro_nueva_fila}.cantidad_creditos')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-cantidad_creditos-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 text-center" id="div-area_curricular-${nro_nueva_fila}">
                                        <select class="selectpicker form-control area_curricular-${nro_nueva_fila} area_curricular @error('detalles.${nro_nueva_fila}.area_curricular') is-invalid @enderror" id="area_curricular-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][area_curricular]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="B">BÁSICO</option>
                                            <option value="C">COMPLEMENTARIO</option>
                                            <option value="P">PROFESIONAL</option>
                                        </select>
                                        <span class="invalid-feedback error-area_curricular-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-doble_grado-${nro_nueva_fila}">
                                        <div class="div-label-doble_grado-${nro_nueva_fila}">

                                        </div>
                                        <div class="btn-group @error('detalles.${nro_nueva_fila}.doble_grado') is-invalid @enderror" role="group">
                                            <input type="radio" class="btn-check doble_grado1-${nro_nueva_fila} doble_grado1" id="detalles[${nro_nueva_fila}][doble_grado1]" name="detalles[${nro_nueva_fila}][doble_grado]" value="false" @if (old('detalles[${nro_nueva_fila}][doble_grado]') == 'false') checked @endif data-id="${nro_nueva_fila}">
                                            <label class="btn btn-outline-danger" for="detalles[${nro_nueva_fila}][doble_grado1]">No</label>
                                            <input type="radio" class="btn-check doble_grado2-${nro_nueva_fila} doble_grado2" id="detalles[${nro_nueva_fila}][doble_grado2]" name="detalles[${nro_nueva_fila}][doble_grado]" value="true" @if (old('detalles[${nro_nueva_fila}][doble_grado]') == 'true') checked @endif data-id="${nro_nueva_fila}">
                                            <label class="btn btn-outline-success" for="detalles[${nro_nueva_fila}][doble_grado2]">Sí</label>
                                        </div>
                                        <input type="hidden" class="doble_grado-${nro_nueva_fila}" value="{{old('detalles.${nro_nueva_fila}.doble_grado')}}">
                                        <span class="invalid-feedback error-doble_grado-${nro_nueva_fila}" role="alert">

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

            materia_accion_add = $('#btn-add-' + fila).detach();

            $('#materia-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            // materiasSelected.forEach(item => {
            //     for (let index in materias) {
            //         if (materias[index].id == item) {
            //             materias.splice(index, 1);
            //         }
            //     }
            // });

            $('.materia-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(materias, function (i, val) {
                $('.materia-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            })
            $('.materia-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            $('.area_curricular-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            materia_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.semestre-' + nro_nueva_fila, formatoSeparadorMiles);
            new Cleave ('.carga_horaria-' + nro_nueva_fila, formatoSeparadorMiles);
            new Cleave ('.cantidad_creditos-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.materia', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        // var valor = $('.materia-' + fila + ' option:selected').val();

        // materiasSelected = [];
        // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
        //     var valor = $('.materia-' + index + ' option:selected').val();
        //     if ((parseInt(valor) || 0) != 0) {
        //         materiasSelected.push(parseInt(valor));
        //     }
        // }
        // materiasSelected.sort();

        // materiasSelected.forEach(item => {
        //     for (let index in materias) {
        //         if (materias[index].id == item) {
        //             materias.splice(index, 1);
        //         }
        //     }
        // });

        // for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
        //     var valor = $('.materia-' + index + ' option:selected').val();
        //     if ((parseInt(valor) || 0) != 0) {
        //         var texto = $('.materia-' + index + ' option:selected').text();
        //         $('.materia-' + index).selectpicker('destroy');
        //         $('.materia-' + index + ' option').each(function () {
        //             $(this).remove();
        //         });

        //         $('.materia-' + index).append('<option value="" disabled>Seleccionar...</option>');
        //         $('.materia-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
        //         $.each(materias, function (i, val) {
        //             $('.materia-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
        //         })
        //         $('.materia-' + index).addClass('selectpicker').selectpicker('render');
        //     } else {
        //         $('.materia-' + index).selectpicker('destroy');
        //         $('.materia-' + index + ' option').each(function () {
        //             $(this).remove();
        //         });

        //         $('.materia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
        //         $.each(materias, function (i, val) {
        //             $('.materia-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
        //         })
        //         $('.materia-' + index).addClass('selectpicker').selectpicker('render');
        //     }
        // }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotalMaterias();
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.materia-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }

        // var indice_eliminar = materiasSelected.indexOf(parseInt(valor));
        // if (materiasSelected.length != 0) {
        //     materiasSelected.splice(indice_eliminar, 1);
        // }
        // materiasSelected.sort();

        // if (materiasSelected.length != 0) {
        //     materiasSelected.forEach(item => {
        //         for (let index in materias) {
        //             if (materias[index].id == item) {
        //                 materias.splice(index, 1);
        //             }
        //         }
        //     });
        // }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            // var valor_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').val();
            // if ((parseInt(valor_siguiente) || 0) != 0) {
            //     var texto_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').text();
            //     $('.materia-' + nro_ultima_fila).selectpicker('destroy');
            //     $('.materia-' + nro_ultima_fila + ' option').each(function () {
            //         $(this).remove();
            //     });

            //     $('.materia-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
            //     $('.materia-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
            //     $.each(materias, function (i, val) {
            //         if (parseInt(valor_siguiente) != val.id) {
            //             $('.materia-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            //         }
            //     })
            //     $('.materia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            // } else {
            //     $('.materia-' + nro_ultima_fila).selectpicker('destroy');
            //     $('.materia-' + nro_ultima_fila + ' option').each(function () {
            //         $(this).remove();
            //     });

            //     $('.materia-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            //         $.each(materias, function (i, val) {
            //         if (parseInt(valor_siguiente) != val.id) {
            //             $('.materia-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            //         }
            //     })
            //     $('.materia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            // }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            // var primera_fila = $('.fila:first').prop('id');
            // var nro_primera_fila = primera_fila.split('-')[1];
            // var ultima_fila = $('.fila:last').prop('id');
            // var nro_ultima_fila = ultima_fila.split('-')[1];

            // for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            //     var valor = $('.materia-' + index + ' option:selected').val();
            //     if ((parseInt(valor) || 0) != 0) {
            //         var texto = $('.materia-' + index + ' option:selected').text();
            //         $('.materia-' + index).selectpicker('destroy');
            //         $('.materia-' + index + ' option').each(function () {
            //             $(this).remove();
            //         });

            //         $('.materia-' + index).append('<option value="" disabled>Seleccionar...</option>');
            //         $('.materia-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
            //         $.each(materias, function (i, val) {
            //             if (parseInt(valor_siguiente) != val.id) {
            //                 $('.materia-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            //             }
            //         })
            //         $('.materia-' + index).addClass('selectpicker').selectpicker('render');
            //     } else {
            //         $('.materia-' + index).selectpicker('destroy');
            //         $('.materia-' + index + ' option').each(function () {
            //             $(this).remove();
            //         });

            //         $('.materia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
            //         $.each(materias, function (i, val) {
            //             if (parseInt(valor_siguiente) != val.id) {
            //                 $('.materia-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            //             }
            //         })
            //         $('.materia-' + index).addClass('selectpicker').selectpicker('render');
            //     }
            // }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotalMaterias();
        calcularTotalesCargasCreditos();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-materia').text() == '') {
            $('<label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-semestre').text() == '') {
            $('<label class="form-label label-semestre">Semestre <span class="text-danger">(*)</span></label>').insertBefore('.semestre-' + primera_fila);
        }
        if ($('.label-carga_horaria').text() == '') {
            $('<label class="form-label label-carga_horaria">Carga Horaria <span class="text-danger">(*)</span></label>').insertBefore('.carga_horaria-' + primera_fila);
        }
        if ($('.label-cantidad_creditos').text() == '') {
            $('<label class="form-label label-cantidad_creditos">Cant. de Créditos <span class="text-danger">(*)</span></label>').insertBefore('.cantidad_creditos-' + primera_fila);
        }
        if ($('.label-doble_grado').text() == '') {
            $('.div-label-doble_grado-' + primera_fila).html('<label class="form-label label-doble_grado">Doble Grado <span class="text-danger">(*)</span></label>')
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotalMaterias() {
        var total_materias = $('.fila').length;
        $('#cantidad_materias').val(Intl.NumberFormat('de-DE').format(total_materias));
    }

    function calcularTotalesCargasCreditos() {
        var cantidad_filas = $('.fila').length;
        var total_carga_horaria = 0;
        var total_cantidad_creditos = 0;
        $('.carga_horaria:hidden').each(function () {
            var carga = $(this).val();
            if (carga == '') {
                carga = 0;
            }
            total_carga_horaria = parseInt(total_carga_horaria) + parseInt(carga);
        })
        $('.cantidad_creditos:hidden').each(function () {
            var credito = $(this).val();
            if (credito == '') {
                credito = 0;
            }
            total_cantidad_creditos = parseInt(total_cantidad_creditos) + parseInt(credito);
        })
        $('#carga_horaria_total').val(Intl.NumberFormat('de-DE').format(total_carga_horaria));
        $('#cantidad_creditos_total').val(Intl.NumberFormat('de-DE').format(total_cantidad_creditos));
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
        new Cleave ('.semestre-0', formatoSeparadorMiles);
        new Cleave ('.carga_horaria-0', formatoSeparadorMiles);
        new Cleave ('.cantidad_creditos-0', formatoSeparadorMiles);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        // materiasSelected = [];
        // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
        //     var valor = $('.materia-' + index + ' option:selected').val();
        //     if ((parseInt(valor) || 0) != 0) {
        //         materiasSelected.push(parseInt(valor));
        //     }
        // }
        // materiasSelected.sort();

        // materiasSelected.forEach(item => {
        //     for (let index in materias) {
        //         if (materias[index].id == item) {
        //             materias.splice(index, 1);
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
                    $('.materia-' + index).val(arrayFilas[index].materia);
                    $('.materia-' + index).selectpicker('val', arrayFilas[index].materia);
                    $('.semestre-' + index).val(arrayFilas[index].semestre);
                    $('.carga_horaria-' + index).val(arrayFilas[index].carga_horaria);
                    $('.cantidad_creditos-' + index).val(arrayFilas[index].cantidad_creditos);
                    if (arrayFilas[index].doble_grado == false) {
                        $('.doble_grado1-' + index).prop('checked', true);
                        $('.doble_grado-' + index).val('false');
                    } else if (arrayFilas[index].doble_grado == true) {
                        $('.doble_grado2-' + index).prop('checked', true);
                        $('.doble_grado-' + index).val('true');
                    }
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                // materiasSelected = [];
                // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                //     var valor = $('.materia-' + index + ' option:selected').val();
                //     if ((parseInt(valor) || 0) != 0) {
                //         materiasSelected.push(parseInt(valor));
                //     }
                // }
                // materiasSelected.sort();

                // materiasSelected.forEach(item => {
                //     for (let index in materias) {
                //         if (materias[index].id == item) {
                //             materias.splice(index, 1);
                //         }
                //     }
                // });

                // $('.materia').trigger('change');

                calcularTotalMaterias();
                calcularTotalesCargasCreditos();
            })
        }

        //obtener los errors de materia
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
