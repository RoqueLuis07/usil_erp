<script type="module">
    var puntajesNacionalesList;
    var puntajesSiuList;
    $(document).ready(function() {
        var options_nacional = {
            valueNames: ['materia_nacional', 'tipo_nacional', 'fecha_nacional', 'periodo_nacional'],
            page: 10,
            pagination: true
        };
        puntajesNacionalesList = new List('puntajes_nacionales-list', options_nacional);
        puntajesNacionalesList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });

        var options_siu = {
            valueNames: ['materia_siu', 'tipo_siu', 'fecha_siu', 'periodo_siu'],
            page: 10,
            pagination: true
        };
        puntajesSiuList = new List('puntajes_siu-list', options_siu);
        puntajesSiuList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
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

    $('#add-periodo-siu-filter-btn').on('click', function () {
        if ($(this).hasClass('btn-outline-success')) {
            var periodo = {!!json_encode($periodo_activo, JSON_HEX_TAG) !!}
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-warning');
            $(this).text('Mostrar Todos los Periodos');
            $(this).val(periodo);
            aplicarFiltrosSiu();
        } else {
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-outline-success');
            $(this).text('Mostrar Periodo Activo');
            $(this).val('');
            aplicarFiltrosSiu();
        }
    })

    $('#delete-evaluacion-siu-filter-btn').on('click', function () {
        $('#filtro_evaluacion_siu').selectpicker('val', '');
        $('#filtro_evaluacion_siu').trigger('change');
    })

    $('#filtro_evaluacion_siu').change(aplicarFiltrosSiu);

    function aplicarFiltrosNacionales() {
        var periodoNacionalSeleccionado = $('#add-periodo-nacional-filter-btn').val();
        var evaluacionNacionalSeleccionado = $('#filtro_evaluacion_nacional option:selected').val();


        puntajesNacionalesList.filter(function (item) {
            var periodoNacionalMatch = !periodoNacionalSeleccionado || item.values().periodo_nacional === periodoNacionalSeleccionado;
            var evaluacionNacionalMatch = !evaluacionNacionalSeleccionado || item.values().tipo_nacional === evaluacionNacionalSeleccionado;

            return periodoNacionalMatch && evaluacionNacionalMatch;
        })
    }

    function aplicarFiltrosSiu() {
        var periodoSiuSeleccionado = $('#add-periodo-siu-filter-btn').val();
        var evaluacionSiuSeleccionado = $('#filtro_evaluacion_siu option:selected').val();


        puntajesSiuList.filter(function (item) {
            var periodoSiuMatch = !periodoSiuSeleccionado || item.values().periodo_siu === periodoSiuSeleccionado;
            var evaluacionSiuMatch = !evaluacionSiuSeleccionado || item.values().tipo_siu === evaluacionSiuSeleccionado;

            return periodoSiuMatch && evaluacionSiuMatch;
        })
    }
</script>
