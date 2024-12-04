$(() => {

    $('.date_picker_2').datepicker({
        dateFormat: 'dd-mm-yy',
    }).on('change', function() {
        M.updateTextFields();
    });

    $('#frm_producto').select2();

    init_select();

});

function init_select() {
    $('#frm_nombre_empresa').select2({
        ajax: {
            url: base_url(['funcionario', 'remisiones', 'empresa']),
            type: 'POST',
            dataType: 'json',
            data: function(params) {
                return {
                    frm_nombre_empresa: params.term,
                    buscar: 1
                }
            },
            processResults: function(data, params) {
                return { results: data };
            },
            cache: false
        },
        placeholder: 'Empresa',
        minimumInputLength: 1,
        language: {
            errorLoading: () => { return "La carga falló" },
            inputTooLong: (e) => {
                var t = e.input.length - e.maximum,
                    n = "Por favor, elimine " + t + " car";
                return t == 1 ? n += "ácter" : n += "acteres",
                    n
            },
            inputTooShort: (e) => {
                var t = e.minimum - e.input.length,
                    n = "Por favor, introduzca " + t + " car";
                return t == 1 ? n += "ácter" : n += "acteres",
                    n
            },
            loadingMore: () => { return "Cargando más resultados…" },
            maximumSelected: function(e) {
                var t = "Sólo puede seleccionar " + e.maximum + " elemento";
                return e.maximum != 1 && (t += "s"),
                    t
            },
            noResults: () => { return "No se encontraron resultados" },
            searching: () => { return "Buscando…" }
        }
    });
}

function base_url(array) {
    var url = localStorage.getItem('base_url') ? localStorage.getItem('base_url') : 'https://asbioquim.com.co/public';
    if (array.length === 0) return `${url}`;
    else return `${url}/${array.join('/')}`;
}

function my_toast(html, clase, duracion, error = false) {
    if (error || html.includes('check') || clase.includes('amber'))
        M.Toast.dismissAll();
    M.toast({
        html: html,
        classes: clase,
        displayLength: duracion,
    });
}

function proceso_fetch(url, data) {
    console.log([url, data]);
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret' },
        body: data
    }).then(response => {
        if (!response.ok) throw Error(response.status);
        return response.json();
    }).catch(error => {
        console.log(error);
        my_toast('<i class="fas fa-times"></i>&nbsp&nbsp Error en consulta', 'red darken-2', 3000, true)
    });
}

function new_proceso_fetch(url, data, method = 'POST') {
    return fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: data
    }).then(response => {
        if (!response.ok) throw Error(response.status);
        return response.json();
    }).catch(error => {
        alert('<span class="red-text">Error en la consulta</span>', 'red lighten-5');
    });
}

async function proceso_fetch_get(url){
    try {
        const response = await fetch(url);
        if (!response.ok) throw Error(response.status);
        return await response.json();
    } catch (error) {
        console.log(error);
        my_toast('<i class="fas fa-times"></i>&nbsp&nbsp Error en consulta', 'red darken-2', 3000, true);
    }
}

function buscar_empresa(id) {
    $('.empresa_row small').html('');
    if (id == '')
        my_toast('Debe seleccionar una empresa', 'red darken-2', 3000);
    else {
        var data = new URLSearchParams({
            frm_nombre_empresa: id,
            buscar: 2,
            id_muestra: $('#frm_id_remision').val()
        });
        var form = $('#frm_form');
        var url = form.attr('action');
        my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando empresa', 'blue-grey darken-2', 30000);
        var promesa = proceso_fetch(url, data.toString());
        promesa.then(empresa => {
            console.log(empresa);
            if (empresa.validation) {
                formateo_forms(form[0]);
                $('#frm_nombre_empresa').val(empresa.empresa);
                M.updateTextFields();
                if (remision == 0) {
                    my_toast('<i class="fas fa-times"></i>&nbsp No se encontro empresa', 'amber darken-2', 3000);
                } else {
                    $('#frm_nit').prop('readonly', false);
                    $('.input-field.nit').removeClass('l6');
                    $('.input-field.nit').addClass('l2');
                    if ($('.username').length == 0) {
                        $('.input-field.empresa').after(
                            '<div class="input-field col l4 s12 username">' +
                            '<input id="username" name="username" type="text" class="validate">' +
                            '<label for="username">Usuario</label>' +
                            '<small class=" red-text text-darken-4" id="username"></small>' +
                            '</div>');
                    }
                    $('#username').focus();
                    $('input#empresa_nueva').val(0);
                    my_toast('<i class="fad fa-user-times"></i>&nbsp Empresa sin registrar', 'amber darken-2', 3000);
                }
            } else {
                $('.input-field.username').remove();
                $('#frm_nit').prop('readonly', true);
                $('.input-field.nit').removeClass('l2');
                $('.input-field.nit').addClass('l6');
                $('#frm_nombre_empresa_subtitulo').focus();
                $('input#empresa_nueva').val(1);
                completar_empresa(empresa);
                my_toast('<i class="fas fa-user-check"></i>&nbsp&nbsp Empresa cargada', 'blue darken-2', 3000);
            }
        });
    }
}

function buscar_cliente(remision = 0) {
    $('.empresa_row small').html('');
    if ($('#frm_nombre_empresa').val() === '') {
        my_toast('Nombre de la empresa vacio', 'red darken-2', 3000);
    } else {
        var data = new URLSearchParams({
            frm_nombre_empresa: `${$('#frm_nombre_empresa').val()}`,
            buscar: 2,
            id_muestra: $('#frm_id_remision').val()
        });
        var form = $('#frm_form');
        var url = form.attr('action');
        my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando empresa', 'blue-grey darken-2', 30000);
        var promesa = proceso_fetch(url, data.toString());
        promesa.then(empresa => {
            if (empresa.validation) {
                formateo_forms(form[0]);
                $('#frm_nombre_empresa').val(empresa.empresa);
                M.updateTextFields();
                if (remision == 0) {
                    my_toast('<i class="fas fa-times"></i>&nbsp No se encontro empresa', 'amber darken-2', 3000);
                } else {
                    $('#frm_nit').prop('readonly', false);
                    $('.input-field.nit').removeClass('l6');
                    $('.input-field.nit').addClass('l2');
                    if ($('.username').length == 0) {
                        $('.input-field.empresa').after(
                            '<div class="input-field col l4 s12 username">' +
                            '<input id="username" name="username" type="text" class="validate">' +
                            '<label for="username">Usuario</label>' +
                            '<small class=" red-text text-darken-4" id="username"></small>' +
                            '</div>');
                    }
                    $('#username').focus();
                    $('input#empresa_nueva').val(0);
                    my_toast('<i class="fad fa-user-times"></i>&nbsp Empresa sin registrar', 'amber darken-2', 3000);
                }
            } else {
                $('.input-field.username').remove();
                $('#frm_nit').prop('readonly', true);
                $('.input-field.nit').removeClass('l2');
                $('.input-field.nit').addClass('l6');
                $('#frm_nombre_empresa_subtitulo').focus();
                $('input#empresa_nueva').val(1);
                completar_empresa(empresa);
                my_toast('<i class="fas fa-user-check"></i>&nbsp&nbsp Empresa cargada', 'blue darken-2', 3000);
            }
        });
    }
}

function completar_empresa(empresa) {
    console.log(empresa)
    // my_toast('<p><i class="fas fa-tasks"></i>&nbsp Remisión en proceso. (Cargando datos)</p>', 'light-blue darken-2', 5000);
    // var newOption = new Option(empresa.name, empresa.id, true, true);
    // $('#frm_nombre_empresa').append(newOption).trigger('change');
    $('#frm_nit').prop('readonly', true);
    $('#frm_nit').val(empresa.id);
    $('#frm_nombre_empresa_subtitulo').val(empresa.sucursal);
    // $('#frm_nombre_empresa').val(empresa.name);
    $('#frm_nombre_empresa2').val(empresa.id);
    $('#frm_contacto_cargo').val(empresa.use_cargo);
    $('#frm_contacto_nombre').val(empresa.use_nombre_encargado);
    $('#frm_telefono').val(empresa.use_telefono);
    $('#frm_fax').val(empresa.use_fax);
    $('#frm_correo').val(empresa.email);
    $('#frm_direccion').val(empresa.use_direccion);
    $('#frm_nombre_empresa_subtitulo').focus();
    if (empresa.fecha != null) {
        $('#frm_fecha_muestra').val(empresa.fecha);
        $('#frm_hora_muestra').val(empresa.hora);
    }
    M.updateTextFields();
}


// Productos
function producto_blur(buscar) {
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando producto', 'blue-grey darken-2', 30000);
    setTimeout(function() {
        var form = $('#frm_form_muestra');
        if (buscar == 4)
            var url = $('#myform').attr('action');
        else
            var url = form.attr('action');
        var data = new URLSearchParams(form.serialize());
        data.set('buscar', buscar);
        result = proceso_fetch(url, data.toString());
        result.then(tabla => {
            if (tabla === '') {
                my_toast('<i class="fas fa-times"></i>&nbsp No se encontro producto', 'amber darken-2', 3000);
                return null;
            }
            my_toast('<i class="fas fa-check"></i>&nbsp Tabla de producto cargada', 'blue darken-2', 3000);
            $('.lista_parametros').remove();
            $('#frm_form_muestra .row.finish').after(tabla);
        });
    }, 1000)
};

function tabla_detalles_muestras(tabla) {
    $('#campo_detalle_muestras_basic').hide();
    $('#campo_detalle_muestras').remove();
    $('#tabla_detalles_muestras').after(tabla.tabla);
    $('.row.boton_guardar_remision .centrar_button').remove();
    $('.row.boton_guardar_remision').append(tabla.boton);
    $('.tabla-productos').remove();
    $('#campo_parametros_producto_detalle').remove();
}

function recibe_entrega(muestra, certificado) {
    $('#frm_id_remision').val(certificado.id_muestreo);
    $('#frm_id_muestra').val(certificado.id_muestreo);

    $('#frm_observaciones').val(muestra.mue_observaciones);
    $('#frm_entrega').val(muestra.mue_entrega_muestra);
    $('#frm_recibe').val(muestra.mue_recibe_muestra);
    $('.tooltipped').tooltip();
    formateo_forms($('#frm_form_muestra')[0]);
}

function js_enviar_agregar_a_detalle(url, create = 0) {
    var frm_form = $('#frm_form').serialize();
    var frm_form_muestra = $('#frm_form_muestra').serialize();
    var frm_form_pie = $('#frm_form_pie').serialize();
    // var data = new URLSearchParams(frm_form.serialize(),frm_form_muestra.serialize(),frm_form_pie.serialize());
    // data.set('buscar', 3);
    // return console.log($('#vida_util').val());
    var data = frm_form + '&' + frm_form_muestra + '&' + frm_form_pie + '&buscar=3';
    var result = proceso_fetch(url, data);
    result.then(tabla => {
        $('#campo_detalle_muestras_basic').hide();
        $('#campo_detalle_muestras').remove();
        $('#tabla_detalles_muestras').after(tabla.tabla);
        $('.row.boton_guardar_remision .centrar_button').remove();
        $('.row.boton_guardar_remision').append(tabla.boton);
        var frm_form_muestra = $('#frm_form_muestra')[0];
        formateo_forms(0, frm_form_muestra, 0);
        $('.lista_parametros').remove();
        $('#frm_id_remision').val(tabla.frm_id_remision);
        if (create == 0)
            $('#frm_producto').attr('disabled', true);
        $('.tooltipped').tooltip();
        if (create == 0)
            my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Detalle editado', 'blue darken-2', 3000);
        else
            my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Detalle agregado', 'blue darken-2', 3000);
    }).catch(error => M.Toast.dismissAll());
};

function formateo_forms(frm_form = 0, frm_form_muestra = 0, frm_form_pie = 0) {
    if (frm_form != 0)
        frm_form.reset();
    if (frm_form_muestra != 0)
        frm_form_muestra.reset();
    if (frm_form_pie != 0)
        frm_form_pie.reset();
    M.updateTextFields();
    $("#frm_producto").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
    
    $('#vida_util').val('');


    console.log($('#vida_util').val());
    // $(".select2").select2({
    //     dropdownAutoWidth: true,
    //     width: '100%'
    // });
}

function changeType(campo) {
    var input, check;

    input = $(`#${campo}`);
    check = $(`#${campo}_check`);

    if (check[0].checked == true) // Si la checkbox de mostrar datepicker está activada
    {
        input.val('');
        input.datepicker({
            dateFormat: 'dd-mm-yy',
        }).on('change', function() {
            M.updateTextFields();
        });
    } else // Si no está activada
    {
        input.val('');
        input.datepicker('destroy');
    }
}

// Resultados

function change_date(id_certificacion, date) {
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Actualizando fecha de analisis', 'blue-grey darken-2', 30000);
    var data = new URLSearchParams({
        id_certificacion: id_certificacion,
        date: date
    });
    var url = $('#form_date').attr('action');
    var result = proceso_fetch(url, data.toString());
    result.then(respuesta => {
        if (respuesta.status)
            my_toast('<i class="fas fa-check"></i>&nbsp&nbsp Fecha de analisis actualizada', 'blue darken-2', 3000);
        else
            my_toast('<i class="fas fa-times"></i>&nbsp&nbsp Error en actualizar fecha', 'red darken-2', 3000, true)
    });
}

function download(analisis, type) {
    var objeto = new Object;
    objeto["0"] = 'Todos';
    console.log(analisis)
    console.log(type);
    analisis.forEach(element => {
        var id = element.id_muestra_tipo_analsis;
        // if (type == 0) {
        //     if (element.id_muestra_tipo_analsis != 3 && element.id_muestra_tipo_analsis != 5)
                objeto[id] = `${element.mue_nombre} | ${element.mue_sigla}`
        // } else {
        //     if (element.id_muestra_tipo_analsis == 3 || element.id_muestra_tipo_analsis == 5) {
        //         objeto[id] = `${element.mue_nombre} | ${element.mue_sigla}`
        //             // console.log(type);
        //     }
        //     // console.log(element.id_muestra_tipo_analsis);
        // }

    });
    var tipo_analisis = 0;
    Swal.fire({
        title: 'Ingrese la fecha de toma de la muestra',
        html: `
            <div class="input-field col s12">
            <input id="sweet_date" type="date" class="validate">
            <label for="sweet_date">Fecha de toma de la muestra</label>
            </div>`,
        showCancelButton: true,
        input: 'select',
        inputOptions: objeto,
        inputLabel: 'Tipo de analisis',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Descargar',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                tipo_analisis = value;
                resolve()
            })
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var date = $('#sweet_date').val();
            if (date.length != 0) {
                let url = $('#resultados_download').attr('action');
                var data = new URLSearchParams({
                        'consulta': true,
                        'date_download': date,
                        'tipo_analisis': tipo_analisis,
                        'type': type
                    })
                    // console.log(data.toString());
                var result = proceso_fetch(url, data.toString());
                result.then(response => {
                    console.log(response);
                    if (response) {
                        $("#date_download").val(date);
                        $("#tipo_analisis").val(tipo_analisis);
                        $('#type').val(type);
                        $('#resultados_download').submit();
                        $('#resultados_download')[0].reset();
                    } else {
                        var analisis = objeto[tipo_analisis]
                        Swal.fire({
                            icon: 'warning',
                            title: `No se encontraron muestras realizadas el dia ${date} con tipo de analisis ${analisis}`
                        })
                    }
                })
            }
        }
    })
}



function muestra_producto(id, buscar) {
    $('.lista_parametros').remove();
    my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp&nbsp Buscando producto', 'blue-grey darken-2', 30000);
    if (id == '')
        return my_toast('<i class="fas fa-times"></i>&nbsp No se ingreso producto', 'amber darken-2', 3000);
    var form = $('#frm_form_muestra');
    if (buscar == 4)
        var url = $('#myform').attr('action');
    else
        var url = form.attr('action');
    // var url = form.attr('action');
    var data = new URLSearchParams(form.serialize());
    data.set('buscar', buscar);
    data.set('id_producto', id);

    result = proceso_fetch(url, data.toString());
    result.then(tabla => {
        if (tabla === '') {
            return my_toast('<i class="fas fa-times"></i>&nbsp No se encontro producto', 'amber darken-2', 3000);
        }
        my_toast('<i class="fas fa-check"></i>&nbsp Tabla de producto cargada', 'blue darken-2', 3000);
        $('#frm_form_muestra .row.finish').after(tabla);
    })

}

function select_vida(value) {
    if (value == 1 || value == 2 || value == 4 || value == 3 || value == 6 || value == 5) {
        $('.fechas_vida_util').show();
    } else {
        $('.fechas_vida_util').hide();
        $('#vida_util').val('');
    }
}