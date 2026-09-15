<script type="module">
    var materias_semestresList;
    $(document).ready(function() {
        var options = {
            valueNames: ['semestre', 'materia', 'docente', 'carrera', 'programa', 'periodo', 'inscriptos'],
            page: 50,
            pagination: true,
        };
        materias_semestresList = new List('materias_semestres-list', options);
        materias_semestresList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }

            console.log(list.matchingItems.length);

            cantidad_mostrada(list.matchingItems.length)
        });

        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }

        cantidad_mostrada(materias_semestresList.size());

        var periodo_activo = {!!json_encode($periodo_activo, JSON_HEX_TAG) !!}

        if (periodo_activo) {
            $('#filtro_periodo').selectpicker('val', periodo_activo);
            $('#filtro_periodo').trigger('change');
        }
    });

    if (document.querySelector(".pagination-next"))
    document.querySelector(".pagination-next").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : '' : '';
    });

    if (document.querySelector(".pagination-prev"))
    document.querySelector(".pagination-prev").addEventListener("click", function () {
        (document.querySelector(".pagination.listjs-pagination")) ? (document.querySelector(".pagination.listjs-pagination").querySelector(".active")) ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : '' : '';
    });

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

    function cantidad_mostrada(total_lista) {
	  var total = total_lista;
      var tamanho_pagina = 50;
      var pagina_actual = materias_semestresList.i;

      var inicio = materias_semestresList.i;
	  var fin = materias_semestresList.i + tamanho_pagina - 1;

      if (fin > total) {
          fin = total;
      }

      $("#mostrando").text('Mostrando ' + inicio + ' a ' + fin + ' de ' + total + ' resultados');
    }

    $('#delete-semestre-filter-btn').on('click', function () {
        $('#filtro_semestre').selectpicker('val', '');
        $('#filtro_semestre').trigger('change');
    })

    $('#delete-periodo-filter-btn').on('click', function () {
        $('#filtro_periodo').selectpicker('val', '');
        $('#filtro_periodo').trigger('change');
    })

    $('#delete-docente-filter-btn').on('click', function () {
        $('#filtro_docente').selectpicker('val', '');
        $('#filtro_docente').trigger('change');
    })

    $('#delete-carrera-filter-btn').on('click', function () {
        $('#filtro_carrera').selectpicker('val', '');
        $('#filtro_carrera').trigger('change');
    })

    $('#delete-programa-filter-btn').on('click', function () {
        $('#filtro_programa').selectpicker('val', '');
        $('#filtro_programa').trigger('change');
    })

    $('#delete-inscriptos-filter-btn').on('click', function () {
        $('#filtro_inscriptos').selectpicker('val', '');
        $('#filtro_inscriptos').trigger('change');
    })

    $('#filtro_semestre').change(aplicarFiltros);
    $('#filtro_periodo').change(aplicarFiltros);
    $('#filtro_docente').change(aplicarFiltros);
    $('#filtro_carrera').change(aplicarFiltros);
    $('#filtro_programa').change(aplicarFiltros);
    $('#filtro_inscriptos').change(aplicarFiltros);

    function aplicarFiltros() {
        var semestreSeleccionado = $('#filtro_semestre option:selected').val();
        var periodoSeleccionado = $('#filtro_periodo option:selected').val();
        var docenteSeleccionado = $('#filtro_docente option:selected').val();
        var carreraSeleccionado = $('#filtro_carrera option:selected').val();
        var programaSeleccionado = $('#filtro_programa option:selected').val();
        var inscriptosSeleccionado = $('#filtro_inscriptos option:selected').val();

        var inscriptosMatch;

        inscriptosMatch = function(item) {
            return true;
        };

        if (inscriptosSeleccionado) {
            inscriptosSeleccionado = parseInt(inscriptosSeleccionado);

            var rango_min = 0;
            var rango_max = 0;

            if (inscriptosSeleccionado == 10) {
                rango_min = 0;
                rango_max = 10;
            } else if (inscriptosSeleccionado == 20) {
                rango_min = 11;
                rango_max = 20;
            } else if (inscriptosSeleccionado == 30) {
                rango_min = 21;
                rango_max = 30;
            } else if (inscriptosSeleccionado == 40) {
                rango_min = 31;
                rango_max = 40;
            } else if (inscriptosSeleccionado == 50) {
                rango_min = 41;
                rango_max = 50;
            } else if (inscriptosSeleccionado == 51) {
                rango_min = 51;
                rango_max = 1000;
            }

            inscriptosMatch = function(item) {
                return item.values().inscriptos >= rango_min && item.values().inscriptos <= rango_max;
            }
        }


        materias_semestresList.filter(function (item) {
            var semestreMatch = !semestreSeleccionado || item.values().semestre === semestreSeleccionado;
            var periodoMatch = !periodoSeleccionado || item.values().periodo === periodoSeleccionado;
            var docenteMatch = !docenteSeleccionado || item.values().docente === docenteSeleccionado;
            var carreraMatch = !carreraSeleccionado || item.values().carrera === carreraSeleccionado;
            var programaMatch = !programaSeleccionado || item.values().programa === programaSeleccionado;

            return periodoMatch && docenteMatch && semestreMatch && carreraMatch && programaMatch && inscriptosMatch(item);
        })
    }
</script>
