<?= view('layouts/header') ?>
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.css">
	
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">
    
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
<?= view('layouts/navbar_vertical') ?>
<?= view('layouts/navbar_horizontal') ?>
    <!-- BEGIN: Page Main-->
<?php if ( !empty(configInfo()['intro']) && isset(configInfo()['intro'])): ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div class="card">
                            <div class="card-content">
                                <?= configInfo()['intro'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div id="sales-chart">
                            <div class="row">
                                <div class="col s12">
                                    <?php if (session('error')): ?>
                                        <div class="card-alert card red">
                                            <div class="card-content white-text">
                                                <p><?= session('error') ?></p>
                                            </div>
                                            <button type="button"class="close white-text" data-dismiss="alert"
                                                    aria-label="Close">
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (session('success')): ?>
                                        <div class="card-alert card green">
                                            <div class="card-content white-text">
                                                <p><?= session('success') ?></p>
                                            </div>
                                            <button type="button"class="close white-text" data-dismiss="alert"
                                                    aria-label="Close">
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col s12">
                                    <div id="revenue-chart" class="card animate fadeUp">
                                        <div class="card-content">
                                            <h2 class="card-title">
                                                Informes 
                                            </h2>
        <div class="row">
            <div class="col s12">
                <form autocomplete="off" id="form_filtrar" action="<?= base_url(['cliente/certificado/filtrar'])?>" method="POST" >
                    <div class="row">
                        <div class="input-field col s12 l6 m12">
                            <select name="concepto" onchange="search(this.value, 11)" id="concepto">
                                  <option value="" selected>Sin filtrar</option>
                                  <option value="Cumple">Cumple</option>
                                  <option value="No cumple">No cumple</option>
                                  <option value="No aplica">No aplica</option>
                            </select>
                            <!--<input name="concepto" onchange="search(this.value, 10)" id="concepto" autocomplete="off" type="text">-->
                            <label for="concepto">Conformidad</label>
                        </div>
                        <div class="input-field col s12 l3 m12 x13">
                            <input name="date_start" onchange="search(this.value, 0)" id="date_start" autocomplete="off" type="date">
                            <label>Fecha de inicio</label>
                        </div>
                        <div class="input-field col s12 l3 m12 x13">
                            <input name="date_finish" onchange="search(this.value, 0)" id="date_finish" autocomplete="off" type="date">
                            <label>Fecha final</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 l3 m12">
                            <input name="producto" onchange="search(this.value, 4)" id="producto" autocomplete="off" type="text">
                            <label for="producto">Producto</label>
                        </div>
                        <div class="input-field col s12 l3 m12">    
                            <input name="seccional" onchange="search(this.value, 3)" id="seccional" autocomplete="off" type="text">
                            <label for="seccional">Seccional</label>
                        </div>
                        <div class="input-field col s12 l3 m12 x13">
                            <select name="parametros" onchange="search(this.value, 8)">
                                <option value="">Sin filtrar</option>
                                <?php foreach ($filtros->resultado_parametros as $value):?>
                                    <option value="<?=$value->id_parametro?>"><?= $value->par_descripcion ? $value->par_descripcion : 'Sin filtrar' ?></option>
                                <?php endforeach ?>
                            </select>
                            <label>Parametros</label>
                        </div>
                        <div class="input-field col s12 l3 m12 x13">
                            <select name="tipo_analisis" onchange="search(this.value, 9)">
                                <option value="0">Sin filtrar</option>
                                <?php foreach ($filtros->resultado_muestra as $value):?>
                                    <option value="<?=$value->id_muestra_tipo_analsis?>"><?= $value->mue_nombre ?></option>
                                <?php endforeach ?>
                            </select>
                            <label>Tipo de análisis</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l12 m12 x13 tabla_certificados">
                <?php session() ?>
                <form action="<?= base_url(['certificado', 'download']) ?>" method="POST">
                    <input type="hidden" name="plantilla" value="1">
                    <div class="card-content section-data-tables">
                        <table class="display dataTables" id="table_certificados">
                                <thead>
                                        <tr>
                                                <th>Fecha de registro</th>
                                                <th>Inform Nro.</th>
                                                <th>Lote</th>
                                                <th>Seccional</th>
                                                <th>Producto</th>
                                                <th>Primer informe</th>
                                                <th>Informe</th>
                                                <th>Estado</th>
                                        </tr>
                                </thead>
                                <tbody></tbody>
                        </table>
                    </div>
                    <button class="waves-effect waves-light btn download" type="submit">Descargar</button>
                </form>
            </div>
        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= view('layouts/footer') ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script src="<?= base_url() ?>/assets/js/filtrar.js"></script>
