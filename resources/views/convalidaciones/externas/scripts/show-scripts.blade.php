<script type="module">
    $(document).ready(function () {
        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
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

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $(document).on('click', '.btn-dictaminar', function () {
        var id = $(this).data('id');
        new Cleave('#carga_horaria_materia_origen-' + id, formatoSeparadorMiles);
        new Cleave('#porcentaje_coincidencia_bruta-' + id, formatoSeparadorMiles);
    })

    const minPorcentaje = 1;
    const maxPorcentaje = 100;

    $(document).on('input', '.porcentaje_coincidencia_bruta', function () {
        var valor = $(this).val();
        valor = valor.replace(/[^0-9]/g, '');

        if (valor < minPorcentaje) {
            valor = '';
        } else if (valor > maxPorcentaje) {
            valor = maxPorcentaje;
        }
        $(this).val(valor);
    })

    $('.btn-dictaminar').on('click', function () {
        var id = $(this).data('id');
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#numero_dictamen-' + id).val(response.numero);
        })
    })

    $('.dictaminar-btn').click(function () {
        var id = $(this).data('id');
        var dictamen = $('#numero_dictamen-' + id).val();
        var materia = $('#materia-' + id + ' option:selected').text();

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el dictamen de convalidación N° ' + dictamen + ' aplicado a la materia ' + materia + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                dictaminar(id, url);
            }
        })
    });

    function dictaminar(id, url) {
        const formData = new FormData(document.getElementById('dictaminar-form-' + id));
        $('#dictaminar-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#dictaminar-form-' + id).find('.invalid-feedback').remove();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', 'success');
            window.location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                if (key == 'porcentaje_coincidencia_bruta') {
                    const span = $('.porcentaje_span')
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(span);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    $('.btn-aprobar').on('click', function () {
        var id = $(this).data('id');
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            $('#numero_resolucion-' + id).val(response.numero);
        })
    })

    $('.aprobar-btn').click(function () {
        var id = $(this).data('id');
        var resolucion = $('#numero_resolucion-' + id).val();
        var materia = $('#materia-' + id + ' option:selected').text();

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la resolución de convalidación N° ' + resolucion + ' aplicado a la materia ' + materia + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                aprobar(id, url);
            }
        })
    });

    function aprobar(id, url) {
        const formData = new FormData(document.getElementById('aprobar-form-' + id));
        $('#aprobar-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#aprobar-form-' + id).find('.invalid-feedback').remove();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
            processData: false,
            contentType: false,
            cache: false,
        }).then(function(response) {
            window.localStorage.setItem('message', response.message);
            window.localStorage.setItem('type', 'success');
            window.location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }

    $('.dictamen').on('change', function () {
        var id = $(this).data('id');
        $('#eliminar-dictamen-' + id).prop('disabled', false);
    })

    $('.eliminar-dictamen').on('click', function () {
        var id = $(this).data('id');
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
                $('#dictamen-' + id).val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $('.resolucion').on('change', function () {
        var id = $(this).data('id');
        $('#eliminar-resolucion-' + id).prop('disabled', false);
    })

    $('.eliminar-resolucion').on('click', function () {
        var id = $(this).data('id');
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
                $('#resolucion-' + id).val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $('.destroy-dictamen-btn').on('click', function () {
        var id = $(this).data('id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el dictamen adjunto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#destroy-dictamen-form-' + id).submit();
            }
        });
    });

    $('.destroy-resolucion-btn').on('click', function () {
        var id = $(this).data('id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar la resolución adjunta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#destroy-resolucion-form-' + id).submit();
            }
        });
    });

    $('.btn-cancel-dictamen').click(function () {
        var id = $(this).data('id');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de anular la dictaminación de la convalidación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                $('#cancel-dictamen-form-' + id).submit();
            }
        })
    });

    $('.btn-cancel-resolucion').click(function () {
        var id = $(this).data('id');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de anular la aprobación de la convalidación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                $('#cancel-resolucion-form-' + id).submit();
            }
        })
    });
</script>
