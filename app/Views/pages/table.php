<?= view('layouts/header') ?>
<?php if ($title == 'Informes'): ?>
    <link rel="stylesheet" type="text/css" href="<?= base_url(['assets', 'css', 'funcionario', 'certificados.css']) ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url(['assets', 'css', 'funcionario', 'informe.css']) ?>">
    <link rel="stylesheet" href="<?= base_url() ?>/dropify/css/dropify.min.css">
    
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/flag-icon.min.css">
<?php endif ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>

<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content card-table <?= $title == 'Informes' ? 'td-none':'' ?>">
                            <h4 class="card-title"><?= $title ?></h4>
                            <p><?= $subtitle ?></p>
                                <?=  $output ?>
                        </div>
                        <?php if ($title == 'Informes'): ?>
                            <form id="form-download" action="<?= base_url(['certificado', 'download']) ?>" method="POST">
                                <input id="certificado_reporte" type="checkbox" name="certificado_reporte[]" value="">
                                <input id="certificado_preliminar" type="checkbox" name="certificado_preliminar[]" value="">
                                <input id="validador" type="checkbox" name="validador"/>
                            </form>
                            <div class="card-content card-detalle" style="display:none">
                                <div class="content-info">
                                    <form id="form-certificados" action="<?= base_url(['funcionario', 'certificacion']) ?>" method="POST">
                                    </form>
                                </div>
                                <!-- <button class="btn green darken-3" onClick="js_mostrar_detalle(`card-table`, `card-detalle`,``,2,`php_lista_resultados`)">Volver atrás</button> -->
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'funciones.js']) ?>"></script>
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'certificacion.js']) ?>"></script>