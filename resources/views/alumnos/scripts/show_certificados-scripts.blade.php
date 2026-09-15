<script type="module">
    $('.semestre').on('click', function () {
        var id = $(this).data('id')

        let suma_carga_horaria = 0;
        $('#semestreTabla-' + id + ' .carga_horaria').each(function () {
            suma_carga_horaria += parseInt($(this).text()) || 0;
        })

        if (suma_carga_horaria > 0) {
            $('#total_carga_horaria-' + id).text(suma_carga_horaria);
        }

        let suma_calificacion = 0;
        let i = 0;
        $('#semestreTabla-' + id + ' .calificacion').each(function () {
            suma_calificacion += parseFloat($(this).text()) || 0;
            i++;
        })
        if (suma_calificacion > 0) {
            let promedio_calificacion = i ? (suma_calificacion / i) : 0;
            promedio_calificacion = promedio_calificacion.toFixed(2).replace('.', ',')

            $('#promedio_calificacion-' + id).text(promedio_calificacion);
        }

    })
</script>
