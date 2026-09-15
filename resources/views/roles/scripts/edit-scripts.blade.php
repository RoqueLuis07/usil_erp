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
        const p_cajeros = {!! json_encode($p_cajeros) !!};
        var count_cajeros = p_cajeros.length;
        var count_hijos_cajeros =  $('.hijo-cajeros').filter(':checked').length
        if (count_cajeros == count_hijos_cajeros) {
            $('#chk_cajeros').prop('checked', true);
        }

        const p_clientes = {!! json_encode($p_clientes) !!};
        var count_clientes = p_clientes.length;
        var count_hijos_clientes =  $('.hijo-clientes').filter(':checked').length
        if (count_clientes == count_hijos_clientes) {
            $('#chk_clientes').prop('checked', true);
        }

        const p_alumnos = {!! json_encode($p_alumnos) !!};
        var count_alumnos = p_alumnos.length;
        var count_hijos_alumnos =  $('.hijo-alumnos').filter(':checked').length
        if (count_alumnos == count_hijos_alumnos) {
            $('#chk_alumnos').prop('checked', true);
        }

        const p_matriculaciones = {!! json_encode($p_matriculaciones) !!};
        var count_matriculaciones = p_matriculaciones.length;
        var count_hijos_matriculaciones =  $('.hijo-matriculaciones').filter(':checked').length
        if (count_matriculaciones == count_hijos_matriculaciones) {
            $('#chk_matriculaciones').prop('checked', true);
        }

        const p_docentes = {!! json_encode($p_docentes) !!};
        var count_docentes = p_docentes.length;
        var count_hijos_docentes =  $('.hijo-docentes').filter(':checked').length
        if (count_docentes == count_hijos_docentes) {
            $('#chk_docentes').prop('checked', true);
        }

        const p_examenes_suficiencia = {!! json_encode($p_examenes_suficiencia) !!};
        var count_examenes_suficiencia = p_examenes_suficiencia.length;
        var count_hijos_examenes_suficiencia =  $('.hijo-examenes_suficiencia').filter(':checked').length
        if (count_examenes_suficiencia == count_hijos_examenes_suficiencia) {
            $('#chk_examenes_suficiencia').prop('checked', true);
        }

        const p_materias_semestres = {!! json_encode($p_materias_semestres) !!};
        var count_materias_semestres = p_materias_semestres.length;
        var count_hijos_materias_semestres =  $('.hijo-materias_semestres').filter(':checked').length
        if (count_materias_semestres == count_hijos_materias_semestres) {
            $('#chk_materias_semestres').prop('checked', true);
        }

        const p_actas = {!! json_encode($p_actas) !!};
        var count_actas = p_actas.length;
        var count_hijos_actas =  $('.hijo-actas').filter(':checked').length
        if (count_actas == count_hijos_actas) {
            $('#chk_actas').prop('checked', true);
        }

        const p_materias_clases = {!! json_encode($p_materias_clases) !!};
        var count_materias_clases = p_materias_clases.length;
        var count_hijos_materias_clases =  $('.hijo-materias_clases').filter(':checked').length
        if (count_materias_clases == count_hijos_materias_clases) {
            $('#chk_materias_clases').prop('checked', true);
        }

        const p_anulaciones_correlatividades = {!! json_encode($p_anulaciones_correlatividades) !!};
        var count_anulaciones_correlatividades = p_anulaciones_correlatividades.length;
        var count_hijos_anulaciones_correlatividades =  $('.hijo-anulaciones_correlatividades').filter(':checked').length
        if (count_anulaciones_correlatividades == count_hijos_anulaciones_correlatividades) {
            $('#chk_anulaciones_correlatividades').prop('checked', true);
        }

        const p_convalidaciones = {!! json_encode($p_convalidaciones) !!};
        var count_convalidaciones = p_convalidaciones.length;
        var count_hijos_convalidaciones =  $('.hijo-convalidaciones').filter(':checked').length
        if (count_convalidaciones == count_hijos_convalidaciones) {
            $('#chk_convalidaciones').prop('checked', true);
        }

        const p_tesis = {!! json_encode($p_tesis) !!};
        var count_tesis = p_tesis.length;
        var count_hijos_tesis =  $('.hijo-tesis').filter(':checked').length
        if (count_tesis == count_hijos_tesis) {
            $('#chk_tesis').prop('checked', true);
        }

        const p_tutorias = {!! json_encode($p_tutorias) !!};
        var count_tutorias = p_tutorias.length;
        var count_hijos_tutorias =  $('.hijo-tutorias').filter(':checked').length
        if (count_tutorias == count_hijos_tutorias) {
            $('#chk_tutorias').prop('checked', true);
        }

        const p_extensiones_universitarias = {!! json_encode($p_extensiones_universitarias) !!};
        var count_extensiones_universitarias = p_extensiones_universitarias.length;
        var count_hijos_extensiones_universitarias =  $('.hijo-extensiones_universitarias').filter(':checked').length
        if (count_extensiones_universitarias == count_hijos_extensiones_universitarias) {
            $('#chk_extensiones_universitarias').prop('checked', true);
        }

        const p_solicitudes = {!! json_encode($p_solicitudes) !!};
        var count_solicitudes = p_solicitudes.length;
        var count_hijos_solicitudes =  $('.hijo-solicitudes').filter(':checked').length
        if (count_solicitudes == count_hijos_solicitudes) {
            $('#chk_solicitudes').prop('checked', true);
        }

        const p_encuestas = {!! json_encode($p_encuestas) !!};
        var count_encuestas = p_encuestas.length;
        var count_hijos_encuestas =  $('.hijo-encuestas').filter(':checked').length
        if (count_encuestas == count_hijos_encuestas) {
            $('#chk_encuestas').prop('checked', true);
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

        const p_materias_suficiencia = {!! json_encode($p_materias_suficiencia) !!};
        var count_materias_suficiencia = p_materias_suficiencia.length;
        var count_hijos_materias_suficiencia =  $('.hijo-materias_suficiencia').filter(':checked').length
        if (count_materias_suficiencia == count_hijos_materias_suficiencia) {
            $('#chk_materias_suficiencia').prop('checked', true);
        }

        const p_semestres = {!! json_encode($p_semestres) !!};
        var count_semestres = p_semestres.length;
        var count_hijos_semestres =  $('.hijo-semestres').filter(':checked').length
        if (count_semestres == count_hijos_semestres) {
            $('#chk_semestres').prop('checked', true);
        }

        const p_mallas = {!! json_encode($p_mallas) !!};
        var count_mallas = p_mallas.length;
        var count_hijos_mallas =  $('.hijo-mallas').filter(':checked').length
        if (count_mallas == count_hijos_mallas) {
            $('#chk_mallas').prop('checked', true);
        }

        const p_mallas_espejo = {!! json_encode($p_mallas_espejo) !!};
        var count_mallas_espejo = p_mallas_espejo.length;
        var count_hijos_mallas_espejo =  $('.hijo-mallas_espejo').filter(':checked').length
        if (count_mallas_espejo == count_hijos_mallas_espejo) {
            $('#chk_mallas_espejo').prop('checked', true);
        }

        const p_escalas = {!! json_encode($p_escalas) !!};
        var count_escalas = p_escalas.length;
        var count_hijos_escalas =  $('.hijo-escalas').filter(':checked').length
        if (count_escalas == count_hijos_escalas) {
            $('#chk_escalas').prop('checked', true);
        }

        const p_evaluaciones = {!! json_encode($p_evaluaciones) !!};
        var count_evaluaciones = p_evaluaciones.length;
        var count_hijos_evaluaciones =  $('.hijo-evaluaciones').filter(':checked').length
        if (count_evaluaciones == count_hijos_evaluaciones) {
            $('#chk_evaluaciones').prop('checked', true);
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

        const p_fechas_desmatriculaciones = {!! json_encode($p_fechas_desmatriculaciones) !!};
        var count_fechas_desmatriculaciones = p_fechas_desmatriculaciones.length;
        var count_hijos_fechas_desmatriculaciones =  $('.hijo-fechas_desmatriculaciones').filter(':checked').length
        if (count_fechas_desmatriculaciones == count_hijos_fechas_desmatriculaciones) {
            $('#chk_fechas_desmatriculaciones').prop('checked', true);
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

        const p_alumnos_ubs = {!! json_encode($p_alumnos_ubs) !!};
        var count_alumnos_ubs = p_alumnos_ubs.length;
        var count_hijos_alumnos_ubs =  $('.hijo-alumnos_ubs').filter(':checked').length
        if (count_alumnos_ubs == count_hijos_alumnos_ubs) {
            $('#chk_alumnos_ubs').prop('checked', true);
        }

        const p_inscripciones_ubs = {!! json_encode($p_inscripciones_ubs) !!};
        var count_inscripciones_ubs = p_inscripciones_ubs.length;
        var count_hijos_inscripciones_ubs =  $('.hijo-inscripciones_ubs').filter(':checked').length
        if (count_inscripciones_ubs == count_hijos_inscripciones_ubs) {
            $('#chk_inscripciones_ubs').prop('checked', true);
        }

        const p_cursos_ubs = {!! json_encode($p_cursos_ubs) !!};
        var count_cursos_ubs = p_cursos_ubs.length;
        var count_hijos_cursos_ubs =  $('.hijo-cursos_ubs').filter(':checked').length
        if (count_cursos_ubs == count_hijos_cursos_ubs) {
            $('#chk_cursos_ubs').prop('checked', true);
        }

        const p_modulos_ubs = {!! json_encode($p_modulos_ubs) !!};
        var count_modulos_ubs = p_modulos_ubs.length;
        var count_hijos_modulos_ubs =  $('.hijo-modulos_ubs').filter(':checked').length
        if (count_modulos_ubs == count_hijos_modulos_ubs) {
            $('#chk_modulos_ubs').prop('checked', true);
        }

        const p_tipos_cursos_ubs = {!! json_encode($p_tipos_cursos_ubs) !!};
        var count_tipos_cursos_ubs = p_tipos_cursos_ubs.length;
        var count_hijos_tipos_cursos_ubs =  $('.hijo-tipos_cursos_ubs').filter(':checked').length
        if (count_tipos_cursos_ubs == count_hijos_tipos_cursos_ubs) {
            $('#chk_tipos_cursos_ubs').prop('checked', true);
        }

        const p_maestrias_ubs = {!! json_encode($p_maestrias_ubs) !!};
        var count_maestrias_ubs = p_maestrias_ubs.length;
        var count_hijos_maestrias_ubs =  $('.hijo-maestrias_ubs').filter(':checked').length
        if (count_maestrias_ubs == count_hijos_maestrias_ubs) {
            $('#chk_maestrias_ubs').prop('checked', true);
        }

        const p_tesis_ubs = {!! json_encode($p_tesis_ubs) !!};
        var count_tesis_ubs = p_tesis_ubs.length;
        var count_hijos_tesis_ubs =  $('.hijo-tesis_ubs').filter(':checked').length
        if (count_tesis_ubs == count_hijos_tesis_ubs) {
            $('#chk_tesis_ubs').prop('checked', true);
        }

        const p_extensiones_ubs = {!! json_encode($p_extensiones_ubs) !!};
        var count_extensiones_ubs = p_extensiones_ubs.length;
        var count_hijos_extensiones_ubs =  $('.hijo-extensiones_ubs').filter(':checked').length
        if (count_extensiones_ubs == count_hijos_extensiones_ubs) {
            $('#chk_extensiones_ubs').prop('checked', true);
        }

        const p_docentes_ubs = {!! json_encode($p_docentes_ubs) !!};
        var count_docentes_ubs = p_docentes_ubs.length;
        var count_hijos_docentes_ubs =  $('.hijo-docentes_ubs').filter(':checked').length
        if (count_docentes_ubs == count_hijos_docentes_ubs) {
            $('#chk_docentes_ubs').prop('checked', true);
        }

        const p_ventas = {!! json_encode($p_ventas) !!};
        var count_ventas = p_ventas.length;
        var count_hijos_ventas =  $('.hijo-ventas').filter(':checked').length
        if (count_ventas == count_hijos_ventas) {
            $('#chk_ventas').prop('checked', true);
        }

        const p_recibos = {!! json_encode($p_recibos) !!};
        var count_recibos = p_recibos.length;
        var count_hijos_recibos =  $('.hijo-recibos').filter(':checked').length
        if (count_recibos == count_hijos_recibos) {
            $('#chk_recibos').prop('checked', true);
        }

        const p_cobros = {!! json_encode($p_cobros) !!};
        var count_cobros = p_cobros.length;
        var count_hijos_cobros =  $('.hijo-cobros').filter(':checked').length
        if (count_cobros == count_hijos_cobros) {
            $('#chk_cobros').prop('checked', true);
        }

        const p_notas_creditos = {!! json_encode($p_notas_creditos) !!};
        var count_notas_creditos = p_notas_creditos.length;
        var count_hijos_notas_creditos =  $('.hijo-notas_creditos').filter(':checked').length
        if (count_notas_creditos == count_hijos_notas_creditos) {
            $('#chk_notas_creditos').prop('checked', true);
        }

        const p_cajas_arqueos = {!! json_encode($p_cajas_arqueos) !!};
        var count_cajas_arqueos = p_cajas_arqueos.length;
        var count_hijos_cajas_arqueos =  $('.hijo-cajas_arqueos').filter(':checked').length
        if (count_cajas_arqueos == count_hijos_cajas_arqueos) {
            $('#chk_cajas_arqueos').prop('checked', true);
        }

        const p_compras_ordenes = {!! json_encode($p_compras_ordenes) !!};
        var count_compras_ordenes = p_compras_ordenes.length;
        var count_hijos_compras_ordenes =  $('.hijo-compras_ordenes').filter(':checked').length
        if (count_compras_ordenes == count_hijos_compras_ordenes) {
            $('#chk_compras_ordenes').prop('checked', true);
        }

        const p_compras = {!! json_encode($p_compras) !!};
        var count_compras = p_compras.length;
        var count_hijos_compras =  $('.hijo-compras').filter(':checked').length
        if (count_compras == count_hijos_compras) {
            $('#chk_compras').prop('checked', true);
        }

        const p_pagos_ordenes = {!! json_encode($p_pagos_ordenes) !!};
        var count_pagos_ordenes = p_pagos_ordenes.length;
        var count_hijos_pagos_ordenes =  $('.hijo-pagos_ordenes').filter(':checked').length
        if (count_pagos_ordenes == count_hijos_pagos_ordenes) {
            $('#chk_pagos_ordenes').prop('checked', true);
        }

        const p_pagos = {!! json_encode($p_pagos) !!};
        var count_pagos = p_pagos.length;
        var count_hijos_pagos =  $('.hijo-pagos').filter(':checked').length
        if (count_pagos == count_hijos_pagos) {
            $('#chk_pagos').prop('checked', true);
        }

        const p_proveedores = {!! json_encode($p_proveedores) !!};
        var count_proveedores = p_proveedores.length;
        var count_hijos_proveedores =  $('.hijo-proveedores').filter(':checked').length
        if (count_proveedores == count_hijos_proveedores) {
            $('#chk_proveedores').prop('checked', true);
        }

        const p_asientos_contables = {!! json_encode($p_asientos_contables) !!};
        var count_asientos_contables = p_asientos_contables.length;
        var count_hijos_asientos_contables =  $('.hijo-asientos_contables').filter(':checked').length
        if (count_asientos_contables == count_hijos_asientos_contables) {
            $('#chk_asientos_contables').prop('checked', true);
        }

        const p_cuentas_contables_saldos = {!! json_encode($p_cuentas_contables_saldos) !!};
        var count_cuentas_contables_saldos = p_cuentas_contables_saldos.length;
        var count_hijos_cuentas_contables_saldos =  $('.hijo-cuentas_contables_saldos').filter(':checked').length
        if (count_cuentas_contables_saldos == count_hijos_cuentas_contables_saldos) {
            $('#chk_cuentas_contables_saldos').prop('checked', true);
        }

        const p_articulos = {!! json_encode($p_articulos) !!};
        var count_articulos = p_articulos.length;
        var count_hijos_articulos =  $('.hijo-articulos').filter(':checked').length
        if (count_articulos == count_hijos_articulos) {
            $('#chk_articulos').prop('checked', true);
        }

        const p_cuentas_contables = {!! json_encode($p_cuentas_contables) !!};
        var count_cuentas_contables = p_cuentas_contables.length;
        var count_hijos_cuentas_contables =  $('.hijo-cuentas_contables').filter(':checked').length
        if (count_cuentas_contables == count_hijos_cuentas_contables) {
            $('#chk_cuentas_contables').prop('checked', true);
        }

        const p_tipos_documentos_contables = {!! json_encode($p_tipos_documentos_contables) !!};
        var count_tipos_documentos_contables = p_tipos_documentos_contables.length;
        var count_hijos_tipos_documentos_contables =  $('.hijo-tipos_documentos_contables').filter(':checked').length
        if (count_tipos_documentos_contables == count_hijos_tipos_documentos_contables) {
            $('#chk_tipos_documentos_contables').prop('checked', true);
        }

        const p_centros_costos_contables = {!! json_encode($p_centros_costos_contables) !!};
        var count_centros_costos_contables = p_centros_costos_contables.length;
        var count_hijos_centros_costos_contables =  $('.hijo-centros_costos_contables').filter(':checked').length
        if (count_centros_costos_contables == count_hijos_centros_costos_contables) {
            $('#chk_centros_costos_contables').prop('checked', true);
        }

        const p_unidades_negocios_contables = {!! json_encode($p_unidades_negocios_contables) !!};
        var count_unidades_negocios_contables = p_unidades_negocios_contables.length;
        var count_hijos_unidades_negocios_contables =  $('.hijo-unidades_negocios_contables').filter(':checked').length
        if (count_unidades_negocios_contables == count_hijos_unidades_negocios_contables) {
            $('#chk_unidades_negocios_contables').prop('checked', true);
        }

        const p_cajas = {!! json_encode($p_cajas) !!};
        var count_cajas = p_cajas.length;
        var count_hijos_cajas =  $('.hijo-cajas').filter(':checked').length
        if (count_cajas == count_hijos_cajas) {
            $('#chk_cajas').prop('checked', true);
        }

        const p_cajas_movimientos = {!! json_encode($p_cajas_movimientos) !!};
        var count_cajas_movimientos = p_cajas_movimientos.length;
        var count_hijos_cajas_movimientos =  $('.hijo-cajas_movimientos').filter(':checked').length
        if (count_cajas_movimientos == count_hijos_cajas_movimientos) {
            $('#chk_cajas_movimientos').prop('checked', true);
        }

        const p_movimientos_cuentas = {!! json_encode($p_movimientos_cuentas) !!};
        var count_movimientos_cuentas = p_movimientos_cuentas.length;
        var count_hijos_movimientos_cuentas =  $('.hijo-movimientos_cuentas').filter(':checked').length
        if (count_movimientos_cuentas == count_hijos_movimientos_cuentas) {
            $('#chk_movimientos_cuentas').prop('checked', true);
        }

        const p_cajas_cuentas_movimientos = {!! json_encode($p_cajas_cuentas_movimientos) !!};
        var count_cajas_cuentas_movimientos = p_cajas_cuentas_movimientos.length;
        var count_hijos_cajas_cuentas_movimientos =  $('.hijo-cajas_cuentas_movimientos').filter(':checked').length
        if (count_cajas_cuentas_movimientos == count_hijos_cajas_cuentas_movimientos) {
            $('#chk_cajas_cuentas_movimientos').prop('checked', true);
        }

        const p_cuentas_bancarias = {!! json_encode($p_cuentas_bancarias) !!};
        var count_cuentas_bancarias = p_cuentas_bancarias.length;
        var count_hijos_cuentas_bancarias =  $('.hijo-cuentas_bancarias').filter(':checked').length
        if (count_cuentas_bancarias == count_hijos_cuentas_bancarias) {
            $('#chk_cuentas_bancarias').prop('checked', true);
        }

        const p_bancos = {!! json_encode($p_bancos) !!};
        var count_bancos = p_bancos.length;
        var count_hijos_bancos =  $('.hijo-bancos').filter(':checked').length
        if (count_bancos == count_hijos_bancos) {
            $('#chk_bancos').prop('checked', true);
        }

        const p_cotizaciones = {!! json_encode($p_cotizaciones) !!};
        var count_cotizaciones = p_cotizaciones.length;
        var count_hijos_cotizaciones =  $('.hijo-cotizaciones').filter(':checked').length
        if (count_cotizaciones == count_hijos_cotizaciones) {
            $('#chk_cotizaciones').prop('checked', true);
        }

        const p_convenios = {!! json_encode($p_convenios) !!};
        var count_convenios = p_convenios.length;
        var count_hijos_convenios =  $('.hijo-convenios').filter(':checked').length
        if (count_convenios == count_hijos_convenios) {
            $('#chk_convenios').prop('checked', true);
        }

        const p_pagos_formas = {!! json_encode($p_pagos_formas) !!};
        var count_pagos_formas = p_pagos_formas.length;
        var count_hijos_pagos_formas =  $('.hijo-pagos_formas').filter(':checked').length
        if (count_pagos_formas == count_hijos_pagos_formas) {
            $('#chk_pagos_formas').prop('checked', true);
        }

        const p_monedas = {!! json_encode($p_monedas) !!};
        var count_monedas = p_monedas.length;
        var count_hijos_monedas =  $('.hijo-monedas').filter(':checked').length
        if (count_monedas == count_hijos_monedas) {
            $('#chk_monedas').prop('checked', true);
        }

        const p_empleados = {!! json_encode($p_empleados) !!};
        var count_empleados = p_empleados.length;
        var count_hijos_empleados =  $('.hijo-empleados').filter(':checked').length
        if (count_empleados == count_hijos_empleados) {
            $('#chk_empleados').prop('checked', true);
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

        const p_noticias_avisos = {!! json_encode($p_noticias_avisos) !!};
        var count_noticias_avisos = p_noticias_avisos.length;
        var count_hijos_noticias_avisos =  $('.hijo-noticias_avisos').filter(':checked').length
        if (count_noticias_avisos == count_hijos_noticias_avisos) {
            $('#chk_noticias_avisos').prop('checked', true);
        }

        const p_empresas = {!! json_encode($p_empresas) !!};
        var count_empresas = p_empresas.length;
        var count_hijos_empresas =  $('.hijo-empresas').filter(':checked').length
        if (count_empresas == count_hijos_empresas) {
            $('#chk_empresas').prop('checked', true);
        }

        const p_puntos_impresiones = {!! json_encode($p_puntos_impresiones) !!};
        var count_puntos_impresiones = p_puntos_impresiones.length;
        var count_hijos_puntos_impresiones =  $('.hijo-puntos_impresiones').filter(':checked').length
        if (count_puntos_impresiones == count_hijos_puntos_impresiones) {
            $('#chk_puntos_impresiones').prop('checked', true);
        }

        const p_timbrados = {!! json_encode($p_timbrados) !!};
        var count_timbrados = p_timbrados.length;
        var count_hijos_timbrados =  $('.hijo-timbrados').filter(':checked').length
        if (count_timbrados == count_hijos_timbrados) {
            $('#chk_timbrados').prop('checked', true);
        }

        const p_nacionalidades = {!! json_encode($p_nacionalidades) !!};
        var count_nacionalidades = p_nacionalidades.length;
        var count_hijos_nacionalidades =  $('.hijo-nacionalidades').filter(':checked').length
        if (count_nacionalidades == count_hijos_nacionalidades) {
            $('#chk_nacionalidades').prop('checked', true);
        }

        const p_paises = {!! json_encode($p_paises) !!};
        var count_paises = p_paises.length;
        var count_hijos_paises =  $('.hijo-paises').filter(':checked').length
        if (count_paises == count_hijos_paises) {
            $('#chk_paises').prop('checked', true);
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

        const p_tipos_movimientos = {!! json_encode($p_tipos_movimientos) !!};
        var count_tipos_movimientos = p_tipos_movimientos.length;
        var count_hijos_tipos_movimientos =  $('.hijo-tipos_movimientos').filter(':checked').length
        if (count_tipos_movimientos == count_hijos_tipos_movimientos) {
            $('#chk_tipos_movimientos').prop('checked', true);
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
