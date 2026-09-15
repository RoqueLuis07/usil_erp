<script type="module">
    const hoy = moment();
    // const min_date = hoy.clone().subtract(15, 'days').format('YYYY-MM-DD');
    const today = hoy.format('Y-M-D');
	const min_date = hoy.clone().startOf('day').format('YYYY-MM-DD HH:mm:ss'); // Hoy a las 00:00
	const max_date = hoy.clone().endOf('day').format('YYYY-MM-DD HH:mm:ss'); // Hoy a las 23:59
	const default_date = {!!json_encode($fecha_default, JSON_HEX_TAG) !!}

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'Y-m-d H:i:s',
        minDate: min_date,
        maxDate: max_date,
        enableTime: true,
        time_24hr: true,
        defaultDate: default_date,
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

        ClassicEditor
            .create(document.querySelector('#observaciones'), {
                language: 'es',
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'insertTable', 'blockQuote', '|',
                    'undo', 'redo'
                ],
            })
            .then(editor => {

            })
            .catch(error => {
                console.error(error);
            });
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
                window.location.href = '{{route('pantallas_docentes.index', Auth::id())}}';
            }
        })
    });

    $('#save-btn').click(function () {
        var materia = $('#materia').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la clase de la materia ' + materia + '?',
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
</script>
