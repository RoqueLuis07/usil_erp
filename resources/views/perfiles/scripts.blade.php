<script type="module">
    $(document).ready(function () {
        if (document.cookie.includes('avatar=true')) {
            message('Su imagen de perfil fue actualizada correctamente');
            document.cookie = 'avatar' + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
        } if (document.cookie.includes('portada=true')) {
            message('Su imagen de portada fue actualizada correctamente');
            document.cookie = 'portada' + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
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

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $(document).on('click', '#add-btn', function () {
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
    })

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

    $('#update-info-btn').click(function () {
        var nombre = $('#name').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar su información?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                update(id);
            }
        })
    })


    $('#update-password-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar su contraseña?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                change_password(id);
            }
        })
    })

    $('#update-customizer-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar su diseño de vista?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                customizer(id);
            }
        })
    })

    $('#reset-customizer-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de restablecer su diseño de vista?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, restablecer!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#tipo_customizer').val('reset_customizer');
                var id = $(this).data('id');
                reset_customizer(id);
            }
        })
    })

    function message(message) {
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
            text: message,
        })
    }

    function update(id) {
        const formData = new FormData(document.getElementById('update-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
        $('#update-form').find('.is-invalid').removeClass('is-invalid');
        $('#update-form').find('.invalid-feedback').remove();
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
            message(response.message);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    function change_password(id) {
        const formData = new FormData(document.getElementById('change-password-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
        $('#change-password-form').find('.is-invalid').removeClass('is-invalid');
        $('#change-password-form').find('.invalid-feedback').remove();
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
            $('#change-password-form').trigger('reset');
            message(response.message);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    function customizer(id) {
        const formData = new FormData(document.getElementById('customizer-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
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
            message(response.message);
        }).fail(function(response) {

        })
    }

    function reset_customizer(id) {
        const formData = new FormData(document.getElementById('customizer-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
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
            message(response.message);
        }).fail(function(response) {

        })
    }

    $('#profile-img-file-input').change(function () {
        var id = $(this).data('id');
        const formData = new FormData(document.getElementById('update-avatar-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            data: formData,
            enctype: 'multipart/form-data',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            document.cookie = 'avatar=true';
            window.location.reload(true);
        }).fail(function(response) {

        })
    })

    $('#profile-foreground-img-file-input').change(function () {
        var id = $(this).data('id');
        const formData = new FormData(document.getElementById('update-portada-form'));
        var url = "{{route('perfiles.update', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            data: formData,
            enctype: 'multipart/form-data',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            document.cookie = 'portada=true';
            window.location.reload(true);
        }).fail(function(response) {

        })
    })
</script>
