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
        var cant_filas = $('.puntaje_obtenido').length;
        for (let index = 0; index < cant_filas; index++) {
            new Cleave ('.puntaje_obtenido-' + index, formatoSeparadorMiles);
        }
    })

    $(document).on('keyup', '.puntaje_obtenido', function () {
        var fila = $(this).data('id');
        var posibles = parseInt($('#puntos-' + fila).text());

        if ($(this).val() > posibles) {
            $(this).val(posibles);
            $('#puntaje_obtenido-' + fila + ':hidden').val(posibles)
        }
        calcularTotalPuntos();
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

    function calcularTotalPuntos() {
        var total_puntos = 0;
        $('.puntaje_obtenido:hidden').each(function () {
            var punto = $(this).val();
            if (punto == '') {
                punto = 0;
            }
            total_puntos = parseInt(total_puntos) + parseInt(punto);
        })
        $('#puntaje_total').val(Intl.NumberFormat('de-DE').format(total_puntos));
    }
</script>
