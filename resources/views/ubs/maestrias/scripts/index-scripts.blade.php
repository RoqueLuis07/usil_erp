<script type="module">
    $(document).ready(function() {
        var options = {
            valueNames: ['nombre_fantasia', 'nombre_real', 'tipo_curso', 'fecha_apertura', 'fecha_fin', 'modalidad'],
            page: 50,
            pagination: true
        };
        var maestriasList = new List('maestrias-list', options);
        maestriasList.on('updated', function(list) {
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

    $(document).on('click', '.certificado-btn', function () {
        var maestria = $(this).data('id');
        var url = "{{route('certificados_estudios_ubs.get_alumnos', ":id")}}";
        url = url.replace(':id', maestria);
        get_alumnos(url);
    })

    function get_alumnos(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#alumno').selectpicker('destroy');
            $('#alumno').empty();
            $('#alumno').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.inscripciones, function (index,value) {
                $('#alumno').append('<option value="' + value.alumno_id + '" data-subtext="' + value.alumno.numero_documento + '">' + value.alumno.primer_nombre + ' ' + value.alumno.primer_apellido + '</option>')
            })
            $('#alumno').selectpicker('render');
        })
    }

    $(document).on('click', '.btn-show-certificado', function () {
        var maestria = $(this).data('id');
        var alumno = $('#alumno option:selected').val();
        var url = "{{route('certificados_estudios_ubs.show', ['maestria' => ":maestria", 'alumno' => ":alumno"])}}";
        url = url.replace(':maestria', maestria);
        url = url.replace(':alumno', alumno);
        console.log(url);

        window.location.href = url;
    })
</script>
