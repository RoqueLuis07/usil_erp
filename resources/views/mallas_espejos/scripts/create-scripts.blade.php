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

    $('.btn-check').on('click', function () {
        $('.btn-group').removeClass('is-invalid');
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
                window.location.href = '{{route('mallas_espejos.index')}}';
            }
        })
    });

    $('#clean-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de vaciar todos los campos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('form :input').val('');
                $('.selectpicker').selectpicker('val', '');
            }
        })
    })

    $('#save-btn').click(function () {
        var paraguay = $('#malla_paraguay option:selected').text();
        var siu = $('#malla_siu option:selected').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la malla espejo ' + paraguay + ' - ' + siu + '?',
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

    $('#malla_paraguay').on('change', function () {
        var materia = $(this).val();
        var url = "{{route('mallas_espejos.get_materias', ":materia")}}";
        url = url.replace(':materia', materia);
        get_materias(url, 'paraguay');
    })

    $('#malla_siu').on('change', function () {
        var materia = $(this).val();
        var url = "{{route('mallas_espejos.get_materias', ":materia")}}";
        url = url.replace(':materia', materia);
        get_materias(url, 'siu');
    })

    function get_materias(url, tipo) {
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
            $('#materia_' + tipo + '-0').selectpicker('destroy');
            $('#materia_' + tipo + '-0').empty();
            $('#materia_' + tipo + '-0').append('<option value="" selected disabled>Seleccionar...</option>')
            $.each(response.materias, function (index, value) {
                $('#materia_' + tipo + '-0').append('<option value="' + value.id + '">' + value.nombre_fantasia + '</option>')
            })
            $('#materia_' + tipo + '-0').prop('disabled', false);
            $('#materia_' + tipo + '-0').selectpicker('render');
        })
    }
</script>
