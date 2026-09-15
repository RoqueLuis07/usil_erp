<script type="module">
    $(document).ready(function () {
        var alumno_nacionalidades = {!!json_encode($alumno->nacionalidades, JSON_HEX_TAG) !!}
        var nacionalidades = [];
        $.each(alumno_nacionalidades, function (index, value) {
                nacionalidades.push(value.nombre)
            $('#nacionalidad').val(nacionalidades);
        })
    })
</script>
