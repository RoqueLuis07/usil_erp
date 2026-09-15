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

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function() {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);

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

    $('.monto').on('focus', function () {
        var id = $(this).data('id');
        new Cleave ('#monto_virtual-' + id, formatoSeparadorMiles);
        new Cleave ('#monto_teams-' + id, formatoSeparadorMiles);
        new Cleave ('#monto_presencial-' + id, formatoSeparadorMiles);
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

    $('#delete-edad-filter-btn').on('click', function () {
        $('#filtro_edad').selectpicker('val', '');
        $('#filtro_edad').trigger('change');
    })

    $('#filtro_edad').change(function () {
        $('#filtro_edad_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-area-filter-btn').on('click', function () {
        $('#filtro_area').selectpicker('val', '');
        $('#filtro_area').trigger('change');
    })

    $('#filtro_area').change(function () {
        $('#filtro_area_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-didactica-filter-btn').on('click', function () {
        $('#filtro_didactica').selectpicker('val', '');
        $('#filtro_didactica').trigger('change');
    })

    $('#filtro_didactica').change(function () {
        $('#filtro_didactica_input').val($(this).val());
        $('#buscar_form').submit();
    });
</script>
