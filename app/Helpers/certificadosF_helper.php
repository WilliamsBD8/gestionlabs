<?php
use App\Models\Certificacion;
use Config\Services;

	function lista_resultados($id_certificado, $que_mostrar){
		$db = \Config\Database::connect();
		$certificados = new Certificacion();
		$certificados = $certificados->select('*')
			->join('muestreo', 'certificacion.id_muestreo = muestreo.id_muestreo')
			->join('usuario', 'usuario.id = muestreo.id_cliente')
			->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle')
			->join('producto', 'muestreo_detalle.id_producto = producto.id_producto')
			->join('muestra_tipo_analisis', 'muestreo_detalle.id_tipo_analisis = muestra_tipo_analisis.id_muestra_tipo_analsis')
			->where(['certificado_nro' => $id_certificado])
			->orderBy('id_certificacion', 'DESC')
			->get()->getResult();
        if (!$certificados[0]->mue_subtitulo){
            $certificados[0]->mue_subtitulo = '-xxx-';
        }
        $certificados[0]->mue_subtitulo = formatea_tildes($certificados[0]->mue_subtitulo);
        $contenido = view('funcionarios/pages/certificados', [
        	'id_certificado' => $id_certificado,
        	'certificados' => $certificados,
        	'que_mostrar' => $que_mostrar, 
        	'db' => $db,
        ]);
        return $contenido;
	}
	function cambiar_campos($type, $campo_salida, $valor, $nombre_campo_frm, $nombre_campo_bd, $tabla_update, $id_operacion){

		if($valor == '-xxx-'){//por si el valor es vacio no guarde la bandera
       		$valor='';
    	}
//     	if($type == 'date'){
//     		$valor = $valor.' '.date('H:i:s', strtotime("now"));
// 			//$valor = utf8_decode($valor);
//     	}

    	if($tabla_update == 'muestreo'){
        	$texto = "update muestreo set
                    	$nombre_campo_bd = '$valor'
                    	where id_muestreo = $id_operacion";
    	}elseif($tabla_update == 'certificacion'){
        	$texto = "update certificacion set
                    	$nombre_campo_bd = '$valor'
                    	where certificado_nro = $id_operacion";
    	}elseif($tabla_update == 'muestreo_detalle'){
        	$texto = "update muestreo_detalle set
                    	$nombre_campo_bd = '$valor'
                    	where id_muestra_detalle = $id_operacion";
    	}elseif($tabla_update == 'ensayo_vs_muestra'){
    		$texto = "update ensayo_vs_muestra set
                		$nombre_campo_bd = '$valor'
                		where id_ensayo_vs_muestra = $id_operacion";
		}

    	if(empty($valor)){
        	if($valor<>0)
         		$valor='-xxx-';
    	}
    	$db = \Config\Database::connect();
    	$result = $db->query($texto);
	    //formateamos las salida para regla
	     if (!is_numeric($campo_salida)) {
	   // 	$type == 'date' ? date('Y-m-d', strtotime($valor)):$valor;
	     	$salida = '<p class="doble_click" ondblclick="editar_campos(`'.$type.'`,`'.$campo_salida.'`,`'.$valor.'`, `'.$nombre_campo_frm.'`, `'.$nombre_campo_bd.'`, `'.$tabla_update.'` , `'.$id_operacion.'`)">'.$valor.'</p>';
    	}else{
        	$salida ="<spam style='color:blue'>Regla aplicada </spam>";
            $campo_salida = 'campo_regla_'.$campo_salida;
    	}
    	almacena_auditoria($id_operacion, $valor, $nombre_campo_bd);
    	return [$salida, $campo_salida];
	}

	function muestra_mensaje($id_mensaje, $tabla){
		if ($id_mensaje){
			$mensaje = procesar_registro_fetch($tabla, 'id_mensaje', $id_mensaje);
            // $sql_mensaje = "select * from $tabla where id_mensaje = $id_mensaje";

            // $query_mensaje = mysql_query($sql_mensaje) or die ('error '.  mysql_error().$sql_mensaje);
            // $fila_mensaje = mysql_fetch_object($query_mensaje);

            if($tabla == 'mensaje_resultado'){
                $salida = $mensaje[0]->mensaje_titulo;
            }else{
                $salida = $mensaje[0]->mensaje_detalle;
            }
	    }else{
	        $salida = "";
	    }
	  	$respuesta = ['tabla' => $tabla, 'mensaje' => $salida];
    	return $respuesta;

    
    // $respuesta->assign("campo_$tabla","innerHTML", utf8_encode($salida));
	}
	function guardar_certificado($form, $certificado){
		$db = \Config\Database::connect();
		if($form['frm_id_procedencia'] == 2){
      $sql_guardar = "update certificacion set
                    cer_fecha_informe=now(),
                    certificado_estado=5,
										id_tipo_analisis_informe_final = ".$form['id_tipo_analisis_informe_final']." 
                    where certificado_nro=".$form['frm_id_certificado'];
			$certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $form['frm_id_certificado']);
			$sql_actualizar = "update muestreo_detalle set
												id_tipo_analisis = ".$form['id_tipo_analisis_informe_final']."
												where id_muestra_detalle=".$certificado[0]->id_muestreo_detalle;
			$db->query($sql_actualizar);
	  }else{
			$aux_estado = $certificado->certificado_estado == 4 || $certificado->certificado_estado == 0 ? ', certificado_estado=4':'';
	    $sql_guardar = "update certificacion set
	                    cer_fecha_preinforme=now(),
											id_tipo_analisis_primer_informe = ".$form['id_tipo_analisis_primer_informe']."
											$aux_estado
	                    where certificado_nro=".$form['frm_id_certificado'];
	  }
	  if ($db->simpleQuery($sql_guardar)){
			$salida .= '<br>Operacion ok 222222<br>';
	  }
		$sql_formatea = "update ensayo_vs_muestra set campo_primer_informe='0' where id_muestra= ".$form['frm_id_muestra']; // formatea valores en ensayo_vs_muestra a 0
		$db->query($sql_formatea);
		if (!empty($form['primer_informe'])) {
			foreach ($form['primer_informe'] as $key => $value) {
				$sql = "update ensayo_vs_muestra set campo_primer_informe='1' where id_ensayo_vs_muestra=".$value;
				$db->query($sql);
			}
		}
		$fila_existe = procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $form['frm_id_certificado'], 'id_mensaje_tipo', $form['frm_id_procedencia']);
		if (!empty($fila_existe[0])){//existe actualiza
	        $sql = "update certificacion_vs_mensaje set
	                            id_firma = ".$form['frm_mensaje_firma'].",
	                            form_valo =  ".$form['frm_form_valo'].",
	                            id_plantilla = ".$form['frm_plantilla'].",
								complemento = '".$form['complemento']."',
								modificacion = '".$form['modificacion']."'
	                            where id_certificacion=".$form['frm_id_certificado']." and id_mensaje_tipo=".$form['frm_id_procedencia'];

	    }else{//inserta
	        $sql = "insert into certificacion_vs_mensaje (id_certificacion,id_mensaje_resultado, id_mensaje_comentario,
						 id_mensaje_tipo,id_firma,id_plantilla, complemento, modificacion, form_valo)
	                            values
				(
						".$form['frm_id_certificado'].",
						1,1,
						'".$form['frm_id_procedencia']."',
	          ".$form['frm_mensaje_firma'].",
						".$form['frm_plantilla'].",
						'".$form['complemento']."',
						'".$form['modificacion']."',
						".$form['frm_form_valo'].")";

	    }
	    if ($db->simpleQuery($sql)) {
			$response = [
	            'html' => '<i class="fas fa-check"></i>&nbsp Reporte actualizado ',
	            'class' => 'blue darken-3',
	            'mensaje' => $sql
	        ];
		}else{
			$response = [
	            'html' => '<i class="fas fa-times"></i>&nbsp Error actualizando reporte ',
	            'class' => 'red darken-3',
	            'mensaje' => $sql
	        ];
		}
		return $response;
	}
	function previsualizar($form){
		$db = \Config\Database::connect();
		$certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $form['frm_id_certificado']);
		$certificado = $certificado[0];
		//formateo de muestreo
        $sql = "select * from muestreo where id_muestreo=$certificado->id_muestreo  group by id_muestreo";
        $muestreo = $db->query($sql)->getResult();
        $muestreo = $muestreo[0];
	    // return $muestreo;
    	//formateo de cliente
        $cliente = procesar_registro_fetch('usuario ', 'id', $muestreo->id_cliente);
        if($form['frm_id_procedencia'] == 1){//preinformes
            $aux_mensaje='Primer informe';
            if($certificado->cer_fecha_preinforme == '0000-00-00 00:00:00') $aux_fecha_informe=date("Y-m-d H:i:s"); // True
            else $aux_fecha_informe=$certificado->cer_fecha_preinforme;
        }else{
            $aux_mensaje='Informe';
            if($certificado->cer_fecha_informe == '0000-00-00 00:00:00') $aux_fecha_informe=date("Y-m-d H:i:s"); // True
            else $aux_fecha_informe=$certificado->cer_fecha_informe;
        }
         //formatemos el subtitulo de la empresa
        if($muestreo->mue_subtitulo){
            //$fila_muestreo->mue_subtitulo = str_replace("Ã±", "ñ", $fila_muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = formatea_tildes($muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = ' - '.$muestreo->mue_subtitulo;
            
        }
        //recortar fecha
        $certificado->cer_fecha_analisis = recortar_fecha($certificado->cer_fecha_analisis, 1);

        //BUSCAMOS EL MENSAJE DE TIPO DE MUESTERO
        //SE TOMARA EL DEL PRIMER REGISTRO YA QUE PUEDE TENER ASOCIADO VARIOS PRODUCTOS
        $fila_detalle_para_tipo_muestreo = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
        $fila_detalle_para_tipo_muestreo = $fila_detalle_para_tipo_muestreo[0];

        //formatemos la fecha de analisis
        $fecha_analisis = recortar_fecha($muestreo->mue_fecha_muestreo,1);
        return [
	    	'db' => $db,
	    	'certificado' => $certificado,
	    	'cliente' => $cliente[0],
	    	'muestreo' => $muestreo,
	    	'aux_fecha_informe' => $aux_fecha_informe,
	    	'aux_mensaje' => $aux_mensaje,
	    	'fecha_analisis' => $fecha_analisis,
	    	'fila_detalle_para_tipo_muestreo' => $fila_detalle_para_tipo_muestreo,
	    	'plantilla' => $form['frm_plantilla'],
	    	'form_entrada' => $form
	    ];        
	}
	function presentar_preinforme($form){
		$db = \Config\Database::connect();
		$certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $form['frm_id_certificado']);
		$certificado = $certificado[0];
		if($form['frm_id_procedencia'] == 2){
			if($certificado->cer_fecha_publicacion > '0000-00-00 00:00:00')
				$aux_mensaje_certificado = ", id_mensaje = '".$form['frm_mensaje_resultado']."' ";
			else
				$aux_mensaje_certificado = '';
        	$sql_guardar = "update certificacion set
                    cer_fecha_informe=now(),
                    certificado_estado=5
                    ".$aux_mensaje_certificado."
                    where certificado_nro= ".$form['frm_id_certificado'];
	    }else{
	        $sql_guardar = "update certificacion set
	                    cer_fecha_preinforme=now(),
	                    certificado_estado=4,
	                    where certificado_nro=".$form['frm_id_certificado'];
	    }
	   // echo $sql;
	    if ($db->simpleQuery($sql_guardar)){
			$salida .= '<br>Operacion ok 222222<br>';
	    }
	    $certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $form['frm_id_certificado']);
		$certificado = $certificado[0];
		$fila_existe = procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $form['frm_id_certificado'], 'id_mensaje_tipo', $form['frm_id_procedencia']);
		if (!empty($fila_existe[0])){//existe actualiza
	        $sql = "update certificacion_vs_mensaje set
	                            id_mensaje_resultado ='".$form['frm_mensaje_resultado']."', 
	                            id_mensaje_comentario ='".$form['frm_mensaje_observacion']."',
	                            id_firma = ".$form['frm_mensaje_firma'].",
	                            id_plantilla = ".$form['frm_plantilla']." ,
	                            form_valo =  ".$form['frm_form_valo']."
	                            where id_certificacion=".$form['frm_id_certificado']." and id_mensaje_tipo=".$form['frm_id_procedencia'];

	    }else{//inserta
	        $sql = "insert into certificacion_vs_mensaje (id_certificacion, id_mensaje_resultado, id_mensaje_comentario, id_mensaje_tipo,
	                            id_firma, id_plantilla, form_valo)
	                            values
			(".$form['frm_id_certificado'].",'".$form['frm_mensaje_resultado']."', '".$form['frm_mensaje_observacion']."', '".$form['frm_id_procedencia']."',
	                    ".$form['frm_mensaje_firma'].",".$form['frm_plantilla'].",".$form['frm_form_valo'].")";

	    }
	    if ($db->simpleQuery($sql)) {
		    $salida .= "<br>Operacion ok 11111";
		}
		//formateo de muestreo
        $sql = "select * from muestreo where id_muestreo=$certificado->id_muestreo  group by id_muestreo";
        $muestreo = $db->query($sql)->getResult();
        $muestreo = $muestreo[0];
	    // return $muestreo;
    	//formateo de cliente
        $cliente = procesar_registro_fetch('usuario ', 'id', $muestreo->id_cliente);
        if($form['frm_id_procedencia'] == 1){//preinformes
            $aux_mensaje='PRELIMINAR';
            if($certificado->cer_fecha_preinforme == '0000-00-00 00:00:00') $aux_fecha_informe=date("Y-m-d H:i:s"); // True
            else $aux_fecha_informe=$certificado->cer_fecha_preinforme;
        }else{
            $aux_mensaje='REPORTE DE ENSAYO 1';
            if($certificado->cer_fecha_informe == '0000-00-00 00:00:00') $aux_fecha_informe=date("Y-m-d H:i:s"); // True
            else $aux_fecha_informe=$certificado->cer_fecha_informe;
        }
         //formatemos el subtitulo de la empresa
        if($muestreo->mue_subtitulo){
            //$fila_muestreo->mue_subtitulo = str_replace("Ã±", "ñ", $fila_muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = formatea_tildes($muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = ' - '.$muestreo->mue_subtitulo;
            
        }
        //recortar fecha
        $certificado->cer_fecha_analisis = recortar_fecha($certificado->cer_fecha_analisis, 1);

        //BUSCAMOS EL MENSAJE DE TIPO DE MUESTERO
        //SE TOMARA EL DEL PRIMER REGISTRO YA QUE PUEDE TENER ASOCIADO VARIOS PRODUCTOS
        $fila_detalle_para_tipo_muestreo = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
        $fila_detalle_para_tipo_muestreo = $fila_detalle_para_tipo_muestreo[0];

        //formatemos la fecha de analisis
        $fecha_analisis = recortar_fecha($muestreo->mue_fecha_muestreo,1);
        return [
	    	'db' => $db,
	    	'certificado' => $certificado,
	    	'cliente' => $cliente[0],
	    	'muestreo' => $muestreo,
	    	'aux_fecha_informe' => $aux_fecha_informe,
	    	'aux_mensaje' => $aux_mensaje,
	    	'fecha_analisis' => $fecha_analisis,
	    	'fila_detalle_para_tipo_muestreo' => $fila_detalle_para_tipo_muestreo,
	    	'plantilla' => $form['frm_plantilla'],
	    	'form_entrada' => $form,
	    ];        
	}
	function presentar_preinforme2($nro_informe, $aux_tipo_documento=1, $id_rol=0, $pdf=false){
		$db = \Config\Database::connect();
		$certificados = procesar_registro_fetch('certificacion', 'certificado_nro', $nro_informe);
		//formateo de muestreo
        	$sql = "select * from muestreo where id_muestreo=".$certificados[0]->id_muestreo."  group by id_muestreo";
        	$muestreo = $db->query($sql)->getResult();
        	$muestreo = $muestreo[0];
    	//formateo de cliente
        	$cliente = procesar_registro_fetch('usuario', 'id', $muestreo->id_cliente);
        //formateo de mensaje
        if($aux_tipo_documento==0){//preinformes
            $aux_mensaje='PRELIMINAR';
            $aux_fecha_informe=$certificados[0]->cer_fecha_preinforme;
            $tipo_mensajes = 1;
        }else{
            $aux_mensaje='REPORTE DE ENSAYO 3';
            $aux_fecha_informe=$certificados[0]->cer_fecha_informe;
            $tipo_mensajes = 2;
        }
        //BUSCAMOS EL MENSAJE DE TIPO DE MUESTERO
        //SE TOMARA EL DEL PRIMER REGISTRO YA QUE PUEDE TENER ASOCIADO VARIOS PRODUCTOS
        $detalle_para_tipo_muestreo = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificados[0]->id_muestreo_detalle);

        //formatemos la fecha de analisis
        $fecha_analisis = recortar_fecha($muestreo->mue_fecha_muestreo,1);

        //formatemos el subtitulo de la empresa
        if($muestreo->mue_subtitulo){
            //$fila_muestreo->mue_subtitulo = str_replace("Ã±", "ñ", $fila_muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = formatea_tildes($muestreo->mue_subtitulo);
            $muestreo->mue_subtitulo = ' - '.$muestreo->mue_subtitulo;
        }
        $certificados[0]->cer_fecha_analisis = recortar_fecha($certificados[0]->cer_fecha_analisis, 1);
        $contenido = view('views_mpdf/preinforme2', [
        	'certificado' => $certificados[0],
        	'aux_mensaje' => $aux_mensaje,
        	'cliente' => $cliente[0],
        	'muestreo' => $muestreo,
        	'fecha_analisis' => $fecha_analisis,
        	'detalle_para_tipo_muestreo' => $detalle_para_tipo_muestreo[0],
        	'aux_fecha_informe' => $aux_fecha_informe,
        	'tipo_mensajes' => $tipo_mensajes,
        	'db' => $db,
        	'pdf' => $pdf
        ]);
		if(!$pdf){
	        $botones = '
	        	<br>
	        	<hr>
	        	<input type="hidden" name="certificado_nro" value="'.$certificados[0]->certificado_nro.'" />
	        	<input type="hidden" name="que_mostrar" value="'.$aux_tipo_documento.'" />
	        	<input type="hidden" name="user_rol_id" value="'.session('user')->usr_rol.'" />
	        	<input type="hidden" name="funcion" value="descargar" />
				<div class="row">
					<div class="col s12 m6 l12">
						<button class="btn red darken-4" onClick="descargar()"><i class="far fa-file-pdf"></i> Descargar documento</button>';
						if($aux_tipo_documento==1 &&  session('user')->usr_rol<>10){//Informes
				            //ajuste 1 de febrero de 2019
				            // si no esta autorizado mostramos el link de autorizacion
				            // si esta autorizado mostrara quien autoriza la publicacion y mostrara el link de facturacion
				            //si esta facturado quien lo facturo
				            if($certificados[0]->cer_usuario_publica){
				                //verificamos si esta facturado
				                if($certificados[0]->cer_fecha_facturacion > '0000-00-00 00:00:00'){//fue ya fa facturado
				                    $botones .='';
				                }else{//mostramos boton para facturar
				                    $botones .=' <a href="#" class="btn green" onclick="certificado_facturacion('.$certificados[0]->certificado_nro.')"><i class="fas fa-dollar-sign"></i> Indicar que ya se factur&oacute;</a>';
				                }
				                //buscamos el usurio e indicamos que lo autorizo
				                //$usr_publica = procesar_registro_fetch('cms_users', 'id', $fila_certificado->cer_usuario_publica); 
				                //$salida .='<br> Usuario que autorizo publicaci&oacute;n: <b>'.$usr_publica->usr_usuario.'</b> ';
				            }else{
				                $botones .=' <a href="#" class="btn amber darken-4" onclick="certificado_autorizacion('.$certificados[0]->certificado_nro.')"><i class="far fa-thumbs-up"></i> Autorizar Publicaci&oacute;n</a>';
				            }
						}
			$botones .='
						<button class="btn green darken-3" onClick="js_mostrar_detalle(`card-table`, `card-detalle`)">Volver atrás</button>
					</div>
				</div>';

			$contenido .= $botones;
		}
        return $contenido;
	}
	function certificado_facturacion($certificado_nro){
		$db = \Config\Database::connect();
		$sql3 = "update certificacion set                            
                            cer_fecha_facturacion=now()
                            where certificado_nro =$certificado_nro";

	    if ($db->simpleQuery($sql3)){
	        almacena_auditoria($certificado_nro, 'facturacion',session('user')->id);
	    	$salida = [
	    		'html' => 'Se indico al sistema que la factura se creo',
	    		'icon' => 'success'
	    	];
	    }else{
	    	$salida = [
	    		'html' => 'Error al registrar la factura',
	    		'icon' => 'warning'
	    	];
	    }
	    return $salida;
	}
	function certificado_autorizacion($certificado_nro){
		$db = \Config\Database::connect();
		$fila =  procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $certificado_nro, 'id_mensaje_tipo', 2);
		$mensajes = procesar_registro_fetch('mensaje_resultado', 'id_mensaje', $fila[0]->id_mensaje_resultado);
    	$sql3 = "update certificacion set                            
                            cer_fecha_publicacion=now(),
                            cer_usuario_publica= ".session('user')->id." ,
                            id_mensaje=".$fila[0]->id_mensaje_resultado."
                            where certificado_nro =$certificado_nro";

        if ($db->simpleQuery($sql3)){
            almacena_auditoria($certificado_nro, 'autorizaciones','cer_usuario_publica');
	        //1 
		    $certificado =  procesar_registro_fetch('certificacion', 'certificado_nro', $certificado_nro);
		    $muestreo =  procesar_registro_fetch('muestreo', 'id_muestreo', $certificado[0]->id_muestreo);
		    //2
		    $usuario =  procesar_registro_fetch('usuario', 'id', $muestreo[0]->id_cliente);
		    if($usuario[0]->emails != 'Permitido'){
    		    $sAsunto = "Asbioquim S.A.S ha generado el informe ".$certificado_nro;
    		    $sTexto = "Asbioquim S.A.S Laboratorios ha generado el informe ".$certificado_nro." y se encuentra disponible  en el siguiente link <br> 
    		        <a href='".base_url(['certificado', 'view', $certificado[0]->clave_documento_final])."' target='_blank'>Ver archivo</a>
    		        <br> si no funciona copie y pegue el link en el navegador <br>
    		        ".base_url(['certificado', 'view', $certificado[0]->clave_documento_final]);
    		    $email = \Config\Services::email();
                $email->setFrom(!empty(configInfo()['email']) ? configInfo()['email'] : 'iplanet@iplanetcolombia.com', !empty(configInfo()['name_app']) ? configInfo()['name_app'] : 'IPlanet Colombia S.A.S');
                $email->setTo($usuario[0]->email);
                $email->setSubject($sAsunto);
                $email->setMessage($sTexto);
                $email->send();
		        $html = 'Informe Autorizado. Se enviarà link de descarga al correo <b>'.$usuario[0]->email.'</b>';
		    }else{
		        $html = 'Informe Autorizado. El informe esta listo para ser enviado de forma masiva';
		    }
        	$salida = [
        		'html' => $html,
        		'icon' => 'success',
        		'div' => '#div_resultado_'.$certificado_nro,
                'mensaje' => $mensajes[0]->mensaje_titulo
        	];
        }else{
            $salida = [
	    		'html' => $fila[0],
	    		'icon' => 'warning',
	    		'div' => '#div_resultado_'.$certificado_nro,
                'mensaje' => ''
	    	];
        }
        return $salida;
	}
?>