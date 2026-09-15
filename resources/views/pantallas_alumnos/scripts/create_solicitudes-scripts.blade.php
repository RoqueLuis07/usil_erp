<script type="module">
    const hoy = moment();
    const manana = hoy.add(1, 'days');
    const fecha = manana.format('Y-M-D');

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        minDate: fecha,
        enableTime: false,
        defaultDate: fecha,
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
        var tipos = {!!json_encode($tipos_solicitudes, JSON_HEX_TAG) !!}
        var tipo = {!!json_encode(old('tipo_solicitud'), JSON_HEX_TAG) !!}
        var monto = 0;
        $.each(tipos, function (index, value) {
                if (value.id == tipo) {
                    monto = value.monto;
                }
            })
        monto = Intl.NumberFormat('de-DE').format(parseInt(monto));
        if (tipo == 1) {
            $('#card-certificado-estudios').removeClass('d-none');
            $('#div-mensaje-certificado-estudios').html('<p class="text-muted">El costo del certificado de estudios es de Gs. ' + monto + ' y se nacesita la aprobación del Dpto. Académico.</p>');
        } else if (tipo == 2) {
            $('#card-suficiencia').removeClass('d-none');
            $('#div-mensaje-suficiencia').html('<p class="text-muted">El costo del examen de suficiencia es de Gs. ' + monto + ' y necesita la aprobación del Dpto. Académico.</p>');
        } else if (tipo == 3) {
            $('#card-inasistencia').removeClass('d-none');
            $('#div-mensaje-inasistencia').html('<p class="text-muted">La solicitud de inasistencia es sin costo y necesita la aprobación del Dpto. Académico.</p>');
        } else if (tipo == 4) {
            $('#card-constancia-carrera').removeClass('d-none');
            $('#div-mensaje-constancia-carrera').html('<p class="text-muted">El costo de la constancia de cursar una carrera es de Gs. ' + monto + ' y necesita la aprobación del Dpto. Académico.</p>');
        } else if (tipo == 5) {
            $('#card-tutoria').removeClass('d-none');
        } else if (tipo == 6) {
            $('#card-desmatriculacion').removeClass('d-none');
            $('#div-mensaje-desmatriculacion').html('<p class="text-muted">La desmatriculación de una materia es sin costo y necesita la aprobación del Dpto. Académico.</p>');
        }
    })

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


    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

    $('#tipo_solicitud').on('change', function () {
        $('#card-certificado-estudios').addClass('d-none');
        $('#card-suficiencia').addClass('d-none');
        $('#card-inasistencia').addClass('d-none');
        $('#card-constancia-carrera').addClass('d-none');
        $('#card-tutoria').addClass('d-none');
        $('#card-desmatriculacion').addClass('d-none');

        var tipos = {!!json_encode($tipos_solicitudes, JSON_HEX_TAG) !!}
        var seleccionado = $(this).val();
        var monto = 0;

        $('.tipo_solicitud_form').val(seleccionado);
        if (seleccionado == 1) {
            $('#card-certificado-estudios').removeClass('d-none');
            $.each(tipos, function (index, value) {
                if (value.id == seleccionado) {
                    monto = value.monto;
                }
            })

            if (isNaN(monto)) {
                $('#div-mensaje-certificado-estudios').html('<p class="text-muted">El certificado de estudios posee un costo y necesita la aprobación del Dpto. Académico.</p>');
            } else {
                monto = Intl.NumberFormat('de-DE').format(parseInt(monto));
                $('#div-mensaje-certificado-estudios').html('<p class="text-muted">El costo del certificado de estudios es de Gs. ' + monto + ' y se nacesita la aprobación del Dpto. Académico.</p>');
            }
        } else if (seleccionado == 2) {
            var fecha_solicitud = {!!json_encode($examen_suficiencia_fecha_solicitud, JSON_HEX_TAG) !!}
            var fecha_inicio = dayjs(fecha_solicitud.fecha_inicio).format('DD/MM/YYYY');
            var fecha_fin = dayjs(fecha_solicitud.fecha_fin).format('DD/MM/YYYY');

            $('#card-suficiencia').removeClass('d-none');
            $.each(tipos, function (index, value) {
                if (value.id == seleccionado) {
                    monto = value.monto;
                }
            })

            if (isNaN(monto)) {
                $('#div-mensaje-suficiencia').html('<p class="text-muted">El examen de suficiencia posee un costo y necesita la aprobación del Dpto. Académico.<br><span class="text-danger">Recuerde que puede realizar esta solicitud desde el ' + fecha_inicio + ' y antes del ' + fecha_fin +'.</p>');
            } else {
                monto = Intl.NumberFormat('de-DE').format(parseInt(monto));
                $('#div-mensaje-suficiencia').html('<p class="text-muted">El costo del examen de suficiencia es de Gs. ' + monto + ' y necesita la aprobación del Dpto. Académico.<br><span class="text-danger">Recuerde que puede realizar esta solicitud desde el ' + fecha_inicio + ' y antes del ' + fecha_fin +'.</p>');
            }
        } else if (seleccionado == 3) {
            $('#card-inasistencia').removeClass('d-none');

            $('#div-mensaje-inasistencia').html('<p class="text-muted">La solicitud de inasistencia es sin costo y necesita la aprobación del Dpto. Académico.</p>');
        } else if (seleccionado == 4) {
            $('#card-constancia-carrera').removeClass('d-none');

            $.each(tipos, function (index, value) {
                if (value.id == seleccionado) {
                    monto = value.monto;
                }
            })

            if (isNaN(monto)) {
                $('#div-mensaje-constancia-carrera').html('<p class="text-muted">La constancia de cursar una carrera posee un costo y necesita la aprobación del Dpto. Académico.</p>');
            } else {
                monto = Intl.NumberFormat('de-DE').format(parseInt(monto));
                $('#div-mensaje-constancia-carrera').html('<p class="text-muted">El costo de la constancia de cursar una carrera es de Gs. ' + monto + ' y necesita la aprobación del Dpto. Académico.</p>');
            }
        } else if (seleccionado == 5) {
            $('#card-tutoria').removeClass('d-none');
        } else if (seleccionado == 6) {
            var fecha_desmatriculacion = {!!json_encode($fechas_desmatriculacion, JSON_HEX_TAG) !!}
            var fecha_inicio = dayjs(fecha_desmatriculacion.fecha_inicio).format('DD/MM/YYYY');
            var fecha_fin = dayjs(fecha_desmatriculacion.fecha_fin).format('DD/MM/YYYY');

            $('#card-desmatriculacion').removeClass('d-none');

            $('#div-mensaje-desmatriculacion').html('<p class="text-muted">La desmatriculación de una materia es sin costo y necesita la aprobación del Dpto. Académico.<br><span class="text-danger">Recuerde que puede realizar esta solicitud desde el ' + fecha_inicio + ' y antes del ' + fecha_fin +'.</p>');
        }
    })

    $('#modalidad_tutoria').on('change', function () {
        var precios = {!!json_encode($modalidades_tutorias, JSON_HEX_TAG) !!};
        var seleccionado = $(this).val();
        var monto = 0;

        $.each(precios, function (index, value) {
            if (value.modalidad_id == seleccionado) {
                monto = value.precio;
            }
        })

        if (isNaN(monto)) {
            $('#div-mensaje-tutoria').html('<p class="text-muted">La tutoría posee un costo y necesita la aprobación del Dpto. Académico.</p>');
        } else {
            monto = Intl.NumberFormat('de-DE').format(parseInt(monto));
            $('#div-mensaje-tutoria').html('<p class="text-muted">El costo de la tutoría es de Gs. ' + monto + ' y necesita la aprobación del Dpto. Académico.</p>');
        }
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
                window.location.href = '{{route('root')}}';
            }
        })
    });

    $('.save-btn').click(function () {
        var solicitud = $('#tipo_solicitud option:selected').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de realizar la solicitud de ' + solicitud + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, solicitar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var tipo = $(this).data('id');
                $('#store-' + tipo + '-form').submit();
            }
        })
    });

    $('.adjunto').on('change', function () {
        $('.eliminar-adjunto').prop('disabled', false);
    })

    $('.eliminar-adjunto').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el archivo subido?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('.adjunto').val('');
                $(this).prop('disabled', true);
            }
        });
    });
</script>
