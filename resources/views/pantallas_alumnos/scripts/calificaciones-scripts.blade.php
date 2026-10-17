<script type="module">
    var notasNacionalesList;
    $(document).ready(function() {
        var options_nacional = {
            valueNames: ['materia_nacional', 'tipo_nacional', 'fecha_nacional', 'periodo_nacional'],
            page: 10,
            pagination: true
        };
        notasNacionalesList = new List('notas_nacionales-list', options_nacional);
        notasNacionalesList.on('updated', function(list) {
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

    $('#add-periodo-nacional-filter-btn').on('click', function () {
        if ($(this).hasClass('btn-outline-success')) {
            var periodo = {!!json_encode($periodo_activo, JSON_HEX_TAG) !!}
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-warning');
            $(this).text('Mostrar Todos los Periodos');
            $(this).val(periodo);
            aplicarFiltrosNacionales();
        } else {
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-outline-success');
            $(this).text('Mostrar Periodo Activo');
            $(this).val('');
            aplicarFiltrosNacionales();
        }
    })

    $('#delete-evaluacion-nacional-filter-btn').on('click', function () {
        $('#filtro_evaluacion_nacional').selectpicker('val', '');
        $('#filtro_evaluacion_nacional').trigger('change');
    })

    $('#filtro_evaluacion_nacional').change(aplicarFiltrosNacionales);




    function aplicarFiltrosNacionales() {
        var periodoNacionalSeleccionado = $('#add-periodo-nacional-filter-btn').val();
        var evaluacionNacionalSeleccionado = $('#filtro_evaluacion_nacional option:selected').val();


        notasNacionalesList.filter(function (item) {
            var periodoNacionalMatch = !periodoNacionalSeleccionado || item.values().periodo_nacional === periodoNacionalSeleccionado;
            var evaluacionNacionalMatch = !evaluacionNacionalSeleccionado || item.values().tipo_nacional === evaluacionNacionalSeleccionado;

            return periodoNacionalMatch && evaluacionNacionalMatch;
        })
    }

</script>
