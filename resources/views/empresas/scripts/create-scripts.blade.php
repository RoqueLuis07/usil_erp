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

    //Inicio formatos para CleaveJS
    const formatoLineaBaja = {
            blocks: [3, 3, 3],
            numericOnly: true,

        };
    const formatoCelular = {
        blocks: [4, 3, 3],
        numericOnly: true,
    };

    $('#telefono-celular').mouseenter(function () {
        if ($(this).prop('disabled')) {
            message('Debe seleccionar un tipo de teléfono para poder escribir en el campo', 'error');
        }
    })

    $('#telefono-linea-baja').mouseenter(function () {
        if ($(this).prop('disabled')) {
            message('Debe seleccionar un tipo de teléfono para poder escribir en el campo', 'error');
        }
    })

    var old_check_telefono = $('#check_telefono').val();

    if (old_check_telefono == 1) {
        $('#telefono-linea-baja').val('');
        $('#telefono-linea-baja').prop('hidden', true);
        $('#telefono-celular').prop('hidden', false);
        $('#telefono-celular').prop('disabled', false);
        $('#linea_baja').prop('checked', false);
        if (!celular) {
            var celular = new Cleave ('#telefono-celular', formatoCelular);
        }
    } else if (old_check_telefono == 2) {
        $('#telefono-celular').val('');
        $('#telefono-celular').prop('hidden', true);
        $('#telefono-linea-baja').prop('hidden', false);
        $('#telefono-linea-baja').prop('disabled', false);
        $('#celular').prop('checked', false);
        if (!linea_baja) {
            var linea_baja = new Cleave ('#telefono-linea-baja', formatoLineaBaja);
        }
    }

    $('#linea_baja').on('change', function () {
        if ($('#linea_baja').prop('checked')) {
            $('#telefono-celular').val('');
            $('#telefono-celular').prop('hidden', true);
            $('#telefono-linea-baja').prop('hidden', false);
            $('#telefono-linea-baja').prop('disabled', false);
            $('#celular').prop('checked', false);
            if (!linea_baja) {
                var linea_baja = new Cleave ('#telefono-linea-baja', formatoLineaBaja);
            }
        } else {
            $('#telefono-linea-baja').val('');
            $('#telefono-linea-baja').prop('disabled', true);
        }
    })

    $('#celular').on('change', function () {
        if ($(this).prop('checked')) {
            $('#telefono-linea-baja').val('');
            $('#telefono-linea-baja').prop('hidden', true);
            $('#telefono-celular').prop('hidden', false);
            $('#telefono-celular').prop('disabled', false);
            $('#linea_baja').prop('checked', false);
            if (!celular) {
                var celular = new Cleave ('#telefono-celular', formatoCelular);
            }
        } else {
            $('#telefono-celular').val('');
            $('#telefono-celular').prop('disabled', true);
        }
    })

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

    $('#logo').change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#vista-imagen').prop('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        };
    })

    $('#delete-logo').on('click', function () {
                Swal.fire({
                    title: 'Está seguro de eliminar la imagen?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                    buttonsStyling: true,
                    showCloseButton: true
                }).then((result) => {
                if (result.isConfirmed) {
                    $('#logo').val('');
                    $('#vista-imagen-div').html('<img id="vista-imagen" class="hidden img-fluid img-circle" src="{{asset('storage/empresa/no_image.png')}}" alt="Logo" style="width: 154px; height: 154px">');
                }
                });
        });

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
                window.location.href = '{{route('root')}}';
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
                $('#vista-imagen-div').html('<img id="vista-imagen" class="hidden img-fluid img-circle" src="{{asset('storage/empresa/no_image.png')}}" alt="Logo" style="width: 154px; height: 154px">');
            }
        })
    })

    $('#save-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar los datos de la empresa?',
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

    $('#save-pais-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el nuevo país?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                save_pais();
            }
        })
    });

    $('#save-ciudad-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la nueva ciudad?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                save_ciudad();
            }
        })
    });

    function save_pais(id) {
        const formData = new FormData(document.getElementById('store-pais-form'));
        $('#store-pais-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-pais-form').find('.invalid-feedback').remove();
        $.ajax({
            url: '{{route('paises.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#store-pais-form').trigger('reset');
            $('#createPaisModal').modal('hide');
            $('#pais').selectpicker('destroy');
            $('#pais option').each(function () {
                $(this).remove();
            });
            $('#pais').append('<option value="" selected disabled>Selecionar...</option>');
            $.each(response.paises, function (index, value) {
                $('#pais').append('<option value="' + value.id + '">' + value.nombre + '</option>');
            })

            $('#pais').addClass('selectpicker').val(response.selected.id).selectpicker('render');

            message(response.message, 'success');
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    function save_ciudad(id) {
        const formData = new FormData(document.getElementById('store-ciudad-form'));
        $('#store-ciudad-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-ciudad-form').find('.invalid-feedback').remove();
        $.ajax({
            url: '{{route('ciudades.store')}}',
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#store-ciudad-form').trigger('reset');
            $('#createCiudadModal').modal('hide');
            $('#ciudad').selectpicker('destroy');
            $('#ciudad option').each(function () {
                $(this).remove();
            });
            $('#ciudad').append('<option value="" selected disabled>Selecionar...</option>');
            $.each(response.ciudades, function (index, value) {
                $('#ciudad').append('<option value="' + value.id + '">' + value.nombre + '</option>');
            })

            $('#ciudad').addClass('selectpicker').val(response.selected.id).selectpicker('render');

            message(response.message, 'success');
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    $('input[name="ruc"]').bind('keypress', function(e) {
        var ruc = $(this).val();
        if (ruc.search('-') != -1 ) {
            if (e.code === 'NumpadSubtract' || e.code === 'Minus' || e.code === 'Script' || e.keyCode === 45) {
                return false;
            }
            var ruc_separado = ruc.split('-');
            if (ruc_separado[1].length == 1) {
                return false;
            }
        }
        var patron = /[0-9-]/;
        return patron.test(String.fromCharCode(e.keyCode));
    });
</script>
