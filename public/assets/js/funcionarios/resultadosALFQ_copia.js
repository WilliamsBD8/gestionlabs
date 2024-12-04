function js_cambiar_campos(campo_respuesta,
	valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, aux_id_parametro){
		if(valor){
			if(
            	aux_id_parametro == 24
            	|| aux_id_parametro == 39
            	|| aux_id_parametro ==25
            	|| aux_id_parametro ==26
            	|| aux_id_parametro ==27
            	|| aux_id_parametro ==29
            	|| aux_id_parametro ==126
            	|| aux_id_parametro ==151 // Acidez Titulable Total
            	|| aux_id_parametro ==157 // Acidez Titulable Nectares
            	|| aux_id_parametro ==28
            	|| aux_id_parametro ==131
            	|| aux_id_parametro ==188
            	|| aux_id_parametro ==120 //N volatil total 
            	|| aux_id_parametro ==111 //N volatil total 
        	){
        		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Cambiando resultado', 'blue-grey darken-2', 30000);
        		var url = $('#form_cambia_campos').attr('action');
        		var data = new URLSearchParams({
        			campo_respuesta: campo_respuesta,
					valor: valor,
					frm_resultado: frm_resultado,
					resultado_analisis: resultado_analisis,
					aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
					aux_id_parametro: aux_id_parametro,
					funcion: 'cambiar_campos'
		        });
		        var result = proceso_fetch(url, data.toString());
		        result.then(respuesta => {
		        	console.log(respuesta);
		        	if(respuesta.validation){
		        		$('input#'+frm_resultado).prop('disabled', true);
		        		$('select#'+frm_resultado).attr('disabled', 'disabled');
		        		$('select').formSelect();
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('valid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('green-text text-darken-2');
		        		$('#campo_repuesta_mensaje_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado);
		        		my_toast('<i class="fas fa-check"></i>&nbsp Resultado actualizado', 'blue darken-2', 3000);
		        	}else{
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('invalid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('red-text text-darken-2');
		        		my_toast('<i class="fas fa-times"></i>&nbsp Ha ocurrido un error', 'red darken-2', 3000);
		        	}
		        	$('span#'+frm_resultado).html(respuesta.mensaje);
		        })
        		// xajax_cambiar_campos_resultados_fq(campo_respuesta,valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, aux_id_parametro);
        		// xajax_calcula_solidos_totales(aux_id_ensayo_vs_muestra);   
       		}else{
       			my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Cambiando resultado', 'blue-grey darken-2', 30000);
        		var url = $('#form_cambia_campos').attr('action');
        		var data = new URLSearchParams({
        			campo_respuesta: campo_respuesta,
					valor: valor,
					frm_resultado: frm_resultado,
					resultado_analisis: resultado_analisis,
					aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
					aux_id_parametro: aux_id_parametro,
					funcion: 'cambiar_campos_resultados_fq_directo'
		        });
		        var result = proceso_fetch(url, data.toString());
		        result.then(respuesta =>{
		        	console.log([respuesta, frm_resultado]);
		        	if(respuesta.validation){
		        		$('input#'+frm_resultado).prop('disabled', true);
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('valid');
		        		$('span#'+campo_respuesta).removeClass();
		        		$('span#'+campo_respuesta).addClass('green-text text-darken-2');
		        		my_toast('<i class="fas fa-check"></i>&nbsp Resultado actualizado', 'blue darken-2', 3000);
		        	}else{
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('invalid');
		        		$('span#'+campo_respuesta).removeClass();
		        		$('span#'+campo_respuesta).addClass('red-text text-darken-2');
		        		my_toast('<i class="fas fa-times"></i>&nbsp Ha ocurrido un error', 'red darken-2', 3000);
		        	}
		        	$('span#'+campo_respuesta).html(respuesta.mensaje);
		        })
           		// xajax_cambiar_campos_resultados_fq_directo(campo_respuesta,valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, aux_id_parametro);
       		}
		}
}
function js_calcula_independiente(aux_id_ensayo_vs_muestra, campo, valor, funcion){
	if(valor >= 0){
		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Calculando', 'blue-grey darken-2', 30000);
        var url = $('#form_cambia_campos').attr('action');
		var data = new URLSearchParams({
			aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
			valor: valor,
			funcion: funcion
        });
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
        	$('.campo_resultado_'+campo+' b').html(respuesta.mensaje);
			$('select#campo_muestra_redondeo_'+campo).attr('disabled', 'disabled');
			$('select').formSelect();
        	my_toast('<i class="fas fa-check"></i>&nbsp Calculado', 'blue darken-2', 3000);
        });
	}
}
$(document).ready(function(){
});