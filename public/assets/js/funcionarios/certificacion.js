function js_mostrar_detalle(
    campo_muestra, campo_oculta, certificado_nro,
    que_mostrar, bandera_mostrar_formulario_o_informe, rol = '') {
    if (certificado_nro) {
        my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Cargando datos', 'blue-grey darken-2', 30000);
        var url = $('form#form-certificados').attr('action');
        if (bandera_mostrar_formulario_o_informe == 'php_lista_resultados') {
            var data = new URLSearchParams({
                certificado_nro: certificado_nro,
                que_mostrar: que_mostrar,
                funcion: 'lista_resultados'
            });
            // xajax_php_lista_resultados(certificado_nro,que_mostrar );
        } else {
            var data = new URLSearchParams({
                certificado_nro: certificado_nro,
                que_mostrar: que_mostrar,
                user_rol_id: rol,
                funcion: 'presentar_preinforme2'
            });
            // xajax_php_presentar_preinforme2(certificado_nro,que_mostrar, <?=$user_rol_id;?> );//que_mostrar=id_certificado a presentar, tipo si es preliminar(0) o informe(1)
        }
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
            $('.card-content.card-detalle .content-info').html(respuesta.data);
            $('.card-content.' + campo_oculta).fadeOut();
            $('.card-content.' + campo_muestra).fadeIn();
            my_toast('<i class="fas fa-check"></i>&nbsp Datos cargados', 'blue darken-2', 3000);
            $('table select').formSelect();
            $('.table select').formSelect();
        }).catch(error => {
            my_toast('<i class="fas fa-times"></i>&nbsp Error carga', 'red darken-2', 3000);
        })
    } else {
        $('.card-content.' + campo_oculta).fadeOut();
        $('.card-content.' + campo_muestra).fadeIn();
        $('.fa-refresh').trigger('click');
        setTimeout(function() {
            $('html, body').animate({ scrollTop: 0 }, 'slow')
        }, 500);
    }
}

function editar_campos(type, id_campo, valor_campo, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion) {
    if (type == 'date') {
        var text = `<div class="input-field col s12">
            <input id="${nombre_campo_frm}" name="${nombre_campo_frm}" type="text" class="validate" value="${valor_campo}">
            <p>
                <label>
                    <input id="${nombre_campo_frm}_check" onChange="changeType2('${type}','${id_campo}', '${valor_campo}', '${nombre_campo_frm}',  '${nombre_campo_bd}', '${tabla_update}', '${id_operacion}')" type="checkbox" checked />
                    <span>Formato ("DD-MM-YYYY")</span>
                </label>
            </p>
        </div>`;
        $('#' + id_campo).html(text);
        const pattern = /^(?:(?:(?:0?[1-9]|1\d|2[0-8])[/-](?:0?[1-9]|1[0-2])|(?:29|30)[/-](?:0?[13-9]|1[0-2])|31[/](?:0?[13578]|1[02]))[/-](?:0{2,3}[1-9]|0{1,2}[1-9]\d|0?[1-9]\d{2}|[1-9]\d{3})|29[/-]0?2[/-](?:\d{1,2}(?:0[48]|[2468][048]|[13579][26])|(?:0?[48]|[13579][26]|[2468][048])00))$/;
        if (!pattern.test(valor_campo)) {
            $(`#${nombre_campo_frm}_check`).prop('checked', false);
            changeType2(type, id_campo, valor_campo, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion);
        } else {
            $(`#${nombre_campo_frm}_check`).prop('checked', true);
            changeType2(type, id_campo, valor_campo, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion);
        }
        // alert('Date');
    } else if (type == 'date_complete') {
        var text = `<div class="input-field col s12">
            <input id="${nombre_campo_frm}" name="${nombre_campo_frm}" type="text" class="validate" value="${valor_campo}">
        </div>`;
        $('#' + id_campo).html(text);
        $(`#${nombre_campo_frm}`).datetimepicker({
            dateFormat: 'yy-mm-dd',
            onClose: (fecha) => {
                cambiar_campos(type, id_campo, fecha, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion);
            }
        })
    } else {
        var text = '<input type="' + type + '" name="' + nombre_campo_frm + '"' +
            'id="' + nombre_campo_frm + '"' +
            'value="' + valor_campo + '"' +
            'onblur=" cambiar_campos(`' + type + '`, `' + id_campo + '`,this.value, `' + nombre_campo_frm + '`, `' + nombre_campo_bd + '`, `' + tabla_update + '`, `' + id_operacion + '`)";/>';
        $('#' + id_campo).html(text);
    }
}

function changeType2(type, id_campo, fecha, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion) {
    var input, check;
    var validar = fecha;

    input = $(`#${nombre_campo_frm}`);
    check = $(`#${nombre_campo_frm}_check`);

    if (check[0].checked == true) // Si la checkbox de mostrar datepicker est¨¢ activada
    {
        input.attr('onblur', ``)

        // input.val('');
        const pattern = /^(?:(?:(?:0?[1-9]|1\d|2[0-8])[/-](?:0?[1-9]|1[0-2])|(?:29|30)[/-](?:0?[13-9]|1[0-2])|31[/](?:0?[13578]|1[02]))[/-](?:0{2,3}[1-9]|0{1,2}[1-9]\d|0?[1-9]\d{2}|[1-9]\d{3})|29[/-]0?2[/-](?:\d{1,2}(?:0[48]|[2468][048]|[13579][26])|(?:0?[48]|[13579][26]|[2468][048])00))$/;
        if (!pattern.test(validar)) {
            input.val('');
        } else {
            input.val(fecha);
        }
        input.datepicker({
            dateFormat: 'dd-mm-yy',
            onClose: (fecha_input) => {
                cambiar_campos(type, id_campo, fecha_input, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion);
            }
        })
    } else // Si no est¨¢ activada
    {
        input.attr('onblur', `cambiar_campos('${type}', '${id_campo}', this.value, '${nombre_campo_frm}', '${nombre_campo_bd}', '${tabla_update}', '${id_operacion}')`)
        input.datepicker('destroy');
    }
    // console.log(fecha);
}

function cambiar_campos(type, id_campo, valor, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion) {
    var url = $('form#form-certificados').attr('action');
    if (valor.length > 0) {
        var data = new URLSearchParams({
            type: type,
            id_campo: id_campo,
            valor: valor,
            nombre_campo_frm: nombre_campo_frm,
            nombre_campo_bd: nombre_campo_bd,
            tabla_update: tabla_update,
            id_operacion: id_operacion,
            funcion: 'cambiar_campos'
        });
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
            $('#' + respuesta.data[1]).html(respuesta.data[0]);
        })
    }
}

function editar_campos_redondeo(id_campo, valor_campo, parametro, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion, conteo) {
    var inputs = `
        <hr>
        <div class="row center-align">
            <br>
            <p class="center-align"><b>Seleccione redondeo para : ` + valor_campo + `
            <br><small>Parametro: ` + parametro + `</small></b></p>
            <br>
            <div class="col s12 m6 l3">
                <label>
                    <input class="with-gap" name="frm_tipo_entero" value="0" type="radio"
                    onclick='redondear_y_enviar(
                        "` + id_campo + `",
                        "` + valor_campo + `" ,
                        "` + nombre_campo_frm + `" ,
                        "` + nombre_campo_bd + `" ,
                        "` + tabla_update + `" ,
                        "` + id_operacion + `",
                        this.value)'/>
                    <span>Entero</span>
                </label>
            </div>
            <div class="col s12 m6 l3">
                <label>
                    <input class="with-gap" name="frm_tipo_entero" value="1" type="radio"
                    onclick='redondear_y_enviar(
                        "` + id_campo + `",
                        "` + valor_campo + `" ,
                        "` + nombre_campo_frm + `" ,
                        "` + nombre_campo_bd + `" ,
                        "` + tabla_update + `" ,
                        "` + id_operacion + `",
                        this.value)'/>
                    <span>1 decimal</span>
                </label>
            </div>
            <div class="col s12 m6 l3">
                <label>
                    <input class="with-gap" name="frm_tipo_entero" value="2" type="radio"
                    onclick='redondear_y_enviar(
                        "` + id_campo + `",
                        "` + valor_campo + `" ,
                        "` + nombre_campo_frm + `" ,
                        "` + nombre_campo_bd + `" ,
                        "` + tabla_update + `" ,
                        "` + id_operacion + `",
                        this.value)'/>
                    <span>2 decimales</span>
                </label>
            </div>
            <div class="col s12 m6 l3">
                <label>
                    <input class="with-gap" name="frm_tipo_entero" value="3" type="radio"
                    onclick='redondear_y_enviar(
                        "` + id_campo + `",
                        "` + valor_campo + `" ,
                        "` + nombre_campo_frm + `" ,
                        "` + nombre_campo_bd + `" ,
                        "` + tabla_update + `" ,
                        "` + id_operacion + `",
                        this.value)'/>
                    <span>3 decimales</span>
                </label>
            </div>
        </div>
    `;
    $('#campo_resultado_redondeo').html(inputs);
}

function mathRound2(num, decimales) {

    var exponente = Math.pow(10, decimales);
    return (num >= 0 || -1) * Math.round(Math.abs(num) * exponente) / exponente;
}

function redondear_y_enviar(id_campo, valor_campo, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion, otro) {
    //alert('Antes-->'+valor_campo+' - '+otro);  
    var numero = mathRound2(valor_campo.replace(",", "."), otro);
    //alert('despues -->'+numero+' - '+otro); 

    if (isNaN(numero)) {
        my_toast('<i class="fas fa-times"></i>&nbsp No permito el cambio ' + numero, 'red darken-2', 3000);
    } else {
        numero = numero.toString();
        numero = numero.replace(".", ",");
        console.log(valor_campo);
        // alert('No NAn-->'+numero);  
        var url = $('form#form-certificados').attr('action');
        var data = new URLSearchParams({
            type: 'text',
            id_campo: id_campo,
            valor: numero,
            nombre_campo_frm: nombre_campo_frm,
            nombre_campo_bd: nombre_campo_bd,
            tabla_update: tabla_update,
            id_operacion: id_operacion,
            funcion: 'cambiar_campos'
        });
        var result = proceso_fetch(url, data.toString());
        result.then(respuesta => {
                $('#' + respuesta.data[1]).html(respuesta.data[0]);
            })
            // xajax_cambiar_campos(id_campo, numero, nombre_campo_frm, nombre_campo_bd, tabla_update, id_operacion);
    }
}

function muestra_mensaje(id_mensaje, tabla) {
    var url = $('form#form-certificados').attr('action');
    var data = new URLSearchParams({
        id_mensaje: id_mensaje,
        tabla: tabla,
        funcion: 'muestra_mensaje'
    });
    var result = proceso_fetch(url, data.toString());
    result.then(respuesta => {
        $('#campo_' + respuesta.data.tabla).html(respuesta.data.mensaje);
    })
}

function js_enviar(aux, certificado_nro) {
    $('#form-certificados').attr('target', '');
    if ($('#frm_id_forma').val() == 'forma_muestra_preinforme') {
        if ($('#frm_mensaje_firma').val()) {
            if (!$('input[name="frm_id_procedencia"]').is(':checked')) {
                return my_toast('<i class="fas fa-times"></i>&nbsp Debe Seleccionar tipo de informe', 'red darken-2', 3000);
            }
            switch (aux) {
                case 0:
                    $('#funcion').val('guardar');
                    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Actualizando datos', 'blue-grey darken-2', 30000);
                    var form = $('#form-certificados');
                    var result = proceso_fetch(form.attr('action'), form.serialize());
                    result.then(respuesta => {
                        
                        // $(respuesta.data.boton.div).html(respuesta.data.boton.button);
                        // $(respuesta.data.mensaje_resultado.div).html(respuesta.data.mensaje_resultado.mensaje);
                        // console.log($('#frm_id_certificado').val());
                        enviar_certificado($('input:radio[name=frm_id_procedencia]:checked').val(), certificado_nro, 1);
                    });
                    break;
                case 1:
                    $('#funcion').val('guardar');
                    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Actualizando datos', 'blue-grey darken-2', 30000);
                    var form = $('#form-certificados');
                    var result = proceso_fetch(form.attr('action'), form.serialize());
                    result.then(respuesta => {
                        console.log(respuesta)
                        var formData = new FormData();
                        var fileField = document.querySelector("input[type='file']");

                        formData.append('funcion', 'file_document');
                        formData.append('file_certificado', fileField.files[0]);
                        formData.append('frm_id_procedencia', $('input:radio[name=frm_id_procedencia]:checked').val());
                        formData.append('frm_id_certificado', $('#frm_id_certificado').val());

                        var file = fetch(form.attr('action'), {
                            method: 'POST',
                            body: formData
                        }).then(response => {
                            if (!response.ok) throw Error(response.status);
                            return response.json();
                        });
                        file.then(respuesta_file => {
                            my_toast(respuesta.data.mensaje.html, respuesta.data.mensaje.class, 3000);
                            // $(respuesta.data.boton.div).html(respuesta.data.boton.button);
                            // $(respuesta.data.mensaje_resultado.div).html(respuesta.data.mensaje_resultado.mensaje);
                        })
                    });
                    break;
                case 2:
                    $('#funcion').val('previsualizar');
                    $('#form-certificados').attr('target', '_blank');
                    descargar();
                    break;
            }
        } else {
            if ($('#frm_mensaje_firma').val() === '')
                my_toast('<i class="fas fa-times"></i>&nbsp Debe Seleccionar la firma', 'red darken-2', 3000);
        }
    }
}

function enviar_certificado(procedencia, certificado_nro, plantilla) {
    my_toast('<i class="fas fa-check"></i>&nbsp Generando reporte', 'blue darken-2', 3000);
    if (procedencia == 2) { // 2 - informe
        $('#certificado_reporte').val(certificado_nro);
        $('#certificado_reporte').attr('checked', true);
    } else {
        $('#certificado_preliminar').val(certificado_nro);
        $('#certificado_preliminar').attr('checked', true);
    }
    $('#form-download').submit();
    $('#certificado_preliminar').attr('checked', false);
    $('#certificado_reporte').attr('checked', false);
}

function descargar() {
    my_toast('<i class="fas fa-check"></i>&nbsp Generando reporte', 'blue darken-2', 3000);
    $('#form-certificados').submit();
}

function descargar_info(certificado_nro, tipo_documento, rol) {
    enviar_certificado(tipo_documento, certificado_nro, 1);
}

function certificado_facturacion(certificado_nro, rol, tipo_documento) {
    Swal.fire({
        title: 'Ya realizo factura ?',
        icon: 'warning',
        position: 'top-end',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = $('form#form-certificados').attr('action');
            var data = new URLSearchParams({
                certificado_nro: certificado_nro,
                funcion: 'certificado_facturacion'
            });
            var result = proceso_fetch(url, data.toString());
            result.then(respuesta => {
                Swal.fire({
                    text: respuesta.data.html,
                    position: 'top-end',
                    icon: respuesta.data.icon,
                });
                $('.fa-refresh').trigger('click');
                // $('#certificado_' + certificado_nro).html(`
                //     <button class="btn cyan white-text" onClick="actualizar_informe(` + certificado_nro + `, 3, ` + rol + `, ` + tipo_documento + `)"><i class="fad fa-usd-circle"></i></button>`);
            })
        }
    })
}

function certificado_autorizacion(certificado_nro, rol, tipo_documento) {
    Swal.fire({
        title: 'Autoriza publicaciÃ³n?',
        icon: 'warning',
        position: 'top-end',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = $('form#form-certificados').attr('action');
            var data = new URLSearchParams({
                certificado_nro: certificado_nro,
                funcion: 'certificado_autorizacion'
            });
            var result = proceso_fetch(url, data.toString());
            result.then(respuesta => {
                Swal.fire({
                    html: respuesta.data.html,
                    position: 'top-end',
                    icon: respuesta.data.icon,
                });
                if (respuesta.data.icon != 'warning') {
                    $('.fa-refresh').trigger('click');
                    // $('#certificado_' + certificado_nro).html(`
                    //     <button class="btn deep-orange white-text" onClick="actualizar_informe(` + certificado_nro + `, 2, ` + rol + `, ` + tipo_documento + `)">
                    //         <i class="fad fa-thumbs-up"></i>
                    //     </button>`);
                    // $(respuesta.data.div).html(respuesta.data.mensaje);
                }
            })
        }
    })
}

function actualizar_informe(certificado_nro, metodo, rol, tipo_documento) {
    if (metodo == 3) {
        my_toast('<i class="fas fa-check"></i>&nbsp Generando reporte', 'blue darken-2', 3000);
        descargar_info(certificado_nro, tipo_documento, rol);
    } else {
        if (metodo == 1) {
            var aux_button = `<i class="fad fa-thumbs-up"></i>&nbsp Autorizar publicaci&oacute;n`;
            var clase = '#ff5722';
        } else if (metodo == 2) {
            var aux_button = `<i class="fas fa-dollar-sign"></i>&nbsp Indicar que ya se factur&oacute;`;
            var clase = '#00bcd4';
        }
        Swal.fire({
            position: 'top-end',
            title: 'Informe ' + certificado_nro,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#b71c1c',
            cancelButtonColor: '#263238',
            denyButtonColor: clase,
            confirmButtonText: '<i class="fad fa-file-pdf"></i>&nbsp Descargar informe',
            denyButtonText: aux_button,
            cancelButtonText: '<i class="fad fa-times-circle"></i>&nbsp Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                descargar_info(certificado_nro, tipo_documento, rol);
            } else if (result.isDenied) {
                if (metodo == 1) {
                    certificado_autorizacion(certificado_nro, rol, tipo_documento);
                } else {
                    certificado_facturacion(certificado_nro, rol, tipo_documento);
                }
            }
        })

    }
}

function analisis_select(type_informe) {
    if (type_informe == 1) {
        $('#analisis_primer_informe').show();
        $('#analisis_informe_final').hide();
        select_firmas(1)
    } else {
        $('#analisis_primer_informe').hide();
        $('#analisis_informe_final').show();
        select_firmas();
    }
}

const aux_firmas = [];

function select_firmas(defect = 2) {
    if (defect == 2) {
        if (aux_firmas[2] == undefined)
            aux_firmas[2] = firmas(2);
    } else {
        if (aux_firmas[1] == undefined)
            aux_firmas[1] = firmas(1);
    }
    var value = aux_firmas[defect].id_firma;
    $(`#firma_${value}`).prop("selected", true);
    $('#frm_mensaje_firma').formSelect();
    var frm_form_valo = aux_firmas[defect].form_valo;
    $(`#valor_${frm_form_valo}`).prop("selected", true);
    $('#frm_form_valo').formSelect();
    if (value != '') {
        var complemento = aux_firmas[defect].complemento;
        var modificacion = aux_firmas[defect].modificacion;
        $('#complemento').val(complemento);
        $('#modificacion').val(modificacion);
        
        M.updateTextFields();
    }
}