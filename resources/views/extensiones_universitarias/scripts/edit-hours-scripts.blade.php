<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorDecimales = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 2,
    };

    $(document).ready(function () {
        var largo_detalles = parseInt($('#largo_detalles').val());
        for (let index = 0; index < largo_detalles; index++) {
            new Cleave ('#cantidad_horas_alumno-' + index, formatoSeparadorDecimales);
        }
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
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
                var url = $(this).data('url');
                window.location.href = url;
            }
        })
    });

    $('#update-btn').click(function () {
        var nombre = $('#nombre_proyecto').val().toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cargas las horas a los alumnos participantes de la extensión universitaria ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#update-form').submit();
            }
        })
    });
</script>
