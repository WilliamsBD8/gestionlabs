function proceso_fetch(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret' },
        body: data
    }).then(response => {
        if (!response.ok) throw Error(response.status);
        return response.json();
    }).catch(error => alert("Error"));
}

function validation() {
    Swal.fire({
        title: 'Digite el codigo del certificado.',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonColor: '',
        confirmButtonText: 'Validar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (codigo) => {
            var url = $('form#form-valid').attr('action');
            var data = new URLSearchParams({
                codigo: codigo
            });
            var result = proceso_fetch(url, data.toString());
            result.then(respuesta => {
                if (respuesta.response) {
                    var certificado = respuesta.certificado;
                    Swal.fire({
                        title: 'Certificado encontrado',
                        text: `Certificado Nro. ${ certificado.certificado_nro }`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Descargar',
                        cancelButtonText: 'Cerrar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var type = respuesta.type;
                            if (type == 2) { // 2 - informe
                                $('#certificado_reporte').val(certificado.certificado_nro);
                                $('#certificado_reporte').attr('checked', true);
                            } else {
                                $('#certificado_preliminar').val(certificado.certificado_nro);
                                $('#certificado_preliminar').attr('checked', true);
                            }
                            $('#form-download').submit();
                            $('#certificado_preliminar').attr('checked', false);
                            $('#certificado_reporte').attr('checked', false);
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: `No se encontro certificado con codigo ${ codigo }`,
                    })
                }

            })
        },
        allowOutsideClick: () => !Swal.isLoading()
    })
}