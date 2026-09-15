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

    var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
    var materia_accion_add;
    var materiasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.materia-' + nro_ultima_fila + ' option:selected').val() != '') {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-materia-${nro_nueva_fila}">
                                            <select class="selectpicker form-control materia-${nro_nueva_fila} materia @error('detalles.${nro_nueva_fila}.materia') is-invalid @enderror" id="materia-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                            </select>
                                            <span class="invalid-feedback error-materia-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-2 col-sm-2 text-center">
                                            <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                            </div>
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

            materia_accion_add = $('#btn-add-' + nro_nueva_fila);
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

        var valor = $('.materia-' + fila + ' option:selected').val();

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

        calcularTotalCorrelatividades();
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

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

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
            //                 $('.materia-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
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

        calcularTotalCorrelatividades();
    })

    function insertarLabels(primera_fila) {
        if ($('.label-materia').text() == '') {
            $('<label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    function calcularTotalCorrelatividades() {
        var total_correlatividades = $('.fila').length;
        $('#cantidad_correlatividades').val(Intl.NumberFormat('de-DE').format(total_correlatividades));
    }

    $(document).ready(function () {
        var detalles = {!! json_encode($materia->correlativas, JSON_HEX_TAG) !!}
        if (!$.isEmptyObject(detalles)) {
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
                    $('.materia-' + index).val(arrayFilas[index].materia);
                    $('.materia-' + index).selectpicker('val', arrayFilas[index].materia);
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

                calcularTotalCorrelatividades();
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
