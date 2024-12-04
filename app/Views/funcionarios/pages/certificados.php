<div id="form-nueva"></div>
<form id="form-certificados" action="<?= base_url(['funcionario', 'certificacion']) ?>" method="POST"  enctype="multipart/form-data">
	<div class="row">
		<div class="col s12 l12">
			<b><h5 class="center-align">Resultado para el informe Nro.<?= $certificados[0]->certificado_nro ?></h5></b>
			<table class="striped">
				<thead>
					<tr>
		            	<th><b>Procedencia</b></th>
		                <th><?= $certificados[0]->name ?></th>
		                <th><b>Muestreo</b></th>
		                <th id="campo_fecha_muestreo"><p class="doble_click" ondblclick="editar_campos('date_complete','campo_fecha_muestreo','<?= $certificados[0]->mue_fecha_muestreo ?>', 'frm_fecha_muestra', 'mue_fecha_muestreo', 'muestreo' , '<?= $certificados[0]->id_muestreo ?>')"><?= $certificados[0]->mue_fecha_muestreo ?></p></th>
		            </tr>
		            <tr>
		           		<th><b>Sub titulo</b></th>
		                <th id="campo_sub_titulo"><p class="doble_click" ondblclick="editar_campos(`text`, `campo_sub_titulo`,`<?= $certificados[0]->mue_subtitulo ?>`, `frm_nombre_empresa_subtitulo`, `mue_subtitulo`, `muestreo` ,`<?= $certificados[0]->id_muestreo ?>`)"><?= $certificados[0]->mue_subtitulo ?></p></th>
		                <th><b>Registro</b></th>
		                <th id="campo_fecha_recepcion"><p class="doble_click" ondblclick="editar_campos(`date_complete`,`campo_fecha_recepcion`,`<?= $certificados[0]->mue_fecha_recepcion ?>`, `frm_fecha_recepcion`, `mue_fecha_recepcion`, `muestreo` ,`<?= $certificados[0]->id_muestreo ?>`)"><?= $certificados[0]->mue_fecha_recepcion ?></p></th>
		            </tr>
		            <tr>
		            	<th><b>Nit</b></th>
		                <th><?= $certificados[0]->id ?></th>
		                <th><b>Analisis</b></th>
		                <th></th>
		            </tr>
		            <tr>
		            		<th><b>&nbsp;</b></th>
		                <th><b>&nbsp;</b></th>
		                <th><b>Elaboracion del primer informe</b></th>
		                <th id="campo_fecha_preinforme"><p class="doble_click" ondblclick="editar_campos(`date_complete`,`campo_fecha_preinforme`,`<?= $certificados[0]->cer_fecha_preinforme ?>`, `frm_fecha_preinforme`, `cer_fecha_preinforme`, `certificacion` ,`<?= $id_certificado ?>`)"><?= $certificados[0]->cer_fecha_preinforme ?></p></th>
		            </tr>
		            <tr>
		            	<th><b>&nbsp;</b></th>
		                <th><b>&nbsp;</b></th>
		                <th><b>Elaboracion del informe</b></th>
		                <th id="campo_fecha_informe"><p class="doble_click" ondblclick="editar_campos(`date_complete`,`campo_fecha_informe`,`<?= $certificados[0]->cer_fecha_informe ?>`, `frm_fecha_informe`, `cer_fecha_informe`, `certificacion` ,`<?= $id_certificados ?>`)"><?= $certificados[0]->cer_fecha_informe ?></p></th>
		            </tr>
				</thead>
			</table>
		</div>
	</div>
	<hr>
	<?php foreach ($certificados as $key => $certificado): ?>
		<?php 	
	        if (!$certificado->mue_procedencia)
	            $certificado->mue_procedencia='-xxx-';
	        if (!$certificado->mue_identificacion)
	            $certificado->mue_identificacion='-xxx-';
	        if (!$certificado->mue_lote)
	            $certificado->mue_lote='-xxx-';
	        if (!$certificado->mue_fecha_produccion)
	            $certificado->mue_fecha_produccion='-xxx-';
	        if (!$certificado->mue_fecha_vencimiento)
	            $certificado->mue_fecha_vencimiento='-xxx-';
	        if (!$certificado->mue_temperatura_muestreo)
	            $certificado->mue_temperatura_muestreo='-xxx-';
	        if (!$certificado->mue_temperatura_laboratorio)
	            $certificado->mue_temperatura_laboratorio='-xxx-';
	        if (!$certificado->mue_condiciones_recibe)
	            $certificado->mue_condiciones_recibe='-xxx-';
	        if (!$certificado->mue_cantidad)
	            $certificado->mue_cantidad='-xxx-';
	    ?>
	    <!-- // Encabezado -->
		<div class="row">
			<div class="col s12 l12">
				<h5 class="center-align"><b>Muestra <?=  construye_codigo_amc($certificados[0]->id_muestra_detalle)?></b></h5>
				<table class="striped table-muestreo">
					<thead>
						<tr>
			            	<th><b>Procedencia</b></th>
			                <th id="campo_procedencia<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_procedencia<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_procedencia?>`, `frm_procedencia`, `mue_procedencia`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_procedencia?></p></th>
			                <th><b>Identificaci&oacute;n</b></th>
			                <?php $certificado->mue_identificacion = str_replace('"', "''", $certificado->mue_identificacion)?>
			                <th id="campo_identificacion<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_identificacion<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_identificacion?>`, `frm_identificacion`, `mue_identificacion`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_identificacion?></p></th>
			           		<th><b>No. lote</b></th>
			                <th id="campo_lote<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_lote<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_lote?>`, `frm_lote`, `mue_lote`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_lote?></p></th>
			            </tr>
			            <tr>
			                <th><b>Fecha Producci&oacute;n</b></th>
			                <th id="campo_fecha_produccion<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`date`,`campo_fecha_produccion<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_fecha_produccion ?>`, `frm_fecha_produccion`, `mue_fecha_produccion`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_fecha_produccion?></p></th>
			            	<th><b>Fecha vencimiento</b></th>
			                <th id="campo_fecha_vencimiento<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`date`,`campo_fecha_vencimiento<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_fecha_vencimiento ?>`, `frm_fecha_vencimiento`, `mue_fecha_vencimiento`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_fecha_vencimiento?></p></th>
			                <th><b>Temp. Ingreso</b></th>
			                <th id="campo_temperatura_muestreo<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_temperatura_muestreo<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_temperatura_muestreo?>`, `frm_temperatura_muestreo`, `mue_temperatura_muestreo`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_temperatura_muestreo?></p></th>
			            </tr>
			            <tr>
			                <th><b>Temp. recepci&oacute;n</b></th>
			                <th id="campo_temperatura_laboratorio<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_temperatura_laboratorio<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_temperatura_laboratorio?>`, `frm_temperatura_laboratorio`, `mue_temperatura_laboratorio`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_temperatura_laboratorio?></p></th>
			            	<th><b>Condiciones de recibido</b></th>
			            	
			                <th><?= $certificado->mue_condiciones_recibe?></th>
			                <!--<th id="campo_condiciones_recibe<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_condiciones_recibe<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_condiciones_recibe?>`, `frm_condiciones_recibe`, `mue_condiciones_recibe`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_condiciones_recibe?></p></th>-->
			                <th><b>Cantidad</b></th>
			                <th id="campo_cantidad<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_cantidad<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_cantidad?>`, `frm_cantidad`, `mue_cantidad`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_cantidad?></p></th>
			            </tr>
			            
			            <!-- Adicion de nuevos parametros 29-09-2022 -->
			            <tr>
    						<th><b>Area / Función</b></th>
    						<th id="campo_area_funcion<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_area_funcion<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_area?>`, `frm_area`, `mue_area`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_area?></p></th>
    						<th><b>Tipo de muestreo</b></th>
    						<th id="campo_tipo_muestreo<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_tipo_muestreo<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_tipo_muestreo?>`, `frm_tipo_muestreo`, `mue_tipo_muestreo`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_tipo_muestreo?></p></th>
    						<th><b>Dilución</b></th>
    						<th id="campo_dilucion<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_dilucion<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_dilucion?>`, `frm_mue_dilucion`, `mue_dilucion`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_dilucion?></p></th>
    					</tr>
    
    					<tr>
    						<th><b>Empaque</b></th>
    						<th colspan="2" id="campo_empaque<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_empaque<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_empaque?>`, `frm_mue_empaque`, `mue_empaque`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_empaque?></p></th>
    						<th><b>Observaciones</b></th>
    						<th colspan="2" id="campo_adicional<?= $certificado->id_muestra_detalle?>"><p class="doble_click" ondblclick="editar_campos(`text`,`campo_adicional<?= $certificado->id_muestra_detalle?>`,`<?= $certificado->mue_adicional?>`, `frm_adicional`, `mue_adicional`, `muestreo_detalle` ,`<?= $certificado->id_muestra_detalle?>`)"><?= $certificado->mue_adicional?></p></th>
    					</tr>
			            <!-- Fin 29-09-2022 -->
			            
			            <tr>
			                <th><b>An&aacute;lisis solicitado</b></th>
			                <th colspan="2"><?= $certificado->mue_nombre?></th>
			            	<th><b>Producto</b></th>
			                <th colspan="2"><?= $certificado->pro_nombre?></th>
			            </tr>
					</thead>
				</table>
			</div>
		</div>

		<!-- Parametros -->
		<?php
			$ensayos = procesar_registro_fetch('ensayo', 'id_producto', $certificado->id_producto);
    	    $parametros_aux = [];
    	    $llaves_ensayo = [];
			$fechasUtiles = procesar_registro_fetch('fecha_vida_util', 'id_detalle_muestreo', $certificado->id_muestreo_detalle);
    	  ?>
    	  
    	<!-- Adicion de boton para las fechas utiles 29-09-2022 -->
    	
    	<?php if(!empty($fechasUtiles)): ?>
			<br>
			<div class="row">
				<div class="col s12 l12 center">
					<a href="#!" class="btn" onclick='openFechas(<?= json_encode($fechasUtiles) ?>)'>Buscar fechas</a>
				</div>
			</div>
			<br>
		<?php else: ?>
		    <br><br>
		<?php endif ?>
    	
	  <div class="row">
			<div class="col s12 l12">
				<div class="div_parametros">
					<table class="striped centered">
						<thead>
							<tr>
								<th class="black-text"><b>Titulo</b></th>
								<?php foreach ($ensayos as $key => $ensayo): ?>
									<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro); ?>
									<?php if ($parametro[0]->par_estado == 'Activo'): ?>
										<?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle); ?>
		        						<?php if (!empty($ensayo_vs_muestra[0])): ?>
											<?php
												$llaves_ensayo[$key] = 1;
												$parametros_aux[$key] = $parametro[0];
												$tecnica = procesar_registro_fetch('tecnica', 'id_tecnica', $parametro[0]->id_tecnica);
											?>
											<th class="black-text" style="min-width: 130px">
												<b><?= $parametro[0]->par_nombre ?></b>
												<br>
												<small class="grey-text text-darken-4"><?= $tecnica[0]->nor_nombre ?></small>
												<br>
												<small class="green-text text-darken-4"><b><?= $parametro[0]->par_estado ?></b></small>
											</th>
										<?php endif ?>
									<?php endif ?>
								<?php endforeach ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><b>Rango</b></td>
								<?php foreach ($ensayos as $key => $ensayo): ?>
			        					<?php if (!empty($llaves_ensayo[$key])): ?>
											<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);  ?>
											<?php if ($parametro[0]->par_estado == 'Activo'): ?>
												<td>
				        							<b><?= $ensayo->med_valor_min.' - '.$ensayo->med_valor_max ?></b>
				        						</td>
											<?php endif ?>
										<?php endif ?>
								<?php endforeach ?>
							</tr>
							<tr>
								<td><b>Unidades</b></td>
								<?php foreach ($ensayos as $key => $ensayo): ?>
										<?php if (!empty($llaves_ensayo[$key])): ?>
											<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);  ?>
											<?php if ($parametro[0]->par_estado == 'Activo'): ?>
												<?php $aux = 'No aplica'; ?>
												<?php if ($certificado->mue_unidad_medida): ?>
													<?php $aux = ($certificado->mue_unidad_medida == 'solida')?$parametro[0]->unidad_solida:$parametro[0]->unidad_liquida ?>
													<?php if (!trim($aux)): ?>
														<?php $aux= ($certificado->mue_unidad_medida == 'solida')?'gr':'ml'; ?>
													<?php endif ?>
												<?php endif ?>
												<?php $aux_incertidumbre = ($parametro[0]->incertidumbre) ? $parametro[0]->incertidumbre : 'No aplica'; ?>
												<td><b><?= $aux ?></b></td>
											<?php endif ?>
										<?php endif ?>
								<?php endforeach ?>
							</tr>
							<tr>
								<td><b>μ</b></td>
								<?php foreach ($ensayos as $key => $ensayo): ?>
									<?php if (!empty($llaves_ensayo[$key])): ?>
										<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro); ?>
										<?php if ($parametro[0]->par_estado == 'Activo'): ?>
											<?php $aux = 'No aplica'; ?>
											<?php if($certificado->mue_unidad_medida): ?>
																		<?php $aux = ($certificado->mue_unidad_medida == 'solida')?$parametro[0]->unidad_solida:$parametro[0]->unidad_liquida; ?>
																		<!-- // si es vacio el campo se mostrara la unidad de medida -->
																		<?php if(!trim($aux)): ?>
																			<?php $aux= ($certificado->mue_unidad_medida == 'solida') ? 'gr':'ml'; ?>
																		<?php endif ?>
																<?php endif ?>
															<?php $aux_incertidumbre = ($parametro[0]->incertidumbre) ? $parametro[0]->incertidumbre : 'No aplica'; ?>
											<td><b><?= $aux_incertidumbre ?></b></td>
										<?php endif ?>
									<?php endif ?>
								<?php endforeach ?>
							</tr>
							<!--<tr>-->
								<!--<td><b>Regla</b></td>-->
								<?php foreach ($ensayos as $key => $ensayo): ?>
									<?php if (!empty($llaves_ensayo[$key])): ?>
										<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro); ?>
										<?php if ($parametro[0]->par_estado == 'Activo'): ?>
											<?php
												$ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle);
												$aux_id_ensayo_vs_muestra = $ensayo_vs_muestra[0]->id_ensayo_vs_muestra;
												$aux_id_regla = $ensayo_vs_muestra[0]->id_regla;
											?>
									<!--		<td>-->
									<!--			<div class="input-field col s12">-->
									<!--			<select name="frm_regla_<?= $aux_id_ensayo_vs_muestra ?>"-->
									<!--				onChange="cambiar_campos(`text`,<?= $aux_id_ensayo_vs_muestra ?>,this.value,-->
									<!--						`frm_regla_<?= $aux_id_ensayo_vs_muestra ?>`, `id_regla`, `ensayo_vs_muestra`, `<?= $aux_id_ensayo_vs_muestra ?>` )">-->
									<!--					<option value="0">No aplica</option>-->
									<!--					<?php $reglas = procesar_registro_fetch('regla' ,'estado', 'Activa'); ?>-->
									<!--					<?php foreach ($reglas as $key => $regla): ?>-->
									<!--						<?php $aux_checked = $regla->id_regla == $aux_id_regla ? "selected":''; ?>-->
									<!--						<option value="<?= $regla->id_regla ?>" <?= $aux_checked ?> > <?= $regla->nombre ?></option>-->
									<!--					<?php endforeach ?>-->
									<!--			</select>-->
									<!--			<small id="campo_regla_<?= $aux_id_ensayo_vs_muestra ?>"></small>-->
									<!--	</div>-->
									<!--</td>-->
										<?php endif ?>
									<?php endif ?>
								<?php endforeach ?>
							<!--</tr>-->
							<?php if(!empty($fechasUtiles)): ?>
							    <?php foreach($fechasUtiles as $key_aux => $fecha): ?>
							        <tr class="fecha_tr" id="registro_<?= $fecha->id ?>"  <?= $key_aux > 0 ? 'style="display: none"': '' ?>>
										<td><b>Registros [Día <?= $fecha->dia?>]</b></td>
										<?php foreach ($ensayos as $key => $ensayo): ?>
											<?php if (!empty($llaves_ensayo[$key])): ?>
												<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro); ?>
												<?php if ($parametro[0]->par_estado == 'Activo'): ?>
													<?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle, 'id_fecha_vida_util', $fecha->id); ?>
													<?php if (!empty($ensayo_vs_muestra[0])): ?>
														<td>
															<b><?= $ensayo_vs_muestra[0]->resultado_analisis ?> &nbsp;</b><br>
															<b><?= $ensayo_vs_muestra[0]->resultado_analisis2 ?> &nbsp;</b>
														</td>
													<?php endif ?>
												<?php endif ?>
											<?php endif ?>
										<?php endforeach ?>
									</tr>
									<tr class="fecha_tr" id="resultado_<?= $fecha->id ?>"  <?= $key_aux > 0 ? 'style="display: none"': '' ?>>
										<td><b>Resultado [Día <?= $fecha->dia?>]</b></td>
										<?php foreach ($ensayos as $llave => $ensayo): ?>
											<?php if (!empty($llaves_ensayo[$llave])): ?>
												<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);  ?>
												<?php if ($parametro[0]->par_estado == 'Activo'): ?>
													<?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle, 'id_fecha_vida_util', $fecha->id); ?>
													<?php if(!empty($ensayo_vs_muestra[0])): ?>
														<?php if ($ensayo_vs_muestra[0]->resultado_mensaje<>''): ?>
															<?php $color="black-text"; ?>
																				<?php if(preg_match("/-MAS-/",evalua_alerta($ensayo->med_valor_min  ,$ensayo->med_valor_max , $ensayo_vs_muestra[0]->resultado_mensaje, $certificado->id_tipo_analisis, $ensayo_vs_muestra[0]->id_ensayo_vs_muestra,2))): ?><!-- //2 para que no genere correos -->
																						<?php $color="red-text" ?>
																				<?php endif ?>
															<td style=" border-style: dotted;" id="resultado_<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>">
																<p class="<?= $color ?>" >
																		<b><?= $ensayo_vs_muestra[0]->resultado_mensaje ?></b></p>
																		
																		<!--ondblclick="editar_campos_redondeo(`resultado_<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>`,`<?= $ensayo_vs_muestra[0]->resultado_mensaje ?>`,`<?= $parametros_aux[$llave]->par_nombre ?>`, `frm_resultado_mensaje`, `resultado_mensaje`, `ensayo_vs_muestra` ,`<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>`, `<?= ($llave+1) ?>`)"-->
															</td>
														<?php else: ?>
															<?php if($que_mostrar==2): ?>
																<td><b>Pendiente</b></td>
															<?php else: ?>
																<td>&nbsp;</td>
															<?php endif ?>
														<?php endif ?>
													<?php endif ?>
												<?php endif ?>
											<?php endif ?>
										<?php endforeach ?>
									</tr>
									<tr class="fecha_tr" id="primer_<?= $fecha->id ?>"  <?= $key_aux > 0 ? 'style="display: none"': '' ?>>
										<td><b>Primer informe</b></td>
											<?php foreach ($ensayos as $llave => $ensayo): ?>
												<?php if (!empty($llaves_ensayo[$llave])): ?>
													<?php if ($parametro[0]->par_estado == 'Activo'): ?>
														<td> No aplica</td>
													<?php endif ?>
												<?php endif ?>
											<?php endforeach ?>
									</tr>
									<tr class="fecha_tr" id="condicion_<?= $fecha->id ?>"  <?= $key_aux > 0 ? 'style="display: none"': '' ?>>
										<td><b>Condición</b></td>
										<?php foreach ($ensayos as $llave => $ensayo): ?>
												<?php if (!empty($llaves_ensayo[$llave])): ?>
													<?php if ($parametro[0]->par_estado == 'Activo'): ?>
														<td> No aplica</td>
													<?php endif ?>
												<?php endif ?>
											<?php endforeach ?>
									</tr>
							    <?php endforeach ?>
							<?php else: ?>
    							<tr>
    								<td><b>Registros</b></td>
    								<?php foreach ($ensayos as $key => $ensayo): ?>
    									<?php if (!empty($llaves_ensayo[$key])): ?>
    										<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro); ?>
    										<?php if ($parametro[0]->par_estado == 'Activo'): ?>
    											<?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle); ?>
    											<?php if (!empty($ensayo_vs_muestra[0])): ?>
    												<td>
    													<b><?= $ensayo_vs_muestra[0]->resultado_analisis ?> &nbsp;</b><br>
    															<b><?= $ensayo_vs_muestra[0]->resultado_analisis2 ?> &nbsp;</b>
    												</td>
    											<?php endif ?>
    										<?php endif ?>
    									<?php endif ?>
    								<?php endforeach ?>
    							</tr>
    							<tr>
    								<td><b>Resultado</b></td>
    								<?php foreach ($ensayos as $llave => $ensayo): ?>
    									<?php if (!empty($llaves_ensayo[$llave])): ?>
    										<?php $parametro = procesar_registro_fetch('parametro', 'id_parametro', $ensayo->id_parametro);  ?>
    										<?php if ($parametro[0]->par_estado == 'Activo'): ?>
    											<?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle); ?>
    											<?php if(!empty($ensayo_vs_muestra[0])): ?>
    												<?php if ($ensayo_vs_muestra[0]->resultado_mensaje<>''): ?>
    													<?php $color="black-text"; ?>
    																		<?php if(preg_match("/-MAS-/",evalua_alerta($ensayo->med_valor_min  ,$ensayo->med_valor_max , $ensayo_vs_muestra[0]->resultado_mensaje, $certificado->id_tipo_analisis, $ensayo_vs_muestra[0]->id_ensayo_vs_muestra,2))): ?><!-- //2 para que no genere correos -->
    																				<?php $color="red-text" ?>
    																		<?php endif ?>
    													<td style=" border-style: dotted;" id="resultado_<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>">
    														<p class="<?= $color ?> doble_click" ondblclick="editar_campos_redondeo(`resultado_<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>`,`<?= $ensayo_vs_muestra[0]->resultado_mensaje ?>`,`<?= $parametros_aux[$llave]->par_nombre ?>`, `frm_resultado_mensaje`, `resultado_mensaje`, `ensayo_vs_muestra` ,`<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>`, `<?= ($llave+1) ?>`)">
    														    <b><?= $ensayo_vs_muestra[0]->resultado_mensaje ?></b></p>
    														    <!---->
    													</td>
    												<?php else: ?>
    													<?php if($que_mostrar==2): ?>
    														<td><b>Pendiente</b></td>
    													<?php else: ?>
    														<td>&nbsp;</td>
    													<?php endif ?>
    												<?php endif ?>
    											<?php endif ?>
    										<?php endif ?>
    									<?php endif ?>
    								<?php endforeach ?>
    							</tr>
    							<tr>
    								<td><b>Primer informe</b></td>
    									<?php foreach ($ensayos as $llave => $ensayo): ?>
    										<?php if (!empty($llaves_ensayo[$llave])): ?>
    											<?php
    												$ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle);
    												$checked = $ensayo_vs_muestra[0]->campo_primer_informe == 1 ? 'checked' : '';
    												$disabled = $ensayo_vs_muestra[0]->resultado_mensaje <>'' ? '' : 'disabled';
    											?>
    											<td>
    											<p>
    												<label>
    													<input type="checkbox" class="primer_informe" name="primer_informe[]" value="<?= $ensayo_vs_muestra[0]->id_ensayo_vs_muestra ?>" <?= "{$checked} {$disabled}" ?>/>
    													<span></span>
    												</label>
    											</p>
    											</td>
    										<?php endif ?>
    									<?php endforeach ?>
    							</tr>
    							<tr>
    								<td><b>Condición</b></td>
    								<?php foreach ($ensayos as $llave => $ensayo): ?>
    										<?php if (!empty($llaves_ensayo[$llave])): ?>
    											<?php if ($parametro[0]->par_estado == 'Activo'): ?>
    									            <?php $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo->id_ensayo, 'id_muestra', $certificado->id_muestra_detalle); ?>
    									        <?php if(!empty($ensayo_vs_muestra[0])): ?>
    										        <?php if ($ensayo_vs_muestra[0]->resultado_mensaje<>''): ?>
    											        <?php $cumple="Cumple";
    												        $evalua = evalua_alerta($ensayo->med_valor_min  ,$ensayo->med_valor_max , $ensayo_vs_muestra[0]->resultado_mensaje, $certificado->id_tipo_analisis, $ensayo_vs_muestra[0]->id_ensayo_vs_muestra,2);?>
    											<?php if(
    													preg_match("/-MAS-/",$evalua)
    												): ?><!-- //2 para que no genere correos -->
    													<?php $cumple="No cumple" ?>
    											<?php endif ?>
    											
    											<?php if(
    													preg_match("/-NOAPLICACUMPLE-/",$evalua)
    												): ?><!-- //2 para que no genere correos -->
    													<?php $cumple="No aplica" ?>
    											<?php endif ?>
    											<td>
    												<p><b><?= $cumple.' <br>'.$evalua ?></b></p>
    											</td>
    										<?php else: ?>
    												<td>&nbsp;</td>
    										<?php endif ?>
    									<?php endif ?>
    								<?php endif ?>
    										<?php endif ?>
    									<?php endforeach ?>
    							</tr>
							<?php endif ?>
						</tbody>
					</table>
				</div>
				<div class="row">
					<br>
					<div class="col s12 l12 cyan lighten-5" id="campo_resultado_redondeo">
					</div>
				</div>
			</div>
		</div>
	<?php endforeach ?>
			<?php
				$aux_c_v_m = $que_mostrar == 1 ? 2 : 1;
				$id_tipo_analisis = $certificado->id_tipo_analisis;
				$mensajes_aux =  procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $certificados[0]->certificado_nro, 'id_mensaje_tipo', $aux_c_v_m);
	    ?>
		    	<div class="row table">
		    		<div class="input-field col s12 m6 l6">
					    <select id="frm_mensaje_firma" name="frm_mensaje_firma" class="required">
					      	<option id="firma_" value="">Seleccione firma</option>
					      	<?php $sql_firma = "select cf.id_firma
			            			, cu.nombre n1
			            			, cu.cargo c1
			            			, cu2.nombre n2
			            			, cu2.cargo c2 
			            			, cf.id_firma_1
			            			, cf.id_firma_2
			            		from cms_firma cf inner join cms_users cu on cf.id_firma_1=cu.id 
			            		INNER JOIN cms_users cu2 on cf.id_firma_2=cu2.id 
			            		where cf.estado='Activo'";
		            			$firmas = $db->query($sql_firma)->getResult();
		            		?>
		            		<?php foreach ($firmas as $key => $firma): ?>
		            			<!--<?php $selected = $mensajes_aux[0]->id_firma == $firma->id_firma ? 'selected':'';?>-->
		            			<option id="firma_<?= $firma->id_firma ?>" value="<?= $firma->id_firma ?>" <?= $selected ?> ><?= $firma->n1.' ('.$firma->c1.') -/- '.$firma->n2.' ('.$firma->c2.')' ?></option>
		            		<?php endforeach ?>
								</select>
					    <label><b>Firmas</b></label>
					    <p>
					      	<label>
					        	<input type="checkbox" value="1" name="frm_firma_preliminar" id="frm_firma_preliminar" checked />
					        	<span><b>Con firma digital al previsualizar</b></span>
					      	</label>
					    </p>
						</div>
						<div class="input-field col s12 m6 l6">
					    <select id="frm_form_valo" name="frm_form_valo">
					      	<option value="0" id="valor_0">Predeterminado</option>
		                    <option value="1" id="valor_1">Sin Decimales</option>
		                    <option value="2" id="valor_2">Con un decimal</option>
		                    <option value="3" id="valor_3">Con dos decimales</option>
					    </select>
					    <label><b>Formato de presentaci&oacute;n de resultados</b></label>
						</div>
		    	</div>
		    	</div class="row table">
		    	<div class="input-field col s12 l12">
		    			<p>
							<label>
					            <input class="with-gap" type="radio" name="frm_plantilla" id="frm_plantilla" value="1"
					                <?php if(!empty($mensajes_aux[0]->id_plantilla)) echo $mensajes_aux[0]->id_plantilla == 1 ? 'checked':'';
					                    else echo 'checked';
					                ?>
					           />
					            <span>Plantilla estandar</span>
					    	</label>
						</p>
						<p>
							<label>
					            <input class="with-gap" type="radio" name="frm_plantilla" id="frm_plantilla" value="2" <?= $mensajes_aux[0]->id_plantilla == 2 ? 'checked':''?>/>
					            <span>Plantilla con 2 hojas</span>
					    	</label>
						</p>
					    <!--
					    <p><label>
					        <input class="with-gap" type="radio" name="frm_plantilla" id="frm_plantilla" value="2"/>
					        <span>Plantilla Frotis</span>
					    </label></p>
					    <p><label>
					        <input class="with-gap" type="radio" name="frm_plantilla" id="frm_plantilla" value="5"/>
					        <span>Plantilla Esterilidad</span>
					    </label></p> -->
					</div>
		    	</div>
				  <input type="hidden" name="plantilla_validator" id="plantilla_validator" value="0">
				  <div id="form-nueva"></div>

					<input type="hidden" name="frm_id_certificado" id="frm_id_certificado" value="<?= $id_certificado ?>"/>
					<input type="hidden" name="frm_id_muestra" id="frm_id_muestra" value="<?= $certificado->id_muestreo_detalle ?>"/>
		      <input type="hidden" name="frm_id_forma" id="frm_id_forma" value="forma_muestra_preinforme"/>
		      <input type="hidden" name="frm_id_responsable" id="frm_id_responsable" value="0"/>
		      <input type="hidden" name="funcion" value="" id="funcion"/>
					<div class="row">
						
					<?php
						$analisis = procesar_registro_fetch('muestra_tipo_analisis', 0, 0);
					?>
						<div class="col s12 l12 center">
							<br>
							<p>
							    <?php if(empty($fechasUtiles)): ?>
    								<label>
    									<input type="radio" class="with-gap" name="frm_id_procedencia" value="1" onclick="analisis_select(1);"/>
    									<span>Primer informe</span>
    								</label>
    							<?php endif ?>
								<label>
									<input type="radio" class="with-gap" name="frm_id_procedencia" value="2" onclick="analisis_select(0);" checked/>
									<span>Informe final</span>
								</label>
							</p>
							<br>
						</div>
						<?php if($certificados[0]->id_tipo_analisis_primer_informe != 0): ?>
							<div class="col s12 l6 center">
								<span>Análisis Primer Informe</span>
								<h5>
									<?php foreach($analisis as $value): ?>
										<?php if($value->id_muestra_tipo_analsis == $certificados[0]->id_tipo_analisis_primer_informe): ?>
											<?= $value->mue_nombre ?>
										<?php endif ?>
									<?php endforeach ?>
								</h5>
							</div>
							<div class="col s12 l6 center">
							<span>Análisis Informe Final</span>
								<h5>
									<?php foreach($analisis as $value): ?>
										<?php if($value->id_muestra_tipo_analsis == $certificados[0]->id_tipo_analisis_informe_final): ?>
											<?= $value->mue_nombre ?>
										<?php endif ?>
									<?php endforeach ?>
								</h5>
							</div>
						<?php else: ?>
							<div class="col s12 l12 center">
							<span>Análisis Informe Final</span>
								<h5>
									<?php foreach($analisis as $value): ?>
										<?php if($value->id_muestra_tipo_analsis == $certificados[0]->id_tipo_analisis_informe_final): ?>
											<?= $value->mue_nombre ?>
										<?php endif ?>
									<?php endforeach ?>
								</h5>
							</div>
						<?php endif ?>



						<div class="row table center">
							<div class="input-field col s12 animate fadeLeft" id="analisis_primer_informe" style="display:none">
								<select name="id_tipo_analisis_primer_informe">
									<option value="0" selected>Seleccione opción</option>
									<?php foreach($analisis as $value): ?>
										<?php
											$id_tipo_analisis = $value->id_muestra_tipo_analsis;
											$nombre = $value->mue_nombre.' - '.$value->mue_sigla;
											$checked_analisis = $value->id_muestra_tipo_analsis == $certificados[0]->id_tipo_analisis_primer_informe ? 'selected':'';
										?>
										<option value="<?= $id_tipo_analisis?>" <?= $checked_analisis ?> ><?= $nombre ?></option>
									<?php endforeach ?>
								</select>
								<label>Tipo de análisis primer informe</label>
							</div>
							<div class="input-field col s12 animate fadeLeft" id="analisis_informe_final">
								<select name="id_tipo_analisis_informe_final">
									<?php foreach($analisis as $value): ?>
										<?php
											$id_tipo_analisis = $value->id_muestra_tipo_analsis;
											$nombre = $value->mue_nombre.' - '.$value->mue_sigla;
											$checked_analisis = $value->id_muestra_tipo_analsis == $certificados[0]->id_tipo_analisis_informe_final ? 'selected':'';
										?>
										<option value="<?= $id_tipo_analisis?>" <?= $checked_analisis ?> ><?= $nombre ?></option>
									<?php endforeach ?>
								</select>
								<label>Tipo de análisis informe final</label>
							</div>
						</div>
					</div>
					
					<div id="file-upload" class="section">
						<!--Default version-->
						<div class="row section">
								<div class="col s12 m12 l12">
										<input type="file" name="file_certificado"
														id="file_certificado" class="dropify-Es"/>
								</div>
						</div>
					</div>
					
					<?php if(!empty($fechasUtiles)): ?>
						<div class="input-field col s12">
							<textarea name="complemento" class="materialize-textarea" id="complemento"></textarea>
							<label for="complemento">Complemento fechas utiles</label>
						</div>
					<?php endif ?>
					<?php if(!empty($certificado->cer_fecha_publicacion)): ?>
						<div class="input-field col s12">
							<textarea name="modificacion" class="materialize-textarea" id="modificacion"></textarea>
							<label for="modificacion">Modificación</label>
						</div>
					<?php endif ?>
		      </form>
					<div class="row">
						<div class="col s12 l12 center">
							<button style="margin: 2px 0px !important" id="guardar" class="btn blue darken-3 p-1" onClick="js_enviar(1, <?= $id_certificado ?>)"><i class="fad fa-save"></i> Guardar</button>
							<small id="campo_bttn_enviar"></small>
							<button style="margin: 2px 0px !important" id="enviar" class="btn red darken-2 p-1" onClick="js_enviar(0, <?= $id_certificado ?>)"><i class="fad fa-file-download"></i> Guardar y descargar documento</button>
							<!-- <button style="margin: 2px 0px !important" id="enviar" class="btn red darken-3 p-1" onClick="js_enviar(2, <?= $id_certificado ?>, 1)"><i class="fad fa-file-pdf"></i> previsualizar documento</button> -->
							<button style="margin: 2px 0px !important" class="btn green darken-3 p-1" onClick="js_mostrar_detalle(`card-table`, `card-detalle`);"><i class="fal fa-arrow-alt-to-left"></i> Volver atrás</button>
						</div>
					</div>
					
										
<script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/js/form-file-uploads.js"></script>
<script>
	$(() => {
		$('.dropify-Es').dropify({
			messages: {
					default: 'Arrastre y suelte un archivo aquí o haga clic.',
					replace: 'Arrastre y suelte un archivo o haga clic para reemplazar',
					remove: 'Borrar',
					error: 'Ooops, sucedió algo malo.'
			},
			error: {
					'fileSize': 'The file size is too big ({{ value }} max).',
					'minWidth': 'The image width is too small ({{ value }}}px min).',
					'maxWidth': 'The image width is too big ({{ value }}}px max).',
					'minHeight': 'The image height is too small ({{ value }}}px min).',
					'maxHeight': 'The image height is too big ({{ value }}px max).',
					'imageFormat': 'The image format is not allowed ({{ value }} only).',
					'fileExtension': 'El archivo no está permitido. Formato válido ({{ value }}).',
			}
	    });
	    select_firmas();
		aux_firmas.splice(0, aux_firmas.length);
	})
	function openFechas(fechas){
		console.log(fechas);
        var array = new Object();
		fechas.forEach(fecha => {
			array[`${fecha.id}`] = `[${fecha.dia}] ${fecha.fecha}`
		});
		Swal.fire({
			title:"Historico resultados",
			input:"select",
			inputOptions: array,
			inputPlaceholder: 'Fecha análisis',
			inputValidator: (value) => {
				return new Promise((resolve) => {
					console.log(value);
					$('.fecha_tr').hide();
					$(`#registro_${value}`).show();
					$(`#primer_${value}`).show();
					$(`#resultado_${value}`).show();
					$(`#condicion_${value}`).show();
					resolve();
				})
			}
		})
	}
	
	
	function firmas(type){
		if(type == 2){
			<?= $firmas = procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $certificados[0]->certificado_nro, 'id_mensaje_tipo', 2);
				if(!empty($firmas[0]))
					$firmas = $firmas[0];
				else
					$firmas = (object) ['id_firma' => ''];
			?>;
			return <?= json_encode($firmas) ?>
		}else{
			<?= $firmas = procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $certificados[0]->certificado_nro, 'id_mensaje_tipo', 1);
				if(!empty($firmas[0]))
					$firmas = $firmas[0];
				else
					$firmas = (object) ['id_firma' => ''];
			?>;
			return <?= json_encode($firmas) ?>
		}
	}
</script>