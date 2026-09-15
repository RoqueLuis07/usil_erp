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

    var puntaje_accion_add;
    $(document).on('click', '.btn-add', function () {
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.punto_minimo-' + nro_ultima_fila).val() != '' && $('.punto_maximo-' + nro_ultima_fila).val() != '' && $('.nota-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_minimo-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center punto_minimo-${nro_nueva_fila} punto_minimo @error('detalles.${nro_nueva_fila}.punto_minimo') is-invalid @enderror" id="detalles[${nro_nueva_fila}][punto_minimo]" name="detalles[${nro_nueva_fila}][punto_minimo]" value="{{old('detalles.${nro_nueva_fila}.punto_minimo')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-punto_minimo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-punto_maximo-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center punto_maximo-${nro_nueva_fila} punto_maximo @error('detalles.${nro_nueva_fila}.punto_maximo') is-invalid @enderror" id="detalles[${nro_nueva_fila}][punto_maximo]" name="detalles[${nro_nueva_fila}][punto_maximo]" value="{{old('detalles.${nro_nueva_fila}.punto_maximo')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-punto_maximo-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-12 mb-2 me-3 text-center" id="div-nota-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center nota-${nro_nueva_fila} nota @error('detalles.${nro_nueva_fila}.nota') is-invalid @enderror" id="detalles[${nro_nueva_fila}][nota]" name="detalles[${nro_nueva_fila}][nota]" value="{{old('detalles.${nro_nueva_fila}.nota')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-nota-${nro_nueva_fila}" role="alert">

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

            puntaje_accion_add = $('#btn-add-' + fila).detach();

            $('#puntaje-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            puntaje_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.punto_minimo-' + nro_nueva_fila, formatoSeparadorMiles);
            new Cleave ('.punto_maximo-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-punto_minimo').text() == '') {
            $('<label class="form-label label-punto_minimo">Puntaje Mínimo <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-punto_maximo').text() == '') {
            $('<label class="form-label label-punto_maximo">Puntaje Máximo <span class="text-danger">(*)</span></label>').insertBefore('.semestre-' + primera_fila);
        }
        if ($('.label-nota').text() == '') {
            $('<label class="form-label label-nota">Nota <span class="text-danger">(*)</span></label>').insertBefore('.carga_horaria-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
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
        new Cleave ('.punto_minimo-0', formatoSeparadorMiles);
        new Cleave ('.punto_maximo-0', formatoSeparadorMiles);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

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
                    $('.punto_minimo-' + index).val(arrayFilas[index].punto_minimo);
                    $('.punto_maximo-' + index).val(arrayFilas[index].punto_maximo);
                    $('.nota-' + index).val(arrayFilas[index].nota);
                }
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
