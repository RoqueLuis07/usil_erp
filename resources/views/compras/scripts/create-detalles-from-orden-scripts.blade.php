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

    $('#unidad_negocio').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('compras.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    $(document).on('change', '.centro_costo', function () {
        var id = $(this).val();
        var fila = $(this).data('id');
        var url = "{{ route('compras.get_subcentros_costos', ":id") }}";
        url = url.replace(':id', id);

        get_subcentros_costos(url, fila);
    });

    //Inicio formatos para CleaveJS
    const formatoNumero = {
        blocks: [3,3,7],
        delimiter: '-',
        numericOnly: true
    };

    $(document).ready(function () {
        new Cleave ('#numero_factura', formatoNumero);
        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.centro_costo-' + index).val(arrayFilas[index].centro_costo);
                    $('.centro_costo-' + index).selectpicker('val', arrayFilas[index].centro_costo);
                    $('.subcentro_costo-' + index).val(arrayFilas[index].subcentro_costo);
                    $('.subcentro_costo-' + index).selectpicker('val', arrayFilas[index].subcentro_costo);
                    $('.iva-' + index).selectpicker('val', arrayFilas[index].iva);

                    var centro_costoSelected = $('.centro_costo-' + index + ' option:selected').val();
                    if (centro_costoSelected) {
                        $('#centro_costo-' + index).trigger('change');
                    }
                }

                var unidad_negocioSelected = $('#unidad_negocio option:selected').val();
                if (unidad_negocioSelected) {
                    $('#unidad_negocio').trigger('change');
                }

                var centro_costoSelected = $('#centro_costo-0 option:selected').val();
                if (centro_costoSelected) {
                    $('#centro_costo-0').trigger('change');
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

    function get_subunidades_negocios(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subunidad_negocio').selectpicker('destroy');
            $('#subunidad_negocio').empty();
            $('#subunidad_negocio').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subunidades_negocios, function (index, value) {
                $('#subunidad_negocio').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subunidades_negocios.length == 1) {
                    $('#subunidad_negocio').val(value.id);
                }
            })
            $('#subunidad_negocio').prop('disabled', false);
            $('#subunidad_negocio').selectpicker('render');
        })
    }

    function get_subcentros_costos(url, fila) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subcentro_costo-' + fila).selectpicker('destroy');
            $('#subcentro_costo-' + fila).empty();
            $('#subcentro_costo-' + fila).append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subcentros_costos, function (index, value) {
                $('#subcentro_costo-' + fila).append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subcentros_costos.length == 1) {
                    $('#subcentro_costo-' + fila).val(value.id);
                }
            })
            $('#subcentro_costo-' + fila).prop('disabled', false);
            $('#subcentro_costo-' + fila).selectpicker('render');
        })
    }
</script>
