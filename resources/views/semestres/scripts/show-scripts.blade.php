<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        numeralPositiveOnly: true,
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
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

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

    $('.edit-semestre-malla').on('click', function () {
        var id = $(this).data('id');
        if (!$('.precio_matricula-' + id).data('cleave')) {
            new Cleave($('.precio_matricula-' + id), formatoSeparadorMiles);
            $('.precio_matricula-' + id).data('cleave', true)
        }
        if (!$('.precio_contado-' + id).data('cleave')) {
            new Cleave($('.precio_contado-' + id), formatoSeparadorMiles);
            $('.precio_contado-' + id).data('cleave', true)
        }
        if (!$('.precio_cuota-' + id).data('cleave')) {
            new Cleave($('.precio_cuota-' + id), formatoSeparadorMiles);
            $('.precio_cuota-' + id).data('cleave', true)
        }
        if (!$('.cantidad_cuotas-' + id).data('cleave')) {
            new Cleave($('.cantidad_cuotas-' + id), formatoSeparadorMiles);
            $('.cantidad_cuotas-' + id).data('cleave', true)
        }
        if (!$('.dia_vencimiento_cuota-' + id).data('cleave')) {
            new Cleave($('.dia_vencimiento_cuota-' + id), formatoSeparadorMiles);
            $('.dia_vencimiento_cuota-' + id).data('cleave', true)
        }
        if (!$('.precio_multa-' + id).data('cleave')) {
            new Cleave($('.precio_multa-' + id), formatoSeparadorMiles);
            $('.precio_multa-' + id).data('cleave', true)
        }
        if (!$('.dias_gracia-' + id).data('cleave')) {
            new Cleave($('.dias_gracia-' + id), formatoSeparadorMiles);
            $('.dias_gracia-' + id).data('cleave', true)
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
                window.location.href = '{{route('semestres.index')}}';
            }
        })
    });

    $('.update-semestre-malla-btn').click(function () {
        var id = $(this).data('id');
        var nombre = $('#title-malla-' + id).text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar los detalles de la carrera ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                update(id, url);
            }
        })
    });

    function update(id, url) {
        const formData = new FormData(document.getElementById('update-semestre-malla-form-' + id));
        var type = 'success';
        $('#update-semestre-malla-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#update-semestre-malla-form-' + id).find('.invalid-feedback').remove();
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
            $('#editSemestreMallaModal-' + id).modal('hide');
            message(response.message, type);
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]:not(:hidden)`);
                console.log(key);
                input.addClass('is-invalid');
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
            })
        })
    }
</script>
