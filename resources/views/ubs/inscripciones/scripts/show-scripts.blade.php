<script type="module">
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

    $('#save-btn').click(function () {
        const nombre = $('#alumno').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de aplicar el convenio a la inscripción del alumno ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, aplicar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                saveConvenio(url);
            }
        })
    });

    function saveConvenio(url) {
        const formData = new FormData(document.getElementById('agregar-convenio-form'));
        $('#agregar-convenio-form').find('.is-invalid').removeClass('is-invalid');
        $('#agregar-convenio-form').find('.invalid-feedback').remove();
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
            if (response.error) {
                type = 'error';
                window.localStorage.setItem('message', response.error);
                window.localStorage.setItem('type', type);
                window.location.reload();
            } else {
                window.localStorage.setItem('message', response.message);
                window.localStorage.setItem('type', type);
                window.location.reload();
            }
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }
</script>
