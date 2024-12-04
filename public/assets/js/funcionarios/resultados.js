$(function(){});

function cambiar_fecha_vida_util(value) {
    $('.fechas_util_parametro').hide();
    $(`#fecha_util_${value}`).show();
}

function js_muestra_tip(id_muestreo, codigo_amc, parametro, codigo, nombre_producto, mensaje = 0, min = 0, max = 0) {
    // $('.mensaje_'+id_muestreo+' .row').remove();
    var mensaje = (mensaje) ? `<div class="col s12 l12"><b>Rango:</b> ${ mensaje } </div>` : '';
    var min = (min != 0) ? `<div class="col s12 l6"><b>Valor Minimo:</b> ${ min } </div>` : '';
    var max = (max != 0) ? `<div class="col s12 l6"><b>Valor Maximo:</b> ${ max } </div>` : '';
    var result =
        `<div class="row">
            <div class="col s12 l6"><b>Codigo:</b> ${codigo_amc}</div>
            <div class="col s12 l6"><b>Parametro:</b> ${parametro}</div>
            <div class="col s12 l6"><b>Producto:</b> ${nombre_producto}</div>
            <div class="col s12 l6"><b>Identificacion:</b> ${codigo}</div>
            ${mensaje}
            ${min}
            ${max}
        </div>
        <hr>`;
    $('.mensaje_' + id_muestreo).html(result);
}

function js_cambiar_campos(campo_respuesta, valor, frm_resultado, resultado_analisis,  aux_id_ensayo_vs_muestra, id_tecnica, id_tipo_analisis, mohos_levaduras=0, frm_dilucion=0){
    if(valor){
        my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Actualizando resultado', 'blue-grey darken-2', 3000);
        
        var id_detalle_muestreo = $(`#id_detalle_muestreo_${aux_id_ensayo_vs_muestra}`).val();
        var fechaUtil = $(`#select_${id_detalle_muestreo}`).val() !== undefined ? $(`#select_${id_detalle_muestreo}`).val() : '';
        var data = new URLSearchParams({
            campo_respuesta: campo_respuesta,
            valor: valor,
            frm_resultado: frm_resultado,
            resultado_analisis: resultado_analisis,
            aux_id_ensayo_vs_muestra: aux_id_ensayo_vs_muestra,
            id_tecnica: id_tecnica,
            id_tipo_analisis: id_tipo_analisis,
            mohos_levaduras: mohos_levaduras,
            dilucion: frm_dilucion,
            fechaVidaUtil: fechaUtil
        });
        var url = $('#form_resultado').attr('action');
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
            console.log(respuesta);
            if(respuesta.hide){
                // $('#'+respuesta.campo_frm).prop('disabled', true);
                my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Resultado actualizado', 'blue darken-2', 3000);
            }
                var campo_mensaje = respuesta.campo_mensajes;
                $('#'+respuesta.campo_frm).removeClass();
                $('#'+respuesta.campo_frm).addClass(respuesta.style);

                var campo_respuesta = respuesta.campo_respuesta;
                $('#'+campo_respuesta).html(respuesta[campo_respuesta]);
                $('#'+campo_mensaje).html(respuesta[campo_mensaje]);

        });
    }
}

function guardar(id) {
    var input_1 = $(`#frm_resultado${id}`);
    var input_2 = $(`#frm_resultado2${id}`);
    if (input_1.val() != '') {
        input_1.prop('disabled', true)
    }
    if (input_2.val() != '') {
        input_2.prop('disabled', true)
    }
}

async function confirmation(input, id, resultado, a, b, c){
    const { value: data } = await Swal.fire({
		title: 'Confirmación',
		html: `
            <p>
                <label>
                    <input value="${a == 'false' ? '' : a}" id="aux_a" type="number" class="validate">
                    <label for="aux_a">Colonias seleccionadas</label>
                </label>
			</p>
            <p>
                <label>
                    <input value="${b == 'false' ? '' : b}" id="aux_b" type="number" class="validate">
                    <label for="aux_a">Colonias confirmadas</label>
                </label>
			</p>
            <p>
                <label>
                    <input value="${c == 'false' ? '' : c}" id="aux_c" type="number" class="validate">
                    <label for="aux_a">Total colonias</label>
                </label>
			</p>`,
		preConfirm: () => {
				const aux_a = $('#aux_a').val();
				const aux_b = $('#aux_b').val();
				const aux_c = $('#aux_c').val();
				if (!aux_a) {
					Swal.showValidationMessage('El valor de Colonias seleccionadas es necesario.'); // Mostrar mensaje de error
				}else if(!aux_b){
                    Swal.showValidationMessage('El valor de Colonias confirmadas es necesario.'); // Mostrar mensaje de error
                }else if(!aux_c){
                    Swal.showValidationMessage('El valor de Total colonias es necesario.'); // Mostrar mensaje de error
                }else {
                    const result = parseInt(aux_b)/parseInt(aux_a)*parseInt(aux_c)
					return { 'result': parseInt(result), 'confirmacion_a': aux_a, 'confirmacion_b': aux_b, 'confirmacion_c': aux_c};
				}
		}
	});
	if (data) {
        data.resultado = resultado;
        data.id = id;
        var data_send = JSON.stringify(data);
		$(`#${input}${id}`).val(data.result)
        var url = base_url(['funcionario/resultados/confirmacion']);
        await new_proceso_fetch(url, data_send).then(result => {
            $(`#confirmation_${id}_${resultado}`).attr('onclick', `confirmation('${input}', '${id}', ${resultado}, ${data.confirmacion_a}, ${data.confirmacion_b}, ${data.confirmacion_c})`);
            
            var miElemento = document.getElementById(`${input}${id}`);
    
            // Crea un nuevo evento 'blur'
            var eventoBlur = new Event('blur');
    
            // Dispara el evento 'blur' en el elemento
            miElemento.dispatchEvent(eventoBlur);
        })
	}
}

async function change_date_1407(id_muestreo_detalle){
    var muestras = date_fechas();
    var muestra = muestras.find(m => m.id_muestreo_detalle == id_muestreo_detalle)
    var fechas = muestra.fechasUtiles
    var fecha = $(`#select_${id_muestreo_detalle}`).val();
    var fecha_aux = fechas.find(f => f.id == fecha)
    console.log(fecha_aux);
    const { value: data } = await Swal.fire({
		title: 'Cambio de Fecha',
		html: `
            <p>
                <label>
                    <input id="fecha_change" type="text" class="validate">
                    <label for="fecha_change">Fecha</label>
                </label>
			</p>`,
		preConfirm: () => {
				const fecha = $('#fecha_change').val();
				if (!fecha) {
					Swal.showValidationMessage('La fecha es necesaria.'); // Mostrar mensaje de error
				}else {
                    fecha_aux.fecha = fecha
					return fecha_aux;
				}
		}
	});
    if (data) {
        var url = base_url(['funcionario/resultados/date/fecha']);
        await new_proceso_fetch(url, JSON.stringify(data)).then(result => {
            $(`#fecha_${result.id}`).text(`[${result.dia}] ${result.fecha}`);
            $(`#select_${id_muestreo_detalle}`).formSelect();
        })
	}
}

// function download(analisis) {
//     var objeto = new Object;
//     objeto["0"] = 'Todos';
//     analisis.forEach(element => {
//         var id = element.id_muestra_tipo_analsis;
//         objeto[id] = `${element.mue_nombre} | ${element.mue_sigla}`

//     });
//     var tipo_analisis = 0;
//     Swal.fire({
//         title: 'Ingrese la fecha de toma de la muestra',
//         html: `
//             <div class="input-field col s12">
//             <input id="sweet_date" type="date" class="validate">
//             <label for="sweet_date">Fecha de toma de la muestra</label>
//             </div>`,
//         showCancelButton: true,
//         input: 'select',
//         inputOptions: objeto,
//         inputLabel: 'Tipo de analisis',
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Descargar',
//         inputValidator: (value) => {
//             return new Promise((resolve) => {
//                 tipo_analisis = value;
//                 resolve()
//             })
//         }
//     }).then((result) => {
//         if (result.isConfirmed) {
//             var date = $('#sweet_date').val();
//             if (date.length != 0) {
//                 let url = $('#resultados_download').attr('action');
//                 var data = new URLSearchParams({
//                     'consulta': true,
//                     'date_download': date,
//                     'tipo_analisis': tipo_analisis
//                 })
//                 var result = proceso_fetch(url, data.toString());
//                 result.then(response => {
//                     // console.log(response);
//                     if (response.length > 0) {
//                         $("#date_download").val(date);
//                         $("#tipo_analisis").val(tipo_analisis);
//                         $('#resultados_download').submit();
//                         $('#resultados_download')[0].reset();
//                     } else {
//                         var analisis = objeto[tipo_analisis]
//                         Swal.fire({
//                             icon: 'warning',
//                             title: `No se encontraron muestras realizadas el dia ${date} con tipo de analisis ${analisis}`
//                         })
//                     }
//                 })
//             }
//         }
//     })
// }