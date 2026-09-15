<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            if (input.files[0].type == 'application/pdf') {
                $('#vista-imagen').css('width', '100px');
                $('#vista-imagen').css('height', '100px');
                $('#vista-imagen').prop('src', '{{asset('storage/pdf.png')}}');
            } else {
                reader.onload = function (e) {
                    $('#vista-imagen').prop('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        };
    };
</script>

<script type="module">
	var planes_clasesList;
    $(document).ready(function() {
        var options = {
            valueNames: ['materia'],
            page: 10,
            pagination: true
        };
        planes_clasesList = new List('planes_clases-list', options);
        planes_clasesList.on('updated', function(list) {
            if (list.matchingItems.length > 0) {
                $('.noresults').hide()
            } else {
                $('.noresults').show()
            }
			cantidad_mostrada(list.matchingItems.length)
        });

		cantidad_mostrada(planes_clasesList.size());

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

    $('.adjunto').on('change', function () {
        $('.eliminar-adjunto').prop('disabled', false);
    })

    $('.eliminar-adjunto').on('click', function () {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el archivo subido?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                var tipo = $(this).data('tipo');
                $('#' + tipo.toLowerCase() + '-' + id).val('');
                $(this).prop('disabled', true);
            }
        });
    });

    $(document).on('click', '.save-adjunto-btn', function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        $('#store-' + tipo.toLowerCase() + '-clase-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#store-' + tipo.toLowerCase() + '-clase-form-' + id).find('.invalid-feedback').remove();
    })

    $('.cancel-adjunto-btn').click(function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        if (tipo == 'Plan') {
            var sufijo_tipo = ' de clases';
        } else {
            var sufijo_tipo = ' de estudio';
        }
        var nombre = ($('.materia-' + id).val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de cancelar la subida del archivo al ' + tipo + sufijo_tipo + ' de la materia ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar!',
            cancelButtonText: 'Volver',
        }).then((result) => {
            if (result.isConfirmed) {
                var tipo = $(this).data('tipo');
                $('#' + tipo.toLowerCase() + '-' + id).val('');
                $('#subir' + tipo + 'ClaseModal-' + id).modal('hide');
            }
        })
    });

    $('.save-adjunto-btn').click(function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        if (tipo == 'Plan') {
            var sufijo_tipo = ' de clases';
        } else {
            var sufijo_tipo = ' de estudio';
        }
        var nombre = ($('.materia-' + id).val()).toUpperCase();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de guardar el archivo subido al ' + tipo.toLowerCase() + sufijo_tipo + ' de la materia ' + nombre + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                var url = $(this).data('url');
                save(id, url, tipo);
            }
        })
    });

    function save(id, url, tipo) {
        const formData = new FormData(document.getElementById('store-' + tipo.toLowerCase() + '-clase-form-' + id));
        $('#store-' + tipo.toLowerCase() + '-clase-form-' + id).find('.is-invalid').removeClass('is-invalid');
        $('#store-' + tipo.toLowerCase() + '-clase-form-' + id).find('.invalid-feedback').remove();
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
            location.reload();
        }).fail(function(response) {
            $.each(response.responseJSON.errors, function (key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');

                if (key == 'adjunto') {
                    var btn = $('.eliminar-adjunto');
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(btn);
                } else {
                    $('<span>').addClass('invalid-feedback').html(value.join('<strong>')).insertAfter(input);
                }
            })
        })
    }

    $('.delete-adjunto-btn').on('click', function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        if (tipo == 'Plan') {
            var sufijo_tipo = ' de clases';
        } else {
            var sufijo_tipo = ' de estudio';
        }
        var nombre = $('.materia-' + id).val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de eliminar el ' + tipo.toLowerCase() + sufijo_tipo + ' de la materia ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-' + tipo.toLowerCase() + '-clase-form-' + id).submit();
            }
        })
    })

    $('.copy-btn').on('click', function () {
        var id = $(this).data('id');
        var tipo = $(this).data('tipo');
        if (tipo == 'Plan') {
            var sufijo_tipo = ' de clases';
        } else {
            var sufijo_tipo = ' de estudio';
        }
        var nombre = $('.materia-' + id).val();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-light',
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de copiar el ' + tipo.toLowerCase() + sufijo_tipo + ' anterior de la materia ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, copiar!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('#copy-' + tipo.toLowerCase() + '-clase-form-' + id);
                $('#copy-' + tipo.toLowerCase() + '-clase-form-' + id).submit();
            }
        })
    })

	function cantidad_mostrada(total_lista) {
	  var total = planes_clasesList.size();
      var tamanho_pagina = 10;
      var pagina_actual = planes_clasesList.i;

	  var fin = Math.min(pagina_actual + tamanho_pagina, total_lista);

      $("#mostrando").text('Mostrando ' + pagina_actual + '-' + fin + ' registros de ' + total);
    }
</script>
