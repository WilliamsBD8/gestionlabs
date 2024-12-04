<?php
  	function limpia_campos_frm($titulo_campo_1){
    
	    $titulo_campo_1 = str_replace(" ","_",$titulo_campo_1);
	    $titulo_campo_1 = str_replace("(","",$titulo_campo_1);
	    $titulo_campo_1 = str_replace(")","",$titulo_campo_1);
	    $titulo_campo_1 = str_replace("/","_",$titulo_campo_1);
	    $titulo_campo_1 = str_replace("[","",$titulo_campo_1);
	    $titulo_campo_1 = str_replace("]","",$titulo_campo_1);
	    $titulo_campo_1 = str_replace("-","",$titulo_campo_1);
	    
	    return $titulo_campo_1;
  	}
	function parametros_aguas_fq(){
		$parametros = [
			[101, 'CLORO LIBRE', 'mg/L Cl2'],
			[86, 'pH', 'U'],
			[241, 'Turbiedad', 'UNT'],
			[237, 'COLOR', 'UPC'],
			[102, 'CONDUCTIVIDAD', '(us/cm)'],
			[434, 'ALCALINIDAD TOTAL', 'Concentracion [H2SO4]', 'mL Fenolftaleina', 'mL desplazados mg/L CaCO3'],
			[236, 'DUREZA TOTAL', 'Concentracion [EDTA]', 'mL desplazados EDTA'],
			[235, 'CLORUROS', 'Concentracion [AgNO3]', 'mL Blanco', 'mL desplazados AgNO3'],
			[240, 'SULFATOS', 'NTU 1', 'NTU 2', 'Valor 1', 'Valor 2'],
			[244, 'NITRITOS', 'mg/L NO2'],
			[239, 'HIERRO', 'mg/L Fe'],
			[238, 'Fosfatos', 'mg/L PO4'],
			[242, 'Aluminio', 'mg/L AL'],
			[245, 'NITRATOS', 'mg/L  NO3'],
			[39, 'Peso capsula con solidos','Peso capsula vacia','Peso muestra'],
			[40, 'Peso capsula con solidos','Peso capsula vacia','Peso muestra']
		];
		return $parametros;
	}
	function proceso_parametro_fq($id_parametro){
		$parametro = 0;
		$parametros = parametros_aguas_fq();
		foreach ($parametros as $key => $value) {
			if ($value[0] == $id_parametro)
				$parametro = $value;
		}
		// return $parametro;
		$proceso = [
			234 => '( ( '.$parametro[4].' - '.$parametro[3].' ) * '.$parametro[2].' * Factor  )  /  Alicuota (ml)',
			235 => '( ( mL desplazados AgNO3 - mL Blanco ) *  Concentracion [AgNO3]  * Factor )/ Alicuota (ml)',
			236 => '( ( Concentracion [EDTA] * mL desplazados EDTA ) / Alicuota (ml) )  * Factor',
			240 => '( ( NTU 2 - NTU 1 ) -  Valor 1 )/ Valor 2'
		];
		if(!empty($proceso[$id_parametro]))
			return $proceso[$id_parametro];
		else
			return '';
	}
	
	function pinta_parametro_agua($id_parametro, $id_muestra_detalle, $user_rol_id, $titulo_principal, $titulo_campo_1, $titulo_campo_2 = '',  $titulo_campo_3 = '',  $titulo_campo_4 = '' ){
		$fila = fq_tiene_parametro($id_muestra_detalle, $id_parametro);
		$aux_titulo_label = $titulo_principal.' 1x'.$id_parametro;
		$aux_titulo_label_1 = $titulo_campo_1.' 2x';
		$aux_titulo_label_2 = $titulo_campo_2.' 3x';
		$aux_titulo_label_3 = $titulo_campo_3.' 4x';
		$aux_titulo_label_4 = $titulo_campo_4.' 5x';;
		$titulo_principal   = limpia_campos_frm($titulo_principal);
	    $titulo_campo_1     = limpia_campos_frm($titulo_campo_1);
	    $titulo_campo_2     = limpia_campos_frm($titulo_campo_2);
	    $titulo_campo_3     = limpia_campos_frm($titulo_campo_3);
	    $titulo_campo_4     = limpia_campos_frm($titulo_campo_4);
		//$salida = '<br>-pa-->'.$id_parametro;
		$aux_contador = 0;
		if (!empty($fila[0])){
			$result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $fila[0]->id_ensayo_vs_muestra, 'id_parametro', $id_parametro);
			if($id_parametro == 39 || $id_parametro == 40){
				$salida .= '
					<tr>
						<td>
							<p class="center-align">
                                <b>Solidos totales.</b>
                                <br>
                                <small>( ( Peso capsula con solidos - Peso capsula vacia ) x 1.000.000 ) / Peso muestra</small>
                            </p>
                            <div class="row">
                                <div class="input-field col s12 l4">
                                    <input type="text" name="frm_'.$titulo_principal.'_alicuota"
                                    id="frm_'.$titulo_principal.'_alicuota" value="'.$result[0]->result_1.'"
                                    onblur="js_cambiar_campos(`campo_repuesta_1_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_alicuota`, `result_1`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
                                                '.disable_frm($result[0]->result_1, session('user')->id).'>
                                    <label for="frm_'.$titulo_principal.'_alicuota">'.$aux_titulo_label.'</label>
                                    <span id="frm_'.$titulo_principal.'_alicuota"></span>
                                </div>
                                <div class="input-field col s12 l4">
                                    <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_1.'"
                                    id="frm_'.$titulo_principal.'_'.$titulo_campo_1.'" value="'.$result[0]->result_2.'"
                                    onblur="js_cambiar_campos(`campo_repuesta_2_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_1.'`, `result_2`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
                                                '.disable_frm($result[0]->result_2, session('user')->id).'>
                                    <label for="frm_'.$titulo_principal.'_'.$titulo_campo_1.'">'.$aux_titulo_label_1.'</label>
                                    <span id="frm_'.$titulo_principal.'_'.$titulo_campo_1.'"></span>
                                </div>
                                <div class="input-field col s12 l4">
                                    <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_2.'"
                                    id="frm_'.$titulo_principal.'_'.$titulo_campo_2.'" value="'.$result[0]->result_3.'"
                                    onblur="js_cambiar_campos(`campo_repuesta_3_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_2.'`, `result_3`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
                                                '.disable_frm($result[0]->result_3, session('user')->id).'>
                                    <label for="frm_'.$titulo_principal.'_'.$titulo_campo_2.'">'.$aux_titulo_label_2.'</label>
                                    <span id="frm_'.$titulo_principal.'_'.$titulo_campo_2.'"></span>
                                </div>
                            </div>
				';
				$aux_titulo_label = 'Solidos totales.';
			}else{
				$salida .= '
					<tr>
						<td>
							<p class="center-align">
                                <b>'.$aux_titulo_label.'</b>
                                <br>
                                <small>'.proceso_parametro_fq($id_parametro).'</small>
                            </p>
                            <div class="row">
                                <div class="input-field col s12 l3">
                                    <input type="text" name="frm_'.$titulo_principal.'_alicuota"
                                    id="frm_'.$titulo_principal.'_alicuota" value="'.$result[0]->result_1.'"
                                    onblur="js_cambiar_campos(`campo_repuesta_1_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_alicuota`, `result_1`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
                                                '.disable_frm($result[0]->result_1, session('user')->id).'>
                                    <label for="frm_'.$titulo_principal.'_alicuota">Alicuota (ml)</label>
                                    <span id="frm_'.$titulo_principal.'_alicuota"></span>
                                </div>
                                <div class="input-field col s12 l3">
                                    <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_1.'"
                                    id="frm_'.$titulo_principal.'_'.$titulo_campo_1.'" value="'.$result[0]->result_2.'"
                                    onblur="js_cambiar_campos(`campo_repuesta_2_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_1.'`, `result_2`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
                                                '.disable_frm($result[0]->result_2, session('user')->id).'>
                                    <label for="frm_'.$titulo_principal.'_'.$titulo_campo_1.'">'.$aux_titulo_label_1.'</label>
                                    <span id="frm_'.$titulo_principal.'_'.$titulo_campo_1.'"></span>
                                </div>
				';
				$aux_contador = 2;
				if($id_parametro == 234 || $id_parametro == 236  || $id_parametro == 235 || $id_parametro == 240) {
					$aux_contador++;
					$salida .= '
									<div class="input-field col s12 l3">
	                                    <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_2.'"
	                                    id="frm_'.$titulo_principal.'_'.$titulo_campo_2.'" value="'.$result[0]->result_3.'"
	                                    onblur="js_cambiar_campos(`campo_repuesta_3_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_2.'`, `result_3`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
	                                                '.disable_frm($result[0]->result_3, session('user')->id).'>
	                                    <label for="frm_'.$titulo_principal.'_'.$titulo_campo_2.'">'.$aux_titulo_label_2.'</label>
	                                    <span id="frm_'.$titulo_principal.'_'.$titulo_campo_2.'"></span>
	                                </div>
					';
				}
			}
			if($id_parametro == 234 || $id_parametro == 235 || $id_parametro == 240 ) {
				$aux_contador++;
				$salida .= '
									<div class="input-field col s12 l3">
	                                    <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_3.'"
	                                    id="frm_'.$titulo_principal.'_'.$titulo_campo_3.'" value="'.$result[0]->result_4.'"
	                                    onblur="js_cambiar_campos(`campo_repuesta_4_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_3.'`, `result_4`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
	                                                '.disable_frm($result[0]->result_4, session('user')->id).'>
	                                    <label for="frm_'.$titulo_principal.'_'.$titulo_campo_3.'">'.$aux_titulo_label_3.'</label>
	                                    <span id="frm_'.$titulo_principal.'_'.$titulo_campo_3.'"></span>
	                                </div>';
			}
			if($aux_contador == 4){
				$salida .= '</div>
                <div class="row">';
			}
			if($id_parametro == 234 || $id_parametro == 236 || $id_parametro == 235 ) {
				$factores = procesar_registro_fetch('factor_fq', 'estado', 'Activo');
				$salida .= '<div class="input-field col s12 l3">
							    <select 
							    	name="frm_'.$titulo_principal.'_factor"
                                	id="frm_'.$titulo_principal.'_factor"
                                	onchange="js_cambiar_campos(`campo_repuesta_factor_'.$fila[0]->id_ensayo_vs_muestra.'`,this.value, `frm_'.$titulo_principal.'_factor`, `id_factor`, `'.$fila[0]->id_ensayo_vs_muestra.'`, `'.$id_parametro.'`)"
                                	'.disable_frm($result[0]->id_factor, session('user')->usr_rol).'
                                >
							      	<option>Seleccione factor</option>';
				foreach ($factores as $key => $factor) {
					$aux_select = $factor->id_factor == $result[0]->id_factor ? 'selected': '';
					$salida .= '<option value="'.$factor->id_factor.'"'.$aux_select.'>'.$factor->nombre.' | '.$factor->valor.'</option>';
				}
				$salida .= '
						    </select>
						    <label>Factor</label>
						    <span id="frm_'.$titulo_principal.'_factor"></span>
						 </div>';
			}elseif($id_parametro == 240 ) {
				$salida .= '<div class="input-field col s12 l3">
	                            <input type="text" name="frm_'.$titulo_principal.'_'.$titulo_campo_4.'"
	                            id="frm_'.$titulo_principal.'_'.$titulo_campo_4.'" value="'.$result[0]->result_6.'"
	                            onblur="js_cambiar_campos(`campo_repuesta_5_'.$fila[0]->id_ensayo_vs_muestra.'`, this.value, `frm_'.$titulo_principal.'_'.$titulo_campo_4.'`, `result_6`, `'.$fila[0]->id_ensayo_vs_muestra.'`, '.$id_parametro.')"
	                                        '.disable_frm($result[0]->result_6, session('user')->id).'>
	                            <label for="frm_'.$titulo_principal.'_'.$titulo_campo_4.'">'.$aux_titulo_label_4.'</label>
	                            <span id="frm_'.$titulo_principal.'_'.$titulo_campo_4.'"></span>
	                        </div>';
			}
			if($aux_contador != 4) $salida .= '</div><div class="row">';
			if($aux_contador == 4) $aux_col = 3;
			else $aux_col = 4;
			$salida .= '
							<div class="col s12 l'.$aux_col.'">
	                            <p><b>'.$aux_titulo_label.'</b></p>
	                            <b id="campo_respuesta_agua_'.$fila[0]->id_ensayo_vs_muestra.'">'.$result[0]->result_5.'</b>
	                        </div>
	                        <div class="col s12 l'.$aux_col.'">
	                            <p><b>IRCA '.$aux_titulo_label.'</b></p>
	                            <b id="campo_respuesta_irca_'.$fila[0]->id_ensayo_vs_muestra.'">'.$result[0]->result_irca.'</b>
	                        </div>';
	            $equipos = procesar_registro_fetch('equipo_fq', 'estado', 'Activo');
	            $salida .= '
						<div class="input-field col s12 l3">
						    <select
						    	name="frm_'.$titulo_principal.'_equipo"
	                            id="frm_'.$titulo_principal.'_equipo"
	                            onchange="js_cambiar_campos(\'campo_repuesta_equipo_'.$fila[0]->id_ensayo_vs_muestra.'\',this.value, \'frm_'.$titulo_principal.'_equipo\', \'id_equipo\', \''.$fila[0]->id_ensayo_vs_muestra.'\', '.$id_parametro.')"
	                            '.disable_frm($result[0]->id_equipo, session('user')->usr_rol).'
	                            >
						      	<option value="0">Seleccione equipo</option>
						    ';
	            foreach ($equipos as $key => $equipo) {
	            	$aux_select = $equipo->id_equipo == $result[0]->id_equipo ? 'selected':'';
	            	$salida .= '<option value="'.$equipo->id_equipo.'" '.$aux_select.'>'.$equipo->nombre.'</option>';
	            }

			$salida .= '
								</select>
							    <label>Codigo equipo</label>
							    <span id="frm_'.$titulo_principal.'_equipo"></span>
							 </div>
						</div>
					</td>
				</tr>
			';	
		}
		return $salida;
	}
	
	function comprueba_calcular_resultado_agua($id_ensayo_vs_muestra, $id_parametro, $id_calculo){
	    
	    $result_fq = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra, 'id_parametro', $id_parametro);
	    $calculos_fq = procesar_registro_fetch('calculos_fq', 'id_calculo', $id_calculo);

    	$result_1 = str_replace(",",".",$result_fq[0]->result_1);
    	$result_2 = str_replace(",",".",$result_fq[0]->result_2);
    	$result_3 = str_replace(",",".",$result_fq[0]->result_3); 
    	$result_4 = str_replace(",",".",$result_fq[0]->result_4);
    	$result_5 = str_replace(",",".",$result_fq[0]->result_5);
    	$result_6 = str_replace(",",".",$result_fq[0]->result_6);
    	$result_7 = str_replace(",",".",$result_fq[0]->result_7);
    	
    	// 1. remplaza variables en formula 
    	
    	$formula = str_replace("R1","$result_1",$calculos_fq[0]->formula_sistema);
    	$formula = str_replace("R2","$result_2",$formula);
    	$formula = str_replace("R3","$result_3",$formula);
    	$formula = str_replace("R4","$result_4",$formula);
    	$formula = str_replace("R5","$result_5",$formula);
    	$formula = str_replace("R6","$result_6",$formula);
    	$formula = str_replace("R7","$result_7",$formula);
    	
    	
    	$bandera_cualitativo = 'No';
    	$nombre_campo_frm = '';
    	
    	$salida[0] = 'Sin resultados';
    	//$salida[1] = -1;
    	//$salida[2] = -1;
    	
    	if($result_fq[0]->id_equipo ) {
    	    // se debe validar si se ingresaron todas las variables de la formula, de locontrario traera error
        	// 1. la formula contiene el parametro Rx
        	// 2. Validamos si el usario la ingreso
        	// 3. evaluamos si la formula contine factor
        	
        	$aux_todas = "Si";
    	    for($i=1;$i<=7;$i++){
        	    $porciones = explode("R".$i, strtoupper($calculos_fq[0]->formula_sistema));
        	    if(isset($porciones[1])){// 1.
        	        //2
        	        if($i==1){
            	        if(!isset($result_1)) $aux_todas = "No";
            	    }elseif($i==2){
            	        if(!isset($result_2)) $aux_todas = "No";
            	    }elseif($i==3){
            	        if(!isset($result_3)) $aux_todas = "No";
            	    }elseif($i==4){
            	        if(!isset($result_4)) $aux_todas = "No";
            	    }elseif($i==5){
            	        if(!isset($result_5)) $aux_todas = "No";
            	    }elseif($i==6){
            	        if(!isset($result_6)) $aux_todas = "No";
            	    }elseif($i==7){
            	        if(!isset($result_7)) $aux_todas = "No";
            	    }
        	    }
        	}//fin for
        	
        	// 3
        	$porciones = explode("FACTOR", strtoupper($calculos_fq[0]->formula_sistema));
        	if(isset($porciones[1])){
        	    
        	    if($result_fq[0]->id_factor){
        	        $fila_factor = procesar_registro_fetch ('factor_fq', 'id_factor', $result_fq[0]->id_factor);
		            $factor = str_replace(",",".",$fila_factor[0]->valor);
        	        $formula = str_replace("FACTOR",$factor,$formula);
        	    }else{
        	         $aux_todas = "No";   
        	    }
        	}
        	
        	if($aux_todas=="Si"){
        	    
        	   if($id_calculo == -1){//CALCULO DIRECTO
        	    
        	        if($result_fq[0]->id_equipo==1  ){//otros analisis (sin dilucion)// $result_fq->id_equipo lo use para guardado de dilusion
        	            //$salida ="Calcula otros analisis sin dilucion";
	                    $result =    ($result_1)?$result_1:$result_3;
	                    $bandera_cualitativo = "Si";
	        
        	        }else{
        	            //$salida ="Calcula otros analisis con dilucion";
	                    $vf     =   $result_1 + $result_2;
	                    $factor =    $vf / $result_1;
	                    $result =    $result_3 * $factor;
        	        }
        	        $nombre_campo_frm ="resultado_otros_analsis";
        	        $nombre_campo_frm_irca="resultado_otros_analsis_irca";
        	        
        	        //calculamos el IRCA
        	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
        	        
        	    }else{
        	        try {
        	            $formula = str_replace("+ -","-",$formula);
	                    $formula = str_replace("--","-",$formula);
        	            $formula = str_replace(",",".",$formula);
        	            $result = eval( 'return '.$formula.';'  );
        	            
        	        } catch (Throwable $t) {
                        $result = "<span style='color:red' >Error: por favor valide la formula del sistema</span>";
                    }
        	        
        	        
        	        $nombre_campo_frm ="resultado_".$calculos_fq[0]->sigla;// para auditoria
        	        $nombre_campo_frm_irca="resultado_irca_".$calculos_fq[0]->sigla;
        	        
        	        //calculamos el IRCA
        	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
        	        
        	    }
        	}//fin todas
        	
    	    
    	}//fin validar equipo
    	
    	//$salida[0] = $bandera_cualitativo;
    	//$salida[1] = $nombre_campo_frm;
    	
    	if($bandera_cualitativo == "Si"){
		    
		    $salida[0] = $result ;
		    
		    almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
		    
		}elseif($nombre_campo_frm){
	        //$result =   round($result, 6);
	        
	        //pasamos el resultado de punto a coma
	        $result = str_replace(".",",",$result);
	        
	        
	        $salida[0] = "<b>". $result ."</b>";
	        
	        //guarda campo en result_fq
	        almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	       	almacena_campo_resultado($id_ensayo_vs_muestra, $result);
        
    	}
    	
    	//guarda valor parcial irca si existe
        // $aux_irca = str_replace(".",",",$aux_irca);
	    if( isset($salida[1]) ){
	            almacena_campo_fq($id_ensayo_vs_muestra, $salida[1], $id_parametro, $nombre_campo_frm_irca, 'result_irca');    
	    }
    	
    	return $salida;
	
	    
	}
	
	function comprueba_calcular_resultado_agua_old($id_ensayo_vs_muestra, $id_parametro, $id_calculo){
	    //buscamos si ya estan diligenciado los valores
	    //si hacemos el calculo y guardamos en fq y ensayo_vs_resultado
	    $result_fq = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra, 'id_parametro', $id_parametro);
	    $result_fq = $result_fq[0];
	    
	    $result_1 = str_replace(",",".",$result_fq->result_1);
	    $result_2 = str_replace(",",".",$result_fq->result_2);
	    $result_3 = str_replace(",",".",$result_fq->result_3);
	    $result_4 = str_replace(",",".",$result_fq->result_4);
	    $result_5 = str_replace(",",".",$result_fq->result_5);
	    $result_6 = str_replace(",",".",$result_fq->result_6);
	    $result_7 = str_replace(",",".",$result_fq->result_7);
	    
	    if(isset($result_1) && isset($result_2) && isset($result_3) && isset($result_4)  && $result_fq->id_equipo && $id_calculo == 18){ //alcalinidad
	        //$salida ="Calcula Alacalinidad 1";
	        $result =   ((( $result_1 - $result_2 ) * $result_3 ) / $result_4 ) * 50000;

	        $nombre_campo_frm ="resultado_alcaliniad_1";
	        $nombre_campo_frm_irca="resultado_alcaliniad_1_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && isset($result_4)  && $result_fq->id_equipo && $id_calculo == 19){ //alcalinidad 2
	        //$salida ="Calcula Alacalinidad 2";
	        $result =   ((( 2 * $result_1 - $result_2 ) * $result_3 ) / $result_4 ) *50000;
	        

	        $nombre_campo_frm ="resultado_alcaliniad_2";
	        $nombre_campo_frm_irca="resultado_alcaliniad_2_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && isset($result_4)  && $result_fq->id_equipo && $id_calculo == 20){ //cloruros
	        //$salida ="Calcula cloruros";
	        $result =   ((( $result_1 - $result_2 ) * $result_3 ) / $result_4 ) * 35450;
	       
	        $nombre_campo_frm ="resultado_cloruros";
	        $nombre_campo_frm_irca="resultado_cloruros_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && isset($result_4)  && $result_fq->id_equipo && $id_calculo == 21){ //dureza total
	        //$salida ="Calcula dureza total";
	        $result =   ((( $result_1 - $result_2 ) * $result_3 ) / $result_4 ) * 100087;

	        $nombre_campo_frm ="resultado_dureza_total";
	        $nombre_campo_frm_irca="resultado_dureza_total_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && $result_fq->id_equipo && $id_calculo == 22){ //dureza calcica
	        //$salida ="Calcula total calcica";
	        $result =   (( $result_1 * $result_2 ) / $result_3 ) * 100087;

	        $nombre_campo_frm ="resultado_dureza_calcica";
	        $nombre_campo_frm_irca="resultado_dureza_calcica_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && $result_fq->id_equipo && $id_calculo == 24){ //calcio
	        //$salida ="Calcula total calcica";
	        $result =   (( $result_1 * $result_2 ) / $result_3 ) * 40078;

	        $nombre_campo_frm ="resultado_calcio";
	        $nombre_campo_frm_irca="resultado_calcio_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && $result_fq->id_equipo && $id_calculo == 25){ //solidos totales
	        //$salida ="Calcula solidos totales";
	        $result =   (( $result_2 - $result_1 ) / $result_3 ) * 1000000;

	        $nombre_campo_frm ="resultado_solidos_totales";
	        $nombre_campo_frm_irca="resultado_solidos_totales_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);
	        
		}elseif(( isset($result_1) || isset($result_3) ) && $result_fq->id_equipo==1  && $id_calculo == -1){ //otros analisis (sin dilucion)// $result_fq->id_equipo lo use para guardado de dilusion
	        //$salida ="Calcula otros analisis";
	       
	        $result =    ($result_1)?$result_1:$result_3;
	        
	        $result = str_replace("<","",$result);
	        $result = str_replace(",",".",$result);
	       

	        $nombre_campo_frm ="resultado_otros_analsis";
	        $nombre_campo_frm_irca="resultado_otros_analsis_irca";
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);

	        $bandera_cualitativo = "Si";
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && $result_fq->id_equipo==2  && $id_calculo == -1){ //otros analisis (con dilucion)// $result_fq->id_equipo lo use para guardado de dilucion
	        //$salida ="Calcula otros analisis";
	        $vf     =   $result_1 + $result_2;
	        $factor =    $vf / $result_1;
	        $result =    $result_3 * $factor;
	        
	        //$result = 1;

	        $nombre_campo_frm ="resultado_otros_analsis";
	        $nombre_campo_frm_irca="resultado_otros_analsis_irca";
	        
	        //calculamos el IRCA
	        $salida[1] = calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result);

	        
		}else{
	        $salida[0] ="pendiente de datos ";
	    }
	    
	    //comprobamos que el lleno un resuldado
	    
	    if($bandera_cualitativo == "Si"){
		    
		    $salida[0] = $result ;
		    
		    almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
		    
		}elseif($nombre_campo_frm){
	        $result =   round($result, 6);
	        
	        //pasamos el resultado de punto a coma
	        $result = str_replace(".",",",$result);
	        
	        //$salida[0] = "<b>". $result  ." 2:".$result_fq->result_2 ."  3:".$result_fq->result_3 ."  4:".$result_fq->result_4."</b>";
	        $salida[0] = "<b>". $result ."</b>";
	        
	        //guarda campo en result_fq
	        almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	       	almacena_campo_resultado($id_ensayo_vs_muestra, $result);
        
        	//guarda valor parcial irca si existe
        	// $aux_irca = str_replace(".",",",$aux_irca);
	        if( isset($salida[1]) ){
	            almacena_campo_fq($id_ensayo_vs_muestra, $salida[1], $id_parametro, $nombre_campo_frm_irca, 'result_irca');    
	        }
    	}
    	return $salida;
	
	    
	}
	
	
	function calcula_irca_parcial($id_ensayo_vs_muestra, $id_parametro, $result){
    	//calculamos el IRCA
        //1. averiguamos el rango de medicion
        //2. vemos si esta en el rango
        //3. si esta el IRCA es 0
        //4. si no esta el IRCA es el valor por defecto que esta en el parametro
        $fila_ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
        $fila_ensayo = procesar_registro_fetch('ensayo', 'id_ensayo', $fila_ensayo_vs_muestra[0]->id_ensayo, 'id_parametro', $id_parametro);
        $fila_parametro = procesar_registro_fetch('parametro', 'id_parametro', $id_parametro);
        
        //NOTA. Si no solicitaron el parametro 243 IRCA no se calculda
        $fila_ensayo_vs_muestra_irca = procesar_registro_fetch('ensayo', 'id_ensayo', $fila_ensayo_vs_muestra[0]->id_ensayo, 'id_parametro', 243);
       
        if($fila_ensayo_vs_muestra_irca[0]){
            $aux_irca ="No solicitado";
        }
       
        //formateo de rango minimo
            $med_valor_min = ($fila_ensayo[0]->med_valor_min) ? $fila_ensayo[0]->med_valor_min:-0.999999;
            $med_valor_min = str_replace(",",".",$med_valor_min);
        //formateo de rango maximo.
            $med_valor_max = formatea_valor_min_max($fila_ensayo[0]->med_valor_max);
            $med_valor_max = str_replace(",",".",$med_valor_max);
         
            $salida = 'Rango '.$med_valor_min.' a  '.$med_valor_max.' irca '.$fila_parametro[0]->par_irca .'  resultado '.$result;
         
            $aux_irca =($med_valor_min <= $result  &&   $result <= $med_valor_max )?0:$fila_parametro[0]->par_irca;//
            
            $salida .=' resultado IRCA '.$aux_irca;
            
            return  $aux_irca;
            //return  $salida;
	}
	

	function calcula_IRCA($id_muestra_detalle){//$id_ensayo_vs_muestra
	    
	    $salida="Calculos IRCA<br>";
        $aux_todos_llenos ="Si";
        $aux_total_castigo=0;
        $aux_total_analizados=0;
        
        $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_muestra_detalle);
	    $aux_todos_llenos ="Si";
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $id_muestra_detalle = $result_e_v_m[0]->id_muestra;
        
        //$salida2 = fq_tiene_calculo($id_muestra_detalle,26);// 26 IRCA
        //$salida2    = fq_tiene_calculo($id_muestra_detalle,20);// 20 cloruro
        //$result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $salida2[0]->id_ensayo_vs_muestra, 'id_parametro', $salida2[0]->id_parametro);
        //$parametro  = procesar_registro_fetch('parametro', 'id_parametro', $salida2[0]->id_parametro);
        
        //ALCALINIDAD
        $producto    = fq_tiene_calculo($id_muestra_detalle,18,0,$id_f);// 18 ALCALINIDAD
        // return $id_muestra_detalle;
        if (!empty($producto[0])){
            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $producto[0]->id_ensayo_vs_muestra, 'id_parametro', $producto[0]->id_parametro);
            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $producto[0]->id_parametro);
            
            if(isset($result[0]->result_irca)){
                $aux_total_castigo = $aux_total_castigo + $result[0]->result_irca;
                $aux_total_analizados = $aux_total_analizados + $parametro[0]->par_irca;
                
            }else{
                $salida.='Sin ALCALINIDAD';
                $aux_todos_llenos ="No";
                
                return $salida;
            }
            
            //$salida.="<br>  producto : ".$parametro[0]->par_nombre;
            //$salida.="<br>  irca producto : ".$parametro[0]->par_irca;
            //$salida.="<br> resultado : ".$result[0]->result_irca;
        }
        // return $salida;
        
         //ALCALINIDAD 2
        $producto    = fq_tiene_calculo($id_muestra_detalle,19, 0, $id_f);// 19 ALCALINIDAD 2
        if (!empty($producto[0])){
            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $producto[0]->id_ensayo_vs_muestra, 'id_parametro', $producto[0]->id_parametro);
            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $producto[0]->id_parametro);
            
            
            if(!empty($result[0]->result_irca)){
                // inicializa
                $parametro[0]->par_irca = ($parametro[0]->par_irca)?$parametro[0]->par_irca:0;
                
                $aux_total_castigo = $aux_total_castigo + $result[0]->result_irca;
                $aux_total_analizados = $aux_total_analizados + $parametro[0]->par_irca;
                
            }else{
                $salida.='Sin ALCALINIDAD 2';
                $aux_todos_llenos ="No";
                return $salida;
            }
            
            // $salida.="<br>  producto : ".$parametro[0]->par_nombre;
            // $salida.="<br>  irca producto : ".$parametro[0]->par_irca;
            // $salida.="<br> resultado : ".$result[0]->result_irca;
            // return $salida;
        }
        // return $salida;
        //CLORUROS
        $producto    = fq_tiene_calculo($id_muestra_detalle,20, 0, $id_f);// 20 cloruro
        if (!empty($producto[0])){
            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $producto[0]->id_ensayo_vs_muestra, 'id_parametro', $producto[0]->id_parametro);
            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $producto[0]->id_parametro);
            
            if(!empty($result[0]->result_irca)){
                $aux_total_castigo = $aux_total_castigo + $result[0]->result_irca;
                $aux_total_analizados = $aux_total_analizados + $parametro[0]->par_irca;
                
            }else{
                $salida.='Sin CLORUROS';
                $aux_todos_llenos ="No";
                
                return $salida;
            }
            
            //$salida.="<br>  producto : ".$parametro[0]->par_nombre;
            //$salida.="<br>  irca producto : ".$parametro[0]->par_irca;
            //$salida.="<br> resultado : ".$result[0]->result_irca;
        }
        
        //dureza TOTAL
        $producto    = fq_tiene_calculo($id_muestra_detalle,21, 0, $id_f);// 21 dureza total
        if (!empty($producto[0])){
            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $producto[0]->id_ensayo_vs_muestra, 'id_parametro', $producto[0]->id_parametro);
            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $producto[0]->id_parametro);
            
            if(!empty($result[0]->result_irca)){
                $aux_total_castigo = $aux_total_castigo + $result[0]->result_irca;
                $aux_total_analizados = $aux_total_analizados + $parametro[0]->par_irca;
                
            }else{
                $salida.='Sin dureza TOTAL';
                $aux_todos_llenos ="No";
                
                return $salida;
            }
            
            
            //$salida.="<br>  producto : ".$parametro[0]->par_nombre;
            //$salida.="<br>  irca producto : ".$parametro[0]->par_irca;
            //$salida.="<br> resultado : ".$result[0]->result_irca;
        }
        
        // se contemplan los casos de otros analisis
        $otros    = fq_tiene_calculo($id_muestra_detalle,1,1, $id_f);// 1 otros analsis
        //$uax_="";
        foreach ($otros as $key => $fila) {
            //$uax_ .= "<br>".$fila->id_ensayo_vs_muestra ." ".$fila->id_parametro;
            $result     = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $fila->id_ensayo_vs_muestra, 'id_parametro', $fila->id_parametro);
            $parametro  = procesar_registro_fetch('parametro', 'id_parametro', $fila->id_parametro);
            //$uax_ .= " : ".$result[0]->result_irca;
            
            if(!empty($result[0]->result_irca)){
                $aux_total_castigo = $aux_total_castigo + intval($result[0]->result_irca);
                $aux_total_analizados = $aux_total_analizados + $parametro[0]->par_irca;
                
            }else{
                $salida.='Sin '.$parametro[0]->par_nombre;
                $aux_todos_llenos ="No";
                
                return $salida;
            }
        }
        
        $salida.=" <br> Total castigo: ".$aux_total_castigo ;
        $salida.="<br> Total analizados: ".$aux_total_analizados."<br>";
                
        if($aux_todos_llenos=="Si" && $aux_total_analizados <> 0 ){ //LO QUITAMOS CUANDO SE VALIDE EL AUTOCALCULO DE LOS PARAMETROS DIRECTOS
            $irca    = fq_tiene_calculo($id_muestra_detalle,26, 0, $id_f);// 26 IRCA
        
            $aux_total_castigo = $aux_total_castigo;
            $result  = ($aux_total_castigo / $aux_total_analizados) *100;
            //$result  = 11;
                 
            $result =   round($result);
            //pasamos el resultado de punto a coma
            $result = str_replace(".",",",$result);

            almacena_campo_fq($irca[0]->id_ensayo_vs_muestra, $result, $irca[0]->id_parametro, 'resultado_irca', 'result_irca');
            
            //guarda resultado para certificado
            almacena_primer_campo($irca[0]->id_ensayo_vs_muestra, $result);
            
            //validaion resultados
            if($result <= 5 ){
               $result = $result."% SIN RIESGO"; 
            }elseif($result <= 14 ){
               $result = $result."% BAJO"; 
            }elseif($result <= 35 ){
               $result = $result."% MEDIO"; 
            }elseif($result <= 80 ){
               $result = $result."% ALTO"; 
            }else{
               $result = $result."% INVIABLE SANITARIAMENTE"; 
            }
            
            almacena_campo_resultado($irca[0]->id_ensayo_vs_muestra, $result);
            $salida .= $result;
        }else{
            $salida = "Sin resultados";
        }
	
	    return $salida;
	}
	
	
	
	function calcula_dureza_magnesica($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene dureza total id_calculo = 21
	    //3. validar si tiene dureza calcica id_calculo = 22
	    //4. valida si tiene los dos
	    //5. si los tiene se calcula 
	    //6. se retorna mensaje
	    
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $aux_todos_llenos ="Si";
	    
	    $st = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 21);
	    $result_st = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_st[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Dureza total ";
	        $aux_st = str_replace(",",".",$result_st[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene Dureza Total ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 22);
	    $result_grasa = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_grasa[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Dureza Calcica";
	        $aux_grasa = str_replace(",",".",$result_grasa[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene Dureza Calcica ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = ( $aux_st - $aux_grasa ) * 0.242;
            // ajuste decimales
                $result = round($result, $redondeo);
            //pasamos el resultado de punto a coma
                $result = str_replace(".",",",$result);
                        
            //guarda resultado para certificado
                almacena_primer_campo($id_ensayo_vs_muestra, $result);
                almacena_campo_resultado($id_ensayo_vs_muestra, $result);
                $salida .= $result;
            }else{
                $salida .= "<br>Sin resultados";
            }
	    return $salida;
	}
?>