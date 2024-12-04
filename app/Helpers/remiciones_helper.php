<?php
use App\Models\Producto;
use App\Models\Parametros;
use App\Models\Ensayo;
use App\Models\Muestreo;
use App\Models\MuestreoDetalle;

	function muestra_tabla($producto){
		$data = new Producto();
		$producto = $data
			->join('norma', 'producto.id_norma = norma.id_norma')
			->where(['pro_nombre' => $producto])->get()->getResult();
		if(empty($producto[0]))
			return '';
		$tabla = '
			<div class="tabla-productos">
				<hr>
				<table>
					<thead>
						<tr>
							<th colspan="2"><h4>Lista de parametros asociados a este producto</h4></th>
						</tr>
						<tr>
							<th>Producto x1: '.$producto[0]->pro_nombre.'</th>
							<th>Norma: '.$producto[0]->nor_nombre.'</th>
						</tr>
					</thead>
					<tbody>
						<table class="striped centered">
							<thead>
								<tr>';
									$ensayo = procesar_registro_fetch('ensayo', 'id_producto', $producto[0]->id_producto);
									foreach($ensayo as $key => $value){
										$parametro = procesar_registro_fetch('parametro', 'id_parametro', $value->id_parametro);
										if($parametro[0]->par_estado == 'Inactivo')
											continue;
		                                $tabla .='<th><b>'.$parametro[0]->par_nombre.'</b></th>';
									}

					$tabla 	.=	'</tr>
							</thead>
							<tbody>
								<tr>';
								foreach($ensayo as $key => $value){
									$parametro = procesar_registro_fetch('parametro', 'id_parametro', $value->id_parametro);
									if($parametro[0]->par_estado == "Inactivo")
	                                    continue;
	                                $tabla .= '<td>'.$value->med_valor_min.' - '.$value->med_valor_max.'</td>';
								}
				$tabla .= 	'	</tr>
								<tr>';

								$aux_cols = 0;
								foreach($ensayo as $value){
									$parametro = procesar_registro_fetch('parametro', 'id_parametro', $value->id_parametro);
									if($parametro[0]->par_estado == "Inactivo")
	                                    continue;
	                                $checked = "checked";
	                                $tabla .='<td  class="action">
	                                		<label>
		                                		<input type="checkbox" name="frm_chk_'.$value->id_ensayo.'" id="frm_chk_'.$value->id_ensayo.'" '.$checked.'/>
		                                		<span></span>
	                                		</label>
	                                	</td>';
                        			$aux_cols++;
								}
				$mensaje = "Agregar a lista";
               	$inputIdMuestraDetalle = '';

				$tabla .=	'	</tr>
								<tr><td colspan="'.$aux_cols.'">&nbsp;</td></tr>
								<tr>
			                        <td colspan="'.$aux_cols.'" class="action">
			                        	<b>Unidad de Medida</b><br>
			                        	<label>
			                        		<input  type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="solida" checked>
			                        		<span>S&oacute;lidas</span>
	                                	</label>
			                        	<label>
			                        		<input  type="radio" id="frm_unidad_parametro" name="frm_unidad_parametro" value="liquida">
			                        		<span>Liquidas</span>
	                                	</label>
			                        	<br><br>
			                        </td>
			                    </tr>
			                    <tr>
			                        <td colspan="'.$aux_cols.'" class="action">
			                        	<label>
			                        		<input type="checkbox" value="1" name="frm_bandera_certificado" id="frm_bandera_certificado" checked>
			                        		<span>Un certificado</span>
	                                	</label>
	                                </td>
			                    </tr>
			                    <tr>
					                <td colspan="'.$aux_cols.'" class="action"><div id="campo_botton_agregar">
					                	<button id="btn-muestreo-form" class="btn gradient-45deg-purple-deep-orange border-round">'.$mensaje.'</button>
					                	<input type="hidden" name="frm_id_forma" id="frm_id_forma" value="frm_form_muestra"/>
					                	'.$inputIdMuestraDetalle.'
					                </td>
					            </tr>
							</tbody>
						</table>
					</tbody>
				</table>
			</div>
		';
	    return $tabla;
	}

	function detalles_tabla($forms){
		$db = \Config\Database::connect();
		$forms['frm_fecha_recepcion'] = $forms['frm_fecha_muestra'];
		$salida = '';
		if ($forms['frm_id_remision'] == 0){
			$data = [
				'id_cliente' 			=> $forms['frm_nombre_empresa2'], 
				'mue_estado' 			=> $forms['frm_estado_remision'], 
				'mue_fecha_muestreo' 	=> $forms['frm_fecha_muestra'].' '.$forms['frm_hora_muestra'], 
				'mue_fecha_recepcion' 	=> $forms['frm_fecha_recepcion'], 
				'mue_fecha_analisis' 	=> $forms['frm_fecha_analisis'], 
				'mue_fecha_informe' 	=> $forms['frm_fecha_informe'], 
				'mue_entrega_muestra' 	=> $forms['frm_entrega'], 
				'mue_recibe_muestra' 	=> $forms['frm_recibe'], 
				'mue_responsable_op' 	=> $forms['frm_responsable'], 
				'mue_observaciones' 	=> $forms['frm_observaciones'], 
				'mue_subtitulo' 		=> $forms['frm_nombre_empresa_subtitulo'] 
			];
			$muestreo = new Muestreo();
			$muestreo->insert($data);
			$forms['frm_id_remision'] = $muestreo->getInsertID();
		}else{
			//ya existe x consiguiente se actualizan valores de encabezado
			$data_update = [
				'id_cliente' 			=> $forms['frm_nombre_empresa2'],
				'mue_fecha_muestreo' 	=> $forms['frm_fecha_muestra'].' '.$forms['frm_hora_muestra'], 
				'mue_fecha_recepcion' 	=> $forms['frm_fecha_recepcion'],
				'mue_entrega_muestra' 	=> $forms['frm_entrega'], 
				'mue_recibe_muestra' 	=> $forms['frm_recibe'],
				'mue_observaciones' 	=> $forms['frm_observaciones'], 
				'mue_subtitulo' 		=> $forms['frm_nombre_empresa_subtitulo'] 
			];
			$muestreo = new Muestreo();
            $muestreo->set($data_update)
            	->where(['id_muestreo' => $forms['frm_id_remision']])
            	->update();
		}
		$anio_= date("y");
        $tabla = 'muestreo_detalle where ano_codigo_amc='.$anio_;
        $aux_codigo_amc = auto_incrementar('id_codigo_amc',$tabla);

        if ($forms['frm_bandera_certificado'] == 1) //un certificado
            $aux_nro_certificado = auto_incrementar('certificado_nro','certificacion');
        else
            $aux_nro_certificado = auto_incrementar('certificado_nro','certificacion')-1;

        $id_producto = procesar_registro_fetch('producto', 'pro_nombre', $forms['frm_producto']);
        $forms['frm_producto'] = $id_producto[0]->id_producto;
        
        $data_detalle = [
         	'id_tipo_analisis'				=> $forms['frm_analisis'],
			'id_codigo_amc'					=> $aux_codigo_amc,
			'mue_procedencia'				=> $forms['frm_procedencia'],
			'mue_identificacion'			=> $forms['frm_identificacion'],
			'mue_lote'						=> $forms['frm_lote'],
			'mue_fecha_produccion'			=> $forms['frm_fecha_produccion'],
			'mue_fecha_vencimiento'			=> $forms['frm_fecha_vencimiento'],
			'mue_temperatura_muestreo'		=> $forms['frm_tmp_muestreo'],
			'mue_temperatura_laboratorio'	=> $forms['frm_tmp_recepcion'],
			'mue_condiciones_recibe'		=> $forms['frm_condiciones_recibido'],
			'mue_cantidad'					=> $forms['frm_cantidad'],
			'id_producto'					=> $forms['frm_producto'],
			'mue_momento_muestreo'			=> $forms['frm_momento_muestreo'],
			'mue_parametro'					=> $forms['frm_parametro'],
			'mue_area'						=> $forms['frm_area'],
			'mue_tipo_muestreo'				=> $forms['frm_tipo_muestreo'],
			'ano_codigo_amc'				=> $anio_,
			'mue_adicional'					=> $forms['frm_adicional']
			// 'mue_unidad_medida'				=> $forms['frm_unidad_parametro'],
        ];
        $muestreo_detalle = new MuestreoDetalle();
        $muestreo_detalle->insert($data_detalle);
        $salida .= "<br><font color=red>Muestra detalle correctamente</font>";
        $aux_id_muestra_detalle = $muestreo_detalle->getInsertID();

        $salida .= "<br><font color=red>sale 2</font>";

        $ensayo = new Ensayo();
        $ensayo_result = $ensayo->where(['id_producto' => $forms['frm_producto']])
        			->get()->getResult();
        foreach($ensayo_result as $recordSet){
        	if(isset($forms['frm_chk_'.$recordSet->id_ensayo])){
        		//si exite valor
				// confirmamos que si fue seleccionada la bandera de eliminar resultados
				// si fue seleccionada debemos validar que no existan el registro de resultados
				if($forms['frm_elimina_resultado']<>'SI'){// ajuste del 6 de septiembre de 2017
					$aux_existe_resultados = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $recordSet->id_ensayo, 'id_muestra', $aux_id_muestra_detalle);
					if(!$aux_existe_resultados->id_ensayo){
						$sql_ensayo = "insert into ensayo_vs_muestra (id_ensayo, id_muestra)
                                            values ($recordSet->id_ensayo, $aux_id_muestra_detalle)";
						if ($db->simpleQuery($sql_ensayo)){
						    $salida .= "<br><font color=red>ensayo_vs_muestra correctamente</font>";
						}
						
				 	}
				}else{					 
					$sql_ensayo = "insert into ensayo_vs_muestra (id_ensayo, id_muestra)
                                            values ($recordSet->id_ensayo, $aux_id_muestra_detalle)";
					if ($db->simpleQuery($sql_ensayo)){
					    $salida .= "<br><font color=red>ensayo_vs_muestra correctamente</font>";
					}
				}
        	}else{//si no esta seleccionado lo eliminamos
    			// $texto_elimina_enayo = "delete from ensayo_vs_muestra where id_muestra=$aux_id_muestra_detalle and id_ensayo=$recordSet->id_ensayo ";
				// if ($db->simpleQuery($texto_elimina_enayo)){
				//     $salida .= "<br><font color=red>elimina ensayo_vs_muestra correctamente</font>";
				// }
				continue;
         	}
        }
        $aux_clave_preinforme = generaClave();
        $aux_clave_informe = generaClave();
		
        /*
         * mejora quitar de los encriptados los ceros, letra o . la letra l el numero uno y el punto
         */
        $aux_clave_preinforme = str_replace("1", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("l", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("O", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("0", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("k", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("K", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("2", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("z", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("6", "", $aux_clave_preinforme);
        $aux_clave_preinforme = str_replace("G", "", $aux_clave_preinforme);

        //$aux_clave_preinforme = str_replace($search, $replace, $subject);
        $aux_clave_informe = str_replace("1", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("l", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("O", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("0", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("k", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("K", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("2", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("z", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("6", "", $aux_clave_informe);
        $aux_clave_informe = str_replace("G", "", $aux_clave_informe);


        $sql_certificacion = "insert into certificacion
                            (id_muestreo, id_muestreo_detalle, clave_documento_pre, clave_documento_final, 
                            certificado_nro, cer_fecha_analisis, cer_fecha_preinforme, cer_fecha_informe)
                                values
                           ($forms[frm_id_remision], $aux_id_muestra_detalle, '$aux_clave_preinforme','$aux_clave_informe',
                            $aux_nro_certificado,'0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00')";
        if ($db->simpleQuery($sql_certificacion)){
             $salida .= "<br><font color=red>Muestra detalle correctamente</font>";
        }
    	$salida = imprime_detalle_muestras($forms['frm_id_remision']);

		return ['tabla' => $salida['tabla'], 'frm_id_remision' => $forms['frm_id_remision'], 'boton' => $salida['boton']];
	}

	function guardar_remicion($form){
		$muestreo = new Muestreo();
		$muestreo->set([
			'mue_estado'			=> 1,
			'mue_recibe_muestra'	=> $form['frm_recibe'],
			'mue_responsable_op'	=> $form['frm_entrega'],
			'mue_observaciones' 	=> $form['frm_observaciones']
		])->where(['id_muestreo' => $form['frm_id_remision']])->update();
		return 'Remicion creada con exito.';
	}