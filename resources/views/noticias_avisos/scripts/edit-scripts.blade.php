<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#vista-imagen').prop('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        };
    };
</script>

<script type="module">
    const hoy = moment();
    const today = hoy.format('Y-M-D H:m');

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y H:i',
        dateFormat: 'Y-m-d H:i:s',
        minDate: today,
        enableTime: true,
        time_24hr: true,
        defaultDate: today,
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
        onChange: function () {
            $('#fecha_cambiada').val('SI');
        }
    }

    $(document).ready(function () {
        var calendario = flatpickr('.flatpickr', flatpickrOptions);

        ClassicEditor
            .create(document.querySelector('#descripcion'), {
                language: 'es',
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'insertTable', 'blockQuote', '|',
                    'undo', 'redo'
                ],
            })
            .then(editor => {

            })
            .catch(error => {
                console.error(error);
            });
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
                window.location.href = '{{route('noticias_avisos.index')}}';
            }
        })
    });

    $('#update-btn').click(function () {
        var titulo = $('#titulo').val();
        if ($('#tipo1').checked) {
            var noticia_aviso = 'la noticia';
        } else {
            var noticia_aviso = 'el aviso';
        }
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar ' + noticia_aviso + ' ' + titulo + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#update-form').submit();
            }
        })
    });

    $('#portada').on('change', function () {
        $('#eliminar-portada').prop('disabled', false);
    })

    $('#eliminar-portada').on('click', function () {
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
                $('#portada').val('');
                $('#vista-imagen').css('width', '200px');
                $('#vista-imagen').css('height', '200px');
                var noticia_aviso = {!!json_encode($noticia_aviso, JSON_HEX_TAG) !!}
                if (noticia_aviso.portada) {
                    var url = "{{asset(":portada")}}"
                    url = url.replace(':portada', noticia_aviso.portada)
                    $('#vista-imagen').prop('src', url);
                } else {
                    $('#vista-imagen').prop('src', '{{asset('storage/no_image.png')}}');
                }
                $(this).prop('disabled', true);
            }
        });
    });
</script>
