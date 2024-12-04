function js_cambiar_campos(campo_respuesta,
	valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, aux_id_parametro, aux_calculo){
		if(valor){
		    //alert(valor + '<-->' + aux_id_parametro);
		
        	    //alert($('#form_cambia_campos').attr('action'))
        		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Cambiando resultado', 'blue-grey darken-2', 30000);
        		var url = $('#form_cambia_campos').attr('action');
        		var data = new URLSearchParams({
        			campo_respuesta: campo_respuesta,
					valor: valor,
					frm_resultado: frm_resultado,
					resultado_analisis: resultado_analisis,
					aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
					aux_id_parametro: aux_id_parametro,
					aux_calculo: aux_calculo,
					funcion: 'cambiar_campos'
		        });
		        var result = proceso_fetch(url, data.toString());
		        result.then(respuesta => {
		        	console.log(respuesta);
		        	console.log('resultado '+aux_id_ensayo_vs_muestra);
		        	if(respuesta.validation){
		        		$('input#'+frm_resultado).prop('disabled', true);
		        		$('select#'+frm_resultado).attr('disabled', 'disabled');
		        		$('select').formSelect();
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('valid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('green-text text-darken-2');
		        		//$('#campo_repuesta_mensaje_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado);//para eliminar cuando se implemente la formula
		        		$('#campo_repuesta_mensaje_f_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado);
		        		$('#campo_respuesta_alimento_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado);
		        		my_toast('<i class="fas fa-check"></i>&nbsp Resultado actualizado ', 'blue darken-2', 3000);
		        	}else{
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('invalid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('red-text text-darken-2');
		        		my_toast('<i class="fas fa-times"></i>&nbsp Ha ocurrido un error 1a', 'red darken-2', 3000);
		        	}
		        	$('span#'+frm_resultado).html(respuesta.mensaje);
		        })
        
		}
}
function js_calcula_independiente(aux_id_ensayo_vs_muestra, campo, valor, funcion, aux_calculo=0){
    // cambiar_campos_resultados_fq
	if(valor >= 0){
	    //alert(aux_id_ensayo_vs_muestra+' campo: '+campo+' valor: ' +valor+' funcion;'+funcion+' aux_calculo:'+aux_calculo);
	    //alert($('#form_cambia_campos').attr('action'))
		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Calculando', 'blue-grey darken-2', 30000);
        var url = $('#form_cambia_campos').attr('action');
		var data = new URLSearchParams({
			aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
			valor: valor,
			aux_calculo: aux_calculo,
			funcion: funcion
        });
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
            
        	$('#campo_resultado_'+campo).html(respuesta.mensaje);//campo_resultado_compuesto_1_0
			$('select#campo_muestra_redondeo_'+campo).attr('disabled', 'disabled');
			$('select').formSelect();
        	my_toast('<i class="fas fa-check"></i>&nbsp Calculado ', 'blue darken-2', 3000);
        });
	}
}

function cambiar_fecha_vida_util(value) {
    $('.table_resultados').hide();
    $(`#table_${value}`).show();
}
$(document).ready(function(){
});