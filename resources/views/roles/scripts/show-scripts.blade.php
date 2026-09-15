<script type="module">
    $(document).ready(function() {
        mostrarValores();
    });

    function mostrarValores() {
        //Para marcar los permisos activos en el rol
        const permisos = {!! json_encode($permisos) !!};
        for (let index = 0; index < permisos.length; index++) {
            $('input[name="permiso[' + permisos[index].id + ']"]').prop('checked', true);
        }
    };
</script>
