<script type="module">
    $(document).ready(function() {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
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

    if ($('#error').val() != null) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: $('#error').val(),
            icon: 'warning',
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
        })
    }

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

    $('#delete-periodo-filter-btn').on('click', function () {
        $('#filtro_periodo').selectpicker('val', '');
        $('#filtro_periodo').trigger('change');
    })

    $('#filtro_periodo').change(function () {
        $('#filtro_periodo_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-fecha-filter-btn').on('click', function () {
        $('#filtro_fecha').selectpicker('val', '');
        $('#filtro_fecha').trigger('change');
    })

    $('#filtro_fecha').change(function () {
        $('#filtro_fecha_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-alumno-filter-btn').on('click', function () {
        $('#filtro_alumno').selectpicker('val', '');
        $('#filtro_alumno').trigger('change');
    })

    $('#filtro_alumno').change(function () {
        $('#filtro_alumno_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-ingreso-filter-btn').on('click', function () {
        $('#filtro_ingreso').selectpicker('val', '');
        $('#filtro_ingreso').trigger('change');
    })

    $('#filtro_ingreso').change(function () {
        $('#filtro_ingreso_input').val($(this).val());
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

    $('#delete-carrera-filter-btn').on('click', function () {
        $('#filtro_carrera').selectpicker('val', '');
        $('#filtro_carrera').trigger('change');
    })

    $('#filtro_carrera').change(function () {
        $('#filtro_carrera_input').val($(this).val());
        $('#buscar_form').submit();
    });

    $('#delete-carrera_siu-filter-btn').on('click', function () {
        $('#filtro_carrera_siu').selectpicker('val', '');
        $('#filtro_carrera_siu').trigger('change');
    })

    $('#filtro_carrera_siu').change(function () {
        $('#filtro_carrera_siu_input').val($(this).val());
        $('#buscar_form').submit();
    });
</script>
