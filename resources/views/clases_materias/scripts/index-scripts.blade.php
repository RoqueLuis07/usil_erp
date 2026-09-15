<script type="module">
    const today = new Date();

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'Y-m-d H:i:s',
        maxDate: today,
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

    var filtro_fecha;
    $(document).ready(function() {
        filtro_fecha = flatpickr('.flatpickr', flatpickrOptions);
    });


    if (document.querySelector(".pagination-next"))
    document.querySelector(".pagination-next").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : '' : '';
    });

    if (document.querySelector(".pagination-prev"))
    document.querySelector(".pagination-prev").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : '' : '';
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

    $('#delete-fecha-filter-btn').on('click', function () {
        filtro_fecha.clear();
        $('#filtro_fecha_input').val('');
        $('#buscar_form').submit();
    })

    $('#filtro_fecha').change(function () {
        $('#filtro_fecha_input').val($(this).val());
    });

    $('#search-fecha-filter-btn').click(function () {
        $('#buscar_form').submit();
    });

    $('#delete-materia-filter-btn').on('click', function () {
        $('#filtro_materia').selectpicker('val', '');
        $('#filtro_materia').trigger('change');
    })

    $('#filtro_materia').change(function () {
        $('#filtro_materia_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-carrera-filter-btn').on('click', function () {
        $('#filtro_carrera').selectpicker('val', '');
        $('#filtro_carrera').trigger('change');
    })

    $('#filtro_carrera').change(function () {
        $('#filtro_carrera_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-docente-filter-btn').on('click', function () {
        $('#filtro_docente').selectpicker('val', '');
        $('#filtro_docente').trigger('change');
    })

    $('#filtro_docente').change(function () {
        $('#filtro_docente_input').val($(this).val());
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

    $('#delete-inscriptos-filter-btn').on('click', function () {
        $('#filtro_inscriptos').selectpicker('val', '');
        $('#filtro_inscriptos').trigger('change');
    })

    $('#filtro_inscriptos').change(function () {
        $('#filtro_inscriptos_input').val($(this).val());
        $('#buscar_form').submit();
    });
</script>
