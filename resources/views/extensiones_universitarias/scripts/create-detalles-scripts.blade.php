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

    // var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
    var alumno_accion_add;
    var alumnosSelected = [];
    $(document).on('click', '.btn-add', function () {
        var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.alumno-' + nro_ultima_fila + ' option:selected').val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-alumno-${nro_nueva_fila}">
                                        <select class="selectpicker form-control alumno-${nro_nueva_fila} alumno @error('detalles.${nro_nueva_fila}.alumno') is-invalid @enderror" id="alumno-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][alumno]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-alumno-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 col-sm-2 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            alumno_accion_add = $('#btn-add-' + fila).detach();

            $('#alumno-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            // alumnosSelected.forEach(item => {
                // for (let index in alumnos) {
                    // if (alumnos[index].id == item) {
                        // alumnos.splice(index, 1);
                    // }
                // }
            // });

            $('.alumno-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(alumnos, function (i, val) {
                $('.alumno-' + nro_nueva_fila).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
            })
            $('.alumno-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            alumno_accion_add = $('#btn-add-' + nro_nueva_fila);
        } else {
            message('Debe seleccionar un alumno antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.alumno', function () {
        // var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
        // var cantidad_filas = $('.fila').length;
        // var fila = $(this).data('id');
        // var primera_fila = $('.fila:first').prop('id');
        // var nro_primera_fila = primera_fila.split('-')[1];
        // var ultima_fila = $('.fila:last').prop('id');
        // var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        // var valor = $('.alumno-' + fila + ' option:selected').val();

        // alumnosSelected = [];
        // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            // var valor = $('.alumno-' + index + ' option:selected').val();
            // if ((parseInt(valor) || 0) != 0) {
                // alumnosSelected.push(parseInt(valor));
            // }
        // }
        // alumnosSelected.sort();

        // alumnosSelected.forEach(item => {
            // for (let index in alumnos) {
                // if (alumnos[index].id == item) {
                    // alumnos.splice(index, 1);
                // }
            // }
        // });

        // for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            // var valor = $('.alumno-' + index + ' option:selected').val();
            // if ((parseInt(valor) || 0) != 0) {
                // var texto = $('.alumno-' + index + ' option:selected').text();
                // $('.alumno-' + index).selectpicker('destroy');
                // $('.alumno-' + index + ' option').each(function () {
                    // $(this).remove();
                // });

                // $('.alumno-' + index).append('<option value="" disabled>Seleccionar...</option>');
                // $('.alumno-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                // $.each(alumnos, function (i, val) {
                    // $('.alumno-' + index).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                // })
                // $('.alumno-' + index).addClass('selectpicker').selectpicker('render');
            // } else {
                // $('.alumno-' + index).selectpicker('destroy');
                // $('.alumno-' + index + ' option').each(function () {
                    // $(this).remove();
                // });

                // $('.alumno-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                // $.each(alumnos, function (i, val) {
                    // $('.alumno-' + index).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                // })
                // $('.alumno-' + index).addClass('selectpicker').selectpicker('render');
            // }
        // }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.alumno-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        alumno_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        // var indice_eliminar = alumnosSelected.indexOf(parseInt(valor));
        // if (alumnosSelected.length != 0) {
            // alumnosSelected.splice(indice_eliminar, 1);
        // }
        // alumnosSelected.sort();

        // if (alumnosSelected.length != 0) {
            // alumnosSelected.forEach(item => {
                // for (let index in alumnos) {
                    // if (alumnos[index].id == item) {
                        // alumnos.splice(index, 1);
                    // }
                // }
            // });
        // }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            // var valor_siguiente = $('.alumno-' + nro_ultima_fila + ' option:selected').val();
            // if ((parseInt(valor_siguiente) || 0) != 0) {
                // var texto_siguiente = $('.alumno-' + nro_ultima_fila + ' option:selected').text();
                // $('.alumno-' + nro_ultima_fila).selectpicker('destroy');
                // $('.alumno-' + nro_ultima_fila + ' option').each(function () {
                    // $(this).remove();
                // });

                // $('.alumno-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                // $('.alumno-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                // $.each(alumnos, function (i, val) {
                    // if (parseInt(valor_siguiente) != val.id) {
                        // $('.alumno-' + nro_ultima_fila).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                    // }
                // })
                // $('.alumno-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            // } else {
                // $('.alumno-' + nro_ultima_fila).selectpicker('destroy');
                // $('.alumno-' + nro_ultima_fila + ' option').each(function () {
                    // $(this).remove();
                // });

                // $('.alumno-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    // $.each(alumnos, function (i, val) {
                    // if (parseInt(valor_siguiente) != val.id) {
                        // $('.alumno-' + nro_ultima_fila).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                    // }
                // })
                // $('.alumno-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            // }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            // var primera_fila = $('.fila:first').prop('id');
            // var nro_primera_fila = primera_fila.split('-')[1];
            // var ultima_fila = $('.fila:last').prop('id');
            // var nro_ultima_fila = ultima_fila.split('-')[1];

            // for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                // var valor = $('.alumno-' + index + ' option:selected').val();
                // if ((parseInt(valor) || 0) != 0) {
                    // var texto = $('.alumno-' + index + ' option:selected').text();
                    // $('.alumno-' + index).selectpicker('destroy');
                    // $('.alumno-' + index + ' option').each(function () {
                        // $(this).remove();
                    // });

                    // $('.alumno-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    // $('.alumno-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    // $.each(alumnos, function (i, val) {
                        // if (parseInt(valor_siguiente) != val.id) {
                            // $('.alumno-' + index).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                        // }
                    // })
                    // $('.alumno-' + index).addClass('selectpicker').selectpicker('render');
                // } else {
                    // $('.alumno-' + index).selectpicker('destroy');
                    // $('.alumno-' + index + ' option').each(function () {
                        // $(this).remove();
                    // });

                    // $('.alumno-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    // $.each(alumnos, function (i, val) {
                        // if (parseInt(valor_siguiente) != val.id) {
                            // $('.alumno-' + index).append('<option value="' + val.id + '"' + ((val.carrera_id && val.anho_ingreso) ? '' : ' disabled') + ' data-subtext="' + val.numero_documento + ((val.carrera_id && val.anho_ingreso) ? '' : ' · datos incompletos') + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
                        // }
                    // })
                    // $('.alumno-' + index).addClass('selectpicker').selectpicker('render');
                // }
            // }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-alumno').text() == '') {
            $('<label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label>').insertBefore('#alumno-' + primera_fila);
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
                    $('.alumno-' + index).val(arrayFilas[index].alumno);
                    $('.alumno-' + index).selectpicker('val', arrayFilas[index].alumno);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                // alumnosSelected = [];
                // for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    // var valor = $('.alumno-' + index + ' option:selected').val();
                    // if ((parseInt(valor) || 0) != 0) {
                        // alumnosSelected.push(parseInt(valor));
                    // }
                // }
                // alumnosSelected.sort();

                // alumnosSelected.forEach(item => {
                    // for (let index in alumnos) {
                        // if (alumnos[index].id == item) {
                            // alumnos.splice(index, 1);
                        // }
                    // }
                // });

                $('.alumno').trigger('change');
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
