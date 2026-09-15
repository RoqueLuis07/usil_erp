<script type="module">
    $(document).ready(function () {
        $('.ausente-presente-btn').on('click', function () {
            var id = $(this).data('id');
            if ($('#asistencia-' + id).val() == 'AU') {
                $(this).removeClass('btn-outline-danger');
                $(this).removeClass('btn-outline-success');
                $(this).addClass('btn-success');
                $('#asistencia-' + id).val('PR');
                $('.ausente-presente-btn-text-' + id).html('Presente');
            } else {
                $(this).removeClass('btn-success');
                $(this).addClass('btn-outline-danger');
                $('#asistencia-' + id).val('AU');
                $('.ausente-presente-btn-text-' + id).html('Ausente');
            }
        })

        $('.ausente-presente-btn').mouseleave( function () {
            if (!$(this).hasClass('btn-success')) {
                $(this).removeClass('btn-outline-success').addClass('btn-outline-danger');
            }
        })
    })

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
                window.location.href = '{{route('pantallas_docentes.index', Auth::id())}}';
            }
        })
    });

    $('#save-btn').click(function () {
        var materia = $('#materia').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar las asistencias de la materia ' + materia + '?',
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
</script>
