<script type="module">
    //Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: true,
        numeralDecimalScale: 0,
    };

    $(document).ready(function () {
        var monto = new Cleave ('#monto', formatoSeparadorMiles);

        var movimiento = {!! json_encode($movimiento, JSON_HEX_TAG) !!};
        var cuentas_bancarias = {!! json_encode($cuentas_bancarias, JSON_HEX_TAG) !!};
        var tipo = movimiento.tipo_movimiento_id;

        var error = {!! json_encode($errors->any(), JSON_HEX_TAG) !!};
        if (error) {
            var tipo = {!! json_encode(old('tipo_movimiento'), JSON_HEX_TAG) !!};
            if (tipo == 1) {
                var cuenta_bancaria_destino = {!! json_encode(old('cuenta_bancaria_destino'), JSON_HEX_TAG) !!};
                if (cuenta_bancaria_destino) {
                    cuenta_bancaria_destino = parseInt(cuenta_bancaria_destino);
                    $('#cuenta_bancaria_destino').selectpicker('destroy');
                    $('#cuenta_bancaria_destino').empty();
                    $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_destino) {
                            $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_destino').prop('disabled', false);
                    $('#cuenta_bancaria_destino').selectpicker('render');
                } else {
                    $('#cuenta_bancaria_destino').selectpicker('destroy');
                    $('#cuenta_bancaria_destino').empty();
                    $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_destino').prop('disabled', false);
                    $('#cuenta_bancaria_destino').selectpicker('render');
                }
            } else if (tipo == 2) {
                var cuenta_bancaria_origen = {!! json_encode(old('cuenta_bancaria_origen'), JSON_HEX_TAG) !!};
                if (cuenta_bancaria_origen) {
                    cuenta_bancaria_origen = parseInt(cuenta_bancaria_origen);
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_origen) {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                } else {
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                }
            } else if (tipo == 3) {
                var cuenta_bancaria_origen = {!! json_encode(old('cuenta_bancaria_origen'), JSON_HEX_TAG) !!};
                if (cuenta_bancaria_origen) {
                    cuenta_bancaria_origen = parseInt(cuenta_bancaria_origen);
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_origen) {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');

                    var cuenta_bancaria_destino = {!! json_encode(old('cuenta_bancaria_destino'), JSON_HEX_TAG) !!};
                    if (cuenta_bancaria_destino) {
                        cuenta_bancaria_destino = parseInt(cuenta_bancaria_destino);
                        $('#cuenta_bancaria_destino').selectpicker('destroy');
                        $('#cuenta_bancaria_destino').empty();
                        $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cuentas_bancarias, function (index, value) {
                            if (value.id == cuenta_bancaria_destino) {
                                $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                            } else {
                                if (value.id != cuenta_bancaria_origen) {
                                    $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                                }
                            }
                        })
                        $('#cuenta_bancaria_destino').prop('disabled', false);
                        $('#cuenta_bancaria_destino').selectpicker('render');
                    } else {
                        $('#cuenta_bancaria_destino').selectpicker('destroy');
                        $('#cuenta_bancaria_destino').empty();
                        $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cuentas_bancarias, function (index, value) {
                            if (value.id != cuenta_bancaria_origen) {
                                $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                            }
                        })
                        $('#cuenta_bancaria_destino').prop('disabled', false);
                        $('#cuenta_bancaria_destino').selectpicker('render');
                    }
                } else {
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                }
            }
        } else {
            if (tipo == 1) {
                var cuenta_bancaria_destino = movimiento.cuenta_bancaria_destino_id;
                if (cuenta_bancaria_destino) {
                    cuenta_bancaria_destino = parseInt(cuenta_bancaria_destino);
                    $('#cuenta_bancaria_destino').selectpicker('destroy');
                    $('#cuenta_bancaria_destino').empty();
                    $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_destino) {
                            $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_destino').prop('disabled', false);
                    $('#cuenta_bancaria_destino').selectpicker('render');
                } else {
                    $('#cuenta_bancaria_destino').selectpicker('destroy');
                    $('#cuenta_bancaria_destino').empty();
                    $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_destino').prop('disabled', false);
                    $('#cuenta_bancaria_destino').selectpicker('render');
                }
            } else if (tipo == 2) {
                var cuenta_bancaria_origen = movimiento.cuenta_bancaria_origen_id;
                if (cuenta_bancaria_origen) {
                    cuenta_bancaria_origen = parseInt(cuenta_bancaria_origen);
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_origen) {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                } else {
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                }
            } else if (tipo == 3) {
                var cuenta_bancaria_origen = movimiento_cuenta_bancaria_origen_id;
                if (cuenta_bancaria_origen) {
                    cuenta_bancaria_origen = parseInt(cuenta_bancaria_origen);
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        if (value.id == cuenta_bancaria_origen) {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        } else {
                            $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                        }
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');

                    var cuenta_bancaria_destino = movimiento.cuenta_bancaria_destino_id;
                    if (cuenta_bancaria_destino) {
                        cuenta_bancaria_destino = parseInt(cuenta_bancaria_destino);
                        $('#cuenta_bancaria_destino').selectpicker('destroy');
                        $('#cuenta_bancaria_destino').empty();
                        $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cuentas_bancarias, function (index, value) {
                            if (value.id == cuenta_bancaria_destino) {
                                $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" selected data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                            } else {
                                if (value.id != cuenta_bancaria_origen) {
                                    $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                                }
                            }
                        })
                        $('#cuenta_bancaria_destino').prop('disabled', false);
                        $('#cuenta_bancaria_destino').selectpicker('render');
                    } else {
                        $('#cuenta_bancaria_destino').selectpicker('destroy');
                        $('#cuenta_bancaria_destino').empty();
                        $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cuentas_bancarias, function (index, value) {
                            if (value.id != cuenta_bancaria_origen) {
                                $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                            }
                        })
                        $('#cuenta_bancaria_destino').prop('disabled', false);
                        $('#cuenta_bancaria_destino').selectpicker('render');
                    }
                } else {
                    $('#cuenta_bancaria_origen').selectpicker('destroy');
                    $('#cuenta_bancaria_origen').empty();
                    $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cuentas_bancarias, function (index, value) {
                        $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                    })
                    $('#cuenta_bancaria_origen').prop('disabled', false);
                    $('#cuenta_bancaria_origen').selectpicker('render');
                }
            }
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
                window.location.href = '{{route('movimientos_bancos.index')}}';
            }
        })
    });

    $('#update-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el movimiento de banco?',
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

    $('#tipo_movimiento').on('change', function () {
        var tipo = $(this).val();
        var cuentas_bancarias = {!! json_encode($cuentas_bancarias, JSON_HEX_TAG) !!};
        if (tipo == 1) {
            $('#cuenta_bancaria_destino').selectpicker('destroy');
            $('#cuenta_bancaria_destino').empty();
            $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>')
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            });
            $('#cuenta_bancaria_destino').prop('disabled', false);
            $('#cuenta_bancaria_destino').selectpicker('render');

            $('#cuenta_bancaria_origen').selectpicker('destroy');
            $('#cuenta_bancaria_origen').empty();
            $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>')
            $('#cuenta_bancaria_origen').prop('disabled', true);
            $('#cuenta_bancaria_origen').selectpicker('render');
        } else {
            $('#cuenta_bancaria_origen').selectpicker('destroy');
            $('#cuenta_bancaria_origen').empty();
            $('#cuenta_bancaria_origen').append('<option value="" selected disabled>Seleccionar...</option>')
            $.each(cuentas_bancarias, function (index, value) {
                $('#cuenta_bancaria_origen').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>')
            });
            $('#cuenta_bancaria_origen').prop('disabled', false);
            $('#cuenta_bancaria_origen').selectpicker('render');

            $('#cuenta_bancaria_destino').selectpicker('destroy');
            $('#cuenta_bancaria_destino').empty();
            $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>')
            $('#cuenta_bancaria_destino').prop('disabled', true);
            $('#cuenta_bancaria_destino').selectpicker('render');
        }
    });

    $('#cuenta_bancaria_origen').on('change', function () {
        if ($('#tipo_movimiento option:selected').val() == 3) {
            var cuenta_bancaria_origen = parseInt($(this).val());
            var cuentas_bancarias = {!! json_encode($cuentas_bancarias, JSON_HEX_TAG) !!};

            $('#cuenta_bancaria_destino').selectpicker('destroy');
            $('#cuenta_bancaria_destino').empty();
            $('#cuenta_bancaria_destino').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cuentas_bancarias, function (index, value) {
                if (value.id != cuenta_bancaria_origen) {
                    $('#cuenta_bancaria_destino').append('<option value="' + value.id + '" data-subtext="' + value.numero_cuenta + '">' + value.banco.nombre + '</option>');
                }
            })
            $('#cuenta_bancaria_destino').prop('disabled', false);
            $('#cuenta_bancaria_destino').selectpicker('render');
        }
    })
</script>
