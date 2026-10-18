<script type="module">
    $(document).ready(function() {
        mostrarValores();
        marcarPadres();
    });

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

    $('#update-btn').click(function () {
        var nombre = $('#name').val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el rol ' + nombre + '?',
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

    function mostrarValores() {
        //Para marcar los permisos activos en el rol
        const permisos = {!! json_encode($permisos) !!};
        for (let index = 0; index < permisos.length; index++) {
            $('input[name="permission[' + permisos[index].id + ']"]').prop('checked', true);
        }
    };

    function marcarPadres() {


        const p_alumnos = {!! json_encode($p_alumnos) !!};
        var count_alumnos = p_alumnos.length;
        var count_hijos_alumnos =  $('.hijo-alumnos').filter(':checked').length
        if (count_alumnos == count_hijos_alumnos) {
            $('#chk_alumnos').prop('checked', true);
        }


        const p_docentes = {!! json_encode($p_docentes) !!};
        var count_docentes = p_docentes.length;
        var count_hijos_docentes =  $('.hijo-docentes').filter(':checked').length
        if (count_docentes == count_hijos_docentes) {
            $('#chk_docentes').prop('checked', true);
        }






        const p_convalidaciones = {!! json_encode($p_convalidaciones) !!};
        var count_convalidaciones = p_convalidaciones.length;
        var count_hijos_convalidaciones =  $('.hijo-convalidaciones').filter(':checked').length
        if (count_convalidaciones == count_hijos_convalidaciones) {
            $('#chk_convalidaciones').prop('checked', true);
        }



        const p_extensiones_universitarias = {!! json_encode($p_extensiones_universitarias) !!};
        var count_extensiones_universitarias = p_extensiones_universitarias.length;
        var count_hijos_extensiones_universitarias =  $('.hijo-extensiones_universitarias').filter(':checked').length
        if (count_extensiones_universitarias == count_hijos_extensiones_universitarias) {
            $('#chk_extensiones_universitarias').prop('checked', true);
        }



        const p_parametros_academicos = {!! json_encode($p_parametros_academicos) !!};
        var count_parametros_academicos = p_parametros_academicos.length;
        var count_hijos_parametros_academicos =  $('.hijo-parametros_academicos').filter(':checked').length
        if (count_parametros_academicos == count_hijos_parametros_academicos) {
            $('#chk_parametros_academicos').prop('checked', true);
        }

        const p_programas = {!! json_encode($p_programas) !!};
        var count_programas = p_programas.length;
        var count_hijos_programas =  $('.hijo-programas').filter(':checked').length
        if (count_programas == count_hijos_programas) {
            $('#chk_programas').prop('checked', true);
        }

        const p_facultades = {!! json_encode($p_facultades) !!};
        var count_facultades = p_facultades.length;
        var count_hijos_facultades =  $('.hijo-facultades').filter(':checked').length
        if (count_facultades == count_hijos_facultades) {
            $('#chk_facultades').prop('checked', true);
        }

        const p_carreras = {!! json_encode($p_carreras) !!};
        var count_carreras = p_carreras.length;
        var count_hijos_carreras =  $('.hijo-carreras').filter(':checked').length
        if (count_carreras == count_hijos_carreras) {
            $('#chk_carreras').prop('checked', true);
        }

        const p_materias = {!! json_encode($p_materias) !!};
        var count_materias = p_materias.length;
        var count_hijos_materias =  $('.hijo-materias').filter(':checked').length
        if (count_materias == count_hijos_materias) {
            $('#chk_materias').prop('checked', true);
        }


        const p_semestres = {!! json_encode($p_semestres) !!};
        var count_semestres = p_semestres.length;
        var count_hijos_semestres =  $('.hijo-semestres').filter(':checked').length
        if (count_semestres == count_hijos_semestres) {
            $('#chk_semestres').prop('checked', true);
        }





        const p_modalidades = {!! json_encode($p_modalidades) !!};
        var count_modalidades = p_modalidades.length;
        var count_hijos_modalidades =  $('.hijo-modalidades').filter(':checked').length
        if (count_modalidades == count_hijos_modalidades) {
            $('#chk_modalidades').prop('checked', true);
        }

        const p_formaciones_academicas = {!! json_encode($p_formaciones_academicas) !!};
        var count_formaciones_academicas = p_formaciones_academicas.length;
        var count_hijos_formaciones_academicas =  $('.hijo-formaciones_academicas').filter(':checked').length
        if (count_formaciones_academicas == count_hijos_formaciones_academicas) {
            $('#chk_formaciones_academicas').prop('checked', true);
        }

        const p_instituciones_educativas = {!! json_encode($p_instituciones_educativas) !!};
        var count_instituciones_educativas = p_instituciones_educativas.length;
        var count_hijos_instituciones_educativas =  $('.hijo-instituciones_educativas').filter(':checked').length
        if (count_instituciones_educativas == count_hijos_instituciones_educativas) {
            $('#chk_instituciones_educativas').prop('checked', true);
        }


        const p_areas_conocimientos = {!! json_encode($p_areas_conocimientos) !!};
        var count_areas_conocimientos = p_areas_conocimientos.length;
        var count_hijos_areas_conocimientos =  $('.hijo-areas_conocimientos').filter(':checked').length
        if (count_areas_conocimientos == count_hijos_areas_conocimientos) {
            $('#chk_areas_conocimientos').prop('checked', true);
        }

        const p_reportes_academicos = {!! json_encode($p_reportes_academicos) !!};
        var count_reportes_academicos = p_reportes_academicos.length;
        var count_hijos_reportes_academicos =  $('.hijo-reportes_academicos').filter(':checked').length
        if (count_reportes_academicos == count_hijos_reportes_academicos) {
            $('#chk_reportes_academicos').prop('checked', true);
        }






































        const p_usuarios = {!! json_encode($p_usuarios) !!};
        var count_usuarios = p_usuarios.length;
        var count_hijos_usuarios =  $('.hijo-usuarios').filter(':checked').length
        if (count_usuarios == count_hijos_usuarios) {
            $('#chk_usuarios').prop('checked', true);
        }

        const p_roles = {!! json_encode($p_roles) !!};
        var count_roles = p_roles.length;
        var count_hijos_roles =  $('.hijo-roles').filter(':checked').length
        if (count_roles == count_hijos_roles) {
            $('#chk_roles').prop('checked', true);
        }

        const p_permisos = {!! json_encode($p_permisos) !!};
        var count_permisos = p_permisos.length;
        var count_hijos_permisos =  $('.hijo-permisos').filter(':checked').length
        if (count_permisos == count_hijos_permisos) {
            $('#chk_permisos').prop('checked', true);
        }





        const p_nacionalidades = {!! json_encode($p_nacionalidades) !!};
        var count_nacionalidades = p_nacionalidades.length;
        var count_hijos_nacionalidades =  $('.hijo-nacionalidades').filter(':checked').length
        if (count_nacionalidades == count_hijos_nacionalidades) {
            $('#chk_nacionalidades').prop('checked', true);
        }


        const p_departamentos_paraguay = {!! json_encode($p_departamentos_paraguay) !!};
        var count_departamentos_paraguay = p_departamentos_paraguay.length;
        var count_hijos_departamentos_paraguay =  $('.hijo-departamentos_paraguay').filter(':checked').length
        if (count_departamentos_paraguay == count_hijos_departamentos_paraguay) {
            $('#chk_departamentos_paraguay').prop('checked', true);
        }

        const p_ciudades = {!! json_encode($p_ciudades) !!};
        var count_ciudades = p_ciudades.length;
        var count_hijos_ciudades =  $('.hijo-ciudades').filter(':checked').length
        if (count_ciudades == count_hijos_ciudades) {
            $('#chk_ciudades').prop('checked', true);
        }

        const p_barrios = {!! json_encode($p_barrios) !!};
        var count_barrios = p_barrios.length;
        var count_hijos_barrios =  $('.hijo-barrios').filter(':checked').length
        if (count_barrios == count_hijos_barrios) {
            $('#chk_barrios').prop('checked', true);
        }


        const p_formas_conocimientos = {!! json_encode($p_formas_conocimientos) !!};
        var count_formas_conocimientos = p_formas_conocimientos.length;
        var count_hijos_formas_conocimientos =  $('.hijo-formas_conocimientos').filter(':checked').length
        if (count_formas_conocimientos == count_hijos_formas_conocimientos) {
            $('#chk_formas_conocimientos').prop('checked', true);
        }

        const p_alumnos_pantalla = {!! json_encode($p_alumnos_pantalla) !!};
        var count_alumnos_pantalla = p_alumnos_pantalla.length;
        var count_hijos_alumnos_pantalla =  $('.hijo-alumnos_pantalla').filter(':checked').length
        if (count_alumnos_pantalla == count_hijos_alumnos_pantalla) {
            $('#chk_alumnos_pantalla').prop('checked', true);
        }

        const p_docentes_pantalla = {!! json_encode($p_docentes_pantalla) !!};
        var count_docentes_pantalla = p_docentes_pantalla.length;
        var count_hijos_docentes_pantalla =  $('.hijo-docentes_pantalla').filter(':checked').length
        if (count_docentes_pantalla == count_hijos_docentes_pantalla) {
            $('#chk_docentes_pantalla').prop('checked', true);
        }
    }

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

        $(".padre").click(function() {
            var padre = ($(this).attr("id"));
            var hijo = padre.split('chk_')[1];

            $("#" + padre).change(function () {
                var cantidad_hijos = $(".hijo-" + hijo).toArray().length;
                if ($("#" + padre).is(':checked')) {
                    $(".hijo-" + hijo).prop('checked', true);
                } else {
                    $(".hijo-" + hijo).prop('checked', false);
                }
                contarPermisos();
            });
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
</script>
