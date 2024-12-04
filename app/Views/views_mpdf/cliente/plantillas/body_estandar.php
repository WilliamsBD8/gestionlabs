<?php $db = \Config\Database::connect(); ?>
<tr>
	<td colspan="3">
		<table class="table" style="width: 100%;">
			<thead>
				<tr>
					<th colspan="5">
						<div id="amc-header2">                        
						<strong>IDENTIFICACIÓN DE LA MUESTRA</strong><br>
						</div>
					</th>
				</tr>
				<tr>
					<th class="text-center">No. Muestra</th>
					<th class="text-center">Identificación</th>
					<th class="text-center">Codigo</th>
					<th class="text-center">Tipo de muestra</th>
					<th class="text-center">Estado / Área / Función</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sql_analisis = "select * from certificacion where certificado_nro=$certificado->certificado_nro";
					$certificacion = $db->query($sql_analisis)->getResult();
					$conteo_productos = 0;
					$contador = 1;
				?>
				<?php foreach ($certificacion as $key => $fila_analisis): ?>
					<?php 
						//formateo de detalle
								$detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle);
							//formateo tipo de analisis
								$tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle[0]->id_tipo_analisis);
							?>
							<tr>
									<td><div align="center"><?= $contador++ ?></div></td>
									<td><div align="center"><?= $detalle[0]->mue_identificacion ?></div></td>
									<td><div align="center"><?= construye_codigo_amc($detalle[0]->id_muestra_detalle) ?></div></td>
									<td><div align="center"><?= $tipo_analisis[0]->mue_nombre ?></div></td>
									<td><div align="center"><?= $detalle[0]->mue_area ?></div></td>
							</tr>
							<?php $conteo_productos++; ?>
				<?php endforeach ?>
				<?php $conteo_columnas = $conteo_productos+5; ?>
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
					<th colspan="<?= ($conteo_columnas+1) ?>">
						<div id="amc-header2">                        
							<strong>TABLA DE RESULTADOS</strong><br>
						</div>
					</th>
				</tr>
				<tr>
					<th class="text-center" rowspan="2">ENSAYO/ MÉTODO</th>
					<th class="text-center" colspan="<?= ($conteo_productos+1) ?>">RESULTADOS</th>
					<th class="text-center" rowspan="2">UNIDADES</th>
					<th class="text-center" style="font-size: 12px;" rowspan="2">µ</th>
					<th class="text-center" rowspan="2">REGLA</th>
					<th class="text-center" >ESPECIFICACIÓN</th>
				</tr>
				<tr>
					<?php foreach($certificacion as $fila_analisis): ?>
						<th class="text-center"><?= $detalle[0]->mue_identificacion ?></th>
						<?php $id_detalle = $detalle[0]->id_muestra_detalle; ?>
						<?php endforeach ?>
						<th class="text-center">Cumplimiento</th>
					<?php $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
                                                where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                and e.id_ensayo=(SELECT MIN(id_ensayo) FROM ensayo_vs_muestra e where id_muestra=$id_detalle )";
                    	// $query_norma_producto = $mysqli->query($sql_norma_producto) or die ('Error 03. Obteniendo norma en la tabla de resultados' .  $mysqli->error.$sql_norma_producto);
                    	$norma_producto = $db->query($sql_norma_producto)->getResult();
                    ?>
					<th class="text-center"><?= $norma_producto[0]->nor_nombre.' '.$norma_producto[0]->pro_nombre ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$campo_primer_informe = $tipo_mensajes == 2 ? 0:1;
					// $primer_informe = implode(",", $primer_informe);
					$sql_ensayos = "SELECT 
			 					DISTINCT p.id_parametro
								,p.fecha_aplica_referencia
								,p.id_ensayo
								,p.refe_bibl
								,p.med_valor_min
								,p.med_valor_max								
			  				FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
							where c.id_muestreo_detalle=m.id_muestra_detalle
							and m.id_muestra_detalle=e.id_muestra
							and e.id_ensayo=p.id_ensayo
							and e.campo_primer_informe=$campo_primer_informe
							and c.certificado_nro=$certificado->certificado_nro "; // group by e.id_ensayo
					$query_ensayos = $db->query($sql_ensayos)->getResult(); ?>
				<?php foreach ($query_ensayos as $fila_ensayos): ?>
					<?php
						$sql_ensayosWD = "SELECT 
			 						DISTINCT e.resultado_mensaje
									,e.id_regla
									,m.mue_unidad_medida
			  				FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
							where c.id_muestreo_detalle=m.id_muestra_detalle
							and m.id_muestra_detalle=e.id_muestra
							and e.id_ensayo=p.id_ensayo
							and c.certificado_nro=$certificado->certificado_nro and p.id_ensayo = $fila_ensayos->id_ensayo limit 1"; // group by e.id_ensayo
						$query_ensayosWD = $db->query($sql_ensayosWD)->getResult();
						$fila_ensayosWD = $query_ensayosWD[0];

						$parametro = procesar_registro_fetch('parametro', 'id_parametro', $fila_ensayos->id_parametro);
						if($fila_ensayos->fecha_aplica_referencia < $fecha_analisis)
						   $aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$parametro[0]->par_descripcion;
						else
						   $aux_descripcion_ensayo = $parametro[0]->par_descripcion;
					?>
					<tr>
						<td><?= $aux_descripcion_ensayo ?></td>
						<?php foreach($certificacion as $fila_analisis): ?>
							<?php
								$sql_ensayos2 = "
									SELECT e.resultado_mensaje, m.id_tipo_analisis, e.id_ensayo_vs_muestra
									FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
									WHERE 
									c.id_muestreo_detalle=m.id_muestra_detalle
									AND m.id_muestra_detalle=e.id_muestra
									AND e.id_ensayo=p.id_ensayo
									AND c.id_certificacion=$fila_analisis->id_certificacion 
									AND e.id_ensayo=$fila_ensayos->id_ensayo ";//
									$query_ensayos2 = $db->query($sql_ensayos2)->getResult();
									$fila_ensayos2 = $query_ensayos2[0];
							?>

						 <!-- //formateo de detalle -->
                         	<?php if (!isset($fila_ensayos2->resultado_mensaje) ):?>
								<?php if($fila_ensayos2->resultado_mensaje == '0'):?>
									<td><div align="center">0 </div></td>
								<?php else:?>
									<td><div align="center">Pendientes</div></td>
								<?php endif?>
							<?php else:?>
								<?php $fila_ensayosWD->resultado_mensaje = formateo_valores($fila_ensayos2->resultado_mensaje, $frm_form_valo); ?>
								<td><div align="center"><?= $fila_ensayosWD->resultado_mensaje ?></div></td>
							<?php endif?>
							<?php
								$evalua = evalua_alerta($fila_ensayos->med_valor_min,$fila_ensayos->med_valor_max, $fila_ensayosWD->resultado_mensaje, $fila_ensayos2->id_tipo_analisis, $fila_analisis->id_ensayo_vs_muestra, 2);
								$cumple = preg_match("/-MAS-/", $evalua) ? "No cumple":"Cumple";
							?>
							<td><?= $cumple ?></td>
						<?php endforeach ?>
						<?php
							//unidades
							if($fila_ensayosWD->mue_unidad_medida == 'solida'){
								$unidad = $parametro[0]->unidad_solida;
							}elseif($fila_ensayosWD->mue_unidad_medida == 'liquida'){
								$unidad = $parametro[0]->unidad_liquida;
							}else {
								$unidad = 'No aplica';
							}
							//incertidumbre
							$incertidumbre = ($parametro[0]->incertidumbre)?$parametro[0]->incertidumbre:'No aplica';
							//validamos que tenga una incertidumbre
							if($aux_incertidumbre == 0){
								if($incertidumbre <> 'No aplica'){
									$aux_incertidumbre = 1;
								}
							}
							//regla
							if($fila_ensayosWD->id_regla > 0){
								$fila_regla = procesar_registro_fetch('regla', 'id_regla', $fila_ensayosWD->id_regla );
								$regla = $fila_regla[0]->nombre;
								
								$descripcion = str_replace('<p>', '', $fila_regla[0]->descripcion); 
								$descripcion = str_replace('</p>', '', $descripcion); 
								$array_regla[$regla] = trim($descripcion); 
							}else{
								$regla = 'No aplica';
								$descripcion = 'xxxx'; 
							}
						?>
						<td><div align="center"><?= $unidad ?></div></td>
						<td><div align="center"><?= $incertidumbre ?> incertidumbre</div></td>
						<td><div align="center"><?= $regla ?> </div></td>
						<?php if($fila_ensayos->med_valor_min && $fila_ensayos->med_valor_max): ?>
							<td><div align="center"><?= $fila_ensayos->med_valor_min.' - '.$fila_ensayos->med_valor_max ?></div></td>
					    <?php else: ?>
							<td><div align="center"><?= $fila_ensayos->med_valor_min.' - '.$fila_ensayos->med_valor_max ?></div></td>
					    <?php endif ?>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</td>
</tr>
<!----- fin Area de tabla Resultados  ---->
