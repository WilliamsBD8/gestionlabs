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
    <form class="col s12" method="POST" action="<?= base_url(['funcionario', 'resultados', 'alimentos']) ?>">
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
                <form id="form_cambia_campos" method="POST" action="<?= base_url(['funcionario', 'resultados', 'alimentos', 'cambiar', 'fq']) ?>">
                    <?php foreach($certificado->fechasUtiles as $key_fecha => $fechaVidaUtil): ?>
                        <?php $id_f = $fechaVidaUtil->id ?>
                        <table class="striped centered table_resultados" id="table_<?= $fechaVidaUtil->id ?>" <?= $key_fecha == 0 ? '' : 'style="display:none"' ?>>
    
                            <tbody>
                                
                            <tr>
                        <td>
                            <h4>Formulas directas </h4>
                        </td>
                    </tr>
                                
                    <?php
                    // CALCULOS CON FORMULAS NORMALES        
                    
                    //evaluamos sei existe $id_f
                    $id_salida = ($id_f)? "AND em.id_fecha_vida_util ='".$id_f."'" : '';
                     
                     //AND em.id_fecha_vida_util ='".$id_f."'
                      
                      
                    $campo_retorno  = 'em.id_ensayo_vs_muestra
                                                ,pa.id_parametro
                                                ,pa.id_calculo
                                                ,pa.par_nombre
                                                ,cfq.descripcion
                                                ,cfq.formula
                                                ,cfq.formula_sistema
                                                ,cfq.campo_resultado_1
                                                ,cfq.campo_resultado_2
                                                ,cfq.campo_resultado_3
                                                ,cfq.campo_resultado_4
                                                ,cfq.campo_resultado_5
                                                ,cfq.campo_resultado_6
                                                ,cfq.campo_factor';
                                                
                    $tabla          = "ensayo_vs_muestra em 
                                                INNER JOIN ensayo en        ON em.id_ensayo=en.id_ensayo 
                                                INNER JOIN parametro pa     ON pa.id_parametro = en.id_parametro 
                                                INNER JOIN calculos_fq cfq  ON cfq.id_calculo = pa.id_calculo ";
                                                
                    $predicado      = "WHERE em.id_muestra=".$certificado->id_muestra_detalle." 
                                            AND pa.id_calculo <>1 
                                            AND cfq.tipo_formula ='Directa'
                                            ".$id_salida."                      
                                            ORDER BY trim(cfq.descripcion) ";//36 $certificado->otros[0][0]->id_muestra
                            
                    $salida_formulados = buscaRegistro($tabla,$predicado,$campo_retorno);
                            //exit();
                    $indice = 0;        
                    foreach ($salida_formulados as $key1 => $parametro1):
                        
                        //validamos si hay formula en el sistema
                        if($parametro1->formula_sistema){
                            $alert_formula = ' <button type="button" class="btn green white-text btn-small border-round"
                                                    data-toggle="tooltip" data-placement="top" title="'.$parametro1->formula_sistema.'">
                                                    <i class="fas fa-flask"></i> 
                                                </button>';
                        }else{
                            $alert_formula = ' <button type="button" class="btn red white-text btn-xsmall border-round"><i class="fas fa-flask"></i> </button>';
                        }
                        
                    ?>    
                    <tr>
                        <td>
                            <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                    <b><?=$parametro1->descripcion; ?></b>
                                    <br>
                                    <small> 
                                        <?=$parametro1->par_nombre; ?> 
                                        <b >FORMULA:</b> <?=$parametro1->formula; ?>
                                     </small>
                                     <div align="right"> &nbsp;<?php echo $alert_formula; ?></div>
                                    
                            </p>
                            <div class="row">
                            <?php
                                //se presentara si el campo resultado esta diligenciado
                                $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $parametro1->id_ensayo_vs_muestra, 'id_parametro', $parametro1->id_parametro);
                                $indice++;
                                if($parametro1->campo_resultado_1){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_1_<?= $id_f ?>" 
                                            id="frm_campo_1_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_1 ?>" <?= disable_frm($result[0]->result_1, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('',this.value, 'frm_campo_1<?=$indice?>_<?= $id_f ?>', 'result_1', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <?php 
                                        
                                            //echo  'frm_campo_1'.$indice.'_'.$id_f.'' ;
                                            //echo  '<br>id_ensayo_vs_muestra '.$parametro1->id_ensayo_vs_muestra  ;
                                            //echo  '<br>id_parametro '. $parametro1->id_parametro ;
                                            //echo  '<br>id_calculo '.$parametro1->id_calculo ;
                                            
                                            
                                        ?>
                                        
                                        <label for="frm_campo_1<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_1 ?> </label>
                                        <span id="frm_campo_1<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                $indice++;
                                if($parametro1->campo_resultado_2){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_2_<?= $id_f ?>" 
                                            id="frm_campo_2_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_2 ?>" <?= disable_frm($result[0]->result_2, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_2_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_2<?=$indice?>_<?= $id_f ?>', 'result_2', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <label for="frm_campo_2<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_2 ?> </label>
                                        <span id="frm_campo_2<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                $indice++;
                                if($parametro1->campo_resultado_3){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_3_<?= $id_f ?>" 
                                            id="frm_campo_3_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_3 ?>" <?= disable_frm($result[0]->result_3, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_3_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_3<?=$indice?>_<?= $id_f ?>', 'result_3', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <label for="frm_campo_3<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_3 ?> </label>
                                        <span id="frm_campo_3<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                $indice++;
                                if($parametro1->campo_resultado_4){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_4_<?= $id_f ?>" 
                                            id="frm_campo_4_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_4 ?>" <?= disable_frm($result[0]->result_4, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_4_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_4<?=$indice?>_<?= $id_f ?>', 'result_4', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <label for="frm_campo_4<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_4 ?> </label>
                                        <span id="frm_campo_4<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                $indice++;
                                if($parametro1->campo_resultado_5){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_5_<?= $id_f ?>" 
                                            id="frm_campo_5_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_5 ?>" <?= disable_frm($result[0]->result_5, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_5_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_5<?=$indice?>_<?= $id_f ?>', 'result_5', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <label for="frm_campo_5<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_5 ?> </label>
                                        <span id="frm_campo_5<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                $indice++;
                                if($parametro1->campo_resultado_6){
                                ?>
                                    <div class="input-field col s12 l4">
                                        <input type="text" 
                                            name="frm_campo_6_<?= $id_f ?>" 
                                            id="frm_campo_6_<?= $id_f ?>" 
                                            value="<?= $result[0]->result_6 ?>" <?= disable_frm($result[0]->result_6, session('user')->usr_rol) ?>
                                            onblur="js_cambiar_campos('campo_repuesta_6_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_6<?=$indice?>_<?= $id_f ?>', 'result_6', '<?= $parametro1->id_ensayo_vs_muestra ?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                        >
                                        <label for="frm_campo_6<?=$indice?>_<?= $id_f ?>"> <?=$parametro1->campo_resultado_6 ?> </label>
                                        <span id="frm_campo_6<?=$indice?>_<?= $id_f ?>"></span>
                                    </div>
                                    
                                <?php
                                }
                                
                                 $indice++;
                                if($parametro1->campo_factor){
                                ?>
                                    <div class="input-field col col s12 l4">
                                        <select name="frm_campo_factor_<?= $id_f ?>"
                                                id="frm_campo_factor_<?= $id_f ?>"
                                                onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_factor_<?=$indice?>_<?= $id_f ?>', 'id_factor', '<?= $parametro1->id_ensayo_vs_muestra?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                                <?= disable_frm($result[0]->id_factor, session('user')->usr_rol) ?>>
                                                <option>Seleccione factor</option>
                                                <?php foreach ($factores as $key => $factor): ?>
                                                        <option value="<?= $factor->id_factor ?>" <?= $factor->id_factor==$result[0]->id_factor ?'selected':''; ?>><?= $factor->nombre.' | '.$factor->valor ?></option>
                                                <?php endforeach ?>
                                        </select>
                                        <label><?=$parametro1->campo_factor ?></label>
                                    </div>
                                
                                <?php
                                }
                                ?>
                                </div>
                                <div class="row">  
                                 <div class="input-field col col s12 l6">
                                    <select name="frm_campo_equipo_<?= $id_f ?>"
                                            id="frm_campo_equipo_<?= $id_f ?>"
                                            onchange="js_cambiar_campos('campo_repuesta_equipo_<?= $parametro1->id_ensayo_vs_muestra?>',this.value, 'frm_campo_equipo_<?= $id_f ?>', 'id_equipo', '<?= $parametro1->id_ensayo_vs_muestra?>', <?= $parametro1->id_parametro ?>, <?= $parametro1->id_calculo ?>)"
                                            <?= disable_frm($result[0]->id_equipo, session('user')->usr_rol) ?>>
                                            <option value="0">Seleccione equipo</option>
                                            <?php foreach ($equipos as $key => $equipo): ?>
                                                <option value="<?= $equipo->id_equipo ?>" <?= $equipo->id_equipo==$result[0]->id_equipo ?'selected':''; ?>><?= $equipo->nombre ?></option>
                                            <?php endforeach ?>
                                    </select>
                                    <label>Codigo equipo</label>
                                </div>
                                <br>            
                                <div class="col col s12 l6">
                                    <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                    <b id="campo_repuesta_mensaje_f_<?= $parametro1->id_ensayo_vs_muestra ?>"><?= $result[0]->result_8 ?></b>
                                    
                                </div>
                                
                            </div><!-- fin div row -->
                        </td>
                    </tr>
                            
                    <?php    
                    endforeach    
                    ?>
                    <tr>
                        <td>
                            <h4>Formulas compuestas</h4>
                        </td>
                    </tr>
                    
                    <?php
                    // CALCULOS CON FORMULAS COMPUESTAS        
                    $campo_retorno  = 'em.id_ensayo_vs_muestra
                                                ,pa.id_parametro
                                                ,pa.id_calculo
                                                ,pa.par_nombre
                                                ,cfq.descripcion
                                                ,cfq.formula
                                                ,cfq.formula_sistema
                                                ,cfq.campo_resultado_1
                                                ,cfq.campo_resultado_2
                                                ,cfq.campo_resultado_3
                                                ,cfq.campo_resultado_4
                                                ,cfq.campo_resultado_5
                                                ,cfq.campo_resultado_6
                                                ,cfq.campo_factor';
                                                
                    $tabla          = "ensayo_vs_muestra em 
                                                INNER JOIN ensayo en        ON em.id_ensayo=en.id_ensayo 
                                                INNER JOIN parametro pa     ON pa.id_parametro = en.id_parametro 
                                                INNER JOIN calculos_fq cfq  ON cfq.id_calculo = pa.id_calculo ";
                                                
                    $predicado      = "WHERE em.id_muestra=".$certificado->id_muestra_detalle." AND pa.id_calculo <>1 AND cfq.tipo_formula ='Compuesta' ORDER BY trim(cfq.descripcion) ";//36 $certificado->otros[0][0]->id_muestra
                            
                    $salida_formulados = buscaRegistro($tabla,$predicado,$campo_retorno);
                            
                           // echo '<br>salida_formulados-->'.$salida_formulados;
                           // echo '<br>salida_formulados-->'.print_r($salida_formulados);
                            //exit();
                    $indice = 0;           
                    foreach ($salida_formulados as $key1 => $parametro1):
                        $result     = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $parametro1->id_ensayo_vs_muestra);
                        
                        //validamos si hay formula en el sistema
                        if($parametro1->formula_sistema){
                            $alert_formula = ' <button type="button" class="btn green white-text btn-small border-round"
                                                    data-toggle="tooltip" data-placement="top" title="'.$parametro1->formula_sistema.'">
                                                    <i class="fas fa-flask"></i> 
                                                </button>';
                        }else{
                            $alert_formula = ' <button type="button" class="btn red white-text btn-xsmall border-round"><i class="fas fa-flask"></i> </button>';
                        }
                        
                         ?>    
                        <tr>
                            <td>
                                <p class="center-align" style="background-color:#B0B1DA;  color: #FFFFFF;">
                                    <b><?=$parametro1->descripcion; ?> </b>
                                    <br>
                                    <small> 
                                        <?=$parametro1->par_nombre; ?> 
                                        <b >FORMULA:</b> <?=$parametro1->formula; ?>
                                     </small>
                                    <div align="right"> &nbsp;<?php echo $alert_formula; ?></div>
                                </p>
                                <div class="row">
                                    <div class="input-field col s12 l6">
                                       <!--
                                        <select id="campo_muestra_redondeo_compuesto_<?= $indice ?>_<?= $id_f ?>" name="campo_muestra_redondeo_compuesto_<?= $indice ?>_<?= $id_f ?>"
                                                    onchange="js_calcula_independiente('<?= $parametro1->id_ensayo_vs_muestra ?>', 'compuesto_<?= $indice ?>_<?= $id_f ?>', this.value, 'calculos_compuestos',<?= $parametro1->id_calculo ?>)">
                                                    <option>Sin seleccionar</option>
                                                    <option value="0">Número entero</option>
                                                    <option value="1">Número con 1 decimal</option>
                                                    <option value="2">Número con 2 decimales</option>
                                                    <option value="2">Número con 3 decimales</option>
                                        </select>
                                         <label for="campo_muestra_redondeo_compuesto_<?= $indice ?>_<?= $id_f ?>">Calcular :</label>
                                         -->
                                        <input 
                                            id="campo_muestra_redondeo_compuesto_<?= $indice ?>_<?= $id_f ?>" 
                                            name="campo_muestra_redondeo_compuesto_<?= $indice ?>_<?= $id_f ?>"
                                            onclick="js_calcula_independiente('<?= $parametro1->id_ensayo_vs_muestra ?>', 'compuesto_<?= $indice ?>_<?= $id_f ?>', 0, 'calculos_compuestos',<?= $parametro1->id_calculo ?>)"
                                            value="Calcular"
                                            type="button"
                                            >
                                            
                                       
                                    </div>
                                    <div class="col s12 l6" id="campo_resultado_compuesto_<?= $indice ?>_<?= $id_f ?>">
                                            <p class="center-align" style="background-color:#CCD7D6;  color: #FFFFFF;">Resultado:</p>
                                            <b><?= $result[0]->resultado_mensaje ? $result[0]->resultado_mensaje.' ' : 'Sin resultado' ?></b>
                                    </div>
                                </div>
                            </td>
                        </tr>
                            
                        <?php    
                        $indice++;      
                        endforeach    
                        ?>
                        
                        <!----------- OTROS ANALISIS --------------------->
                        
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

