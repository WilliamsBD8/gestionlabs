const table = [];
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

	// DataTable

	table[0] = $('#table_datatable').DataTable({
    dom: 'lrtip B',
    ajax: {
      url: `${ base_url(['funcionario', 'remisiones', 'detail', $('#frm_id_remision').val(), 'created']) }`,
      dataSrc: 'table'
    },
    columns:[
			// {title: '#', width:'25px', data:'key'},
			{title: 'Informe', data:'certificado_nro'},
			{title: 'Tipo de analisis', data:'mue_nombre'},
			{title: 'Código', data:'codigo_amc'},
			{title: 'Norma', data:'pro_nombre'},
			{title: 'Identificación', data:'mue_identificacion'},
			{title: 'Cantidad', data:'mue_cantidad'},
			{title: 'Unidad', data:'mue_unidad_medida'},
			{title: 'Opciones', className:"action_detail_2", render: (a,e, certificado) => {
				if(!certificado.certificado_nro.includes("@Informe")){
					data = `<a href="javascript:void()" onclick="quitar_detalle(${certificado.id_certificacion}, '${certificado.mue_identificacion}', '${certificado.mue_sigla} ${certificado.id_codigo_amc}')" class="pink-text tooltipped" data-position="bottom" data-tooltip="Eliminar detalle" data-detalle=""><i class="far fa-trash-alt"></i></a>`;
					data += `<a href="${base_url(['funcionario', 'remisiones', 'ticket', certificado.id_certificacion])}" class="ml-10 light-blue-text tooltipped" data-position="bottom" data-tooltip="Imprimir detalle"><i class="fas fa-print"></i></a>`
				}else return '@Opciones'
				return data
			}}
		],
    processing: true,
    serverSide: true,
    responsive: false,
    scrollX: true,
    ordering: false,
    language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
    initComplete: (data) => {
			console.log(data.json.cer);
		}
  }).on('draw', function (){
		$('.material-tooltip').remove();
    $('.tooltipped').tooltip();
		if(table[0].ajax.json().cer){
			$('#btn-remision-guardar')
				.removeAttr('disabled')
				.removeClass('blue-grey lighten-5')
				.addClass('gradient-45deg-purple-deep-orange');
		}else{
			$('#btn-remision-guardar')
				.attr('disabled', true)
				.addClass('blue-grey lighten-5')
				.removeClass('gradient-45deg-purple-deep-orange');
		}
  })

});

function reinit_table(){
	table[0].ajax.url(base_url(['funcionario', 'remisiones', 'detail', $('#frm_id_remision').val(), 'created']))
	table[0].ajax.reload()
	if(table[1] != undefined){
		$('.row.div_muestra_productos h6').html(``)
		table[1].destroy();
		table[1] = undefined;
		$('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
	}
	if(table[2] != undefined)
		table[2].clear().rows.add([]).draw()
}

async function change_producto(producto){
	var url = base_url(['funcionario/remisiones/muestra/product', producto, 'created'])
	my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando producto', 'blue-grey darken-2', 30000);
	try {
		var data = await proceso_fetch_get(url).then(info => info);
		my_toast('<i class="fas fa-check"></i>&nbsp Producto encontrado', 'blue darken-2', 3000);
		if(data.ensayos.length != 0){
			var columns = []
			var dataSet = []
			data.ensayos.forEach(e => {
				columns.push({
					title: e.par_nombre
				})
				var text = '';
				if (e.med_valor_min != null || e.med_valor_max != null)
					text += `<b>${e.med_valor_min != null ? e.med_valor_min : ''} - ${e.med_valor_max != null ? e.med_valor_max : ''}</b><br>`
				else 
					text += `<b>Sin limites</b><br>`
				text += `<label>
						<input type="checkbox" name="frm_chk_${e.id_ensayo}" id="frm_chk_${e.id_ensayo}" checked/>
						<span></span>
					</label>`
				dataSet.push(text)
			})
			if(table[1] != undefined){
				$('.row.div_muestra_productos h6').html(``)
				table[1].destroy();
				table[1] = undefined;
				$('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
			}
			table[1] = $('#table_datatable_muestra_productos').DataTable({
				dom: 't',
				columns: columns,
				data: [dataSet],
				responsive: false,
				scrollX: true,
				ordering: false,
				language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
				initComplete: () => {
					if(data.producto.n_analizar_1407 > 0){
						var aux_btn_vida = ``
						var btn_vida_fecha = `<input type="hidden" name="captura_fechas" id="captura_fechas" value="${data.producto.n_analizar_1407}">`
					}else{
						var btn_vida_fecha = `<input type="hidden" name="vida_util" id="vida_util">`
						var aux_btn_vida = `<a href="javascript:void(0)" onclick="view_vidas()" disabled class="btn ml-5 blue-grey lighten-5 blue-grey-text border-round" id="btn-vidas">Vidas utiles</a>`
					}
						
					$('#div_muestra_productos').append(`
						${btn_vida_fecha}
						<div class="row mb-2">
							<div class="col s12 centrar_button">
								<b style="width: 100%; text-align: center;">Unidad de Medida</b><br><br>
								<label>
									<input  type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="solida" checked>
									<span>S&oacute;lidas</span>
								</label>
								<label>
									<input  type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="liquida">
									<span>Liquidas</span>
								</label>
							</div>
							<div class="col s12 centrar_button mt-2" id="campo_botton_agregar">
								<a href="javascript:void(0)" onclick="send_detail()" id="btn-muestreo-form" class="btn gradient-45deg-purple-deep-orange border-round agregar_lista">Agregar a lista</a>
								${aux_btn_vida}
								<input type="hidden" name="frm_id_forma" id="frm_id_forma" value="frm_form_muestra"/>
							</div>
						</div>
					`)
					select_vida($('select#frm_analisis').val())
				}
			}).on('draw', function (){
				$('.material-tooltip').remove();
				$('.tooltipped').tooltip();
				$('.row.div_muestra_productos h6').html(`<b>Norma:</b> ${data.producto.nor_nombre}`)
			})
		}else{
			my_toast('<i class="fas fa-check"></i>&nbsp Producto encontrado sin ensayos', 'orange darken-2', 3000);
			$('.row.div_muestra_productos h6').html(``)
			table[1].destroy();
			table[1] = undefined;
			$('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
		}
	} catch (error) {
		console.error('Error al cargar la información:', error);
	}

}

async function send_detail(){
		var mensaje = '';
		if ($('#frm_identificacion').val() == null || $('#frm_identificacion').val() == "") {
			$('#frm_identificacion').addClass('invalid');
			return $('#frm_identificacion').focus();
		}else if ($('#frm_condiciones_recibido').val() == null) {
				$('.condiciones').addClass('error');
				return $('.condiciones .select-dropdown.dropdown-trigger').focus();
		} else if ($('#frm_mue_procedencia').val() == null) {
				$('.mue_procedencia').addClass('error');
				return $('.mue_procedencia .select-dropdown.dropdown-trigger').focus();
		} else if ($('#frm_analisis').val() == null || $('#frm_analisis').val() == "") {
				$('.frm_analisis').addClass('error');
				return $('.frm_analisis .select-dropdown.dropdown-trigger').focus();
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
			return Swal.fire({
				position: 'top-end',
					icon: 'error',
					text: mensaje,
			});
		}
		my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Agregando detalle', 'blue-grey darken-2', 30000);
		var frm_form = $('#frm_form').serialize();
    var frm_form_muestra = $('#frm_form_muestra').serialize();
    var frm_form_pie = $('#frm_form_pie').serialize();

		var data = frm_form + '&' + frm_form_muestra + '&' + frm_form_pie + '&buscar=3';
    await proceso_fetch($('#frm_form_muestra').attr('action'), data).then(res => {
			$('#frm_id_remision').val(res.frm_id_remision);
			aux_vida_util.splice(0, aux_vida_util.length);
			$('#table_vida_util tbody').html('');
			$('.fechas_vida_util').hide();
      $('#vida_util').val('');
			reinit_table();
			var frm_form_muestra = $('#frm_form_muestra')[0];
			formateo_forms(0, frm_form_muestra, 0);
			my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Detalle agregado', 'blue darken-2', 3000);
		});
		// var boton = $('#btn-muestreo-form');
		// boton.prop('disabled', true);
		// boton.addClass('blue-grey darken-3');
		// boton.removeClass('gradient-45deg-purple-deep-orange');
		// js_enviar_agregar_a_detalle($('#frm_form_muestra').attr('action'), 1);
}

function quitar_detalle(certificado, producto, codigo) {
	Swal.fire({
			position: 'top-end',
			icon: 'warning',
			title: 'Desea quitar de la lista el producto ' + producto + ', con Codigo ' + codigo + ' ?',
			text: 'Recuerde que se sacaran de la lista los productos con Certificados numeros mayores',
			confirmButtonColor: '#1976d2',
			cancelButtonColor: '#d32f2f',
			showDenyButton: true,
			confirmButtonText: 'Eliminar',
			denyButtonText: `Cancelar`,
	}).then((result) => {
			if (result.isConfirmed) {
					var data = 'id_certificacion=' + certificado + "&buscar=5";
					var url = $('#frm_form_pie').attr('action');
					var result = proceso_fetch(url, data);
					result.then(tabla => {
							M.toast({
									html: '<i class="fas fa-check"></i>&nbsp Detalle eliminado',
									classes: 'blue darken-2',
									displayLength: 3000,
							});
							reinit_table()
					});
			}
	})
};

const aux_vida_util = [];

async function view_vidas(){
	table[2] = undefined
	const swalWithBootstrapButtons = Swal.mixin({
		customClass: {
			confirmButton: "btn pink border-round",
		},
		buttonsStyling: false
	});
	var div = `
		<div class="fechas-vidas-utiles" style="overflow-y: auto;">
			<div class="row">
				<div class="input-field col s6">
						<input type="text" name="aux_vida_util" id="aux_vida_util" class="date_picker">
						<label for="aux_vida_util">Fecha vidas utiles</label>
				</div>
				<div class="input-field col s6">
						<input type="number" name="aux_vida_util_dia" id="aux_vida_util_dia">
						<label for="aux_vida_util_dia">Día vidas utiles</label>
				</div>
				<div class="col s12 mb-2">
						<a onclick="add_vida_util()" class="btn border-round green btn-small btn-add-vida">Agregar</a>
				</div>
			</div>
			<div id="div_vidas_utiles" class="col s12 section-data-tables mt-2">
					<table class="display" id="table_datatable_vidas_utiles"></table>
			</div>
  </div>`
	const { value: data } = await swalWithBootstrapButtons.fire({
		title: `Fechas de vidas utiles`,
		html: div,
		width: "80%",
		confirmButtonText: "Cerrar",
		confirmButton: false,
		didOpen: () => {
			swalWithBootstrapButtons.hideLoading()
			table_vida_util()
		}
	})
}

function table_vida_util(){
	var dataSet = []
	aux_vida_util.forEach((vida_util, key) => {
		var buttons = `
			<a href="javascript:void(0)" class="blue-text edit tooltipped" data-position="bottom" data-tooltip="Editar" onclick="edit_vida('${key}')"><i class="far fa-pen"></i></a>
			<a href="javascript:void(0)" class="pink-text delete tooltipped ml-10" data-position="bottom" data-tooltip="Eliminar" onclick="delete_vida('${key}')"><i class="far fa-trash-alt"></i></a>
		`
		dataSet.push([vida_util.fecha, vida_util.dia, buttons])
	})
	if (table[2] != undefined){
		table[2].clear().rows.add(dataSet).draw()
	}else{
		var columns = [
			{title:'Fecha'},
			{title:'Dia'},
			{title:'Acción'},
		]
		table[2] = $('#table_datatable_vidas_utiles').DataTable({
			dom: 'tp',
			columns: columns,
			data: dataSet,
			responsive: false,
			scrollX: true,
			ordering: false,
			displayLength: 5,
			language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
			initComplete: (data) => {}
		}).on('draw', function (){
			$('.material-tooltip').remove();
			$('.tooltipped').tooltip();
		})
	}
}

function add_vida_util(create = 'created') {
    var aux = $(`#aux_vida_util`).val();
    var aux_dia = $(`#aux_vida_util_dia`).val();
    if (aux === '')
        return my_toast('<span class="blue-text"><i class="fas fa-times"></i>&nbsp No se puede agregar una fecha vacia</span>', 'blue lighten-5', 3000);
    if (aux_dia === '')
        return my_toast('<span class="blue-text"><i class="fas fa-times"></i>&nbsp No se puede agregar un día vacio</span>', 'blue lighten-5', 3000);
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
    $(`#aux_vida_util`).val('');
    $(`#aux_vida_util_dia`).val('');
    M.updateTextFields();
    $('#vida_util').val(JSON.stringify(aux_vida_util))
		table_vida_util()
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
		table_vida_util()
}

function select_vida(value) {
	if (value == 1 || value == 2 || value == 4 || value == 3 || value == 6 || value == 5) {
		console.log(value);
		$('#btn-vidas')
			.removeAttr('disabled')
			.removeClass('blue-grey lighten-5 blue-grey-text')
			.addClass('teal lighten-5 teal-text');
	} else {
		$('#btn-vidas')
			.attr('disabled', true)
			.addClass('blue-grey lighten-5 blue-grey-text')
			.removeClass('teal lighten-5 teal-text');
		// $('#vida_util').val('');
	}
}


function btn_remision_guardar() {
	var data = $('#frm_form_pie').serialize();
	data += data + '&buscar=4';
	var url = $('#frm_form_pie').attr('action');
	var result = proceso_fetch(url, data);
	result.then(data => {
			Swal.fire({
					position: 'top-end',
					icon: 'success',
					text: data.mensaje,
			});
			var frm_form = $('#frm_form')[0];
			var frm_form_muestra = $('#frm_form_muestra')[0];
			var frm_form_pie = $('#frm_form_pie')[0];
			formateo_forms(frm_form, frm_form_muestra, frm_form_pie);
			$('input#frm_fecha_muestra').val(data.fecha);
			$('input#frm_hora_muestra').val(data.hora);

			$('#frm_nombre_empresa2').val('');
			$('#empresa_nueva').val(1);
			$('#frm_estado_remision').val(0);
			$('#frm_responsable').val(0);
			$('#frm_fecha_analisis').val('0000-00-00');
			$('#frm_fecha_informe').val('0000-00-00');
			$('#frm_id_remision').val(0);

			reinit_table()
			
			$('#frm_nombre_empresa').val('');
			init_select();
	});
};

async function view_fechas(){
	const { value: data } = await Swal.fire({
		title: 'Añadir Fecha',
		html: `<p>
					<label>
							<input value="${$('#fechas_1407').val()}" id="value_modal" type="number" class="validate">
							<label for="value_modal">Valor añadir</label>
					</label>
			</p>`,
		preConfirm: () => {
				const value = $('#value_modal').val();
				if (!value) {
						Swal.showValidationMessage('El valor a añadir es necesario.'); // Mostrar mensaje de error
				} else {
						return { 'value': parseInt(value)};
				}
		}
	});
	if (data) {
		$('#fechas_1407').val(data.value)
	}
}