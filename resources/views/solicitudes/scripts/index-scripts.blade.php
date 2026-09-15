<script type="module">
    var solicitudesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'alumno', 'tipo', 'programa', 'semestre', 'tipo_generacion'],
            page: 50,
            pagination: true,
        };
        solicitudesList = new List('solicitudes-list', options);
        solicitudesList.on('updated', function(list) {
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

    $('#delete-tipo-filter-btn').on('click', function () {
        $('#filtro_tipo').selectpicker('val', '');
        $('#filtro_tipo').trigger('change');
    })

    $('#filtro_tipo').change(aplicarFiltros);

    function aplicarFiltros() {
        var tipoSeleccionado = $('#filtro_tipo option:selected').val();

        solicitudesList.filter(function (item) {
            var tipoMatch = !tipoSeleccionado || item.values().tipo_generacion === tipoSeleccionado;

            return tipoMatch;
        })
    }
</script>
