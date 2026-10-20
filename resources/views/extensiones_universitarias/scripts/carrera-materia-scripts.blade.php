{{-- Lista dependiente: las materias se acotan a las carreras elegidas y se muestra a qué facultad pertenecen. --}}
<script>
    $(function () {
        var $carreras = $('#carreras_habilitadas');
        var $materia = $('#materia_id');
        if (!$carreras.length || !$materia.length) {
            return;
        }

        function aplicar(reiniciar) {
            var elegidas = ($carreras.val() || []).map(String);

            // Facultad(es) de las carreras elegidas
            var facultades = [];
            $carreras.find('option:selected').each(function () {
                var f = $(this).parent('optgroup').attr('label');
                if (f && facultades.indexOf(f) === -1) { facultades.push(f); }
            });
            $('#facultades-elegidas').text(facultades.length ? facultades.join(', ') : 'todas');

            // Materias: se ven las que pertenecen a alguna carrera elegida o no tienen carrera asignada
            $materia.find('option').each(function () {
                var propias = String($(this).data('carreras') || '').split(',').filter(Boolean);
                var visible = !this.value || !elegidas.length || !propias.length || propias.some(function (c) { return elegidas.indexOf(c) !== -1; });
                $(this).prop('hidden', !visible).prop('disabled', !visible);
            });
            if ($materia.find('option:selected').is(':disabled')) {
                $materia.val('');
                if (reiniciar) {
                    Swal.fire({icon: 'info', title: 'Materia quitada', text: 'La materia elegida no pertenece a las carreras seleccionadas.', timer: 3500, showConfirmButton: false});
                }
            }
            $materia.selectpicker('refresh');
        }

        $carreras.on('changed.bs.select', function () { aplicar(true); });
        aplicar(false);
    });
</script>
