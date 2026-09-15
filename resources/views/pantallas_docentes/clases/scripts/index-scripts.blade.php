<script type="module">
	var clasesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['fecha', 'materia', 'docente', 'carrera', 'semestre', 'modalidad'],
            page: 50,
            pagination: true
        };
        clasesList = new List('clases-list', options);
        clasesList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
			cantidad_mostrada(list.matchingItems.length)
        });
		
		cantidad_mostrada(clasesList.size());
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

    $('#delete-materia-filter-btn').on('click', function () {
        $('#filtro_materia').selectpicker('val', '');
        $('#filtro_materia').trigger('change');
    })

    $('#delete-modalidad-filter-btn').on('click', function () {
        $('#filtro_modalidad').selectpicker('val', '');
        $('#filtro_modalidad').trigger('change');
    })

    $('#filtro_materia').change(aplicarFiltros);
    $('#filtro_modalidad').change(aplicarFiltros);

    function aplicarFiltros() {
        var materiaSeleccionado = $('#filtro_materia option:selected').val();
        var modalidadSeleccionado = $('#filtro_modalidad option:selected').val();


        clasesList.filter(function (item) {
            var materiaMatch = !materiaSeleccionado || item.values().materia === materiaSeleccionado;
            var modalidadMatch = !modalidadSeleccionado || item.values().modalidad === modalidadSeleccionado;

            return materiaMatch && modalidadMatch;
        })
    }
	
	function cantidad_mostrada(total_lista) {
	  var total = clasesList.size();	
      var tamanho_pagina = 50;
      var pagina_actual = clasesList.i;
	  
	  var fin = Math.min(pagina_actual + tamanho_pagina, total_lista);

      $("#mostrando").text('Mostrando ' + pagina_actual + '-' + fin + ' registros de ' + total);
    }
</script>
