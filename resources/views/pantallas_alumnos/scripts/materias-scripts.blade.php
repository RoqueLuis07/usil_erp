<script type="module">
    var materiasList;
    $(document).ready(function() {
        var options = {
            valueNames: ['materia'],
            page: 10,
            pagination: true
        };
        materiasList = new List('materias-list', options);
        materiasList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
    });
</script>
