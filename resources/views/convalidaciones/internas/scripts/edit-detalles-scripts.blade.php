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
        var materias = {!! json_encode($materias, JSON_HEX_TAG) !!}

        if ($('#materia_origen-' + nro_ultima_fila).val() != '' && $('#calificacion_origen-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class=" mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-5 col-lg-3 mb-2 text-center" id="div-materia_origen-${nro_nueva_fila}">
                                        <select class="selectpicker form-control materia_origen-${nro_nueva_fila} materia_origen @error('detalles.${nro_nueva_fila}.materia_origen') is-invalid @enderror" id="materia_origen-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia_origen]" data-live-search="true" data-id="${nro_nueva_fila}">
                                            <option value="" selected disabled>Seleccionar...</option>

                                        </select>
                                        <span class="invalid-feedback error-materia_origen-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-5 col-lg-2 mb-2 text-center" id="div-calificacion_origen-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center calificacion_origen-${nro_nueva_fila} calificacion_origen @error('detalles.${nro_nueva_fila}.calificacion_origen') is-invalid @enderror" id="calificacion_origen-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][calificacion_origen]" value="{{old('detalles.${nro_nueva_fila}.calificacion_origen')}}" readonly>
                                        <span class="invalid-feedback error-calificacion_origen-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-1 col-lg-1 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="0"><i class="ri-add-fill"></i></button>
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

            cargar_materias_origen(nro_nueva_fila, materias);
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
        if ($('.label-materia_origen').text() == '') {
            $('<label class="form-label label-materia_origen">Materia <span class="text-danger">(*)</span></label>').insertBefore('#materia_origen-' + primera_fila);
        }
        if ($('.label-calificacion_origen').text() == '') {
            $('<label class="form-label label-calificacion_origen">Calificación <span class="text-danger">(*)</span></label>').insertBefore('#calificacion_origen-' + primera_fila);
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
                    $('.materia_origen-' + index).selectpicker('val', arrayFilas[index].materia_origen);
                    $('.calificacion_origen-' + index).val(arrayFilas[index].calificacion_origen);
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

    $(document).on('change', '.materia_origen', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        var alumno = $('#alumno_id').val();
        var materia = $(this).val();
        var url = "{{route('convalidaciones_internas.get_nota', ['alumno' => ":alumno", 'materia' => ":materia"])}}"
        url = url.replace(':alumno', alumno);
        url = url.replace(':materia', materia);
        get_nota(id, url);
    })

    function cargar_materias_origen(fila, materias) {
        $('#materia_origen-' + fila).selectpicker('destroy');
        $('#materia_origen-' + fila).empty();
        $('#materia_origen-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');

        $.each(materias, function (index, value) {
            $('#materia_origen-' + fila).append('<option value="' + value.id + '">' + value.nombre_fantasia + '</option>')
        })
        $('#materia_origen-' + fila).selectpicker('render');
    }

    function get_nota(id, url) {
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            if (response.nota == 'N/A' || response.nota == 1) {
                $('#materia_origen-' + id).selectpicker('val', '');
                message('La materia seleccionada no se encuentra aprobada por el alumno, favor seleccione una válida.')
            } else {
                $('#calificacion_origen-' + id).val(response.nota);
            }
        })
    }
</script>
