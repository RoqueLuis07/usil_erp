<script type="module">
    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
            shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            },
            months: {
            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
            longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            },
        },
    }

    $(document).ready(function () {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);
    });

    if ($('#success-message').val() != null) {
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
            icon: 'success',
            text: $('#success-message').val(),
        })
    };

    if ($('#error-message').val() != null) {
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
            icon: 'error',
            text: $('#error-message').val(),
        })
    };

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

    $('#forma_pago').on('change', function () {
        if ($(this).val() == 1) {
            $('.div-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#fecha_transaccion_debito').val('');
            $('#numero_transaccion_debito').val('');

            $('.div-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#fecha_transaccion_credito').val('');
            $('#numero_transaccion_credito').val('');

            $('.div-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('.div-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');
        } else if ($(this).val() == 2) {
            $('.div-debito').removeClass('d-none');
            $('.div-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#fecha_transaccion_credito').val('');
            $('#numero_transaccion_credito').val('');

            $('.div-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('.div-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');
        } else if ($(this).val() == 3) {
            $('.div-credito').removeClass('d-none');
            $('.div-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#fecha_transaccion_debito').val('');
            $('#numero_transaccion_debito').val('');

            $('.div-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');

            $('.div-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');
        } else if ($(this).val() == 4) {
            $('.div-transferencia').removeClass('d-none');
            $('.div-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#fecha_transaccion_debito').val('');
            $('#numero_transaccion_debito').val('');

            $('.div-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#fecha_transaccion_credito').val('');
            $('#numero_transaccion_credito').val('');

            $('.div-deposito').addClass('d-none');
            $('#cuenta_bancaria_deposito').selectpicker('val', '');
            $('#numero_transaccion_deposito').val('');
            $('#fecha_transaccion_deposito').val('');
        } else if ($(this).val() == 5) {
            $('.div-deposito').removeClass('d-none');
            $('.div-debito').addClass('d-none');
            $('#banco_debito').selectpicker('val', '');
            $('#fecha_transaccion_debito').val('');
            $('#numero_transaccion_debito').val('');

            $('.div-credito').addClass('d-none');
            $('#banco_credito').selectpicker('val', '');
            $('#fecha_transaccion_credito').val('');
            $('#numero_transaccion_credito').val('');

            $('.div-transferencia').addClass('d-none');
            $('#banco_transferencia').selectpicker('val', '');
            $('#cuenta_bancaria_transferencia').selectpicker('val', '');
            $('#numero_transaccion_transferencia').val('');
            $('#fecha_transaccion_transferencia').val('');
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
                window.location.href = '{{route('recibos.index')}}';
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
            title: '¿Está seguro de guardar el nuevo recibo?',
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

    $(document).on('focus', '.numero_transaccion', function () {
        $(this).on('input', function (event) {
            var valor = $(this).val();
            var regex = /^\d+$/;

            // Si la tecla presionada no coincide con el formato deseado, se cancela la acción
            if (!regex.test(valor)) {
                $(this).val(valor.slice(0, -1));
            }
        });
    });
</script>
