<script>
    function readURL(input) {
        var id = $(input).data('id');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            if (input.files[0].type == 'application/pdf') {
                $('#vista-imagen-' + id).css('width', '100px');
                $('#vista-imagen-' + id).css('height', '100px');
                $('#vista-imagen-' + id).prop('src', '{{asset('storage/pdf.png')}}');
            } else {
                reader.onload = function (e) {
                    $('#vista-imagen-' + id).prop('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        };
    };
</script>

<script type="module">
    var extensionesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['evento', 'responsable'],
            page: 10,
            pagination: true
        };
        extensionesList = new List('extensiones-list', options);
        extensionesList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });

        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
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

    $('.adjunto').on('change', function () {
        var id = $(this).data('id');
        $('#eliminar-adjunto-' + id).prop('disabled', false);
    })

    $('.eliminar-adjunto').on('click', function () {
        var id = $(this).data('id');
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
                $('#adjunto-' + id).val('');
                $('#vista-imagen-' + id).css('width', '200px');
                $('#vista-imagen-' + id).css('height', '200px');
                $('#vista-imagen-' + id).prop('src', '{{asset('storage/no_image.png')}}');
                $(this).prop('disabled', true);
            }
        });
    });

    $(document).on('click', '.subir-adjunto-btn', function () {
        var id = $(this).data('id');
        $('#store-adjunto-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#store-adjunto-form-' + id).find('.invalid-feedback').remove();
    })

    $('.cancel-adjunto-btn').click(function () {
        var id = $(this).data('id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la subida del archivo al adjunto de la extensión universitaria?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#adjunto-' + id).val('');
                $('#vista-imagen-' + id).css('width', '200px');
                $('#vista-imagen-' + id).css('height', '200px');
                $('#vista-imagen-' + id).prop('src', '{{asset('storage/no_image.png')}}');
                $('#subirAdjuntoModal-' + id).modal('hide');
            }
        })
    });

    $('.save-adjunto-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el archivo subido al adjunto de la extensión universitaria?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                // $('#store-adjunto-form-' + id).submit();
                save_adjunto(id);
            }
        })
    });

    function save_adjunto(id) {
        const formData = new FormData(document.getElementById('store-adjunto-form-' + id));
        var url = "{{route('pantallas_alumnos.adjuntar_certificado_extensiones_universitarias', ":id")}}"
        url = url.replace(':id', id);
        $('#store-adjunto-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#store-adjunto-form-' + id).find('.invalid-feedback').remove();
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

                if (key == 'adjunto') {
                    var btn = $('#eliminar-adjunto-' + id);
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    $('.delete-adjunto-btn').on('click', function () {
        var id = $(this).data('id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el adjunto de la extensión universitaria?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-adjunto-form-' + id).submit();
            }
        })
    })
</script>
