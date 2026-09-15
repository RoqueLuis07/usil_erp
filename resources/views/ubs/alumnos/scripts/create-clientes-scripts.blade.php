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

    var cliente_accion_add;
    var clientesSelected = [];
    $(document).on('click', '.btn-add', function () {
        var clientes = {!!json_encode($clientes, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);

        if ($('.cliente-' + nro_ultima_fila + ' option:selected').val() != null) {
            var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                <div class="row d-flex flex-wrap justify-content-center">
                                    <div class="col-lg-2 mb-2 text-center" id="div-cliente-${nro_nueva_fila}">
                                        <select class="selectpicker form-control cliente-${nro_nueva_fila} cliente @error('clientes.${nro_nueva_fila}.cliente') is-invalid @enderror" id="cliente-${nro_nueva_fila}" name="clientes[${nro_nueva_fila}][cliente]" data-live-search="true" data-id="${nro_nueva_fila}">

                                        </select>
                                        @error('clientes.${nro_nueva_fila}.cliente')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-2 me-3 text-center" id="div-razon_social-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center razon_social-${nro_nueva_fila} razon_social @error('clientes.${nro_nueva_fila}.razon_social') is-invalid @enderror" id="clientes[${nro_nueva_fila}][razon_social]" name="clientes[${nro_nueva_fila}][razon_social]" value="{{old('clientes.${nro_nueva_fila}.razon_social')}}" data-id="${nro_nueva_fila}" readonly>
                                        @error('clientes.${nro_nueva_fila}.razon_social')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-2 me-3 text-center" id="div-numero_documento-${nro_nueva_fila}">
                                        <input type="text" class="form-control text-center numero_documento-${nro_nueva_fila} numero_documento @error('clientes.${nro_nueva_fila}.numero_documento') is-invalid @enderror" id="clientes[${nro_nueva_fila}][numero_documento]" name="clientes[${nro_nueva_fila}][numero_documento]" value="{{old('clientes.${nro_nueva_fila}.numero_documento')}}" data-id="${nro_nueva_fila}" readonly>
                                        @error('clientes.${nro_nueva_fila}.numero_documento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-1 mb-2 me-3 text-center" id="div-es_principal-${nro_nueva_fila}">
                                        <div class="div-label-es_principal-${nro_nueva_fila}">

                                        </div>
                                        <div class="btn-group @error('clientes.${nro_nueva_fila}.es_principal') is-invalid @enderror" role="group">
                                            <input type="radio" class="btn-check es_principal1 es_princpial1-${nro_nueva_fila}" id="clientes[${nro_nueva_fila}][es_princpial1]" name="clientes[${nro_nueva_fila}][es_principal]" value="false" @if (old('clientes.${nro_nueva_fila}.es_principal') == 'false') checked @endif data-id="${nro_nueva_fila}">
                                            <label class="btn btn-outline-danger" for="clientes[${nro_nueva_fila}][es_princpial1]">No</label>
                                            <input type="radio" class="btn-check es_principal2 es_princpial2-${nro_nueva_fila}" id="clientes[${nro_nueva_fila}][es_princpial2]" name="clientes[${nro_nueva_fila}][es_principal]" value="true" @if (old('clientes.${nro_nueva_fila}.es_principal') == 'true') checked @endif data-id="${nro_nueva_fila}">
                                            <label class="btn btn-outline-success" for="clientes[${nro_nueva_fila}][es_princpial2]">Sí</label>
                                        </div>
                                        <input type="hidden" class="es_principal-${nro_nueva_fila}" value="{{old('clientes.${nro_nueva_fila}.es_principal')}}">
                                        @error('clientes.${nro_nueva_fila}.es_principal')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-1 col-sm-2 text-center">
                                        <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

            clientesSelected.forEach(item => {
                for (let index in clientes) {
                    if (clientes[index].id == item) {
                        clientes.splice(index, 1);
                    }
                }
            });

            cliente_accion_add = $('#btn-add-' + fila).detach();

            $('#cliente-fila').append(filaAdd);
            if (cantidad_filas == 1) {
                if ($('#acciones-0').length) {
                    $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                } else {
                    $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                }
            }

            $('.cliente-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(clientes, function (i, val) {
                $('.cliente-' + nro_nueva_fila).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
            })
            $('.cliente-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

            cliente_accion_add = $('#btn-add-' + nro_nueva_fila);
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.')
        }
    })

    $(document).on('change', '.cliente', function () {
        var clientes = {!!json_encode($clientes, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.cliente-' + fila + ' option:selected').val();
        $.each(clientes, function (index, value) {
            if (value.id == valor) {
                $('.razon_social-' + fila).val(value.razon_social);
                $('.numero_documento-' + fila).val(value.numero_documento);
            }
        })

        if (fila == 0) {
            $('.es_princpial2-' + fila).prop('checked', true);
        } else {
            $('.es_princpial1-' + fila).prop('checked', true);
        }

        clientesSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.cliente-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                clientesSelected.push(parseInt(valor));
            }
        }
        clientesSelected.sort();

        clientesSelected.forEach(item => {
            for (let index in clientes) {
                if (clientes[index].id == item) {
                    clientes.splice(index, 1);
                }
            }
        });

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.cliente-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.cliente-' + index + ' option:selected').text();
                $('.cliente-' + index).selectpicker('destroy');
                $('.cliente-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.cliente-' + index).append('<option value="" disabled>Seleccionar...</option>');
                $('.cliente-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                $.each(clientes, function (i, val) {
                    $('.cliente-' + index).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                })
                $('.cliente-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.cliente-' + index).selectpicker('destroy');
                $('.cliente-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.cliente-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(clientes, function (i, val) {
                    $('.cliente-' + index).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                })
                $('.cliente-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var clientes = {!!json_encode($clientes, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.cliente-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }
        cliente_accion_add.appendTo('#acciones-' + nro_ultima_fila);

        var indice_eliminar = clientesSelected.indexOf(parseInt(valor));
        if (clientesSelected.length != 0) {
            clientesSelected.splice(indice_eliminar, 1);
        }
        clientesSelected.sort();

        if (clientesSelected.length != 0) {
            clientesSelected.forEach(item => {
                for (let index in clientes) {
                    if (clientes[index].id == item) {
                        clientes.splice(index, 1);
                    }
                }
            });
        }

        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = ultima_fila.split('-')[1];
        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.cliente-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.cliente-' + nro_ultima_fila + ' option:selected').text();
                $('.cliente-' + nro_ultima_fila).selectpicker('destroy');
                $('.cliente-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.cliente-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                $('.cliente-' + nro_ultima_fila).append('<option value="' + valor_siguiente + '" selected>' + texto_siguiente + '</option>');
                $.each(clientes, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.cliente-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                    }
                })
                $('.cliente-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.cliente-' + nro_ultima_fila).selectpicker('destroy');
                $('.cliente-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.cliente-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(clientes, function (i, val) {
                    if (parseInt(valor_siguiente) != val.id) {
                        $('.cliente-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                    }
                })
                $('.cliente-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.cliente-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.cliente-' + index + ' option:selected').text();
                    $('.cliente-' + index).selectpicker('destroy');
                    $('.cliente-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.cliente-' + index).append('<option value="" disabled>Seleccionar...</option>');
                    $('.cliente-' + index).append('<option value="' + valor + '" selected>' + texto + '</option>');
                    $.each(clientes, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.cliente-' + index).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.cliente-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.cliente-' + index).selectpicker('destroy');
                    $('.cliente-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.cliente-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(clientes, function (i, val) {
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.cliente-' + nro_ultima_fila).append('<option value="' + val.id + '" data-subtext="' + val.numero_documento + '">' + val.nombre + '</option>');
                        }
                    })
                    $('.cliente-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-cliente').text() == '') {
            $('<label class="form-label label-cliente">Cliente <span class="text-danger">(*)</span></label>').insertBefore('#cliente-' + primera_fila);
        }
        if ($('.label-razon_social').text() == '') {
            $('<label class="form-label label-razon_social">Razón Social <span class="text-danger">(*)</span></label>').insertBefore('.razon_social-' + primera_fila);
        }
        if ($('.label-numero_documento').text() == '') {
            $('<label class="form-label label-numero_documento">N° de Documento <span class="text-danger">(*)</span></label>').insertBefore('.numero_documento-' + primera_fila);
        }
        if ($('.label-es_principal').text() == '') {
            $('.div-label-es_principal-' + primera_fila).html('<label class="form-label label-es_principal">Es Principal ? <span class="text-danger">(*)</span></label>')
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    $(document).on('click', '.es_principal2', function () {
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            if (index != fila) {
                $('.es_principal1-' + index).prop('checked', true);
            }
        }
    })

    $(document).on('click', '.es_principal1', function () {
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];

        if ($('.es_principal2:checked').length == 0) {
            $('.es_principal2-' + nro_primera_fila).prop('checked', true);
        }
    })
</script>
