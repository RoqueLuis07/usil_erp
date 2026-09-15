<script type="module">
    $('.light-mode-btn').click(function () {
        console.log('hola')
        var id = $(this).data('id');
        const formData = new FormData(document.getElementById('light-mode-form'));
        var url = "{{route('configuraciones.light_mode', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.location.reload(true)
        }).fail(function(response) {

        })
    })

    $('.dark-mode-btn').click(function () {
        console.log('hola')
        var id = $(this).data('id');
        const formData = new FormData(document.getElementById('dark-mode-form'));
        var url = "{{route('configuraciones.dark_mode', ":id")}}"
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.location.reload(true)
        }).fail(function(response) {

        })
    })
</script>
