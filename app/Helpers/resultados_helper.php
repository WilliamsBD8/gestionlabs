<?php
	function evalua_alerta($med_valor_min ,$med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra, $quien_invoca=1){//2 son de formaulario de consultas y no generea correo
    	
    	
    	$find_signo_menor  = '<';
    	$bandera_signo_menor_lmax   = 0;
    	
    	$bandera_signo_mayor_en_valor =0;
    	
    	$pos_signo_menor = strpos($med_valor_max, $find_signo_menor);
    	if($pos_signo_menor === 0){
    	    $bandera_signo_menor_lmax   = 1;        
    	}
    	 
    	 
    	$med_valor_min = formatea_valor_min_max($med_valor_min);
    	$med_valor_max = formatea_valor_min_max($med_valor_max);
    	//$valor = formatea_mh_total($valor);
    	$mensaje ='';
    	
    	
   
    
	    if(strlen($med_valor_min)>0 || strlen($med_valor_max)>0 ){//|| $med_valor_max || $med_valor_max==0
	        
	        $med_valor_min_sin_tratamiento = $med_valor_min;
	        //tipo de analisis Microbiologia el minimo es cero
	        //1 MICROBIOLÓGICO  ALIMENTOS
	        //2 MICROBIOLÓGICO AGUA  
	        //4 MICROBIOLÓGICO
    	    if($id_tipo_analisis== 1 || $id_tipo_analisis== 2 || $id_tipo_analisis== 4){
    	        $med_valor_min=0;   
    	    }
    	    
    	    $valorOriginal = $valor; //se conserva para tratar en casos con acreditación
    	
	        //$valor   = str_replace('<', '',$valor);
	        //$valor   = str_replace('>', '',$valor);
	        //$valor   = str_replace('.', '',$valor);//se comentarea el 14/10/2022 caso muestra 7233 no evalua bien
	        $valor   = str_replace(',', '.',$valor);//para que sea numerico.
	        $valor   = trim($valor);
	        //$valor_aux = $valor;
	        $findsup = '10sup';
	        $findsup2 = '10<sup>';
            $findml  = '(M)';
            $find_signo_menor  = '<';
            $find_signo_mayor  = '>';
            
            $possup = strpos($valor, $findsup);
            $possup2 = strpos($valor, $findsup2);
            $posml = strpos($valor, $findml);
            $pos_signo_menor = strpos($valor, $find_signo_menor);
            $pos_signo_mayor = strpos($valor, $find_signo_mayor);
            
            $bandera_signo_mayor_en_valor = 0;       
            if($pos_signo_mayor === 0){
    	        $bandera_signo_mayor_en_valor = 1;        
    	    }
    	
            /*
	        if($posml > 0){
	            //formateo de ml a numero ejemplo 30 (M) + <1 (L)  a 31
	            $valor = formateo_mohos_y_levaduras_a_numero($valor);
	            $aux_x = $valor;
	        }elseif( $possup > 0){
	            //formateo de expresiones Asbiquim ejemplo 3.0 x 10 elevado a la 3
	            $valor = formateo_exponenciales_a_numero($valor);
	            //$valor = formateo_exponenciales_a_numero2($valor);
	            
	        }elseif($pos_signo_menor === 0){
	            $valor = formateo_mohos_y_levaduras_a_numero($valor);
	        }
	        */
	        $aux_x =$valor;
	        if ($posml > 0 ||  $possup > 0 ||  $possup2 > 0 || $pos_signo_menor === 0 || $pos_signo_mayor === 0){
	            $aux_a = $aux_x;
	            $valor = formateo_mohos_y_levaduras_a_numero($valor);
	            $aux_x = $valor;
	        }
	        
	        
	        if(is_numeric($valor) ){
			
				$med_valor_max =(strlen($med_valor_max)>0)?$med_valor_max:9999999;
				
				$med_valor_min   = str_replace(',', '.',$med_valor_min);//para que sea numerico
	            $med_valor_max   = str_replace(',', '.',$med_valor_max);//para que sea numerico
	            
				// validamos si es por acreditaciones
				// con el $id_ensayo_vs_muestra obtenemos el ensayo y obtenmos el parametro
				
				$aux_acreditacion = obtiene_acreditracion($id_ensayo_vs_muestra); 
	            
	           
	            if( $aux_acreditacion[0]->id_acreditacion > 1){
	                //1. se obtiene los logaritmo de max y valor y luego se promedian
	                //2. se calcula la media
	                
	                //https://localcoder.org/how-to-generate-a-cumulative-normal-distribution-in-php
	                //https://stackoverflow.com/questions/52820831/calculate-normal-distribution-probability-in-php
	                //https://github.com/gburtini/Probability-Distributions-for-PHP
	                
	                /* version distribucion normal
	                $log_max = log10( $med_valor_max );
	                $log_val = log10( $valor );
	                $media   =   ( $log_max + $log_val ) / 2;
	                $z = ( $log_max - $media ) / $aux_acreditacion[0]->valor;
	                
	                */
	                
	                /* version distribucion normal ajsutada a Asbioquim*/
	                
	                if(preg_match("/< 1/",$valorOriginal)){ // si el valor tiene un < 1, quiere decir que se convirtio de cero a < por manejode diluciones (aplica para < 10 < 100 < 1000)
	                    $valor = 0;
	                }elseif(preg_match("/<1/",$valorOriginal)){ // si el valor tiene un <1, quiere decir que se convirtio de cero a < por manejode diluciones
	                    $valor = 0;
	                }
	                
	                
	                $med_valor_max_aux = floatval($med_valor_max);
	                
	                $log_max = log10( $med_valor_max_aux );
	                $log_val = log10( $valor );
	                $z = ( $log_max - $log_val ) / $aux_acreditacion[0]->valor;
	               
	                $dsn = cumnormdist($z);
	                
	                $dsn = round($dsn * 100);
	                

	                $mensaje.= "<br><b>Acreditación ".$aux_acreditacion[0]->nombre."</b><br>LimSup: ".$med_valor_max
	                            ."<br>Valor: ".$valor
	                            ."<br>Desv Std: ".$aux_acreditacion[0]->valor
	                            ."<br>log(LimSup): ".$log_max
	                            ."<br>log(LimInf) : ".$log_val
	                           ." <br>DSN: ".$dsn."% <br>";
	                
	                $mensaje.=( $dsn >=  95  ) ? '-MAN-No genera Alerta 1' :'-MAS-SI genera Alerta 1:';   //.$id_ensayo_vs_muestra
	                
	                
	            }else{// RDSA
	               
	                $mensaje.= "<br>LimInfr: $med_valor_min  "
	                        ."<br>LimSup: ".$med_valor_max
	                        ."<br>Valor: ".$valor."<br>";
	                
	                //$mensaje.= "|".$aux_a."|<br>";
	                $mensaje.= "|".$aux_x."|<br>";
	                
	                
	                if ($bandera_signo_menor_lmax == 1){
	                    /*
	                    Casos para validar MB
	                    3233  -> MAb Mes (valor = >300 ) Limite superior( < 50 )  Ensayo (Ambientes)
	                    */
	                    
                        $mensaje .= ($med_valor_min <= $valor && $valor < $med_valor_max  ) ? '-MAN-No genera Alerta 2.0 ' :'-MAS-SI genera Alerta 2.0:';        
	                    /*
	                    if ($bandera_signo_mayor_en_valor == 1){
	                        $mensaje .= ($med_valor_min <= $valor && $valor > $med_valor_max  ) ? '-MAN-No genera Alerta 2.0 ' :'-MAS-SI genera Alerta 2.0:';    
	                    }else{
	                        $mensaje .= ($med_valor_min <= $valor && $valor < $med_valor_max  ) ? '-MAN-No genera Alerta 2.1 ' :'-MAS-SI genera Alerta 2.1:';        
	                    }
	                    */
	                    
	                }else{
	                    
	                    /*if ($bandera_signo_mayor_en_valor == 1){
	                        $mensaje .= ($med_valor_min <= $valor && $valor > $med_valor_max  ) ? '-MAN-No genera Alerta 2.2 ' :'-MAS-SI genera Alerta 2.2:';    
	                    }else{*/
	                        $mensaje .= ($med_valor_min <= $valor && $valor <= $med_valor_max  ) ? '-MAN-No genera Alerta 2.3 ' :'-MAS-SI genera Alerta 2.3:';        
	                   // }
	                    
	                }
	           }
	           // Ajuste el 11 de febrero de 2024
	           // Evalua casos resolucion 1407 
	           // si el valor esta dentro del rango se coloca badera
	           // M1407FR = mensaje de conteo alerta resolucion 1407 fuera del rango 
	           // M1407DR = mensaje de conteo alerta resolucion 1407 dentro del rango
	           // M1407NA = mensaje de conteo alerta resolucion 1407 No aplica resolucio
	           //
	           // Ajuste significado alertas anteriores
	           // MAN Menasje de Alerta No
	           // MAS Menasje de Alerta Si
	           
	           //si no tiene algunos de los dos rangos no aplica el 1407
	           
	            if(!$med_valor_min_sin_tratamiento) {
	                $mensaje .='-M1407NA-MIN-'.$med_valor_min_sin_tratamiento;
	            }elseif(!$med_valor_max){
	                $mensaje .='-M1407NA-MAX-'.$med_valor_min_sin_tratamiento;
	            }else{
	                $med_valor_min = ($med_valor_min==0)? $med_valor_min_sin_tratamiento:$med_valor_min;
	                $mensaje .= ($med_valor_min <= $valor && $valor <= $med_valor_max  ) ? '-M1407DR-' :'-M1407FR-';             
	            }
	           
	        }else{
	                
	            $valor          = trim(strtoupper($valor));                
	            $med_valor_min  = strtoupper($med_valor_min);
	            $med_valor_max  = strtoupper($med_valor_max);
	              
	            $mensaje.= "<br>LimInf: ".$med_valor_min
	                        ."<br>LimSup: ".$med_valor_max
	                        ."<br>Valor: ".$valor;
	                        
	           // Ajuste el 11 de febrero de 2024
	           // Evalua casos resolucion 1407 
	           // si el valor esta dentro del rango se coloca badera
	           // M1407FR = mensaje de conteo alerta resolucion 1407 fuera del rango 
	           // M1407DR = mensaje de conteo alerta resolucion 1407 dentro del rango
	           // M1407NA = mensaje de conteo alerta resolucion 1407 No aplica resolucio
	           //
	           // Ajuste significado alertas anteriores
	           // MAN Menasje de Alerta No
	           // MAS Menasje de Alerta Si
	           
	           $mensaje    .= ($valor == $med_valor_min || $valor == $med_valor_max)?'-M1407NA-MAN-No genera Alerta 3':'-M1407NA-MAS-SI genera Alerta 3 ';
	               
	        }
	    }else{
	        $mensaje ='-NOAPLICACUMPLE-MAN-No genera Alerta 4 Por no tener limites';    
	    }
	    if(preg_match("/-MAS-/", $mensaje)){
	        if($quien_invoca==1){
	            envia_alerta($id_ensayo_vs_muestra);
	            // php_envia_mail($id_ensayo_vs_muestra);
	        }
	    }
	   // echo '<<<<<<<<>>>>>>>>><br>'.$mensaje.'<br>';
	  return $mensaje;
	}
	

	function formatea_valor_min_max($valor){
	    $valor          = str_replace('<', '',$valor);
	    $valor          = str_replace('>', '',$valor);
	    //$valor          = str_replace('.', '',$valor);
	    $valor          = str_replace('Máximo', '',  $valor);
	    $valor          = str_replace('Mínimo', '',  $valor);    
	    $valor          = str_replace('Máx', '',  $valor);
	    $valor          = str_replace('Máx', '',$valor);
	    $valor          = str_replace('Máx.', '',$valor);
	    $valor          = str_replace('Max', '',$valor);
	    $valor          = str_replace('(*)', '',$valor);
	    $valor          = str_replace('*', '',$valor);
	    $valor          = trim($valor);
	    return $valor;
	}
	function almacena_primer_campo($id_ensayo_vs_muestra, $valor){
		$db = \Config\Database::connect();
     	$query = "update ensayo_vs_muestra set
                    resultado_analisis = '$valor',
                        id_responsable= ".session('user')->id." 
                    where id_ensayo_vs_muestra = $id_ensayo_vs_muestra";
        $data = $db->query($query);

      
      	almacena_auditoria($id_ensayo_vs_muestra, $valor,'resultado_analisis');
	}
	function almacena_segundo_campo($id_ensayo_vs_muestra, $valor){
		$db = \Config\Database::connect();
     	$texto = "update ensayo_vs_muestra set
                    resultado_analisis2 = '$valor',
                        id_responsable= ".session('user')->id." 
                    where id_ensayo_vs_muestra = $id_ensayo_vs_muestra";
      	$query = $db->query($texto);     
      	almacena_auditoria($id_ensayo_vs_muestra, $valor,'resultado_analisis2');
	}
	function almacena_dilucion($id_ensayo_vs_muestra, $valor){
		$db = \Config\Database::connect();
     	$texto = "update ensayo_vs_muestra set
                    id_dilucion = '$valor',
                    id_responsable= ".session('user')->id." 
                    where id_ensayo_vs_muestra = $id_ensayo_vs_muestra";
      	$query = $db->query($texto);     
      	almacena_auditoria($id_ensayo_vs_muestra, $valor,'id_dilucion');
	}
	function almacena_campo_resultado($id_ensayo_vs_muestra, $valor){
		$db = \Config\Database::connect();
     	$texto = "update ensayo_vs_muestra set resultado_mensaje = '$valor', id_responsable= ".session('user')->id." where id_ensayo_vs_muestra = $id_ensayo_vs_muestra";
      	$query = $db->query($texto);     
      
      	almacena_auditoria($id_ensayo_vs_muestra, $valor,'resultado_mensaje');
      
      	$texto = "SELECT COUNT(*) as total  FROM ensayo_vs_muestra "
              . "where id_muestra=(select id_muestra from ensayo_vs_muestra where id_ensayo_vs_muestra=$id_ensayo_vs_muestra ) and ( resultado_mensaje is null or resultado_mensaje ='')";
      	$query = $db->query($texto)->getResult();;  
      
      	$recordSet= $query[0];
      	if($recordSet->total ==0){
            	$texto = "update certificacion set
                            	certificado_estado = 3
                            	where id_muestreo_detalle= (select id_muestra from ensayo_vs_muestra where id_ensayo_vs_muestra=$id_ensayo_vs_muestra ) ";
            	$query = $db->query($texto);
      	}
      	
      	$ens_vs_mue = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo_vs_muestra', $id_ensayo_vs_muestra);
		$datos = procesar_registro_fetch('ensayo_vs_muestra', 'id_muestra', $ens_vs_mue[0]->id_muestra);
		$conformidad = 'Cumple';
		foreach ($datos as $key => $dato) {
			$muestreo_detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $dato->id_muestra);
			$ensayo = procesar_registro_fetch('ensayo', 'id_ensayo', $dato->id_ensayo);
			$alerta = evalua_alerta($ensayo[0]->med_valor_min, $ensayo[0]->med_valor_max, $dato->resultado_mensaje, $muestreo_detalle[0]->id_tipo_analisis, $dato->id_ensayo_vs_muestra, 2);
			if(!empty($dato->resultado_mensaje)){
				if(preg_match("/-MAS-/", $alerta)){
					$conformidad = 'No cumple';
					break;
				}
			}
		}
		$texto = "update certificacion set
							conformidad = '$conformidad'
							where id_muestreo_detalle= (select id_muestra from ensayo_vs_muestra where id_ensayo_vs_muestra=$id_ensayo_vs_muestra ) ";
		$query = $db->query($texto);
	}
?>