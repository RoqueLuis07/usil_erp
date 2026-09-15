<script type="module">
    var clasesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'modulo', 'docente', 'modalidad'],
            page: 50,
            pagination: true
        };
        clasesList = new List('clases-list', options);
        clasesList.on('updated', function(list) {
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

    $(document).on('click', '#add-btn', function () {
        var curso = $('#curso_id').val();
        var modulo = $('#modulo option:selected').val();

        var url = "{{route('clases_maestrias.create', ['curso' => ":curso", 'modulo' => ":modulo"])}}";
        url = url.replace(':curso', curso);
        url = url.replace(':modulo', modulo);

        window.location.href = url;
    })

    $('#delete-modulo-filter-btn').on('click', function () {
        $('#filtro_modulo').selectpicker('val', '');
        $('#filtro_modulo').trigger('change');
    })

    $('#delete-docente-filter-btn').on('click', function () {
        $('#filtro_docente').selectpicker('val', '');
        $('#filtro_docente').trigger('change');
    })

    $('#filtro_modulo').change(aplicarFiltros);
    $('#filtro_docente').change(aplicarFiltros);

    function aplicarFiltros() {
        var moduloSeleccionado = $('#filtro_modulo option:selected').val();
        var docenteSeleccionado = $('#filtro_docente option:selected').val();


        clasesList.filter(function (item) {
            var moduloMatch = !moduloSeleccionado || item.values().modulo === moduloSeleccionado;
            var docenteMatch = !docenteSeleccionado || item.values().docente === docenteSeleccionado;

            return moduloMatch && docenteMatch;
        })
    }
</script>
