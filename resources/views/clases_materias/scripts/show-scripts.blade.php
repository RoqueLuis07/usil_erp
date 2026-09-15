<script type="module">
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#observaciones-clase'), {
                toolbar: [],
            })
            .then(editor => {
                editor.enableReadOnlyMode('my-feature-id');
            })
            .catch(error => {
                console.error(error);
            });

        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    });

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

    $('.change-asistencia').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-warning me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cambiar la asistencia seleccionada?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                change_asistencia(id, url);
            }
        })
    })

    $('.change-observacion').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-warning me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cambiar la observación de la asistencia seleccionada?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                change_observacion(id, url);
            }
        })
    })

    function change_observacion(id, url) {
        const formData = new FormData(document.getElementById('change-observacion-form-' + id));
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
            var url_nuevo = "{{route('clases_materias.show', ":id")}}"
            var clase_id = $('#clase_id').val();
            url_nuevo = url_nuevo.replace(':id', clase_id);
            window.location.href = url_nuevo;
        }).fail(function(response) {

        })
    }

    function change_asistencia(id, url) {
        const formData = new FormData(document.getElementById('change-asistencia-form-' + id));
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
            var url_nuevo = "{{route('clases_materias.show', ":id")}}"
            var clase_id = $('#clase_id').val();
            url_nuevo = url_nuevo.replace(':id', clase_id);
            window.location.href = url_nuevo;
        }).fail(function(response) {

        })
    }

    $('.btn-asistencia').on('click', function () {
        var id = $(this).data('id');
        $('#estado-' + id).val($(this).val());
        $('.btn-asistencia').prop('checked', false);
        $(this).prop('checked', true);
    })
</script>
