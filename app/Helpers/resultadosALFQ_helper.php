<?php
	function buscaRegistro($tabla,$predicado="",$camposRetorno=""){
		$db = \Config\Database::connect();
		if(!$camposRetorno){
			$camposRetorno="*";
		} 
        $sql = "select  $camposRetorno  from $tabla $predicado";  
		//return $sql."<br>";
        // $miscon = mysql_query($sql);
        //  while ( $row = mysql_fetch_array($miscon,MYSQL_ASSOC) ) {                     
        //      $rower[] = $row;
        // }
        // mysql_free_result($miscon);
        return $db->query($sql)->getResult();
    }
	function fq_tiene_parametro($id_muestra, $id_parametro){
        $tabla = "ensayo_vs_muestra em inner join ensayo en on em.id_ensayo=en.id_ensayo";
        $predicado ='where em.id_muestra='.$id_muestra.' and en.id_parametro = '.$id_parametro;
        $campo_retorno = ('em.id_ensayo_vs_muestra');
                        
        return buscaRegistro($tabla, $predicado, $campo_retorno);
   	}
   	
   	// funciones para calculo fq
   	
   	function fq_tiene_calculo($id_muestra, $id_calculo, $otros_analisis=0, $id_fecha_vida_util=0){ //$otros_analisis para traer datos de otros analisis
   	    $id_fecha_vida_util = $id_fecha_vida_util != 0 ? "= $id_fecha_vida_util":" IS NULL";
        $tabla = "ensayo_vs_muestra em inner join ensayo en on em.id_ensayo=en.id_ensayo INNER JOIN parametro pa on pa.id_parametro = en.id_parametro ";
        $predicado ='where em.id_muestra='.$id_muestra.' and pa.id_calculo = '.$id_calculo." and em.id_fecha_vida_util $id_fecha_vida_util";
        $campo_retorno = 'em.id_ensayo_vs_muestra, pa.id_parametro, pa.id_calculo, pa.par_nombre ';
        
        if($otros_analisis == 1){
            $predicado .= " AND pa.par_irca <>'' ";
            $campo_retorno .= ", pa.par_irca ";
        }                
        return buscaRegistro($tabla, $predicado, $campo_retorno);
   	}
   	
   	function disable_frm($valor, $user_rol_id){
       	if($valor){
            if( $user_rol_id ==1 || $user_rol_id ==2 || $user_rol_id ==3){
                //1 super admin
                //2 gerencia
                //3 director tecnico
                $disable ='';
            }else{        
                $disable ='disabled="true"';
            }
        }else{
            $disable ='';
        }
        return $disable;
   	}
   	
   	function cambiar_campos_resultados_fq_directo($id_ensayo_vs_muestra, $valor, $id_parametro, $nombre_campo_frm, $nombre_campo_bd){
	    //1. evalue si el campo es nuemerico (solo se permite numeros)
	    //2. si es numero
	    //3. es posible que ya este creado. preguntamos si ya esta creado el registro en fq
	    //4. si esta creado se actualiza
	    //5. no creado se inserta
	    //6. se pregunta si ya estan todos los valores para emitir resultado
	    
	    if (is_numeric (str_replace(",","",$valor))) {
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $valor);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
	        
	        //almacena_campo_fq($id_ensayo_vs_muestra, $valor, $id_parametro, $nombre_campo_frm, $nombre_campo_bd);
	        //$mensaje_resultado = comprueba_calcular_resultado($id_ensayo_vs_muestra,  $id_parametro);
	        
	        $respuesta->assign($nombre_campo_frm,"style", "border: 2px solid green;");  
	        $respuesta->assign($nombre_campo_frm,"disabled","true");
	        $respuesta->assign($nombre_campo_frm,"onblur","");
	        
	        $mensaje .="Ok";
	    }else{
	        //almacena_primer_campo($id_ensayo_vs_muestra, $valor);
	        //almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
	        
	        $respuesta->assign($nombre_campo_frm,"style", "border: 2px solid  red;");            
	        $mensaje ="Valor no permitido";
	    }         
	    $respuesta->assign("campo_repuesta_%_".$id_ensayo_vs_muestra, "innerHTML", $mensaje_resultado);
	    $respuesta->assign($campo_salida, "innerHTML", $mensaje);
	    return $respuesta;
	}
	
   
   	function almacena_campo_fq($id_ensayo_vs_muestra, $valor, $id_parametro, $nombre_campo_frm, $nombre_campo_bd){
	    
	    $result_humedad = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra, 'id_parametro', $id_parametro);
	    if($result_humedad[0]->id_ensayo_vs_muestra){
	        $texto = "update ensa_mues_fq set
	                    $nombre_campo_bd = '$valor'
	                    
	                    where id_ensayo_vs_muestra = $id_ensayo_vs_muestra";
	                    //, id_user= ".$_SESSION['user_id']."  se activara si se requiere almacenar el ultimo que lo modifico
	    }else{
	        $texto = "insert into ensa_mues_fq (id_ensayo_vs_muestra, id_parametro, $nombre_campo_bd, id_user)
	                   VALUES
	                   ($id_ensayo_vs_muestra, $id_parametro, '".$valor."', ".session('user')->id." )";
	    }
	    //echo texto;
	    //die();
	    $db = \Config\Database::connect();
	    $query = $db->query($texto);    
	   
	    $nombre_campo_frm = str_replace("frm_","",$nombre_campo_frm);
	    
	    almacena_auditoria($id_ensayo_vs_muestra, $valor,$nombre_campo_frm);
	      
	    return $texto;  
	}
	
	//calculos_fq ($id_calculo)
	// 2 Alimentos Humedad
	
	function comprueba_calcular_resultado($id_ensayo_vs_muestra, $id_parametro, $id_calculo){
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
    	
    	$salida ='';
    	$bandera_cualitativo = 'No';
    	$nombre_campo_frm = '';
    	
    	//return $id_ensayo_vs_muestra.'<--->'.$id_parametro;
    	
    	if($result_fq[0]->id_equipo ) {
    	    
    	    //$salida ='1003';
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
        	            $salida ="Calcula otros analisis sin dilución";
	                    $result =    ($result_1)?$result_1:$result_3;

	                    $nombre_campo_frm ="resultado_otros_analsis";
	                    $bandera_cualitativo = "Si";
	        
        	        }else{
        	            $salida ="Calcula otros analisis con dilución";
	                    $vf     =   $result_1 + $result_2;
	                    $factor =    $vf / $result_1;
	                    $result =    $result_3 * $factor;
	      
	                    $nombre_campo_frm ="resultado_otros_analsis";
        	        }
        	    }else{
        	        try {
        	        
            	        $formula = str_replace("+ -","-",$formula);
    	                $formula = str_replace("--","+",$formula);
                        $formula = str_replace(",",".",$formula);
            	        
            	        $result = eval( 'return '.$formula.';'  );
            	        // ajuste decimales
                            //$result = round($result, $redondeo); // para activar redondeo se debe activar redondeo desde la vista
                        //pasamos el resultado de punto a coma
                            $result = str_replace(".",",",$result);
               
                    } catch (Throwable $t) {
                        $result = "<span style='color:red' >Error: por favor valide la formula del sistema</span>";
                    }
                    
            	    
            	    $salida ="Calcula el dato  |". $result."|";
        	        $nombre_campo_frm ="resultado_".$calculos_fq[0]->sigla; // para auditoria    
    	        
        	    }
        	}
    	}// fin de validar equipo
    	
    	if($bandera_cualitativo == "Si"){
		    
		    $salida = $result;
		    
		    almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
		    
		}elseif($nombre_campo_frm){
	        
	        //pasamos el resultado de punto a coma
	        $result = str_replace(".",",",$result);
	        $salida = $result  ." %";
	        
	        
	        //guarda campo en result_fq
	        almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
	        
	    }
    
    
	    return $salida;//.'102'.$result_fq[0]->id_equipo;
	
	    
	}
	
	function comprueba_calcular_resultado_old($id_ensayo_vs_muestra, $id_parametro, $id_calculo){
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
    	
    	$salida ='';
    	$bandera_cualitativo = 'No';
    	$nombre_campo_frm = '';
    	
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
        	    
        	    try {
        	        
        	        $formula = str_replace("+ -","-",$formula);
	                $formula = str_replace("--","+",$formula);
                    $formula = str_replace(",",".",$formula);
        	        
        	    $result = eval( 'return '.$formula.';'  );
        	    // ajuste decimales
                    //$result = round($result, $redondeo); // para activar redondeo se debe activar redondeo desde la vista
                //pasamos el resultado de punto a coma
                    $result = str_replace(".",",",$result);
               
                } catch (Throwable $t) {
                    $result = "<span style='color:red' >Error: por favor valide la formula del sistema</span>";
                }
                
        	    
        	    
        	    //$result = eval( 'return '.$formula.';'  );
        	    //$result = 1;
        	    if($id_calculo == 2){
        	        $salida ="Calcula el dato humedad |". $result."|";
    	            $nombre_campo_frm ="resultado_humedad"; // para auditoria    
        	    
        	    }elseif($id_calculo == 3 or  $id_calculo == 4 or  $id_calculo == 5 ){
        	        $salida ="Calcula el dato solidos totales, cenizas, grasas";
        	        $nombre_campo_frm ="resultado_solidos_totales";// para auditoria
        	    
        	    }elseif($id_calculo == 6){
        	        $salida ="Calcula proteinas";
        	        $nombre_campo_frm ="resultado_proteina";// para auditoria
        	        
        	    }elseif($id_calculo == 7){
        	        $salida ="Calcula el dato fibra cruda";
        	        $nombre_campo_frm ="resultado_fibra_cruda";// para auditoria
        	       
        	    }elseif($id_calculo == 8){
        	        $salida ="Calcula el dato azucares totales";
        	        $nombre_campo_frm ="resultado_fibra_cruda";// para auditoria
        	       
        	    }
        	    elseif($id_calculo == 9){
        	        $salida ="Calcula el dato acidez";
        	        $nombre_campo_frm ="resultado_acidez";// para auditoria
        	       
        	    }elseif($id_calculo == 10){
        	        $salida ="Calcula el dato densidad";
        	        $nombre_campo_frm ="resultado_densidad";// para auditoria
        	       
        	    }elseif($id_calculo == 11){
        	        $salida ="Calcula el dato acidez en aceites";
	                $nombre_campo_frm ="resultado_acidez_en_aceites";// para auditoria
        	       
        	    }elseif($id_calculo == -1){//CALCULO DIRECTO
        	    
        	        if($result_fq[0]->id_equipo==1  ){//otros analisis (sin dilucion)// $result_fq->id_equipo lo use para guardado de dilusion
        	            $salida ="Calcula otros analisis sin dilución";
	                    $result =    ($result_1)?$result_1:$result_3;

	                    $nombre_campo_frm ="resultado_otros_analsis";
	                    $bandera_cualitativo = "Si";
	        
        	        }else{
        	            $salida ="Calcula otros analisis con dilución";
	                    $vf     =   $result_1 + $result_2;
	                    $factor =    $vf / $result_1;
	                    $result =    $result_3 * $factor;
	      
	                    $nombre_campo_frm ="resultado_otros_analsis";
        	        }
        	    }
        	}
    	}// fin de validar equipo
    	
    	if($bandera_cualitativo == "Si"){
		    
		    $salida = $result ;
		    
		    almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
		    
		}elseif($nombre_campo_frm){
	        
	        //pasamos el resultado de punto a coma
	        $result = str_replace(".",",",$result);
	        $salida = $result  ." %";
	        //guarda campo en result_fq
	        almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
	    }
    
    
	    return $salida;
	
	    
	}
	
	function comprueba_calcular_resultado_2($id_ensayo_vs_muestra, $id_parametro, $id_calculo){
	    $result_fq = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra, 'id_parametro', $id_parametro);
	    
    	$result_1 = str_replace(",",".",$result_fq[0]->result_1);
    	$result_2 = str_replace(",",".",$result_fq[0]->result_2);
    	$result_3 = str_replace(",",".",$result_fq[0]->result_3); 
    	$result_4 = str_replace(",",".",$result_fq[0]->result_4);
    	$result_5 = str_replace(",",".",$result_fq[0]->result_5);
    	$result_6 = str_replace(",",".",$result_fq[0]->result_6);
    	$result_7 = str_replace(",",".",$result_fq[0]->result_7);
    	
    	
    	$nombre_campo_frm ="";
    	//humedad = 2
    	//para humedad los campos result_1, result_3 son solo para almacenar
    	
    	if(isset($result_2) && isset($result_4) && isset($result_5)  && $result_fq[0]->id_equipo && $id_calculo == 2){
	        $salida ="Calcula el dato humedad ";
	        $result =   ( (  $result_5 - ( $result_2 - $result_4 )  ) / $result_5  ) * 100;
	        $nombre_campo_frm ="resultado_humedad"; // para auditoria
	        
		}elseif(isset($result_2) && isset($result_4) && isset($result_5)  && $result_fq[0]->id_equipo && ( $id_calculo == 3 or  $id_calculo == 4 or  $id_calculo == 5 )){
	        $salida ="Calcula el dato solidos totales, cenizas, grasas";
	        $result =   ( ( $result_2 - $result_4 )  / $result_5  ) * 100;
	        $nombre_campo_frm ="resultado_solidos_totales";// para auditoria
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && isset($result_4) && $result_fq[0]->id_factor && $result_fq[0]->id_equipo &&  $id_calculo == 6 ){
		    $salida ="Calcula proteinas";
		    $fila_factor = procesar_registro_fetch ('factor_fq', 'id_factor', $result_fq[0]->id_factor);
		    $factor = str_replace(",",".",$fila_factor[0]->valor);
	        $result =  ( ( ( ( $result_1 - $result_2 ) * $result_3 * 14.01 ) / $result_4 * 10  ) * $factor ) / 100;
	        //$result =  100;
	        $nombre_campo_frm ="resultado_proteina";// para auditoria
	        
		}elseif(isset($result_2) && isset($result_4) && isset($result_5)  && $result_fq[0]->id_equipo &&  $id_calculo == 7){
	        $salida ="Calcula el dato fibra cruda";
	        $result =   ( ( $result_4 - $result_2 )  / $result_5  ) * 100;
	        $nombre_campo_frm ="resultado_fibra_cruda";// para auditoria
	        
		}elseif(isset($result_2) && isset($result_4) && isset($result_5)  && $result_fq[0]->id_equipo &&  $id_calculo == 8){
	        $salida ="Calcula el dato azucares totales";
	        $result =  ( ( $result_1 * $result_2 * $result_3 * $result_4 ) / ( $result_5 * $result_6 * 10 ) );
	        $nombre_campo_frm ="resultado_fibra_cruda";// para auditoria
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3)  && $result_fq[0]->id_factor && $result_fq[0]->id_equipo &&  $id_calculo == 9){
	        $salida ="Calcula el dato acidez";
	        $fila_factor = procesar_registro_fetch ('factor_fq', 'id_factor', $result_fq[0]->id_factor);
		    $factor = str_replace(",",".",$fila_factor[0]->valor);
	        $result =  ( ( $result_3 * $result_1 * $factor  ) /  $result_2   ) * 100;
	        $factor = str_replace(",",".",$fila_factor[0]->valor);
	        $nombre_campo_frm ="resultado_acidez";// para auditoria
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3)   && $result_fq[0]->id_equipo &&  $id_calculo == 10){
	        $salida ="Calcula el dato densidad";
	        $result = ( ( $result_3 - $result_1 ) / 10)  /  ( ($result_2 - $result_1) / 10 ) ;
	        $nombre_campo_frm ="resultado_densidad";// para auditoria
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3)  && $result_fq[0]->id_factor && $result_fq[0]->id_equipo &&  $id_calculo == 11){
	        $salida ="Calcula el dato acidez en aceites";
	        $fila_factor = procesar_registro_fetch ('factor_fq', 'id_factor', $result_fq[0]->id_factor);
		    $factor = str_replace(",",".",$fila_factor[0]->valor);
	        $result =   ( $result_1 * $result_3 * $factor  ) /  ( $result_2 * 10   ) ;
	        $nombre_campo_frm ="resultado_acidez_en_aceites";// para auditoria
		}
		
		elseif( ( isset($result_1) || isset($result_3) )  && $result_fq[0]->id_equipo==1  && $id_calculo == -1){ //otros analisis (sin dilucion)// $result_fq->id_equipo lo use para guardado de dilusion
	        $salida ="Calcula otros analisis";
	        $result =    ($result_1)?$result_1:$result_3;

	        $nombre_campo_frm ="resultado_otros_analsis";
	        $bandera_cualitativo = "Si";
	        
	       
	        
		}elseif(isset($result_1) && isset($result_2) && isset($result_3) && $result_fq[0]->id_equipo==2  && $id_calculo == -1){ //otros analisis (con dilucion)// $result_fq->id_equipo lo use para guardado de dilucion
	        $salida ="Calcula otros analisis";
	        $vf     =   $result_1 + $result_2;
	        $factor =    $vf / $result_1;
	        $result =    $result_3 * $factor;
	      
	        $nombre_campo_frm ="resultado_otros_analsis";
	       
		}
		
		/*
		else{
		    $salida ="Calcula exs";
		    $fila_factor = procesar_registro_fetch ('factor_fq', 'id_factor', $result_fq[0]->id_factor);
		    $factor = str_replace(",",".",$fila_factor[0]->valor);
	        $result =  ( ( ( ( $result_1 - $result_2 ) * $result_3 * 14.01 ) / $result_4 * 10  ) * $factor ) / 100;
	        $nombre_campo_frm ="resultado_acidez_en_aceites";// para auditoria   
		}
		*/
		
		if($bandera_cualitativo == "Si"){
		    
		    $salida = $result ;
		    
		    almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
		    
		}elseif($nombre_campo_frm){
	        $result =   round($result, 2);
	        //pasamos el resultado de punto a coma
	        $result = str_replace(".",",",$result);
	        $salida .= $result  ." %";
	        //guarda campo en result_fq
	        almacena_campo_fq($id_ensayo_vs_muestra, $result, $id_parametro, $nombre_campo_frm, 'result_8');
	        //guarda resultado para certificado
	        almacena_primer_campo($id_ensayo_vs_muestra, $result);
	        almacena_campo_resultado($id_ensayo_vs_muestra, $result);
	    }
	        
	    return $salida;
	
	    
	}

	
	function valida_calculos_genericos($id_muestra){//eliminiar
	    
	    $result_calculo = fq_tiene_calculo($id_muestra, 3);
	    $result_valor = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $result_calculo[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_valor[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Solidos Totales ";
	        $aux_st = str_replace(",",".",$result_valor[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene Solidos Totales ";
	        $aux_todos_llenos ="No";
	    }
	    
	}
	
	function calcula_fq_compuestos($id_ensayo_vs_muestra, $redondeo, $id_calculo){
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $aux_todos_llenos ="Si";
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    
	    //1. unicamos la formula
	    
	    
	    $calculos_fq    = procesar_registro_fetch('calculos_fq', 'id_calculo', $id_calculo);
	    $array_siglas   = buscaRegistro('calculos_fq', '', 'id_calculo, nombre, sigla');
	    
	    $formula        = $calculos_fq[0]->formula_sistema;
	    
	    $salida ="Formula: ".$formula;
	    //$salida .="<br>id_ensayo_vs_muestra:".$id_ensayo_vs_muestra;
	    
	    foreach($array_siglas as $item => $value){
	        
	        
	        /*
            $salida .="<br>array_siglas ".$array_siglas[$item]->id_calculo;
	        $salida .=" || ".$array_siglas[$item]->nombre;
	        $salida .=" || ".$array_siglas[$item]->sigla;
	        */
	        $array_siglas[$item]->sigla = ($array_siglas[$item]->sigla)?$array_siglas[$item]->sigla:'wdd';
	      
	        $pos = strpos($formula, $array_siglas[$item]->sigla);
            
            if (!($pos === false)) {
            
                //$salida.="<br> Formula con sigla ".$array_siglas[$item]->sigla;
                
                $st = fq_tiene_calculo($result_e_v_m[0]->id_muestra, $array_siglas[$item]->id_calculo, 0, $id_f);
                $result_st = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
	    
        	    if (isset($result_st[0]->id_ensayo_vs_muestra)){
        	
        	        $salida.="<br>Tiene resultados para ".$array_siglas[$item]->nombre. " (".$array_siglas[$item]->sigla.")" ;
        	        
        	        $aux_resultado = str_replace(",",".",$result_st[0]->result_8);
        	        $formula = str_replace($array_siglas[$item]->sigla,$aux_resultado,$formula);
        	    }else{
        	        
        	        // para los casos en que se almacena en ensayo_vs_muestra
        	        $result_st = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
        	        
        	        if (isset($result_st[0]->id_ensayo_vs_muestra)){
        	
            	        $salida.="<br>Tiene resultados para ".$array_siglas[$item]->nombre. " (".$array_siglas[$item]->sigla.")" ;
            	        
            	        $aux_resultado = str_replace(",",".",$result_st[0]->resultado_mensaje);
            	        $formula = str_replace($array_siglas[$item]->sigla,$aux_resultado,$formula);
            	        
            	    }else{
            	        
        	            $salida             .=  "<br>No tiene resultados ".$array_siglas[$item]->nombre;
        	            $aux_todos_llenos   =   "No ";
            	    }
        	    }
            }
	        
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        
        //si todos los que tiene estan calculados se calcula 
        
        if($aux_todos_llenos=="Si"){
            //formateo de la formula
	        $formula = str_replace("+ -","-",$formula);
	        $formula = str_replace("--","+",$formula);
            $formula = str_replace(",",".",$formula);
            
        	//$result = eval( 'return '.$formula.';'  );
        	
        	try {
        	    
        	    $result = eval( 'return '.$formula.';'  );
        	    // ajuste decimales
                    //$result = round($result, $redondeo); // para activar redondeo se debe activar redondeo desde la vista
                //pasamos el resultado de punto a coma
                    $result = str_replace(".",",",$result);
                            
                //guarda resultado para certificado
                    almacena_primer_campo($id_ensayo_vs_muestra, $result);
                    almacena_campo_resultado($id_ensayo_vs_muestra, $result);
        	    
        	    
                } catch (Throwable $t) {
                    $result = "<span style='color:red' >Error: por favor valide la formula del sistema</span>";
                }
                
                $salida .= $result;
                
                $salida .= "<br>".$formula;
        }else{
                $salida .= "<br>Sin resultados";
        }

	    
	    return $salida;
	
	}
	function calcula_fq_independientes($id_ensayo_vs_muestra, $redondeo, $id_calculo){
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $aux_todos_llenos ="Si";
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    
	    //1. ubicamos la formula 
	    //2. recorremos todas las siglas vs la formula
	    //3. si la encontramos buscamos si ya fue diligenciada
	    //4. si no esta se cambia la bandera
	    
	    $calculos_fq    = procesar_registro_fetch('calculos_fq', 'id_calculo', $id_calculo);
	    
        $array_siglas   = buscaRegistro('calculos_fq', '', 'id_calculo, nombre, sigla');
        $salida         = "";
        $formula        = $calculos_fq[0]->formula_sistema;
        
        foreach($array_siglas as $item => $value){
            
            $pos = strpos($calculos_fq[0]->formula_sistema, $array_siglas[$item]->sigla);
            
            if (!($pos === false)) {
            
                $salida.="<br> Formula con sigla ".$array_siglas[$item]->sigla;
                
                $st = fq_tiene_calculo($result_e_v_m[0]->id_muestra, $array_siglas[$item]->id_calculo, 0, $id_f);
                $result_st = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
	    
        	    if (isset($result_st[0]->id_ensayo_vs_muestra)){
        	
        	        $salida.="<br>Si tiene resultados de ".$array_siglas[$item]->nombre;
        	        $aux_resultado = str_replace(",",".",$result_st[0]->result_8);
        	        $formula = str_replace($array_siglas[$item]->sigla,$aux_resultado,$formula);
        	    }else{
        	        
        	        // para los casos en que se almacena en ensayo_vs_muestra
        	        $result_st = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
        	        
        	        if (isset($result_st[0]->id_ensayo_vs_muestra)){
        	
            	        $salida.="<br>Si tiene resultados de ".$array_siglas[$item]->nombre;
            	        $aux_resultado = str_replace(",",".",$result_st[0]->resultado_mensaje);
            	        $formula = str_replace($array_siglas[$item]->sigla,$aux_resultado,$formula);
            	        
            	    }else{
            	        
        	            $salida             .=  "<br>No tiene ".$array_siglas[$item]->nombre;
        	            $aux_todos_llenos   =   "No ";
            	    }
        	    }
            }
        }
        
        $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        
        //si todos los que tiene estan calculados se calcula 
        
        if($aux_todos_llenos=="Si"){
            //formateo de la formula
	        $formula = str_replace("+ -","-",$formula);
	    
            $formula = str_replace(",",".",$formula);
        	$result = eval( 'return '.$formula.';'  );
        	    
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
        
	    return $salida;//."|".$formula."|".$id_ensayo_vs_muestra;
	}
	
	function calcula_soli_tota_no_graso($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene ST id_calculo =3 
	    //3. validar si tiene grasa id_calculo =5
	    //4. valida si tiene los dos
	    //5. si los tiene se calcula ( ST - Grasa)
	    //6. se retorna mensaje
	    
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $aux_todos_llenos ="Si";
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    
	    $st = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 3, 0, $id_f);
	    $result_st = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_st[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Solidos Totales ";
	        $aux_st = str_replace(",",".",$result_st[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene Solidos Totales ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 5, 0, $id_f);
	    $result_grasa = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_grasa[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene GRASA ";
	        $aux_grasa = str_replace(",",".",$result_grasa[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene GRASA ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = $aux_st - $aux_grasa;
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
	
	function calcula_carbohidratos($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene humedad id_calculo = 2 
	    //3. validar si tiene Cenizas id_calculo = 4
	    //4. validar si tiene grasa id_calculo = 5
	    //5. validar si tiene Fibra cruda id_calculo = 7
	    //6. validar si tiene Proteina id_calculo = 6
	    
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $aux_todos_llenos ="Si";
	    
	    $hu = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 2, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $hu[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Humedad ";
	        $aux_hu = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Humedad";
	        $aux_todos_llenos ="No";
	    }
	    
	    $ce = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 4, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $ce[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Ceniza ";
	        $aux_ce = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Ceniza";
	        $aux_todos_llenos ="No";
	    }
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 5, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene GRASA ";
	        $aux_grasa = str_replace(",",".",$result[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene GRASA ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $ficu = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 7, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $ficu[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Fibra cruda ";
	        $aux_ficu = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Fibra cruda";
	        $aux_todos_llenos ="No";
	    }
	    
	    $pro = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 6, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $pro[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Proteina ";
	        $aux_pro = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Proteina";
	        $aux_todos_llenos ="No";
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = 100 -( $aux_hu + $aux_ce + $aux_grasa + $aux_ficu + $aux_pro);
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
	
	function calcula_calorias($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene carbohidratos id_calculo = 13 
	    //3. validar si tiene grasa id_calculo = 5
	    //4. validar si tiene Proteina id_calculo = 6
	    
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $aux_todos_llenos ="Si";
	    
	    $ca = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 13, 0, $id_f);
	    
	    //$result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $hu[0]->id_ensayo_vs_muestra);
	    $result = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $ca[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene carbohidratos ";
	        $aux_ca = str_replace(",",".",$result[0]->resultado_mensaje);
	        
	    }else{
	        $salida.="<br>No tiene carbohidratos";
	        $aux_todos_llenos ="No";
	    }
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 5, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene GRASA ";
	        $aux_grasa = str_replace(",",".",$result[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene GRASA ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $pro = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 6, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $pro[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Proteina ";
	        $aux_pro = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Proteina";
	        $aux_todos_llenos ="No";
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = ( $aux_pro + $aux_ca ) *4 + ( $aux_grasa * 9 );
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
	
	function calcula_grasa_seco($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene humedad id_calculo = 2 

	    //4. validar si tiene grasa id_calculo = 5

	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $aux_todos_llenos ="Si";
	    
	    $hu = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 2, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $hu[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Humedad ";
	        $aux_hu = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Humedad";
	        $aux_todos_llenos ="No";
	    }
	    
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 5, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene GRASA ";
	        $aux_grasa = str_replace(",",".",$result[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene GRASA ";
	        $aux_todos_llenos ="No";
	    }
	    
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = ($aux_grasa / ( 100 - $aux_hu) ) *  100 ;
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
	
	function calcula_humedad_grasa($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene humedad id_calculo = 2 

	    //4. validar si tiene grasa id_calculo = 5

	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $aux_todos_llenos ="Si";
	    
	    $hu = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 2, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $hu[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Humedad ";
	        $aux_hu = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Humedad";
	        $aux_todos_llenos ="No";
	    }
	    
	    
	    $grasa = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 5, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $grasa[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene GRASA ";
	        $aux_grasa = str_replace(",",".",$result[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene GRASA ";
	        $aux_todos_llenos ="No";
	    }
	    
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = ($aux_grasa + $aux_hu)  ;
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
	
	
	function calcula_proteina_seca($id_ensayo_vs_muestra, $redondeo){
	    //1. datos registrados
	    //2. validar si tiene solidos totales id_calculo = 3
	    
	    //4. validar si tiene Proteina id_calculo = 6
	    
	    $result_e_v_m = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
	    $id_f = !empty($result_e_v_m[0]->id_fecha_vida_util) ? $result_e_v_m[0]->id_fecha_vida_util : 0;
	    $aux_todos_llenos ="Si";
	    
	    $st = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 3, 0, $id_f);
	    $result_st = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $st[0]->id_ensayo_vs_muestra);
	    
	    if (isset($result_st[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Solidos Totales ";
	        $aux_st = str_replace(",",".",$result_st[0]->result_8);
	        
	    }else{
	        $salida.="<br>No tiene Solidos Totales ";
	        $aux_todos_llenos ="No";
	    }
	    
	    $pro = fq_tiene_calculo($result_e_v_m[0]->id_muestra, 6, 0, $id_f);
	    $result = procesar_registro_fetch('ensa_mues_fq', 'id_ensayo_vs_muestra', $pro[0]->id_ensayo_vs_muestra);
	    if (isset($result[0]->id_ensayo_vs_muestra)){
	        $salida.="<br>Si tiene Proteina ";
	        $aux_pro = str_replace(",",".",$result[0]->result_8);
	    }else{
	        $salida.="<br>No tiene Proteina";
	        $aux_todos_llenos ="No";
	    }
	    
	    $salida.="<br>todos llenos: ".$aux_todos_llenos."<br>Total :  ";
        //si todos los que tiene estan calculados se calcula carbohidratos
        if($aux_todos_llenos=="Si"){
            $result = ( $aux_pro * 100 ) / $aux_st;
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