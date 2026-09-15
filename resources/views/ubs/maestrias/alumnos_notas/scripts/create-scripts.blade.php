<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    const today = new Date();

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'Y-m-d H:i:s',
        // minDate: today,
        defaultDate: today,
        enableTime: true,
        time_24hr: true,
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

        $('.ausente-presente-btn').on('click', function () {
            var id = $(this).data('id');
            if ($('#asistencia-' + id).val() == 'AUSENTE') {
                $(this).removeClass('btn-outline-danger');
                $(this).removeClass('btn-outline-success');
                $(this).addClass('btn-success');
                $('#asistencia-' + id).val('PRESENTE');
                $('.ausente-presente-btn-text-' + id).html('Presente');
                $('.puntos-' + id).prop('disabled', false);
            } else {
                $(this).removeClass('btn-success');
                $(this).addClass('btn-outline-danger');
                $('#asistencia-' + id).val('AUSENTE');
                $('.ausente-presente-btn-text-' + id).html('Ausente');
                $('.puntos-' + id).val('');
                $('#puntos-' + id).val('');
                $('.puntos-' + id).prop('disabled', true);
            }
        })

        $('.ausente-presente-btn').mouseleave( function () {
            if (!$(this).hasClass('btn-success')) {
                $(this).removeClass('btn-outline-success').addClass('btn-outline-danger');
            }
        })
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

    $('#save-btn').click(function () {
        var curso = $('#curso').val();
        var evaluacion = $('#evaluacion').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar los puntajes de la evaluacion ' + evaluacion + ' de la maestría ' + curso + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    });

    $(document).on('focus', '.puntos', function () {
        new Cleave ($(this), formatoSeparadorMiles);
    })

    const minPuntaje = 0
    const maxPuntaje = 60;

    $(document).on('input', '.puntos', function () {
		if ($('#evaluacion_id').val() != 1) {
			maxPuntaje = 40;
		}
		
        var id = $(this).data('id');

        var valor = $(this).val();
        valor = valor.replace('.', '');

        if (valor < minPuntaje) {
            valor = minPuntaje;
        } else if (valor > maxPuntaje) {
            valor = maxPuntaje;
        }

        $(this).val(valor);
        $('#puntos-' + id).val(valor);
    })
</script>
