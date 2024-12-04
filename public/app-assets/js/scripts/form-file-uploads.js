$(document).ready(function() {
    // Basic
    $('.dropify').dropify();

    // Translated
    // $('.dropify-Es').dropify({
    //     messages: {
    //         default: 'Arrastre y suelte un archivo aquí o haga clic.',
    //         replace: 'Arrastre y suelte un archivo o haga clic para reemplazar',
    //         remove: 'Borrar',
    //         error: 'Ooops, sucedió algo malo.'
    //     },
    //     error: {
    //         'fileSize': 'The file size is too big ({{ value }} max).',
    //         'minWidth': 'The image width is too small ({{ value }}}px min).',
    //         'maxWidth': 'The image width is too big ({{ value }}}px max).',
    //         'minHeight': 'The image height is too small ({{ value }}}px min).',
    //         'maxHeight': 'The image height is too big ({{ value }}px max).',
    //         'imageFormat': 'The image format is not allowed ({{ value }} only).',
    //         'fileExtension': 'El archivo no está permitido. Formato válido ({{ value }}).',
    //     }
    // });

    // Used events
    var drEvent = $('.dropify-Es').dropify({
        messages: {
            default: 'Arrastre y suelte un archivo aquí o haga clic.',
            replace: 'Arrastre y suelte un archivo o haga clic para reemplazar',
            remove: 'Borrar',
            error: 'Ooops, sucedió algo malo.'
        },
        error: {
            'fileSize': 'The file size is too big ({{ value }} max).',
            'minWidth': 'The image width is too small ({{ value }}}px min).',
            'maxWidth': 'The image width is too big ({{ value }}}px max).',
            'minHeight': 'The image height is too small ({{ value }}}px min).',
            'maxHeight': 'The image height is too big ({{ value }}px max).',
            'imageFormat': 'The image format is not allowed ({{ value }} only).',
            'fileExtension': 'El archivo no está permitido. Formato válido ({{ value }}).',
        }
    });

    // drEvent.on('dropify.beforeClear', function(event, element) {
    //     return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
    // });

    // drEvent.on('dropify.afterClear', function(event, element) {
    //     alert('File deleted');
    // });
});