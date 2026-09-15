<script type="module">
    $(document).ready(function () {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    })

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

    $('#save-btn').click(function () {
        var nombre = $('#nombre_proyecto').val().toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cargar el informe de la extensión universitaria ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                save(id, url);
            }
        })
    });

    $('#informe').on('change', function () {
        $('#eliminar-informe').prop('disabled', false);
    })

    $('#eliminar-informe').on('click', function () {
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
                $('#informe').val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $('#btn-change-proyecto').on('click', function () {
        $('#tipo_adjunto').val('PROYECTO');
        $('.titulo_adjuntos').text('Cambiar Proyecto');
        $('.label_adjuntos').html('Proyecto <span class="text-danger">(*)</span>');
    })

    $('#btn-change-informe').on('click', function () {
        $('#tipo_adjunto').val('INFORME');
        $('.titulo_adjuntos').text('Cambiar Informe');
        $('.label_adjuntos').html('Informe <span class="text-danger">(*)</span>');
    })

    $('#save-adjuntos-btn').click(function () {
        var nombre = $('#nombre_proyecto').val().toUpperCase();
        var tipo = $('#tipo_adjunto').val().toLowerCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cambiar el ' + tipo + ' de la extensión universitaria ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                save_adjuntos(id, url);
            }
        })
    });

    function save(id, url) {
        const formData = new FormData(document.getElementById('cargarInforme-form-' + id));
        $('#cargarInforme-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#cargarInforme-form-' + id).find('.invalid-feedback').remove();
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
            window.localStorage.setItem('type', type);
            var url_show = "{{route('pantallas_docentes.show_extensiones_universitarias', ":id")}}";
            url_show = url_show.replace(':id', id);
            window.location.href = url_show;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    function save_adjuntos(id, url) {
        const formData = new FormData(document.getElementById('cambiarAdjuntos-form-' + id));
        $('#cambiarAdjuntos-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#cambiarAdjuntos-form-' + id).find('.invalid-feedback').remove();
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
            window.localStorage.setItem('type', type);
            var url_show = "{{route('pantallas_docentes.show_extensiones_universitarias', ":id")}}";
            url_show = url_show.replace(':id', id);
            window.location.href = url_show;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }
</script>
