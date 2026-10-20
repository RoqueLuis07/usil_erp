{{-- Confirmación antes de quitar a un alumno de la actividad. --}}
<script type="module">
    $(document).on('submit', '.form-quitar-alumno', function (e) {
        if (this.dataset.confirmado) {
            return;
        }
        e.preventDefault();
        const formulario = this;
        Swal.fire({
            title: '¿Quitar a ' + formulario.dataset.nombre + ' de la actividad?',
            text: 'Se elimina su participación y las horas asignadas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            customClass: {confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-light'},
            buttonsStyling: false,
        }).then(function (resultado) {
            if (resultado.isConfirmed) {
                formulario.dataset.confirmado = '1';
                formulario.submit();
            }
        });
    });
</script>
