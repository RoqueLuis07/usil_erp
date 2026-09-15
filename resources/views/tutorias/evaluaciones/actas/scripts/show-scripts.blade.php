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

    $('#generar-acta-btn').click(function () {
        var materia = $('#materia').val();
        var carrera = $('#carrera').val();
        var semestre = $('#semestre').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de generar el acta de la materia ' + materia + ' de la carrera ' + carrera + ' del semestre ' + semestre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                window.open(url, '_blank');
                setTimeout(function () {
                    window.location.reload();
                }, 3000);
            }
        })
    });

    $('#acta').on('change', function () {
        $('#eliminar-acta').prop('disabled', false);
    })

    $('#eliminar-acta').on('click', function () {
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
            $('#acta').val('');
            $('#vista-imagen').css('width', '200px');
            $('#vista-imagen').css('height', '200px');
            $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
            $(this).prop('disabled', true);
        }
        });
    });

    $(document).on('click', '#subir-acta-btn', function () {
        $('#store-acta-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-acta-form').find('.invalid-feedback').remove();
    })

    $('#cancel-acta-btn').click(function () {
        var numero = $('#numero_acta').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la subida del archivo al acta N° ' + numero + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#acta').val('');
                $('#vista-imagen').css('width', '200px');
                $('#vista-imagen').css('height', '200px');
                $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
                $('#subirActaModal').modal('hide');
            }
        })
    });

    $('#save-acta-btn').click(function () {
        var numero = $('#numero_acta').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el archivo subido al acta N° ' + numero + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                saveActa(id, url);
            }
        })
    });

    function saveActa(id, url) {
        const formData = new FormData(document.getElementById('store-acta-form'));
        $('#store-acta-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-acta-form').find('.invalid-feedback').remove();
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
            $('#acta').val('');
            $('#vista-imagen').css('width', '200px');
            $('#vista-imagen').css('height', '200px');
            $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', type);
            var url = "{{route('tutorias_evaluaciones.show_acta', ":id")}}"
            url = url.replace(':id', id);
            window.location.href = url;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'acta') {
                    var btn = $('#eliminar-acta');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    $('#eliminar-adjunto-acta-btn').click(function () {
        var numero = $('#numero_acta').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el archivo subido al acta N° ' + numero + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#eliminar-acta-form').submit();
            }
        })
    });
</script>
