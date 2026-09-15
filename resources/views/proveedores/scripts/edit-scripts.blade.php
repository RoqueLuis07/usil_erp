<script type="module">
    $(document).ready(function () {
        var departamentoSelected = $('#departamento_paraguay option:selected').val();
        var ciudadSelected = $('#ciudad_input').val();

        var ciudades = {!!json_encode($ciudades, JSON_HEX_TAG) !!}
        $('#ciudad').selectpicker('destroy');
        $('#ciudad option').each(function () {
            $(this).remove();
        });

        $('#ciudad').append('<option value="" selected disabled>Seleccionar...</option>');
        $.each(ciudades, function (i, val) {
            if (parseInt(departamentoSelected) == val.departamento_id) {
                if (val.id == ciudadSelected) {
                    $('#ciudad').append('<option value="' + val.id + '" selected>' + val.nombre + '</option>');
                } else {
                    $('#ciudad').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                }

            }
        })
        $('#ciudad').prop('disabled', false);
        $('#ciudad').addClass('selectpicker').selectpicker('render');
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).removeClass('is-invalid');
    })

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
                window.location.href = '{{route('proveedores.index')}}';
            }
        })
    });

    $('#update-btn').click(function () {
        var razon_social = ($('#razon_social').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el proveedor ' + razon_social + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $('#update-form').submit();
            }
        })
    });

    $('#departamento_paraguay').on('change', function () {
        var departamentoSelected = $(this).val();
        var ciudades = {!!json_encode($ciudades, JSON_HEX_TAG) !!}
        $('#ciudad').selectpicker('destroy');
        $('#ciudad option').each(function () {
            $(this).remove();
        });

        $('#ciudad').append('<option value="" selected disabled>Seleccionar...</option>');
        $.each(ciudades, function (i, val) {
            if (parseInt(departamentoSelected) == val.departamento_id) {
                $('#ciudad').append('<option value="' + val.id + '">' + val.nombre + '</option>');
            }
        })
        $('#ciudad').prop('disabled', false);
        $('#ciudad').addClass('selectpicker').selectpicker('render');
    })

    $(document).on('focus', '#ruc', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+(-\d?)?$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).on('focus', '#telefono', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });
</script>
