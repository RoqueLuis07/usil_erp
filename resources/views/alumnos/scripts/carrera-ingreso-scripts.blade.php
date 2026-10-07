<script>
    // Facultad y semestre actual del alumno, calculados en pantalla con la
    // misma regla que Alumno::getSemestreActualAttribute() (enero-julio = 1.er
    // período, agosto-diciembre = 2.º, tope en la duración de la carrera).
    $(function () {
        function recalcularAcademico() {
            var opcion = $('#carrera option:selected');
            $('#facultad_carrera').val(opcion.data('facultad') || '');

            var anho = parseInt($('#anho_ingreso').val(), 10);
            if (!anho) {
                $('#semestre_calculado').val('');
                return;
            }
            var hoy = new Date();
            var periodoActual = (hoy.getMonth() + 1) <= 7 ? 1 : 2;
            var periodoIngreso = parseInt($('#semestre_ingreso').val(), 10) || 1;
            var nivel = ((hoy.getFullYear() - anho) * 2) + (periodoActual - periodoIngreso) + 1;
            if (nivel < 1) {
                $('#semestre_calculado').val('');
                return;
            }
            var duracion = parseInt(opcion.data('duracion'), 10);
            if (duracion) {
                nivel = Math.min(nivel, duracion);
            }
            $('#semestre_calculado').val(nivel + '.º semestre');
        }

        $('#carrera').on('changed.bs.select change', recalcularAcademico);
        $('#anho_ingreso, #semestre_ingreso').on('input change', recalcularAcademico);
    });
</script>
