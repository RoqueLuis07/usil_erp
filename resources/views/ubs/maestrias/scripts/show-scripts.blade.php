<script type="module">
	//Inicio formatos para CleaveJS
    const formatoSeparadorMiles = {
        numeral: true,
        numeralDecimalMark: ',',
        delimiter: '.',
        swapHiddenInput: false,
        numeralDecimalScale: 0,
    };

    $(document).ready(function() {
        var options = {
            valueNames: ['alumno', 'numero_documento'],
            page: 50,
            pagination: true
        };
        var inscripcionesList = new List('inscripciones-list', options);
        inscripcionesList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
        });
		
		if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
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
	
	$('.update-puntaje-btn').click(function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de actualizar el puntaje del alumno?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
				var url = $(this).data('url');
				// $('#update-form').submit();
                update_puntaje(id, url);
            }
        })
    });
	
	function update_puntaje(id, url) {
        const formData = new FormData(document.getElementById('update-puntaje-form-' + id));
        $('#update-puntaje-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#update-puntjae-form-' + id).find('.invalid-feedback').remove();
        $.ajax({
            url: url,
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
            window.location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                if (input.hasClass('flatpickr') ) {
                    var i = $('#calendar-icon');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(i);
                        i.detach();
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }
	
	$(document).on('focus', '.puntos', function () {
        new Cleave ($(this), formatoSeparadorMiles);
    })

    const minPuntaje = 0;
    const maxPuntaje = 60;

    $(document).on('input', '.puntos', function () {
        var id = $(this).data('id');

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
