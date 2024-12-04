function js_cambiar_campos(campo_respuesta,
	valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, aux_id_parametro, aux_calculo){
		if(valor){
		    alert(aux_calculo)
			
            if(aux_calculo == 18 //alcalinidad 1
            || aux_calculo == 19 //alcalinidad 2
            || aux_calculo == 20 //cloruros
            || aux_calculo == 21 //dureza total
            || aux_calculo == 22 //dureza calcica
            || aux_calculo == 24 //calcio
            || aux_calculo == 25 //solidos totales
            || aux_calculo == -1 // otros analisis agua fq
            
            ){
                //alert(10)
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
		        	if(respuesta.validation){
		        		$('input#'+frm_resultado).prop('disabled', true);
		        		$('select#'+frm_resultado).attr('disabled', 'disabled');
		        		$('select').formSelect();
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('valid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('green-text text-darken-2');
		        		$('#campo_respuesta_agua_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado[0]);//para eliminar
		        		$('#campo_respuesta_irca_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado[1]);//para eliminar
		        		$('#campo_respuesta_agua_f_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado[0]);
		        		$('#campo_respuesta_irca_f_'+aux_id_ensayo_vs_muestra).html(respuesta.mensaje_resultado[1]);
		        		my_toast('<i class="fas fa-check"></i>&nbsp Resultado actualizado', 'blue darken-2', 3000);
		        	}else{
		        		$('input#'+frm_resultado).removeClass();
		        		$('input#'+frm_resultado).addClass('invalid');
		        		$('span#'+frm_resultado).removeClass();
		        		$('span#'+frm_resultado).addClass('red-text text-darken-2');
		        		my_toast('<i class="fas fa-times"></i>&nbsp Ha ocurrido un error', 'red darken-2', 3000);
		        	}
		        	$('span#'+frm_resultado).html(respuesta.mensaje);
		 
		        });
  
       		}else{ //no se esta usando en asbioquim
       		alert(101010)
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
           		
       		}
		}
}

function js_calcula_independiente(aux_id_ensayo_vs_muestra, campo, valor, funcion, e_v_m=0){
	if(valor >= 0){
		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Calculando', 'blue-grey darken-2', 30000);
        var url = $('#form_cambia_campos').attr('action');
		var data = new URLSearchParams({
			aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
			valor: valor,
			aux_e_v_m: e_v_m,
			funcion: funcion
        });
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
            console.log(respuesta);
        	$('#campo_resultado_'+campo+' b').html(respuesta.mensaje);
			$('select#campo_muestra_redondeo_'+campo).attr('disabled', 'disabled');
			$('select').formSelect();
        	my_toast('<i class="fas fa-check"></i>&nbsp Calculado', 'blue darken-2', 3000);
        });
	}
}

function cambiar_fecha_vida_util(value) {
    $('.table_resultados').hide();
    $(`#table_${value}`).show();
}
