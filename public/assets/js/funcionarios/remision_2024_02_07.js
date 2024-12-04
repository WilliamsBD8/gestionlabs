$(() =>{
	$('input#frm_nit').blur(function(){$('input#frm_nit').removeClass('invalid');});
	$('input#frm_mue_procedencia').autocomplete({
        data: {
            "Asbioquim S.A.S": null,
            "Cliente": null
        },
    });
	$('form').keypress(function(e) { // Negamos el envio por la tecla enter
        if (e.which == 13)
            return false;
    });
    
	$('#frm_form').validate({
		submitHandler: function(form){
			var url = $(form).attr('action');
			var data = $(form).serialize();
			var boton_empresa = $('#btn-empresa');
			boton_empresa.prop('disabled', true);
			boton_empresa.removeClass('gradient-45deg-purple-deep-orange');
			boton_empresa.addClass('blue-grey darken-3');
			boton_empresa.html('Guardando empresa <i class="fas fa-spinner fa-spin"></i>');
			var result = proceso_fetch(url, data);
			result.then(result => {
			 	$('.empresa_row small').html('');
			 	if(result.success){
			 		$(".empresa_row .frm_hora_muestra label").addClass('active');
			 		Swal.fire({
						position: 'top-end',
					  	icon: 'success',
					  	text: result.success,
					});
					if(result.procedencia == 0){
						$('input#empresa_nueva').val(1);
						$('#frm_nombre_empresa2').val(result.id);
					}
			 	}else{
			 		var mensajes = Object.entries(result);
			 		mensajes.forEach(([key, value])=> {
			 			$('small#'+key).html(value);
			 		});
			 	}
			 	boton_empresa.addClass('gradient-45deg-purple-deep-orange');
				boton_empresa.removeClass('blue-grey darken-3');
				boton_empresa.prop('disabled', false);
				boton_empresa.html('Guardar empresa');
			});
		}
	});

	// Muestra
    $('#frm_form_muestra').validate({ // Guardamos el detalle
		rules: {
			frm_identificacion: {required:true},
            frm_mue_procedencia: { required: true }
		},
		showErrors: function(errorMap, errorList) {
			errorList.forEach(key => {
				var input = [key.element];
				id = $(input).attr('id');
				$('input#'+id).addClass('invalid');
			});
		},
		submitHandler: function(){
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
            } else if ($('#frm_analisis').val() == null || $('#frm_analisis').val() == "") {
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
			if(mensaje != ''){
				Swal.fire({
					position: 'top-end',
				  	icon: 'error',
				  	text: mensaje,
				});
			}else if(select){
				var boton = $('#btn-muestreo-form');
				boton.prop('disabled', true);
				boton.addClass('blue-grey darken-3');
				boton.removeClass('gradient-45deg-purple-deep-orange');
				my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Agregando detalle', 'blue-grey darken-2', 30000);
				js_enviar_agregar_a_detalle($('#frm_form_muestra').attr('action'), 1);
				aux_vida_util.splice(0, aux_vida_util.length);
                $('#table_vida_util tbody').html('');
			}
		}
	});
	$('.frm_analisis li').click(function(e){
		$('.frm_analisis').removeClass('error');
	})
});

const aux_vida_util = [];

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
    var array_aux = { fecha: aux, dia: aux_dia };
    if (create === 'created') {
        aux_vida_util.push(array_aux);
    } else {
        aux_vida_util[create] = array_aux;
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

function delete_vida(key) {
    aux_vida_util.splice(key, 1);
    $(`#fecha_${key}`).remove()
    $('#vida_util').val(JSON.stringify(aux_vida_util))
}