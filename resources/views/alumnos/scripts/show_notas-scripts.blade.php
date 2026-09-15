<script type="module">
    $(document).ready(function() {
        var options = {
            valueNames: ['semestre_materia', 'materia', 'calificacion', 'evaluacion'],
            page: 100,
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
</script>
