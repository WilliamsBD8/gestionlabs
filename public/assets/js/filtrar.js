const tables = [];
$(() => {

    tables['table'] = $('#table_certificados').DataTable({
        dom: 'Blrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn green',exportOptions: {columns: [ 0, 1, 2, 3, 4, 11 ]} },
            {
                text: 'Filtrar',
                className: 'btn blue',
                action: function() {
                    Swal.fire({
                        html: '<div class="card-content redo"><div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div><div class="card-action"></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                    tables['table'].draw();
                }
            },
            {
                text: 'Resetear',
                className: 'btn red',
                action: function() {
                    Swal.fire({
                        html: '<div class="card-content redo"><div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div><div class="card-action"></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                    $('#form_filtrar')[0].reset();
                    tables['table'].columns().search('').draw();
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            "url": $('#form_filtrar').attr('action'),
            "dataSrc": 'data'
        },
        columns: [
            {
                data: 'mue_fecha_muestreo',
                name: 'mue_fecha_muestreo'
            },
            {
                data: 'certificado_nro',
                name: 'certificado_nro',
            },
            {
                data: 'mue_lote',
                name: 'mue_lote',
            },
            {
                data: 'mue_subtitulo',
                name: 'mue_subtitulo',
            },
            {
                data: 'mue_identificacion',
                name: 'mue_identificacion',
            },
            {
                data: 'preinforme',
                name: 'preinforme',
                render: (date, type, row) => {
                    if (date === '0000-00-00 00:00:00') {
                        return `
                            <label>
                                <input type="checkbox" disabled="disable" />
                                <span></span>
                            </label>
                        `;
                    }
                    return `
                            <label>
                                <input type="checkbox" name="certificado_preliminar[]" value="${row.certificado_nro}" />
                                <span></span>
                            </label>
                    `;

                }
            },
            {
                data: 'fecha_publicacion',
                name: 'fecha_publicacion',
                render: (date, type, row) => {
                    if (date === null) {
                        return `
                            <label>
                                <input type="checkbox" disabled="disable" />
                                <span></span>
                            </label>
                        `;
                    }
                    return `
                            <label>
                                <input type="checkbox" name="certificado_reporte[]" value="${row.certificado_nro}" />
                                <span></span>
                            </label>
                    `;
                }
            },
            {
                data: 'fecha_publicacion',
                name: 'estado',
                render: (date) => {
                    if (date === null) {
                        return `
                            <div class="estado">
                                <span class="error tooltipped" data-html="true" data-position="left" data-tooltip="Certificado en proceso"><i class="fas fa-cog fa-spin"></i></span>
                            </div>
                        `;
                    }
                    return `
                        <div class="estado">
                            <span class="check tooltipped" data-html="true" data-position="left" data-tooltip="Certificado listo para descargar"><i class="far fa-check-circle"></i> </span>
                        </div>
                    `;
                }
            },
            {
                data: 'id_muestreo',
                name: 'parametro',
                visible: false
            },
            {
                data: 'id_muestreo',
                name: 'tipo_analisis',
                visible: false
            },
            {
                data: 'mensaje',
                name: 'mensaje',
                visible: false
            },
            {
                title: 'Conformidad',
                data: 'conformidad',
                name: 'conformidad',
                visible: false
            }
        ],
        "responsive": false,
        "scrollX": true,
        "ordering": false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json",
            processing: 'Cargando ......'
        },
        searchPane: true,
        initComplete: function(data) {
            console.log(data.json);
        }
    }).on('draw', function(data) {
        $('.material-tooltip').remove();
        $('.tooltipped').tooltip();
        Swal.close();
    });

    $('#filtrar').click(function(e) {
        Swal.fire({
            html: '<div class="card-content redo"><div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div><div class="card-action"></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
        });
        console.log([
            $('#seccional').val(),
        ]);
        tables.ajax.reload(() => {
            return Swal.close();
        });
    });
    $('.reset_btn').click(function() {
        $('#form_filtrar')[0].reset();
        Swal.fire({
            html: '<div class="card-content redo"><div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div><div class="card-action"></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
        });
        tables.ajax.reload(() => {
            return Swal.close();
        });
    });
});

function search(value, idx) {
    switch (idx) {
        case 0:
            value = JSON.stringify({
                date_start: $('#date_start').val(),
                date_finish: $('#date_finish').val(),
            });
            break;
        default:
            break;
    }

    tables['table'].column(idx).search(value);

    // console.log([value, idx]);
}