<?php
use App\Models\Certificacion;
use App\Models\MuestreoDetalle;
use Config\Services;

	function max_valor($nro_certificado){
		$db = \Config\Database::connect();
	    $texto= "select max(form_valo)  valor from certificacion_vs_mensaje where  id_certificacion='$nro_certificado'";
	    $salida = $db->query($texto)->getResult();
	    return $salida[0]->valor;
	}

	function formateo_valores($valor, $formato){
	    // el formateo solo aplica para valores que tiene una coma
	    // y para formato verdadero
	    $valor =  str_replace(",",".",$valor);
	    
	    if(is_numeric($valor) || is_float($valor) ) {
	        
	    if($formato){
	        if($formato==1){
	           $valor = number_format($valor, 0);
	        }elseif($formato==2){
	            $valor = number_format($valor, 1);
	        }elseif($formato==3){
	            $valor = number_format($valor, 2);
	        }   
	    }
	    
	    }
	    $valor =  str_replace(".",",",$valor);
	    return $valor;
	}
	function recortar_fecha($fecha_a_recortar,$separador=0){
		$matriz_fecha = str_split($fecha_a_recortar, 10);
        if($separador==0) $matriz_fecha[0]=str_replace("-","/",$matriz_fecha[0]);
		return $matriz_fecha[0];
	}
	
	function formatea_tildes($aux){
        
	    $array_errores  = array("Ã±","Ã‘", "Ã¡",  "Ã©", "Ã­",  "Ã³",  "Ãº", "Ã", "Ã‰", "Ã", "Ã“", "Ãš"); 
	    $array_corregido= array("ñ","Ñ", "á", "é", "í", "ó", "ú","Á", "É","Í", "Ó", "Ú");
	    $aux = $aux;
	    $aux = str_replace($array_errores, $array_corregido, $aux);
	    return $aux;
	}
	function formatea_mh_parcial($aux_ml,$who){//no aplica para asbioquim
	    if($who == "mohos"){
	        $aux_m = $aux_ml;
	    
	        if ( is_numeric($aux_m) ){
	                $aux_m =  $aux_m."M";
	        }elseif( preg_match("/</", $aux_m) ){
	                $aux_m =  $aux_m."M";
	        }else{
	                $aux_m =  "";
	        }
	        return $aux_m;  
	                    
	    }else{//es levadura
	        $aux_l = $aux_ml;
	        
	         if ( preg_match("/</", $aux_l) ){
	            $aux_l =  $aux_l."L"; 
	        }elseif( is_numeric($aux_l) ){
	            $aux_l = "+ ". $aux_l."L";
	        }else{
	            $aux_l =  "";
	        }
	        return $aux_l;  
	    }
	}
	function calcula_mh($valor){////no aplica para asbioquim
	    $porciones = explode(";", $valor);
	    
	    if( $porciones[1] > 0 )
	        //return  $porciones[1];
	        return ($porciones[0] + $porciones[1]) / 2;
	    else
	       /* if( is_null ($porciones[1]) )
	            return $porciones[1];
	        else*/
	        return $porciones[0] ;
	}
	
	// reglas recuento
            // 1 si no hay dilucion se tomara el primer campo y sera un calculo directo. 
            // 2 si en el primer campo es mayor a 300 se multiplicara por la dilucion
            // 3 si hay una lectura se divide por la dilucion seleccionada 
            // 4 si hay dos diluciones se suman y de divide por la dilucion y x  1.1
            
            // 5 si el resultado es menor a 40 se presenta <40
            // 6 si el resultado es mayor a (2730 ) o en la caja 300 se presenta >3,0 x la dilucion expresada en 10exp
            // 7 si el resultado es menor a 999 se deja asi aplicando redondeo
            // 8 si el resultado es mayo a 1000 se expresada en 10exp
            
            // 9 reglas cuando se ingresa un cero y la dilucion es;
            //   sin dilucion = <1 
            //   1 dilucion = <10
            //   2 dilucion = <100
            //   3 dilucion = Error de dilución
            //   4 dilucion = Error de dilución
    
    /*
    funcion que calcula recuentos aplicando las reglas anterior mensionadas
    $campo_lectura          = si la lectura es del primer campo o del segundo
    $valor                  = valor ingresado en el campo
    $dilucion               = dilucion seleccionada por el analista
    $id_ensayo_vs_muestra   = campo identificador del resultado
    $id_tecnica             = tecnica para manejar excepciones a los resultados
    
    */
    function calcula_recuentos($campo_lectura, $valor, $dilucion, $id_ensayo_vs_muestra, $id_tecnica=0 ){
        
        $porciones = explode(";", $valor);
        $valor_1 = ''; 
        $valor_2 = '';
         
        if( $porciones[1] > 0 ){ 
            $valor_1 = $porciones[0]; 
            $valor_2 =  $porciones[1];
        }else{
            $valor_1 = str_replace(";", "", $valor);
        }
        
        // salida de dos campos para empatar con el sistema 
        $salida[0] = ''; //aux_guarda_resultado_mensaje
        $salida[1] = ''; //mensaje
        $salida[2] = 'SI'; //bandera si guarda resultado
        
	    
	    if($campo_lectura==1){//calculos para el primer campo
	        
	        if (is_numeric ($valor_1)) {
            
                almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                almacena_dilucion($id_ensayo_vs_muestra, $dilucion);
                //se evalua info para calculos directos
                //si no se toma calculos para diluciones
                if($id_tecnica == 29  || $id_tecnica == 28 ){ // 28 Escobillon 29 Sedimentacion
                
                    //menejo de cero
                    if($valor_1 == 0 &&  $id_tecnica == 28){
                        $valor2 = '<1';
                    }
                    //manejo numero mayor a 300
                    elseif($valor_1 >= 300){
                        $valor2 = ">300";
                    }else{
                        $valor2 = $valor_1;
                    }
                    $salida[0].='-NMC2-';
                    $salida[1] = $valor2;//.' ->1mh ';
                }else{
            
                    //formateo de diluciones
                    switch ($dilucion){
                        case 6: //Dilución1y2
                        case 7: //Dilución1y3
                        case 8: //Dilución1y4
                            $dilucion = 1;
                            break;
                        case 9: //Dilución2y1
                        case 10: //Dilución2y3
                        case 11: //Dilución2y4
                            $dilucion = 2;
                            break;
                        case 12: //Dilución3y1
                        case 13: //Dilución3y2
                        case 14: //Dilución3y4
                            $dilucion = 3;
                            break;
                        case 15: //Dilución4y1
                        case 16: //Dilución4y2
                        case 17: //Dilución4y3
                            $dilucion = 4;
                            break;
                        default: //para los demas casos
                            $dilucion = $dilucion;
                    }
                    
                    if ($dilucion == 5){ // selecciono sin dilucion
                            
                        if($valor_1 == 0 && $id_tecnica <> 29){ // para sedimentación no aplica ya que es calculo directo 
                            $valor_1 = ' < 1';
                        }
                            
                        $valor2 = $valor_1;
                        //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                        $salida[0].='-NMC2-';
                        $salida[1] = $valor2;//.' ->1mh ';
                
                    }elseif($valor_1 >= 300){
                        
                        $valor2 = redondeo_asbioquim($valor_1, $valor_1, $dilucion);
                        //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                        $salida[0].='-NMC2-';
                        $salida[1] = $valor2;//.' ->2mh ';
                
                    }else{
                    // comprobamos si existe un segundo dato
                            
                        //$fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        //if($fila_resultado_1[0]->resultado_analisis2){//existe un segundo resultado
                        if( $valor_2 ){//existe un segundo resultado
                                
                            $valor2 = round( ( $valor_1 + $valor_2 )/ ( 1.1 * valores_dilucion($dilucion) ) ); 
                            //$valor2_borrar = $valor2;
                            $valor2 = redondeo_asbioquim($valor2, $valor_2, $dilucion);  
                                
                            //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2.' M');
                            $salida[0].='-NMC2-';
                            $salida[1] = $valor2;//.' ->3mh '. $valor2_borrar.' d '.$dilucion;
                
                        }else{// no existe el segundo campo
                            
                            $valor2 = round( $valor_1 / valores_dilucion($dilucion) ); 
                            $valor2 = redondeo_asbioquim($valor2, $valor_1, $dilucion);
                                
                            //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $salida[0].='-NMC2-';
                            $salida[1] = $valor2;//.' ->4mh '.$dilucion;
                        }
                            
                    }
                }
            }else{
                        
                    $salida[1] = "Valor no numerico 1".$no.' ->5mh '.' -->'.$valor_1;
                    $salida[0] = '-NMC2-';
                    $salida[2] = 'NO';
            }
        }
        else{//SEGUNDO CAMPO
        
            if (is_numeric ($valor_1)) {
            
                almacena_segundo_campo($id_ensayo_vs_muestra, $valor);
                almacena_dilucion($id_ensayo_vs_muestra, $dilucion);
                //se evalua info para calculos directos
                //si no se toma calculos para diluciones
                if($id_tecnica == 29  || $id_tecnica == 28 ){ // 28 Escobillon 29 Sedimentacion
                
                    //menejo de cero
                    if($valor_1 == 0 &&  $id_tecnica == 28){
                        $valor2 = '<1';
                    }
                    //manejo numero mayor a 300
                    elseif($valor_1 >= 300){
                        $valor2 = ">300";
                    }else{
                        $valor2 = $valor_1;
                    }
                    $salida[0].='-NMC2-';
                    $salida[1] = $valor2;//.' ->1mh ';
                }else{
                    
                    //formateo de diluciones
                    switch ($dilucion){
                        
                        case 9: //Dilución2y1    
                        case 12: //Dilución3y1
                        case 15: //Dilución4y1
                            $dilucion = 1;
                            break;
                        case 6: //Dilución1y2
                        case 13: //Dilución3y2
                        case 16: //Dilución4y2
                            $dilucion = 2;
                            break;
                        case 7: //Dilución1y3
                        case 10: //Dilución2y3
                        case 17: //Dilución4y3
                            $dilucion = 3;
                            break;
                        case 8: //Dilución1y4
                        case 11: //Dilución2y4
                        case 14: //Dilución3y4
                            $dilucion = 4;
                            break;
                        default: //para los demas casos
                            $dilucion = $dilucion;
                    }
                
                    if ($dilucion == 5){ // selecciono sin dilucion
                            
                        if($valor_1 == 0 && $id_tecnica <> 29){ // para sedimentación no aplica ya que es calculo directo
                            $valor_1 = '<1';
                        }
                            
                        $valor2 = $valor_1;
                        //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                        $salida[0].='-NMC2-';
                        $salida[1] = $valor2;//.' ->1mh ';
                
                    }elseif($valor_1 >= 300){
                        
                        $valor2 = redondeo_asbioquim($valor_1, $valor_1, $dilucion);
                        //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                        $salida[0].='-NMC2-';
                        $salida[1] = $valor2;//.' ->2mh ';
                
                    }else{
                    // comprobamos si existe un segundo dato
                            
                        //$fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        //if($fila_resultado_1[0]->resultado_analisis2){//existe un segundo resultado
                        if( $valor_2 ){//existe un segundo resultado
                                
                            $valor2 = round( ( $valor_1 + $valor_2 )/ ( 1.1 * valores_dilucion($dilucion) ) ); 
                            $valor2_borrar = $valor2;
                            $valor2 = redondeo_asbioquim($valor2, $valor_2, $dilucion);  
                                
                            //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2.' M');
                            $salida[0].='-NMC2-';
                            $salida[1] = $valor2;//.' ->3mh ';
                
                        }else{// no existe el segundo campo
                            
                            //$valor2 = round( $valor_1 / ( 1.1 * valores_dilucion($dilucion) ) );
                            $valor2 = round( $valor_1 / (  valores_dilucion($dilucion) ) ); 
                            $valor2 = redondeo_asbioquim($valor2, $valor_1, $dilucion);
                                
                            //almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $salida[0].='-NMC2-';
                            $salida[1] = $valor2;//.' ->4mh ';
                        }
                            
                    }
                }
            }else{
                        
                    $salida[1] = "Valor no numerico campo 2";//.$no.' ->5mh '.' -->'.$valor_1;
                    $salida[0] = '-NMC2-';
                    $salida[2] = 'NO';
            }
            ////
            /*
            if (is_numeric ($valor)) {
        
                almacena_segundo_campo($id_ensayo_vs_muestra, $valor);
                $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    
                $valor2     = round( ($fila_resultado_1[0]->resultado_analisis+$valor)/ ( 1.1*valores_dilucion($dilucion) ) ); //0.1 es la dilucion
                $valor2 = redondeo_asbioquim($valor2, $valor, $dilucion);
                    
                almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);                
                $mensaje=$valor2;
                $aux_guarda_resultado_mensaje='-IC2S-'.$valor2;
                $aux_guarda_resultado_mensaje.='-NMC2-';
                $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor2, $id_tipo_analisis, $id_ensayo_vs_muestra);
                
                    
            }else{
            
                $mensaje ="Valor no numerico 2".$no;
                $aux_guarda_resultado_mensaje='-NMC2-';
                $salida[2] = 'NO';
            }
            */
        }
        return $salida;
	}
	
            

	function procesar_registro_fetch($aux_tabla, $aux_columna1, $aux_variable1, 
                                            $aux_columna2=0, $aux_variable2=0, 
                                            $aux_columna3=0, $aux_variable3=0, 
                                            $aux_columna4=0, $aux_variable4=0, 
                                            $aux_columna5=0, $aux_variable5=0, 
                                            $aux_columna6=0, $aux_variable6=0){
		$db = \Config\Database::connect();

	    if(is_numeric($aux_columna1)){
	    	$data = $db->table($aux_tabla)->get()->getResult();
	    }else{
			$wheres = [
				$aux_columna1 	=> $aux_variable1,
				$aux_columna2	=> $aux_variable2, 
	            $aux_columna3	=> $aux_variable3, 
	            $aux_columna4	=> $aux_variable4, 
	            $aux_columna5	=> $aux_variable5, 
	            $aux_columna6	=> $aux_variable6
			];
			$data = $db->table($aux_tabla)->where($wheres)->get()->getResult();
	    }
	    return $data;

	}

	function auto_incrementar($campo,$tabla){
		$db = \Config\Database::connect();
		$r_ok_autoincrementar = $db->query("SELECT max($campo) as total from $tabla")->getResult();

		// $texto_autoincrementar = "SELECT max($campo) as total from $tabla";
		// $query_autoincrementar = mysql_query($texto_autoincrementar) or die ("error en la operacion autoincrementar ".mysql_error().' '.$texto_autoincrementar);
		// $r_ok_autoincrementar=mysql_fetch_object($query_autoincrementar);
		
		$aux_nro=0;
					
		if (!$r_ok_autoincrementar[0]->total)
			$aux_nro=1;
		else
			$aux_nro=$r_ok_autoincrementar[0]->total+1;		
			
		return $aux_nro;
	
	}

	function generaClave(){
	    //Se define una cadena de caractares. Te recomiendo que uses esta.
	    $cadena = "3468ABCDEFGHIJLMNPQRTUVWXYZabcdefghijmnpqrstuvwxy";
	    //Obtenemos la longitud de la cadena de caracteres
	    $longitudCadena=strlen($cadena);
	     
	    //Se define la variable que va a contener la contraseña
	    $pass = "";
	    //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
	    $longitudPass=13;
	     
	    //Creamos la contraseña
	    for($i=1 ; $i<=$longitudPass ; $i++){
	        //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
	        $pos=rand(0,$longitudCadena-1);
	     
	        //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
	        $pass .= substr($cadena,$pos,1);
	    }
	    return $pass;
	}

	function construye_codigo_amc($id_muestreo_detalle){
	    $fila_detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $id_muestreo_detalle);
	    if($fila_detalle[0]->ano_codigo_amc > 0){
	        $aux_codigo =$fila_detalle[0]->ano_codigo_amc.'-'.str_pad($fila_detalle[0]->id_codigo_amc,4,"0",STR_PAD_LEFT);
	    }else{
	        $fila_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $fila_detalle[0]->id_tipo_analisis);
	        $aux_codigo =$fila_analisis[0]->mue_sigla.' '.$fila_detalle[0]->id_codigo_amc;
	    }
	    return $aux_codigo;                     
	}

	function imprime_detalle_muestras($id_muestreo, $editar=0){
		$certificados = new Certificacion();
		$certificados = $certificados->where(['id_muestreo' => $id_muestreo])->get()->getResult();
		$tabla = 	'<div id="campo_detalle_muestras">
						<small>Muestra #'.$id_muestreo.' </small>
						<hr>
		                <table class="striped centered">
		                    <thead>
		                        <tr>
		                            <th>#</th>
		                            <th>Informe</th>
		                            <th>Tipo de An&aacute;lisis</th>
		                            <th>C&oacute;digo</th>
		                            <th>Norma</th>
		                            <th>Identificaci&oacute;n</th>
		                            <th>Cantidad</th>
		    						<th>Unidad</th>
		                            <th>Opciones</th>
		                        </tr>
		                    </thead>
		                    <tbody>';
		                    	$count = 0;
		                    	foreach($certificados as $key => $recordSet){
		                    		$key++;
		                    		$count = $key;
		                    		//formateo de los detalles
		                        	$fila_detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $recordSet->id_muestreo_detalle);
		                 			//formateo del producto
		                        	$fila_producto = procesar_registro_fetch('producto', 'id_producto', $fila_detalle[0]->id_producto);
		                 			//formateo tipo de amanlisis
		                        	$fila_analisis = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $fila_detalle[0]->id_tipo_analisis);
		                        	// return [$fila_detalle , $fila_producto, $fila_analisis];
		                			// formateo de codigo AMC
		                        	/* if($fila_detalle->ano_codigo_amc> 0){
		                             $aux_codigo =$fila_detalle->ano_codigo_amc.'-'.str_pad($fila_detalle->id_tipo_analisis,2,"0",STR_PAD_LEFT).'-'.str_pad($fila_detalle->id_codigo_amc,4,"0",STR_PAD_LEFT);
		                         	}else{
		                             	$aux_codigo =$fila_analisis->mue_sigla.' '.$fila_detalle->id_codigo_amc;
		                         	}*/
		                         	$aux_codigo = construye_codigo_amc($recordSet->id_muestreo_detalle);
		                         	$duplicado = $fila_detalle[0]->duplicado == 0 ? 'Primera muestra':'Duplicado '.$fila_detalle[0]->duplicado;
                    $tabla .= '	<tr class="tr_'.$recordSet->id_certificacion.'">
                    				<td>'.$key.'</div></td>
                                	<td>'.$recordSet->certificado_nro.'</td>
                                	<td>'.$fila_analisis[0]->mue_nombre.'</td>
                                	<td>'.$aux_codigo.'</td>
                                	<td>'.$fila_producto[0]->pro_nombre.'</td>
                                	<td>'.$fila_detalle[0]->mue_identificacion.'</td>
                                	<td>'.$fila_detalle[0]->mue_cantidad.'</td>
                                	<td>'.$fila_detalle[0]->mue_unidad_medida.'</td>
                                	<td class="action_detail">';
                                	if($editar){
                                $tabla .= '<a href="#!" onclick="buscar_detalle('.$recordSet->id_muestreo_detalle.')" class="editar_detalle tooltipped" data-position="bottom" data-tooltip="Editar detalle"><i class="far fa-edit"></i></i></a>';
                                	}else{
                                        $fila_detalle[0]->mue_identificacion = str_replace('"', "''", $fila_detalle[0]->mue_identificacion);
                                    	$tabla .= '<a href="#!" onclick="quitar_detalle('.$recordSet->id_certificacion.', `'.$fila_detalle[0]->mue_identificacion.'`, `'.$fila_analisis[0]->mue_sigla.' '.$fila_detalle[0]->id_codigo_amc.'`)" class="delete_detail_list tooltipped" data-position="bottom" data-tooltip="Eliminar detalle" data-detalle=""><i class="far fa-trash-alt"></i></a>';
                                	}                          
                                    	$tabla .= '<a href="'.base_url(['funcionario', 'remisiones', 'ticket', $recordSet->id_certificacion]).'" class="imprimir_ticket tooltipped" data-position="bottom" data-tooltip="Imprimir detalle"><i class="fas fa-print"></i></a>
                                    </td>
                    			</tr>
                    ';
		                    	}
                $tabla .=	'	<tr>
				                    <td colspan="10"><strong>&nbsp;</strong></td>
				                </tr>
                			</tbody>
		                </table>
		            </div>';
		if($count > 0){
			$button = '	<div class="input-field col s12 centrar_button btn_remision">
	                    	<a href="#!" onclick="btn_remision_guardar()" id="btn-remision-guardar" class="btn gradient-45deg-purple-deep-orange border-round guardar_remision">Guardar remisión</a>
	                    </div>';
		}else{
			$button = '';
		}
		return ['tabla' => $tabla, 'boton' => $button];
	}

	function delete_detail_list($id_certificado){
		$db = \Config\Database::connect();
		$certificados = new Certificacion();
		$fila_certificado = procesar_registro_fetch('certificacion', 'id_certificacion', $id_certificado);
		$certificado = $certificados->where(['id_certificacion' => $id_certificado])->asObject()->first();
        $certificado_nro = $certificado->certificado_nro;
        $muestreo_detalle = new MuestreoDetalle();
        $muestreo_detalle = $muestreo_detalle->where(['id_muestra_detalle' => $certificado->id_muestreo_detalle])->get()->getResult();
        $certificados->where(['id_certificacion' => $id_certificado])->delete();

        $muestreo_detalle = new MuestreoDetalle();
        $producto = $muestreo_detalle->where(['id_muestra_detalle' => $certificado->id_muestreo_detalle])->asObject()->first();
		$codigo = $producto->id_codigo_amc;
		$certi_ids = [];

        $muestreo_detalle->where(['id_muestra_detalle' => $certificado->id_muestreo_detalle])->delete();
        $certificadosA = $certificados->where(['id_certificacion >' => $id_certificado])->get()->getResult();
        foreach ($certificadosA as $certi) {
            $data = ['certificado_nro' => $certificado_nro];
			$certificados->set($data)
						->where(['id_certificacion' => $certi->id_certificacion])
						->update();
			$certificado_nro++;
			array_push($certi_ids, $certi->id_muestreo_detalle);
        }
		
		$muestreoDetalle = new MuestreoDetalle();
		$muestreo_detalles = $muestreoDetalle
			->whereIn('id_muestra_detalle', $certi_ids)->get()->getResult();
		foreach ($muestreo_detalles as $key => $muestreo_detalle) {
			$data = ['id_codigo_amc' => $codigo];
			$muestreoDetalle->set($data)
						->where(['id_muestra_detalle' => $muestreo_detalle->id_muestra_detalle])
						->update();
			$codigo++;
		}

		$tabla = imprime_detalle_muestras($fila_certificado[0]->id_muestreo);

		return $tabla;

	}
	function almacena_auditoria($id_ensayo_vs_muestra, $valor,$columna){
		$db = \Config\Database::connect();
    
    	//para temas de pruebas no se almacena auditoria de id_muestra 4
    
        $fila = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra );
        if($fila[0]->id_muestra==4){
            return ;
        }
   
    	//1 buscamos el cliente
    	//2. verificamos si tiene privilegios 
    	//3. si lo tiene preguntamos la auditoria de existe
    	//4. si existe actualizamos
    	//5. no existe los creamos
    
    	//1
    	$texto="SELECT us.pyme FROM ensayo_vs_muestra em inner join certificacion ce on em.id_muestra=ce.id_certificacion 
                inner join muestreo mu on mu.id_muestreo=ce.id_muestreo 
                inner join usuario us on us.id=mu.id_cliente 
                where em.id_ensayo_vs_muestra=".$id_ensayo_vs_muestra;
    	$rs = $db->query($texto)->getResult();
    	$fila = $rs[0];
	    //2       
	    if($fila->pyme == 'Si'){
	        $fila_au = procesar_registro_fetch('au_ensa_vs_mues', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra, 'columna',$columna );
	        //3
	        if($fila_au[0]->id_auditoria){
	            
	            $texto ="update au_ensa_vs_mues set
	                            valor = '$valor'
	                            where id_ensayo_vs_muestra = $id_ensayo_vs_muestra
	                            and columna = '$columna'";
	            
	        }else{
	            $texto ="Insert into au_ensa_vs_mues (id_ensayo_vs_muestra, fecha, id, columna, valor)"
	            . "values"
	            . "($id_ensayo_vs_muestra, NOW(), ".session('user')->id.",'$columna',  '$valor')";   
	        }
	        
	    }else{
	         $texto ="Insert into au_ensa_vs_mues (id_ensayo_vs_muestra, fecha, id, columna, valor)"
	            . "values"
	            . "($id_ensayo_vs_muestra, NOW(), ".session('user')->id.",'$columna',  '$valor')";
	     
	    }
        //echo $texto;
       $query = $db->query($texto);  
	}
	function formatea_mh_total($valor){
	    if(preg_match("/Total/", $valor)){
	        $porciones = explode("Total ", $valor);
	        return (trim($porciones[1]));    
	    }else{
	        return $valor;    
	    }
	}
	
	function redondeo_asbioquim($resultado, $campo, $dilucion){
	    $aux_superindice = 0;
        $aux_divisor = 1;
        $aux_redondeo = 1;
        $aux_indice_300 = 0;
        
        $salida = '';
        if( $resultado> 0 && $resultado < 40 ){
            $salida = '< 40';    
        }else{
            
            $aux = strlen($resultado);
           
                 if( $aux == 3 ){
                    $aux_superindice = 2;
                    $aux_divisor = 100;
                }
                elseif( $aux == 4 ){
                    $aux_superindice = 3;
                    $aux_divisor = 1000;
                }
                elseif( $aux == 5 ){
                    $aux_superindice = 4;
                    $aux_divisor = 10000;
                }
                elseif( $aux == 6 ){
                    $aux_superindice = 5;
                    $aux_divisor = 100000;
                }
                elseif( $aux == 7 ){
                    $aux_superindice = 6;
                    $aux_divisor = 1000000;
                }
                elseif( $aux == 8 ){
                    $aux_superindice = 7;
                    $aux_divisor = 10000000;
                }
                
                if( $dilucion == 1 ){
                    $salida_cero = '< 10';
                    $aux_indice_300 = 3;
                }elseif( $dilucion == 2 ){
                    $salida_cero = '< 100';
                    $aux_indice_300 = 4;
                }elseif( $dilucion == 3 ){
                    $salida_cero = '< 1000';
                    $aux_indice_300 = 5;
                }elseif( $dilucion == 4 ){
                    $salida_cero = '< 10000';
                    $aux_indice_300 = 6;
                }else{
                    $salida_cero = 'Error de dilución';
                }
                
                if( $resultado == 0 ){
                    $salida = $salida_cero;    
                }elseif( $campo > 300){
                    $salida = '>3,0 x 10<sup> '.$aux_indice_300.'</sup>'; 
                }elseif($aux <= 3 ){
                    $salida  = round($resultado,-1);
                }else{
                    $salida  = round($resultado / $aux_divisor,1);
                    $salida  = number_format($salida,1);
                    $salida  = $salida  .' x 10<sup> '.$aux_superindice.' </sup>'; 
                    $salida   = str_replace('.', ',',$salida);
                }
            
        }
	    
	    return $salida;//.' len:'.$aux.' valor:'.$resultado.' redondeo:'.$aux_redondeo;
    }
	    
	function redondeo_asbioquimx($resultado, $campo, $dilucion){
        // si es menos a 40 retorna <40
        // si el campo es > 300 se coloca >3,0 x 10 elevado a la diculcion
        // si no se realiza conteo de cifras 
        // si esta entre 40 y 999 se redondea al numero mayo multiplo de 10 
        // si es mayor a 1000 se redondea al numero mayor multiplo de 100 y se expresa en potencia
        
        //
        $aux_superindice = 0;
        $aux_divisor = 1;
        $aux_redondeo = 1;

        
        if( $dilucion == 1 ){
            $aux_superindice = 3;
            $aux_divisor = 1000;
            $aux_redondeo = -2;
            $salida_cero = '< 10';
        }
        elseif( $dilucion == 2 ){
            $aux_superindice = 3;
            $aux_divisor = 1000;
            $aux_redondeo = -2;
            $salida_cero = '< 100';
        }
        elseif( $dilucion == 3 ){
            $aux_superindice = 4;
            $aux_divisor = 10000;
            $aux_redondeo = -3;
            $salida_cero = 'Error de dilución';
        }
        elseif( $dilucion == 4 ){
            $aux_superindice = 5;
            $aux_divisor = 100000;
            $aux_redondeo = -4;
            $salida_cero = 'Error de dilución';
        }else{
            $salida_cero = '<1';
        }
            
            
        $salida = '';
        if( $resultado == 0 ){
            $salida = $salida_cero;    
        }
        elseif( $resultado < 40 ){
            $salida = '< 40';    
        }elseif( $campo > 300){
            $salida = '>3,0 x 10<sup> '.$aux_superindice.'</sup>'; 
        }else{
            $aux = strlen($resultado);
            if($aux <= 3 ){
                $salida  = round($resultado,-1);
            }else{
                 $salida  = round($resultado, $aux_redondeo) / $aux_divisor ;
                 $salida  = $salida  .' x 10<sup> '.$aux_superindice.'</sup>'; 
            }
        }
        
        
        return $salida;
    }
    
    
    
    function valores_dilucion($id){
        //$fila_au = procesar_registro_fetch('diluciones', 'id_dilucion', $id );
        
        $aux_valor = 0;
        if( $id == 1 )
            $aux_valor = 0.1;
        elseif( $id == 2 )
            $aux_valor = 0.01;
        elseif( $id == 3 )
            $aux_valor = 0.001;
        elseif( $id == 4 )
            $aux_valor = 0.0001;
        else
            $aux_valor =1;
            
        return $aux_valor ;
    }
    
    function formateo_exponenciales_a_numero($valor){
        //normalizamos para poder formatear
        $valor = strtoupper($valor);
        $valor = str_replace('X 10<SUP>', '--',$valor);
        $valor = str_replace('X 10SUP', '--',$valor);
        $valor = str_replace('</', '/',$valor);
        
        $porciones = explode("--", $valor);
        //$porciones = explode("x 10<sup>", $valor);
        
        if(isset($porciones[1])){
            //$aux = explode("</", $porciones[1]);
            $aux = explode("/", $porciones[1]);
            
            if( $aux[0] > 0 ){ 
                $porciones_pow = explode("/", $porciones[1]);
                //$porciones_pow = explode("</", $porciones[1]);
                //$salida = $porciones[1].'->1'.pow(10,$porciones_pow[0]);
                // if(isset())
                $porciones[0] = str_replace(">", "", $porciones[0]);
                //$salida = intval($porciones[0]) * pow( 10, $porciones_pow[0] );
                
                $salida.=$porciones[0]."<-1->".$porciones[1];
            }else{
                $salida = $porciones[0]; 
                //$salida.=$porciones[0]."<-2->".$porciones[1];
            }
            
            $porciones_pow = explode("/", $porciones[1]);
            $salida = $porciones[0] * pow( 10, $porciones_pow[0] );
            //$salida.=$porciones[0]."<-2.5->".$porciones[1]."<-2.5->".$porciones_pow[0];
        }else{
            /*
            //$salida = $porciones[0]; 
            // validamos si un dato <10 o a <100 etc, que valdra cero
            
            $porcion_signo = explode("<", $porciones[0]);
            //if ($porcion_signo[0] > 0 )
            if (isset($porcion_signo[1]) )
                $salida = 0;
            else{
                $porcion_signo = explode("> 300", $porciones[0]);
                if (isset($porcion_signo[1]) )
                    $salida = 300;
                else
                    $salida = $porciones[0]; 
            */        
            
            $find_signo_menor40  = '<40';
            $find_signo_menor  = '<';
            $find_signo_mayor300  = '>300';
            $find_signo_mayor200  = '>200';
            
            $pos_signo_menor40  = strpos(str_replace(" ","",$porciones[0]), $find_signo_menor40);
            $pos_signo_menor    = strpos($porciones[0], $find_signo_menor);
            $pos_signo_mayor300 = strpos(str_replace(" ","",$porciones[0]), $find_signo_mayor300);
            $pos_signo_mayor200 = strpos(str_replace(" ","",$porciones[0]), $find_signo_mayor200);
            
            if($pos_signo_menor40 === 0){
                $salida = 40;
            }elseif($pos_signo_menor === 0){
                $salida = 0;
            }elseif($pos_signo_mayor300 === 0){
                $salida = 300;
            }elseif($pos_signo_mayor200 === 0){
                $salida = 200;
            }else{
                $salida = $porciones[0];   
            }
            //$salida.=$porciones[0]."<-3->";
        
             /*   
            $porcion_signo = str_replace('<', '--',$porciones[0]); 
            $porcion_signo = explode("--", $porcion_signo);
            
            if (isset($porcion_signo[1])  )
                $salida = 0;
            else
                $salida = $porciones[0]; 
            */
        }
         
        // if( $porciones[1] > 0 ){ 
        //     $porciones_pow = explode("</", $porciones[1]);
        //     //$salida = $porciones[1].'->1'.pow(10,$porciones_pow[0]);
        //     $salida = $porciones[0] * pow( 10, $porciones_pow[0] ); 
        // }else{
        //     $salida = $porciones[0]; 
        // }
        
        return $salida;
    }
    
    function formateo_exponenciales_a_numero2($valor){
        $porciones = explode("X 10SUP", strtoupper($valor));
        
        if(isset($porciones[1])){
            $aux = explode("/", $porciones[1]);
            if( $aux[0] > 0 ){ 
                $porciones_pow = explode("/", $porciones[1]);
                //$salida = $porciones[1].'->1'.pow(10,$porciones_pow[0]);
                $salida = $porciones[0] * pow( 10, $porciones_pow[0] ); 
            }else{
                $salida = $porciones[0]; 
            }
            $porciones_pow = explode("/", $porciones[1]);
            //$salida = $porciones[1].'->1'.pow(10,$porciones_pow[0]);
            $salida = $porciones[0] * pow( 10, $porciones_pow[0] );
        }else{
            // validamos si un dato <10 o a <100 etc, que valdra cero
            $porcion_signo = explode("<", $porciones[0]);
            if ($porcion_signo[0] > 0 )
                $salida = 0;
            else
                $salida = $porciones[0]; 
        }
        
        return $salida;
    }
    
    function obtiene_acreditracion($id_ensayo_vs_muestra){
        $fila_e_v_m     = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
        $fila_ensayo    = procesar_registro_fetch('ensayo', 'id_ensayo', $fila_e_v_m[0]->id_ensayo);
        $fila_parametro = procesar_registro_fetch('parametro', 'id_parametro', $fila_ensayo[0]->id_parametro);
        $fila_acreditacion = procesar_registro_fetch('acreditaciones', 'id_acreditacion', $fila_parametro[0]->id_acreditacion);
        return $fila_acreditacion;
        
    }
    
    function cumnormdist($x)
    {
      $b1 =  0.319381530;
      $b2 = -0.356563782;
      $b3 =  1.781477937;
      $b4 = -1.821255978;
      $b5 =  1.330274429;
      $p  =  0.2316419;
      $c  =  0.39894228;
    
      if($x >= 0.0) {
          $t = 1.0 / ( 1.0 + $p * $x );
          return (1.0 - $c * exp( -$x * $x / 2.0 ) * $t *
          ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
      }
      else {
          $t = 1.0 / ( 1.0 - $p * $x );
          return ( $c * exp( -$x * $x / 2.0 ) * $t *
          ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
        }
    }
    
    
    function formateo_mohos_y_levaduras_a_numero($valor){
        // echo $valor;
        
        $salida = $valor;
        
        $porciones = explode("+", $valor);
        
        $valor_m = 0;
        $valor_l = 0;
        
        if(isset($porciones[0])){
            // convertir Mohos a numero
            $valor_m = str_replace(" (M)", "", $porciones[0]);
        }
        if(isset($porciones[1])){
            // convertir Levadura a numero
            $valor_l = str_replace(" (L)", "", $porciones[1]);
        }
        
            
        
        
        $salida = intval(formateo_exponenciales_a_numero($valor_m) ) + intval(formateo_exponenciales_a_numero($valor_l) )  ;//+ intval($valor_l) ;
        // return "|".json_encode($porciones)."|";
        return $salida;
        
        //return "|".$valor_m."|";
        //return "|".formateo_exponenciales_a_numero($valor_l) ."|";
    }
    
    function envia_alerta($id_ensayo_vs_muestra){
        $db = \Config\Database::connect();
        $sql = "SELECT c.certificado_nro, md.ano_codigo_amc, md.id_codigo_amc, md.id_muestra_detalle
	    , (select name from usuario where id=m.id_cliente ) cliente
        , (select CONCAT(ta.id_muestra_tipo_analsis,'-',ta.mue_sigla,'-',ta.mue_nombre) from muestra_tipo_analisis ta where ta.id_muestra_tipo_analsis =md.id_tipo_analisis) tipo_analisis
        ,(select pro_nombre from producto where id_producto=md.id_producto ) producto
        ,p.par_nombre, (select CONCAT(id_tecnica,'-',nor_nombre) from tecnica where id_tecnica=p.id_tecnica) tecnica
        ,en.med_valor_min, en.med_valor_max
        , e.resultado_analisis, e.resultado_analisis2, e.resultado_mensaje, e.id_ensayo_vs_muestra
        FROM certificacion c inner join ensayo_vs_muestra e on c.id_muestreo_detalle=e.id_muestra
        inner join muestreo_detalle md on md.id_muestra_detalle=c.id_muestreo_detalle
        inner join ensayo en on en.id_ensayo=e.id_ensayo
        inner join parametro p on p.id_parametro=en.id_parametro
        inner join muestreo m on m.id_muestreo=c.id_muestreo
        where e.id_ensayo_vs_muestra=$id_ensayo_vs_muestra";
        $fila = $db->query($sql)->getResult();
        $fila = $fila[0];
        
        $usuario = procesar_registro_fetch('cms_users', 'user_rol', 3);
        
        
        // $sPara       = !empty($usuario[0]->usr_correo) ? $usuario[0]->usr_correo : 'resultados@asbioquim.com.co';//gerente -- gerencia@asbioquim.com.co
        $sPara = 'resultados@asbioquim.com.co';
        $sAsunto     = 'GestionLab Asbioquim Alertas !!!';
        
        $aux_cod_amc = construye_codigo_amc($fila->id_muestra_detalle);	
        $sTexto=" <html> 	<head><title>Alertas GestionLabs!!</title></head>	<body><h1>Alertas GestionLabs</h1>	<hr>        Se genero el registro de muestra fuera de rango, los datos son:	
    	<br><b>Informe:</b> $fila->certificado_nro
    		<br><b>Cogido:</b> $aux_cod_amc
    		<br><b>Cliente:</b>    ".$fila->cliente."
            <br><b>Producto:</b>    ".$fila->producto." 
            <br><b>Tecnica:</b> ".$fila->tecnica."  
            <br><b>Rango :</b>  $fila->med_valor_min - $fila->med_valor_max
            <br><b>Datos ingresados:</b>    $fila->resultado_analisis $fila->resultado_analisis2    
            <br><b>Resultado:</b>   $fila->resultado_mensaje  
            <br><b>Usuario:</b> ".session('user')->usr_usuario."     
    	</body>
    	</html>";
    	$email = \Config\Services::email();
        $email->setFrom(!empty(configInfo()['email']) ? configInfo()['email'] : 'iplanet@iplanetcolombia.com', !empty(configInfo()['name_app']) ? configInfo()['name_app'] : 'IPlanet Colombia S.A.S');
        $email->setTo($sPara);
        $email->setSubject($sAsunto);
        $email->setMessage($sTexto);
        $email->send();
        
    }
    
    
    