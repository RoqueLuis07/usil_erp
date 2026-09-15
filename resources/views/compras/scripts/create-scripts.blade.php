<script type="module">
    const today = new Date();
    const primer_dia_del_mes = new Date(today.getFullYear(), today.getMonth(), 1);

    const flatpickrOptions = {
        altInput: true,
        altFormat: 'd/m/Y',
        dateFormat: 'Y-m-d',
        defaultDate: today,
        maxDate: today,
        minDate: primer_dia_del_mes,
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
                window.location.href = '{{route('compras.index')}}';
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
            title: '¿Está seguro de guardar la nueva factura de compra?',
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

    $('#proveedor').on('change', function () {
        var id = $(this).val();
        var url = "{{ route('compras.get_timbrados_proveedores', ":id") }}";
        url = url.replace(':id', id);

        var url_2 = "{{ route('compras.get_ordenes_compras', ":id") }}";
        url_2 = url_2.replace(':id', id);

        get_timbrados_proveedores(url);
        get_ordenes_compras(url_2);
    });

    $('#condicion_compra').change(function () {
        if ($(this).val() === 'CO') {
            if (!$('#div-credito_a').hasClass('d-none')) {
                $('#div-credito_a').addClass('d-none');
            }
            $('#credito_a').val('');
        } else {
            $('#div-credito_a').removeClass('d-none');
        }
    });

    function get_timbrados_proveedores(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if (response.error) {
                message(response.error, 'error');
                $('#proveedor').selectpicker('val', '');
            } else {
                $('#timbrado').selectpicker('destroy');
                $('#timbrado').empty();
                $('#timbrado').append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(response.timbrados, function (index, value) {
                    $('#timbrado').append('<option value="' + value.id + '" data-subtext="' + value.fecha_fin + '">' + value.numero + '</option>');
                    if (response.timbrados.length == 1) {
                        $('#timbrado').val(value.id);
                    }
                })
                $('#timbrado').prop('disabled', false);
                $('#timbrado').selectpicker('render');
            }
        })
    }

    function get_ordenes_compras(url) {
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).then(function(response) {
            if (response.ordenes_compras.length > 0) {
                $('#orden_compra').selectpicker('destroy');
                $('#orden_compra').empty();
                $('#orden_compra').append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(response.ordenes_compras, function (index, value) {
                    $('#orden_compra').append('<option value="' + value.id + '"> OC N°' + value.id + '</option>');
                })
                $('#orden_compra').prop('disabled', false);
                $('#orden_compra').selectpicker('render');

                $('.fila').detach();

                var filaAdd = `<div class="mb-2 fila" id="fila-0">
                                <div class="row">
                                    <div class="col-lg-2 mb-2 text-center" id="div-articulo-0">
                                        <label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control articulo-0 articulo @error('detalles.0.articulo') is-invalid @enderror" id="articulo-0" name="detalles[0][articulo]" data-live-search="true" data-id="0">

                                        </select>
                                        <span class="invalid-feedback error-articulo-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-descripcion-0">
                                        <label class="form-label label-descripcion">Descripción</label>
                                        <input type="text" class="form-control text-center descripcion-0 descripcion @error('detalles.0.descripcion') is-invalid @enderror" id="detalles[0][descripcion]" name="detalles[0][descripcion]" value="{{old('detalles.0.descripcion')}}" data-id="0">
                                        <span class="invalid-feedback error-descripcion-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-centro_costo-0">
                                        <label class="form-label label-centro_costo">CC1 <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control centro_costo-0 centro_costo @error('detalles.0.centro_costo') is-invalid @enderror" id="centro_costo-0" name="detalles[0][centro_costo]" data-live-search="true" data-id="0">

                                        </select>
                                        <span class="invalid-feedback error-centro_costo-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-subcentro_costo-0">
                                        <label class="form-label label-subcentro_costo">CC2 <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control subcentro_costo-0 subcentro_costo @error('detalles.0.subcentro_costo') is-invalid @enderror" id="subcentro_costo-0" name="detalles[0][subcentro_costo]" data-live-search="true" data-id="0" disabled>
                                            <option value="" selected disabled>Seleccionar...</option>
                                        </select>
                                        <span class="invalid-feedback error-subcentro_costo-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-cantidad-0">
                                        <label class="form-label label-cantidad">Cantidad <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center cantidad-0 cantidad @error('detalles.0.cantidad') is-invalid @enderror" id="detalles[0][cantidad]" name="detalles[0][cantidad]" value="{{old('detalles.0.cantidad')}}" data-id="0" placeholder="1">
                                        <span class="invalid-feedback error-cantidad-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-precio_costo-0">
                                        <label class="form-label label-precio_costo">Costo <span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control text-center precio_costo-0 precio_costo @error('detalles.0.precio_costo') is-invalid @enderror" id="detalles[0][precio_costo]" name="detalles[0][precio_costo]" value="{{old('detalles.0.precio_costo')}}" data-id="0" placeholder="100.000">
                                        <span class="invalid-feedback error-precio_costo-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 mb-2 text-center" id="div-iva-0">
                                        <label class="form-label label-iva">I.V.A. <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control iva-0 iva @error('detalles.0.iva') is-invalid @enderror" id="iva-0" name="detalles[0][iva]" data-live-search="true" data-id="0">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="10">10%</option>
                                            <option value="5">5%</option>
                                            <option value="0">EXENTO</option>
                                        </select>
                                        <span class="invalid-feedback error-iva-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-2 mb-2 text-center" id="div-subtotal-0">
                                        <label class="form-label label-subtotal">Subtotal</label>
                                        <input type="text" class="form-control text-center subtotal-0 subtotal @error('detalles.0.subtotal') is-invalid @enderror" id="subtotal-0" data-id="0" placeholder="0" readonly>
                                        <span class="invalid-feedback error-subtotal-0" role="alert">

                                        </span>
                                    </div>
                                    <div class="col-lg-1 col-sm-2 text-center">
                                        <label class="form-label label-acciones">Acciones</label>
                                        <div class="align-middle" id="acciones-0">
                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`

                $('#articulo-fila').append(filaAdd);

                var articulos = {!!json_encode($articulos, JSON_HEX_TAG) !!};
                $('.articulo-0').append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(articulos, function (i, val) {
                    $('.articulo-0').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                })
                $('.articulo-0').addClass('selectpicker').selectpicker('render');

                var centros_costos = {!!json_encode($centros_costos, JSON_HEX_TAG) !!};
                $('.centro_costo-0').append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(centros_costos, function (i, val) {
                    $('.centro_costo-0').append('<option value="' + val.id + '">' + val.nombre + '</option>');
                })
                $('.centro_costo-0').addClass('selectpicker').selectpicker('render');

                $('.subcentro_costo-0').selectpicker('render');
                $('.iva-0').selectpicker('render');
            }
        })
    }
</script>
