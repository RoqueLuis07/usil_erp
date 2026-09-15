<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $(document).ready(function() {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }

        new Cleave('#puntaje_obtenido', formatoSeparadorMiles);
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

    $('#generar-btn').on('click', function () {
        setTimeout(() => {
            location.reload();
        }, 3000);
    })

    $('#asignar-fecha-defensa-btn').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de asignar la fecha seleccionada?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, asignar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                asignar_fecha_defensa(url);
            }
        })
    })

    function asignar_fecha_defensa(url) {
        const formData = new FormData(document.getElementById('asignar-fecha-defensa-form'));
        $('#asignar-fecha-defensa-form').find('.is-invalid').removeClass('is-invalid');
        $('#asignar-fecha-defensa-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', 'success');
            location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    $(document).on('keyup', '#puntaje_obtenido', function () {
        var escala = {!!json_encode($escala->escalaDetalles, JSON_HEX_TAG) !!}
        var puntaje = parseInt($(this).val());

        if (isNaN(puntaje)) {
            $(this).val(0);
            puntaje = 0;
        }

        $.each(escala, function (index, value) {
            if (puntaje >= value.punto_minimo && puntaje <= value.punto_maximo) {
                $('#calificacion').val(value.nota);
            }
        })
    })

    $('#puntuar-defensa-btn').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de puntuar la defensa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, puntuar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                $('#puntuar-defensa-form').submit();
                // puntuar_defensa(url);
            }
        })
    })

    function puntuar_defensa(url) {
        const formData = new FormData(document.getElementById('puntuar-defensa-form'));
        $('#puntuar-defensa-form').find('.is-invalid').removeClass('is-invalid');
        $('#puntuar-defensa-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', 'success');
            location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    const minPuntaje = 0;
    const maxPuntaje = 100;

    $(document).on('input', '#puntaje_obtenido', function () {
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
