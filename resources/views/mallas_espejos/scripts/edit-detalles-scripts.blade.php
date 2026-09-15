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

    var materia_accion_add;
    $(document).on('click', '.btn-add', function () {
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);
        var materias_paraguay = {!! json_encode($materias_paraguay, JSON_HEX_TAG) !!}
        var materias_siu = {!! json_encode($materias_siu, JSON_HEX_TAG) !!}

        if ($('.materia_paraguay-' + nro_ultima_fila + ' option:selected').val() != '' && $('.materia_siu-' + nro_ultima_fila + ' option:selected').val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-6 col-lg-3 mb-2 text-center" id="div-materia_paraguay-${nro_nueva_fila}">
                                        <select class="selectpicker form-control materia_paraguay-${nro_nueva_fila} materia_paraguay @error('detalles.${nro_nueva_fila}.materia_paraguay') is-invalid @enderror" id="materia_paraguay-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia_paraguay]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-materia_paraguay-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_siu-${nro_nueva_fila}">
                                        <select class="selectpicker form-control materia_siu-${nro_nueva_fila} materia_siu @error('detalles.${nro_nueva_fila}.materia_siu') is-invalid @enderror" id="materia_siu-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia_siu]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-materia_siu-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-1 col-lg-1 text-center">
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

            materia_accion_add = $('#btn-add-' + nro_nueva_fila);

            cargar_materias_paraguay(nro_nueva_fila, materias_paraguay);
            cargar_materias_siu(nro_nueva_fila, materias_siu);
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
        materia_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);
        } else {
            $('#acciones-' + nro_primera_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);
        }

        primera_fila = $('.fila:first').prop('id');
        nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-materia_paraguay').text() == '') {
            $('<label class="form-label label-materia_paraguay">Materia Paraguay <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-materia_siu').text() == '') {
            $('<label class="form-label label-materia_siu">Materia SIU <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
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
                    $('.materia_paraguay-' + index).selectpicker('val', arrayFilas[index].materia_paraguay);
                    $('.materia_siu-' + index).selectpicker('val', arrayFilas[index].materia_siu);
                }
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

    function cargar_materias_paraguay(fila, materias) {
        $('#materia_paraguay-' + fila).selectpicker('destroy');
        $('#materia_paraguay-' + fila).empty();
        $('#materia_paraguay-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');

        $.each(materias, function (index, value) {
            $('#materia_paraguay-' + fila).append('<option value="' + value.materia.id + '">' + value.materia.nombre_fantasia + '</option>')
        })
        $('#materia_paraguay-' + fila).selectpicker('render');
    }

    function cargar_materias_siu(fila, materias) {
        $('#materia_siu-' + fila).selectpicker('destroy');
        $('#materia_siu-' + fila).empty();
        $('#materia_siu-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');

        $.each(materias, function (index, value) {
            $('#materia_siu-' + fila).append('<option value="' + value.materia.id + '">' + value.materia.nombre_fantasia + '</option>')
        })
        $('#materia_siu-' + fila).selectpicker('render');
    }
</script>
