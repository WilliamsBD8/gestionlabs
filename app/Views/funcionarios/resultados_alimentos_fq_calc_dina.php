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
                                        Registro de Muestras FisicoQuímicas <?= $certificado ? $certificado->id_codigo_amc:'' ?>
                                    </h2>
                                    <hr>
<form method="POST" action="<?= base_url(['funcionario', 'resultados', 'analisis']) ?>" id="resultados_download" target="_blank">
    <input type="hidden" name="date_download" id="date_download">
    <input type="hidden" name="tipo_analisis" id="tipo_analisis">
    <input type="hidden" name="type" id="type">
</form>
<div class="row">
    <form class="col s12" method="POST" action="<?= base_url(['funcionario', 'resultados', 'alimentos2']) ?>">
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
        <?php $factores = procesar_registro_fetch('factor_fq', 'estado', 'Activo') ?>
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
                <form id="form_cambia_campos" method="POST" action="<?= base_url(['funcionario', 'resultados', 'alimentos2', 'cambiar', 'fq']) ?>">
                    <?php foreach($certificado->fechasUtiles as $key_fecha => $fechaVidaUtil): ?>
                        <?php $id_f = $fechaVidaUtil->id ?>
                        <table class="striped centered table_resultados" id="table_<?= $fechaVidaUtil->id ?>" <?= $key_fecha == 0 ? '' : 'style="display:none"' ?>>
    
                            <tbody>
                             <?php
                            
                            $campo_retorno  = 'em.id_ensayo_vs_muestra
                                                ,pa.id_parametro
                                                ,pa.id_calculo
                                                ,pa.par_nombre
                                                ,cfq.descripcion
                                                ,cfq.formula
                                                ,cfq.formula_sistema
                                                ,cfq.campo_resultado_1';
                                                
                            $tabla          = "ensayo_vs_muestra em 
                                                INNER JOIN ensayo en        ON em.id_ensayo=en.id_ensayo 
                                                INNER JOIN parametro pa     ON pa.id_parametro = en.id_parametro 
                                                INNER JOIN calculos_fq cfq  ON cfq.id_calculo = pa.id_calculo ";
                                                
                            $predicado      = "WHERE em.id_muestra=36 AND pa.id_calculo <>1";
                            
                            $salida_formulados = buscaRegistro($tabla,$predicado,$campo_retorno);
                            //exit();
                            foreach ($salida_formulados as $key1 => $parametro1):
                            ?>    
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                            <b><?=$parametro1->descripcion; ?></b>
                                            <br>
                                            <small><?=$parametro1->par_nombre; ?> || <b>FORMULA:</b> <?=$parametro1->formula; ?>  </small>
                                            <?=$parametro1->formula_sistema?>
                                        </p>
                                        <div class="row">
                                        <?php
                                        for($i=1;$i<=7;$i++){
                                            $porciones1 = explode("R".$i, strtoupper($parametro1->formula_sistema));
                                            if(isset($porciones1[1])){// 1.
                                            
                                                if($i==1){
                                                    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $parametro1->id_ensayo_vs_muestra, 'id_parametro', $parametro1->id_parametro);
                                        	        ?> 
                                        	        
                                        	        <div class="input-field col s12 l4">
                                                        <input type="text" 
                                                            name="frm_campo_1_<?= $id_f ?>" 
                                                            id="frm_campo_1_<?= $id_f ?>" 
                                                            value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                            onblur="js_cambiar_campos('campo_repuesta_1_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_1_<?= $id_f ?>', 'result_1', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                                            >
                                                        <label for="frm_campo_1_<?= $id_f ?>"> <?=$parametro1->campo_resultado_1 ?> </label>
                                                        <span id="frm_campo_1_<?= $id_f ?>"></span>
                                                    </div>
                                                    
                                        	        <?php
                                        	    }elseif($i==2){
                                        	        ?> 
                                        	        Pinta campo2
                                        	        <?php
                                        	    }elseif($i==3){
                                        	        ?> 
                                        	        Pinta campo3
                                        	        <?php
                                        	    }elseif($i==4){
                                        	        ?> 
                                        	        Pinta campo4
                                        	        <?php
                                        	    }elseif($i==5){
                                        	        ?> 
                                        	        Pinta campo5
                                        	        <?php
                                        	    }elseif($i==6){
                                        	        ?> 
                                        	        Pinta campo6
                                        	        <?php
                                        	    }elseif($i==7){
                                        	        ?> 
                                        	        Pinta campo7
                                        	        <?php
                                        	    }
                                            }
                                        }
                                        ?>
                                        </div>
                                    </td>
                                </tr>
                            
                            <?php    
                            endforeach    
                            
                            ?>
                            
                            
                            
                            <?php /* HUMEDAD */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 2, 0, $id_f);// 2 humedad
                            // echo '<br><br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            // echo '<br>--->'.$salida[0]->id_parametro;
                            // echo '<br>--->'.$certificado->id_muestra_detalle;
                            /*
                            select em.id_ensayo_vs_muestra, pa.id_parametro, pa.id_calculo, pa.par_nombre 
from ensayo_vs_muestra em inner join ensayo en on em.id_ensayo=en.id_ensayo 
INNER JOIN parametro pa on pa.id_parametro = en.id_parametro where em.id_muestra=36
*/
                            
                            if (!empty($salida[0])): ?>
                             <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                       
                               
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_humedad_1_<?= $id_f ?>" id="frm_humedad_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 2)">
                                                <label for="frm_humedad_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_humedad_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_humedad_2_<?= $id_f ?>" id="frm_humedad_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 2)">
                                                <label for="frm_humedad_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_humedad_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_humedad_3_<?= $id_f ?>" id="frm_humedad_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 2)">
                                                <label for="frm_humedad_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_humedad_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_humedad_4_<?= $id_f ?>" id="frm_humedad_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 2)">
                                                <label for="frm_humedad_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_humedad_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_humedad_5_<?= $id_f ?>" id="frm_humedad_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 2)">
                                                <label for="frm_humedad_5_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_humedad_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            
                                            <div class="input-field col s12 l4">
                                                <select name="frm_humedad_equipo_<?= $id_f ?>"
                                                        id="frm_humedad_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_humedad_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 2)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <?php /* SOLIDOS TOTALES */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 3, 0, $id_f);// 3 Solidos totales
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_soli_tota_1_<?= $id_f ?>" id="frm_soli_tota_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 3)">
                                                <label for="frm_soli_tota_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_soli_tota_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_soli_tota_2_<?= $id_f ?>" id="frm_soli_tota_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 3)">
                                                <label for="frm_soli_tota_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_soli_tota_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_soli_tota_3_<?= $id_f ?>" id="frm_soli_tota_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 3)">
                                                <label for="frm_soli_tota_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_soli_tota_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_soli_tota_4_<?= $id_f ?>" id="frm_soli_tota_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 3)">
                                                <label for="frm_soli_tota_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_soli_tota_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_soli_tota_5_<?= $id_f ?>" id="frm_soli_tota_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 3)">
                                                <label for="frm_soli_tota_5_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_soli_tota_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_soli_tota_equipo_<?= $id_f ?>"
                                                        id="frm_soli_tota_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_soli_tota_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 3)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                             <?php /* CENIZA */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 4, 0, $fechaVidaUtil->id);// 4 ceniza
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_ceniza_1_<?= $id_f ?>" id="frm_ceniza_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 4)">
                                                <label for="frm_ceniza_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_ceniza_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_ceniza_2_<?= $id_f ?>" id="frm_ceniza_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 4)">
                                                <label for="frm_ceniza_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_ceniza_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_ceniza_3_<?= $id_f ?>" id="frm_ceniza_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 4)">
                                                <label for="frm_ceniza_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_ceniza_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_ceniza_4_<?= $id_f ?>" id="frm_ceniza_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 4)">
                                                <label for="frm_ceniza_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_ceniza_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_ceniza_5_<?= $id_f ?>" id="frm_ceniza_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 4)">
                                                <label for="frm_ceniza_5_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_ceniza_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_ceniza_equipo_<?= $id_f ?>"
                                                        id="frm_ceniza_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_ceniza_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 4)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                            <?php /* GRASA */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 5, 0, $id_f);// 5 grasa
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_grasa_1_<?= $id_f ?>" id="frm_grasa_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 5)">
                                                <label for="frm_grasa_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_grasa_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_grasa_2_<?= $id_f ?>" id="frm_grasa_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 5)">
                                                <label for="frm_grasa_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_grasa_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_grasa_3_<?= $id_f ?>" id="frm_grasa_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 5)">
                                                <label for="frm_grasa_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_grasa_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_grasa_4_<?= $id_f ?>" id="frm_grasa_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 5)">
                                                <label for="frm_grasa_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_grasa_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_grasa_5_<?= $id_f ?>" id="frm_grasa_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 5)">
                                                <label for="frm_grasa_5_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_grasa_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_grasa_equipo_<?= $id_f ?>"
                                                        id="frm_grasa_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_grasa_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 4)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                            <?php /* Proteina */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 6, 0, $id_f);// 6 proteina
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_proteina_1_<?= $id_f ?>" id="frm_proteina_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 6)">
                                                <label for="frm_proteina_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_proteina_1"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_proteina_2_<?= $id_f ?>" id="frm_proteina_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 6)">
                                                <label for="frm_proteina_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_proteina_2"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_proteina_3_<?= $id_f ?>" id="frm_proteina_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 6)">
                                                <label for="frm_proteina_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_proteina_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_proteina_4_<?= $id_f ?>" id="frm_proteina_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 6)">
                                                <label for="frm_proteina_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_proteina_4_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_proteina_factor_<?= $id_f ?>"
                                                        id="frm_proteina_factor_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_factor_<?= $id_f ?>', 'id_factor', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 6)"
                                                        <?= disable_frm($result[0]->id_factor, session('user')->usr_rol) ?>>
                                                    <option>Seleccione factor</option>
                                                        <?php foreach ($factores as $key => $factor): ?>
                                                            <option value="<?= $factor->id_factor ?>" <?= $factor->id_factor==$result[0]->id_factor ?'selected':''; ?>><?= $factor->nombre.' | '.$factor->valor ?></option>
                                                        <?php endforeach ?>
                                                </select>
                                                <label><?=$result_c[0]->campo_factor ?></label>
                                            </div>
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_proteina_equipo_<?= $id_f ?>"
                                                        id="frm_proteina_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_proteina_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 6)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                             <?php /* Fibra cruda */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 7, 0, $id_f);// 7 Fibra cruda
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_fibra_cruda_1_<?= $id_f ?>" id="frm_fibra_cruda_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 7)">
                                                <label for="frm_fibra_cruda_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_fibra_cruda_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_fibra_cruda_2_<?= $id_f ?>" id="frm_fibra_cruda_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 7)">
                                                <label for="frm_fibra_cruda_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_fibra_cruda_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_fibra_cruda_3_<?= $id_f ?>" id="frm_fibra_cruda_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 7)">
                                                <label for="frm_fibra_cruda_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_fibra_cruda_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_fibra_cruda_4_<?= $id_f ?>" id="frm_fibra_cruda_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 7)">
                                                <label for="frm_fibra_cruda_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_fibra_cruda_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_fibra_cruda_5_<?= $id_f ?>" id="frm_fibra_cruda_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 7)">
                                                <label for="frm_fibra_cruda_5_<?= $id_f ?>"><?=$result_c[0]->campo_resultado_5 ?>  </label>
                                                <span id="frm_fibra_cruda_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_fibra_cruda_equipo_<?= $id_f ?>"
                                                        id="frm_fibra_cruda_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_fibra_cruda_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 7)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col col s12">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                                
                            
                             <?php /* Azucares totales */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 8, 0, $id_f);// 8 Azucares totales
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_azucares_totales_1_<?= $id_f ?>" id="frm_azucares_totales_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_azucares_totales_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4 ">
                                                <input type="text" name="frm_azucares_totales_2_<?= $id_f ?>" id="frm_azucares_totales_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_azucares_totales_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_azucares_totales_3_<?= $id_f ?>" id="frm_azucares_totales_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_azucares_totales_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_azucares_totales_4_<?= $id_f ?>" id="frm_azucares_totales_4_<?= $id_f ?>" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_4_<?= $id_f ?>', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_4_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_4 ?> </label>
                                                <span id="frm_azucares_totales_4_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_azucares_totales_5_<?= $id_f ?>" id="frm_azucares_totales_5_<?= $id_f ?>" value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_5_<?= $id_f ?>', 'result_5', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_5_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_5 ?> </label>
                                                <span id="frm_azucares_totales_5_<?= $id_f ?>"></span>
                                            </div>
                                            
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_azucares_totales_6_<?= $id_f ?>" id="frm_azucares_totales_6_<?= $id_f ?>" value="<?= $result[0]->result_6 ?>" <?= disable_frm($result[0]->result_6, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_6_<?= $id_f ?>', 'result_6', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 8)">
                                                <label for="frm_azucares_totales_6_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_6 ?> </label>
                                                <span id="frm_azucares_totales_6_<?= $id_f ?>"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            
                                            
                                            <div class="input-field col col s12 l6">
                                                <select name="frm_azucares_totales_equipo_<?= $id_f ?>"
                                                        id="frm_azucares_totales_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_azucares_totales_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 8)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                            
                                            <div class="col col s12 l6">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                             <?php /* Acidez */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 9, 0, $id_f);// 9 acidez
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_1_<?= $id_f ?>" id="frm_acidez_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 9)">
                                                <label for="frm_acidez_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_acidez_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_2_<?= $id_f ?>" id="frm_acidez_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 9)">
                                                <label for="frm_acidez_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_acidez_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_3_<?= $id_f ?>" id="frm_acidez_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 9)">
                                                <label for="frm_acidez_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_acidez_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--
                                            <div class="input-field col s4 m4 l4">
                                                <input type="text" name="frm_acidez_4" id="frm_acidez_4" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_4', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 9)">
                                                <label for="frm_acidez_4">PM</label>
                                                <span id="frm_acidez_4"></span>
                                            </div>
                                            -->
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_acidez_factor_<?= $id_f ?>"
                                                        id="frm_acidez_factor_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_factor_<?= $id_f ?>', 'id_factor', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 9)"
                                                        <?= disable_frm($result[0]->id_factor, session('user')->usr_rol) ?>>
                                                    <option>Seleccione factor</option>
                                                        <?php foreach ($factores as $key => $factor): ?>
                                                            <option value="<?= $factor->id_factor ?>" <?= $factor->id_factor==$result[0]->id_factor ?'selected':''; ?>><?= $factor->nombre.' | '.$factor->valor ?></option>
                                                        <?php endforeach ?>
                                                </select>
                                                <label> <?=$result_c[0]->campo_factor ?> </label>
                                            </div>
                                            
                                            
                                            
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_acidez_equipo_<?= $id_f ?>"
                                                        id="frm_acidez_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 9)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                            
                                            <div class="col col s12 l4">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                            <?php /* Densidad */
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 10, 0, $id_f);// 10 densidad
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php 
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_densidad_1_<?= $id_f ?>" id="frm_densidad_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_densidad_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 10)">
                                                <label for="frm_densidad_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_densidad_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_densidad_2_<?= $id_f ?>" id="frm_densidad_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_densidad_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 10)">
                                                <label for="frm_densidad_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_densidad_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_densidad_3_<?= $id_f ?>" id="frm_densidad_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_densidad_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 10)">
                                                <label for="frm_densidad_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_densidad_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            
                                            <div class="input-field col col s12 l6">
                                                <select name="frm_densidad_equipo_<?= $id_f ?>"
                                                        id="frm_densidad_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_densidad_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 10)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                            
                                            <div class="col col s12 l6">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                            
                             <?php /* Acidez en aceites*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 11, 0, $id_f);// 11 acidez en aceites
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            
                            if (!empty($salida[0])): ?>
                                 <?php
                                $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra, 'id_parametro', $salida[0]->id_parametro);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                                $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida[0]->id_parametro);
                                ?>     
                                <tr>
                                    <td>
                                        
                                        <p class="center-align">
                                                <b><?php echo $result_c[0]->descripcion; ?></b>
                                                <br>
                                                <small><?=$parametro[0]->par_nombre; ?> <b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_aceites_1_<?= $id_f ?>" id="frm_acidez_aceites_1_<?= $id_f ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_1_<?= $id_f ?>', 'result_1', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 11)">
                                                <label for="frm_acidez_aceites_1_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_1 ?> </label>
                                                <span id="frm_acidez_aceites_1_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_aceites_2_<?= $id_f ?>" id="frm_acidez_aceites_2_<?= $id_f ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_2_<?= $id_f ?>', 'result_2', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 11)">
                                                <label for="frm_acidez_aceites_2_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_2 ?> </label>
                                                <span id="frm_acidez_aceites_2_<?= $id_f ?>"></span>
                                            </div>
                                            <div class="input-field col s12 l4">
                                                <input type="text" name="frm_acidez_aceites_3_<?= $id_f ?>" id="frm_acidez_aceites_3_<?= $id_f ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_3_<?= $id_f ?>', 'result_3', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 11)">
                                                <label for="frm_acidez_aceites_3_<?= $id_f ?>"> <?=$result_c[0]->campo_resultado_3 ?> </label>
                                                <span id="frm_acidez_aceites_3_<?= $id_f ?>"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--
                                            <div class="input-field col s4 m4 l4">
                                                <input type="text" name="frm_acidez_aceites_4" id="frm_acidez_aceites_4" value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_4', 'result_4', '<?= $salida[0]->id_ensayo_vs_muestra ?>', <?= $salida[0]->id_parametro ?>, 11)">
                                                <label for="frm_acidez_aceites_4">Mmol</label>
                                                <span id="frm_acidez_aceites_4"></span>
                                            </div>
                                            -->
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_acidez_aceites_factor_<?= $id_f ?>"
                                                        id="frm_acidez_aceites_factor_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_factor_<?= $id_f ?>', 'id_factor', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 11)"
                                                        <?= disable_frm($result[0]->id_factor, session('user')->usr_rol) ?>>
                                                    <option>Seleccione factor</option>
                                                        <?php foreach ($factores as $key => $factor): ?>
                                                            <option value="<?= $factor->id_factor ?>" <?= $factor->id_factor==$result[0]->id_factor ?'selected':''; ?>><?= $factor->nombre.' | '.$factor->valor ?></option>
                                                        <?php endforeach ?>
                                                </select>
                                                <label><?=$result_c[0]->campo_factor ?></label>
                                            </div>
                                            
                                            <div class="input-field col col s12 l4">
                                                <select name="frm_acidez_aceites_equipo_<?= $id_f ?>"
                                                        id="frm_acidez_aceites_equipo_<?= $id_f ?>"
                                                        onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $salida[0]->id_ensayo_vs_muestra?>',this.value, 'frm_acidez_aceites_equipo_<?= $id_f ?>', 'id_equipo', '<?= $salida[0]->id_ensayo_vs_muestra?>', <?= $salida[0]->id_parametro ?>, 11)"
                                                        <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                                    <option value="0">Seleccione equipo</option>
                                                    <?php foreach ($equipos as $key => $equipo): ?>
                                                        <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                                <label>Codigo equipo</label>
                                            </div>
                                            
                                            <div class="col col s12 l4">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b id="campo_repuesta_mensaje_<?= $salida[0]->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?> %</b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <?php /* Solidos lacteos no grasos*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 12, 0, $id_f);// 12 Solidos lacteos no grasos
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            ?>
                            <?php if (!empty($salida[0])): ?>
                            <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_soli_tota_no_gras_<?= $id_f ?>" name="campo_muestra_redondeo_soli_tota_no_gras_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'soli_tota_no_gras_<?= $id_f ?>', this.value, 'calcula_soli_tota_no_graso',12)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_soli_tota_no_gras_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_soli_tota_no_gras_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <?php /* Carbohidratos*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 13, 0, $id_f);// 13 Carboidratos
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                             
                            //$carbohidratos = fq_tiene_parametro($certificado->id_muestra_detalle, 28) ?>
                            <?php if (!empty($salida[0])): ?>
                            <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_carbohidratos_<?= $id_f ?>" name="campo_muestra_redondeo_carbohidratos_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'carbohidratos_<?= $id_f ?>', this.value, 'calcula_carbohidratos',13)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_carbohidratos_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_carbohidratos_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <?php /* calorias*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 14, 0, $id_f);// 14 calorias
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                            ?>
                            
                            <?php if (!empty($salida[0])): ?>
                          <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_calorias_<?= $id_f ?>" name="campo_muestra_redondeo_calorias_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'calorias_<?= $id_f ?>', this.value, 'calcula_calorias',14)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_calorias_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_calorias_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                                
                            <?php /* Grasa en extracto seco*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 15, 0, $id_f);// 15 grasa en exrtacto seco
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                            ?>
                            
                            <?php if (!empty($salida[0])): ?>
                           <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_grasa_seco_<?= $id_f ?>" name="campo_muestra_redondeo_grasa_seco_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'grasa_seco_<?= $id_f ?>', this.value, 'calcula_grasa_seco',15)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_grasa_seco_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_grasa_seco_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <?php /* Humendad mas grasa*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 16, 0, $id_f);// 16  Humendad mas grasa
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                            ?>
                            
                            <?php if (!empty($salida[0])): ?>
                           <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_humedad_grasa_<?= $id_f ?>" name="campo_muestra_redondeo_humedad_grasa_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'humedad_grasa_<?= $id_f ?>', this.value, 'calcula_humedad_grasa',16)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_humedad_grasa_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_humedad_grasa_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            
                            <?php /* Proteina en base seca*/
                            $salida = fq_tiene_calculo($certificado->id_muestra_detalle, 17, 0, $id_f);// 17 Proteina en base seca
                            //echo '<br>--->'.$salida[0]->id_ensayo_vs_muestra;
                            //echo '--->'.$salida[0]->id_parametro;
                            //echo '--->'.$certificado->id_muestra_detalle;
                            ?>
                            
                            <?php if (!empty($salida[0])): ?>
                            <?php 
                                $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $salida[0]->id_ensayo_vs_muestra);
                                $result_c = procesar_registro_fetch('calculos_fq', 'id_calculo', $salida[0]->id_calculo);
                            ?>
                                <tr>
                                    <td>
                                        <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?php echo $result_c[0]->descripcion; ?></b>
                                            <br>
                                                <small>-------><b>FORMULA:</b>  <?php echo $result_c[0]->formula; ?></small>
                                        </p>
                                        <div class="row">
                                            <div class="input-field col s12 l6">
                                                <select id="campo_muestra_redondeo_proteina_seca_<?= $id_f ?>" name="campo_muestra_redondeo_proteina_seca_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $salida[0]->id_ensayo_vs_muestra ?>', 'proteina_seca_<?= $id_f ?>', this.value, 'calcula_proteina_seca',17)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                                </select>
                                                <label for="campo_muestra_redondeo_proteina_seca_<?= $id_f ?>">Seleccione cifra de redondeo :</label>
                                            </div>
                                            <div class="col s12 l6 campo_resultado_proteina_seca_<?= $id_f ?>">
                                                <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                                <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' %' : 'Sin resultado' ?></b>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            
                            <!--
                                <?php if (!empty($otros)): ?>
                                    <tr>
                                        <td>
                                            <p class="center-align">
                                                <b>OTROS ANALISIS</b>
                                            </p>
                                            <?php $llave = 6 ?>
                                            <?php foreach ($otros as $key => $fila): ?>
                                                <?php if ($llave == 6): ?>
                                                    <div class="row">
                                                        <?php $llave = 1 ?>
                                                <?php else: ?>
                                                    <?php $llave++ ?>
                                                <?php endif ?>
                                                <div class="input-field col s12 l2">
                                                    <input type="text" name="frm_otro<?= $key ?>" id="frm_otro<?= $key ?>"
                                                    value="<?= $fila->resultado_mensaje ?>"
                                                    onblur="js_cambiar_campos('campo_repuesta_otro_<?= $fila->id_ensayo_vs_muestra ?>', this.value, 'frm_otro<?= $key ?>', 'result_3', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>)"
                                                    <?= disable_frm($fila->resultado_mensaje, session('user')->id) ?>>
                                                    <label for="frm_otro<?= $key ?>"><?= $fila->par_nombre ?></label>
                                                    <span id="campo_repuesta_otro_<?= $fila->id_ensayo_vs_muestra ?>"></span>
                                                </div>
                                                <?php if ($llave == 6): ?>
                                                    </div>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
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
                                
                                ?>  
                                    <tr>
                                        <td>
                                             <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                       <b><?=$fila->par_nombre ?></b> <br>
                                        
                                        </p>
                                        <div class="row">
                                            
                                            
                                            <div class="input-field col col s4 m4 l4">
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
                                            
                                            <div class="input-field col s4 m4 l4">
                                                <input type="text" name="frm_otro_v_<?= $aux ?>" id="frm_otro_v_<?= $aux ?>" value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_1_<?= $aux ?>',this.value, 'frm_otro_v_<?= $aux ?>', 'result_1', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                <label for="frm_otro_v_<?= $aux ?>">V: Volumen muestra</label>
                                                <span id="frm_otro_v_<?= $aux ?>"></span>
                                            </div>
                                            
                                             <div class="input-field col s4 m4 l4">
                                                <input type="text" name="frm_otro_vd_<?= $aux ?>" id="frm_otro_vd_<?= $aux ?>" value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_2_<?= $aux ?>',this.value, 'frm_otro_vd_<?= $aux ?>', 'result_2', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                <label for="frm_otro_vd_<?= $aux ?>">Vd: Volumen de agua</label>
                                                <span id="frm_otro_vd_<?= $aux ?>"></span>
                                            </div>
                                            
                                             <div class="input-field col s4 m4 l4">
                                                <input type="text" name="frm_otro_r_<?= $aux ?>" id="frm_otro_r_<?= $aux ?>" value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                                onblur="js_cambiar_campos('campo_repuesta_3_<?= $aux ?>',this.value, 'frm_otro_r_<?= $aux ?>', 'result_3', '<?= $fila->id_ensayo_vs_muestra ?>', <?= $fila->id_parametro ?>, -1)">
                                                <label for="frm_otro_r_<?= $aux ?>">R: resultado equipo</label>
                                                <span id="frm_otro_r_<?= $aux ?>"></span>
                                            </div>
                                           
                                            
                                            
                                            
                                            <div class="col col s4 m4 l4">
                                                <p style="background-color:#CCD7D6;  color: #FFFFFF;"> Resultado:</p>
                                                <b id="campo_respuesta_alimento_<?= $aux ?>"><?= $result[0]->result_8 ?> </b>
                                            </div>
                                            
                                           
                                            
                                        </div>
                                    </td>
                                    </tr>
                                <?php endforeach ?>
                                <?php endif ?>
                                
                                
                            </tbody>
                        </table>
                    <?php endforeach ?>
                </form>
            </div>
        </div>
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
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'resultadosALFQ.js']) ?>"></script>

