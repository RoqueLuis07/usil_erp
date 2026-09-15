<script type="module">
    $(document).ready(function () {
        var docente_nacionalidades = {!!json_encode($docente->nacionalidades, JSON_HEX_TAG) !!}
        var nacionalidades = [];
        $.each(docente_nacionalidades, function (index, value) {
                nacionalidades.push(value.nombre)
            $('#nacionalidad').val(nacionalidades);
        })
    })
</script>
