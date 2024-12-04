$(() => {
    $('input#frm_mue_procedencia').autocomplete({
        data: {
            "Asbioquim S.A.S": null,
            "Cliente": null
        },
    });
    $('form').keypress(function(e) { if (e.which == 13) return false; });
    $('input#frm_nit').blur(function() { $('input#frm_nit').removeClass('invalid'); });
    $('#myform').validate({
        submitHandler: function(form) {
            if ($('#frm_certificados_editar').val() == '' && $('#frm_muestra_editar').val() == '')
                return my_toast('<i class="fas fa-times"></i>&nbsp Numero de informe y numero de muestra vacios', 'red darken-2', 3000);
            else if ($('#frm_certificados_editar').val() == '' && ($('#frm_muestra_editar').val() == '' || $('#frm_muestra_editar_anio').val() == '') )
                return my_toast('<i class="fas fa-times"></i>&nbsp Código de informe o año de muestra vacio', 'red darken-2', 3000);
            my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Buscando informe', 'blue-grey darken-2', 30000);
            var url = $(form).attr('action');
            var data = $(form).serialize();
            var resultado = proceso_fetch(url, data);
            formateo_forms($('#frm_form')[0]);
            resultado.then(data => {
                if (data.result) {
                    // completar_empresa(data.cliente);
                    recibe_entrega(data.muestra, data.certificado);
                    var data_aux = {
                        id: data.cliente.id,
                        text: data.cliente.name
                    };
                    var newOption = new Option(data_aux.text, data_aux.id, true, true);
                    $('#frm_nombre_empresa').append(newOption).trigger('change');
                    tabla_detalles_muestras(data.tabla);
                    $('#frm_producto').attr('disabled', true);
                } else my_toast('<i class="fas fa-times"></i>&nbsp No se encontro informe', 'amber darken-2', 3000);
            });
            
        }
    });
    $('#frm_form').validate({
        rules: {
            frm_nombre_empresa: { required: true },
        },
        showErrors: function(errorMap, errorList) {
            errorList.forEach(key => {
                var input = [key.element];
                id = $(input).attr('id');
                $('input#' + id).addClass('invalid');
            });
        },
        submitHandler: function(form) {
            var url = $('#myform').attr('action');
            var data = $(form).serialize();
            var boton_empresa = $('#btn-empresa');
            boton_empresa.prop('disabled', true);
            boton_empresa.removeClass('gradient-45deg-purple-deep-orange');
            boton_empresa.addClass('blue-grey darken-3');
            boton_empresa.html('Editando empresa <i class="fas fa-spinner fa-spin"></i>');
            var resultado = proceso_fetch(url, data);
            resultado.then(result => {
                $('.empresa_row small').html('');
                if (result.vacio) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'warning',
                        text: result.mensaje,
                    });
                }
                if (result.success) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        text: 'Datos empresa cambiada correctamente',
                    });
                } else {
                    var mensajes = Object.entries(result);
                    mensajes.forEach(([key, value]) => {
                        $('input#' + key).addClass('invalid');
                        $('small#' + key).html(value);
                    });
                }
                boton_empresa.addClass('gradient-45deg-purple-deep-orange');
                boton_empresa.removeClass('blue-grey darken-3');
                boton_empresa.prop('disabled', false);
                boton_empresa.html('Actualizar empresa');
            }).catch(error => {
                boton_empresa.addClass('gradient-45deg-purple-deep-orange');
                boton_empresa.removeClass('blue-grey darken-3');
                boton_empresa.prop('disabled', false);
                boton_empresa.html('Actualizar empresa');
                my_toast('<i class="fa fas-times"></i>&nbsp&nbsp Error en la consulta', 'red darken-2', 3000);
            })
        }
    });
    $('#frm_nombre_empresa').keyup(function(e) {
        var empresa = $('#frm_nombre_empresa').val();
        var form = $('#frm_form');
        var url = form.attr('action');
        var tecla = e.which;
        if (empresa != "" && tecla != 37 && tecla != 38 && tecla != 39 && tecla != 40) {
            var data = new URLSearchParams({
                frm_nombre_empresa: empresa,
                buscar: 1,
            });
            var resultado = proceso_fetch(url, data.toString());
            resultado.then(lista => {
                $('.autocomplete.frm_nombre_empresa').autocomplete('updateData', lista);
                $('.autocomplete.frm_nombre_empresa').autocomplete('open');
            })
        }
    });
    $('#frm_nombre_empresa').blur(function() {
        buscar_cliente();
    });
    $('#frm_form_muestra').validate({
        rules: {
            frm_identificacion: { required: true },
            frm_mue_procedencia: { required: true }
        },
        showErrors: function(errorMap, errorList) {
            errorList.forEach(key => {
                var input = [key.element];
                id = $(input).attr('id');
                $('input#' + id).addClass('invalid');
            });
        },
        submitHandler: function(form) {
            var mensaje = '';
            var select = true;
            if ($('#frm_condiciones_recibido').val() == null) {
                var select = false;
                $('.condiciones').addClass('error');
                $('.condiciones .select-dropdown.dropdown-trigger').focus();
            } else if ($('#frm_mue_procedencia').val() == null) {
                var select = false;
                $('.mue_procedencia').addClass('error');
                $('.mue_procedencia .select-dropdown.dropdown-trigger').focus();
            } else if ($('#frm_analisis').val() == null) {
                var select = false;
                $('.frm_analisis').addClass('error');
                $('.frm_analisis .select-dropdown.dropdown-trigger').focus();
            } else if ($('#frm_nombre_empresa').val() == '') {
                mensaje = 'Seleccione una empresa o registre una.';
                $('input#frm_nombre_empresa').addClass('invalid');
            } else if ($('#frm_entrega').val() == '') {
                mensaje = 'Registre una persona quien entrego la muestra.';
                $('input#frm_entrega').addClass('invalid');
            } else if ($('#frm_recibe').val() == '') {
                mensaje = 'Registre una persona responsable quien recibe la muestra.';
                $('input#frm_recibe').addClass('invalid');
            }
            if (mensaje != '') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    text: mensaje,
                });
            } else if (select) {
                var boton = $('#btn-muestreo-form');
                boton.prop('disabled', true);
                boton.addClass('blue-grey darken-3');
                boton.removeClass('gradient-45deg-purple-deep-orange');
                my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Editando detalle', 'blue-grey darken-2', 3000);
                js_enviar_agregar_a_detalle($('#myform').attr('action'));
                aux_vida_util.splice(0, aux_vida_util.length);
                $('#table_vida_util tbody').html('');
            }
            
            $(`#aux_vida_util`).val('');
            $(`#aux_vida_util_dia`).val('');
            M.updateTextFields();
            
            $('.btn-add-vida').attr('onclick', `add_vida_util()`);
            $('.btn-add-vida').html(`Agregar`);
        }
    });
    // $('#frm_producto').keyup(function(e) {
    //     var producto = $('#frm_producto').val();
    //     var form = $('#frm_form_muestra');
    //     var url = form.attr('action');
    //     var tecla = e.which;
    //     if (producto != "" && tecla != 37 && tecla != 38 && tecla != 39 && tecla != 40) {
    //         var data = new URLSearchParams({
    //             frm_producto: producto,
    //             buscar: 1,
    //         });
    //         result = proceso_fetch(url, data.toString());
    //         result.then(lista => {
    //             $('.autocomplete.frm_producto').autocomplete('updateData', lista);
    //             $('.autocomplete.frm_producto').autocomplete('open');
    //         });
    //     }
    // });
    // $('#frm_producto').blur(function(e) {
    //     producto_blur(4);
    // });
});

const aux_vida_util = [];

function edit_fechas() {
    aux_vida_util.splice(0, aux_vida_util.length);
    var fechas = $('#vida_util').val();
    fechas = JSON.parse(fechas);
    fechas.forEach(fecha => {
        var array_aux = { id: fecha.id, fecha: fecha.fecha, dia: fecha.dia };
        aux_vida_util.push(array_aux);
    });
    var table = ``;
    fechas.forEach((fecha, key) => {
        table += `<tr id="fecha_${key}">
            <td>${fecha.fecha}</td>
            <td>${fecha.dia}</td>
            <td>
                <a class="btn-floating mb-1 edit" onclick="edit_vida('${key}')"><i class="material-icons">create</i></a>
                <a class="btn-floating mb-1 delete" onclick="delete_vida('${key}', 0)"><i class="material-icons">close</i></a>
            </td>
        </tr>`;
    });
    $('#table_vida_util tbody').html(table);

    $('#vida_util').val(JSON.stringify(aux_vida_util))
}

function add_vida_util(create = 'created') {
    var aux = $(`#aux_vida_util`).val();
    var aux_dia = $(`#aux_vida_util_dia`).val();
    if (aux === '')
        return my_toast('<span class="blue-text"><i class="fas fa-times"></i>&nbsp No se puede agregar una fecha vacia</span>', 'blue lighten-5', 3000);
    if (aux_dia === '')
        return my_toast('<span class="blue-text"><i class="fas fa-times"></i>&nbsp No se puede agregar un día vacio</span>', 'blue lighten-5', 3000);
    var table = '';
    var validate = aux_vida_util.every((value, key) => {
        if (aux === value) return false;
        else return true;
    })
    if (!validate) return my_toast('<span class="blue-text"><i class="fas fa-times"></i>&nbsp No se puede agregar una fecha repetida</span>', 'blue lighten-5', 3000);
    if (create === 'created') {
        var array_aux = { fecha: aux, dia: aux_dia };
        aux_vida_util.push(array_aux);
    } else {
        aux_vida_util[create].fecha = aux;
        aux_vida_util[create].dia = aux_dia;
        $('.btn-add-vida').attr('onclick', `add_vida_util()`);
        $('.btn-add-vida').html(`Agregar`);
    }
    aux_vida_util.forEach((value, key) => {
        table += `
				<tr id="fecha_${key}">
					<td>${value.fecha}</td>
					<td>${value.dia}</td>
					<td>
						<a class="btn-floating mb-1 edit" onclick="edit_vida('${key}')"><i class="material-icons">create</i></a>
						<a class="btn-floating mb-1 delete" onclick="delete_vida('${key}')"><i class="material-icons">close</i></a>
					</td>
				</tr>`
    });
    $('#table_vida_util tbody').html(table);
    $(`#aux_vida_util`).val('');
    $(`#aux_vida_util_dia`).val('');
    M.updateTextFields();
    $('#vida_util').val(JSON.stringify(aux_vida_util))
}

function edit_vida(key) {
    var value = aux_vida_util[key];
    $('#aux_vida_util').val(value.fecha);
    $('#aux_vida_util_dia').val(value.dia);
    $('.btn-add-vida').attr('onclick', `add_vida_util(${key})`);
    $('.btn-add-vida').html(`Editar`);
    M.updateTextFields();
}

function delete_vida(key, created = 'created') {
    if (created == 'created') {
        aux_vida_util.splice(key, 1);
        // $(`#fecha_${key}`).remove()
        $('#vida_util').val(JSON.stringify(aux_vida_util));
        edit_fechas()
    } else {
        Swal.fire({
            title: '¿Está seguro de querer eliminar esta fecha?',
            icon: 'warning',
            html: `Si elimina la fecha ${aux_vida_util[key].fecha} al momento de editar se eliminaran los resultados asignados a esta fecha.`,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: `Cancelar`,
        }).then(result => {
            if (result.isConfirmed) {
                aux_vida_util.splice(key, 1);
                // $(`#fecha_${key}`).remove()
                $('#vida_util').val(JSON.stringify(aux_vida_util));
                edit_fechas()
            }
        })
    }
}