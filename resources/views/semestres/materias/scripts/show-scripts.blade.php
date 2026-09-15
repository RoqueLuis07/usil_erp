<script type="module">
    $(document).ready(function () {
        var mensaje = localStorage.getItem('mensaje');
        var type = localStorage.getItem('type');

        if (mensaje && type) {
            message(mensaje, type);
            localStorage.removeItem('mensaje');
            localStorage.removeItem('type');
        }
    })

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

    $('.abierto').on('click', function () {
        var id = $(this).data('id');
        var nombre_malla = $('#malla').val();
        var nombre_fantasia = $('.materia-' + id).val();
        var nombre_real = $('.materia-' + id).data('title');
        if ($(this).prop('checked')) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-light',
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: '¿Está seguro de activar la materia ' + nombre_fantasia + ' en la carrera' + nombre_malla + '?',
                text: nombre_real,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, activar!',
                cancelButtonText: 'Volver',
            }).then((result) => {
                if (result.isConfirmed) {
                    activate(id);
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    $('#abierto-' + id).prop('checked', false);
                }
            })
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-light',
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: '¿Está seguro de inactivar la materia ' + nombre_fantasia + ' en la carrera' + nombre_malla + '?',
                text: nombre_real,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, inactivar!',
                cancelButtonText: 'Volver',
            }).then((result) => {
                if (result.isConfirmed) {
                    unactivate(id);
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                    $('#abierto-' + id).prop('checked', true);
                }
            })
        }
    })

    function unactivate(id) {
        var url = "{{route('semestres_mallas_materias.unactivate', ":id")}}"
        url = url.replace(':id', id);
        var type = 'error';
        $.ajax({
            url: url,
            // data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            message(response.message, type);
        })
    }

    function activate(id) {
        var url = "{{route('semestres_mallas_materias.activate', ":id")}}"
        url = url.replace(':id', id);
        var type = 'success';
        $.ajax({
            url: url,
            // data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            message(response.message, type);
        })
    }

    $('.update-semestre-malla-materia-btn').click(function () {
        var id = $(this).data('id');
        var nombre = $('#title-materia-' + id).text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar los detalles de la materia ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                update(id, url);
            }
        })
    });

    function update(id, url) {
        const formData = new FormData(document.getElementById('update-semestre-malla-materia-form-' + id));
        var type = 'success';
        $('#update-semestre-malla-materia-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#update-semestre-malla-materia-form-' + id).find('.invalid-feedback').remove();
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
            $('#editSemestreMallaMateriaModal-' + id).modal('hide');
            localStorage.setItem('mensaje', response.message);
            localStorage.setItem('type', type);
            window.location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                if (key == 'moneda') {
                    var div = $('#div-moneda');
                    div.addClass('is-invalid');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(div);
                } else {
                    input.addClass('is-invalid');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }
</script>
