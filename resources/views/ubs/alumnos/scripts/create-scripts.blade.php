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
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).removeClass('is-invalid');
    })

    $('#nacionalidad').on('shown.bs.select', function () {
        $('.bootstrap-select').removeClass('is-invalid');
    })

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
                window.location.href = '{{route('alumnos_ubs.index')}}';
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
	
	$('#numero_documento').on('change', function() {
		$('#numero_documento').removeClass('is-invalid');
		
		var cedula = $(this).val();
		var url = "{{route('alumnos_ubs.get_cedula', ":cedula")}}";
		url = url.replace(':cedula', cedula);
		$.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if (response.alumno_existe == 'SI') {
				$('#numero_documento').addClass('is-invalid');
			} {
				$('#numero_documento').addClass('is-valid');
			}
        }).fail(function(response) {

        })
	})

    $('#save-btn').click(function () {
        var nombre = ($('#primer_nombre_alumno').val()).toUpperCase();
        var apellido = ($('#primer_apellido_alumno').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el alumno ' + nombre + ' ' + apellido + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                save();
            }
        })
    });

    function save() {
        const formData = new FormData(document.getElementById('store-form'));
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: '{{route('alumnos_ubs.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', 'success');
            window.location.href = '{{route('alumnos_ubs.index')}}';
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                if (input.hasClass('flatpickr') ) {
                    var i = $('#calendar-icon');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(i);
                        i.detach();
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }

                if (key == 'nacionalidad') {
                    $('.' + key).addClass('is-invalid');
                    $('#error-nacionalidad').addClass('invalid-feedback').html('<strong>' + value + '</strong>');
                }

            })
        })
    }

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

    $('#save-institucion-educativa-btn').click(function () {
        var nombre = ($('#nombre_institucion_educativa').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la institución educativa ' + nombre +'?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                saveInstitucionEducativa();
            }
        })
    });

    function saveInstitucionEducativa() {
        const formData = new FormData(document.getElementById('store-institucion-educativa-form'));
        $('#store-institucion-educativa-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-institucion-educativa-form').find('.invalid-feedback').remove();
        $.ajax({
            url: '{{route('instituciones_educativas.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#store-institucion-educativa-form').trigger('reset');
            $('#createInstitucionEducativaModal').modal('hide');
            $('.institucion_educativa').selectpicker('destroy');
            $('.institucion_educativa option').each(function () {
                $(this).remove();
            });
            $('.institucion_educativa optgroup').each(function () {
                $(this).remove();
            });
            $('.institucion_educativa').append('<option value="" disabled>Selecionar...</option>');
            $.each(response.instituciones_educativas, function (index, value) {
                $('.institucion_educativa').append('<option value="' + value.id + '">' + value.nombre + '</option>');

            })

            $('.institucion_educativa').addClass('selectpicker').val(response.selected.id).selectpicker('render');

            message(response.message, 'success');
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'tipo_institucion_educativa') {
                    var div = $('#div-institucion-educativa');
                    div.addClass('is-invalid')
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(div);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

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

    $(document).on('focus', '#anho_egreso_educativo', function () {
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

    $(document).on('focus', '#celular_familiar1', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });

    $(document).on('focus', '#celular_familiar2', function () {
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
