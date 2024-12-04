<body style="background: rgb(51, 51, 51); display: flex; justify-content: center;">
    <div style="width: 800px;background: white; padding: 20px 40px;">
        <!--campo de datos de la empresa-->
        <div id="campo_logo" align="right">
            <!-- <img src="/public/assets/img/blank.png" width="300" height="80" /> -->
            <table width="100%" border="1" cellpadding="0" cellspacing="0" >
                <thead>
                    <tr>
                        <td width="170"><img src="assets/img/logo.png" width="130" height="60" /></td>
                        <td><b><h1>Reporte de Ensayo</h1></b></td>
                        <td width="170"><h2 align="center">Versi&oacute;n 03</h2></td>
                    </tr>
                </thead>
            </table>
        </div>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
            <thead>
            <tr>
                <td colspan="4"><h2><?= $aux_mensaje ?> No. <?= $certificado->certificado_nro ?></h2></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><b>CLIENTE<b></td>
                <td><?= $cliente->name.' '.$muestreo->mue_subtitulo ?></td>
                <td><b>PUNTO DE TOMA DE MUESTRA:<b></td>
                <td><?= $muestreo->mue_momento_muestreo?> (momento o procedencia)</td>
            </tr>
            <tr>
                <td><b>DIRIGIDO A <b></td>
                <td><?= $cliente->use_cargo.' '.$cliente->use_nombre_encargado ?></td>
                <td><b>RESPONSABLE DE TOMA DE MUESTRA:<b></td>
                <td><?= $muestreo->mue_entrega_muestra?></td>
            </tr>
            <tr>
                <td><b>DIRECCI&Oacute;N<b></td>
                <td><?= $cliente->use_direccion ?></td>
                <td><b>FECHA DE TOMA MUESTRA/HORA<b></td>
                <td><?= $muestreo->mue_fecha_muestreo?></td>
            </tr>
            <tr>
                <td><b>TELEFAX<b></td>
                <td><?= $fila_cliente->use_telefono.' '.$fila_cliente->use_fax ?></td>
                <td><b>FECHA DE RECEPCION<b></td>
                <td><?= $muestreo->mue_fecha_recepcion ?></td>
            </tr>
            <tr>
                <td><b>EMAIL<b></td>
                <td><?= $cliente->email ?></td>
                <td><b>FECHA DE ANALISIS<b></td>
                <td><?= $fecha_analisis ?></td>
            </tr>
   
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><b>FECHA DE INFORME<b></td>
                <td><?= $aux_fecha_informe ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><b>MÉTODO DE TOMA DE MUESTRA:<b></td>
                <td><?= $fila_detalle_para_tipo_muestreo->mue_tipo_muestreo ?></td>
            </tr>
            </tbody>
        </table>
        <!--Fin de datos de la empresa-->
        
        <!--iDENTIFICACION DE LA MUESTRA-->
        <br/>
        
            <!-- formateo deacuerdo a la plantilla -->
            <?php if($plantilla == 1 || $plantilla == 11 || $plantilla == 111): ?><!-- PLANTILLA STANDAR -->
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="8"><h2 align="center"><i>IDENTIFICACI&Oacute;N DE LA MUESTRA</i></h2></td>
                        </tr>
                    </thead>
                    <tbody class="center">
                        <tr>
                            <td><b>C&oacute;digo</b></td>
                            <td><b>Producto</b></td>
                            <td><b>Momento / temperatura de muestreo</b></td>
                            <td><b>Proveedor/Lote</b></td>
                            <td><b>Fecha de producci&oacute;n</b></td>
                            <td><b>Fecha vencimiento</b></td>
                            <td><b>Condiciones recepci&oacute;n en laboratorio / T &deg;</b></td>
                        </tr>
                        <!-- cantidad de identificaciones por producto -->
                        <?php $analisis_aux = procesar_registro_fetch('certificacion', 'certificado_nro', $certificado->certificado_nro) ?>
                        <?php foreach ($analisis_aux as $key => $analisis): ?>
                            <?php $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $analisis->id_muestreo_detalle); ?>
                            <?php $tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle[0]->id_tipo_analisis); ?>
                                <tr>
                                    <td><?= construye_codigo_amc($detalle[0]->id_muestra_detalle) ?></td>
                                    <td><?= $detalle[0]->mue_identificacion ?></td>
                                    <td><?= $detalle[0]->mue_momento_muestreo.' / '.$detalle[0]->mue_temperatura_muestreo ?> </td>
                                    <td><?= $detalle[0]->mue_procedencia.' / '.$detalle[0]->mue_lote ?></td>
                                    <td><?= $detalle[0]->mue_fecha_produccion ?></td>
                                    <td><?= $detalle[0]->mue_fecha_vencimiento ?></td>
                                    <td><?= $detalle[0]->mue_condiciones_recibe.' / '.$detalle[0]->mue_temperatura_laboratorio ?></td>
                                </tr>
                                <?= $conteo_productos = $key+1 ?>
                        <?php endforeach ?>
                        <?php $conteo_columnas = $conteo_productos+3;?><!-- cantidad de columnas -->
                    </tbody>
                </table>
                <!--FIN IDENTIFICACION DE LA MUESTRA-->

                <!--TABLA DE RESULTADOS-->
                <br/>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="<?= $conteo_columnas ?>"><h2><i>TABLA DE RESULTADOS </i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr  class="center">
                            <td><b>ENSAYO</b></td>
                            <td><b>T&Eacute;CNICA</b></td>
                            <!-- //titulos -->
                            <?php foreach ($analisis_aux as $key => $analisis): ?>
                                <!-- //formateo de detalle -->
                                <?php $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $analisis->id_muestreo_detalle); ?>
                                <td><b><?= $detalle[0]->mue_identificacion ?></b></td>
                                <?php $id_detalle = $detalle[0]->id_muestra_detalle;?><!-- //para sacar posterior mente los datos de norma y producto -->
                            <?php endforeach ?>
                            <!-- //buscamos l anorma y e producto relacionado -->
                            <?php
                                $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                        where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                        and e.id_ensayo=(SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle group by id_muestra)";
                                $query_norma_producto = $db->query($sql_norma_producto)->getResult();
                                $norma_producto = $query_norma_producto[0];
                            ?>
                            <td><b><?= $norma_producto->nor_nombre.' '.$norma_producto->pro_nombre ?></b></td>
                        </tr>
                        
                        <!-- //contenido de resultados -->

                        <?php
                            $sql_ensayos = "SELECT * FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                            where c.id_muestreo_detalle=m.id_muestra_detalle
                                            and m.id_muestra_detalle=e.id_muestra
                                            and e.id_ensayo=p.id_ensayo
                                            and c.certificado_nro=$certificado->certificado_nro group by e.id_ensayo";
                            $ensayos = $db->query($sql_ensayos)->getResult();
                        ?>
                        <?php foreach ($ensayos as $key => $ensayo): ?>
                            <!-- //formateo parametro -->
                            <?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);?>
                            <!-- //evaluamos si exite referncia bibliografica en el ensayo -->
                            <!-- //$aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$fila_parametro->par_descripcion;
                            //evaluamos si existe referencia bibliografica en el ensayo
                            // vemos si tiene fecha en que aplica y luego si la referencia, hecho 9 de dic 2019 -->
                            <?php
                                if($ensayo->fecha_aplica_referencia < $fecha_analisis)
                                    $aux_descripcion_ensayo = ($ensayo->refe_bibl)?$ensayo->refe_bibl:$parametro[0]->par_descripcion;
                                else
                                    $aux_descripcion_ensayo = $parametro[0]->par_descripcion;
                            ?>
                            <!-- //formateo tecnica -->
                            <?php $fila_tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica); ?>
                            <tr class="center">
                                <td class="left"><?= $aux_descripcion_ensayo ?></td>
                                <td><?= $fila_tecnica[0]->nor_nombre ?></td>
                                <?php foreach ($analisis_aux as $key => $analisis): ?>
                                    <!-- /*Ajuste para presentacion de varios resultados en un mismo certificado 22-08-2019*/ -->
                                    <?php $sql_ensayos2 = "SELECT e.resultado_mensaje FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                                where c.id_muestreo_detalle=m.id_muestra_detalle
                                                and m.id_muestra_detalle=e.id_muestra
                                                and e.id_ensayo=p.id_ensayo
                                                and c.id_certificacion=$analisis->id_certificacion and e.id_ensayo=$ensayo->id_ensayo ";//
                                        $query_ensayos2 = $db->query($sql_ensayos2)->getResult();
                                        $ensayos2 = $query_ensayos2[0];
                                    ?>
                                    <!-- //formateo de detalle
                                    //if (!$fila_ensayos2->resultado_mensaje  ){ -->
                                    <?php if (!isset($ensayos2->resultado_mensaje)): ?>
                                        <?php if($ensayos2->resultado_mensaje == '0'): ?>
                                            <td>0</td>
                                        <?php else: ?>
                                            <td>Pendientes</td>
                                        <?php endif ?>                                         
                                    <?php else: ?>
                                        <!-- //$ax=$fila_ensayos->resultado_mensaje; -->
                                        <?php $ensayo->resultado_mensaje = formateo_valores($ensayos2->resultado_mensaje, $form_entrada['frm_form_valo']); ?>
                                        <td><?= $ensayo->resultado_mensaje ?></td>
                                       
                                    <?php endif ?>   
                                <?php endforeach ?>
                                 <!-- //presentacion de rangos de norma
                                 // si existen los 2 rangos se muestran separados de lo contrario se mostrara 1 -->
                                <?php if($ensayo->med_valor_min && $ensayo->med_valor_max): ?>
                                    <td><?= $ensayo->med_valor_min.' -  '.$ensayo->med_valor_max ?></td>
                                <?php else: ?>
                                    <td><?= $ensayo->med_valor_min.' '.$ensayo->med_valor_max ?></td>
                                <?php endif ?>
                                </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php elseif($plantilla == 2): ?> <!-- //PLANTILLA FROTIS -->
                <?php
                    $productos      =   procesar_registro_fetch("certificacion", "certificado_nro", $certificado->certificado_nro);
                    $conteo_productos   =   count($productos);
                    $sql_analisis = "SELECT * FROM ensayo e, parametro p where e.id_parametro=p.id_parametro
                                and e.id_ensayo in (SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=
                                (SELECT max(id_muestreo_detalle) FROM certificacion c where certificado_nro=$certificado->certificado_nro)) order by id_ensayo";
                    $analisis = $db->query($sql_analisis)->getResult();
                    $conteo_columnas = count($analisis);
                    $conteo_columnas = $conteo_columnas+2;//cantidad de columnas
                    $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
                ?>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="3"><h2 align="center"><i>IDENTIFICACI&Oacute;N DE LA MUESTRA</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td><strong>No. Muestras</strong></td>
                            <td><strong>Tipo de Muestras</strong></td>
                            <td><strong>Estado/Area/Funci&oacute;n</strong></td>
                        </tr>
                        <tr class="center">
                            <td><?= $conteo_productos ?></strong></td>
                            <td><?= $detalle[0]->mue_parametro ?> </td>
                            <td><?= $detalle[0]->mue_area ?></td>
                        </tr>
                    </tbody>
                </table>
                <!--FIN IDENTIFICACION DE LA MUESTRA-->

                <!--TABLA DE RESULTADOS-->
                <br/>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="<?= $conteo_columnas ?>"><h2>TABLA DE RESULTADOS</h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr valign=middle class="center">
                            <td width="150"><strong>Identificaci&oacute;n</strong></td>
                            <td width="100"><strong>C&oacute;digo</strong></td>
                            <?php foreach ($analisis as $key => $fila_analisis): ?>
                                <td><strong><?= $fila_analisis->par_descripcion ?></strong></td>
                            <?php endforeach ?>
                        </tr>
                        <?php foreach ($productos as $key => $certificado): ?>
                            <?php
                                $detalle2 = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
                                //formateo tipo de analisis
                                $tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $certificado->id_tipo_analisis);
                                $resultados = procesar_registro_fetch('ensayo_vs_muestra', 'id_muestra', $detalle2[0]->id_muestra_detalle);
                            ?>
                            <tr>
                                <td><?= $detalle2[0]->mue_identificacion ?></td>
                                <td><div align="center"><?= construye_codigo_amc($detalle2[0]->id_muestra_detalle) ?></div></td>
                                <?php foreach ($resultados as $key => $resultado): ?>
                                    <?php if(!$resultado->resultado_mensaje): ?>
                                        <?php if($resultado->resultado_mensaje == '0'): ?>
                                            <td><div align="center">0</div></td>
                                        <?php else: ?>
                                            <td><div align="center">Pendientes</div></td>
                                        <?php endif ?>
                                    <?php else: ?>
                                            <?php $resultado->resultado_mensaje = formateo_valores($resultado->resultado_mensaje, $form_entrada['frm_form_valo']); ?>
                                            
                                        <td><div align="center"><?= $resultado->resultado_mensaje ?></div></td>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </tr>
                            <?php $id_detalle = $detalle2[0]->id_muestra_detalle; ?>
                        <?php endforeach ?>
                        <?php
                            $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                    where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                    and e.id_ensayo=(SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle group by id_muestra)";//$id_detalle
                            $norma_producto = $db->query($sql_norma_producto)->getResult();
                            $sql_norma_parametro = "SELECT * FROM ensayo e, parametro p where e.id_parametro=p.id_parametro
                                                and e.id_ensayo in (SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle) order by id_ensayo";//$id_detalle
                            $norma_parametros = $db->query($sql_norma_parametro)->getResult();
                        ?>
                        <tr class="center">
                            <td colspan="2">
                                <strong><?= $norma_producto[0]->nor_nombre.' '.$norma_producto[0]->pro_nombre ?></strong>
                            </td>
                            <?php foreach ($norma_parametros as $key => $parametro): ?>
                                <?php if($parametro->med_valor_min && $parametro->med_valor_max): ?>
                                    <td><?= $parametro->med_valor_min.' -  '.$parametro->med_valor_max ?></td>
                                <?php else: ?>
                                    <td><?= $parametro->med_valor_min.' '.$parametro->med_valor_max ?></td>
                                <?php endif ?>
                            <?php endforeach ?>
                        </tr>
                    </tbody>
                </table>
            <?php elseif($plantilla == 5): ?>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="8"><h2 align="center"><i>IDENTIFICACI&Oacute;N DE LA MUESTRA</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td width="100"><strong>C&oacute;digo</strong></td>
                            <td width="150"><strong>Producto</strong></td>
                            <td><strong>Lote</strong></td>
                            <td><strong>Fecha elaboraci&oacute;n</strong></td>
                            <td><strong>Fecha vencimiento</strong></td>
                            <td><strong>Condiciones de recepci&oacute;n</strong></td>
                            <td><strong>Nro. muestras analizadas</strong></td>
                        </tr>
                        <?php
                            $analisis = procesar_registro_fetch('certificacion','certificado_nro', $certificado->certificado_nro);
                            $conteo_productos = 0;
                        ?>
                        <?php foreach ($analisis as $key => $fila_analisis): ?>
                            <?php
                                //formateo de detalle
                                $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle);
                                //formateo tipo de analisis
                                $tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle[0]->id_tipo_analisis);
                                $conteo_productos++;
                                $id_detalle = $detalle[0]->id_muestra_detalle;
                            ?>
                            <tr class="center">
                                <td><?= construye_codigo_amc($detalle[0]->id_muestra_detalle) ?></td>
                                <td><?= $detalle[0]->mue_identificacion ?></td>
                                <td><?= $detalle[0]->mue_lote ?></td>
                                <td><?= $detalle[0]->mue_fecha_produccion ?></td>
                                <td><?= $detalle[0]->mue_fecha_vencimiento ?></td>
                                <td><?= $detalle[0]->mue_condiciones_recibe ?></td>
                                <td><?= $detalle[0]->mue_cantidad ?></td>
                            </tr>
                        <?php endforeach ?>
                        <?php $conteo_columnas = $conteo_productos+3+14; ?>
                    </tbody>
                </table>
                <!--FIN IDENTIFICACION DE LA MUESTRA-->

                <!--TABLA DE RESULTADOS-->
                <br/>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="<?= $conteo_columnas ?>"><h2 align="center"><i>TABLA DE RESULTADOS</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td rowspan="2"><strong>ENSAYO</strong></td>
                            <td rowspan="2"><strong>T&Eacute;CNICA</strong></td>
                            <td colspan="14"><strong>DIA</strong></td>
                            <?php
                                $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                and e.id_ensayo=(SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle group by id_muestra)";
                                $norma_producto = $db->query($sql_norma_producto)->getResult();
                            ?>
                            <td rowspan="2"><strong><?= $norma_producto[0]->nor_nombre.' '.$norma_producto[0]->pro_nombre ?></strong></td>
                        </tr>
                        <tr> 
                            <td><strong>1</strong></td>
                            <td><strong>2</strong></td>
                            <td><strong>3</strong></td>
                            <td><strong>4</strong></td>
                            <td><strong>5</strong></td>
                            <td><strong>6</strong></td>
                            <td><strong>7</strong></td>
                            <td><strong>8</strong></td>
                            <td><strong>9</strong></td>
                            <td><strong>10</strong></td>
                            <td><strong>11</strong></td>
                            <td><strong>12</strong></td>
                            <td><strong>13</strong></td>
                            <td><strong>14</strong></td>
                        </tr>
                        <?php
                            $sql_ensayos = "SELECT * FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                        where c.id_muestreo_detalle=m.id_muestra_detalle
                                        and m.id_muestra_detalle=e.id_muestra
                                        and e.id_ensayo=p.id_ensayo
                                        and c.certificado_nro=$certificado->certificado_nro group by e.id_ensayo";
                            $ensayos = $db->query($sql_ensayos)->getResult();
                            $aux_resultado='Satisfactoria';
                        ?>
                        <?php foreach ($ensayos as $key => $ensayo): ?>
                            <!-- //evaluamos si exite referncia bibliografica en el ensayo
                            //$aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$fila_parametro->par_descripcion;
                            //evaluamos si existe referencia bibliografica en el ensayo
                                // vemos si tiene fecha en que aplica y luego si la referencia, hecho 9 de dic 2019 -->
                            <?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);
                                if($ensayo->fecha_aplica_referencia < $fecha_analisis)
                                    $aux_descripcion_ensayo = ($ensayo->refe_bibl)?$ensayo->refe_bibl:$parametro[0]->par_descripcion;
                                else
                                    $aux_descripcion_ensayo = $parametro[0]->par_descripcion;
                                //formateo tecnica
                                $tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica);
                            ?>
                            <tr class="center">
                                <td class="left"><?= $aux_descripcion_ensayo ?> </td>
                                <td><?= $tecnica[0]->nor_nombre ?></td>
                                <?php $ensayo->resultado_mensaje = str_replace(',', '.', $ensayo->resultado_mensaje) ?>
                                <?php if ($ensayo->resultado_mensaje >= 14): ?>
                                    <?php $ensayo->resultado_mensaje = 14 ?>
                                <?php endif ?>
                                <?php for($i=0;$i<$ensayo->resultado_mensaje;$i++): ?>
                                    <td><i class="icon">&#xf00c;</i></td>
                                <?php endfor ?>
                                <?php for($n=$i;$n<14;$n++): ?>
                                    <td>&nbsp;</td>
                                <?php endfor ?>
                                <?php if ($i == 14): ?>
                                    <td><?= $ensayo->resultado_mensaje ?></td> <!-- Satisfactorio -->
                                <?php else: ?>
                                    <td><?= $ensayo->resultado_mensaje ?></td> <!-- No satisfactorio-->
                                    <?php $aux_resultado='No Satisfactoria'; ?>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td colspan="2"><p><strong>RESULTADO</strong></p></td>
                                <td colspan="14"><strong><?= $aux_resultado ?></strong></td>
                                <td >&nbsp;</td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php else: ?>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="8"><h2 align="center"><i>IDENTIFICACI&Oacute;N DE LA MUESTRA</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td width="100"><strong>C&oacute;digo</strong></td>
                            <td width="150"><strong>Producto</strong></td>
                            <td><strong>Momento / temperatura de muestreo</strong></td>
                            <td><strong>Proveedor/Lote</strong></td>
                            <td><strong>Fecha de producci&oacute;n</strong></td>
                            <td><strong>Fecha vencimiento</strong></td>
                            <td><strong>Condiciones recepci&oacute;n en laboratorio / T &deg;</strong></td>
                        </tr>
                        <?php
                            $analisis = procesar_registro_fetch("certificacion","certificado_nro",$certificado->certificado_nro);
                            $conteo_productos = 0;
                        ?>
                        <?php foreach ($analisis as $key => $fila_analisis): ?>
                            <?php
                                //formateo de detalle
                                $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle);
                                //formateo tipo de analisis
                                $tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle[0]->id_tipo_analisis);
                                $conteo_productos++;
                            ?>
                            <tr class="center">
                                <td ><?= construye_codigo_amc($detalle[0]->id_muestra_detalle) ?></td>
                                <td ><?= $detalle[0]->mue_identificacion ?></td>
                                <td ><?= $detalle[0]->mue_momento_muestreo.' / '.$detalle[0]->mue_temperatura_muestreo ?></td>
                                <td ><?= $detalle[0]->mue_procedencia.' / '.$detalle[0]->mue_lote ?></td>
                                <td ><?= $detalle[0]->mue_fecha_produccion ?></td>
                                <td ><?= $detalle[0]->mue_fecha_vencimiento ?></td>
                                <td ><?= $detalle[0]->mue_condiciones_recibe.' / '.$detalle[0]->mue_temperatura_laboratorio ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <?php $conteo_columnas = $conteo_productos+3; ?>
                <!--FIN IDENTIFICACION DE LA MUESTRA-->
                <!--TABLA DE RESULTADOS-->
                <br/>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                            <?php
                                $sql_analisis = "select * from certificacion c,  muestreo_detalle m where c.id_muestreo_detalle=m.id_muestra_detalle 
                                    and certificado_nro=$certificado->certificado_nro and  m.id_tipo_analisis in (1,2) ";
                                $analisis = $db->query($sql_analisis)->getResult();
                                $id_detalle=0;
                                if(empty($analisis))
                                    $conteo_columnas_1 = 2;
                                else
                                    $conteo_columnas_1 = $conteo_columnas;
                            ?>
                        <tr>
                            <td colspan="<?= $conteo_columnas_1 ?>"><h2 align="center"><i>TABLA DE RESULTADOS MICROBIOLOGICOS</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td><strong>ENSAYO</strong></td>
                            <td><strong>T&Eacute;CNICA</strong></td>
                            <?php foreach ($analisis as $key => $fila_analisis): ?>
                                <!-- //formateo de detalle -->
                                <?php
                                    $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle);
                                ?>
                                <td><strong><?= $detalle[0]->mue_identificacion ?></strong></td>
                                <?php $id_detalle = $detalle[0]->id_muestra_detalle; ?><!-- //para sacar posterior mente los datos de norma y producto -->
                            <?php endforeach ?>
                            <?php
                                $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                and e.id_ensayo=(SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle group by id_muestra)";
                                $norma_producto = $db->query($sql_norma_producto)->getResult();
                                $aux_nor_nombre='';
                                $aux_pro_nombre='';
                            ?>
                            <?php if(isset($norma_producto[0]->nor_nombre)): ?>
                                <td><?= $norma_producto[0]->nor_nombre.' '.$norma_producto[0]->pro_nombre ?></strong></td>
                            <?php endif ?>
                        </tr>
                        <?php
                            $sql_ensayos = "SELECT * FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                        where c.id_muestreo_detalle=m.id_muestra_detalle
                                        and m.id_muestra_detalle=e.id_muestra
                                        and e.id_ensayo=p.id_ensayo
                                        and c.certificado_nro=$certificado->certificado_nro AND m.id_tipo_analisis in (1,2) group by e.id_ensayo";
                            $ensayos = $db->query($sql_ensayos)->getResult();
                        ?>
                        <?php foreach ($ensayos as $key => $ensayo): ?>
                            <?php
                                // /formateo parametro
                                $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);
                                //evaluamos si exite referncia bibliografica en el ensayo
                                // $aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$fila_parametro->par_descripcion;
                                //evaluamos si existe referencia bibliografica en el ensayo
                                // vemos si tiene fecha en que aplica y luego si la referencia, hecho 9 de dic 2019
                                if($ensayo->fecha_aplica_referencia < $fecha_analisis)
                                    $aux_descripcion_ensayo = ($ensayo->refe_bibl)?$ensayo->refe_bibl:$parametro[0]->par_descripcion;  
                                else
                                    $aux_descripcion_ensayo = $parametro[0]->par_descripcion;

                                $tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica);
                            ?>
                            <tr class="center">
                                <td class="left"><?= $aux_descripcion_ensayo ?></td>
                                <td><?= $tecnica[0]->nor_nombre ?></td>
                                <?php foreach ($analisis as $key => $fila_analisis): ?>
                                    <?php if (!$ensayo->resultado_mensaje): ?>
                                        <?php if($ensayo->resultado_mensaje == '0'): ?>
                                            <td>0</td>
                                        <?php else: ?>
                                            <td>Pendientes 3</td>
                                        <?php endif ?>                                            
                                    <?php else: ?>
                                         <?php $ensayo->resultado_mensaje = formateo_valores($ensayo->resultado_mensaje, $form_entrada['frm_form_valo']);
                                         ?>
                                         <td><?= $ensayo->resultado_mensaje ?></td>
                                    <?php endif ?>
                                <?php endforeach ?> 
                                <?php if($ensayo->med_valor_min && $ensayo->med_valor_max): ?>
                                    <td><?= $ensayo->med_valor_min.' -  '.$ensayo->med_valor_max ?></td>
                                <?php else: ?>
                                    <td><?= $ensayo->med_valor_min.' '.$ensayo->med_valor_max ?> </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- //RESULTADOS DE ANALISIS FISICOQUIMICOS -->
                <br>
                <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="<?= $conteo_columnas ?>"><h2 align="center"><i>TABLA DE RESULTADOS FISICOQUIMICOS</i></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="center">
                            <td><strong>ENSAYO</strong></td>
                            <td><strong>T&Eacute;CNICA</strong></td>
                            <?php
                                $sql_analisis = "select * from certificacion c,  muestreo_detalle m where c.id_muestreo_detalle=m.id_muestra_detalle
                                    and certificado_nro=$certificado->certificado_nro and  m.id_tipo_analisis in (3,4) ";
                                $analisis = $db->query($sql_analisis)->getResult();
                            ?>
                            <?php if (empty($analisis)): ?>
                                <?php $aux_col = 2 ?>
                            <?php endif ?>
                            <?php foreach ($analisis as $key => $fila_analisis): ?>
                                <?php $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle); ?>
                                <td><strong><?= $detalle[0]->mue_identificacion ?></strong></td>';
                                <?php $id_detalle = $detalle[0]->id_muestra_detalle; ?><!-- //para sacar posterior mente los datos de norma y producto -->
                            <?php endforeach ?>
                            <?php
                                $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                and e.id_ensayo=(SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=$id_detalle group by id_muestra)";
                                $norma_producto = $db->query($sql_norma_producto)->getResult();
                            ?>
                            <td colspan="<?= $aux_col ?>"><strong><?= $norma_producto[0]->nor_nombre.' - '.$norma_producto[0]->pro_nombre ?></strong></td>
                        </tr>
                        <?php
                            $sql_ensayos = "SELECT * FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                        where c.id_muestreo_detalle=m.id_muestra_detalle
                                        and m.id_muestra_detalle=e.id_muestra
                                        and e.id_ensayo=p.id_ensayo
                                        and c.certificado_nro=$certificado->certificado_nro AND m.id_tipo_analisis in (3,4) group by e.id_ensayo";
                            $ensayos = $db->query($sql_ensayos)->getResult();
                        ?>
                        <?php foreach ($ensayos as $key => $fila_ensayos): ?>
                            <?php
                                $parametro = procesar_registro_fetch('parametro', 'id_parametro', $fila_ensayos->id_parametro);
                                //evaluamos si exite referncia bibliografica en el ensayo
                                //$aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$fila_parametro->par_descripcion;
                                //evaluamos si existe referencia bibliografica en el ensayo
                                // vemos si tiene fecha en que aplica y luego si la referencia, hecho 9 de dic 2019
                                    if($fila_ensayos->fecha_aplica_referencia < $fecha_analisis)
                                        $aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$parametro[0]->par_descripcion;
                                    else
                                        $aux_descripcion_ensayo = $parametro[0]->par_descripcion;
                                $tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica);
                            ?>
                            <tr class="center">
                                <td class="left"><?= $aux_descripcion_ensayo ?></td>
                                <td><?= $tecnica[0]->nor_nombre ?></td>
                                <?php foreach ($analisis as $key => $fila_analisis): ?>
                                    <?php
                                        $sql_ensayos2 = "SELECT e.resultado_mensaje FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
                                            where c.id_muestreo_detalle=m.id_muestra_detalle
                                            and m.id_muestra_detalle=e.id_muestra
                                            and e.id_ensayo=p.id_ensayo
                                            and c.id_certificacion=$fila_analisis->id_certificacion and e.id_ensayo=$fila_ensayos->id_ensayo ";//
                                        $ensayos2 = $db->query($sql_ensayos2)->getResult();
                                    ?>
                                    <?php if (!$fila_ensayos->resultado_mensaje): ?>
                                        <?php if($fila_ensayos->resultado_mensaje == '0'): ?>
                                            <td><div align="center">0</div></td>
                                        <?php else: ?>
                                            <td><div align="center">Pendientes</div></td>
                                        <?php endif ?>
                                    <?php else: ?>
                                        <?php $fila_ensayos->resultado_mensaje = formateo_valores($ensayos2[0]->resultado_mensaje, $form_entrada['frm_form_valo']); ?>
                                        <td><?= $fila_ensayos->resultado_mensaje ?></td>
                                    <?php endif ?>
                                <?php endforeach ?>
                                <?php if($fila_ensayos->med_valor_min && $fila_ensayos->med_valor_max): ?>
                                    <td><?= $fila_ensayos->med_valor_min.' -  '.$fila_ensayos->med_valor_max ?></td>';
                                <?php else: ?>
                                    <td><?= $fila_ensayos->med_valor_min.' '.$fila_ensayos->med_valor_max ?></td>';
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
            <?php if ($plantilla == 5): ?>
                <i class="icon">&#xf00c;</i> No present&oacute; crecimiento
            <?php endif ?>
            <!-- //informacion de pie de pagina -->
            <?php $resultado = procesar_registro_fetch('mensaje_resultado', 'id_mensaje', $form_entrada['frm_mensaje_resultado']) ?>
            <?php $resultado_observacion = procesar_registro_fetch('mensaje', 'id_mensaje', $form_entrada['frm_mensaje_observacion']) ?>
            <!-- // firma de sistema -->
            <?php $firma  = procesar_registro_fetch('cms_firma', 'id_firma', $form_entrada['frm_mensaje_firma']); ?>
            <?php $firma1 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_1); ?>
            <?php $firma2 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_2); ?>
            <?php
                $aux_nombre1   = $firma1[0]->nombre;
                $aux_cargo1    = $firma1[0]->cargo;
                $aux_firma     = $firma1[0]->firma;
                $aux_nombre    = $firma2[0]->nombre;
                $aux_cargo     = $firma2[0]->cargo;
            ?>
            <?php if($form_entrada['frm_plantilla'] == 4 || $form_entrada['frm_plantilla'] == 11): ?>
                <div id="campo_logo" align="right"> 
                    <img src="assets/img/blank.png" width="300" height="80" />
                    <img src="assets/img/blank.png" width="300" height="80" />
                </div>
            <?php endif ?>
            <?php if($form_entrada['frm_plantilla'] == 111 ): ?><!-- coloca imagen para dos paginas -->
                <div id="campo_logo" align="right">
                    <img src="assets/img/blank.png" width="300" height="220" />
                    <img src="assets/img/blank.png" width="300" height="220" />
                    <img src="assets/img/blank.png" width="300" height="220" />
                </div>
            <?php endif ?>
            <!--FIN TABLA DE RESULTADOS-->

                <!--PIE DE PAGINA-->
                <!--<br/><br/>-->
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
                    <thead>
                        <tr>
                            <td colspan="2"><h2><p align="center"><?= $resultado[0]->mensaje_titulo ?></p></h2></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2"><?= $resultado_observacion[0]->mensaje_detalle ?> 
                                <br>
                                * Confirme la validez de este documento ingresando a <a href="https://gestionlabs.com/" target="_blank">https://gestionlabs.com</a> y el codigo <?= $certificado->clave_documento_pre ?>
                                <br/>
                                <i><b>Gestion Labs de Colombia Ltda</b></i>
                                <?php if ($form_entrada['frm_firma_preliminar'] == 1): ?>
                                   <br><img height="58" src="assets/img/firmas/<?= $aux_firma?>" />
                                <?php else: ?>
                                   <br/><br/><br/> 
                                <?php endif ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= $aux_nombre1 ?></td><td><?= $aux_nombre ?></td>
                        </tr>
                        <tr>
                            <td><b><?= $aux_cargo1 ?></b></td><td><b><?= $aux_cargo ?></b></td>
                        </tr>
                        <tr class="center">
                            <td colspan="2"><h2><p align="center">- FIN DE INFORME -</p><h2></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>