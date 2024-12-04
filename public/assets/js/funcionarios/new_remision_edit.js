const tables = [];
const aux_vida_util = [];
const c_detalle_muestra = [];
$(() => {
    $('input#frm_mue_procedencia').autocomplete({
    data: {
        "Asbioquim S.A.S": null,
        "Cliente": null
    },
  });
  $('form').keypress(function(e) { if (e.which == 13) return false; });
  $('input#frm_nit').blur(function() { $('input#frm_nit').removeClass('invalid'); });
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
  tables[0] = $("#table_datatable")
    .DataTable({
      dom: "lrtip B",
      ajax: {
        url: `${base_url([
          "funcionario",
          "remisiones",
          "detail",
          $("#frm_id_remision").val(),
          "edit"
        ])}`,
        dataSrc: "table",
      },
      columns: [
        // {title: '#', width:'25px', data:'key'},
        { title: "Informe", data: "certificado_nro" },
        { title: "Tipo de analisis", data: "mue_nombre" },
        { title: "Código", data: "codigo_amc" },
        { title: "Norma", data: "pro_nombre" },
        { title: "Identificación", data: "mue_identificacion" },
        { title: "Cantidad", data: "mue_cantidad" },
        { title: "Unidad", data: "mue_unidad_medida" },
        {
          title: "Opciones",
          className: "action_detail_2",
          render: (a, e, certificado) => {
            if (!certificado.certificado_nro.includes("@Informe")) {
              data = `<a href="javascript:void(0);" onclick="buscar_detalle('${certificado.id_muestreo_detalle}')" class="indigo-text tooltipped" data-position="bottom" data-tooltip="Editar detalle"><i class="far fa-edit"></i></i></a>`;
              data += `<a href="${base_url([
                "funcionario",
                "remisiones",
                "ticket",
                certificado.id_certificacion,
              ])}" class="ml-10 light-blue-text tooltipped" data-position="bottom" data-tooltip="Imprimir detalle"><i class="fas fa-print"></i></a>`;
            } else return "@Opciones";
            return data;
          },
        },
      ],
      processing: true,
      serverSide: true,
      responsive: false,
      scrollX: true,
      ordering: false,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json",
      },
      initComplete: (data) => {
        console.log(data.json.cer);
      },
    })
    .on("draw", function () {
      $(".material-tooltip").remove();
      $(".tooltipped").tooltip();
        if (tables[0].ajax.json().cer) {
          $("#btn-remision-guardar")
            .removeAttr("disabled")
            .removeClass("blue-grey lighten-5")
            .addClass("gradient-45deg-purple-deep-orange");
        } else {
          $("#btn-remision-guardar")
            .attr("disabled", true)
            .addClass("blue-grey darken-4")
            .removeClass("gradient-45deg-purple-deep-orange");
        }
    });
});

async function search_muestra(){
    if ($('#frm_certificados_editar').val() == '' && $('#frm_muestra_editar').val() == '')
        return my_toast('<i class="fas fa-times"></i>&nbsp Numero de informe y numero de muestra vacios', 'red darken-2', 3000);
    else if ($('#frm_certificados_editar').val() == '' && ($('#frm_muestra_editar').val() == '' || $('#frm_muestra_editar_anio').val() == '') )
        return my_toast('<i class="fas fa-times"></i>&nbsp Código de informe o año de muestra vacio', 'red darken-2', 3000);
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Buscando informe', 'blue-grey darken-2', 30000);
    var form = $('#myform')
    var url = form.attr('action');
    var data = form.serialize();
    await proceso_fetch(url, data).then(data => {
        if(data.result){
            recibe_entrega(data.muestra, data.certificado);
            var data_aux = {
                id: data.cliente.id,
                text: data.cliente.name
            };
            var newOption = new Option(data_aux.text, data_aux.id, true, true);
            $('#frm_nombre_empresa').append(newOption).trigger('change');
            // tabla_detalles_muestras(data.tabla);
            $('#frm_producto').attr('disabled', true);
            $('#frm_id_remision').val(data.muestra.id_muestreo)
            reinit_table()
        } else my_toast('<i class="fas fa-times"></i>&nbsp No se encontro informe', 'amber darken-2', 3000);
    });
}

async function buscar_detalle(detalle_muestra) {
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Cargando detalle', 'blue-grey darken-2', 300000);
    var detail = tables[0].rows().data().toArray().find(detail => detail.id_muestreo_detalle == detalle_muestra);
    console.log([detail, detalle_muestra]);
    $('#id_muestra_detalle').val(detalle_muestra);
    $('#frm_identificacion').val(detail.detalle.mue_identificacion);
    $('#frm_lote').val(detail.detalle.mue_lote);
    // const pattern = /[a-zA-Z]/g;
    const pattern = /^(?:(?:(?:0?[1-9]|1\d|2[0-8])[/-](?:0?[1-9]|1[0-2])|(?:29|30)[/-](?:0?[13-9]|1[0-2])|31[/](?:0?[13578]|1[02]))[/-](?:0{2,3}[1-9]|0{1,2}[1-9]\d|0?[1-9]\d{2}|[1-9]\d{3})|29[/-]0?2[/-](?:\d{1,2}(?:0[48]|[2468][048]|[13579][26])|(?:0?[48]|[13579][26]|[2468][048])00))$/;
    var fecha_produ = detail.detalle.mue_fecha_produccion;
    var fecha_vence = detail.detalle.mue_fecha_vencimiento;

    if (!pattern.test(fecha_produ)) {
      $('#frm_fecha_produccion_check').prop('checked', false);
      changeType('frm_fecha_produccion')
    } else {
      $('#frm_fecha_produccion_check').prop('checked', true);
      changeType('frm_fecha_produccion')
    }
    if (!pattern.test(fecha_vence)) {
      $('#frm_fecha_vencimiento_check').prop('checked', false);
      changeType('frm_fecha_vencimiento')
    } else {
      $('#frm_fecha_vencimiento_check').prop('checked', true);
      changeType('frm_fecha_vencimiento')
    }

    $('#frm_fecha_produccion').val(detail.detalle.mue_fecha_produccion);
    $('#frm_fecha_vencimiento').val(detail.detalle.mue_fecha_vencimiento);

    $('#frm_tmp_muestreo').val(detail.detalle.mue_temperatura_muestreo);
    $('#frm_momento_muestreo').val(detail.detalle.mue_momento_muestreo);
    $('#frm_tmp_recepcion').val(detail.detalle.mue_temperatura_laboratorio);
    $('#frm_condiciones_recibido').val(detail.detalle.mue_condiciones_recibe);
    $('#frm_cantidad').val(detail.detalle.mue_cantidad);
    $('#frm_mue_dilucion').val(detail.detalle.mue_dilucion);
    $('#frm_mue_empaque').val(detail.detalle.mue_empaque);

    $('#frm_mue_procedencia').val(detail.detalle.mue_procedencia);

    $('#frm_parametro').val(detail.detalle.mue_parametro);
    $('#frm_area').val(detail.detalle.mue_area);
    $('#frm_tipo_muestreo').val(detail.detalle.mue_tipo_muestreo);
    $('#frm_adicional').val(detail.detalle.mue_adicional);
    $('#frm_analisis').val(detail.detalle.id_tipo_analisis);
    $('#frm_producto').attr('disabled', false);
    $('#frm_producto').val(detail.id_producto);
    $('.select-form').formSelect();
    $("#frm_producto").select2({
        dropdownAutoWidth: !0,
        width: "100%"
    })
    $('.tooltipped').tooltip();

    // var fechas = detail.detalle.fechas;
    // $('#vida_util').val(JSON.stringify(fechas));
    // select_vida(result.detalle.id_tipo_analisis);
    
    // $(`#aux_vida_util`).val('');
    // $(`#aux_vida_util_dia`).val('');
    M.updateTextFields();

    /** Carga de DataTable */
    if(detail.ensayos.length != 0){
      var columns = []
      var dataSet = []
      detail.ensayos.forEach(e => { // Creamos los checks de los ensayos
        columns.push({
          title: e.par_nombre
        })
        var text = '';
        if (e.med_valor_min != null || e.med_valor_max != null)
          text += `<b>${e.med_valor_min != null ? e.med_valor_min : ''} - ${e.med_valor_max != null ? e.med_valor_max : ''}</b><br>`
        else 
          text += `<b>Sin limites</b><br>`
        text += `<label>
            <input type="checkbox" name="frm_chk_${e.id_ensayo}" id="frm_chk_${e.id_ensayo}" ${e.is_checked}/>
            <span></span>
          </label>`
        dataSet.push(text)
      })
      if(tables[1] != undefined){ // Destruimos ensayos existente
        $('.row.div_muestra_productos h6').html(``)
        tables[1].destroy();
        tables[1] = undefined;
        $('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
      }
      tables[1] = $('#table_datatable_muestra_productos').DataTable({
        dom: 't',
        columns: columns,
        data: [dataSet],
        responsive: false,
        scrollX: true,
        ordering: false,
        initComplete: () => {
          detail.fechas.forEach(fecha => {
            aux_vida_util.push(fecha);
          });
          if(detail.producto.n_analizar_1407 > 0){
            var btn_vida_fecha = `<input type="hidden" name="captura_fechas" id="captura_fechas" value="${detail.producto.n_analizar_1407}">`
            var aux_btn_vida = ``
          }else{
            var btn_vida_fecha = `<input type="hidden" name="vida_util" id="vida_util" value="">`
            var aux_btn_vida = `<a href="javascript:void(0)" onclick="view_vidas()" disabled class="btn ml-5 blue-grey lighten-5 blue-grey-text border-round agregar_lista" id="btn-vidas">Vidas utiles</a>`
          }
          $('#div_muestra_productos').append(`
            ${btn_vida_fecha}
            <div class="row mb-2">
              <div class="col s12 centrar_button">
                <b style="width: 100%; text-align: center;">Unidad de Medida</b><br><br>
                <label>
                  <input type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="solida" ${detail.mue_unidad_medida == 'solida' ? 'checked' : ''}>
                  <span>S&oacute;lidas</span>
                </label>
                <label>
                  <input type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="liquida" ${detail.mue_unidad_medida == 'liquida' ? 'checked' : ''} >
                  <span>Liquidas</span>
                </label>
              </div>
              <div class="col s12 centrar_button mt-2" id="campo_botton_agregar">
                <a href="javascript:void(0)" onclick="send_detail()" id="btn-muestreo-form" class="btn gradient-45deg-purple-deep-orange border-round agregar_lista">Editar detalle</a>
                ${aux_btn_vida}
                <input type="hidden" name="frm_id_forma" id="frm_id_forma" value="frm_form_muestra"/>
              </div>
            </div>
            <div class="col s12 mt-2 centrar_button">
              <label>
                <input type="checkbox" name="frm_elimina_resultado" id="frm_elimina_resultado" value="SI" checked/>
                <span>Eliminar resultado ingresados.</span>
              </label>
              <input type="hidden" name="frm_id_muestra_detalle" id="frm_id_muestra_detalle" value="${detalle_muestra}"/>
              <input type="hidden" name="frm_id_codigo_amc" id="frm_id_codigo_amc" value="${detail.id_codigo_amc}"/>
              <input type="hidden" name="frm_id_certificado" id="frm_id_certificado" value="${detail.certificado_nro}"/>
              <input type="hidden" name="frm_id_muestreo" id="frm_id_muestreo" value="${detail.id_muestreo}"/>
            </div>
          `)
          c_detalle_muestra[0] = detalle_muestra;
          c_detalle_muestra[1] = detail.id_codigo_amc;
          c_detalle_muestra[2] = detail.certificado_nro;
          c_detalle_muestra[3] = detail.id_muestreo;
          select_vida($('select#frm_analisis').val());
          $('.row.div_muestra_productos h6').html(`<b>Norma:</b> ${detail.producto.nor_nombre}`)
          $('#vida_util').val(JSON.stringify(aux_vida_util))
          my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Muestra cargada', 'blue darken-2', 3000);
        }
      })
    }else{
      my_toast('<i class="fas fa-check"></i>&nbsp Producto encontrado sin ensayos', 'orange darken-2', 3000);
      $('.row.div_muestra_productos h6').html(``)
      tables[1].destroy();
      tables[1] = undefined;
      $('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
    }
}

async function change_producto(producto){
  var detalle = tables[0].rows().data().toArray().find(detail => detail.id_muestreo_detalle == c_detalle_muestra[0]);
  if (detalle.id_producto == producto){
    buscar_detalle(c_detalle_muestra[0])
  }else{
    var url = base_url(['funcionario/remisiones/muestra/product', producto, 'edit'])
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando producto', 'blue-grey darken-2', 30000);
    try {
      var detail = await proceso_fetch_get(url).then(info => info);
      my_toast('<i class="fas fa-check"></i>&nbsp Producto encontrado', 'blue darken-2', 3000);
      if(detail.ensayos.length != 0){
        var columns = []
        var dataSet = []
        detail.ensayos.forEach(e => { // Creamos los checks de los ensayos
          columns.push({
            title: e.par_nombre
          })
          var text = '';
          if (e.med_valor_min != null || e.med_valor_max != null)
            text += `<b>${e.med_valor_min != null ? e.med_valor_min : ''} - ${e.med_valor_max != null ? e.med_valor_max : ''}</b><br>`
          else 
            text += `<b>Sin limites</b><br>`
          text += `<label>
              <input type="checkbox" name="frm_chk_${e.id_ensayo}" id="frm_chk_${e.id_ensayo}" ${e.is_checked}/>
              <span></span>
            </label>`
          dataSet.push(text)
        })
        if(tables[1] != undefined){ // Destruimos ensayos existente
          $('.row.div_muestra_productos h6').html(``)
          tables[1].destroy();
          tables[1] = undefined;
          $('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
        }
        tables[1] = $('#table_datatable_muestra_productos').DataTable({
          dom: 't',
          columns: columns,
          data: [dataSet],
          responsive: false,
          scrollX: true,
          ordering: false,
          initComplete: () => {
            if(detail.producto.n_analizar_1407 > 0){
              var btn_vida_fecha = `<input type="hidden" name="captura_fechas" id="captura_fechas" value="${detail.producto.n_analizar_1407}">`
              var aux_btn_vida = ``
            }else{
              var btn_vida_fecha = `<input type="hidden" name="vida_util" id="vida_util" value="">`
              var aux_btn_vida = `<a href="javascript:void(0)" onclick="view_vidas()" disabled class="btn ml-5 blue-grey lighten-5 blue-grey-text border-round agregar_lista" id="btn-vidas">Vidas utiles</a>`
            }
            $('#div_muestra_productos').append(`
              ${btn_vida_fecha}
              <div class="row mb-2">
                <div class="col s12 centrar_button">
                  <b style="width: 100%; text-align: center;">Unidad de Medida</b><br><br>
                  <label>
                    <input type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="solida" checked>
                    <span>S&oacute;lidas</span>
                  </label>
                  <label>
                    <input type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="liquida">
                    <span>Liquidas</span>
                  </label>
                </div>
                <div class="col s12 centrar_button mt-2" id="campo_botton_agregar">
                  <a href="javascript:void(0)" onclick="send_detail()" id="btn-muestreo-form" class="btn gradient-45deg-purple-deep-orange border-round agregar_lista">Editar detalle</a>
                  ${aux_btn_vida}
                  <input type="hidden" name="frm_id_forma" id="frm_id_forma" value="frm_form_muestra"/>
                </div>
              </div>
              <div class="col s12 mt-2 centrar_button">
                <label>
                  <input type="checkbox" name="frm_elimina_resultado" id="frm_elimina_resultado" value="SI" checked/>
                  <span>Eliminar resultado ingresados.</span>
                </label>
                <input type="hidden" name="frm_id_muestra_detalle" id="frm_id_muestra_detalle" value="${c_detalle_muestra[0]}"/>
                <input type="hidden" name="frm_id_codigo_amc" id="frm_id_codigo_amc" value="${c_detalle_muestra[1]}"/>
                <input type="hidden" name="frm_id_certificado" id="frm_id_certificado" value="${c_detalle_muestra[2]}"/>
                <input type="hidden" name="frm_id_muestreo" id="frm_id_muestreo" value="${c_detalle_muestra[3]}"/>
              </div>
            `)
            select_vida($('select#frm_analisis').val());
            $('.row.div_muestra_productos h6').html(`<b>Norma:</b> ${detail.producto.nor_nombre}`)
            $('#vida_util').val(JSON.stringify(aux_vida_util))
            my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Muestra cargada', 'blue darken-2', 3000);
          }
        })
      }else{
        my_toast('<i class="fas fa-check"></i>&nbsp Producto encontrado sin ensayos', 'orange darken-2', 3000);
        $('.row.div_muestra_productos h6').html(``)
        tables[1].destroy();
        tables[1] = undefined;
        $('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
      }
    } catch (error) {
      console.error('Error al cargar la información:', error);
    }
  }

}

function reinit_table(){
	tables[0].ajax.url(base_url(['funcionario', 'remisiones', 'detail', $('#frm_id_remision').val(), 'edit']))
	tables[0].ajax.reload()
	if(tables[1] != undefined){
		$('.row.div_muestra_productos h6').html(``)
		tables[1].destroy();
		tables[1] = undefined;
		$('#div_muestra_productos').html('<table class="display" id="table_datatable_muestra_productos"></table>')
	}
	if(tables[2] != undefined)
		tables[2].clear().rows.add([]).draw()
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
  my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Editando detalle', 'blue-grey darken-2', 30000);
  var frm_form = $('#frm_form').serialize();
  var frm_form_muestra = $('#frm_form_muestra').serialize();
  var frm_form_pie = $('#frm_form_pie').serialize();

  var data = frm_form + '&' + frm_form_muestra + '&' + frm_form_pie + '&buscar=3';
  await proceso_fetch($('#myform').attr('action'), data).then(res => {
    $('#frm_id_remision').val(res.frm_id_remision);
    aux_vida_util.splice(0, aux_vida_util.length);
    reinit_table();
    var frm_form_muestra = $('#frm_form_muestra')[0];
    formateo_forms(0, frm_form_muestra, 0);
    my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Detalle editado', 'blue darken-2', 3000);
    $('#frm_producto').attr('disabled','true')
  });
  // var boton = $('#btn-muestreo-form');
  // boton.prop('disabled', true);
  // boton.addClass('blue-grey darken-3');
  // boton.removeClass('gradient-45deg-purple-deep-orange');
  // js_enviar_agregar_a_detalle($('#frm_form_muestra').attr('action'), 1);
}

async function btn_remision_guardar() {
	var data = $('#frm_form_pie').serialize();
	data += data + '&buscar=4';
	var url = $('#frm_form_pie').attr('action');
	await proceso_fetch(url, data).then(data => {
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

function view_vidas(){
	tables[2] = undefined
	const swalWithBootstrapButtons = Swal.mixin({
		customClass: {
			confirmButton: "btn pink border-round",
		},
		buttonsStyling: false
	});
	var div = `
		<div class="products-exceso" style="overflow-y: auto;">
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
  `
	div += `</div>`
	swalWithBootstrapButtons.fire({
		title: `Fechas de vidas utiles`,
		html: div,
		width: "80%",
		confirmButtonText: "Cerrar",
		confirmButton: false,
		didOpen: () => {
			swalWithBootstrapButtons.hideLoading()
			table_vida_util()
		},
	})
}

function table_vida_util(){
	var dataSet = []
	aux_vida_util.forEach((vida_util, key) => {
		var buttons = `
			<a href="javascript:void(0)" class="blue-text edit tooltipped" data-position="bottom" data-tooltip="Editar" onclick="edit_vida('${key}')"><i class="far fa-pen"></i></a>
		  <a href="javascript:void(0)" class="pink-text delete tooltipped ml-10" data-position="bottom" data-tooltip="Eliminar" onclick="delete_vida('${key}')"><i class="far fa-trash-alt"></i></a>`
		dataSet.push([vida_util.fecha, vida_util.dia, buttons])
	})
	if (tables[2] != undefined){
		tables[2].clear().rows.add(dataSet).draw()
	}else{
		var columns = [
			{title:'Fecha'},
			{title:'Dia'},
			{title:'Acción'},
		]
		tables[2] = $('#table_datatable_vidas_utiles').DataTable({
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

function add_vida_util(create = 'created', valid_limit = false) {
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
      aux_vida_util[create].fecha = aux;
      aux_vida_util[create].dia = aux_dia;
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
  console.log(value.fecha);
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
  $('#vida_util').val('');
}
}

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