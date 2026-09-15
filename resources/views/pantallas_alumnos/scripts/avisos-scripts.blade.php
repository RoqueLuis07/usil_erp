<script type="module">
    var avisosList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'titulo'],
            page: 10,
            pagination: true
        };
        avisosList = new List('avisos-list', options);
        avisosList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
    });
</script>
