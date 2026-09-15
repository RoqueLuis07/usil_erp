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

    $(document).on('keyup', '.puntos', function () {
        calcularTotalPuntos();
    })

    var niveles = {!!json_encode($niveles, JSON_HEX_TAG) !!}
    var detalle_accion_add;
    $(document).on('click', '.btn-add', function () {
        var niveles = {!!json_encode($niveles, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.nivel-' + nro_ultima_fila + ' option:selected').val() != '' && $('.descripcion-' + nro_ultima_fila).val() != '' && $('.puntos-' + nro_ultima_fila).val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="col-lg-2 mb-2 text-center" id="div-nivel-${nro_nueva_fila}">
                                        <label class="form-label label-nivel" for="nivel-${nro_nueva_fila}">Pertenece a <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker nivel-${nro_nueva_fila} nivel @error('detalles.${nro_nueva_fila}.nivel') is-invalid @enderror" id="nivel-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][nivel]" data-live-search="true">

                                        </select>
                                        <span class="invalid-feedback error-nivel-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-6 mb-2 text-center" id="div-descripcion-${nro_nueva_fila}">
                                        <label class="form-label label-descripcion" for="descripcion-${nro_nueva_fila}">Descripción <span class="text-danger">(*)</span></label>
                                        <textarea class="form-control descripcion-${nro_nueva_fila} descripcion @error('detalles.${nro_nueva_fila}.descripcion') is-invalid @enderror" id="descripcion-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][descripcion]" cols="3${nro_nueva_fila}" rows="1${nro_nueva_fila}">{{old('detalles.${nro_nueva_fila}.descripcion')}}</textarea>
                                        <span class="invalid-feedback error-descripcion-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 me-3 text-center" id="div-puntos-${nro_nueva_fila}">
                                        <label class="form-label label-puntos">Puntos <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center puntos-${nro_nueva_fila} puntos @error('detalles.${nro_nueva_fila}.puntos') is-invalid @enderror" id="detalles[${nro_nueva_fila}][puntos]" name="detalles[${nro_nueva_fila}][puntos]" value="{{old('detalles.${nro_nueva_fila}.puntos')}}" data-id="${nro_nueva_fila}">
                                        <span class="invalid-feedback error-puntos-${nro_nueva_fila}" role="alert">

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

            detalle_accion_add = $('#btn-add-' + fila).detach();

            $('#detalle-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            $('.nivel-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(niveles, function (i, val) {
                $('.nivel-' + nro_nueva_fila).append('<option value="' + val.id + '">' + val.nombre + '</option>');
            })
            $('.nivel-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            detalle_accion_add = $('#btn-add-' + nro_nueva_fila);

            new Cleave ('.puntos-' + nro_nueva_fila, formatoSeparadorMiles);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var niveles = {!!json_encode($niveles, JSON_HEX_TAG) !!}
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

        calcularTotalPuntos();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-nivel').text() == '') {
            $('<label class="form-label label-nivel">Pertenece a <span class="text-danger">(*)</span></label>').insertBefore('#nivel-' + primera_fila);
        }
        if ($('.label-descripcion').text() == '') {
            $('<label class="form-label label-descripcion">Descripción <span class="text-danger">(*)</span></label>').insertBefore('.descripcion-' + primera_fila);
        }
        if ($('.label-puntos').text() == '') {
            $('<label class="form-label label-puntos">Puntos <span class="text-danger">(*)</span></label>').insertBefore('.puntos-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotalPuntos() {
        var cantidad_filas = $('.fila').length;
        var total_carga_horaria = 0;
        var total_puntos = 0;
        $('.puntos:hidden').each(function () {
            var punto = $(this).val();
            if (punto == '') {
                punto = 0;
            }
            total_puntos = parseInt(total_puntos) + parseInt(credito);
        })
        $('#puntaje_total').val(Intl.NumberFormat('de-DE').format(total_puntos));
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
        // new Cleave ('.puntos-0', formatoSeparadorMiles);

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        for (let index = 0; index < nro_ultima_fila; index++) {
            new Cleave ('.puntos-' + index, formatoSeparadorMiles);

        }

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
                    $('.nivel-' + index).val(arrayFilas[index].nivel);
                    $('.nivel-' + index).selectpicker('val', arrayFilas[index].nivel);
                    $('.descripcion-' + index).val(arrayFilas[index].descripcion);
                    $('.puntos-' + index).val(arrayFilas[index].puntos);
                }
                calcularTotalPuntos();
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
