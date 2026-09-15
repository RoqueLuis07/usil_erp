<script type="module">
    const today = new Date();

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        maxDate: today,
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

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function () {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);

        new Cleave('#precio_matricula', formatoSeparadorMiles);
        new Cleave('#precio_contado', formatoSeparadorMiles);
        new Cleave('#precio_cuota', formatoSeparadorMiles);
        new Cleave('#precio_defensa', formatoSeparadorMiles);
        new Cleave('#precio_titulo', formatoSeparadorMiles);
        new Cleave('#precio_multa', formatoSeparadorMiles);
        new Cleave('#precio_certificado', formatoSeparadorMiles);
        new Cleave('#precio_examen_suficiencia', formatoSeparadorMiles);
        new Cleave('#precio_constancia_carrera', formatoSeparadorMiles);
        new Cleave('#precio_tutoria', formatoSeparadorMiles);
    })

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
                window.location.href = '{{route('articulos.index')}}';
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
            title: '¿Está seguro de guardar el nuevo artículo?',
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

    $('.compra_venta1').on('click', function () {
        if ($(this).is(':checked')) {
            $('.compra_venta2').prop('checked', false);
            $('#row-precios').addClass('d-none');
            $('.precios').val('');
            $('.cuentas').selectpicker('val', '');
            $('.vencimientos').val('');

            $('#div-cuenta_compra').removeClass('d-none');

            $('#div-carrera_curso').addClass('d-none');
            $('.carrera_curso1').prop('checked', false);
            $('.carrera_curso2').prop('checked', false);
            $('#div-carrera').addClass('d-none');
            $('#carrera').selectpicker('val', '');
            $('#curso').selectpicker('val', '');
            $('#div-curso').addClass('d-none');
        }
    });

    $('.compra_venta2').on('click', function () {
        if ($(this).is(':checked')) {
            $('.compra_venta1').prop('checked', false);
            $('#row-precios').removeClass('d-none');

            $('#div-cuenta_compra').addClass('d-none');
            $('#cuenta_compra').selectpicker('val', '');

            $('#div-carrera_curso').removeClass('d-none');
        }
    });

    $('.carrera_curso1').on('click', function () {
        if ($(this).is(':checked')) {
            $('.carrera_curso2').prop('checked', false);
            $('#div-curso').addClass('d-none');
            $('#curso').selectpicker('val', '');

            $('#div-carrera').removeClass('d-none');
        }
    });

    $('.carrera_curso2').on('click', function () {
        if ($(this).is(':checked')) {
            $('.carrera_curso1').prop('checked', false);
            $('#div-carrera').addClass('d-none');
            $('#carrera').selectpicker('val', '');

            $('#div-curso').removeClass('d-none');
        }
    });

    $('#unidad_negocio').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('articulos.get_subunidades_negocios', ":id") }}";
        url = url.replace(':id', id);

        get_subunidades_negocios(url);
    });

    $('#centro_costo').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('articulos.get_subcentros_costos', ":id") }}";
        url = url.replace(':id', id);

        get_subcentros_costos(url);
    });

    const max_cuotas = 24;
    const max_dias = 28;

    $('#cantidad_cuotas').on('input', function () {
        var valor = parseInt($(this).val());

        if (valor == '' || isNaN(valor)) {
            $(this).val('');
            return;
        }

        if (valor > max_cuotas) {
            this.value = max_cuotas;
        }
    })

    $('#dia_vencimiento_cuotas').on('input', function () {
        var valor = parseInt($(this).val());

        if (valor == '' || isNaN(valor)) {
            $(this).val('');
            return;
        }

        if (valor > max_dias) {
            this.value = max_dias;
        }
    })

    $('#dias_gracia').on('input', function () {
        var valor = parseInt($(this).val());

        if (valor == '' || isNaN(valor)) {
            $(this).val('');
            return;
        }

        if (valor > max_dias) {
            this.value = max_dias;
        }
    })

    function get_subunidades_negocios(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            console.log(response.subunidades_negocios);
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

    function get_subcentros_costos(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            $('#subcentro_costo').selectpicker('destroy');
            $('#subcentro_costo').empty();
            $('#subcentro_costo').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(response.subcentros_costos, function (index, value) {
                $('#subcentro_costo').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                if (response.subcentros_costos.length == 1) {
                    $('#subcentro_costo').val(value.id);
                }
            })
            $('#subcentro_costo').prop('disabled', false);
            $('#subcentro_costo').selectpicker('render');
        })
    }
</script>
