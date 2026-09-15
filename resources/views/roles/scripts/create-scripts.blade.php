<script type="module">
    $(document).ready(function () {
        setTimeout(() => { $('.alert').remove(); }, 3000);
    });

    $('input').on('focus', function () {
        $(this).removeClass('is-invalid');
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
                window.location.href = '{{route('roles.index')}}';
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
                $('#name').val('');
                $('#deseleccionar_todo').trigger('click');
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
            title: '¿Está seguro de guardar el nuevo usuario?',
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

    function contarPermisos() {
        var cant_permisos = $('.hijo').filter(':checked').length
        $('#cant_permisos').val(cant_permisos);
    }

    // Para Seleccionar/Deseleccionar todos los checkboxs
    $("#seleccionar_todo").click(function () {
        $(".form-check-input").prop('checked', true);
        contarPermisos();
    });

    $("#deseleccionar_todo").click(function () {
        $(".form-check-input").prop('checked', false);
        $(".padre").prop('checked', false);
        contarPermisos();
    });

    $("input.padre").click(function() {
        var padre = ($(this).attr("id"));
        var hijo = padre.split('chk_')[1];

        $("#" + padre).change(function () {
            if ($("#" + padre).is(':checked')) {
                $(".hijo-" + hijo).prop('checked', true);
            } else {
                $(".hijo-" + hijo).prop('checked', false);
            }
            contarPermisos();
        });

        $('.hijo').change(function() {
            contarPermisos();
            var hijo_class = $(this).prop('class');
            var hijo_class_nombre = hijo_class.substring(hijo_class.indexOf("form-check-input") + 27, hijo_class.lastIndexOf(""));
            var count_hijos = $('.hijo-' + hijo_class_nombre).length
            var count_hijo_checked =  $('.hijo-' + hijo_class_nombre).filter(':checked').length
            if (count_hijos > count_hijo_checked) {
                $('#chk_' + hijo_class_nombre).prop('checked', false);
            }
            else {
                $('#chk_' + hijo_class_nombre).prop('checked', true);
            }
        });
    });
</script>
