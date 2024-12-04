<?= view('layouts/header') ?>
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
                                        Registro de Muestras FisicoQuímicas de Aguas <?= $certificado ? $certificado->id_codigo_amc:'' ?>
                                    </h2>
                                    <hr>
<form method="POST" action="<?= base_url(['funcionario', 'resultados', 'analisis']) ?>" id="resultados_download" target="_blank">
    <input type="hidden" name="date_download" id="date_download">
    <input type="hidden" name="tipo_analisis" id="tipo_analisis">
    <input type="hidden" name="type" id="type">
</form>
<div class="row">
    <form class="col s12" method="POST" action="<?= base_url(['funcionario', 'resultados', 'aguas']) ?>">
        <div class="row">
            <div class="input-field col s12 l6">
                <input id="frm_anio_busca" name="frm_anio_busca" type="text" class="validate">
                <label for="frm_anio_busca">Año:</label>
                <small class="red-text text-darken-2"><?= $validation->getError('frm_anio_busca') ?></small>
            </div>
            <div class="input-field col s12 l6">
                <input id="frm_codigo_busca" name="frm_codigo_busca" type="text" class="validate">
                <label for="frm_codigo_busca">C&oacute;digo muestra:</label>
                <small class="red-text text-darken-2"><?= $validation->getError('frm_codigo_busca') ?></small>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12 l6 centrar_button">
                <button type="button" class="btn gradient-45deg-blue-deep-orange border-round" onclick="downloadAux()">
                <i class="fad fa-file-pdf"></i> Descargar hoja de trabajo
                </button>
            </div>
            <div class="input-field col s12 l6 centrar_button">
                <button class="btn gradient-45deg-purple-deep-orange border-round">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </form>
</div>
<?php if ($certificado->id_muestreo): ?>
<?php $equipos = procesar_registro_fetch('equipo_fq', 'estado', 'Activo') ?>
<div class="row">
    <div class="col l12 z-depth-2">
        <table class="striped centered">
            <thead>
                <tr>
                    <th>
                        <p class="card-title"><b><?= $certificado->mue_identificacion ?></b></p>
                        <b>Analisis: <?= $certificado->pro_nombre ?></b>
                    </th>
                </tr>
            </thead>
        </table>
        <form action="<?= base_url(['funcionario','resultados','date']) ?>" id="form_date" autocomplete="off">
            <?php
                $date_muestro = $certificado->mue_fecha_muestreo;
                $date_analisis = $certificado->cer_fecha_analisis;
            
                if($date_analisis == "0000-00-00 00:00:00"){
                    $aux = strtotime($date_muestro);
                    $date = date('Y-m-d', $aux);
                }else{
                    $aux = strtotime($date_analisis);
                    $date = date('Y-m-d', $aux);
                }
            
            ?>
            
            <!-- Editar fecha -->
            <div class="row">
                <?php if(!empty($certificado->fechasUtiles[0]->id)): ?>
                    <div class="input-field col s12 l6">
                        <select name="" id="select_<?= $certificado->id_muestreo_detalle ?>" onchange="cambiar_fecha_vida_util(this.value)">
                            <?php foreach ($certificado->fechasUtiles as $key => $fechaVidaUtil): ?>
                                <option value="<?= $fechaVidaUtil->id ?>">[<?= $fechaVidaUtil->dia ?>] <?= $fechaVidaUtil->fecha ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="select_<?= $certificado->id_muestreo_detalle ?>">Historico resultados</label>
                    </div>
                <?php else: ?>
                    <div class="input-field col s12 l6">
                        <input id="date_analisis" type="date" class="validate" value="<?= $date ?>" onblur="change_date(`<?= $certificado->id_certificacion ?>`, this.value)">
                        <label for="date_analisis">Fecha análisis</label>
                    </div>
                <?php endif ?>
            </div>
        </form>
        <form id="form_cambia_campos" method="POST" action="<?= base_url(['funcionario', 'resultados', 'aguas', 'cambiar', 'fq']) ?>">
            <?php foreach($certificado->fechasUtiles as $key_fecha => $fechaVidaUtil): ?>
                <?php $id_f = $fechaVidaUtil->id ?>
                <table class="striped centered table_resultados" id="table_<?= $fechaVidaUtil->id ?>" <?= $key_fecha == 0 ? '' : 'style="display:none"' ?>>
    
                    <tbody>
                        <?php /* ALCALINIDAD */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 18, 0, $id_f);// 18 Alcalinidad
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>ALCALINIDAD 1 <?= "$certificado->id_muestra_detalle, 18, 0, $id_f" ?></b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ((( V1 - Vb ) * N ) / M ) * 50000 </small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad1_1_<?= $id_f ?>" id="frm_alcalinidad1_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad1_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 18)">
                                            <label for="frm_alcalinidad1_1_<?= $id_f ?>">V1: Volumen de acido gastado en la muestra</label>
                                            <span id="frm_alcalinidad1_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4 ">
                                            <input type="text" name="frm_alcalinidad1_2_<?= $id_f ?>" id="frm_alcalinidad1_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad1_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 18)">
                                            <label for="frm_alcalinidad1_2_<?= $id_f ?>">Vb: Volumen acido gastado en blanco</label>
                                            <span id="frm_alcalinidad1_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad1_3_<?= $id_f ?>" id="frm_alcalinidad1_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad1_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 18)">
                                            <label for="frm_alcalinidad1_3_<?= $id_f ?>">N: normalidad del ácido</label>
                                            <span id="frm_alcalinidad1_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad1_4_<?= $id_f ?>" id="frm_alcalinidad1_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad1_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 18)">
                                            <label for="frm_alcalinidad1_4_<?= $id_f ?>">M: muestra en mL</label>
                                            <span id="frm_alcalinidad1_4_<?= $id_f ?>"></span>
                                        </div>
                                        
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_alcalinidad1_equipo_<?= $id_f ?>"
                                                id="frm_alcalinidad1_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad1_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 18)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>Alcalinidad 1</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col col s12">
                                            <p><b>IRCA Alcalinidad 1</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                    
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>
                            
                            <?php /* ALCALINIDAD 2 */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 19, 0, $id_f);// 19 Alcalinidad
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>ALCALINIDAD 2</b>
                                    <br>Cuando la alcalinidd es >20 mgCaCO3/L<br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ((( 2 * V1 - Vb ) * N ) / M ) * 50000 </small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad2_1_<?= $id_f ?>" id="frm_alcalinidad2_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad2_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 19)">
                                            <label for="frm_alcalinidad2_1_<?= $id_f ?>">V1: Volumen de acido gastado en la muestra 1pH</label>
                                            <span id="frm_alcalinidad2_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4 ">
                                            <input type="text" name="frm_alcalinidad2_2_<?= $id_f ?>" id="frm_alcalinidad2_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad2_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 19)">
                                            <label for="frm_alcalinidad2_2_<?= $id_f ?>">V2: Volumen acido gastado gastado en la muestra 2pH</label>
                                            <span id="frm_alcalinidad2_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad2_3_<?= $id_f ?>" id="frm_alcalinidad2_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad2_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 19)">
                                            <label for="frm_alcalinidad2_3_<?= $id_f ?>">N: normalidad del ácido</label>
                                            <span id="frm_alcalinidad2_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_alcalinidad2_4_<?= $id_f ?>" id="frm_alcalinidad2_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad2_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 19)">
                                            <label for="frm_alcalinidad2_4_<?= $id_f ?>">M: muestra en mL</label>
                                            <span id="frm_alcalinidad2_4_<?= $id_f ?>"></span>
                                        </div>
                                        
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_alcalinidad2_equipo_<?= $id_f ?>"
                                                id="frm_alcalinidad2_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_alcalinidad2_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 19)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>Alcalinidad 2</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col col s12">
                                            <p><b>IRCA Alcalinidad 2</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                    
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>
                            
                            <?php /* cloruros */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 20, 0, $id_f);// 20 cloruros
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>CLORUROS</b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ( ( ( Vf - Vb ) * N ) / M) * 35450</small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_cloruros_1_<?= $id_f ?>" id="frm_cloruros_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_cloruros_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 20)">
                                            <label for="frm_cloruros_1_<?= $id_f ?>">Vf: Volumen de AgNO3 gastados</label>
                                            <span id="frm_cloruros_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4 ">
                                            <input type="text" name="frm_cloruros_2_<?= $id_f ?>" id="frm_cloruros_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_cloruros_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 20)">
                                            <label for="frm_cloruros_2_<?= $id_f ?>">Vb: Volumen de AgNO3 gastado en blanco</label>
                                            <span id="frm_cloruros_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_cloruros_3_<?= $id_f ?>" id="frm_cloruros_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_cloruros_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 20)">
                                            <label for="frm_cloruros_3_<?= $id_f ?>">N: Normalidad</label>
                                            <span id="frm_cloruros_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_cloruros_4_<?= $id_f ?>" id="frm_cloruros_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_cloruros_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 20)">
                                            <label for="frm_cloruros_4_<?= $id_f ?>">M: volumen de la muestra (mL)</label>
                                            <span id="frm_cloruros_4_<?= $id_f ?>"></span>
                                        </div>
                                        
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_cloruros_equipo_<?= $id_f ?>"
                                                id="frm_cloruros_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_cloruros_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 20)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>CLORUROS</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col col s12">
                                            <p><b>IRCA CLORUROS</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                        
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>
                            
                            <?php /* dureza total */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 21, 0, $id_f);// 21 dureza total
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>DUREZA TOTAL</b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ( ( ( A - B ) * M ) / V) * 100087</small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_total_1_<?= $id_f ?>" id="frm_dureza_total_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_total_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 21)">
                                            <label for="frm_dureza_total_1_<?= $id_f ?>">A: Volumen del EDTA empleado</label>
                                            <span id="frm_dureza_total_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4 ">
                                            <input type="text" name="frm_dureza_total_2_<?= $id_f ?>" id="frm_dureza_total_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_total_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 21)">
                                            <label for="frm_dureza_total_2_<?= $id_f ?>">B: Volumen del EDTA en blanco</label>
                                            <span id="frm_dureza_total_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_total_3_<?= $id_f ?>" id="frm_dureza_total_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_total_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 21)">
                                            <label for="frm_dureza_total_3_<?= $id_f ?>">M: molaridd el EDTA</label>
                                            <span id="frm_dureza_total_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_total_4_<?= $id_f ?>" id="frm_dureza_total_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_total_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 21)">
                                            <label for="frm_dureza_total_4_<?= $id_f ?>">V: Volumen de muestra (mL)</label>
                                            <span id="frm_dureza_total_4_<?= $id_f ?>"></span>
                                        </div>
                                        
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_dureza_total_equipo_<?= $id_f ?>"
                                                id="frm_dureza_total_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_total_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 21)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>DUREZA TOTAL</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col col s12">
                                            <p><b>IRCA DUREZA TOTAL</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                        
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>
                            
                            
                            <?php /* dureza calcica */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 22, 0, $id_f);// 22 dureza calcica
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>DUREZA CALCICA</b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ( ( A * M ) / V) * 100087</small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_calcica_1_<?= $id_f ?>" id="frm_dureza_calcica_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_calcica_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 22)">
                                            <label for="frm_dureza_calcica_1_<?= $id_f ?>">A: Volumen del EDTA empleado</label>
                                            <span id="frm_dureza_calcica_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_calcica_2_<?= $id_f ?>" id="frm_dureza_calcica_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_calcica_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 22)">
                                            <label for="frm_dureza_calcica_2_<?= $id_f ?>">M: molaridd el EDTA</label>
                                            <span id="frm_dureza_calcica_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_dureza_calcica_3_<?= $id_f ?>" id="frm_dureza_calcica_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_calcica_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 22)">
                                            <label for="frm_dureza_calcica_3_<?= $id_f ?>">V: Volumen de muestra (mL)</label>
                                            <span id="frm_dureza_calcica_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_dureza_calcica_equipo_<?= $id_f ?>"
                                                id="frm_dureza_calcica_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_dureza_calcica_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 22)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>DUREZA CALCICA TOTAL</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>IRCA CALCICA TOTAL</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                        
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>
                            
                            
                            <!-- area para Dureza magnesica: -->
                            
                             <?php /* Dureza magnesica*/
                                $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 23, 0, $id_f);// 23 Dureza magnesica
                                //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                                //echo '--->'.$salida[0]->id_parametro;
                                //echo '--->'.$certificado->id_muestra_detalle;
                                 
                               if (!empty($salida[0])): ?>
                                <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                                ?>
                                    <tr>
                                        <td>
                                            <p class="center-align">
                                                <b>DUREZA MAGNESICA</b>
                                                <br>
                                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> ( Dureza total - Dureza calcica ) * 0,242</small>
                                            </p>
                                            <div class="row">
                                                <div class="input-field col s12 l6">
                                                    <select id="campo_muestra_redondeo_dureza_magnesica_<?= $id_f ?>" name="campo_muestra_redondeo_dureza_magnesica_<?= $id_f ?>"
                                                        onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'dureza_magnesica_<?= $id_f ?>', this.value, 'calcula_dureza_magnesica')">
                                                        <option>Sin seleccionar</option>
                                                        <option value="0">Número entero</option>
                                                        <option value="1">Número con 1 decimal</option>
                                                        <option value="2">Número con 2 decimales</option>
                                                        <option value="2">Número con 3 decimales</option>
                                                    </select>
                                                    <label for="campo_muestra_redondeo_dureza_magnesica_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                                </div>
                                                <div class="col s12 l6 campo_resultado_dureza_magnesica_<?= $id_f ?>">
                                                    <p>DUREZA MAGNESICA</p>
                                                    <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' ' : 'Sin resultado' ?></b>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif ?>
                            
                            <!-- fin area para Dureza magnesica: -->
                            
                            <?php /* calcio */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 24, 0, $id_f);// 24 calcio
                        //echo '--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '--->'.$salida[0]->id_parametro;
                        //echo '--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>CALCIO</b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>Formula:</b> ( ( A * M ) / V) * 40078</small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_calcio_1_<?= $id_f ?>" id="frm_calcio_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_calcio_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 24)">
                                            <label for="frm_calcio_1_<?= $id_f ?>">A: Volumen del EDTA empleado</label>
                                            <span id="frm_calcio_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_calcio_2_<?= $id_f ?>" id="frm_calcio_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_calcio_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 24)">
                                            <label for="frm_calcio_2_<?= $id_f ?>">M: molaridd el EDTA</label>
                                            <span id="frm_calcio_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_calcio_3_<?= $id_f ?>" id="frm_calcio_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_calcio_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 24)">
                                            <label for="frm_calcio_3_<?= $id_f ?>">V: Volumen de muestra (mL)</label>
                                            <span id="frm_calcio_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_calcio_equipo_<?= $id_f ?>"
                                                id="frm_calcio_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_calcio_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 24)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>CALCIO TOTAL</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>IRCA CALCIO TOTAL</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                        
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>  
                            
                            
                            
                            <?php /* solidos totales */
                        $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 25, 0, $id_f);// 25 solidos totales
                        //echo '<br>id_ensayo_vs_muestra--->'.$salida[0]->id_ensayo_vs_muestra;
                        //echo '<br>id_parametro--->'.$salida[0]->id_parametro;
                        //echo '<br>id_muestra_detalle--->'.$certificado->id_muestra_detalle;
                                 
                        if (!empty($salida[0])): ?>
                            <?php
                            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>    
                            <tr>
                                <td>
                                    <p class="center-align">
                                    <b>SOLIDOS TOTALES</b>
                                    <br>
                                    <small> <?=$parametro[0]->par_nombre; ?> <b>Formula:</b> ( ( PM - PC ) / M ) * 1000000</small>
                                    </p>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_solidos_totales_1_<?= $id_f ?>" id="frm_solidos_totales_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_solidos_totales_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 25)">
                                            <label for="frm_solidos_totales_1_<?= $id_f ?>">PC: Peso capsula vacio</label>
                                            <span id="frm_solidos_totales_1_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_solidos_totales_2_<?= $id_f ?>" id="frm_solidos_totales_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_solidos_totales_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 25)">
                                            <label for="frm_solidos_totales_2_<?= $id_f ?>">PM: peso capsula muestra</label>
                                            <span id="frm_solidos_totales_2_<?= $id_f ?>"></span>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <input type="text" name="frm_solidos_totales_3_<?= $id_f ?>" id="frm_solidos_totales_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_solidos_totales_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 25)">
                                            <label for="frm_solidos_totales_3_<?= $id_f ?>">M: Muestra en mL</label>
                                            <span id="frm_solidos_totales_3_<?= $id_f ?>"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="input-field col col s12 l4">
                                            <select name="frm_solidos_totales_equipo_<?= $id_f ?>"
                                                id="frm_solidos_totales_equipo_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_solidos_totales_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 25)"
                                                <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                <option value="0">Seleccione equipo</option>
                                                <?php foreach ($equipos as $key => $equipo): ?>
                                                    <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <label>Codigo equipo</label>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>SOLIDO TOTALES</b></p>
                                            <b id="campo_respuesta_agua_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> </b>
                                        </div>
                                        
                                        <div class="col col s12 l4">
                                            <p><b>IRCA SOLIDOS TOTALES</b></p>
                                            <b id="campo_respuesta_irca_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_irca ?> </b>
                                        </div>
                                        
                                    </div>
                                </td>
            
                            </tr>
                            <?php endif ?>  
                            
                            
                            
                            
                            <?php /* IRCA*/
                                $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 26, 0, $id_f);// 26 IRCA
                                //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                                //echo '--->'.$salida[0]->id_parametro;
                                //echo '--->'.$certificado->id_muestra_detalle;
                                 
                               if (!empty($salida[0])): ?>
                                <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                                
                                
                                //$salida[0]->id_ensayo_vs_muestra
                                ?>
                                    <tr>
                                        <td>
                                            <p class="center-align">
                                                <b>IRCA</b>
                                                <br>
                                                    <small> <?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b> Calculo Matematico</small>
                                            </p>
                                            <div class="row">
                                                <div class="input-field col s12 l6">
                                                   <!--<select id="campo_muestra_redondeo_irca" name="campo_muestra_redondeo_irca"
                                                        onchange="js_calcula_independiente('<?= $certificado->id_muestra_detalle ?>', 'frm_irca', this.value, 'calcula_IRCA')">
                                                        <option>Sin seleccionar</option>
                                                        <option value="0">Número entero</option>
                                                        <option value="1">Número con 1 decimal</option>
                                                        <option value="2">Número con 2 decimales</option>
                                                        <option value="2">Número con 3 decimales</option>
                                                    </select>
                                                    <label for="campo_muestra_redondeo_frm_irca">Seleccione cifra de redondeo :</label>
                                                    -->
                                                    <input type="button" 
                                                        id="campo_muestra_redondeo_irca_<?= $id_f ?>" 
                                                        name="campo_muestra_redondeo_irca_<?= $id_f ?>"
                                                        onclick="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'frm_irca_<?= $id_f ?>', 1, 'calcula_IRCA', '<?= $salida[0]->id_ensayo_vs_muestra ?>')"
                                                        value="Calcular IRCA">
                                                    
                                                    
                                                </div>
                                                <div class="col s12 l6 campo_resultado_frm_irca_<?= $id_f ?>">
                                                    <p>IRCA TOTAL</p>
                                                    <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' ' : 'Sin resultado' ?></b>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif ?>
                                    
                                    <!--
                                    <?php foreach (parametros_aguas_fq() as $key => $value): 
                                            //echo '<br>->'.print_r($value);
                                            ?>
                                        <?php $pinta = pinta_parametro_agua($value[0], $certificado->id_muestra_detalle, session('user')->usr_rol, $value[1], $value[2], $value[3], $value[4], $value[5]) ?>
                                        <?php if (!empty($pinta)): ?>
                                            <?= $pinta ?>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    
                                    -->
                                    
                                   
                                <?php if (!empty($certificado->otros[$fechaVidaUtil->id])): ?>
                                <tr>
                                    <td>
                                        <p class="center-align">
                                        <b>OTROS ANALISIS</b> <br>
                                        Las formulas que aplican son:<br>
                                        <b>Sin dilución:</b> Calculo directo de Volumen de muestra<br>
                                        <b>Con dilución:</b>  Rf = R * (V / (V + Vd))
                                        </p>
                                    </td>
                                </tr>
                                
                                <?php foreach ($certificado->otros[$fechaVidaUtil->id] as $key => $fila): ?>
                                    <?php
                                    $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $fila->id_ensayo_vs_muestra, 'id_parametro', $fila->id_parametro);
                                    $aux = $fila->id_ensayo_vs_muestra;
                                    $aux_irca_titulo = ($fila->par_irca)?"IRCA limite:".$fila->par_irca : "No aplica IRCA";
                                    ?>  
                                        <tr>
                                            <td>
                                            <p class="center-align">
                                            <b><?=$fila->par_nombre ?></b>
                                            
                                            </p>
                                            <div class="row">
                                                
                                                
                                                <div class="input-field col col s12 l4">
                                                    <select name="frm_tiene_dilucion_<?= $aux ?>"
                                                        id="frm_tiene_dilucion_<?= $aux ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $aux?>',this.value, 'frm_tiene_dilucion_<?= $aux ?>', 'id_equipo', '<?= $fila->id_ensayo_vs_muestra?>', <?= $fila->id_parametro ?>, -1)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                        <option value="0">Seleccion</option>
                                                        <option value="1" <?= 1==$result[0]->id_equipo ?'selected':''; ?>>Sin dilución</option>
                                                        <option value="2" <?= 2==$result[0]->id_equipo ?'selected':''; ?>>Con dilución</option>
                                                    </select>
                                                    <label>Tiene dilución</label>
                                                </div>
                                                
                                                <div class="input-field col s12 l4">
                                                    <input type="text" name="frm_otro_v_<?= $aux ?>" id="frm_otro_v_<?= $aux ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                    onblur="js_cambiar_campos('campo_repuesta_1_<?= $aux ?>',this.value, 'frm_otro_v_<?= $aux ?>', 'result_1', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                    <label for="frm_otro_v_<?= $aux ?>">V: Volumen muestra</label>
                                                    <span id="frm_otro_v_<?= $aux ?>"></span>
                                                </div>
                                                
                                                 <div class="input-field col s12 l4">
                                                    <input type="text" name="frm_otro_vd_<?= $aux ?>" id="frm_otro_vd_<?= $aux ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                    onblur="js_cambiar_campos('campo_repuesta_2_<?= $aux ?>',this.value, 'frm_otro_vd_<?= $aux ?>', 'result_2', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                    <label for="frm_otro_vd_<?= $aux ?>">Vd: Volumen de agua</label>
                                                    <span id="frm_otro_vd_<?= $aux ?>"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                
                                                 <div class="input-field col s12 l4">
                                                    <input type="text" name="frm_otro_r_<?= $aux ?>" id="frm_otro_r_<?= $aux ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                    onblur="js_cambiar_campos('campo_repuesta_3_<?= $aux ?>',this.value, 'frm_otro_r_<?= $aux ?>', 'result_3', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                    <label for="frm_otro_r_<?= $aux ?>">R: resultado equipo</label>
                                                    <span id="frm_otro_r_<?= $aux ?>"></span>
                                                </div>
                                               
                                                
                                                
                                                
                                                <div class="col col s12 l4">
                                                    <p>Resultado final</p>
                                                    <b id="campo_respuesta_agua_<?= $aux ?>"><?= $result[0]->result_8 ?> </b>
                                                </div>
                                                
                                                <div class="col col s12 l4">
                                                    <p><?=$aux_irca_titulo ?> </p>
                                                    <b id="campo_respuesta_irca_<?= $aux ?>"><?= $result[0]->result_irca ?> </b>
                                                </div>
                                                        
                                                
                                            </div>
                                        </td>
                                        </tr>
                                    <?php endforeach ?>
                                    <?php endif ?>
                                    
                                    <!--
                                    <?php if (!empty($otros)): ?>
                                        <tr>
                                            <td>
                                                <p class="center-align">
                                                    <b>OTROS ANALISIS</b>
                                                </p>
                                                <?php $llave = 4 ?>
                                                <?php foreach ($otros as $key => $fila): ?>
                                                    <?php if ($llave == 4): ?>
                                                        <div class="row">
                                                            <?php $llave = 1 ?>
                                                    <?php else: ?>
                                                        <?php $llave++ ?>
                                                    <?php endif ?>
                                                    <div class="input-field col s12 l3">
                                                        <input type="text" name="frm_otro<?= $key ?>" id="frm_otro<?= $key ?>"
                                                        value="<?= $fila->resultado_mensaje ?>"
                                                        onblur="js_cambiar_campos('campo_repuesta_otro_<?= $fila->id_ensayo_vs_muestra ?>', this.value, 'frm_otro<?= $key ?>', 'result_3', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>)"
                                                        <?= disable_frm($fila->resultado_mensaje, session('user')->id) ?>>
                                                        <label for="frm_otro<?= $key ?>"><?= $fila->par_nombre.' - '. $fila->id_parametro ?></label>
                                                        <span id="campo_repuesta_otro_<?= $fila->id_ensayo_vs_muestra ?>"></span>
                                                    </div>
                                                    <?php if ($llave == 4): ?>
                                                        </div>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            </td>
                                        </tr>
                                    <?php endif ?>
                                    
                                   
                                    
                                    <?php $fila = fq_tiene_parametro($certificado->id_muestra_detalle, 243); ?>
                                    <?php if (!empty($fila[0])): ?>
                                        <?php $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $fila[0]->id_ensayo_vs_muestra, 'id_parametro', 243); ?>
                                        <tr>
                                            <td>
                                                <p class="center-align">
                                                    <b>Resultados IRCA</b>
                                                </p>
                                                <div class="row">
                                                    <div class="input-field col s12 l6">
                                                        <input type="text" name="frm_micro" id="frm_micro"
                                                        value="<?= $result[0]->result_1 ?>"
                                                        onblur="js_cambiar_campos('campo_respuesta_micro_<?= $fila[0]->id_ensayo_vs_muestra ?>', this.value, 'frm_micro<?= $key ?>', 'result_1', '<?= $fila[0]->id_ensayo_vs_muestra ?>', 243)"
                                                        <?= disable_frm($result->result_1, session('user')->id) ?>>
                                                        <label for="frm_micro">Resultado microbiologico</label>
                                                        <span id="campo_respuesta_micro_<?= $fila->id_ensayo_vs_muestra ?>"></span>
                                                    </div>
                                                    <div class="col s12 l6">
                                                        <p class="center-align">
                                                            <b>Resultado IRCA</b>
                                                        </p>
                                                        <small id="campo_resultado_irca"><?= $result->result_irca ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif ?>
                                    -->
                                
                            </tbody>
                </table>
            <?php endforeach ?>
        </form>
    <?php else: ?>
        <?php if (empty($validate)): ?>
            <h5 class="center-align">No se encontr&oacute; ningun resultado de FA o FM </h5>
            <hr>
        <?php endif ?>
    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const downloadAux = () => download(<?= json_encode($analisis) ?>, 1);
    </script>

<?= view('layouts/footer') ?>
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'funciones.js']) ?>"></script>
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'resultadosAGFQ.js']) ?>"></script>

