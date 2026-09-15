<script type="module">
    const today = new Date();
    const treintaDiasAntes = new Date(today);
    treintaDiasAntes.setDate(today.getDate() - 30);

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        minDate: treintaDiasAntes,
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

    $(document).ready(function () {
        flatpickr('.flatpickr', flatpickrOptions);

        $('.nexttab').on('click', function () {
            var nextTab = $(this).data('nexttab');
            $(nextTab).tab('show');
        })

        $('.previoustab').on('click', function () {
            var previousTab = $(this).data('previoustab');
            $(previousTab).tab('show');
        })

        $('#evaluacion').on('change', function () {
            if ($(this).val() != '') {
                $('#lista_alumnos').html('');

                $('#puntuar-btn').prop('disabled', false);

                var tipo = $(this).val();
                var materia = $('#materia_id').val();
                var carrera = $('#carrera_id').val();
                var semestre = $('#semestre_id').val();
                if (tipo > 3) {
                    get_alumnos(tipo, materia, semestre, carrera);
                } else {
                    var alumnos = {!!json_encode($alumnos, JSON_HEX_TAG) !!}
                    cargar_lista(alumnos, tipo);
                }

            } else {
                $('#puntuar-btn').prop('disabled', true);
            }
        })
    })

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).removeClass('is-invalid');
    })

    $(document).on('click', '#cancel-btn', function () {
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
                var url = $(this).data('url');
                window.location.href = url;
            }
        })
    })

    $('#save-btn').click(function () {
        var materia = $('#materia').val();
        var evaluacion = $('#evaluacion_detalle option:selected').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la evaluación de ' + evaluacion + ' de la materia ' + materia + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                save(url);
            }
        })
    });

    function get_alumnos(tipo, materia, semestre, carrera) {
        var url = "{{route('materias_evaluaciones.get_create_ajax', ['tipo' => ":tipo", 'materia' => ":materia", 'semestre' => ":semestre", 'carrera' => ":carrera"])}}";
        url = url.replace(":tipo", tipo);
        url = url.replace(':materia', materia);
        url = url.replace(':semestre', semestre);
        url = url.replace(':carrera', carrera);
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
            cargar_lista(response.alumnos, tipo);
        })
    }

    function save(url) {
        const formData = new FormData(document.getElementById('store-form'));
        $('#store-form').find('.is-invalid').removeClass('is-invalid');
        $('#store-form').find('.invalid-feedback').remove();
        var type = 'success';
        $.ajax({
            url: '{{route('materias_evaluaciones.store')}}',
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
            window.location.href = url;
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                if (key.includes('detalles')) {
                    var partes = key.split('.')
                    var input = $(`[name="${partes[0]}[${partes[1]}][${partes[2]}]"]`);
                } else {
                    var input = $(`[name="${key}"]`);
                }
                $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                input.addClass('is-invalid');

                if (key == 'evaluacion_detalle') {
                    $('#span-verificar').removeClass('d-none');
                }
            })
        })
    }

    function cargar_lista(alumnos, tipo) {
        var body = '';
        $.each(alumnos, function (index, value) {
            if (tipo == 1) {
                body += `<div class="row d-flex flex-wrap justify-content-center">
                    <div class="col-lg-1 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="nro_documento">Documento N°</label>' : ''}
                        <input type="text" class="form-control" id="nro_documento" value="${value.numero_documento}" readonly>
                    </div>
                    <div class="col-lg-3 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="detalles[' + index + '][alumno]">Alumno</label>' : '' }
                        <input type="text" class="form-control" id="detalles[${index}][alumno]" value="${value.primer_nombre} ${value.primer_apellido}" readonly>
                        <input type="hidden" name="detalles[${index}][alumno]" value="${value.id}">
                    </div>
                    <div class="col-lg-1 mb-3 text-center d-none">
                        ${index === 0 ? '<label class="form-label" for="ausente-presente-input-' + index + '">Asistencia</label>' : ''}
                        <input type="hidden" class="form-control text-center" id="ausente-presente-input-${index}" name="detalles[${index}][observacion]" value="">
                    </div>
                    <div class="col-lg-2 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="puntaje_obtenido' + index + '">Puntaje Obtenido</label>' : ''}
                        <input type="text" class="form-control text-center puntaje_obtenido" id="puntaje_obtenido-${index}" name="detalles[${index}][puntaje_obtenido]" data-id=${index}>
                    </div>
                </div>`;
            } else {
                body += `<div class="row d-flex flex-wrap justify-content-center">
                    <div class="col-lg-1 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="nro_documento">Documento N°</label>' : ''}
                        <input type="text" class="form-control" id="nro_documento" value="${value.numero_documento}" readonly>
                    </div>
                    <div class="col-lg-3 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="detalles[' + index + '][alumno]">Alumno</label>' : '' }
                        <input type="text" class="form-control" id="detalles[${index}][alumno]" value="${value.primer_nombre} ${value.primer_apellido}" readonly>
                        <input type="hidden" name="detalles[${index}][alumno]" value="${value.id}">
                    </div>
                    <div class="col-lg-1 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="ausente-presente-input-' + index + '">Asistencia</label>' : ''}
                        <div>
                            <button type="button" class="btn btn-outline-danger ausente-presente-btn" data-id=${index}><span class="ausente-presente-btn-text-${index}">Ausente</span></button>
                            <input type="hidden" class="form-control text-center" id="ausente-presente-input-${index}" name="detalles[${index}][observacion]" value="AUSENTE">
                        </div>
                    </div>
                    <div class="col-lg-2 mb-3 text-center">
                        ${index === 0 ? '<label class="form-label" for="puntaje_obtenido' + index + '">Puntaje Obtenido</label>' : ''}
                        <input type="text" class="form-control text-center puntaje_obtenido" id="puntaje_obtenido-${index}" name="detalles[${index}][puntaje_obtenido]" data-id=${index} readonly>
                    </div>
                </div>`;
            }
        })
        $('#lista_alumnos').append(body);
    }

    $('#pasouno-btn').on('click', function () {
        if ($('#span-verificar').hasClass('d-none')) {
            return false;
        } else {
            $('#span-verificar').addClass('d-none');
        }
    })

    $('#pasouno-volver-btn').on('click', function () {
        if ($('#span-verificar').hasClass('d-none')) {
            return false;
        } else {
            $('#span-verificar').addClass('d-none');
        }
    })

    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $(document).on('click', '.ausente-presente-btn', function () {
        var id = $(this).data('id');
        if ($('#ausente-presente-input-' + id).val() == 'AUSENTE') {
            $(this).removeClass('btn-outline-danger');
            $(this).removeClass('btn-outline-success');
            $(this).addClass('btn-success');
            $('#ausente-presente-input-' + id).val('');
            $('.ausente-presente-btn-text-' + id).html('Presente');
            $('#puntaje_obtenido-' + id).prop('readonly', false);
            var puntaje_obtenido = new Cleave ('#puntaje_obtenido-' + id, formatoSeparadorMiles);
        } else {
            $(this).removeClass('btn-success');
            $(this).addClass('btn-outline-danger');
            $('#ausente-presente-input-' + id).val('AUSENTE');
            $('.ausente-presente-btn-text-' + id).html('Ausente');
            $('#puntaje_obtenido-' + id).val('');
            $('#puntaje_obtenido-' + id).prop('readonly', true);
        }
    })

    $(document).on('mouseleave', '.ausente-presente-btn', function () {
        if (!$(this).hasClass('btn-success')) {
            $(this).removeClass('btn-outline-success').addClass('btn-outline-danger');
        }
    })

    const minPuntaje = 0
    const maxPuntaje = 40;

    $(document).on('input', '.puntos', function () {
		if ($('#evaluacion option:selected').val() == 2 || $('#evaluacion option:selected').val() == 3) {
			maxPuntaje = 20;
		}

        var valor = $(this).val();
        valor = valor.replace('.', '');

        if (valor < minPuntaje) {
            valor = minPuntaje;
        } else if (valor > maxPuntaje) {
            valor = maxPuntaje;
        }

        $(this).val(valor);
        $('#puntos-' + id).val(valor);
    })
</script>
