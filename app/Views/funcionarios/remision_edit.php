<?= view('layouts/header') ?>
    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2.min.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2-materialize.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/form-select2.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
<?= view('layouts/navbar_vertical') ?>
<?= view('layouts/navbar_horizontal') ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            <div class="card animate fadeUp">
                                <div class="card-content">
    <h2 class="card-title">
        Editar muestras
    </h2>
    <hr>
    <form method="POST" id="myform" autocomplete="off" action="<?= base_url(['funcionario', 'remisiones', 'edit', 'muestra']) ?>">
        <div class="row">
            <div class="col s12 l10">
                <div class="row">
                    <div class="input-field col s5 certificado_editar">
                        <input type="text" name="frm_certificados_editar" id="frm_certificados_editar" class="autocomplete validate">
                        <label for="frm_certificados_editar">Seleccione informe a editar</label>
                        <small class="grey-text text-darken-4">
                            Autocompletado disponible desde el informe # <?= $ultimo ?> hasta el informe # <?= $primero  ?>
                        </small> 
                    </div>
                    <div class="col s7">
                        <div class="row">
                            <div class="input-field col s6 certificado_editar">
                                <input type="text" name="frm_muestra_editar" id="frm_muestra_editar" class="autocomplete validate">
                                <label for="frm_muestra_editar">Seleccione código a editar</label>
                            </div>
                            <div class="input-field col s6 certificado_editar">
                                <input type="text" name="frm_muestra_editar_anio" id="frm_muestra_editar_anio" class="autocomplete validate">
                                <label for="frm_muestra_editar_anio">Seleccione año a editar</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="input-field col s12 l2 centrar_button">
                <a href="javascript:void(0);" class="btn gradient-45deg-purple-deep-orange border-round" onclick="search_muestra()" id="btn-buscar-muestra">
                    <i class="fas fa-search"></i> Buscar
                </a>
            </div>
        </div>
    </form>
    <br>
    <hr>
    <form action="<?= base_url(['funcionario','remisiones','empresa']) ?>" method="POST" id="frm_form" autocomplete="off">
        <input type='hidden' name='frm_nombre_empresa2' id='frm_nombre_empresa2' class='required'/>
        <input type="hidden" name="empresa_nueva" id="empresa_nueva" value="1">
        <input type="hidden" id="click" value="0">
        <input type="hidden" name="buscar" value="1">
        <div class="row empresa_row">
            <div class="col s12">
                <div class="row">
                    <div class="input-field col l4 s12 empresa">
                        
                        <select class="select2 browser-default select-form" name="frm_nombre_empresa" id="frm_nombre_empresa" onchange="buscar_cliente(1)">
                        </select>
                        <!--<input id="frm_nombre_empresa" name="frm_nombre_empresa" type="text" class="autocomplete frm_nombre_empresa validate">-->
                        <!--<label for="frm_nombre_empresa">Empresa</label>-->
                        <!--<small class=" red-text text-darken-4" id="frm_nombre_empresa"></small>-->
                    </div>
                    <div class="input-field col l2 s12">
                        <input id="frm_nombre_empresa_subtitulo" name="frm_nombre_empresa_subtitulo" type="text" class="validate">
                        <label for="frm_nombre_empresa_subtitulo">Sucursal</label>
                        <small class=" red-text text-darken-4" id="frm_nombre_empresa_subtitulo"></small>
                    </div>
                    <div class="input-field col l6 s12 nit">
                        <input id="frm_nit" name="frm_nit" type="text" class="validate nit">
                        <label for="frm_nit">Nit/Cédula</label>
                        <small class=" red-text text-darken-4" id="frm_nit"></small>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col l5 s12">
                        <input id="frm_contacto_nombre" name="frm_contacto_nombre" type="text" class="validate">
                        <label for="frm_contacto_nombre">Nombre del contacto</label>
                        <small class=" red-text text-darken-4" id="frm_contacto_nombre"></small>
                    </div>
                    <div class="input-field col l2 s12">
                        <input id="frm_contacto_cargo" name="frm_contacto_cargo" type="text" class="validate">
                        <label for="frm_contacto_cargo">Cargo</label>
                        <small class=" red-text text-darken-4" id="frm_contacto_cargo"></small>
                    </div>
                    <div class="input-field col l5 s12">
                        <input id="frm_telefono" name="frm_telefono" type="text" class="validate">
                        <label for="frm_telefono">Telefono</label>
                        <small class=" red-text text-darken-4" id="frm_telefono"></small>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col l6 s12">
                        <input id="frm_fax" name="frm_fax" type="text" class="validate">
                        <label for="frm_fax">Numero de fax</label>
                        <small class=" red-text text-darken-4" id="frm_fax"></small>
                    </div>
                    <div class="input-field col l6 s12">
                        <input id="frm_correo" name="frm_correo" type="text" class="validate">
                        <label for="frm_correo">Email</label>
                        <small class=" red-text text-darken-4" id="frm_correo"></small>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col l6 s12">
                        <input id="frm_direccion" name="frm_direccion" type="text" class="validate">
                        <label for="frm_direccion">Dirección</label>
                        <small class="red-text text-darken-4" id="frm_direccion"></small>
                    </div>
                    <div class="input-field col l4 s12 fecha_muestra">
                        <input name="frm_fecha_muestra" id="frm_fecha_muestra" type="text" class="date_picker" value="<?= date('Y-m-d')?>">
                        <label for="frm_fecha_muestra">Fecha de muestreo</label>
                    </div>
                    <div class="input-field col l2 s12 hora">
                        <input name="frm_hora_muestra" id="frm_hora_muestra" type="text" class="time_picker" value="<?= date('H:i:s')?>">
                        <label for="frm_hora_muestra">Hora</label>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="input-field col s12 centrar_button">
                <button class="btn gradient-45deg-purple-deep-orange border-round" id="btn-empresa">
                    Actualizar empresa
                </button>
            </div>
        </div>
        <input type="hidden" name="frm_id_forma" id="frm_id_forma" value="forma_cliente_muestra"/>
        <input type="hidden" name="frm_id_muestra" id="frm_id_muestra" value=""/>
    </form>
    <ul class="collapsible popout">
        <li class="active">
            <div class="collapsible-header">Muestra</div>
            <div class="collapsible-body">
                <div class="row">
                    <div class="col s12">
                        <form action="<?= base_url(['funcionario','remisiones','muestra']) ?>" method="POST" id="frm_form_muestra" autocomplete="off">
                            <input type="hidden" name="id_muestra_detalle" id="id_muestra_detalle" value="">
                            <div class="row">
                                <div class="input-field col l4 s12">
                                    <input id="frm_identificacion" name="frm_identificacion" type="text" class="validate">
                                    <label for="frm_identificacion">Identificación de la muestra</label>
                                    <small class="red-text text-darken-4" id="frm_identificacion"></small>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input id="frm_lote" name="frm_lote" type="text" class="validate">
                                    <label for="frm_lote">Número de lote</label>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input id="frm_fecha_produccion" name="frm_fecha_produccion" type="text" class="validate">
                                    <label for="frm_fecha_produccion">Fecha de producción</label>
                                    <p>
                                        <label>
                                            <input id="frm_fecha_produccion_check" onChange="changeType('frm_fecha_produccion', 'frm_fecha_vencimiento_check')" type="checkbox" checked />
                                            <span>Formato ("DD-MM-AAAA")</span>
                                        </label>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col l4 s12">
                                    <input id="frm_fecha_vencimiento" name="frm_fecha_vencimiento" type="text" class="validate">
                                    <label for="frm_fecha_vencimiento">Fecha de vencimiento</label>
                                    <p>
                                        <label>
                                            <input id="frm_fecha_vencimiento_check" onChange="changeType('frm_fecha_vencimiento', 'frm_fecha_vencimiento_check')" type="checkbox" checked />
                                            <span>Formato ("DD-MM-AAAA")</span>
                                        </label>
                                    </p>
                                </div>
                                <!-- <div class="input-field col l4 s12">
                                    <input id="frm_momento_muestreo" name="frm_momento_muestreo" type="text" class="validate">
                                    <label for="frm_momento_muestreo">Momento del muestreo</label>
                                </div> -->
                                <div class="input-field col l4 s12">
                                    <input id="frm_tmp_recepcion" name="frm_tmp_recepcion" type="text" class="validate">
                                    <label for="frm_tmp_recepcion">Temperatura de recepción</label>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input id="frm_tmp_muestreo" name="frm_tmp_muestreo" type="text" class="validate">
                                    <label for="frm_tmp_muestreo">Temperatura de ingreso</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col l4 s12 condiciones">
                                    <select id="frm_condiciones_recibido" name="frm_condiciones_recibido" class="validate select-form">
                                        <option value="" disabled selected>Seleccione condición</option>
                                        <option value="Cumple">Cumple</option>
                                        <option value="No cumple">No cumple</option>
                                    </select>
                                    <label>Condiciones de recibido</label>
                                </div>
                                <div class="input-field col l4 s12 cantidad">
                                    <input id="frm_cantidad" name="frm_cantidad" type="text" class="validate" value="1">
                                    <label for="frm_cantidad">Cantidad</label>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input id="frm_area" name="frm_area" type="text" class="validate">
                                    <label for="frm_area">Area / Función</label>
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="input-field col l4 s12">
                                    <p>Procedencia</p>
                                    <p>
                                        <label>
                                            <input class="with-gap" name="frm_procedencia" id="frm_procedencia_1" type="radio" checked value="1" />
                                            <span>Asbioquim</span>
                                        </label>
                                        <label>
                                            <input class="with-gap" name="frm_procedencia" id="frm_procedencia_2" type="radio" value="2" />
                                            <span>Cliente</span>
                                        </label>
                                    </p>
                                </div> -->
                                <!-- <div class="input-field col l4 s12">
                                    <input id="frm_parametro" name="frm_parametro" type="text" class="validate">
                                    <label for="frm_parametro">Tipo de muestra</label>
                                </div> -->
                            </div>
                            <div class="row">
                                <div class="input-field col l4 s12">
                                    <input id="frm_tipo_muestreo" name="frm_tipo_muestreo" type="text" class="validate">
                                    <label for="frm_tipo_muestreo">Tipo de muestreo</label>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input name="frm_mue_dilucion" id="frm_mue_dilucion" type="text" class="validate">
                                    <label for="frm_mue_dilucion">Dilución</label>
                                </div>
                                <div class="input-field col l4 s12">
                                    <input name="frm_mue_empaque" id="frm_mue_empaque" type="text" class="validate">
                                    <label for="frm_mue_empaque">Empaque</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col l6 s12">
                                    <textarea id="frm_adicional" name="frm_adicional" class="materialize-textarea"></textarea>
                                    <label for="frm_adicional">Observaciones</label>
                                </div>
                                <div class="input-field col l6 s12 mue_procedencia">
                                    <input type="text" id="frm_mue_procedencia" name="frm_mue_procedencia" class="validate autocomplete">
                                    <label for="frm_mue_procedencia">Muestra recolectada por</label>
                                </div>
                            </div>
                            <div class="row finish">
                                <div class="input-field col l4 s12 frm_analisis">
                                    <select class="select-form" name="frm_analisis" id="frm_analisis"  onchange="select_vida(this.value)">
                                        <option value="" disabled selected>Seleccione analisis</option>
                                        <?php foreach ($analisis as $key => $value): ?>
                                            <option value="<?= $value->id_muestra_tipo_analsis ?>"><?= $value->mue_nombre ?> / <?= $value->mue_sigla ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <label>Análisis solicitado</label>
                                    <small class="red-text text-darken-4" id="frm_analisis"></small>
                                </div>
                                <div class="input-field col l8 s12 frm_producto">
                                    <select class="select2 browser-default select-form" onchange="change_producto(this.value)" id="frm_producto" name="frm_producto" disabled="true">
                                        <option value="" selected>Area/Función</option>
                                        <?php foreach($productos as $producto): ?>
                                            <option value="<?= $producto->id_producto ?>"><?= $producto->pro_nombre ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <!-- <input id="frm_producto" name="frm_producto" type="text" class="validate autocomplete frm_producto" placeholder="Area/Función"> -->
                                    <!-- <input id="frm_producto" name="frm_producto" type="hidden" class="validate autocomplete frm_producto" placeholder="Area/Función">-->
                                    <!--<label for="frm_producto">Norma</label>-->
                                    <label for="frm_producto">Norma</label>
                                </div>
                                
                                <!-- <div class="col l12 s12 fechas_vida_util" style="display:none">
                                    <ul class="collapsible">
                                        <li>
                                            <div class="collapsible-header">Fechas vidas útiles</div>
                                            <div class="collapsible-body">

                                                <input type="hidden" name="vida_util" id="vida_util">

                                                <div class="row">
                                                    <div class="input-field col s12 l8">
                                                        <input type="text" name="aux_vida_util" id="aux_vida_util" class="date_picker">
                                                        <label for="aux_vida_util">Fecha vida útil</label>
                                                    </div>
                                                    <div class="input-field col s12 l4">
                                                        <input type="number" name="aux_vida_util_dia" id="aux_vida_util_dia">
                                                        <label for="aux_vida_util_dia">Día vida útil</label>
                                                    </div>
                                                    <div class="col s12">
                                                        <a onclick="add_vida_util()" class="waves-effect waves-light btn-small btn-add-vida">Agregar</a>
                                                    </div>
                                                </div>
                                                <table id="table_vida_util" class="centered">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Día</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </li>
                                    </ul>
                                </div> -->
                            </div>

                            <div class="row div_muestra_productos">
                                <h6 class="center-align"></h6>
                                <div id="div_muestra_productos" class="col s12 section-data-tables mt-2">
                                    <table class="display" id="table_datatable_muestra_productos"></table>
                                </div>
                            </div>
                            <hr>
                            <h5 class="card-title" id="tabla_detalles_muestras">Detalle de remisión</h5>
                            <div class="row">
                                <div id="status" class="col s12 section-data-tables">
                                    <table class="display" id="table_datatable"></table>
                                </div>
                            </div>
                            <!-- <div id="campo_detalle_muestras_basic">
                                <table class="striped centered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Informe</th>
                                            <th>Tipo de An&aacute;lisis</th>
                                            <th>C&oacute;digo</th>
                                            <th>Norma</th>
                                            <th>Identificaci&oacute;n</th>
                                            <th>Cantidad</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td scope="row">1</td>
                                            <td>@Informe</td>
                                            <td>@Tipo</td>
                                            <td>@C&oacute;digo</td>
                                            <td>@Norma</td>
                                            <td>@Identificaci&oacute;n</td>
                                            <td>@Cantidad</td>
                                            <td>@Opciones</td>
                                          </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="campo_detalle_muestras"></div> -->
                        </form>
                        <form id="frm_form_pie" method="POST" action="<?= base_url(['funcionario','remisiones','muestra']) ?>" autocomplete="off">
                            <div class="row">
                                <div class="input-field col s12 observacion">
                                    <textarea id="frm_observaciones" name="frm_observaciones" class="materialize-textarea"></textarea>
                                    <label for="frm_observaciones">Observaci&oacute;n</label>
                                </div>
                                <div class="input-field col l6 s12 entrega">
                                    <input id="frm_entrega" type="text" name="frm_entrega" class="validate" placeholder="Nombre de quien entrega la muestra">
                                    <label for="frm_entrega">Entrega muestra</label>
                                    <small class="red-text text-darken-4" id="frm_entrega"></small>
                                </div>
                                <div class="input-field col l6 s12 recibe">
                                    <input id="frm_recibe" type="text" name="frm_recibe" class="validate" placeholder="Nombre de quien recibe la muestra">
                                    <label for="frm_recibe">Recibe</label>
                                    <small class="red-text text-darken-4" id="frm_recibe"></small>
                                </div>
                            </div>
                            <div id="campo_id_remision">
                                <input type="hidden" value="0" name="frm_id_remision" id="frm_id_remision"/>
                            </div>
                            <input type="hidden" value="0" name="frm_estado_remision" id="frm_estado_remision"/>
                            <input type="hidden" value="<?=date('Y-m-d H:i:s');?>" name="frm_fecha_recepcion" id="frm_fecha_recepcion"/>
                            <input type="hidden" value="0000-00-00" name="frm_fecha_analisis" id="frm_fecha_analisis"/>
                            <input type="hidden" value="0000-00-00" name="frm_fecha_informe" id="frm_fecha_informe"/>
                            <input type="hidden" value="0" name="frm_responsable" id="frm_responsable"/>
                            <input type="hidden" name="accion" value="1">
                            <div class="input-field col s12 centrar_button btn_remision">
	                    	    <a href="javascript:void(0)" onclick="btn_remision_guardar()" id="btn-remision-guardar" class="btn border-round guardar_remision">Guardar remisión</a>
	                        </div>
                        </form>
                        <form method="POST" id="mensajesform" autocomplete="off" action="<?= base_url(['funcionario', 'remisiones', 'edit', 'muestra']) ?>">
                        </form>
                    </div>
                </div>
            </div>
        </li>
    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= view('layouts/footer') ?>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>

<script src="<?= base_url() ?>/app-assets/vendors/select2/select2.full.min.js"></script>
<!--<script src="<?= base_url() ?>/app-assets/js/scripts/form-select2.min.js"></script>-->
<script src="<?= base_url() ?>/assets/js/funcionarios/funciones.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var data = <?= json_encode($certificados,JSON_FORCE_OBJECT); ?>;
        $('#frm_certificados_editar.autocomplete').autocomplete({data:data});
    })
</script>
<!-- <script src="<?= base_url() ?>/assets/js/funcionarios/remision_edit.js"></script> -->
<script src="<?= base_url() ?>/assets/js/funcionarios/new_remision_edit.js"></script>