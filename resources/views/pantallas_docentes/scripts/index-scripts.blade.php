<script type="module">
    if ($('#success-message').val() != null) {
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
            icon: 'success',
            text: $('#success-message').val(),
        })
    };

    if ($('#error-message').val() != null) {
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
            icon: 'error',
            text: $('#error-message').val(),
        })
    };

    $('#btn-generate-clases').on('click', function () {
        if ($('#materia_clase option:selected').val()) {
            var usuario = $('#usuario_clase').val();
            var periodo_activo = $('#periodo_activo_clase').val();
            var materia_seleccionada = $('#materia_clase option:selected').val();
            var url = "{{route('pantallas_docentes.generate_clases', ['usuario' => ":usuario", 'materia' => ":materia", 'semestre' => ":semestre"])}}";
            url = url.replace(':usuario', usuario);
            url = url.replace(':materia', materia_seleccionada);
            url = url.replace(':semestre', periodo_activo);

            window.location.href = url;
        } else {
            $('#materia_clase').selectpicker('destroy');
            $('#materia_clase').addClass('is-invalid');
            $('#materia_clase').selectpicker('render');
        }
    })

    $('#btn-charge-asistencias').on('click', function () {
        if ($('#materia_asistencia option:selected').val()) {
            var usuario = $('#usuario_asistencia').val();
            var periodo_activo = $('#periodo_activo_asistencia').val();
            var materia_seleccionada = $('#materia_asistencia option:selected').val();
            var url = "{{route('pantallas_docentes.charge_asistencias', ['usuario' => ":usuario", 'materia' => ":materia", 'semestre' => ":semestre"])}}";
            url = url.replace(':usuario', usuario);
            url = url.replace(':materia', materia_seleccionada);
            url = url.replace(':semestre', periodo_activo);

            window.location.href = url;
        } else {
            $('#materia_asistencia').selectpicker('destroy');
            $('#materia_asistencia').addClass('is-invalid');
            $('#materia_asistencia').selectpicker('render');
        }
    })

    $('#btn-charge-evaluaciones').on('click', function () {
        if ($('#materia_evaluacion option:selected').val() && $('#carrera_evaluacion option:selected').val() && $('#tipo_evaluacion option:selected').val()) {
            var usuario = $('#usuario_evaluacion').val();
            var periodo_activo = $('#periodo_activo_evaluacion').val();
            var materia_seleccionada = $('#materia_evaluacion option:selected').val();
            var carrera_seleccionada = $('#carrera_evaluacion option:selected').val();
            var tipo = $('#tipo_evaluacion option:selected').val();
            var url = "{{route('pantallas_docentes.charge_evaluaciones', ['usuario' => ":usuario", 'materia' => ":materia", 'carrera' => ":carrera", 'semestre' => ":semestre", 'tipo' => ":tipo"])}}";
            url = url.replace(':usuario', usuario);
            url = url.replace(':materia', materia_seleccionada);
            url = url.replace(':carrera', carrera_seleccionada);
            url = url.replace(':semestre', periodo_activo);
            url = url.replace(':tipo', tipo);

            window.location.href = url;
        } else {
            if (!$('#materia_evaluacion option:selected').val()) {
                $('#materia_evaluacion').selectpicker('destroy');
                $('#materia_evaluacion').addClass('is-invalid');
                $('#materia_evaluacion').selectpicker('render');
            }
            if (!$('#tipo_evaluacion option:selected').val()) {
                $('#tipo_evaluacion').selectpicker('destroy');
                $('#tipo_evaluacion').addClass('is-invalid');
                $('#tipo_evaluacion').selectpicker('render');
            }
            if (!$('#carrera_evaluacion option:selected').val()) {
                $('#carrera_evaluacion').selectpicker('destroy');
                $('#carrera_evaluacion').addClass('is-invalid');
                $('#carrera_evaluacion').selectpicker('render');
            }
        }
    })

    $('#materia_evaluacion').on('change', function () {
        if ($('#carrera_evaluacion option:selected').val()) {
            var usuario = $('#usuario_evaluacion').val();
            var materia_seleccionada = $(this).val();
            var carrera_seleccionada = $('#carrera_evaluacion option:selected').val();
            var periodo_activo = $('#periodo_activo_evaluacion').val();

            var url = "{{route('pantallas_docentes.obtener_evaluaciones', ['usuario' => ":usuario", 'materia' => ":materia", 'carrera' => ":carrera", 'semestre' => ":semestre"])}}";
            url = url.replace(':usuario', usuario);
            url = url.replace(':materia', materia_seleccionada);
            url = url.replace(':carrera', carrera_seleccionada);
            url = url.replace(':semestre', periodo_activo);

            obtener_evaluaciones(url);
        }
    })

    $('#carrera_evaluacion').on('change', function () {
        if ($('#materia_evaluacion option:selected').val()) {
            var usuario = $('#usuario_evaluacion').val();
            var materia_seleccionada = $('#materia_evaluacion option:selected').val();
            var carrera_seleccionada = $(this).val()
            var periodo_activo = $('#periodo_activo_evaluacion').val();

            var url = "{{route('pantallas_docentes.obtener_evaluaciones', ['usuario' => ":usuario", 'materia' => ":materia", 'carrera' => ":carrera", 'semestre' => ":semestre"])}}";
            url = url.replace(':usuario', usuario);
            url = url.replace(':materia', materia_seleccionada);
            url = url.replace(':carrera', carrera_seleccionada);
            url = url.replace(':semestre', periodo_activo);

            obtener_evaluaciones(url);
        }
    })

    function obtener_evaluaciones(url) {
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
            $('#tipo_evaluacion').selectpicker('destroy');
            $('#tipo_evaluacion').empty()
            $('#tipo_evaluacion').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.evaluaciones, function (index, value) {
                $('#tipo_evaluacion').append('<option value="' + value.id + '">' + value.nombre + '</option>');
            })
            $('#tipo_evaluacion').prop('disabled', false);
            $('#tipo_evaluacion').selectpicker('render');
        })
    }
</script>
