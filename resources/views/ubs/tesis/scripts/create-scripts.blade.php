<script type="module">
    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
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

    $('#cancel-btn').click(function () {
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
                window.location.href = '{{route('tesis_ubs.index')}}';
            }
        })
    });

    $('#save-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la inscripción de la tesis?',
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

    $('#alumno').on('change', function () {
        var alumno = $(this).val();
        var url = "{{route('tesis_ubs.get_maestrias', ":alumno")}}"
        url = url.replace(':alumno', alumno);
        get_maestrias(url);
    })

    function get_maestrias(url) {
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            if (!response.message) {
                $('#maestria').selectpicker('destroy');
                $('#maestria').prop('disabled', false);
                $('#maestria').empty();
                if (response.maestrias.length > 1) {
                    $.each(response.maestrias, function (index, value) {
                        $('#maestria').append('<option value="" selected disabled>Seleccionar...</option>');
                        $('#maestria').append('<option value="' + value.id + '" data-subtext="' + value.llamado + '° LLAMADO">' + value.nombre_fantasia + '</option>');
                    })
                } else {
                    $.each(response.maestrias, function (index, value) {
                        $('#maestria').append('<option value="" disabled>Seleccionar...</option>');
                        $('#maestria').append('<option value="' + value.id + '" data-subtext="' + value.llamado + '° LLAMADO" selected>' + value.nombre_fantasia + '</option>');
                    })
                }
                $('#maestria').selectpicker('render');
            } else {
                message(response.message, 'error');
                $('#alumno').selectpicker('val', '');
            }
        })
    }
</script>
