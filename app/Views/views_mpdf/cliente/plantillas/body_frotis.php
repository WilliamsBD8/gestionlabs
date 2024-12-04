<?php
	$db = \Config\Database::connect();
	//conteo de numeros
	$sql_productos      =   "SELECT * FROM certificacion c where certificado_nro=$certificado->certificado_nro";
	$query_productos    =   $db->query($sql_productos)->getResult();
	$conteo_productos   =   count($query_productos);
	//
	$detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
	//conteo de columnas
	$sql_analisis = "SELECT * FROM ensayo e, parametro p where e.id_parametro=p.id_parametro
                                and e.id_ensayo in (SELECT id_ensayo FROM ensayo_vs_muestra e where id_muestra=
                                (SELECT max(id_muestreo_detalle) FROM certificacion c where certificado_nro=$certificado->certificado_nro)) order by id_ensayo";

    $query_analisis = $db->query($sql_analisis)->getResult();
    $conteo_columnas = count($query_analisis);
    $conteo_columnas = $conteo_columnas+2;//cantidad de columnas
?>
<tr>
	<td colspan="3">
		<table class="table" style="width: 100%;">
			<thead>
				<tr>
					<th colspan="3">
						<div id="amc-header2">                        
						<strong>IDENTIFICACIÓN DE LA MUESTRA</strong><br>
						</div>
					</th>
				</tr>
				<tr>
					<th class="text-center">No. Muestras</th>
					<th class="text-center">Tipo de Muestras</th>
					<th class="text-center">Estado/Area/Funci&oacute;n</th>					
				</tr>
			</thead>
			<tbody>
				<tr>
                        <td><div align="center"><strong><?= $conteo_productos ?></strong></div></td>
                        <td><div align="center"><?= $detalle[0]->mue_parametro ?> </div></td>
                        <td><div align="center"><?= $detalle[0]->mue_area ?></div></td>
                </tr>
			</tbody>
		</table>
	</td>
</tr>
<!----- fin Area de Identificacion ---->
<!----- Area de tabla Resultados  ---->
<tr>
	<td colspan="3">
		<table class="table" style="width: 100%;">
			<thead>
				<tr>
					<th colspan="<?= $conteo_columnas ?>">
						<div id="amc-header2">                        
						<strong>TABLA DE RESULTADOS</strong><br>
						</div>
					</th>
				</tr>
				
				<tr>
					<th class="text-center" >Identificaci&oacute;n</th>
					<th class="text-center" >C&oacute;digo</th>
				 <!-- //TITULOS -->
				 <?php foreach($query_analisis as $fila_analisis): ?>
					 <th><div align="center"><strong><?= $fila_analisis->par_descripcion ?></strong></div></th>
				 <?php endforeach ?>
				 <tr>
			</thead>
			<tbody>
				<?php 
			 		//RESULTADOS
             		//recorre todos los certificados de la muestra a informar
             		$sql_certificados2 = "SELECT * FROM certificacion where certificado_nro=$certificado->certificado_nro";
                	$query_certificados2 = $db->query($sql_certificados2)->getResult();
                ?>

                <?php foreach($query_certificados2 as $fila_certificados2): ?>
            		<?php 
            		//formateo parametro
                    	$detalle2 = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_certificados2->id_muestreo_detalle);
                	//formateo tipo de analisis
                    	$tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle2[0]->id_tipo_analisis);
                    ?>
                    <tr>
						<td><?= $detalle2[0]->mue_identificacion ?></td>
						<td ><div align="center"><?= construye_codigo_amc($detalle2[0]->id_muestra_detalle) ?></div></td>
						<?php 
							//se debe de recorrer todos los resultados
							$sql_resultados = "SELECT * FROM ensayo_vs_muestra e where id_muestra=".$detalle2[0]->id_muestra_detalle;
							$query_resultados = $db->query($sql_resultados)->getResult();
						?>
						<?php foreach($query_resultados as $fila_resultados): ?>
							<!-- //formateo de detalle -->
							<?php if (!$fila_resultados->resultado_mensaje): ?>
								<?php if($fila_resultados->resultado_mensaje == '0'): ?>
									<td><div align="center">0</div></td>
								<?php else: ?>
									<td><div align="center">Pendientes</div></td>
								<?php endif ?>
							<?php else: ?>
								<?php $fila_resultados->resultado_mensaje = formateo_valores($fila_resultados->resultado_mensaje, $frm_form_valo); ?>
								<td><div align="center"><?= $fila_resultados->resultado_mensaje ?></div></td>
							<?php endif ?>
						<?php endforeach ?>
					</tr> 
					<?php $id_detalle = $detalle2[0]->id_muestra_detalle;//para sacar posterior mente los datos de norma y producto ?>
				<?php endforeach ?>
				<?php 
					//NORMA Y RANGOS DE MEDICION
					$sql_norma_producto = "SELECT distinct n.nor_nombre, p.pro_nombre FROM ensayo e, producto p, norma n
											where e.id_producto=p.id_producto and p.id_norma=n.id_norma
											and e.id_ensayo=(
											 SELECT max(id_ensayo) 
											 FROM ensayo_vs_muestra e 
											 WHERE id_muestra=$id_detalle )";//$id_detalle group by id_muestra
					$query_norma_producto = $db->query($sql_norma_producto)->getResult();
					$norma_producto = $query_norma_producto[0];
				?>
				<tr>
					<td colspan="2">
						<div align="center">
		 					<strong><?= $norma_producto->nor_nombre.' '.$norma_producto->pro_nombre ?></strong>
						</div>
					</td>
					<?php 
						$sql_norma_parametro = "SELECT * FROM ensayo e, parametro p 
											WHERE e.id_parametro=p.id_parametro
											AND e.id_ensayo in (
													SELECT id_ensayo 
													FROM ensayo_vs_muestra e 
													WHERE id_muestra=$id_detalle) order by id_ensayo";//$id_detalle
						$norma_parametros = $db->query($sql_norma_parametro)->getResult();
					?>
					<?php foreach($norma_parametros as $fila_parametros): ?>
						<!-- //presentacion de rangos de norma -->
						<!-- // si existen los 2 rangos se muestran separados de lo contrario se mostrara 1 -->
						<?php if($fila_parametros->med_valor_min && $fila_parametros->med_valor_max): ?>
							<td><div align="center"><?= $fila_parametros->med_valor_min.' - '.$fila_parametros->med_valor_max ?></div></td>
						<?php else: ?>
							<td><div align="center"><?= $fila_parametros->med_valor_min.' - '.$fila_parametros->med_valor_max ?></div></td>
						<?php endif ?>
					<?php endforeach ?>
				</tr>				
			</tbody>
		</table>
	</td>
</tr>