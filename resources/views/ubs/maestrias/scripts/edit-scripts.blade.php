<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            if (input.files[0].type == 'application/pdf') {
                $('#vista-imagen').css('width', '100px');
                $('#vista-imagen').css('height', '100px');
                $('#vista-imagen').prop('src', '{{asset('storage/pdf.png')}}');
            } else {
                reader.onload = function (e) {
                    $('#vista-imagen').prop('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        };
    };
</script>

<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    var fecha_apertura = {!!json_encode($maestria->fecha_apertura, JSON_HEX_TAG) !!}

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        minDate: fecha_apertura,
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
        var cantidad_horas = new Cleave('#cantidad_horas', formatoSeparadorMiles);
        var cantidad_creditos = new Cleave('#cantidad_creditos', formatoSeparadorMiles);
        var duracion = new Cleave('#duracion', formatoSeparadorMiles);
        var llamado = new Cleave('#llamado', formatoSeparadorMiles);
        var precio_contado = new Cleave('#precio_contado', formatoSeparadorMiles);
        var cantidad_cuotas = new Cleave('#cantidad_cuotas', formatoSeparadorMiles);
        var precio_cuota = new Cleave('#precio_cuota', formatoSeparadorMiles);
        var dia_vencimiento_cuota = new Cleave('#dia_vencimiento_cuota', formatoSeparadorMiles);
        var precio_defensa = new Cleave('#precio_defensa', formatoSeparadorMiles);
        var precio_titulo = new Cleave('#precio_titulo', formatoSeparadorMiles);
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
                window.location.href = '{{route('maestrias.index')}}';
            }
        })
    });

    $('#clean-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de vaciar todos los campos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('form :input').val('');
                $('.selectpicker').selectpicker('val', '');
            }
        })
    })

    $('#update-btn').click(function () {
        var nombre = $('#nombre_fantasia').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar la maestría ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#update-form').submit();
            }
        })
    })

    $('#cronograma').on('change', function () {
        $('#eliminar-cronograma').prop('disabled', false);
        var imagen = $('#imagen').val();
        if (imagen == 'SI') {
            $('#div-imagen-original').addClass('d-none');
            $('#div-imagen').removeClass('d-none');
        }
    })

    $('#eliminar-cronograma').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el archivo subido?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var imagen = $('#imagen').val();
                if (imagen == 'SI') {
                    $('#div-imagen-original').removeClass('d-none');
                    $('#div-imagen').addClass('d-none');
                }
                $('#cronograma').val('');
                $('#vista-imagen').css('width', '200px');
                $('#vista-imagen').css('height', '200px');
                $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
                $(this).prop('disabled', true);
            }
        });
    });
</script>
