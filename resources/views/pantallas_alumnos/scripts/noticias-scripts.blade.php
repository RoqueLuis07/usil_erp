<script type="module">
    var noticiasList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'titulo'],
            page: 10,
            pagination: true
        };
        noticiasList = new List('noticias-list', options);
        noticiasList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
    });
</script>
