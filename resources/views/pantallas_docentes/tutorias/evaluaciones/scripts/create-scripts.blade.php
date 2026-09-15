<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $(document).on('click', '#cancel-btn', function () {
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
                window.location.href = '{{route('tutorias_docentes.index', Auth::id())}}';
            }
        })
    })

    $('#save-btn').click(function () {
        var materia = $('#materia').val();
        var evaluacion = $('#evaluacion_detalle option:selected').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la evaluación ' + evaluacion + ' de tutoría de la materia ' + materia + '?',
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

    $(document).on('click', '.ausente-presente-btn', function () {
        var id = $(this).data('id');
        if ($('#ausente-presente-input-' + id).val() == 'AUSENTE') {
            $(this).removeClass('btn-outline-danger');
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-success');
            $('#ausente-presente-input-' + id).val('');
            $('.ausente-presente-btn-text-' + id).html('Presente');
            $('#puntaje_obtenido-' + id).prop('readonly', false);
            var puntaje_obtenido = new Cleave ('#puntaje_obtenido-' + id, formatoSeparadorMiles);
        } else {
            $(this).removeClass('btn-success');
            $(this).addClass('btn-outline-danger');
            $('#ausente-presente-input-' + id).val('AUSENTE');
            $('.ausente-presente-btn-text-' + id).html('Ausente');
            $('#puntaje_obtenido-' + id).val('');
            $('#puntaje_obtenido-' + id).prop('readonly', true);
        }
    })

    $(document).on('mouseleave', '.ausente-presente-btn', function () {
        if (!$(this).hasClass('btn-success')) {
            $(this).removeClass('btn-outline-success').addClass('btn-outline-danger');
        }
    })

    const minPuntaje = 0;
    const maxPuntaje = 100;

    $(document).on('input', '.puntaje_obtenido', function () {
        var valor = $(this).val();
        valor = valor.replace(/\./g, '');

        if (valor < minPuntaje) {
            valor = minPuntaje;
        } else if (valor > maxPuntaje) {
            valor = maxPuntaje;
        }

        $(this).val(valor);
    })
</script>
