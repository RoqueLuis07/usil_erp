<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

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

    $('#cancel-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la operación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{route('maestrias.index')}}';
            }
        })
    });

    $('#update-btn').click(function () {
        var nombre = $('#nombre_fantasia').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar los módulos de la maestría ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#update-form').submit();
            }
        })
    });

    var modulo_id = 0;
    $(document).on('click', '.add-modulo', function (e) {
        modulo_id = $(this).data('id');
    })

    $('#save-modulo-btn').click(function () {
        var nombre = ($('#nombre_fantasia').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el módulo ' + nombre +'?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                saveModulo(modulo_id);
            }
        })
    });

    function saveModulo(modulo_id) {
        const formData = new FormData(document.getElementById('store-modulo-form'));
        $('#store-modulo-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-modulo-form').find('.invalid-feedback').remove();
        $.ajax({
            url: '{{route('modulos_maestrias.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#store-modulo-form').trigger('reset');
            $('#createModuloModal').modal('hide');
            $('.modulo-' + modulo_id).selectpicker('destroy');
            $('.modulo-' + modulo_id).empty();
            $('.modulo-' + modulo_id).append('<option value="" disabled>Selecionar...</option>');
            $.each(response.modulos, function (index, value) {
                $('.modulo-' + modulo_id).append('<option value="' + value.id + '" data-subtext="' + value.nombre_real + '">' + value.nombre_fantasia + '</option>');
            })

            $('.modulo-' + modulo_id).addClass('selectpicker').val(response.selected.id).selectpicker('render');

            message(response.message, 'success');
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'tipo_modulo') {
                    var div = $('#div-modulo');
                    div.addClass('is-invalid')
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(div);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    var modulos = {!!json_encode($modulos, JSON_HEX_TAG) !!}
    var modulo_accion_add;
    var modulosSelected = [];
    $(document).on('click', '.btn-add', function () {
        var modulos = {!!json_encode($modulos, JSON_HEX_TAG) !!}
        var docentes = {!!json_encode($docentes, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.semestre-' + nro_ultima_fila).val() && $('.modulo-' + nro_ultima_fila + ' option:selected').val() && $('.docente-' + nro_ultima_fila + ' option:selected').val()) {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-1 mb-2 text-center">
                                        <input class="form-control text-center semestre-${nro_nueva_fila} semestre @error('detalles.${nro_nueva_fila}.semestre') is-invalid @enderror" id="semestre-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][semestre]" data-id="${nro_nueva_fila}"></input>
                                        <span class="invalid-feedback error-semestre-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-3 mb-2 text-center">
                                        <div class="d-flex flex-row bd-highlight" id="div-modulo-${nro_nueva_fila}">
                                            <select class="selectpicker form-control modulo-${nro_nueva_fila} modulo @error('detalles.${nro_nueva_fila}.modulo') is-invalid @enderror" id="modulo-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][modulo]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                            </select>
                                            <span class="invalid-feedback error-modulo-${nro_nueva_fila}" role="alert">

                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-success add-modulo" data-bs-toggle="modal" data-bs-target="#createModuloModal" data-id="${nro_nueva_fila}"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center">
                                        <select class="selectpicker form-control docente-${nro_nueva_fila} docente @error('detalles.${nro_nueva_fila}.docente') is-invalid @enderror" id="docente-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][docente]" data-live-search="true" data-live-search-normalize="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        <span class="invalid-feedback error-docente-${nro_nueva_fila}" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            modulo_accion_add = $('#btn-add-' + fila).detach();

            modulosSelected.forEach(item => {
                for (let index in modulos) {
                    if (modulos[index].id == item) {
                        modulos.splice(index, 1);
                    }
                }
            });

            $('#modulo-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            $('.modulo-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(modulos, function (i, val) {
                $('.modulo-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
            })
            $('.modulo-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            $('.docente-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(docentes, function (i, val) {
                $('.docente-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.primer_nombre + ' ' + val.primer_apellido + '</option>');
            })
            $('.docente-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            modulo_accion_add = $('#btn-add-' + nro_nueva_fila);
        } else {
            message('Debe completar los campos requeridos antes de agregar la siguiente línea.')
        }
    })

    $(document).on('change', '.modulo', function () {
        var modulos = {!!json_encode($modulos, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.modulo-' + fila + ' option:selected').val();

        modulosSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.modulo-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                modulosSelected.push(parseInt(valor));
            }
        }
        modulosSelected.sort();

        modulosSelected.forEach(item => {
            for (let index in modulos) {
                if (modulos[index].id == item) {
                    modulos.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.modulo-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.modulo-' + index + ' option:selected').text();
                $('.modulo-' + index).selectpicker('destroy');
                $('.modulo-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.modulo-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.modulo-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(modulos, function (i, val) {
                    $('.modulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                })
                $('.modulo-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.modulo-' + index).selectpicker('destroy');
                $('.modulo-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.modulo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(modulos, function (i, val) {
                    $('.modulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                })
                $('.modulo-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var modulos = {!!json_encode($modulos, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.modulo-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        modulo_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = modulosSelected.indexOf(parseInt(valor));
        if (modulosSelected.length != 0) {
            modulosSelected.splice(indice_eliminar, 1);
        }
        modulosSelected.sort();

        if (modulosSelected.length != 0) {
            modulosSelected.forEach(item => {
                for (let index in modulos) {
                    if (modulos[index].id == item) {
                        modulos.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.modulo-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.modulo-' + nro_ultima_fila + ' option:selected').text();
                $('.modulo-' + nro_ultima_fila).selectpicker('destroy');
                $('.modulo-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.modulo-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.modulo-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(modulos, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.modulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                    }
                })
                $('.modulo-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.modulo-' + nro_ultima_fila).selectpicker('destroy');
                $('.modulo-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.modulo-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(modulos, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.modulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                    }
                })
                $('.modulo-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.modulo-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.modulo-' + index + ' option:selected').text();
                    $('.modulo-' + index).selectpicker('destroy');
                    $('.modulo-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.modulo-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.modulo-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(modulos, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.modulo-' + index).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                        }
                    })
                    $('.modulo-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.modulo-' + index).selectpicker('destroy');
                    $('.modulo-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.modulo-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(modulos, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.modulo-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.nombre_real + '">' + val.nombre_fantasia + '</option>');
                        }
                    })
                    $('.modulo-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-modulo').text() == '') {
            $('<label class="form-label label-modulo">Módulo <span class="text-danger">(*)</span></label>').insertBefore('#div-modulo-' + primera_fila);
        }
        if ($('.label-docente').text() == '') {
            $('<label class="form-label label-docente">Docente <span class="text-danger">(*)</span></label>').insertBefore('#div-docente-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    $(document).on('focus', '.semestre', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^[1-4]$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).ready(function () {
        new Cleave($('#carga_horaria'), formatoSeparadorMiles);

        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.modulo-' + index).val(arrayFilas[index].modulo);
                    $('.modulo-' + index).selectpicker('val', arrayFilas[index].modulo);
                    $('.docente-' + index).val(arrayFilas[index].docente);
                    $('.docente-' + index).selectpicker('val', arrayFilas[index].docente);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                modulosSelected = [];
                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.modulo-' + index + ' option:selected').val();
                    if ((parseInt(valor) || 0) != 0) {
                        modulosSelected.push(parseInt(valor));
                    }
                }
                modulosSelected.sort();

                modulosSelected.forEach(item => {
                    for (let index in modulos) {
                        if (modulos[index].id == item) {
                            modulos.splice(index, 1);
                        }
                    }
                });

                $('.modulo').trigger('change');
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
