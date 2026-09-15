<script type="module">
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#descripcion'), {
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

    $('#eliminar-portada').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar la portada?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var url = $(this).data('url');
                destroy_portada(id, url);
            }
        })
    })

    function destroy_portada(id, url) {
        $.ajax({
            url: url,
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
            var url_nuevo = "{{route('noticias_avisos.show', ":id")}}"
            url_nuevo = url_nuevo.replace(':id', id);
            window.location.href = url_nuevo;
        })
    }
</script>
