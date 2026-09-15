<script type="module">
    const today = new Date();

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        maxDate: today,
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
            shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            },
            months: {
            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
            longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            },
        },
    }

    $(document).ready(function () {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);

        var departamentoSelected = $('#departamento option:selected').val();
        var ciudadSelected = $('#ciudad_input').val();
        var barrioSelected = $('#barrio_input').val();


        var nacionalidades = {!!json_encode($nacionalidades, JSON_HEX_TAG) !!}
        var docente_nacionalidades = {!!json_encode($docente->nacionalidades, JSON_HEX_TAG) !!}
        var addedIds = new Set();

        $('#nacionalidad').append('<option value="" disabled>Seleccionar...</option>');
        $.each(nacionalidades, function (index, value) {
            var selected = false;
            $.each(docente_nacionalidades, function (i, val) {
                if (value.id == val.id) {
                    selected = true;
                }
            });
            if (!addedIds.has(value.id)) {
                $('#nacionalidad').append('<option value="' + value.id + '"' + (selected ? ' selected' : '') + '>' + value.nombre + '</option>');
                addedIds.add(value.id);
            }
        });
        $('#nacionalidad').selectpicker('render');

        var ciudades = {!!json_encode($ciudades, JSON_HEX_TAG) !!}
        $('#ciudad').selectpicker('destroy');
        $('#ciudad option').each(function () {
            $(this).remove();
        });

        $('#ciudad').append('<option value="" disabled>Seleccionar...</option>');
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

        var barrios = {!!json_encode($barrios, JSON_HEX_TAG) !!}
        $('#barrio').selectpicker('destroy');
        $('#barrio option').each(function () {
            $(this).remove();
        });

        $('#barrio').append('<option value="" disabled>Seleccionar...</option>');
        $.each(barrios, function (i, val) {
            if (parseInt(ciudadSelected) == val.ciudad_id) {
                if (val.id == barrioSelected) {
                    $('#barrio').append('<option value="' + val.id + '" selected>' + val.nombre + '</option>');
                } else {
                    $('#barrio').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                }

            }
        })
        $('#barrio').prop('disabled', false);
        $('#barrio').addClass('selectpicker').selectpicker('render');
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

    $('.btn-check').on('click', function () {
        $('.btn-group').removeClass('is-invalid');
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
                window.location.href = '{{route('docentes_ubs.index')}}';
            }
        })
    });

    $('#clean-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de vaciar todos los campos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('form :input').val('');
                $('.selectpicker').selectpicker('val', '');
            }
        })
    })

    $('#update-btn').click(function () {
        var nombre = ($('#primer_nombre_docente').val()).toUpperCase();
        var apellido = ($('#primer_apellido_docente').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el docente ' + nombre + ' ' + apellido + '?',
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

    $('#departamento').on('change', function () {
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

        $('#barrio').selectpicker('destroy');
        $('#barrio option').each(function () {
            $(this).remove();
        });

        $('#barrio').append('<option value="" selected disabled>Seleccionar...</option>');
        $('#barrio').prop('disabled', true);
        $('#barrio').addClass('selectpicker').selectpicker('render');
    })

    $('#ciudad').on('change', function () {
        var ciudadSelected = $(this).val();
        var barrios = {!!json_encode($barrios, JSON_HEX_TAG) !!}
        $('#barrio').selectpicker('destroy');
        $('#barrio option').each(function () {
            $(this).remove();
        });

        $('#barrio').append('<option value="" selected disabled>Seleccionar...</option>');
        $.each(barrios, function (i, val) {
            if (parseInt(ciudadSelected) == val.ciudad_id) {
                $('#barrio').append('<option value="' + val.id + '">' + val.nombre + '</option>');
            }
        })
        $('#barrio').prop('disabled', false);
        $('#barrio').addClass('selectpicker').selectpicker('render');
    })

    $(document).on('focus', '#numero_documento', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^([A-Fa-f]\d*|\d+)$/;

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

    $(document).on('focus', '#celular', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).on('focus', '#telefono_laboral', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).on('focus', '#celular_laboral', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

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
</script>
