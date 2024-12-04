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
                                        Registro de muestras
                                    </h2>
                                    <hr>
<form method="POST" action="<?= base_url(['funcionario', 'resultados', 'analisis']) ?>" id="resultados_download" target="_blank">
    <input type="hidden" name="date_download" id="date_download">
    <input type="hidden" name="tipo_analisis" id="tipo_analisis">
    <input type="hidden" name="type" id="type">
</form>
<div class="row">
    <form class="col s12" method="POST" autocomplete="off" action="<?= base_url(['funcionario', 'resultados', 'ingreso']) ?>">
        <div class="row">
            <div class="input-field col s12 l4">
                <input id="frm_codigo_busca" name="frm_codigo_busca" type="text" class="validate">
                <label for="frm_codigo_busca">Código</label>    
            </div>
            <div class="input-field col s12 l4">
                <input id="frm_dia_listar" name="frm_dia_listar" type="text" class="validate">
                <label for="frm_dia_listar">Dia</label>
            </div>
            <div class="input-field col s12 l4">
                <select name="frm_tipo_analisis">
                    <option value="1246">Todos</option>
                    <?php foreach ($analisis as $key => $value): ?>
                        <?php if($value->id_muestra_tipo_analsis != 3 and $value->id_muestra_tipo_analsis != 5): // se excluyen los FQ?>
                            <option value="<?= $value->id_muestra_tipo_analsis ?>"><?= $value->mue_nombre ?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
                <label>Tipo analisis</label>
            </div>
            <div class="input-field col s12 l6 centrar_button">
                <button type="button" class="btn gradient-45deg-blue-deep-orange border-round" onclick="downloadAux()">
                <i class="fad fa-file-pdf"></i> Descargar hoja de trabajo
                </button>
            </div>
            <div class="input-field col s12 l6 centrar_button">
                <button class="btn gradient-45deg-purple-deep-orange border-round" id="btn-buscar-muestra">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($muestras)): ?>
    <hr>
    <ul class="collapsible expandable popout">
        <?php foreach ($muestras as $llave => $muestra): ?>
            <?php $aux_codigo_amc = construye_codigo_amc($muestra->id_muestra_detalle); ?>
            <li class="<?= $llave == 0 ? 'active':'' ?>">
                <div class="collapsible-header">
                    <div class="list-muestras">
                        <div class="col s1 l1">
                            <span><b><?= $llave+1 ?></b></span>
                        </div>
                        <div class="col s11 l3">
                            <span><b>Codigo:</b> <?= $aux_codigo_amc?></span>
                        </div>
                        <div class="col s12 l6">
                            <span><b>Identificación:</b> <?= $muestra->mue_identificacion ?></span>
                        </div>
                        <div class="col s12 l2">
                            <span><b><?= $muestra->certificado_nro ?></b></span>
                        </div>
                    </div>
                </div>
                <div class="collapsible-body">
                    <div class="mensaje_algo mensaje_<?= $muestra->id_muestreo ?>"></div>
                    <div class="table-content">
                        <form action="<?= base_url(['funcionario','resultados','date']) ?>" id="form_date" autocomplete="off">
                        <?php
                                $date_muestro = $muestra->mue_fecha_muestreo;
                                $date_analisis = $muestra->cer_fecha_analisis;
                            
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
                                <?php if(!empty($muestra->fechasUtiles)): ?>
                                    <div class="input-field col s12 l6">
                                        <select name="" id="select_<?= $muestra->id_muestreo_detalle ?>" onchange="cambiar_fecha_vida_util(this.value)">
                                            <?php foreach ($muestra->fechasUtiles as $key => $fechaVidaUtil): ?>
                                                <option id="fecha_<?= $fechaVidaUtil->id ?>" value="<?= $fechaVidaUtil->id ?>">[<?= $fechaVidaUtil->dia ?>] <?= $fechaVidaUtil->fecha ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="select_<?= $muestra->id_muestreo_detalle ?>">Historico resultados</label>
                                    </div>
                                    <div class="input-field col s12 l6">
                                        <a href="javascript:void(0);" onclick="change_date_1407(<?= $muestra->id_muestreo_detalle ?>)" class="btn indigo lighten-5 indigo-text border-round tooltipped" data-position="bottom" data-tooltip="Cambiar fecha"><i class="material-icons">update</i></a>
                                    </div>
                                <?php else: ?>
                                    <div class="input-field col s12 l6">
                                      <input id="date_analisis" type="date" class="validate" value="<?= $date ?>" onblur="change_date(`<?= $muestra->id_certificacion ?>`, this.value)">
                                      <label for="date_analisis">Fecha análisis</label>
                                    </div>
                                <?php endif ?>
                            </div>
                        </form>
                        <form action="<?= base_url(['funcionario','resultados','ingreso','resultado']) ?>" id="form_resultado" autocomplete="off">
                            
                            
                        <table class="centered striped highlight ingreso_muestras">
                            <thead>
                                <tr>
                                    <?php foreach($muestra->parametros as $key => $parametro): ?>
                                        <th><?= $parametro->parametro ?><br>(<?= $parametro->tecnica ?>)  </th>
                                    <?php endforeach ?>
                                </tr>
                                
                            </thead>

    <tbody>
        <?php if(empty($muestra->fechasUtiles)) $muestra->ensayo_vs_muestras[0] = $muestra->ensayo_vs_muestras; ?>
            <?php foreach($muestra->ensayo_vs_muestras as $key_aux => $ensayo_vs_muestras): ?>
                <tr id="fecha_util_<?= $muestra->fechasUtiles[$key_aux]->id?>" class="fechas_util_parametro" <?= $key_aux > 0 ? 'style="display: none"': '' ?> >
                    <?php foreach($ensayo_vs_muestras as $key => $ensayo_vs_muestra): ?>
                        
                        <input type="hidden" id="id_detalle_muestreo_<?= $ensayo_vs_muestra->id_ensayo_vs_muestra ?>" value="<?= $muestra->id_muestreo_detalle ?>">
                            <?php
                                //ROLES
                                // 1 Super Admin
                                // 2 Gerencia
                                // 3 Director T��cnico
                                // 4 Lider de microbiologia
                                // 5 Lider de fisiquicoquimica
                                // 6 Cordinador de muestreo y emisi��n de resultado
                                // 7 T��cnico de muestreo
                            
                                $aux_id_ensayo_vs_muestra = $ensayo_vs_muestra->id_ensayo_vs_muestra;
                                $aux_cambiar_campos = 'onblur="js_cambiar_campos(`campo_repuesta_'.$aux_id_ensayo_vs_muestra.'`,this.value, `frm_resultado'.$aux_id_ensayo_vs_muestra.'`, `resultado_analisis`, '.$aux_id_ensayo_vs_muestra.', '.$muestra->parametros[$key]->id_tecnica.', '.$muestra->id_tipo_analisis.')"' ;
                                $aux_mensaje_tecnica_7_y_texto = ($muestra->parametros[$key]->id_tecnica==7 || preg_match('/usen/',$muestra->ensayos[$key]->med_valor_min) || preg_match('/usen/',$muestra->ensayos[$key]->med_valor_max) )?formatea_valor_min_max($muestra->ensayos[$key]->med_valor_min).'-'.formatea_valor_min_max($muestra->ensayos[$key]->med_valor_max) :'';
                                $aux_max = is_numeric(formatea_valor_min_max($muestra->ensayos[$key]->med_valor_max)) ? 0 : $muestra->ensayos[$key]->med_valor_max;
                                $aux_min = is_numeric(formatea_valor_min_max($muestra->ensayos[$key]->med_valor_min)) ? 0 : $muestra->ensayos[$key]->med_valor_min;
                                $aux_mensaje_tip = 'onclick="js_muestra_tip('.$muestra->id_muestreo.', `'.$aux_codigo_amc.'`, `'.$muestra->parametros[$key]->parametro.'`, `'.$muestra->mue_identificacion.'`, `'.$muestra->pro_nombre.'`, 0, `'.$aux_min.'`, `'.$aux_max.'`)" ' ;
                                
                                $aux_cambiar_campos2 = 'onblur="js_cambiar_campos(`campo_repuesta_'.$aux_id_ensayo_vs_muestra.'\'`,this.value, `frm_resultado2'.$aux_id_ensayo_vs_muestra.'`, `resultado_analisis2`, '.$aux_id_ensayo_vs_muestra.', '.$muestra->parametros[$key]->id_tecnica.','.$muestra->id_tipo_analisis.')" ' ;
                                
                                if($ensayo_vs_muestra->resultado_analisis2){
                                    $aux_cambiar_campos2 = (session('user')->usr_rol == 1 || session('user')->usr_rol == 2 || session('user')->usr_rol == 3) ? $aux_cambiar_campos2.' class="valid" ':'disabled class="valid"  ';//|| $user_rol_id==4
                                }
                                $aux_value2 = $ensayo_vs_muestra->resultado_analisis2;
                                $aux_input2 = '<input type="text" name="frm_resultado2'.$aux_id_ensayo_vs_muestra.'" id="frm_resultado2'.$aux_id_ensayo_vs_muestra.'" '.$aux_cambiar_campos2.' value="'.$aux_value2.'">';
                                
                                if(!$ensayo_vs_muestra->resultado_analisis  ){// || 
                                    if($ensayo_vs_muestra->resultado_analisis == '0' ){
                                        $aux_cambiar_campos = (session('user')->usr_rol == 1 || session('user')->usr_rol == 2 || session('user')->usr_rol == 3) ? $aux_cambiar_campos.' class="valid"':'disabled class="valid"';
                                        $aux_value          =   $ensayo_vs_muestra->resultado_analisis;
                                    }else{
                                        $aux_value          =   '';
                                         
                                        if($muestra->parametros[$key]->id_tecnica <> 80){ //ajustar con las demas tecnicas que aplique
                                        
                                            $aux_input2         = '';    
                                        }
                                        
                                    }                                                
                                }else{
                                    $aux_cambiar_campos = (session('user')->usr_rol == 1 || session('user')->usr_rol == 2 || session('user')->usr_rol == 3) ? $aux_cambiar_campos.' class="valid"  ':'disabled class="valid"  ';
                                    $aux_value = $ensayo_vs_muestra->resultado_analisis;
                                }
                                $aux_div_rta = '<div id="campo_respuesta_'.$aux_id_ensayo_vs_muestra.'"></div>';
                                $aux_mensaje_respuesta='';
                                
                                if(isset($ensayo_vs_muestra->resultado_mensaje) ){
                                   if($ensayo_vs_muestra->resultado_mensaje<>'' ){
                                       if($ensayo_vs_muestra->resultado_analisis2=='' ){
                                           
                                           if($muestra->parametros[$key]->id_tecnica <> 80){ //ajustar con las demas tecnicas que aplique
                                            $aux_input2='';      
                                           }
                                            
                                       }                                               
                                       $aux_mensaje_respuesta=$ensayo_vs_muestra->resultado_mensaje;
                                       $aux_ml_existe = explode("Total", $ensayo_vs_muestra->resultado_mensaje);
                                       $aux_ml_existe = trim($aux_ml_existe[1]);
                                       if(!empty($aux_ml_existe)){
                                            $ensayo_vs_muestra->resultado_mensaje =   $aux_ml_existe;
                                       }
                                       $evaluar = evalua_alerta($muestra->ensayos[$key]->med_valor_min, $muestra->ensayos[$key]->med_valor_max , $ensayo_vs_muestra->resultado_mensaje, $muestra->id_tipo_analisis, $aux_id_ensayo_vs_muestra,2);
                                        if(preg_match("/-MAS-/", $evaluar)){//2 para que no genere correos
                                           $aux_input2          = str_replace('valid', 'invalid', $aux_input2);
                                           $aux_cambiar_campos  = str_replace('valid', 'invalid', $aux_cambiar_campos);
                                        }
                                    }
                                }
                                
                                /*
                                
                                id_tecnica = 80 Recuento en placa
                                */
                                $aux_muestra_dilucion = "";
                                //$aux_diluciones  = $muestra->parametros[$key]->id_tecnica; // para quitar
                                
                                $aux_muestra_dilucion = '
                                    <div class="input-field col l12 ">
                                        <div class="prueba">
                                        <select id="id_dilucion_'.$aux_id_ensayo_vs_muestra.'" name="id_dilucion">
                                            ';
                                            foreach ($diluciones as $key2 => $dilucion){
                                                $aux_select = $dilucion->id_dilucion == $ensayo_vs_muestra->id_dilucion ? 'selected':'';
                                                $aux_muestra_dilucion .= '<option '.$aux_select.' value="'.$dilucion->id_dilucion.'">'.$dilucion->nombre.'</option>';
                                            }
                                        $aux_muestra_dilucion .= '</select><label>Dilución  </label>  </div></div>  ';
                                        
                                
                                /*
                                Relacionado con Mohos y levaduras
                                */
                                
                                $mohos      = strripos( strtolower( $muestra->parametros[$key]->parametro_descripcion ), 'mohos' );
                                $levaduras  = strripos(strtolower( $muestra->parametros[$key]->parametro_descripcion ), 'levadura' );
                                $aux_etiqueta_mohos='';
                                
                        
                                if ( ($mohos !== false && $levaduras >1) && (!preg_match("/--r/", $aux_value)) && ($muestra->certificado_nro>1) ){
                                    $aux_ml="Captura de mohos y levaduras";
                                    // modificamos tip, pendiene de valiar el ultimo campo
                                    $aux_mensaje_tip = 'onclick="js_muestra_tip('.$muestra->id_muestreo.', `'.$aux_codigo_amc.'`, `'.$muestra->parametros[$key]->parametro.'`, `'.$muestra->mue_identificacion.'`, `'.$muestra->pro_nombre.'`, `Ingrese el valor de Mohos y levaduras separados por punto y coma (;)`)"';
                                    $aux_input2 = '
                                        <input  type="text" 
                                                name="frm_resultado2'.$aux_id_ensayo_vs_muestra.'" 
                                                id="frm_resultado2'.$aux_id_ensayo_vs_muestra.'" 
                                                '.$aux_cambiar_campos2.' 
                                                value="'.$aux_value2.'">
                                        <label for="frm_resultado2'.$aux_id_ensayo_vs_muestra.'">Levadura</label>
                                    ';
                                    // incluimos una bandera para identificar en la funncion
                                    $aux_cambiar_campos = str_replace( ")", ", 'mohos', document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').options[document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').selectedIndex].value)", $aux_cambiar_campos);
                                    $aux_input2 = str_replace( ")", ", 'levaduras',document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').options[document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').selectedIndex].value)", $aux_input2);
                                    if(preg_match('/invalid/', $aux_cambiar_campos)){
                                        $aux_input2  = str_replace('valid', 'invalid', $aux_input2);
                                    }
                                    
                                    //etiqueta mohos
                                    $aux_etiqueta_mohos = '<label for="frm_resultado'.$aux_id_ensayo_vs_muestra.'">Moho:</label>';
                                
                                    
                                }elseif($muestra->parametros[$key]->id_tecnica == 80){
                                    
                                    
                                        
                                        // incluimos una bandera para identificar en la funcion
                                        $aux_select = '';
                                        $aux_cambiar_campos = str_replace( ")", ", 0, document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').options[document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').selectedIndex].value)", $aux_cambiar_campos); // primer campo mohos el segundo dilucion
                                        $aux_input2 = str_replace( ")", ", 0,document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').options[document.getElementById('id_dilucion_$aux_id_ensayo_vs_muestra').selectedIndex].value)", $aux_input2); // primer campo mohos el segundo dilucion
                                        
                                        //if(preg_match('/invalid/', $aux_cambiar_campos)){
                                        //    $aux_input2  = str_replace('valid', 'invalid', $aux_input2);
                                        //}
                                        $aux_input2 = $aux_input2;
                                
                                }else{
                                    $aux_muestra_dilucion ='';
                                }
                                
                            ?>
                                
                                <td>
                                    <!-- <?= $ensayo_vs_muestra->resultado_mensaje ?> -->
                                    
                                    <?= $aux_muestra_dilucion ?>
                                    <div class="row display-flex justify-content-center align-items-center">
                                        <div class="input-field col l12">
                                            <input  type="text" 
                                                    name="frm_resultado<?= $aux_id_ensayo_vs_muestra ?>" 
                                                    id="frm_resultado<?= $aux_id_ensayo_vs_muestra ?>" 
                                                    <?= $aux_cambiar_campos ?> 
                                                    <?= $aux_mensaje_tip ?> 
                                                    value="<?= $aux_value ?>">
                                                    
                                                    <?= $aux_etiqueta_mohos ?>
                                        </div>
                                        <?php
                                            if(!empty($ensayo_vs_muestra->confirmacion_a)){
                                                $aux_a = explode(':', $ensayo_vs_muestra->confirmacion_a);
                                                $aux_b = explode(':', $ensayo_vs_muestra->confirmacion_b);
                                                $aux_c = explode(':', $ensayo_vs_muestra->confirmacion_c);
                                                $a_1 = $aux_a[0];
                                                $b_1 = $aux_b[0];
                                                $c_1 = $aux_c[0];
                                                $a_2 = $aux_a[1];
                                                $b_2 = $aux_b[1];
                                                $c_2 = $aux_c[1];
                                            }else{
                                                $a_1 = 'false';
                                                $b_1 = 'false';
                                                $c_1 = 'false';
                                                $a_2 = 'false';
                                                $b_2 = 'false';
                                                $c_2 = 'false';
                                            }
                                        ?>
                                        <div class="col s3 display-flex justify-content-center align-items-center">
                                            <a id="confirmation_<?= $aux_id_ensayo_vs_muestra ?>_1" href="javascript:void(0);" onclick="confirmation('frm_resultado', `<?= $aux_id_ensayo_vs_muestra ?>`, 1, <?= $a_1 ?>,<?= $b_1 ?>,<?= $c_1 ?>)" class="light-blue lighten-5 light-blue-text tooltipped" data-position="bottom" data-tooltip="Confirmar"><i class='fa fa-check-circle fa-2x'></i></a>
                                        </div>
                                    </div>
                                    <?php if($aux_input2 != ''): ?>
                                        <div class="row display-flex justify-content-center align-items-center">
                                            <div class="input-field col l12" id="campo_respuesta2_<?= $aux_id_ensayo_vs_muestra ?>">
                                                <?= $aux_input2 ?>
                                            </div>
                                            <div class="col s3 display-flex justify-content-center align-items-center">
                                                <a id="confirmation_<?= $aux_id_ensayo_vs_muestra ?>_2" href="javascript:void(0);" onclick="confirmation('frm_resultado2', `<?= $aux_id_ensayo_vs_muestra?>`, 2, <?= $a_2 ?>,<?= $b_2 ?>,<?= $c_2 ?>)" class="light-blue lighten-5 light-blue-text tooltipped" data-position="bottom" data-tooltip="Confirmar"><i class='fa fa-check-circle fa-2x'></i></a>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <!-- boton guardado 
                                    <div class="input-field col l12" id="campo_boton_guardar_<?= $aux_id_ensayo_vs_muestra ?>">
                                        <input type="button" 
                                            name="frm_bttn_guardar_rta" 
                                            id="frm_bttn_guardar_rta" 
                                            value="Guardar"
                                            onclick="alert('Cambia resultados')" >
                                    </div>
                                    -->
                                    
                                    
                                    <?= $aux_div_rta ?>
                                    <div id="campo_mensajes_<?= $aux_id_ensayo_vs_muestra?>">
                                        <?= $aux_mensaje_respuesta.'<br>'.$evaluar ?>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="btn teal lighten-5 teal-text border-round tooltipped" data-position="bottom" data-tooltip="Guardar" onclick="guardar(<?= $aux_id_ensayo_vs_muestra?>)">
                                        <i class="fa fa-save"></i></a>
                                    </div>
                                </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
    </tbody>
                        </table>
                        </form>
                    </div>
                </div>
            </li>
        <?php endforeach ?> 
    </ul>
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
        const downloadAux = () => download(<?= json_encode($analisis) ?>, 0);
        function date_fechas(){
            return <?= json_encode($muestras) ?>
        }
        
        $(document).ready(function(){
            alert('Hello');
            console.log('1111111')
        })
    </script>

<?= view('layouts/footer') ?>
<script src="<?= base_url() ?>/assets/js/funcionarios/funciones.js"></script>
<script src="<?= base_url(['assets', 'js', 'funcionarios', 'resultados.js']) ?>"></script>

