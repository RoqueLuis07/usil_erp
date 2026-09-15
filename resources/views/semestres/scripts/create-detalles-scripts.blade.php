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
            longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            },
        },
    }

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

    var malla_accion_add;
    var mallasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var mallas = {!!json_encode($mallas, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.malla-' + nro_ultima_fila + ' option:selected').val() != '' && $('.coordinador-' + nro_ultima_fila + ' option:selected').val() != '' && $('.fecha_inicio_matriculacion-' + nro_ultima_fila).val() != '' && $('.fecha_fin_matriculacion-' + nro_ultima_fila).val()) {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12 mb-2 text-center" id="div-malla-${nro_nueva_fila}">
                                        <label class="form-label label-malla">Carrera <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control malla-${nro_nueva_fila} malla @error('detalles.${nro_nueva_fila}.malla') is-invalid @enderror" id="malla-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][malla]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-malla-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-coordinador-${nro_nueva_fila}">
                                        <label class="form-label label-coordinador">Coordinador <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control coordinador-${nro_nueva_fila} coordinador @error('detalles.${nro_nueva_fila}.coordinador') is-invalid @enderror" id="coordinador-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][coordinador]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($coordinadores as $coordinador)
                                                <option value="{{$coordinador->id}}" @if (old('detalles.${nro_nueva_fila}.coordinador') == strval($coordinador->id)) selected @endif>{{$coordinador->primer_nombre}} {{$coordinador->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback error-coordinador-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-fecha_inicio_matriculacion-${nro_nueva_fila}">
                                        <label class="form-label label-fecha_inicio_matriculacion">Inicio Matriculación <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon fecha_inicio_matriculacion-${nro_nueva_fila} fecha_inicio_matriculacion @error('detalles.${nro_nueva_fila}.fecha_inicio_matriculacion') is-invalid @enderror" id="detalles[${nro_nueva_fila}][fecha_inicio_matriculacion]" name="detalles[${nro_nueva_fila}][fecha_inicio_matriculacion]" value="{{old('detalles.${nro_nueva_fila}.fecha_inicio_matriculacion')}}" placeholder="Seleccionar...">
                                            <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                            <span class="invalid-feedback error-fecha_inicio_matriculacion-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-fecha_fin_matriculacion-${nro_nueva_fila}">
                                        <label class="form-label label-fecha_fin_matriculacion">Fin Matriculación <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon fecha_fin_matriculacion-${nro_nueva_fila} fecha_fin_matriculacion @error('detalles.${nro_nueva_fila}.fecha_fin_matriculacion') is-invalid @enderror" id="detalles[${nro_nueva_fila}][fecha_fin_matriculacion]" name="detalles[${nro_nueva_fila}][fecha_fin_matriculacion]" value="{{old('detalles.${nro_nueva_fila}.fecha_fin_matriculacion')}}" placeholder="Seleccionar...">
                                            <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                            <span class="invalid-feedback error-fecha_fin_matriculacion-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-2 text-center">
                                        <label class="form-label label-acciones">Acciones</label>
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            malla_accion_add = $('#btn-add-' + fila).detach();

            $('#malla-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            mallasSelected.forEach(item => {
                for (let index in mallas) {
                    if (mallas[index].id == item) {
                        mallas.splice(index, 1);
                    }
                }
            });

            $('.malla-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(mallas, function (i, val) {
                $('.malla-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.carrera.programa.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
            })
            $('.malla-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            $('.coordinador-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');
            flatpickr('.fecha_inicio_matriculacion-' + nro_nueva_fila, flatpickrOptions);
            flatpickr('.fecha_fin_matriculacion-' + nro_nueva_fila, flatpickrOptions);

            malla_accion_add = $('#btn-add-' + nro_nueva_fila);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.malla', function () {
        var mallas = {!!json_encode($mallas, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.malla-' + fila + ' option:selected').val();

        mallasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.malla-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                mallasSelected.push(parseInt(valor));
            }
        }
        mallasSelected.sort();

        mallasSelected.forEach(item => {
            for (let index in mallas) {
                if (mallas[index].id == item) {
                    mallas.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.malla-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.malla-' + index + ' option:selected').text();
                $('.malla-' + index).selectpicker('destroy');
                $('.malla-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.malla-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.malla-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(mallas, function (i, val) {
                    $('.malla-' + index).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                })
                $('.malla-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.malla-' + index).selectpicker('destroy');
                $('.malla-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.malla-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(mallas, function (i, val) {
                    $('.malla-' + index).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                })
                $('.malla-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotalCarreras();
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var mallas = {!!json_encode($mallas, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.malla-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        malla_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = mallasSelected.indexOf(parseInt(valor));
        if (mallasSelected.length != 0) {
            mallasSelected.splice(indice_eliminar, 1);
        }
        mallasSelected.sort();

        if (mallasSelected.length != 0) {
            mallasSelected.forEach(item => {
                for (let index in mallas) {
                    if (mallas[index].id == item) {
                        mallas.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.malla-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.malla-' + nro_ultima_fila + ' option:selected').text();
                $('.malla-' + nro_ultima_fila).selectpicker('destroy');
                $('.malla-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.malla-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.malla-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(mallas, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.malla-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                    }
                })
                $('.malla-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.malla-' + nro_ultima_fila).selectpicker('destroy');
                $('.malla-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.malla-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(mallas, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.malla-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                    }
                })
                $('.malla-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.malla-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.malla-' + index + ' option:selected').text();
                    $('.malla-' + index).selectpicker('destroy');
                    $('.malla-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.malla-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.malla-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(mallas, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.malla-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                        }
                    })
                    $('.malla-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.malla-' + index).selectpicker('destroy');
                    $('.malla-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.malla-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(mallas, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.malla-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.carrera.nombre_real + ' - ' + val.tipo_malla.nombre + '">' + val.carrera.nombre_fantasia + '</option>');
                        }
                    })
                    $('.malla-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);

        calcularTotalCarreras();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-malla').text() == '') {
            $('<label class="form-label label-malla">Materia <span class="text-danger">(*)</span></label>').insertBefore('#malla-' + primera_fila);
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

    function calcularTotalCarreras() {
        var total_mallas = $('.fila').length;
        $('#cantidad_mallas').val(Intl.NumberFormat('de-DE').format(total_mallas));
    }

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
                    $('.malla-' + index).val(arrayFilas[index].malla);
                    $('.malla-' + index).selectpicker('val', arrayFilas[index].malla);
                    $('.coordinador-' + index).val(arrayFilas[index].coordinador);
                    $('.fecha_inicio_matriculacion-' + index).val(arrayFilas[index].fecha_inicio_matriculacion);
                    $('.fecha_fin_matriculacion-' + index).val(arrayFilas[index].fecha_fin_matriculacion);

                    flatpickr('.fecha_inicio_matriculacion-' + index, flatpickrOptions);
                    flatpickr('.fecha_fin_matriculacion-' + index, flatpickrOptions);
                }
                calcularTotalCarreras();
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
