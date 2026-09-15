<script type="module">
    var solicitudesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'solicitud', 'estado'],
            page: 10,
            pagination: true
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

    $('#add-estado-filter-btn').on('click', function () {
        if ($(this).hasClass('btn-outline-success')) {
            var estado = 'PE';
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-warning');
            $(this).text('Mostrar Todo');
            $(this).val(estado);
            aplicarFiltros();
        } else {
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-outline-success');
            $(this).text('Mostrar Solo Pendientes');
            $(this).val('');
            aplicarFiltros();
        }
    })

    function aplicarFiltros() {
        var estadoSeleccionado = $('#add-estado-filter-btn').val();

        solicitudesList.filter(function (item) {
            var estadoMatch = !estadoSeleccionado || item.values().estado === estadoSeleccionado;

            return estadoMatch;
        })
    }
</script>
