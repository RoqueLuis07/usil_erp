<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function () {
        new Cleave ('#monto_virtual', formatoSeparadorMiles);
        new Cleave ('#monto_teams', formatoSeparadorMiles);
        new Cleave ('#monto_presencial', formatoSeparadorMiles);

        $.each($('.horas'), function () {
            new Cleave(this, formatoSeparadorMiles);
        })
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    function message(message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        })
        Toast.fire({
            icon: type,
            text: message,
        })
    }

    $('#cancel-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la operación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{route('docentes.index')}}';
            }
        })
    });

    $('#save-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de generar el reporte de horas del docente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    });

    $(document).on('keyup', '.monto', function () {
        calcular_monto_total();
    });

    $(document).on('keyup', '.horas', function () {
        calcular_horas_totales();
        calcular_monto_total();
    })

    function calcular_horas_totales() {
        var valor = 0;
        var horas_virtuales = 0;
        var horas_teams = 0;
        var horas_presenciales = 0;

        $.each($('.horas_virtuales'), function () {
            if ($(this).is(':visible')) {
                valor = parseInt($(this).val().replace(/\./g, ''));
                if (isNaN(valor)) {
                    valor = 0;
                }
                horas_virtuales += parseInt(valor);
            }
        })

        $.each($('.horas_teams'), function () {
            if ($(this).is(':visible')) {
                valor = parseInt($(this).val().replace(/\./g, ''));
                if (isNaN(valor)) {
                    valor = 0;
                }
                horas_teams += parseInt(valor);
            }
        })

        $.each($('.horas_presenciales'), function () {
            if ($(this).is(':visible')) {
                valor = parseInt($(this).val().replace(/\./g, ''));
                if (isNaN(valor)) {
                    valor = 0;
                }
                horas_presenciales += parseInt(valor);
            }
        })

        $('#total_horas_virtuales').val(horas_virtuales);
        $('#total_horas_teams').val(horas_teams);
        $('#total_horas_presenciales').val(horas_presenciales);
    }

    function calcular_monto_total() {
        var horas_virtuales = parseInt($('#total_horas_virtuales').val());
        if (isNaN(horas_virtuales)) {
            horas_virtuales = 0;
        }
        var horas_teams = parseInt($('#total_horas_teams').val());
        if (isNaN(horas_teams)) {
            horas_teams = 0;
        }
        var horas_presenciales = parseInt($('#total_horas_presenciales').val());
        if (isNaN(horas_presenciales)) {
            horas_presenciales = 0;
        }

        var monto_virtuales = parseInt($('#monto_virtual').val().replace(/\./g, '')) * parseInt(horas_virtuales);
        if (isNaN(monto_virtuales)) {
            monto_virtuales = 0;
        }
        var monto_teams = parseInt($('#monto_teams').val().replace(/\./g, '')) * parseInt(horas_teams);
        if (isNaN(monto_teams)) {
            monto_teams = 0;
        }
        var monto_presenciales = parseInt($('#monto_presencial').val().replace(/\./g, '')) * parseInt(horas_presenciales);
        if (isNaN(monto_presenciales)) {
            monto_presenciales = 0;
        }

        var monto_total = parseInt(monto_virtuales) + parseInt(monto_teams) + parseInt(monto_presenciales);

        $('#monto_total').val(Intl.NumberFormat('de-DE').format(parseInt(monto_total)));
    }
</script>
