<?= view('layouts/header') ?>
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
	
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">
    
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">

    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2.min.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2-materialize.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/form-select2.css">

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
            <div class="col s12 l12 m12 x13 tabla_certificados">
                <?php session() ?>
                <div class="row">
                    <div class="col s12">
                        <ul class="tabs">
                            <li class="tab col m6"><a href="#table_informes">Informes</a></li>
                            <li class="tab col m6"><a href="#table_emails" onclick="reinit_emails()">Emails</a></li>
                        </ul>
                    </div>
                    <div id="table_informes" class="col s12">
                        <form action="<?= base_url(['funcionario', 'certificacion', 'emails_certificado', 'send']) ?>"  id="form_informes" method="POST" target="_blank">
                            <input type="hidden" name="addEmails" id="addEmails">
                            <div class="row">
                                <div class="input-field col s12 ">
                                    <select class="select2 browser-default" id="cliente" name="id_cliente[]" multiple="multiple">
                                    <option value="" select>Sin filtrar</option>
                                    <?php foreach ($clientes as $key => $value): ?>
                                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                                    <?php endforeach ?>
                                    </select>
                                    <label>Cliente</label>
                                </div>
                                
                                <!--<div class="col s12 l3">-->
                                <!--    <a href="#!" onclick="add_email()" class="btn mt-2 tooltipped"  data-position="bottom" data-tooltip="Agregar correo"><i class="material-icons">add</i></a>-->
                                <!--</div>-->
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <input id="asunto" name="asunto" type="text" class="validate">
                                    <label for="asunto">Asunto</label>
                                </div>
                                <div class="input-field col s6">
                                    <textarea id="texto" name="texto" class="materialize-textarea"></textarea>
                                    <label for="texto">Mensaje</label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col s12 l12 center">
                                    <p>
                                      <label>
                                        <!--<input disabled id="validador" type="checkbox" name="validador"  checked="checked"/>-->
                                        <!--<span>Certificado plantilla nueva</span>-->
                                      </label>
                                    </p>
                                </div>
                            </div>
                            <input type="hidden" name="plantilla" value="1">
                            <div class="card-content section-data-tables">
                                <table class="display" id="table_certificados_emails">
                                        <thead>
                                                <tr>
                                                  <th>Informe Nro.</th>
                                                  <th>Fecha de registro</th>
                                                  <th>Cliente</th>
                                                  <th>Seccional</th>
                                                  <th>Codigo</th>
                                                  <th>Producto</th>
                                                  <th>Estado</th> 
                                                  <th>Informe</th>
                                                </tr>
                                        </thead>
                                        <tbody></tbody>
                                </table>
                            </div>
                        </form>
                        <button class="waves-effect waves-light btn download"  onclick="descargar()" type="submit">Enviar</button>
                    </div>
                    <div id="table_emails" class="col s12">
                        <div class="card-content section-data-tables">
                            <form id="emails_forms">
                                <table id="table_email">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Email</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                </table>
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
            </div>
        </div>
    </div>
<?php endif; ?>

<?= view('layouts/footer') ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/select2/select2.full.min.js"></script>
<script src="<?= base_url() ?>/app-assets/js/scripts/form-select2.min.js"></script>
<script src="<?= base_url() ?>/assets/js/funcionarios/funciones.js"></script>
<script src="<?= base_url() ?>/assets/js/funcionarios/emails.js"></script>
