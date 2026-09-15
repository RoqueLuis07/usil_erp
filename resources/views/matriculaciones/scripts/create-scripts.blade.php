<script type="module">
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

    $(document).ready(function () {
        var semestres = {!!json_encode($semestres, JSON_HEX_TAG) !!}
        var programas = {!!json_encode($programas, JSON_HEX_TAG) !!}
        var carreras = {!!json_encode($carreras, JSON_HEX_TAG) !!}

        var id_control;
        var hoy = moment();
        var semestreSelected = $('#semestre option:selected').val();
        var programaSelected = {!!json_encode(old('programa'), JSON_HEX_TAG) !!};
        var carreraSelected = {!!json_encode(old('carrera'), JSON_HEX_TAG) !!};
        var carreraSiuSelected = {!!json_encode(old('carrera_siu'), JSON_HEX_TAG) !!};

        $.each(semestres, function (index, value) {
            if (semestreSelected == value.id) {
                $('#programa').empty();
                $('#programa').append('<option value="" selected disabled>Seleccionar...</option>');
                $('#carrera').empty();
                $('#carrera').append('<option value="" selected disabled>Seleccionar...</option>');
                $('#carrera_siu').empty();
                $('#carrera_siu').append('<option value="" selected disabled>Seleccionar...</option>');

                var opcionesUnicas = new Set();
                var opcionesUnicasProgramas = new Set();
                var opcionesUnicasSiu = new Set();
                var programaSiuId = 4;

                $.each(value.semestre_mallas, function (i, s_malla) {
                    var fechaInicio = moment(s_malla.fecha_inicio_matriculacion);
                    var fechaFin = moment(s_malla.fecha_fin_matriculacion);
                    // if (hoy.isBetween(fechaInicio, fechaFin, 'day', '[]')) {
                        var programaId = s_malla.malla.carrera.programa.id;
                        var carreraId = s_malla.malla.carrera.id;
                        if (!opcionesUnicasProgramas.has(programaId)) {
                            opcionesUnicasProgramas.add(programaId);
                        }
                        if (programaId == 1 || programaId == 2 || programaId == 3) {
                            if (!opcionesUnicas.has(carreraId)) {
                                opcionesUnicas.add(carreraId);
                            }
                        } else {
                            if (!opcionesUnicasSiu.has(carreraId)) {
                                opcionesUnicasSiu.add(carreraId);
                            }
                        }
                    // }
                });

                opcionesUnicasProgramas.forEach(function (programaId) {
                    $.each(programas, function (index, programa) {
                        if (programaId == programa.id) {
                            $('#programa').append('<option value="' + programa.id + '">' + programa.nombre + '</option>');
                        }
                    })
                });

                opcionesUnicas.forEach(function (carreraId) {
                    $.each(carreras, function (index, carrera) {
                        if (carreraId == carrera.id && carrera.programa_id == programaSelected) {
                            $('#carrera').append('<option value="' + carrera.id + '" data-subtext="' + carrera.nombre_real + '">' + carrera.nombre_fantasia + '</option>');
                        }
                    })
                });

                $('#carrera_siu').selectpicker('destroy');
                opcionesUnicasSiu.forEach(function (carreraId) {
                    $.each(carreras, function (index, carrera) {
                        if (carreraId == carrera.id && carrera.programa_id == programaSiuId) {
                            $('#carrera_siu').append('<option value="' + carrera.id + '" data-subtext="' + carrera.nombre_real + '">' + carrera.nombre_fantasia + '</option>');
                        }
                    })
                });
                $('#carrera_siu').selectpicker('render');
            }
        });

        if ($('#semestre option:selected').val() != '') {
            $('#programa').selectpicker('destroy');
            $('#programa').prop('disabled', false);
            $('#programa option').each(function () {
                if ($(this).val() == programaSelected) {
                        $(this).prop('selected', true);
                }
            });
        }

        if ($('#programa option:selected').val() != '') {
            $('#carrera').selectpicker('destroy');
            $('#carrera').prop('disabled', false);
            $('#carrera option').each(function () {
                if ($(this).val() == carreraSelected) {
                        $(this).prop('selected', true);
                }
            });
        }
        if ($('#programa option:selected').val() == 1) {
            $('#div-carrera_siu').removeClass('d-none');
            $('#carrera_siu').selectpicker('destroy');
            $('#carrera_siu').prop('disabled', false);
            $('#carrera_siu option').each(function () {
                if ($(this).val() == carreraSiuSelected) {
                        $(this).prop('selected', true);
                }
            });
            $('#carrera_siu').addClass('selectpicker').selectpicker('render');
        }

        $('#programa').addClass('selectpicker').selectpicker('render');
        $('#carrera').addClass('selectpicker').selectpicker('render');
    })

    $('.selectpicker').on('shown.bs.select', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');
    })

    $('.tipo_pago').on('click', function () {
        $('#div-tipo_pago').removeClass('is-invalid')
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
                window.location.href = '{{route('matriculaciones.index')}}';
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

    $('#save-btn').click(function () {
        var nombre = $('#alumno option:selected').text();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar la matriculación del alumno ' + nombre + '?',
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
    $('#semestre').on('change', function () {
        var hoy = moment();
        var semestres = {!!json_encode($semestres, JSON_HEX_TAG) !!}
        var programas = {!!json_encode($programas, JSON_HEX_TAG) !!}
        var semestreSelected = $(this).val();
        var alumnoSelected = $('#alumno option:selected').val();

        if (alumnoSelected != '') {
            get_carrera_alumno(alumnoSelected);
        }

        $.each(semestres, function (index, value) {
            if (semestreSelected == value.id) {
                $('#programa').empty();
                $('#programa').append('<option value="" selected disabled>Seleccionar...</option>');

                var opcionesUnicas = new Set();

                $.each(value.semestre_mallas, function (i, s_malla) {
                    var fechaInicio = moment(s_malla.fecha_inicio_matriculacion);
                    var fechaFin = moment(s_malla.fecha_fin_matriculacion);
                    // if (hoy.isBetween(fechaInicio, fechaFin, 'day', '[]')) {
                        var programaId = s_malla.malla.carrera.programa.id;
                        if (!opcionesUnicas.has(programaId)) {
                            opcionesUnicas.add(programaId);
                        }
                    // }
                });

                opcionesUnicas.forEach(function (programaId) {
                    $.each(programas, function (index, programa) {
                        if (programaId == programa.id) {
                            $('#programa').append('<option value="' + programa.id + '">' + programa.nombre + '</option>');
                        }
                    })
                });

                if (opcionesUnicas.size > 0) {
                    $('#programa').selectpicker('destroy');
                    $('#programa').prop('disabled', false);
                    $('#programa').addClass('selectpicker').selectpicker('render');
                } else {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-danger me-2',
                            cancelButton: 'btn btn-light',
                        },
                        buttonsStyling: false
                    });
                    swalWithBootstrapButtons.fire({
                        title: 'No hay programas disponibles para matriculación en este semestre',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Salir',
                        cancelButtonText: 'Volver',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{route('matriculaciones.index')}}';
                        }
                    })
                }
            }
        });
    })

    $('#programa').on('change', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');

        var hoy = moment();
        var semestres = {!!json_encode($semestres, JSON_HEX_TAG) !!}
        var carreras = {!!json_encode($carreras, JSON_HEX_TAG) !!}
        var semestreSelected = $('#semestre option:selected').val();
        var programaSelected = $(this).val();


        $.each(semestres, function (index, value) {
            if (semestreSelected == value.id) {
                $('#carrera').empty();
                $('#carrera').append('<option value="" selected disabled>Seleccionar...</option>');

                var opcionesUnicas = new Set();

                $.each(value.semestre_mallas, function (i, s_malla) {
                    var fechaInicio = moment(s_malla.fecha_inicio_matriculacion);
                    var fechaFin = moment(s_malla.fecha_fin_matriculacion);
                    // if (hoy.isBetween(fechaInicio, fechaFin, 'day', '[]')) {
                        var carreraId = s_malla.malla.carrera.id;
                        var programaId = s_malla.malla.carrera.programa.id;
                        if (programaId == 1 || programaId == 2 || programaId == 3) {
                            if (!opcionesUnicas.has(carreraId)) {
                                opcionesUnicas.add(carreraId);
                            }
                        }
                    // }
                });

                opcionesUnicas.forEach(function (carreraId) {
                    $.each(carreras, function (index, carrera) {
                        if (carreraId == carrera.id && carrera.programa_id == programaSelected) {
                            $('#carrera').append('<option value="' + carrera.id + '" data-subtext="' + carrera.nombre_real + '">' + carrera.nombre_fantasia + '</option>');
                        }
                    })
                });

                if (opcionesUnicas.size > 0) {
                    $('#carrera').selectpicker('destroy');
                    $('#carrera').prop('disabled', false);
                    $('#carrera').addClass('selectpicker').selectpicker('render');
                } else {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-danger me-2',
                            cancelButton: 'btn btn-light',
                        },
                        buttonsStyling: false
                    });
                    swalWithBootstrapButtons.fire({
                        title: 'No hay carreras disponibles para matriculación en este semestre y programa',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Salir',
                        cancelButtonText: 'Volver',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{route('matriculaciones.index')}}';
                        }
                    })
                }

                if (programaSelected == 1) {
                    $('#div-carrera_siu').removeClass('d-none');
                    var opcionesUnicasSiu = new Set();

                    $.each(value.semestre_mallas, function (i, s_malla) {
                        var fechaInicio = moment(s_malla.fecha_inicio_matriculacion);
                        var fechaFin = moment(s_malla.fecha_fin_matriculacion);
                        // if (hoy.isBetween(fechaInicio, fechaFin, 'day', '[]')) {
                            var carreraId = s_malla.malla.carrera.id;
                            var programaId = s_malla.malla.carrera.programa.id;
                            if (programaId == 4) {
                                if (!opcionesUnicasSiu.has(carreraId)) {
                                    opcionesUnicasSiu.add(carreraId);
                                }
                            }
                        // }
                    });
                    $('#carrera_siu').selectpicker('destroy');
                    $('#carrera_siu').empty();
                    $('#carrera_siu').append('<option value="" selected disabled>Seleccionar...</option>')
                    opcionesUnicasSiu.forEach(function (carreraId) {
                        $.each(carreras, function (index, carrera) {
                            var programaSiuId = 4;
                            if (carreraId == carrera.id && carrera.programa_id == programaSiuId) {
                                $('#carrera_siu').append('<option value="' + carrera.id + '" data-subtext="' + carrera.nombre_real + '">' + carrera.nombre_fantasia + '</option>');
                            }
                        })
                    });

                    if (opcionesUnicasSiu.size > 0) {
                        $('#carrera_siu').selectpicker('destroy');
                        $('#carrera_siu').prop('disabled', false);
                        $('#carrera_siu').addClass('selectpicker').selectpicker('render');
                    }
                } else {
                    if (!$('#div-carrera_siu').hasClass('d-none')) {
                        $('#div-carrera_siu').addClass('d-none');
                        $('#carrera_siu').selectpicker('destroy');
                        $('#carrera_siu').empty();
                        $('#carrera_siu').append('<option value="" selected disabled>Seleccionar...</option>')
                        $('#carrera_siu').selectpicker('render');
                        $('#validacion').val(0);
                    }
                }
            }
        });

        var alumno = $('#alumno option:selected').val();
        var programa = $(this).val();
        var carrera = $('#carrera option:selected').val();

        if (alumno != '' && carrera != '') {
            validate_carrera_siu(alumno, carrera, programa);
        }
    });

    $('#carrera').on('change', function () {
        $(this).selectpicker('destroy');
        $(this).removeClass('is-invalid');
        $(this).selectpicker('render');
        $(this).selectpicker('toggle');

        var alumno = $('#alumno option:selected').val();
        var programa = $('#programa option:selected').val();
        var carrera = $(this).val();

        if (alumno != '' && programa != '') {
            validate_carrera_siu(alumno, carrera, programa);
        }
    })

    $('#alumno').on('change', function () {
        var alumno = $(this).val();
        var programa = $('#programa option:selected').val();
        var carrera = $('#carrera option:selected').val();
        var semestre = $('#semestre option:selected').val();

        if (semestre != '') {
            get_carrera_alumno(alumno);
        }

        if (programa != '' && carrera != '') {
            validate_carrera_siu(alumno, carrera, programa);
        }
    })

    function get_carrera_alumno(alumno) {
        var url = "{{route('matriculaciones.get_carrera', ":alumno")}}";
        url = url.replace(':alumno', alumno);

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
            if (response.validacion != 0) {
                $('#programa').selectpicker('destroy');
                if ($('#programa option').length == 1) {
                    $('#programa').empty();
                    $('#programa').append('<option value="" disabled>Seleccionar...</option>');
                    $.each(response.programas, function (index, value) {
                        if (index == 0) {
                            var selected = 'selected';
                        } else {
                            var selected = '';
                        }
                        $('#programa').append('<option value="' + value.id + '"' + selected + '>' + value.nombre + '</option>');
                    })
                } else {
                    $.each(response.programas, function (index, value) {
                        if ($('#programa option[value="' + value.id + '"]').length == 0) {
                            $('#programa').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        } else {
                            $('#programa').val(value.id);
                        }
                    })
                }
                $('#programa').selectpicker('render');




                $('#carrera').selectpicker('destroy');
                if ($('#carrera option').length == 1) {
                    $('#carrera').empty();
                    $('#carrera').prop('disabled', false);
                    $('#carrera').append('<option value="" disabled>Seleccionar...</option>');
                    $.each(response.carreras, function (index, value) {
                        if (index == 0) {
                            var selected = 'selected';
                        } else {
                            var selected = '';
                        }
                        $('#carrera').append('<option value="' + value.id + '"' + selected + ' data-subtext="' + value.nombre_real + '">' + value.nombre_fantasia + '</option>');
                    })
                } else {
                    $.each(response.carreras, function (index, value) {
                        if ($('#carrera option[value="' + value.id + '"]').length == 0) {
                            $('#carrera').append('<option value="' + value.id + '" data-subtext="' + value.nombre_real + '">' + value.nombre_fantasia + '</option>');
                        } else {
                            $('#carrera').val(value.id);
                        }
                    })
                }
                $('#carrera').selectpicker('render');

                if (response.carreras_siu.length != 0) {
                    $('#carrera_siu').selectpicker('destroy');
                    $('#div-carrera_siu').removeClass('d-none');
                    if ($('#carrera_siu option').length == 1) {
                        $('#carrera_siu').empty();
                        $('#carrera_siu').prop('disabled', false);
                        $('#carrera_siu').append('<option value="" disabled selected>Seleccionar...</option>');
                        $.each(response.carreras_siu, function (index, value) {
                            if (value != null) {
                                if (index == 0) {
                                    var selected = 'selected';
                                } else {
                                    var selected = '';
                                }
                                $('#carrera_siu').append('<option value="' + value.id + '"' + selected + ' data-subtext="' + value.nombre_real + '">' + value.nombre_fantasia + '</option>');
                            }
                        })
                    } else {
                        $.each(response.carreras_siu, function (index, value) {
                            if (value != null) {
                                if ($('#carrera_siu option[value="' + value.id + '"]').length == 0) {
                                    $('#carrera_siu').append('<option value="' + value.id + '" data-subtext="' + value.nombre_real + '">' + value.nombre_fantasia + '</option>');
                                } else {
                                    $('#carrera_siu').val(value.id);
                                }
                            }
                        })
                    }
                    $('#carrera_siu').selectpicker('render');
                }
            }
        })
    }

    function validate_carrera_siu(alumno, carrera, programa) {
        var url = "{{route('matriculaciones.validate_carrera_siu', ['alumno' => ":alumno", 'carrera' => ":carrera", 'programa' => ":programa"])}}";
        url = url.replace(':alumno', alumno);
        url = url.replace(':carrera', carrera);
        url = url.replace(':programa', programa);

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
            if (response.validacion == 1) {
                $('#validacion').val(response.validacion);
                $('#label-carrera_siu').html('Carrera SIU <span class="text-danger">(*)</span>');
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-light',
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: 'El alumno ya debe ser inscripto obligatoriamente en una carrera SIU.',
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                })
            }
            if (response.carrera_siu) {
                $('#carrera_siu').selectpicker('destroy');
                $('#carrera_siu').val(response.carrera_siu);
                $('#carrera_siu').selectpicker('render');
            }
        })
    }
</script>
