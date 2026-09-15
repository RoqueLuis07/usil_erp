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

    $('#aplica_a1').on('click', function () {
        if ($(this).is(':checked') && $('#cliente option:selected')) {
            var id = $('#cliente option:selected').val();
            var url = "{{ route('notas_creditos.get_ventas', ":id") }}";
            url = url.replace(':id', id);

            get_ventas(url);
        }
    });

    $('#aplica_a2').on('click', function () {
        if ($(this).is(':checked') && $('#cliente option:selected').val()) {
            var id = $('#cliente option:selected').val();
            console.log(id);
            var url = "{{ route('notas_creditos.get_ventas', ":id") }}";
            url = url.replace(':id', id);

            console.log(url);

            get_ventas(url);
        }
    });

    $('#cliente').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('notas_creditos.get_ventas', ":id") }}";
        url = url.replace(':id', id);

        get_ventas(url);
    });

    $('#venta').on('change', function () {
        var monto = $('#venta option:selected').data('subtext');
        $('#monto_venta').val(monto);
    });

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
                window.location.href = '{{route('notas_creditos.index')}}';
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
            title: '¿Está seguro de guardar la nueva nota de crédito?',
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

    function get_ventas(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if ($('#aplica_a1').is(':checked') || $('#aplica_a2').is(':checked')) {
                $('#venta').selectpicker('destroy');
                $('#venta').empty();
                $('#venta').append('<option value="" selected disabled>Seleccionar...</option>');

                $.each(response.ventas, function (index, value) {
                    if ($('#aplica_a1').is(':checked')) {
                        var monto = value.monto_total;
                    } else if ($('#aplica_a2').is(':checked')) {
                        var monto = value.saldo;
                    }

                    $('#venta').append('<option value="' + value.id + '" data-subtext="' + monto + '">' + value.numero_factura + '</option>');
                })
                $('#venta').prop('disabled', false);
                $('#venta').selectpicker('render');
            }
        })
    }
</script>
