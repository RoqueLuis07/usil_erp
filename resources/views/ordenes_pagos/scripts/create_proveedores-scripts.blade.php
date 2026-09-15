<script type="module">
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

    $('#compra').on('change', function () {
        var id = $(this).val();
        console.log(id);
        var url = "{{ route('ordenes_pagos.get_compra', ":id") }}";
        url = url.replace(':id', id);

        get_compra(url);
    });

    function get_compra(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#fecha_factura').val(response.compra.fecha);
            $('#proveedor').val(response.compra.proveedor_nombre);
            $('#ruc_proveedor').val(response.compra.ruc_proveedor);
            $('#numero_factura').val(response.compra.numero_factura);
            $('#condicion').val(response.compra.condicion);
            $('#monto_total').val(response.compra.monto_total);
        })
    }
</script>
