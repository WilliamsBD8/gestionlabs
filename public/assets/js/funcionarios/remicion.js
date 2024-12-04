function empresa_blur(){
	$('.empresa_row small').html('');
	var data = "frm_nombre_empresa="+$('#frm_nombre_empresa').val()+"&buscar=2";
	var form = $('#frm_form');
	var url = form.attr('action');
	fetch(url, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
		body: data
	})
	.then(response => {
	    if (!response.ok) throw Error(response.status);
	    return response.json();
	}).then(empresa => {
	 	$('#frm_nombre_empresa_subtitulo').val(empresa.sucursal);
	 	$('#frm_nit').val(empresa.id);
	 	$('.input-field.username').remove();
	 	if(empresa.id == ''){
	 		$('#frm_nit').prop('disabled', false);
	 		$('.input-field.nit').removeClass('l6');
	 		$('.input-field.nit').addClass('l2');
	 		$('.input-field.empresa').after(
	 			'<div class="input-field col l4 s12 username">'+
                    '<input id="username" name="username" type="text" class="validate">'+
                    '<label for="username">Usuario</label>'+
                    '<small class=" red-text text-darken-4" id="username"></small>'+
                '</div>');
	 		$('#username').focus();
	 	}else{
	 		$('#frm_nit').prop('disabled', true);
	 		$('.input-field.nit').removeClass('l2');
	 		$('.input-field.nit').addClass('l6');
	 		$('#frm_nombre_empresa_subtitulo').focus();
	 	}
	 	$('#frm_contacto_cargo').val(empresa.use_cargo);
	 	$('#frm_contacto_nombre').val(empresa.use_nombre_encargado);
	 	$('#frm_telefono').val(empresa.use_telefono);
	 	$('#frm_fax').val(empresa.use_fax);
	 	$('#frm_correo').val(empresa.email);
	 	$('#frm_direccion').val(empresa.use_direccion);
	 	$('#frm_fecha_muestra').val(empresa.fecha);
	 	$('#frm_hora_muestra').val(empresa.hora);
	 	$('#frm_nombre_empresa2').val(empresa.id);
	 	$(".empresa_row label").addClass('active');
	}).catch(error => {
	 	$('#frm_nombre_empresa').focus();
	 	$('#frm_nombre_empresa').next().focus(); 
	});
};
function producto_blur(){
	$('#frm_producto').next().focus();
	var producto = $('#frm_producto').val();
	var form = $('#frm_form_muestra');
	var url = form.attr('action');
	var data = form.serialize();
	data += "&buscar=2";
	fetch(url, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
		body: data
	}).then(response => {
	    if (!response.ok) throw Error(response.status);
	    return response.json();
	 }).then(tabla => {
	 	$('.tabla-productos').remove();
	 	$('#frm_form_muestra .row.finish').after(tabla);
	 });
};
$(document).ready(function(){
	$('input.autocomplete').autocomplete({data: {}});
	$('form').keypress(function(e) {
        if (e.which == 13)
            return false;
    });
	$('#frm_nombre_empresa').keyup(function(e){
		var empresa = $('#frm_nombre_empresa').val();
		var form = $('#frm_form');
		var url = form.attr('action');
		var tecla = e.which;
		if(empresa != "" && tecla != 37 && tecla != 38 && tecla != 39 && tecla != 40){
			var data = "frm_nombre_empresa="+empresa+"&buscar=1";
			fetch(url, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
				body: data
			})
			.then(response => {
			    if (!response.ok) throw Error(response.status);
			    return response.json();
			 }).then(lista => {
				$('.autocomplete.frm_nombre_empresa').autocomplete('updateData',lista);
			 	$('.autocomplete.frm_nombre_empresa').autocomplete('open');
			 });
		}
	});
	$('.empresa .autocomplete-content').click(function(e){
		$('#frm_nombre_empresa').focus();
		$('#frm_nombre_empresa').next().focus();
	});

	var boton_empresa = $('#btn-empresa');
	boton_empresa.click(function(e){
		e.preventDefault();
		$('.empresa_row small').html('');
		var form = $('#frm_form');
		var url = form.attr('action');
		var data = form.serialize();
		data+='&frm_fecha_muestra='+$('#frm_fecha_muestra').val()+'&frm_hora_muestra='+$('#frm_hora_muestra').val()+'&buscar='+empresa_nueva+'&frm_nit='+$('#frm_nit').val();
		boton_empresa.prop('disabled', true);
		boton_empresa.removeClass('gradient-45deg-purple-deep-orange');
		boton_empresa.addClass('blue-grey darken-3');
		boton_empresa.html('Guardando empresa <i class="fas fa-spinner fa-spin"></i>');
		fetch(url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
			body: data
		})
		.then(response => {
		    if (!response.ok) throw Error(response.status);
		    return response.json();
		 }).then(result => {
		 	if(result.success){
		 		// $('#remicion-form')[0].reset();
		 		$(".empresa_row .frm_hora_muestra label").addClass('active');
		 		$('.empresa_row small').html('');
		 		Swal.fire({
					position: 'top-end',
				  	icon: 'success',
				  	text: result.success,
				});
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
		 }).catch(error => {
		 	boton_empresa.addClass('gradient-45deg-purple-deep-orange');
			boton_empresa.removeClass('blue-grey darken-3');
			boton_empresa.prop('disabled', false);
			boton_empresa.html('Guardar empresa');
		 });
	});

// Muestra
	$('#frm_producto').keyup(function(e){
		var producto = $('#frm_producto').val();
		var form = $('#frm_form_muestra');
		var url = form.attr('action');
		var tecla = e.which;
		if(producto != "" && tecla != 37 && tecla != 38 && tecla != 39 && tecla != 40){
			var data = "frm_producto="+producto+"&buscar=1";
			fetch(url, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
				body: data
			}).then(response => {
			    if (!response.ok) throw Error(response.status);
			    return response.json();
			 }).then(lista => {
				$('.autocomplete.frm_producto').autocomplete('updateData', lista);
				$('.autocomplete.frm_producto').autocomplete('open');
			 });
		}
	});
	$('.producto .autocomplete-content').click(function(e){
		$('#frm_producto').focus();
		$('#frm_producto').next().focus();
	});
    $('#frm_form_muestra').validate({
		rules: {
			frm_identificacion: {required:true},
			frm_analisis: { minlength: 1 },
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
			if($('#frm_analisis').val() == ''){
				var select = false;
				$('.frm_analisis').addClass('error');
				$('.select-dropdown.dropdown-trigger').focus();
			}else if($('#frm_nombre_empresa').val() == ''){
				mensaje = 'Seleccione una empresa o registre una.';
				$('input#frm_nombre_empresa').addClass('invalid');
			}else if($('#frm_entrega').val() == ''){
				mensaje = 'Registre una persona quien entrego la muestra.';
				$('input#frm_entrega').addClass('invalid');
			}else if($('#frm_recibe').val() == ''){
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
				js_enviar_agregar_a_detalle();
			}
		}
	});
	$('.frm_analisis li').click(function(e){
		$('.frm_analisis').removeClass('error');
	});

	function js_enviar_agregar_a_detalle(){
		var url = $('#frm_form_muestra').attr('action');
		var frm_form 			= $('#frm_form').serialize();
		var frm_form_muestra 	= $('#frm_form_muestra').serialize();
		var frm_form_pie 		= $('#frm_form_pie').serialize();
		var data = 'buscar=3&'+frm_form+'&'+frm_form_muestra+'&'+frm_form_pie;
		fetch(url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
			body: data
		}).then(response => {
		    if (!response.ok) throw Error(response.status);
		    return response.json();
		 }).then(tabla => {
		 	$('#campo_detalle_muestras_basic').hide();
		 	$('#campo_detalle_muestras').remove();
		 	$('#tabla_detalles_muestras').after(tabla.tabla);
		 	$('.row.boton_guardar_remicion .centrar_button').remove();
		 	$('.row.boton_guardar_remicion').append(tabla.boton);
		 	$('#frm_form_muestra').find('input:text, input:password, input:file, select, textarea').val('');
    		$('#frm_form_muestra').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		 	$('.tabla-productos').remove();
		 	$('#frm_id_remision').val(tabla.frm_id_remision);
		 	$('.tooltipped').tooltip();
		 });
	};


});
	function btn_remicion_guardar(){
		var data = $('#frm_form_pie').serialize();
		data += data+'&buscar=4';
		var url = $('#frm_form_pie').attr('action');
		fetch(url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
			body: data
		})
		.then(response => {
		    if (!response.ok) throw Error(response.status);
		    return response.json();
		}).then(data => {
			Swal.fire({
				position: 'top-end',
			  	icon: 'success',
			  	text: data,
			});
			$('#campo_detalle_muestras_basic').show();
			$('#campo_detalle_muestras').remove();
			$('form').find('input:text, input:password, input:file, select, textarea').val('');
    		$('form').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    		$('.tooltipped').tooltip();
		});
	};
	function quitar_detalle(certificado){
		// e.preventDefault();
		var data = 'id_certificacion='+certificado+"&buscar=5";
		var url = $('#frm_form_pie').attr('action');
		fetch(url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
			body: data
		})
		.then(response => {
		    if (!response.ok) throw Error(response.status);
		    return response.json();
		}).then(tabla => {
			$('#campo_detalle_muestras').remove();
		 	$('#tabla_detalles_muestras').after(tabla);
			$('#frm_form_muestra').find('input:text, input:password, input:file, select, textarea').val('');
    		$('#frm_form_muestra').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		});
	};