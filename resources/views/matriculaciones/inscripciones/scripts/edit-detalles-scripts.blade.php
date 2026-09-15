<script type="module">
    function message(message, type) {
        var Toast = Swal.mixin({
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

    var materia_accion_add;
    var materiasSelected = [];
    $(document).on('click', '.btn-add', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var fila = parseInt($(this).data('id'));
        var cantidad_filas = $('.fila').length;
        var ultima_fila = $('.fila:last').prop('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var nro_ultima_fila = parseInt(ultima_fila.split('-')[1]);
        var nro_nueva_fila = parseInt(nro_ultima_fila + 1);
        materiasSelected = [];

        if ($('.materia-' + nro_ultima_fila + ' option:selected').val() != '') {
            if (cantidad_filas < 10) {
                var filaAdd = `<div class="mb-2 fila" id="fila-${nro_nueva_fila}">
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-6 col-sm-12 mb-2 text-center" id="div-materia-${nro_nueva_fila}">
                                            <select class="selectpicker form-control materia-${nro_nueva_fila} materia @error('detalles.${nro_nueva_fila}.materia') is-invalid @enderror" id="materia-${nro_nueva_fila}" name="detalles[${nro_nueva_fila}][materia]" data-live-search="true" data-id="${nro_nueva_fila}">

                                            </select>
                                            <span class="invalid-feedback error-materia-${nro_nueva_fila}" role="alert">

                                            </span>
                                        </div>
                                        <div class="col-lg-1 col-sm-2 text-center">
                                            <div class="align-middle" id="acciones-${nro_nueva_fila}">
                                                <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-subtract-fill"></i></button>
                                                <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_nueva_fila}" data-id="${nro_nueva_fila}"><i class="ri-add-fill"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>`

                materia_accion_add = $('#btn-add-' + fila).detach();

                var nro_ultima_fila = parseInt(ultima_fila.split('-')[1] + 1);

                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.materia-' + index + ' option:selected').val();
                    if (valor && !isNaN(valor)) {
                        materiasSelected.push(parseInt(valor));
                    }
                }
                materiasSelected.sort();

                if (materiasSelected.length != materias.length) {
                    $('#materia-fila').append(filaAdd);

                    $.each(materiasSelected, function (index, value) {
                        for (var clave in materias) {
                            if (materias.hasOwnProperty(clave)) {
                                if (parseInt(materias[clave].materia_id) === value) {
                                    delete materias[clave];
                                    break;
                                }
                            }
                        }
                    })

                    $('.materia-' + nro_nueva_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(materias, function (i, val) {
                        if (val) {
                            var optionText = val.materia.nombre_fantasia;
                            var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            var dataContent = '';
                            
                            if (val.tiene_conflicto) {
                                dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                            } else {
                                dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            }
                            $('.materia-' + nro_nueva_fila).append(
                                '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                            );
                        }
                    });
                    $('.materia-' + nro_nueva_fila).addClass('selectpicker').selectpicker('render');

                    materia_accion_add = $('#btn-add-' + nro_nueva_fila);

                    if (cantidad_filas == 1) {
                        if ($('#acciones-0').length) {
                            $('#acciones-0').html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-0" data-id="0"><i class="ri-subtract-fill"></i></button>`);
                        } else {
                            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>`);
                        }
                    }
                } else {
                    message('No hay más materias habilitadas para agregar a la inscripción.', 'warning')
                }
            } else {
                message('No puede seleccionar más de 10 materias.', 'warning')
            }
        } else {
            message('Debe completar todos los campos antes de insertar la siguiente línea.', 'warning')
        }
    })

    $(document).on('change', '.materia', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var cantidad_filas = $('.fila').length;
        var fila = $(this).data('id');
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        var valor = $('.materia-' + fila + ' option:selected').val();

        materiasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.materia-' + index + ' option:selected').val();
            if (valor && !isNaN(valor)) {
                materiasSelected.push(parseInt(valor));
            }
        }
        materiasSelected.sort();

        $.each(materiasSelected, function (index, value) {
            for (var clave in materias) {
                if (materias.hasOwnProperty(clave)) {
                    if (parseInt(materias[clave].materia_id) === value) {
                        delete materias[clave];
                        break;
                    }
                }
            }
        })

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var valor = $('.materia-' + index + ' option:selected').val();
            if ((parseInt(valor) || 0) != 0) {
                var texto = $('.materia-' + index + ' option:selected').text();
                var content = $('.materia-' + index + ' option:selected').data('content');
                var subtext = $('.materia-' + index + ' option:selected').data('subtext');
                $('.materia-' + index).selectpicker('destroy');
                $('.materia-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.materia-' + index).append('<option value="" disabled>Seleccionar...</option>');
                let optionAttrs = ' selected ';
                if (content) {
                    optionAttrs += ' data-content="' + content.replace(/"/g, '&quot;') + '" ';
                } if (subtext) {
                    optionAttrs += ' data-subtext="' + subtext.replace(/"/g, '&quot;') + '" ';
                }
                $('.materia-' + index).append('<option value="' + valor + '"' + optionAttrs + '>' + texto + '</option>');
                $.each(materias, function (i, val) {
                    if (val) {
                        var optionText = val.materia.nombre_fantasia;
                        var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        var dataContent = '';
                        
                        if (val.tiene_conflicto) {
                            dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                        } else {
                            dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        }
                        $('.materia-' + index).append(
                            '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                        );
                    }
                });
                $('.materia-' + index).addClass('selectpicker').selectpicker('render');
            } else {
                $('.materia-' + index).selectpicker('destroy');
                $('.materia-' + index + ' option').each(function () {
                    $(this).remove();
                });

                $('.materia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                if (Object.keys(materias).lenght > 0) {
                    $.each(materias, function (i, val) {
                        if (val) {
                            var optionText = val.materia.nombre_fantasia;
                            var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            var dataContent = '';
                            
                            if (val.tiene_conflicto) {
                                dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                            } else {
                                dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            }
                        
                            $('.materia-' + index).append(
                                '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                            );
                        }
                    });
                }
                $('.materia-' + index).addClass('selectpicker').selectpicker('render');
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    //boton remover fila
    $(document).on('click', '.btn-erase', function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var fila = $(this).data('id');
        var cantidad_filas = $('.fila').length;
        var valor = $('.materia-' + fila + ' option:selected').val();

        if (cantidad_filas != 1) {
            $('#fila-' + fila).remove();
            cantidad_filas = cantidad_filas - 1;
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        materiasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.materia-' + index + ' option:selected').val();
            if (valor && !isNaN(valor)) {
                materiasSelected.push(parseInt(valor));
            }
        }
        materiasSelected.sort();

        $.each(materiasSelected, function (index, value) {
            for (var clave in materias) {
                if (materias.hasOwnProperty(clave)) {
                    if (parseInt(materias[clave].materia_id) === value) {
                        delete materias[clave];
                        break;
                    }
                }
            }
        })

        if (cantidad_filas == 1) {
            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            var valor_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').val();
            if ((parseInt(valor_siguiente) || 0) != 0) {
                var texto_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').text();
                var content_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').data('content');
                var subtext_siguiente = $('.materia-' + nro_ultima_fila + ' option:selected').data('subtext');
                $('.materia-' + nro_ultima_fila).selectpicker('destroy');
                $('.materia-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.materia-' + nro_ultima_fila).append('<option value="" disabled>Seleccionar...</option>');
                let optionAttrs = ' selected ';
                if (content_siguiente) {
                    optionAttrs += ' data-content="' + content_siguiente.replace(/"/g, '&quot;') + '" ';
                } else if (subtext_siguiente) {
                    optionAttrs += ' data-subtext="' + subtext_siguiente.replace(/"/g, '&quot;') + '" ';
                }
                $.each(materias, function (i, val) {
                    if (val) {
                        var optionText = val.materia.nombre_fantasia;
                        var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        var dataContent = '';
                        
                        if (val.tiene_conflicto) {
                            dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                        } else {
                            dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        }
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.materia-' + nro_ultima_fila).append(
                                '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                            );
                        }
                    }
                })
                $('.materia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            } else {
                $('.materia-' + nro_ultima_fila).selectpicker('destroy');
                $('.materia-' + nro_ultima_fila + ' option').each(function () {
                    $(this).remove();
                });

                $('.materia-' + nro_ultima_fila).append('<option value="" selected disabled>Seleccionar...</option>');
                $.each(materias, function (i, val) {
                    if (val) {
                        var optionText = val.materia.nombre_fantasia;
                        var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        var dataContent = '';
                        
                        if (val.tiene_conflicto) {
                            dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                        } else {
                            dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                        }
                        if (parseInt(valor_siguiente) != val.id) {
                            $('.materia-' + nro_ultima_fila).append(
                                '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                            );
                        }
                    }
                })
                $('.materia-' + nro_ultima_fila).addClass('selectpicker').selectpicker('render');
            }
        } else {
            var primera_fila = $('.fila:first').prop('id');
            var nro_primera_fila = primera_fila.split('-')[1];
            var ultima_fila = $('.fila:last').prop('id');
            var nro_ultima_fila = ultima_fila.split('-')[1];

            $('#acciones-' + nro_ultima_fila).html(`<button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-subtract-fill"></i></button>
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-${nro_ultima_fila}" data-id="${nro_ultima_fila}"><i class="ri-add-fill"></i></button>`);

            for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
                var valor = $('.materia-' + index + ' option:selected').val();
                if ((parseInt(valor) || 0) != 0) {
                    var texto = $('.materia-' + index + ' option:selected').text();
                    var content = $('.materia-' + index + ' option:selected').data('content');
                    var subtext = $('.materia-' + index + ' option:selected').data('subtext');
                    $('.materia-' + index).selectpicker('destroy');
                    $('.materia-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.materia-' + index).append('<option value="" disabled>Seleccionar...</option>');

                    let optionAttrs = ' selected ';
                    if (content) {
                        optionAttrs += ' data-content="' + content.replace(/"/g, '&quot;') + '" ';
                    } else if (subtext) {
                        optionAttrs += ' data-subtext="' + subtext.replace(/"/g, '&quot;') + '" ';
                    }

                    $('.materia-' + index).append('<option value="' + valor + '"' + optionAttrs + '>' + texto + '</option>');
                    $.each(materias, function (i, val) {
                        if (val) {
                            var optionText = val.materia.nombre_fantasia;
                            var dataSubtext = 'data-subtext="' + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            var dataContent = '';
                                
                            if (val.tiene_conflicto) {
                                dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                            } else {
                                dataSubtext = 'data-subtext="' + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            }
                            if (parseInt(valor_siguiente) != val.id) {
                                $('.materia-' + index).append(
                                    '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                                );
                            }
                        }
                    })
                    $('.materia-' + index).addClass('selectpicker').selectpicker('render');
                } else {
                    $('.materia-' + index).selectpicker('destroy');
                    $('.materia-' + index + ' option').each(function () {
                        $(this).remove();
                    });

                    $('.materia-' + index).append('<option value="" selected disabled>Seleccionar...</option>');
                    $.each(materias, function (i, val) {
                        if (val) {
                            var optionText = val.materia.nombre_fantasia;
                            var dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            var dataContent = '';
                            
                            if (val.tiene_conflicto) {
                                dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                            } else {
                                dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            }
                            if (parseInt(nro_ultima_fila) != val.id) {
                                $('.materia-' + index).append(
                                    '<option value="' + val.materia_id + '" ' + dataSubtext + ' ' + dataContent + '>' + optionText + '</option>'
                                );
                            }
                        }
                    })
                    $('.materia-' + index).addClass('selectpicker').selectpicker('render');
                }
            }
        }

        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        insertarLabels(nro_primera_fila);
    })

    function insertarLabels(primera_fila) {
        if ($('.label-materia').text() == '') {
            $('<label class="form-label label-materia">Materia <span class="text-danger">(*)</span></label>').insertBefore('#materia-' + primera_fila);
        }
        if ($('.label-acciones').text() == '') {
            $('<label class="form-label label-acciones">Acciones</label>').insertBefore('#acciones-' + primera_fila);
        }
    }

    $(document).ready(function () {
        var materias = {!!json_encode($materias, JSON_HEX_TAG) !!}
        var primera_fila = $('.fila:first').prop('id');
        var nro_primera_fila = primera_fila.split('-')[1];
        var ultima_fila = $('.fila:last').prop('id');
        var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

        materiasSelected = [];
        for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
            var valor = $('.materia-' + index + ' option:selected').val();
            if (valor && !isNaN(valor)) {
                materiasSelected.push(parseInt(valor));
            }
        }
        materiasSelected.sort();

        $.each(materiasSelected, function (index, value) {
            for (var clave in materias) {
                if (materias.hasOwnProperty(clave)) {
                    if (parseInt(materias[clave].materia_id) === value) {
                        delete materias[clave];
                        break;
                    }
                }
            }
        })

        for (let index = nro_primera_fila; index <= nro_ultima_fila; index++) {
            var select = $('.materia-' + index);
            
            var selectedOption = select.find('option:selected');
            var valorActual = parseInt(selectedOption.val()) || 0;
            var textoActual = selectedOption.text();
            var currentDataSubtext = selectedOption.data('subtext') || ''; 
            var currentDataContent = selectedOption.data('content') || '';

            select.selectpicker('destroy');
            select.find('option').remove();

            select.append('<option value="" disabled>Seleccionar...</option>');

            if (valorActual !== 0) {
                let optionAttrs = ' selected ';
                
                if (currentDataContent) {
                    optionAttrs += ' data-content="' + currentDataContent.replace(/"/g, '&quot;') + '" ';
                } else if (currentDataSubtext) {
                    optionAttrs += ' data-subtext="' + currentDataSubtext.replace(/"/g, '&quot;') + '" ';
                }
                
                select.append(
                    '<option value="' + valorActual + '"' + optionAttrs + '>' + textoActual + '</option>'
                );
            } else {
                select.find('option[value=""]').prop('selected', true);
            }

            var selectedIdsSet = new Set(materiasSelected); // Para búsquedas rápidas

            $.each(materias, function (i, val) {
                if (val) {
                    var valor = parseInt(val.materia_id);

                    if (valor !== valorActual && !selectedIdsSet.has(valor)) {
                        var optionText = val.materia.nombre_fantasia;
                        var dataSubtext = '';
                        var dataContent = '';
                        var optionAttrs = '';

                        if (val.tiene_conflicto) {
                            dataContent = 'data-content="<div>' + val.materia.nombre_fantasia + ' <span class=\'badge bg-danger\' style=\'background: red; color: white;\'>Solapado: ' + val.conflicto + '</span></div>"';
                            optionAttrs = ' ' + dataContent;
                        } else {
                            dataSubtext = 'data-subtext="' + val.materia.nombre_real + ' (SEMESTRE ' + val.semestre_materia + ')"';
                            optionAttrs = ' ' + dataSubtext;
                        }

                        select.append(
                            '<option value="' + val.materia_id + '"' + optionAttrs + '>' + optionText + '</option>'
                        );
                    }
                }
            });
            select.addClass('selectpicker').selectpicker('render');
        }

        var errors = {!! json_encode($errors->any(), JSON_HEX_TAG) !!}
        if (errors) {
            //Genera el array con todas las filas devueltas por el request validator
            var arrayFilas = {!! json_encode(Session::get('_old_input.detalles'), JSON_HEX_TAG) !!};

            $.each(arrayFilas, function (index) {
                //Si no es el primer elemento, se debe añadir el registro
                if (index != 0) {
                    $('.btn-add').trigger('click');
                    //se añaden los elemenos 1 a 1
                    $('.materia-' + index).val(arrayFilas[index].materia);
                    $('.materia-' + index).selectpicker('val', arrayFilas[index].materia);
                }

                var primera_fila = $('.fila:first').prop('id');
                var nro_primera_fila = primera_fila.split('-')[1];
                var ultima_fila = $('.fila:last').prop('id');
                var nro_ultima_fila = (ultima_fila.split('-')[1]) + 1;

                materiasSelected = [];
                for (let index = nro_primera_fila; index < nro_ultima_fila; index++) {
                    var valor = $('.materia-' + index + ' option:selected').val();
                    if ((parseInt(valor) || 0) != 0) {
                        materiasSelected.push(parseInt(valor));
                    }
                }
                materiasSelected.sort();

                $.each(materiasSelected, function (index, value) {
                    for (var clave in materias) {
                        if (materias.hasOwnProperty(clave)) {
                            if (parseInt(materias[clave].materia_id) === value) {
                                delete materias[clave];
                                break;
                            }
                        }
                    }
                })

                $('.materia').trigger('change');
            })
        }

        //obtener los errors de los detalles
        var arrayFilasErrors = {!! json_encode($errors->get('detalles.*'), JSON_HEX_TAG) !!};
        $.each(arrayFilasErrors, function (index, value) {
            if (index != '') {
                var numero = index.split('.')[1];
                var tipoError = index.split('.')[2];
                $('.' + tipoError + '-' + numero).addClass('is-invalid');
                $('.error-' + tipoError + '-' + numero).append('<strong>' + value + '</strong>');
            }
        })
    })
</script>
