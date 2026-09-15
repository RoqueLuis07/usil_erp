<script type="module">
    $(document).ready(function () {
        var evaluacion = {!!json_encode($evaluacion, JSON_HEX_TAG) !!}
        if (evaluacion.tipo_evaluacion_id == 1) {
            var filas = $('#cantidad_filas').val();
            for (let index = 0; index < filas; index++) {
                $('#puntaje_obtenido-' + index).prop('readonly', false);
                var puntaje_obtenido = new Cleave ('#puntaje_obtenido-' + index, formatoSeparadorMiles);
            }
        }
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
                var url = $(this).data('url');
                window.location.href = url;
            }
        })
    })

    $('#save-btn').click(function () {
        var materia = $('#materia').val();
        var evaluacion = $('#evaluacion').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la evaluación de ' + evaluacion + ' de la materia ' + materia + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                // var url = $(this).data('url');
                // save(url);
                $('#store-form').submit();
            }
        })
    });

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

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

    var minPuntaje = 0
    var maxPuntaje = 30;

    $(document).on('input', '.puntaje_obtenido', function () {
		var id = $(this).data('id');
		if ($('#evaluacion_id').val() == 4 || $('#evaluacion_id').val() == 5 || $('#evaluacion_id').val() == 6) {
			maxPuntaje = 40;
		}

        var valor = $(this).val();
        valor = valor.replace('.', '');

        if (valor < minPuntaje) {
            valor = minPuntaje;
        } else if (valor > maxPuntaje) {
            valor = maxPuntaje;
        }

        $(this).val(valor);
        $('#puntaje_obtenido-' + id).val(valor);
    })
</script>
