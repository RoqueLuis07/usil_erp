<script type="module">
    $(document).ready(function() {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    });

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

    $('#delete-ingreso-filter-btn').on('click', function () {
        $('#filtro_ingreso').selectpicker('val', '');
        $('#filtro_ingreso').trigger('change');
    })

    $('#filtro_ingreso').change(function () {
        $('#filtro_ingreso_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-edad-filter-btn').on('click', function () {
        $('#filtro_edad').selectpicker('val', '');
        $('#filtro_edad').trigger('change');
    })

    $('#filtro_edad').change(function () {
        $('#filtro_edad_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-programa-filter-btn').on('click', function () {
        $('#filtro_programa').selectpicker('val', '');
        $('#filtro_programa').trigger('change');
    })

    $('#filtro_programa').change(function () {
        $('#filtro_programa_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-periodo-filter-btn').on('click', function () {
        $('#filtro_periodo').selectpicker('val', '');
        $('#filtro_periodo').trigger('change');
    })

    $('#filtro_periodo').change(function () {
        $('#filtro_periodo_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-correo-filter-btn').on('click', function () {
        $('#filtro_correo').selectpicker('val', '');
        $('#filtro_correo').trigger('change');
    })

    $('#filtro_correo').change(function () {
        $('#filtro_correo_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-ubs-filter-btn').on('click', function () {
        $('#filtro_ubs').selectpicker('val', '');
        $('#filtro_ubs').trigger('change');
    })

    $('#filtro_ubs').change(function () {
        $('#filtro_ubs_input').val($(this).val());
        $('#buscar_form').submit();
    });
</script>
