<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    const formatoSeparadorDecimales = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 2,
    };

    $(document).ready(function () {
        new Cleave ('#monto_guaranies', formatoSeparadorMiles);
        new Cleave ('#monto_dolares', formatoSeparadorDecimales);
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

    $('.btn-check').on('click', function () {
        $('.btn-group').removeClass('is-invalid');
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
                window.location.href = '{{route('ordenes_pagos.index')}}';
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
            title: '¿Está seguro de guardar la nueva OP?',
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

    $('#forma_pago').on('change', function () {
        $('#div-caja').addClass('d-none');
        $('#div-cuenta_bancaria').addClass('d-none');
        $('#div-numero_cheque').addClass('d-none');
        $('#div-numero_serie').addClass('d-none');
        $('#caja').selectpicker('val', '');
        $('#cuenta_bancaria').selectpicker('val', '');
        $('#numero_cheque').val('');
        $('#numero_serie').val('');

        var forma_pago = $(this).val();
        if (forma_pago == 1) {
            $('#div-caja').removeClass('d-none');
        } else if (forma_pago == 4) {
            $('#div-cuenta_bancaria').removeClass('d-none');
        } else if (forma_pago == 5) {
            $('#div-caja').removeClass('d-none');
        } else if (forma_pago == 7) {
            $('#div-cuenta_bancaria').removeClass('d-none');
            $('#div-numero_cheque').removeClass('d-none');
            $('#div-numero_serie').removeClass('d-none');
        }
    });

    $('#moneda').on('change', function () {
        if ($(this).val() == 1) {
            $('#monto_dolares').addClass('d-none');
            $('#monto_dolares').val('');
            $('#monto_guaranies').removeClass('d-none');
        } else {
            console.log('hola');
            $('#monto_guaranies').addClass('d-none');
            $('#monto_guaranies').val('');
            $('#monto_dolares').removeClass('d-none');
        }
    })

    $('#unidad_negocio').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('ordenes_pagos.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    function get_subunidades_negocios(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subunidad_negocio').selectpicker('destroy');
            $('#subunidad_negocio').empty();
            $('#subunidad_negocio').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subunidades_negocios, function (index, value) {
                $('#subunidad_negocio').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subunidades_negocios.length == 1) {
                    $('#subunidad_negocio').val(value.id);
                }
            })
            $('#subunidad_negocio').prop('disabled', false);
            $('#subunidad_negocio').selectpicker('render');
        })
    }
</script>
