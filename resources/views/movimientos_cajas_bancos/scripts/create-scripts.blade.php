<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorDecimales = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 2,
    };

    $(document).ready(function () {
        var monto = new Cleave ('#monto', formatoSeparadorDecimales);
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

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
                window.location.href = '{{route('movimientos_cajas_bancos.index')}}';
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
            title: '¿Está seguro de guardar el movimiento entre caja y banco?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    })

    $('#tipo_movimiento').on('change', function () {
        var tipo = parseInt($(this).val());
        if (tipo == 7) {
            $('#sentido').val('R');
            $('#icon-sentido').html('');
            $('#icon-sentido').html('<i class="ri-arrow-right-line"></i>');
            $('#caja').selectpicker('val', '');
            $('#cuenta_bancaria').selectpicker('val', '');
            $('#moneda').val('');
        } else {
            $('#sentido').val('I');
            $('#icon-sentido').html('');
            $('#icon-sentido').html('<i class="ri-arrow-left-line"></i>');
            $('#caja').selectpicker('val', '');
            $('#cuenta_bancaria').selectpicker('val', '');
            $('#moneda').val('');
        }
    })

    $('#caja').on('change', function () {
        if ($('#tipo_movimiento option:selected').val() == 7) {
            $('#moneda').val('GS');
        }
    })

    $('#cuenta_bancaria').on('change', function () {
        if ($('#tipo_movimiento option:selected').val() == 8) {
            var cuenta_bancaria = parseInt($(this).val());
            var cuentas_bancarias = {!! json_encode($cuentas_bancarias, JSON_HEX_TAG) !!};
            $.each(cuentas_bancarias, function (index, value) {
                if (value.id == cuenta_bancaria) {
                    $('#moneda').val(value.moneda.codigo);
                }
            })
        }
    })
</script>
