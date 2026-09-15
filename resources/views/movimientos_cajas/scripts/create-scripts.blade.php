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

        var error = {!! json_encode($errors->any(), JSON_HEX_TAG) !!};
        if (error) {
            var cajas = {!! json_encode($cajas, JSON_HEX_TAG) !!};
            var tipo = {!! json_encode(old('tipo_movimiento'), JSON_HEX_TAG) !!};
            if (tipo == 1) {
                var caja_destino = {!! json_encode(old('caja_destino'), JSON_HEX_TAG) !!};
                if (caja_destino) {
                    caja_destino = parseInt(caja_destino);
                    $('#caja_destino').selectpicker('destroy');
                    $('#caja_destino').empty();
                    $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        if (value.id == caja_destino) {
                            $('#caja_destino').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                        } else {
                            $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        }
                    })
                    $('#caja_destino').prop('disabled', false);
                    $('#caja_destino').selectpicker('render');
                } else {
                    $('#caja_destino').selectpicker('destroy');
                    $('#caja_destino').empty();
                    $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    })
                    $('#caja_destino').prop('disabled', false);
                    $('#caja_destino').selectpicker('render');
                }
            } else if (tipo == 2) {
                var caja_origen = {!! json_encode(old('caja_origen'), JSON_HEX_TAG) !!};
                if (caja_origen) {
                    caja_origen = parseInt(caja_origen);
                    $('#caja_origen').selectpicker('destroy');
                    $('#caja_origen').empty();
                    $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        if (value.id == caja_origen) {
                            $('#caja_origen').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                        } else {
                            $('#caja_origen').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        }
                    })
                    $('#caja_origen').prop('disabled', false);
                    $('#caja_origen').selectpicker('render');
                } else {
                    $('#caja_origen').selectpicker('destroy');
                    $('#caja_origen').empty();
                    $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        $('#caja_origen').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    })
                    $('#caja_origen').prop('disabled', false);
                    $('#caja_origen').selectpicker('render');
                }
            } else if (tipo == 3) {
                var caja_origen = {!! json_encode(old('caja_origen'), JSON_HEX_TAG) !!};
                if (caja_origen) {
                    caja_origen = parseInt(caja_origen);
                    $('#caja_origen').selectpicker('destroy');
                    $('#caja_origen').empty();
                    $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        if (value.id == caja_origen) {
                            $('#caja_origen').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                        } else {
                            $('#caja_origen').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        }
                    })
                    $('#caja_origen').prop('disabled', false);
                    $('#caja_origen').selectpicker('render');

                    var caja_destino = {!! json_encode(old('caja_destino'), JSON_HEX_TAG) !!};
                    if (caja_destino) {
                        caja_destino = parseInt(caja_destino);
                        $('#caja_destino').selectpicker('destroy');
                        $('#caja_destino').empty();
                        $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cajas, function (index, value) {
                            if (value.id == caja_destino) {
                                $('#caja_destino').append('<option value="' + value.id + '" selected>' + value.nombre + '</option>');
                            } else {
                                if (value.id != caja_origen) {
                                    $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                                }
                            }
                        })
                        $('#caja_destino').prop('disabled', false);
                        $('#caja_destino').selectpicker('render');
                    } else {
                        $('#caja_destino').selectpicker('destroy');
                        $('#caja_destino').empty();
                        $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>');
                        $.each(cajas, function (index, value) {
                            if (value.id != caja_origen) {
                                $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                            }
                        })
                        $('#caja_destino').prop('disabled', false);
                        $('#caja_destino').selectpicker('render');
                    }
                } else {
                    $('#caja_origen').selectpicker('destroy');
                    $('#caja_origen').empty();
                    $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(cajas, function (index, value) {
                        $('#caja_origen').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    })
                    $('#caja_origen').prop('disabled', false);
                    $('#caja_origen').selectpicker('render');
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
                window.location.href = '{{route('movimientos_cajas.index')}}';
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
            title: '¿Está seguro de guardar el movimiento de caja?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#store-form').submit();
            }
        })
    })

    $('#tipo_movimiento').on('change', function () {
        var tipo = $(this).val();
        var cajas = {!! json_encode($cajas, JSON_HEX_TAG) !!};
        if (tipo == 1) {
            $('#caja_destino').selectpicker('destroy');
            $('#caja_destino').empty();
            $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>')
            $.each(cajas, function (index, value) {
                $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>')
            });
            $('#caja_destino').prop('disabled', false);
            $('#caja_destino').selectpicker('render');

            $('#caja_origen').selectpicker('destroy');
            $('#caja_origen').empty();
            $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>')
            $('#caja_origen').prop('disabled', true);
            $('#caja_origen').selectpicker('render');
        } else {
            $('#caja_origen').selectpicker('destroy');
            $('#caja_origen').empty();
            $('#caja_origen').append('<option value="" selected disabled>Seleccionar...</option>')
            $.each(cajas, function (index, value) {
                $('#caja_origen').append('<option value="' + value.id + '">' + value.nombre + '</option>')
            });
            $('#caja_origen').prop('disabled', false);
            $('#caja_origen').selectpicker('render');

            $('#caja_destino').selectpicker('destroy');
            $('#caja_destino').empty();
            $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>')
            $('#caja_destino').prop('disabled', true);
            $('#caja_destino').selectpicker('render');
        }
    });

    $('#caja_origen').on('change', function () {
        if ($('#tipo_movimiento option:selected').val() == 3) {
            var caja_origen = parseInt($(this).val());
            var cajas = {!! json_encode($cajas, JSON_HEX_TAG) !!};

            $('#caja_destino').selectpicker('destroy');
            $('#caja_destino').empty();
            $('#caja_destino').append('<option value="" selected disabled>Seleccionar...</option>');
            $.each(cajas, function (index, value) {
                if (value.id != caja_origen) {
                    $('#caja_destino').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            })
            $('#caja_destino').prop('disabled', false);
            $('#caja_destino').selectpicker('render');
        }
    })
</script>
