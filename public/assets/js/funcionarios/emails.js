const table = [];
const table_emails = [];
$(function() {
    table['table'] = $('table.display').DataTable({
        ajax: {
            "url": base_url(['funcionario', 'certificacion', 'emails_certificado']),
            "data": function(d) {
                d.cliente = $('#cliente').val();
            },
            "type": 'post',
            "dataSrc": ''
        },
        columns: [
            { data: 'certificado_nro' },
            { data: 'mue_fecha_muestreo' },
            { data: 'cliente' },
            { data: 'mue_subtitulo' },
            { data: 'codigo' },
            { data: 'producto' },
            { data: 'status' },
            { data: 'fecha_publicacion' },
        ],
        "responsive": false,
        "scrollX": true,
        "ordering": false,
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        initComplete: (data) => {
            $('.material-tooltip').remove();
            $('.tooltipped').tooltip();
        }
    });
    $('#cliente').change(() => {
        Swal.fire({
            title: 'Actualizando',
            didOpen: () => Swal.showLoading(),
        });
        setTimeout(() => {
            var ids = $('#cliente').val();
            var validador = ids.every(id => {
                if (id == '') return false;
                else return true;
            });
            if (!validador) {
                $('#cliente').val([]);
                $('#cliente').select2();
            }
            table['table'].ajax.reload((data) => {
                $('.material-tooltip').remove();
                $('.tooltipped').tooltip();
                Swal.fire({
                    title: 'Informes cargados correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            });
        }, 500);
    });
    
    table_emails['table'] = $('#table_email').DataTable({
        ajax: {
            url: `${base_url(['funcionario', 'certificacion', 'emails_get'])}`,
            'dataSrc': 'data'
        },
        // order: [0, 'desc'],
        ordering: false,
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        scrollY: '50vh',
        scrollCollapse: true,
        paging: false,
        columns: [
            { data: 'id' },
            { data: 'email' },
            {
                data: 'id',
                render: (id) => {
                    return `
                    <label>
                        <input type="checkbox" name="emails[]" value="${id}" />
                        <span></span>
                    </label>`
                }
            },
        ],
    });
});

function reinit_emails() {
    setTimeout(() => {
        table_emails['table'].draw();
    }, 500);
}

const aux_emails = [];
function descargar() {
    if ($('#cliente').val().length > 1 && aux_emails.length === 0)
        return my_toast('<i class="fas fa-times"></i>&nbsp Por obligación debe especificar un Correo Electrónico', 'orange darken-2', 3000);
    if ($('[name="certificados[]"]:checked').length === 0)
        return my_toast('<i class="fas fa-times"></i>&nbsp No ha seleccionado ningun informe', 'orange darken-2', 3000);
    if($('#asunto').val() === '')
        return my_toast('<i class="fas fa-times"></i>&nbsp El asunto es obligatorio', 'orange darken-2', 3000);
    if($('#texto').val() === '')
        return my_toast('<i class="fas fa-times"></i>&nbsp El mensaje es obligatorio', 'orange darken-2', 3000);
    if ($('[name="emails[]"]:checked').length === 0)
        return my_toast('<i class="fas fa-times"></i>&nbsp No ha seleccionado ningun correo', 'orange darken-2', 3000);
    var check = $('#emails_forms').serialize();
    var form = $('#form_informes');
    var url = base_url(['funcionario', 'certificacion', 'emails_certificado', 'send']);
    var data = `${form.serialize()}&${check}`;
    var result = proceso_fetch(url, data);
    Swal.fire({
            title: 'Enviando correo',
            didOpen: () => Swal.showLoading(),
        });
    // my_toast('<i class="fas fa-spinner fa-spin"></i>&nbsp Enviando correo', 'blue-grey darken-2', 30000);
    result.then(info => {
        M.Toast.dismissAll();
        setTimeout(() => {
            console.log(info);
            if (info.status) {
                $('#asunto').val('');
                $('#texto').val('');
                $('#addEmails').val('');
                $('#cliente').val([]);
                $('#cliente').select2();
                aux_emails.splice(0, aux_emails.length);
                M.updateTextFields();
                table['table'].ajax.reload((data) => {
                    $('.material-tooltip').remove();
                    $('.tooltipped').tooltip();
                });
                return Swal.fire({
                    title: 'Informes enviados',
                    text: `${info.mensaje}`,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
                return my_toast(`<i class="fas fa-check"></i>&nbsp ${info.mensaje}`, 'green darken-2', 3000);
            } else {
                return Swal.fire({
                    title: 'Error al enviar el correo',
                    text: `${info.mensaje}`,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return my_toast('<i class="fas fa-times"></i>&nbsp Ha ocurrido un error al enviar el correo', 'orange darken-2', 3000);
            }
        }, 500);
    })
}


function add_email() {
    Swal.fire({
        title: 'Ingrese el correo al que se enviaran los informes',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            label: 'Correo'
        },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: (correo) => {
            if (correo == '') return Swal.showValidationMessage(`El correo es obligatorio`);

            var data = new URLSearchParams({
                email: correo,
            });
            var result = proceso_fetch(base_url(['funcionario', 'certificacion', 'email', 'create']), data.toString());
            // return Swal.isLoading();
            return result.then(data => {
                if (!data.validado) {
                    return Swal.showValidationMessage(`El correo es ya se encuentra registrado`);
                } else {

                    table_emails['table'].row.add({
                        email: correo,
                        id: data.data
                    }).draw();
                    return null;
                }
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {

            Swal.fire({
                title: 'Correo ingresado a la lista correctamente',
                icon: 'success'
            });

            var correo = result.value;
            var array_aux = { email: correo };
            aux_emails.push(array_aux);
            $('#addEmails').val(JSON.stringify(aux_emails));
        }
    })
}

function edit_email(key) {
    console.log(aux_emails[key]);
    Swal.fire({
        title: `Editar correo '${aux_emails[key].email}'`,
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            label: 'Correo'
        },
        showCancelButton: true,
        confirmButtonText: 'Editar',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: (correo) => {
            if (correo == '') return Swal.showValidationMessage(`El correo es obligatorio`);
            var validate = aux_emails.every((value, key_aux) => {
                if (correo === value.email && key != key_aux) return false;
                else return true;
            })
            if (!validate) return Swal.showValidationMessage(`El correo es ya se encuentra registrado`);
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            var correo = result.value;
            aux_emails[key].email = correo;
            $('#addEmails').val(JSON.stringify(aux_emails));
        }
    })
}

function delete_email(key) {
    aux_emails.splice(key, 1);
    $(`#email_${key}`).remove()
    $('#addEmails').val(JSON.stringify(aux_emails));
}

function table_email() {
    var table = `
        <div style="
        max-height: 400px;
        overflow-y: auto;">
        <table id="table_email" class="centered">
            <thead>
                <tr>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        <tbody>`;
    aux_emails.forEach((value, key) => {
        table += `
                <tr id="email_${key}">
                    <td>${value.email}</td>
                    <td>
                        <a class="btn-floating mb-1 edit" onclick="edit_email('${key}')"><i class="material-icons">create</i></a>
                        <a class="btn-floating mb-1 delete" onclick="delete_email('${key}')"><i class="material-icons">close</i></a>
                    </td>
                </tr>`
    });
    table += `</tbody>
    </table></div>`;
    Swal.fire({
        title: 'Correos ingresados',
        confirmButtonText: 'Cerrar',
        html: table,
        heightAuto: '50px'
    })
}