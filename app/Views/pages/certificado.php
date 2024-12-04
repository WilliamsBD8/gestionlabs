<?= view('layouts/header') ?>
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
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
                                                Certificados 
                                            </h2>
        <div class="row">
            <div class="col s12">
                <form autocomplete="off" id="form_filtrar" action="<?= base_url(['amc-laboratorio/certificado/filtrar'])?>"method="POST">
                    <div class="input-field col s12 l6 m12 x1">
                        <select name="concepto">
                            <option value="-1">Sin filtrar</option>
                            <!-- <option value="">Concepto vacio</option> -->
                            <?php foreach ($resultado_concepto as $key => $value):?>
                                <option value="<?=$value->id_mensaje?>"><?= $value->mensaje_titulo ? $value->mensaje_titulo:'Concepto vacio' ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Concepto</label>
                    </div>
                    <div class="input-field col s12 l3 m12 x13">
                        <input name="date_start" autocomplete="off" type="date">
                        <label>Fecha de inicio</label>
                    </div>
                    <div class="input-field col s12 l3 m12 x13">
                        <input name="date_finish" autocomplete="off" type="date">
                        <label>Fecha final</label>
                    </div>

                    <div class="input-field col s12 l3 m12 x13">
                        <select name="producto">
                            <option value="0">Sin filtrar</option>
                            <?php foreach ($resultado_productos as $value):?>
                                <option value="<?=$value->id_producto?>"><?= $value->producto ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Productos</label>
                    </div>

                    <div class="input-field col s12 l3 m12 x13">
                        <select name="seccional">
                            <option value="0">Sin filtrar</option>
                            <?php foreach ($resultado_seccional as $value):?>
                                <option value="<?= $value ? $value: 1 ?>"><?= $value ? $value: 'Seccional vacio' ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Seccional</label>
                    </div>
                    
                    <div class="input-field col s12 l3 m12 x13">
                        <select name="parametros">
                            <!-- <option value="">Sin filtrar</option> -->
                            <?php foreach ($resultado_parametros as $value):?>
                                <option value="<?=$value->id_parametro?>"><?= $value->par_nombre ? $value->par_nombre:'Sin filtrar' ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Parametros</label>
                    </div>
                    <div class="input-field col s12 l3 m12 x13">
                        <select name="tipo_analisis">
                            <option value="0">Sin filtrar</option>
                            <?php foreach ($resultado_muestra as $value):?>
                                <option value="<?=$value->id_muestra_tipo_analsis?>"><?= $value->mue_nombre ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Tipo de análisis</label>
                    </div>
                    
                    <a id="filtrar" class="waves-effect waves-light btn">Buscar</a>
                    <!-- <button id="filtrar" class="waves-effect waves-light btn">Buscar</button> -->
                    <button type="reset" class="btn red accent-3 reset_btn">Reiniciar</button>
                </form>
            </div>
            <div class="col s12 mostrar_text">
                <p id="r_total">Mostrando: <?= $total_2 ?> de <?= $total ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l12 m12 x13 tabla_certificados">
                <?php session() ?>
                <form action="<?= base_url(['amc-laboratorio/certificado/download']) ?>" method="POST">
                    <div id="tabla"></div>
                    <?= $certificados ?>
                    <button class="waves-effect waves-light btn download" type="submit">Descargar</button>
                </form>
            </div>
            <div class="col s12 paginator">
                <form action="<?= base_url(['amc-laboratorio/certificado/filtrar'])?>" method="POST" id="form_pagina">
                    <div class="input-field col s12 l6 m6 x13">
                        <div class="botones_paginador">
                            <a data-pagina="start" class="enviar start deep-purple darken-1" data-page="0" style="display: none;"><i class="fas fa-angle-double-left"></i></a>
                            <a data-pagina="back" class="enviar back deep-purple darken-1" data-page="" style="display: none;"><i class="fas fa-angle-left"></i></a>
                            <p class="pagina_text"> Pagina 1 </p>
                            <a data-pagina="next" class="enviar next deep-purple darken-1" data-page="1"><i class="fas fa-angle-right"></i></a>
                            <a data-pagina="finish" class="enviar finish deep-purple darken-1" data-page="<?= ($count-1) ?>"><i class="fas fa-angle-double-right"></i></a>
                        </div>
                    </div>
                    <div class="input-field col s12 l3 m6 x13 paginador_select">
                        <select name="limite">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="">Todos</option>
                        </select>
                        <label>Número de resultados</label>
                    </div>
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
<script src="<?= base_url() ?>/assets/js/filtrar.js"></script>
