<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            if (input.files[0].type == 'application/pdf') {
                $('#vista-imagen').css('width', '100px');
                $('#vista-imagen').css('height', '100px');
                $('#vista-imagen').prop('src', '{{asset('storage/pdf.png')}}');
            } else {
                reader.onload = function (e) {
                    $('#vista-imagen').prop('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        };
    };
</script>

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

    $('#legajo').on('change', function () {
        $('#eliminar-legajo').prop('disabled', false);
    })

    $('#eliminar-legajo').on('click', function () {
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
                $('#legajo').val('');
                $('#vista-imagen').css('width', '200px');
                $('#vista-imagen').css('height', '200px');
                $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
                $(this).prop('disabled', true);
            }
        });
    });

    $(document).on('click', '#subir-legajo-btn', function () {
        $('#store-alumno-legajo-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-alumno-legajo-form').find('.invalid-feedback').remove();
    })

    $('#cancel-legajo-btn').click(function () {
        var nombre = ($('#alumno').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la subida del archivo al legajo del alumno ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#legajo').val('');
                $('#vista-imagen').css('width', '200px');
                $('#vista-imagen').css('height', '200px');
                $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
                $('#subirLegajoModal').modal('hide');
            }
        })
    });

    $('#save-legajo-btn').click(function () {
        var nombre = ($('#alumno').val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el archivo subido al legajo del alumno ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                saveLegajo(id);
            }
        })
    });

    function saveLegajo(id) {
        const formData = new FormData(document.getElementById('store-alumno-legajo-form'));
        var url = "{{route('alumnos.subir_legajo', ":id")}}"
        url = url.replace(':id', id);
        $('#store-alumno-legajo-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-alumno-legajo-form').find('.invalid-feedback').remove();
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

                if (key == 'legajo') {
                    var btn = $('#eliminar-legajo');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    $('.delete-legajo-btn').on('click', function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        var nombre = $('#alumno').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el ' + tipo + ' del alumno ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-legajo-form-' + id).submit();
            }
        })
    })
</script>
