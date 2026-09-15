<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function () {
        new Cleave ('#puntaje_obtenido', formatoSeparadorMiles);
    })

    $(document).on('keyup', '.puntaje_obtenido', function () {
        var posibles = parseInt($('#total_posible').val());

        if ($(this).val() > posibles) {
            $(this).val(posibles);
            $('#puntaje_obtenido:hidden').val(posibles)
        }
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
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
                var id = $('#inscripcion_id').val();
                var url = "{{route('inscripciones_temas_tesis.show', ":id")}}";
                url = url.replace(':id', id);
                window.location.href = url;
            }
        })
    });

    $('#save-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la rúbrica del alumno?',
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

    $('#acta').on('change', function () {
        $('#eliminar-acta').prop('disabled', false);
    })

    $('#eliminar-acta').on('click', function () {
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
                $('#acta').val('');
                $(this).prop('disabled', true);
            }
        });
    });
</script>
