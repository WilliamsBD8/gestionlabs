<?php $db = \Config\Database::connect(); ?>
<tr>
	<td colspan="3">
		<table class="table" style="width: 100%;">
			<thead>
				<tr>
					<th colspan="7">
						<div id="amc-header2">                        
						<strong>IDENTIFICACIÓN DE LA MUESTRA</strong><br>
						</div>
					</th>
				</tr>
				<tr>
					<th class="text-center">Codigo</th>
					<th class="text-center">Producto</th>
					<th class="text-center">Lote</th>
					<th class="text-center">Fecha elaboraci&oacute;n</th>
					<th class="text-center">Fecha vencimiento</th>
					<th class="text-center">Condiciones de recepci&oacute;n</th>
					<th class="text-center">Nro. muestras analizadas</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sql_analisis = "select * from certificacion where certificado_nro=$certificado->certificado_nro";
            
		            $query_analisis = $db->query($sql_analisis)->getResult();

		            $conteo_productos = 1;
				?>
				<?php foreach ($query_analisis as $fila_analisis): ?>
					<?php
						//formateo de detalle
                    		$detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $fila_analisis->id_muestreo_detalle);
               			//formateo tipo de analisis
                    		$tipo_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $detalle[0]->id_tipo_analisis);
					?>
					<tr>
						<td><div align="center" ><?= construye_codigo_amc($detalle[0]->id_muestra_detalle) ?></div></td>
						<td><div align="center"><?= $detalle[0]->mue_identificacion ?></div></td>
						<td><div align="center"><?= $detalle[0]->mue_lote ?> </div></td>
						<td><div align="center"><?= $detalle[0]->mue_fecha_produccion ?></div></td>
						<td><div align="center"><?= $detalle[0]->mue_fecha_vencimiento ?></div></td>
						<td><div align="center"><?= $detalle[0]->mue_condiciones_recibe ?></div></td>
						<td><div align="center"><?= $detalle[0]->mue_cantidad ?></div></td>
					</tr>
					<?php
						$conteo_productos++;
						$id_detalle = $detalle[0]->id_muestra_detalle;//para sacar posterior mente los datos de norma y producto
					?>
				<?php endforeach ?>
				<?php $conteo_columnas = $conteo_productos+15; ?>
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
					<th class="text-center" rowspan="2">ENSAYO</th>
					<th class="text-center" rowspan="2">T&Eacute;CNICA</th>                 
					<th class="text-center" colspan="14">DIA</th>
					<?php 
						//buscamos l anorma y e producto relacionado
                       $sql_norma_producto = "SELECT distinct n.nor_nombre, p.pro_nombre FROM ensayo e, producto p, norma n
                                                where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                                                and e.id_ensayo=(
													SELECT max(id_ensayo) 
													FROM ensayo_vs_muestra e where id_muestra=$id_detalle )";//group by id_muestra
                       
						$query_norma_producto = $db->query($sql_norma_producto)->getResult();
                        $norma_producto = $query_norma_producto[0];
                    ?>
                    <th rowspan="2">
                        <div align="center"><strong><?= $norma_producto->nor_nombre.' '.$norma_producto->pro_nombre ?></strong></div>
                    </th>
				</tr>
				<tr> 
                    <td><div align="center"><strong>1</strong></div></td>
                    <td><div align="center"><strong>2</strong></div></td>
                    <td><div align="center"><strong>3</strong></div></td>
                    <td><div align="center"><strong>4</strong></div></td>
                    <td><div align="center"><strong>5</strong></div></td>
                    <td><div align="center"><strong>6</strong></div></td>
                    <td><div align="center"><strong>7</strong></div></td>
                    <td><div align="center"><strong>8</strong></div></td>
                    <td><div align="center"><strong>9</strong></div></td>
                    <td><div align="center"><strong>10</strong></div></td>
                    <td><div align="center"><strong>11</strong></div></td>
                    <td><div align="center"><strong>12</strong></div></td>
                    <td><div align="center"><strong>13</strong></div></td>
                    <td><div align="center"><strong>14</strong></div></td>
                </tr>
			</thead>
			<tbody>
				<?php
					$sql_ensayos = "SELECT 
										DISTINCT p.id_parametro
										,p.refe_bibl
										,e.resultado_mensaje	
										,p.fecha_aplica_referencia					
								FROM certificacion c, muestreo_detalle m, ensayo_vs_muestra e, ensayo p
								where c.id_muestreo_detalle=m.id_muestra_detalle
								and m.id_muestra_detalle=e.id_muestra
								and e.id_ensayo=p.id_ensayo
								and c.certificado_nro=$certificado->certificado_nro ";//group by e.id_ensayo
					$query_ensayos = $db->query($sql_ensayos)->getResult();
				?>
				<?php foreach ($query_ensayos as $fila_ensayos): ?>
					<?php
						$aux_resultado = 'Satisfactoria';
						//formateo parametro
							$parametro = procesar_registro_fetch('parametro', 'id_parametro', $fila_ensayos->id_parametro);
						//evaluamos si existe referencia bibliografica en el ensayo
						// vemos si tiene fecha en que aplica y luego si la referencia, hecho 9 de dic 2019
							if($fila_ensayos->fecha_aplica_referencia < $fecha_analisis){
								$aux_descripcion_ensayo = ($fila_ensayos->refe_bibl)?$fila_ensayos->refe_bibl:$parametro[0]->par_descripcion;
							}else
								$aux_descripcion_ensayo = $parametro[0]->par_descripcion;

						//formateo tecnica
							$tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica);
					?>
					<tr>
						<td><?= $aux_descripcion_ensayo ?></td>
						<td><div align="center"><?= $tecnica[0]->nor_nombre ?></div></td>
						<?php for($i=0;$i<$fila_ensayos->resultado_mensaje;$i++): ?>
							<td>*</td>
						<?php endfor ?>
						<?php for($n=$i;$n<14;$n++): ?>
							<td><div align="center">&nbsp;</div></td>
						<?php endfor ?>
						<?php if ($i == 14): ?>
							<td><div align="center">Satisfactoria</div></td></tr>
						<?php else: ?>
							<td><div align="center">No satisfactoria</div></td></tr>
							<?php $aux_resultado='No Satisfactoria'; ?>
						<?php endif ?>
					</tr>
				<?php endforeach ?>
				<tr>
					<td colspan="2"><p><div align="center"><strong>RESULTADO</strong></div></p></td>
					<td colspan="14"><div align="center"><strong><?= $aux_resultado ?></strong></div></td>
					<td >&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>