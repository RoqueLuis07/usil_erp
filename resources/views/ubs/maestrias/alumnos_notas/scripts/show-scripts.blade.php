<script type="module">
    $(document).ready(function() {
        var options = {
            valueNames: ['alumno', 'numero_documento'],
            page: 10,
            pagination: true
        };
        var notasList = new List('notas-list', options);
        notasList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
    });

    if (document.querySelector(".pagination-next"))
    document.querySelector(".pagination-next").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : '' : '';
    });

    if (document.querySelector(".pagination-prev"))
    document.querySelector(".pagination-prev").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : '' : '';
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

    $(document).on('click', '.cargar-btn', function () {
        var curso = $(this).data('id');
        var modulo = $('#modulo option:selected').val();
        var evaluacion = $('#evaluacion option:selected').val();
        var url = "{{route('alumnos_notas_ubs.create', ['curso' => ":curso", 'modulo' => ":modulo", 'evaluacion' => ":evaluacion"])}}";
        url = url.replace(':curso', curso);
        url = url.replace(':modulo', modulo);
        url = url.replace(':evaluacion', evaluacion);

        window.location.href = url;
    })

    $(document).on('change', '#modulo', function () {
        var curso = $('#curso_id').val();
        var modulo = $(this).val();
        var url = "{{route('alumnos_notas_ubs.get_evaluaciones', ['curso' => ":curso", 'modulo' => ":modulo"])}}";
        url = url.replace(':curso', curso);
        url = url.replace(':modulo', modulo);
        get_evaluaciones(url);
    })

    function get_evaluaciones(url) {
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
            if (response.message) {
                $('#modulo').selectpicker('val', '');
                $('#evaluacion').selectpicker('destroy');
                $('#evaluacion').empty();
                $('#evaluacion').prop('disabled', true);
                $('#evaluacion').append('<option value="" selected disabled>Seleccionar...</option>');
                $('#evaluacion').selectpicker('render');

                message(response.message, 'error');
            } else {
                $('#evaluacion').selectpicker('destroy');
                $('#evaluacion').empty();
                $('#evaluacion').append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(response.evaluaciones, function (index, value) {
                    $('#evaluacion').append('<option value="' + value.id + '">' + value.nombre + '</option>')
                })
                $('#evaluacion').prop('disabled', false);
                $('#evaluacion').selectpicker('render');
            }
        })
    }
</script>
