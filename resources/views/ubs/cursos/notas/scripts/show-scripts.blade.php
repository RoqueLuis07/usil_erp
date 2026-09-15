<script type="module">
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

    $(document).ready(function() {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    });

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $(document).on('focus', '.puntaje_obtenido', function () {
        new Cleave($(this), formatoSeparadorMiles);
    })

    $(document).on('keyup', '.puntaje_obtenido', function () {
        var id = $(this).data('id');
        var escala = {!!json_encode($escala->escalaDetalles, JSON_HEX_TAG) !!}
        var puntaje = parseInt($(this).val());

        if (isNaN(puntaje)) {
            $(this).val(0);
            puntaje = 0;
        }

        $.each(escala, function (index, value) {
            if (puntaje >= value.punto_minimo && puntaje <= value.punto_maximo) {
                $('#calificacion-' + id).val(value.nota);
            }
        })
    })

    $('.edit-puntaje').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-warning me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cambiar la nota del alumno seleccionado?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                // $('#edit-puntaje-form-' + id).submit();
                edit_puntaje(id, url);
            }
        })
    })

    function edit_puntaje(id, url) {
        const formData = new FormData(document.getElementById('edit-puntaje-form-' + id));
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

        })
    }

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
