<script type="module">
    $(document).ready(function () {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
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

    $('#archivo-entrega').on('change', function () {
        $('#eliminar-archivo-entrega').prop('disabled', false);
    })

    $('#archivo-correccion').on('change', function () {
        $('#eliminar-archivo-correccion').prop('disabled', false);
    })

    $('#eliminar-archivo-entrega').on('click', function () {
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
                $('#archivo-entrega').val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $('#eliminar-archivo-correccion').on('click', function () {
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
                $('#archivo-correccion').val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $(document).on('click', '#add-correccion-btn', function () {
        $('#add-correccion-form').find('.is-invalid').removeClass('is-invalid');
        $('#add-correccion-form').find('.invalid-feedback').remove();
    })

    $('#save-entrega-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la entrega?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                saveEntrega(id);
            }
        })
    });

    $('#save-correccion-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la correción/comentario?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                saveCorreccion(id);
            }
        })
    });

    function saveEntrega(id) {
        const formData = new FormData(document.getElementById('add-entrega-form'));
        var url = "{{route('anteproyectos_tesis.save_entrega', ":id")}}"
        url = url.replace(':id', id);
        $('#add-entrega-form').find('.is-invalid').removeClass('is-invalid');
        $('#add-entrega-form').find('.invalid-feedback').remove();
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
            $('#archivo').val('');
            $('#addEntregaModal').modal('hide');
            if (response.mensaje) {
                window.localStorage.setItem('message', response.mensaje);
            window.localStorage.setItem('type', 'error');
            } else {
                window.localStorage.setItem('message', response.message);
                window.localStorage.setItem('type', 'success');
            }
            var url = "{{route('anteproyectos_tesis.show_entregas', ":id")}}";
            url = url.replace(':id', id);
            window.location.href = url;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'archivo') {
                    var btn = $('#eliminar-archivo-entrega');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    function saveCorreccion(id) {
        const formData = new FormData(document.getElementById('add-correccion-form'));
        var url = "{{route('anteproyectos_tesis.save_correccion', ":id")}}"
        url = url.replace(':id', id);
        $('#add-correccion-form').find('.is-invalid').removeClass('is-invalid');
        $('#add-correccion-form').find('.invalid-feedback').remove();
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
            $('#archivo').val('');
            $('#addCorreccionModal').modal('hide');
            if (response.mensaje) {
                window.localStorage.setItem('message', response.mensaje);
                window.localStorage.setItem('type', 'error');
            } else {
                window.localStorage.setItem('message', response.message);
                window.localStorage.setItem('type', 'success');
            }
            var url = "{{route('anteproyectos_tesis.show_entregas', ":id")}}";
            url = url.replace(':id', id);
            window.location.href = url;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'archivo') {
                    var btn = $('#eliminar-archivo-correccion');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }
</script>
