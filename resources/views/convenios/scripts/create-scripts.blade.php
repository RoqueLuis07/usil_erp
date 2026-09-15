<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    var descuento_matricula = new Cleave('#descuento_matricula', formatoSeparadorMiles);
    var descuento_contado = new Cleave('#descuento_contado', formatoSeparadorMiles);
    var descuento_cuotas = new Cleave('#descuento_cuotas', formatoSeparadorMiles);
    var descuento_cuota_1 = new Cleave('#descuento_cuota_1', formatoSeparadorMiles);
    var descuento_cuota_2 = new Cleave('#descuento_cuota_2', formatoSeparadorMiles);
    var descuento_cuota_3 = new Cleave('#descuento_cuota_3', formatoSeparadorMiles);
    var descuento_cuota_4 = new Cleave('#descuento_cuota_4', formatoSeparadorMiles);
    var descuento_cuota_5 = new Cleave('#descuento_cuota_5', formatoSeparadorMiles);
    var precio_descuento = new Cleave('#precio_descuento', formatoSeparadorMiles);

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
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

    $(document).ready(function () {
        var errors = {!!json_encode($errors->any(), JSON_HEX_TAG) !!};
        if (errors) {
            var old_tipo = {!!json_encode(old('tipo'), JSON_HEX_TAG) !!};
            if (old_tipo == 'CO') {
                $('#div-convenio').removeClass('d-none');
            } else if (old_tipo == 'DE') {
                $('#div-descuento').removeClass('d-none');
            }
        }
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
                window.location.href = '{{route('convenios.index')}}';
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
            title: '¿Está seguro de guardar el nuevo convenio?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    });

    $('#tipo1').on('click', function () {
        $('#div-convenio').removeClass('d-none');
        $('#div-descuento').addClass('d-none');
        $('#tipo_descuento').selectpicker('val', '');
        $('#porcentaje_descuento').val('');
        precio_descuento.setRawValue('');
        $('#div-precio-descuento').addClass('d-none');
        $('#div-porcentaje-descuento').addClass('d-none');
    })

    $('#tipo2').on('click', function () {
        $('#div-descuento').removeClass('d-none');
        $('#div-convenio').addClass('d-none');

        $('#tipo_matricula').selectpicker('val', '');
        $('#tipo_contado').selectpicker('val', '');
        $('#tipo_cuotas').selectpicker('val', '');
        $('#aplica_a_cuotas').selectpicker('val', '');
        $('#tipo_cuotas').selectpicker('val', '');

        $('#div-descuento-matricula').addClass('d-none');
        $('#div-porcentaje-matricula').addClass('d-none');
        $('#div-descuento-contado').addClass('d-none');
        $('#div-porcentaje-contado').addClass('d-none');
        $('#div-descuento-cuotas').addClass('d-none');
        $('#div-porcentaje-cuotas').addClass('d-none');
        $('#div-descuento-cuota-1').addClass('d-none');
        $('#div-descuento-cuota-2').addClass('d-none');
        $('#div-descuento-cuota-3').addClass('d-none');
        $('#div-descuento-cuota-4').addClass('d-none');
        $('#div-descuento-cuota-5').addClass('d-none');
        $('#div-porcentaje-cuota-1').addClass('d-none');
        $('#div-porcentaje-cuota-2').addClass('d-none');
        $('#div-porcentaje-cuota-3').addClass('d-none');
        $('#div-porcentaje-cuota-4').addClass('d-none');
        $('#div-porcentaje-cuota-5').addClass('d-none');

        $('#porcentaje_matricula').val('');
        descuento_matricula.setRawValue('');
        $('#porcentaje_contado').val('');
        descuento_contado.setRawValue('');
        $('#porcentaje_cuota_1').val('');
        $('#porcentaje_cuota_2').val('');
        $('#porcentaje_cuota_3').val('');
        $('#porcentaje_cuota_4').val('');
        $('#porcentaje_cuota_5').val('');
        descuento_cuota_1.setRawValue('');
        descuento_cuota_2.setRawValue('');
        descuento_cuota_3.setRawValue('');
        descuento_cuota_4.setRawValue('');
        descuento_cuota_5.setRawValue('');
        $('#porcentaje_cuotas').val('');
    })

    $('#tipo_matricula').on('change', function () {
        if ($(this).val() == 'VA') {
            $('#div-descuento-matricula').removeClass('d-none');
            $('#div-porcentaje-matricula').addClass('d-none');
            $('#porcentaje_matricula').val('');            
        } else {
            $('#div-porcentaje-matricula').removeClass('d-none');
            $('#div-descuento-matricula').addClass('d-none');
            descuento_matricula.setRawValue('');
        }
    });

    $('#tipo_contado').on('change', function () {
        if ($(this).val() == 'VA') {
            $('#div-descuento-contado').removeClass('d-none');
            $('#div-porcentaje-contado').addClass('d-none');
            $('#porcentaje_contado').val('');
        } else {
            $('#div-porcentaje-contado').removeClass('d-none');
            $('#div-descuento-contado').addClass('d-none');
            descuento_contado.setRawValue('');
            descuento_cuotas.setRawValue('');
        }
    });

    $('#tipo_cuotas').on('change', function () {
        if ($('#aplica_a_cuotas').val() == '1C') {
            $('#div-descuento-cuotas').addClass('d-none');
            $('#div-porcentaje-cuotas').addClass('d-none');
            $('#div-descuento-cuota-1').addClass('d-none');
            $('#div-descuento-cuota-2').addClass('d-none');
            $('#div-descuento-cuota-3').addClass('d-none');
            $('#div-descuento-cuota-4').addClass('d-none');
            $('#div-descuento-cuota-5').addClass('d-none');
            $('#div-porcentaje-cuota-1').addClass('d-none');
            $('#div-porcentaje-cuota-2').addClass('d-none');
            $('#div-porcentaje-cuota-3').addClass('d-none');
            $('#div-porcentaje-cuota-4').addClass('d-none');
            $('#div-porcentaje-cuota-5').addClass('d-none');
            descuento_cuota_1.setRawValue('');
            descuento_cuota_2.setRawValue('');
            descuento_cuota_3.setRawValue('');
            descuento_cuota_4.setRawValue('');
            descuento_cuota_5.setRawValue('');
            $('#porcentaje_cuota_1').val('');
            $('#porcentaje_cuota_2').val('');
            $('#porcentaje_cuota_3').val('');
            $('#porcentaje_cuota_4').val('');
            $('#porcentaje_cuota_5').val('');
            if ($(this).val() == 'VA') {
                $('#div-descuento-cuota-1').removeClass('d-none');
                $('#div-porcentaje-cuota-1').addClass('d-none');
                $('#porcentaje_cuota_1').val('');
            } else {
                $('#div-porcentaje-cuota-1').removeClass('d-none');
                $('#div-descuento-cuota-1').addClass('d-none');
                descuento_cuota_1.setRawValue('');
            }
        } else if ($('#aplica_a_cuotas').val() == 'TO') {
            $('#div-descuento-cuota-1').addClass('d-none');
            $('#div-descuento-cuota-2').addClass('d-none');
            $('#div-descuento-cuota-3').addClass('d-none');
            $('#div-descuento-cuota-4').addClass('d-none');
            $('#div-descuento-cuota-5').addClass('d-none');
            $('#div-porcentaje-cuota-1').addClass('d-none');
            $('#div-porcentaje-cuota-2').addClass('d-none');
            $('#div-porcentaje-cuota-3').addClass('d-none');
            $('#div-porcentaje-cuota-4').addClass('d-none');
            $('#div-porcentaje-cuota-5').addClass('d-none');
            descuento_cuota_1.setRawValue('');
            descuento_cuota_2.setRawValue('');
            descuento_cuota_3.setRawValue('');
            descuento_cuota_4.setRawValue('');
            descuento_cuota_5.setRawValue('');
            $('#porcentaje_cuota_1').val('');
            $('#porcentaje_cuota_2').val('');
            $('#porcentaje_cuota_3').val('');
            $('#porcentaje_cuota_4').val('');
            $('#porcentaje_cuota_5').val('');
            if ($(this).val() == 'VA') {
                $('#div-descuento-cuotas').removeClass('d-none');
                $('#div-porcentaje-cuotas').addClass('d-none');
                $('#porcentaje_cuotas').val('');
            } else {
                $('#div-porcentaje-cuotas').removeClass('d-none');
                $('#div-descuento-cuotas').addClass('d-none');
                descuento_cuotas.setRawValue('');
            }
        } else if ($('#aplica_a_cuotas').val() == 'CO') {
            $('#div-descuento-cuotas').addClass('d-none');
            $('#div-porcentaje-cuotas').addClass('d-none');
            descuento_cuotas.setRawValue('');
            $('#porcentaje_cuotas').val('');
            if ($(this).val() == 'VA') {
                $('#div-descuento-cuota-1').removeClass('d-none');
                $('#div-descuento-cuota-2').removeClass('d-none');
                $('#div-descuento-cuota-3').removeClass('d-none');
                $('#div-descuento-cuota-4').removeClass('d-none');
                $('#div-descuento-cuota-5').removeClass('d-none');
                $('#div-porcentaje-cuota-1').addClass('d-none');
                $('#div-porcentaje-cuota-2').addClass('d-none');
                $('#div-porcentaje-cuota-3').addClass('d-none');
                $('#div-porcentaje-cuota-4').addClass('d-none');
                $('#div-porcentaje-cuota-5').addClass('d-none');
                $('#porcentaje_cuota_1').val('');
                $('#porcentaje_cuota_2').val('');
                $('#porcentaje_cuota_3').val('');
                $('#porcentaje_cuota_4').val('');
                $('#porcentaje_cuota_5').val('');
            } else {
                $('#div-porcentaje-cuota-1').removeClass('d-none');
                $('#div-porcentaje-cuota-2').removeClass('d-none');
                $('#div-porcentaje-cuota-3').removeClass('d-none');
                $('#div-porcentaje-cuota-4').removeClass('d-none');
                $('#div-porcentaje-cuota-5').removeClass('d-none');
                $('#div-descuento-cuota-1').addClass('d-none');
                $('#div-descuento-cuota-2').addClass('d-none');
                $('#div-descuento-cuota-3').addClass('d-none');
                $('#div-descuento-cuota-4').addClass('d-none');
                $('#div-descuento-cuota-5').addClass('d-none');
                descuento_cuota_1.setRawValue('');
                descuento_cuota_2.setRawValue('');
                descuento_cuota_3.setRawValue('');
                descuento_cuota_4.setRawValue('');
                descuento_cuota_5.setRawValue('');
            }
        }
    });

    $('#aplica_a_cuotas').on('change', function () {
        if ($(this).val() == '1C') {
            $('#div-descuento-cuotas').addClass('d-none');
            $('#div-porcentaje-cuotas').addClass('d-none');
            $('#div-descuento-cuota-1').addClass('d-none');
            $('#div-descuento-cuota-2').addClass('d-none');
            $('#div-descuento-cuota-3').addClass('d-none');
            $('#div-descuento-cuota-4').addClass('d-none');
            $('#div-descuento-cuota-5').addClass('d-none');
            $('#div-porcentaje-cuota-1').addClass('d-none');
            $('#div-porcentaje-cuota-2').addClass('d-none');
            $('#div-porcentaje-cuota-3').addClass('d-none');
            $('#div-porcentaje-cuota-4').addClass('d-none');
            $('#div-porcentaje-cuota-5').addClass('d-none');
            descuento_cuota_1.setRawValue('');
            descuento_cuota_2.setRawValue('');
            descuento_cuota_3.setRawValue('');
            descuento_cuota_4.setRawValue('');
            descuento_cuota_5.setRawValue('');
            $('#porcentaje_cuota_1').val('');
            $('#porcentaje_cuota_2').val('');
            $('#porcentaje_cuota_3').val('');
            $('#porcentaje_cuota_4').val('');
            $('#porcentaje_cuota_5').val('');
            if ($('#tipo_cuotas').val() == 'VA') {
                $('#div-descuento-cuota-1').removeClass('d-none');
                $('#div-porcentaje-cuota-1').addClass('d-none');
                $('#porcentaje_cuota_1').val('');
            } else if ($('#tipo_cuotas').val() == 'PO') {
                $('#div-porcentaje-cuota-1').removeClass('d-none');
                $('#div-descuento-cuota-1').addClass('d-none');
                descuento_cuota_1.setRawValue('');
            }
        } else if ($(this).val() == 'TO') {
            $('#div-descuento-cuota-1').addClass('d-none');
            $('#div-descuento-cuota-2').addClass('d-none');
            $('#div-descuento-cuota-3').addClass('d-none');
            $('#div-descuento-cuota-4').addClass('d-none');
            $('#div-descuento-cuota-5').addClass('d-none');
            $('#div-porcentaje-cuota-1').addClass('d-none');
            $('#div-porcentaje-cuota-2').addClass('d-none');
            $('#div-porcentaje-cuota-3').addClass('d-none');
            $('#div-porcentaje-cuota-4').addClass('d-none');
            $('#div-porcentaje-cuota-5').addClass('d-none');
            descuento_cuota_1.setRawValue('');
            descuento_cuota_2.setRawValue('');
            descuento_cuota_3.setRawValue('');
            descuento_cuota_4.setRawValue('');
            descuento_cuota_5.setRawValue('');
            $('#porcentaje_cuota_1').val('');
            $('#porcentaje_cuota_2').val('');
            $('#porcentaje_cuota_3').val('');
            $('#porcentaje_cuota_4').val('');
            $('#porcentaje_cuota_5').val('');
            if ($('#tipo_cuotas').val() == 'VA') {
                $('#div-descuento-cuotas').removeClass('d-none');
                $('#div-porcentaje-cuotas').addClass('d-none');
                $('#porcentaje_cuotas').val('');
            } else if ($('#tipo_cuotas').val() == 'PO') {
                $('#div-porcentaje-cuotas').removeClass('d-none');
                $('#div-descuento-cuotas').addClass('d-none');
                descuento_cuotas.setRawValue('');
            }
        } else if ($(this).val() == 'CO') {
            $('#div-descuento-cuotas').addClass('d-none');
            $('#div-porcentaje-cuotas').addClass('d-none');
            descuento_cuotas.setRawValue('');
            $('#porcentaje_cuotas').val('');
            if ($('#tipo_cuotas').val() == 'VA') {
                $('#div-descuento-cuota-1').removeClass('d-none');
                $('#div-descuento-cuota-2').removeClass('d-none');
                $('#div-descuento-cuota-3').removeClass('d-none');
                $('#div-descuento-cuota-4').removeClass('d-none');
                $('#div-descuento-cuota-5').removeClass('d-none');
                $('#div-porcentaje-cuota-1').addClass('d-none');
                $('#div-porcentaje-cuota-2').addClass('d-none');
                $('#div-porcentaje-cuota-3').addClass('d-none');
                $('#div-porcentaje-cuota-4').addClass('d-none');
                $('#div-porcentaje-cuota-5').addClass('d-none');
                $('#porcentaje_cuota_1').val('');
                $('#porcentaje_cuota_2').val('');
                $('#porcentaje_cuota_3').val('');
                $('#porcentaje_cuota_4').val('');
                $('#porcentaje_cuota_5').val('');
            } else if ($('#tipo_cuotas').val() == 'PO') {
                $('#div-porcentaje-cuota-1').removeClass('d-none');
                $('#div-porcentaje-cuota-2').removeClass('d-none');
                $('#div-porcentaje-cuota-3').removeClass('d-none');
                $('#div-porcentaje-cuota-4').removeClass('d-none');
                $('#div-porcentaje-cuota-5').removeClass('d-none');
                $('#div-descuento-cuota-1').addClass('d-none');
                $('#div-descuento-cuota-2').addClass('d-none');
                $('#div-descuento-cuota-3').addClass('d-none');
                $('#div-descuento-cuota-4').addClass('d-none');
                $('#div-descuento-cuota-5').addClass('d-none');
                descuento_cuota_1.setRawValue('');
                descuento_cuota_2.setRawValue('');
                descuento_cuota_3.setRawValue('');
                descuento_cuota_4.setRawValue('');
                descuento_cuota_5.setRawValue('');
            }
        }
    });

    $('#tipo_descuento').on('change', function () {
        if ($(this).val() == 'VA') {
            $('#div-precio-descuento').removeClass('d-none');
            $('#div-porcentaje-descuento').addClass('d-none');
            $('#porcentaje_descuento').val('');            
        } else {
            $('#div-porcentaje-descuento').removeClass('d-none');
            $('#div-precio-descuento').addClass('d-none');
            precio_descuento.setRawValue('');
        }
    })

    $('.porcentaje').on('input', function () {
        var valor = $(this).val();

        valor = valor.replace(/\D/g, '');

        valor = parseInt(valor, 10);

        if (isNaN(valor) || valor < 1) {
            valor = '';
        } else if (valor > 100) {
            valor = 100;
        }

        $(this).val(valor);
    })
</script>
