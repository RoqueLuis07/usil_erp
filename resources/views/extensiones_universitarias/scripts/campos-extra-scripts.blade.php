{{-- Comportamiento de los campos de ficha compartidos por crear y editar:
     línea/problemática/tope según el tipo de actividad, período según la fecha de inicio y formato numérico. --}}
@php
    $tiposInfo = $tipos_extensiones->mapWithKeys(function ($t) {
        return [$t->id => [
            'nombre' => $t->nombre,
            'linea' => $t->linea_numero,
            'tope' => (float) $t->maxima_cantidad_horas,
        ]];
    });
@endphp
<script type="module">
    const tiposInfo = @json($tiposInfo);

    // Al cambiar el tipo se sugiere la problemática del mismo número que su
    // línea (tipo 1 -> problemática 1); sigue siendo editable a mano.
    function actualizarTipo(sugerirProblematica) {
        const info = tiposInfo[$('#tipo_extension').val()];
        if (!info) {
            $('#linea_extension').val('');
            $('#tope-horas').text('');
            return;
        }
        $('#linea_extension').val(info.linea ? 'Línea ' + info.linea + ' · ' + info.nombre : 'Sin línea asignada');
        $('#tope-horas').text('Tope acreditable por alumno en este tipo: ' + info.tope.toLocaleString('es-PY') + ' horas.');
        if (sugerirProblematica && info.linea) {
            $('#problematica').selectpicker('val', String(info.linea));
        }
    }

    // Período académico: enero-julio = 1, agosto-diciembre = 2 (igual que los reportes).
    function actualizarPeriodo() {
        const coincidencia = /^(\d{4})-(\d{2})-/.exec($('#fecha_inicio').val() || '');
        $('#periodo_actividad').val(coincidencia ? coincidencia[1] + '-' + (parseInt(coincidencia[2], 10) <= 7 ? 1 : 2) : '');
    }

    $(document).ready(function () {
        const enteros = {
            numeral: true,
            numeralDecimalMark: ',',
            numeralDecimalScale: 0,
            numeralPositiveOnly: true,
            delimiter: '.',
            swapHiddenInput: true,
        };
        new Cleave('#presupuesto', enteros);
        new Cleave('#cantidad_beneficiados', enteros);

        actualizarTipo(false);
        actualizarPeriodo();
    });

    $('#tipo_extension').on('change', function () {
        actualizarTipo(true);
    });
    $(document).on('change input', '#fecha_inicio', actualizarPeriodo);
</script>
