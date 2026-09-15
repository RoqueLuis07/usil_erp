<script type="module">
    var asistenciasList;
    $(document).ready(function() {
        var options = {
            valueNames: ['materia', 'total_clases', 'horas_desarrollo', 'total_asistido', 'horas_asistencia', 'porcentaje', 'periodo'],
            page: 20,
            pagination: true,
        };
        asistenciasList = new List('asistencias-list', options);
        asistenciasList.on('updated', function(list) {
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

    $('#delete-materia-filter-btn').on('click', function () {
        $('#filtro_materia').selectpicker('val', '');
        $('#filtro_materia').trigger('change');
    })

    $('#delete-periodo-filter-btn').on('click', function () {
        $('#filtro_periodo').selectpicker('val', '');
        $('#filtro_periodo').trigger('change');
    })

    $('#delete-asistencia-filter-btn').on('click', function () {
        $('#filtro_asistencia').selectpicker('val', '');
        $('#filtro_asistencia').trigger('change');
    })

    $('#filtro_materia').change(aplicarFiltros);
    $('#filtro_periodo').change(aplicarFiltros);
    $('#filtro_asistencia').change(aplicarFiltros);

    function aplicarFiltros() {
        var materiaSeleccionado = $('#filtro_materia option:selected').val();
        var periodoSeleccionado = $('#filtro_periodo option:selected').val();
        var asistenciaSeleccionado = $('#filtro_asistencia option:selected').val();

        var asistenciaMatch;

        asistenciaMatch = function(item) {
            return true;
        };

        if (asistenciaSeleccionado) {
            asistenciaSeleccionado = parseInt(asistenciaSeleccionado);

            var rango_min = 0;
            var rango_max = 0;

            if (asistenciaSeleccionado == 10) {
                rango_min = 0;
                rango_max = 10;
            } else if (asistenciaSeleccionado == 20) {
                rango_min = 11;
                rango_max = 20;
            } else if (asistenciaSeleccionado == 30) {
                rango_min = 21;
                rango_max = 30;
            } else if (asistenciaSeleccionado == 40) {
                rango_min = 31;
                rango_max = 40;
            } else if (asistenciaSeleccionado == 50) {
                rango_min = 41;
                rango_max = 50;
            } else if (asistenciaSeleccionado == 60) {
                rango_min = 51;
                rango_max = 60;
            } else if (asistenciaSeleccionado == 70) {
                rango_min = 61;
                rango_max = 70;
            } else if (asistenciaSeleccionado == 80) {
                rango_min = 71;
                rango_max = 80;
            } else if (asistenciaSeleccionado == 90) {
                rango_min = 81;
                rango_max = 90;
            } else if (asistenciaSeleccionado == 100) {
                rango_min = 91;
                rango_max = 100;
            }

            asistenciaMatch = function(item) {
                return item.values().porcentaje >= rango_min && item.values().porcentaje <= rango_max;
            };
        }

        asistenciasList.filter(function (item) {
            var materiaMatch = !materiaSeleccionado || item.values().materia === materiaSeleccionado;
            var periodoMatch = !periodoSeleccionado || item.values().periodo === periodoSeleccionado;

            return periodoMatch && materiaMatch && asistenciaMatch(item);
        })
    }
</script>
