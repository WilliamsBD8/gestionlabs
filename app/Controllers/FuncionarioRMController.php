<?php

namespace App\Controllers;
use App\Models\Cliente;
use App\Models\Certificacion;
use App\Models\Funcionario;
use App\Models\Muestreo;
use App\Models\MuestreoDetalle;
use App\Models\Analisis;
use App\Models\Producto;
use App\Models\Ensayo;
use App\Models\EnsayoMuestra;
use App\Models\Parametros;
use App\Models\FechaVidaUtil;
use Config\Services;

use CodeIgniter\API\ResponseTrait;

class FuncionarioRMController extends BaseController
{
    use ResponseTrait;
    // --------------------------------------- Resultados
    public function resultados(){
        $analisis = new Analisis();
        $analisis = $analisis->whereIn('id_muestra_tipo_analsis', [1, 2, 4])->get()->getResult(); 
        return view('funcionarios/resultados',[
            'analisis' => $analisis,
            'filtros' => '',
        ]);
    }

    public function ingreso_muestras(){
        $codigo_amc     = $this->request->getPost('frm_codigo_busca');
        $dia_listar     = $this->request->getPost('frm_dia_listar');
        $tipo_analisis  = $this->request->getPost('frm_tipo_analisis');
        $bandera        = false;
        $certificados   = new Certificacion();
        $analisis       = new Analisis();
        $analisis       = $analisis->get()->getResult(); 
        $fecha = date('Y-m-d H:i:s');
        $nuevafecha0 = strtotime ( '-6 day' , strtotime ( $fecha ) ) ;
        $nuevafecha0 = date ( 'Y-m-d H:i:s' , $nuevafecha0 );
        $nuevafecha1 = strtotime ( '-2 day' , strtotime ( $fecha ) ) ;
        $nuevafecha1 = date ( 'Y-m-d H:i:s' , $nuevafecha1 );
        $certificados->select('*')
            ->join('muestreo', 'certificacion.id_muestreo = muestreo.id_muestreo')
            ->join('muestreo_detalle', 'muestreo_detalle.id_muestra_detalle = certificacion.id_muestreo_detalle')
            ->join('producto', 'producto.id_producto = muestreo_detalle.id_producto');
        if(!empty($codigo_amc)){
            $certificados->where(['id_codigo_amc' => $codigo_amc]);
        }else{
            $certificados->where(['mue_estado' => '1']);
            if(!empty($dia_listar)){
                $nuevafecha0 = strtotime ( '-'.$dia_listar.' day' , strtotime ( $fecha ) ) ;
                $nuevafecha0 = date ( 'Y-m-d' , $nuevafecha0 );
                $nuevafecha1 =0;
                $certificados->where([
                    'mue_fecha_muestreo >=' => $nuevafecha0.' 00:00:00',
                    'mue_fecha_muestreo <=' => $nuevafecha0.' 23:59:59'
                ]);
            }else{
                $certificados->where([
                    'mue_fecha_muestreo >=' => $nuevafecha0,
                    'mue_fecha_muestreo <=' => $nuevafecha1
                ]);
            }
            if(strlen($tipo_analisis)==1){
                list($par1) = str_split($tipo_analisis);
                $tipo_analisis = array($par1);
            }elseif(strlen($tipo_analisis)==2){
                list($par1, $par2) = str_split($tipo_analisis);
                $tipo_analisis = array($par1, $par2);
            }elseif(strlen($tipo_analisis)==3){
                list($par1, $par2, $par3) = str_split( $tipo_analisis);
                $tipo_analisis = array($par1, $par2, $par3);
            }elseif(strlen($tipo_analisis)==4){
                list($par1, $par2, $par3, $par4) = str_split( $tipo_analisis);
                $tipo_analisis = array($par1, $par2, $par3, $par4);
            }else{
                list($par1, $par2, $par3, $par4, $par5) = str_split( $tipo_analisis);
                $tipo_analisis = array($par1, $par2, $par3, $par4, $par5);
            }
            $certificados->whereIn('id_tipo_analisis', $tipo_analisis);
        }
        $filtros = $certificados->orderBy('id_certificacion', 'desc')->get()->getResult();
        // return var_dump($filtros);
        $db = \Config\Database::connect();
        $aux_where = 'p.id_producto in (select d.id_producto from certificacion c, muestreo m, muestreo_detalle d where c.id_muestreo = m.id_muestreo and c.id_muestreo_detalle = d.id_muestra_detalle';
        if(!empty($codigo_amc)){
            $aux_where .=" and d.id_codigo_amc=$codigo_amc";
        }else{
            $aux_where .=" and m.mue_estado =1 ";
            if(!empty($dia_listar)){
                $nuevafecha0 = strtotime ( '-'.$dia_listar.' day' , strtotime ( $fecha ) ) ;
                $nuevafecha0 = date ( 'Y-m-d' , $nuevafecha0 );
                $nuevafecha1 =0;
                $aux_where .=" and m.mue_fecha_muestreo between '".$nuevafecha0." 00:00:00' and '".$nuevafecha0." 23:59:59' ";
            }else{
                $aux_where .=" and m.mue_fecha_muestreo between '".$nuevafecha0."' and '".$nuevafecha1."' ";
            }
            $aux_analisis = implode(',', $tipo_analisis);
            $aux_where .=" and  d.id_tipo_analisis in ($aux_analisis)"; 
        }
        $aux_where .=")"; //echo $aux_;
        $parametros = $db->table('producto p')->select('
            distinct (select par_nombre from parametro where id_parametro= e.id_parametro) parametro,
            e.id_parametro,
            (select par_descripcion from parametro where id_parametro= e.id_parametro  ) parametro_descripcion,
            (select concat(t.id_tecnica,"-",t.nor_nombre) nombre from parametro p inner join tecnica t on p.id_tecnica=t.id_tecnica where id_parametro=  e.id_parametro ) tecnica,
            (select t.id_tecnica from parametro p inner join tecnica t on p.id_tecnica=t.id_tecnica where id_parametro=  e.id_parametro ) id_tecnica')->join('ensayo e', 'p.id_producto=e.id_producto')->where($aux_where)->orderBy('id_parametro', 'ASC')->get()->getResult();
        foreach($filtros as $key => $muestra){
            $aux_parametro = [];
            $aux_ensayo_vs_muestra = [];
            $aux_ensayo = [];
            $fechasUtiles = procesar_registro_fetch('fecha_vida_util', 'id_detalle_muestreo', $muestra->id_muestreo_detalle);
            $muestra->fechasUtiles = $fechasUtiles;
            if(!empty($muestra->fechasUtiles)){
                foreach ($muestra->fechasUtiles as $key_fecha => $fecha) {
                    $aux_fechas = [];
                    foreach($parametros as $llave => $parametro){
                        $ensayo = procesar_registro_fetch('ensayo', 'id_producto', $muestra->id_producto, 'id_parametro', $parametro->id_parametro);
                        if(!empty($ensayo[0])){  
                            $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo[0]->id_ensayo, 'id_muestra', $muestra->id_muestra_detalle, 'id_fecha_vida_util', $fecha->id);
                            if(!empty($ensayo_vs_muestra[0])){
                                array_push($aux_fechas, $ensayo_vs_muestra[0]);
                                if($key_fecha == 0){
                                    array_push($aux_parametro, $parametro);
                                    array_push($aux_ensayo, $ensayo[0]);
                                }
                            }
                        }
                    }
                    array_push($aux_ensayo_vs_muestra, $aux_fechas);
                }
            }else{
                foreach($parametros as $llave => $parametro){
                    $ensayo = procesar_registro_fetch('ensayo', 'id_producto', $muestra->id_producto, 'id_parametro', $parametro->id_parametro);
                    if(!empty($ensayo[0])){
                        $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo[0]->id_ensayo, 'id_muestra', $muestra->id_muestra_detalle);
                        if(!empty($ensayo_vs_muestra[0])){
                            array_push($aux_parametro, $parametro);
                            array_push($aux_ensayo_vs_muestra, $ensayo_vs_muestra[0]);
                            array_push($aux_ensayo, $ensayo[0]);
                        }
                    }
                }
            }
            $muestra->parametros = $aux_parametro;
            $muestra->ensayos = $aux_ensayo;
            $muestra->ensayo_vs_muestras = $aux_ensayo_vs_muestra;
            // var_dump($muestra->ensayo_vs_muestras); die();
            // break;
        }
        
        if($codigo_amc == 5532){
            // var_dump($filtros); die;
        }
        
        $diluciones = procesar_registro_fetch('diluciones', 0, 0);
        // if($codigo_amc == 7131){
        //     var_dump($filtros); die;
        // }

        return view('funcionarios/resultados',[
            'analisis'      => $analisis,
            'muestras'      => $filtros,
            'diluciones'    => $diluciones
            // 'parametros'    => $parametros
        ]);
    }
    
    public function cambio_date(){
        $id_certificacion = $this->request->getPost('id_certificacion');
        $date = $this->request->getPost('date');
        $date = $date.' '.date('H:i:s');
        $db = \Config\Database::connect();
        $sql_guardar = "update certificacion set
                    cer_fecha_analisis='$date'
                    where id_certificacion='$id_certificacion'";
        if ($db->simpleQuery($sql_guardar))
            return json_encode(['status' => true]);
        else
            return json_encode(['status' => false]);
    }
    
   

    public function ingreso_muestras_resultado(){
        $campo_salida           =   $this->request->getPost('campo_respuesta');    
        $valor                  =   $this->request->getPost('valor');    
        $nombre_campo_frm       =   $this->request->getPost('frm_resultado');
        $nombre_campo_bd        =   $this->request->getPost('resultado_analisis');    
        $id_tecnica             =   $this->request->getPost('id_tecnica');    
        $id_ensayo_vs_muestra   =   $this->request->getPost('aux_id_ensayo_vs_muestra'); 
        $rol                    =   session('user')->usr_rol;
        $id_tipo_analisis       =   $this->request->getPost('id_tipo_analisis');
        $mohos_levaduras        =   $this->request->getPost('mohos_levaduras');
        $dilucion        =   $this->request->getPost('dilucion');
        
        $result['hide'] = false;
        $fila_ensayo_vs_muestra = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
        $fila_ensayo = procesar_registro_fetch("ensayo", "id_ensayo", $fila_ensayo_vs_muestra[0]->id_ensayo);
        $fila_ensayo = $fila_ensayo[0];
        $aux_guarda_resultado_mensaje = 'SI';
        $no = '<input type="hidden" class="required">';
        $mensaje        = '';
        $mensaje_campo  ='';
        $mensaje_campo2 ='';
        
        if($mohos_levaduras){
            
            if($nombre_campo_bd=='resultado_analisis'){// primer campo
                // ajsute ml para siembra directa
                if( $id_tecnica == 97 || $id_tecnica == 4 ){//  //97 Siembra directa- //4 Siembra directa
                        
                        //validamos si existe levaduras
                        $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        if(isset($fila_resultado_1[0]->resultado_analisis2)){
                        
                            if (is_numeric ($valor)) {
                                if ($valor > 300){
                                    $valor = ">300";
                                }
                            }   
                            $aux_m = $valor;
                            almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                            
                            // validacion de valor levaduras o segundo campo
                            $aux_l = $fila_resultado_1[0]->resultado_analisis2;
                            if (is_numeric ($aux_l)) {
                                if ($aux_l > 300){
                                    $aux_l = ">300";
                                }
                            }   
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) + '.$aux_l.' (L)' );
                            
                            $mensaje = "<b>".$aux_m." (M) + ".$aux_l." (L) </b> <br>"; 
                            $aux_guarda_resultado_mensaje='';//       -IC1S-
                            $mensaje .=$aux_guarda_resultado_mensaje;
                            
                        }else{
                            
                             if (is_numeric ($valor)) {
                                if ($valor > 300){
                                    $valor = ">300";
                                }
                            }   
                            $aux_m = $valor;
                            almacena_primer_campo($id_ensayo_vs_muestra, $aux_m.' (M) ');   
                            
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) ');    
                            $mensaje = "<b>".$aux_m." (M) </b>".$aux_g; 
                            
                        }
                    
                }else{
                
                    $aux_recuentos = calcula_recuentos (1, $valor, $dilucion, $id_ensayo_vs_muestra, $id_tecnica);
                    $aux_m = $aux_recuentos[1];
                    $aux_r = $aux_recuentos[0];
                    $aux_g = $aux_recuentos[2];
                    
                    $aux_guarda_resultado_mensaje .= $aux_r;
                    
                    if($aux_g == 'SI'){// si pasa los filtros 
                        //validamos si existe levaduras
                        $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        if(isset($fila_resultado_1[0]->resultado_analisis2)){
                            
                            $aux_recuentos_l = calcula_recuentos (2, $fila_resultado_1[0]->resultado_analisis2, $dilucion, $id_ensayo_vs_muestra, $id_tecnica);
                            $aux_l = $aux_recuentos_l[1];
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) + '.$aux_l.' (L)' );
                            
                            $mensaje = "<b>".$aux_m." (M) + ".$aux_l." (L) </b> <br>"; 
                        
                            
                        }else{
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) ');    
                            $mensaje = "<b>".$aux_m." (M) </b>".$aux_g; 
                            
                        }
                    }
                }
                
            }else{ // segundo campo
                 // ajsute ml para siembra directa
                if( $id_tecnica == 97 || $id_tecnica == 4 ){//  //97 Siembra directa- //4 Siembra directa
                        
                        //validamos si existe mohos
                        $fila_resultado_m = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        if(isset($fila_resultado_m[0]->resultado_analisis)){
                        
                            if (is_numeric ($valor)) {
                                if ($valor > 300){
                                    $valor = ">300";
                                }
                            }   
                            $aux_l = $valor;
                            almacena_segundo_campo($id_ensayo_vs_muestra, $valor);
                            
                            // validacion de valor mohos  o primer campo
                            $aux_m = $fila_resultado_1[0]->resultado_analisis;
                            if (is_numeric ($aux_m)) {
                                if ($aux_m > 300){
                                    $aux_m = ">300";
                                }
                            }   
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) + '.$aux_l.' (L)' );
                            
                            $mensaje = "<b>".$aux_m." (M) + ".$aux_l." (L) </b> <br>"; 
                            $aux_guarda_resultado_mensaje='-IC1S-';//       
                            $mensaje .=$aux_guarda_resultado_mensaje;
                            
                        }else{
                            
                             if (is_numeric ($valor)) {
                                if ($valor > 300){
                                    $valor = ">300";
                                }
                            }   
                            $aux_l = $valor;
                            almacena_segundo_campo($id_ensayo_vs_muestra, $aux_l.' (L) ');   
                            
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (L) ');    
                            $mensaje = "<b>".$aux_l." (L) </b>".$aux_g; 
                            
                        }
                    
                }else{
                    
                    $aux_recuentos = calcula_recuentos (2, $valor, $dilucion, $id_ensayo_vs_muestra, $id_tecnica);
                    $aux_l = $aux_recuentos[1];
                    $aux_r = $aux_recuentos[0];
                    $aux_g = $aux_recuentos[2];
                    
                    $mensaje = "<b>".$aux_l." (L) </b>"; 
                    $aux_guarda_resultado_mensaje .= $aux_r;
                    
                    if($aux_g == 'SI'){
                        
                        $fila_resultado_m = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        if(isset($fila_resultado_m[0]->resultado_analisis)){
                            
                            $aux_recuentos_m = calcula_recuentos (1, $fila_resultado_m[0]->resultado_analisis, $dilucion, $id_ensayo_vs_muestra, $id_tecnica);
                            $aux_m = $aux_recuentos_m[1];
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' (M) + '.$aux_l.' (L)' );
                            
                            $mensaje = "<b>".$aux_m." (M) + ".$aux_l." (L) </b>"; 
                        
                            
                        }else{
                            
                            almacena_campo_resultado($id_ensayo_vs_muestra, $aux_l.' (L) ');    
                            $mensaje = "<b>".$aux_l." (L) </b>"; 
                            
                        }
                        
                        //almacena_campo_resultado($id_ensayo_vs_muestra, $aux_m.' L');    
                    }
            }
                
            }//fin segundo campo
            
            
            
        }
        elseif($mohos_levaduras == 'para eliminar'){
            $aux_0 = strripos($valor, ';'); //Encuentra la posición de la última aparición del separador ;
            $aux_m = calcula_mh($valor, $dilucion);
            
            
            if($nombre_campo_bd=='resultado_analisis'){
                if($aux_0 > 0){
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);                    
                    $mensaje = "<b>".$aux_m." M ->3</b>";                     
                    $aux_guarda_resultado_mensaje='-NMC2-';//                    -IC1S
                    //almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    
                    if($fila_resultado_1[0]->resultado_analisis2){
                       
                        $aux_l = calcula_mh($fila_resultado_1[0]->resultado_analisis2);
                        // $aux_l = 10;
                        $aux_resultado = $aux_m+$aux_l;
                        
                        $aux_m = formatea_mh_parcial($aux_m,"mohos");
                        $aux_l = formatea_mh_parcial($aux_l,"levaduras");
                              
                        //$aux_resultado = $aux_m."M + ".$aux_l."L Total ".$aux_resultado;
                        $aux_resultado = $aux_m." ".$aux_l." Total ".$aux_resultado;
                        // return json_encode($aux_resultado);
                        almacena_campo_resultado($id_ensayo_vs_muestra, $aux_resultado);  
                        
                        $mensaje = "<b>".$aux_resultado." -> 1</b>";       
                    }
                    //no tiene else
                }else{
                    $aux_guarda_resultado_mensaje='-NMC2-';
                    $mensaje ="Debe ingresar 2 valores separados por <b>;</b>".$no;
                }
            }else{
                if($aux_0 > 0){
                    $aux_l = $aux_m; //para manejarla como levadura
                    almacena_segundo_campo($id_ensayo_vs_muestra, $valor);    
                    $mensaje = "<b>".$aux_l." L </b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//    
                   
                    $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    if($fila_resultado_1[0]->resultado_analisis){
                       
                        $aux_m = calcula_mh($fila_resultado_1[0]->resultado_analisis);
                        
                        $aux_resultado = $aux_m+$aux_l;
                        $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $aux_resultado, $id_tipo_analisis, $id_ensayo_vs_muestra);
                        
                        $aux_m = formatea_mh_parcial($aux_m,"mohos");
                        $aux_l = formatea_mh_parcial($aux_l,"levaduras");
                        
                        //$aux_resultado = $aux_l."M + ".$aux_m."L Total ".$aux_resultado;
                        $aux_resultado = $aux_m." ".$aux_l." Total ".$aux_resultado;
                        almacena_campo_resultado($id_ensayo_vs_muestra, $aux_resultado);  
                        
                        $mensaje = "<b>".$aux_resultado." -> 2</b>";       
                    }
                    
                }else{
                    $aux_guarda_resultado_mensaje='-NMC2-';
                    $mensaje ="Debe ingresar 2 valores separados por <b>;</b>".$no;
                }
                
            }
        }
        elseif ($id_tecnica == 80){//Recuentos Asbioquim
            // reglas
            // 1 si no hay dilucion se tomara el primer campo y sera un calculo directo. 
            // 2 si en el primer campo es mayor a 300 se multiplicara dos veces por la dilucion
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
            //   3 dilucion = <1000
            //   4 dilucion = <10000
            
            //validacion de diluciones que solo aplican a MH
            if ($dilucion > 5 ){
                $mensaje ="Dilución no permitida".$no;
                $aux_guarda_resultado_mensaje='-NMC2-';
                
            }else{
        
                if($nombre_campo_bd=='resultado_analisis'){//calculos para el primer campo
                    if (is_numeric ($valor)) {
                        almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                        almacena_dilucion($id_ensayo_vs_muestra, $dilucion);
                        // 1
                        if ($dilucion == 5){ // selecciono sin dilucion
                            
                            //$valor2 = redondeo_asbioquim($valor, $valor, $dilucion);
                            if($valor == 0){
                                $valor = '<1';
                            }elseif($valor >= 300){
                                $valor = '>300';
                            }
                            
                            $valor2 = $valor;
                            almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $aux_guarda_resultado_mensaje.='-NMC2-';
                            $mensaje=$valor2.' ->1';
                        
                            
                            
                        }elseif($valor >= 300){
                        
                            $valor2 = redondeo_asbioquim($valor, $valor, $dilucion);
                            almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $aux_guarda_resultado_mensaje.='-NMC2-';
                            $mensaje=$valor2.' ->2';
                        }else{
                            // comprobamos si existe un segundo dato
                            
                            $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                            if($fila_resultado_1[0]->resultado_analisis2){//existe un segundo resultado
                                
                                $valor2     =  round(($fila_resultado_1[0]->resultado_analisis2+$valor) / ( 1.1*valores_dilucion($dilucion) ) ); //0.1 es la dilucion
                                $aa = $valor2;  
                                $valor2 = redondeo_asbioquim($valor2, $valor, $dilucion);  
                                
                                almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                                $aux_guarda_resultado_mensaje.='-NMC2-';
                                $mensaje=$valor2.' <br> ->3... Sin redondeo '.$aa ;//.' id:'.$id_ensayo_vs_muestra.' dilucion '. $dilucion;
                            
                                
                            }else{// no existe el segundo campo
                            
                                $valor2     = round($valor/ valores_dilucion($dilucion) ); //0.1 es la dilucion
                                $aa = $valor2;
                                $valor2 = redondeo_asbioquim($valor2, $valor, $dilucion);
                                
                                almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                                $aux_guarda_resultado_mensaje.='-NMC2-';
                                $mensaje=$valor2.' <br>->4 Sin redondeo '.$aa ;//.' id:'.$id_ensayo_vs_muestra.' dilucion '. $dilucion;;
                            }
                            
                        }
                    }else{
                            
                            $mensaje ="Valor no numerico 1".$no.' ->5'.' -->'.$mohos_levaduras;
                            $aux_guarda_resultado_mensaje='-NMC2-';
                    }
                }
                else{//SEGUNDO CAMPO
                    if (is_numeric ($valor)) {
                        almacena_segundo_campo($id_ensayo_vs_muestra, $valor);
                        $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                        
                        $valor2     = round( ($fila_resultado_1[0]->resultado_analisis+$valor)/ ( 1.1*valores_dilucion($dilucion) ) ); //0.1 es la dilucion
                        $aa = $valor2;          
                        $valor2 = redondeo_asbioquim($valor2, $valor, $dilucion);
                        
                        almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);                
                        
                        $mensaje=$valor2.' <br>->5 Sin redondeo '.$aa ;//.' id:'.$id_ensayo_vs_muestra.' '.$fila_resultado_1[0]->resultado_analisis .' '. $valor .' dilucion '. $dilucion;
                        
                        $aux_guarda_resultado_mensaje='-IC2S-'.$valor2;
                        $aux_guarda_resultado_mensaje.='-NMC2-';
                        $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor2, $id_tipo_analisis, $id_ensayo_vs_muestra);
                    
                        
                    }else{
                            
                            $mensaje ="Valor no numerico 2".$no;
                            $aux_guarda_resultado_mensaje='-NMC2-';
                    }
                    
                }
            }//fin validacion de diluciones permitidas para recuento
            
        }//fin recuentos asbioquim
    
        elseif ($id_tecnica == 28){//Escobillon Asbioquim
            // regla: maneja un dato directo de tipo numerico
            // excepcion: cuando ingresa 0 se presenta <1
            // Excepción: cuando ingresa un numero mayor a 300 se expresara como >300
            
            if($nombre_campo_bd=='resultado_analisis'){
                if (is_numeric ($valor)) {
                    
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);   
                    // evalua si es cero
                    if ($valor == 0){
                        $valor = '< 1';
                    }elseif($valor > 300){
                        $valor = '> 300';
                    }
                    $mensaje = "<b>".$valor." </b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{  
                    $mensaje ="Debe ser númerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
                     
            }
        }
        
         elseif ($id_tecnica == 96){//Escobillon- Asbioquim
            // regla: maneja un dato directo de tipo numerico
            // excepcion: cuando ingresa 0 se presenta <10
            // Excepción: cuando ingresa un numero mayor a 300 se expresara como >300
            
            if($nombre_campo_bd=='resultado_analisis'){
                if (is_numeric ($valor)) {
                    
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);   
                    // evalua si es cero
                    if ($valor == 0){
                        $valor = '< 10';
                    }elseif($valor > 300){
                        $valor = '> 300';
                    }
                    $mensaje = "<b>".$valor." </b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{  
                    $mensaje ="Debe ser númerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
                     
            }
        }
        
        elseif ($id_tecnica == 6){// Filtracion por membrana
            // regla: Dato Directo de tipo numerico
            // excepción: cuando se digita un numero mayor a 200 se reporta >2.000
            
            if($nombre_campo_bd=='resultado_analisis'){
                if (is_numeric ($valor)) {
                    
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);   
                    // evalua si es msyor a 200 se reporta como >2000
                    if ($valor > 200){
                        $valor = '> 2000';
                    }
                    $mensaje = "<b>".$valor."</b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{  
                    $mensaje ="Debe ser númerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
                     
            }
            
        
        }
        elseif($id_tecnica == 81   ){//  Detección de microorganismos específicos ausencia presencia          
            if($nombre_campo_bd=='resultado_analisis'){
                if (!is_numeric ($valor)) {
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);                    
                    $mensaje = "<b>".$valor."</b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                    $mensaje .=$aux_guarda_resultado_mensaje;
                }else{  
                    $mensaje ="Valor numérico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
                     
            }
        }
        elseif( $id_tecnica == 97 || $id_tecnica == 4 ){//  //97 Siembra directa- //4 Siembra directa
            //  26 de febrero de 2022
            // En la técnica de siembra directa está solo para resultado de ausencia/ presencia, 
            // necesitaría también numérico directo  si da 0 se reporta 0 no <1, pero si es mayor a 300  >300
            
            if($nombre_campo_bd=='resultado_analisis'){
                if (is_numeric ($valor)) {
                    if ($valor > 300){
                        $valor = ">300";
                    }
                }/*
                else{  
                    $mensaje ="Valor numérico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }*/
                almacena_primer_campo($id_ensayo_vs_muestra, $valor);                    
                    $mensaje = "<b>".$valor."</b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                    $mensaje .=$aux_guarda_resultado_mensaje;
                     
            }
        }
            
        elseif ($id_tecnica == 1){
            if (is_numeric ($valor)) {
                    if ($valor <= 14){
                        $mensaje = "<b>".$valor." d&iacute;as</b>";
                        //$respuesta->assign("campo_repuesta_".$id_ensayo_vs_muestra, "innerHTML", $mensaje);
                        $aux_guarda_resultado_mensaje='-IC1S-NMC2-';
                        almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                        almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                        $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                    }else{                        
                        $mensaje ="Valor no permitido xc".$no;
                        //$respuesta->assign("campo_repuesta_".$id_ensayo_vs_muestra, "innerHTML", $mensaje);
                        $aux_guarda_resultado_mensaje='-NMC2-';
                    }
            }else{
                    
                    $mensaje ="Valor no numerico".$no;
                    //$respuesta->assign("campo_repuesta_".$id_ensayo_vs_muestra, "innerHTML", $mensaje);
                    $aux_guarda_resultado_mensaje='-NMC2-';
            }           
        }elseif ($id_tecnica == 2 || $id_tecnica == 4 || $id_tecnica == 10){
            //se ajusta para que si el susario ingresa <10 <100 <1000 lo toma como cero para promediar
            $valor_signo    =   $valor;
            $valor          =   (preg_match("/</", $valor))?0:$valor;
        
            if($nombre_campo_bd=='resultado_analisis'){
                if(is_numeric ($valor)) {               
                    $aux_guarda_resultado_mensaje='-IC1S-';
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor_signo);
                
                    if($rol==1 || $rol==2 || $rol==3){
                        $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    
                        if($fila_resultado_1[0]->resultado_analisis2){
                            if(preg_match("/</", $fila_resultado_1[0]->resultado_analisis2) && preg_match("/</", $valor_signo)){
                                $valor2=$valor_signo;
                            }elseif(preg_match("/</", $valor_signo)){
                                $valor2     = round(($fila_resultado_1[0]->resultado_analisis2+0)/2); 
                            }elseif(preg_match("/</", $fila_resultado_1[0]->resultado_analisis2)){
                                $valor2     = round((0+$valor)/2); 
                            }else{
                                $valor2     = round(($fila_resultado_1[0]->resultado_analisis2+$valor)/2); 
                            }                        
                            almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $aux_guarda_resultado_mensaje.='-NMC2-';
                            $mensaje=$valor2;
                        }
                    }
                }else{
                    $mensaje ="Valor no numerico".$no;                
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
            }else{
                if(is_numeric ($valor)) {  
                    almacena_segundo_campo($id_ensayo_vs_muestra, $valor_signo);
                    $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    if(preg_match("/</", $fila_resultado_1[0]->resultado_analisis) && preg_match("/</", $valor_signo)){
                        $valor2=$valor_signo;
                    }elseif(preg_match("/</", $valor_signo)){
                        $valor2     = round(($fila_resultado_1[0]->resultado_analisis+0)/2); 
                    }elseif(preg_match("/</", $fila_resultado_1[0]->resultado_analisis)){
                        $valor2     = round((0+$valor)/2); 
                    }else{
                        $valor2     = round(($fila_resultado_1[0]->resultado_analisis+$valor)/2); 
                    }                        
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);                
                    $mensaje=$valor2;
                    $aux_guarda_resultado_mensaje='-IC2S-'.$valor2;
                    $aux_guarda_resultado_mensaje.='-NMC2-';
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor2, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{
                    $mensaje ="Valor no numerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
            }
            $valor=$valor_signo;
        }elseif($id_tecnica == 5){// nmp     
            if($nombre_campo_bd=='resultado_analisis'){
                $cantidad = strlen($valor);
                if(is_numeric ($valor) && $cantidad == 3) {
                    $busca = procesar_registro_fetch("tabla_nmp", "combinacion", $valor);
                    if (isset($busca[0]->id)){
                        almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                        $valor = $busca[0]->resultado;
                        $mensaje = "<b>".$valor."</b>";                    
                        $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                        almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                        $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                    }else{
                        $mensaje ="Rango no permitido.".$no;                   
                        $aux_guarda_resultado_mensaje='-IC1N-NMC2-';//-IC1S-NMC2-GCMS-
                    }
                }else{
                    $mensaje ="Valor no permitido, solo 3 n&uacute;meros. EJ 001 033".$no;                
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
            }
        }elseif($id_tecnica == 6 && $id_tipo_analisis==3){// sal tecnica 6 Filtraci�n por Membrana  y tipo de analisis FA     
            if($nombre_campo_bd=='resultado_analisis'){
                almacena_primer_campo($id_ensayo_vs_muestra, $valor);                    
                $mensaje = "<b>".$valor."</b>";                    
                $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
            }
        }elseif($id_tecnica == 7){//  sal tecnica 7 ausencia presencia         
            if($nombre_campo_bd=='resultado_analisis'){
                if (!is_numeric ($valor)) {
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);                    
                    $mensaje = "<b>".$valor."</b>";                    
                    $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{  
                    $mensaje ="Valor numerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
                     
            }
        
            
        }
        elseif($id_tecnica == 29){//   29 Sedimentacion de  Ambientes, ajuste realizado el 25 de mayo de 2020. para poder ingresar texto 20(M)5(L)   
            // Sedimentacion	
            // Regla: maneja un dato directo de tipo numerico, incluyendo el cero.	
            // Excepción: cuando ingresa un numero mayor a 300 se expresara como >300	

            if($nombre_campo_bd=='resultado_analisis'){
                almacena_primer_campo($id_ensayo_vs_muestra, $valor);     
                if($valor > 300){
                        $valor = '> 300';
                }
                $mensaje = "<b>".$valor."</b>";                    
                $aux_guarda_resultado_mensaje='-IC1S-NMC2-';//                    
                almacena_campo_resultado($id_ensayo_vs_muestra, $valor);        
            }
        
        }else{//demas tecnica
            if($nombre_campo_bd=='resultado_analisis'){
                if(is_numeric ($valor)) {               
                    $aux_guarda_resultado_mensaje='-IC1S-';
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                    if($rol==1 || $rol==2 || $rol==3){
                        $fila_resultado_1   = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    
                        if($fila_resultado_1[0]->resultado_analisis2){
                            $valor2 = round(($fila_resultado_1[0]->resultado_analisis2+$valor)/2); 
                            almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                            $aux_guarda_resultado_mensaje.='-NMC2-';
                            $mensaje=$valor2;
                        }
                    }
                }else{
                    $aux_guarda_resultado_mensaje='-IC1S-';
                    almacena_primer_campo($id_ensayo_vs_muestra, $valor);
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor);
                    $aux_guarda_resultado_mensaje.='-NMC2-';
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }
            }else{
                if(is_numeric ($valor)) {  
                    almacena_segundo_campo($id_ensayo_vs_muestra, $valor);
                    $fila_resultado_1 = procesar_registro_fetch("ensayo_vs_muestra", "id_ensayo_vs_muestra", $id_ensayo_vs_muestra);
                    $aux_valor = is_numeric($fila_resultado_1[0]->resultado_analisis) ? $fila_resultado_1[0]->resultado_analisis:0;
                    $valor2 = round(($aux_valor+$valor)/2); 
                            // return json_encode($valor2);
                    almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                    $aux_guarda_resultado_mensaje='-IC2S-'.$valor2;//
                 
                    if($rol==1 || $rol==2 || $rol==3){
                        $valor2     = round(($aux_valor+$valor)/2); 
                        almacena_campo_resultado($id_ensayo_vs_muestra, $valor2);
                        $aux_guarda_resultado_mensaje.='-NMC2-';
                        $mensaje=$valor2;
                    }
                    $aux_guarda_resultado_mensaje.=evalua_alerta($fila_ensayo->med_valor_min ,$fila_ensayo->med_valor_max, $valor2, $id_tipo_analisis, $id_ensayo_vs_muestra);
                }else{
                    $mensaje ="Valor no numerico".$no;               
                    $aux_guarda_resultado_mensaje='-NMC2-';
                }
            }
            $aux_guarda_resultado_mensaje.="Tecnica_".$id_tecnica;
        }// fin demas tecnica
        $aux_guarda_resultado_mensaje.="Tecnica_".$id_tecnica;
        $aux_guarda_resultado_mensaje.="Campo".$nombre_campo_bd;
        $aux_guarda_resultado_mensaje.="Valor".$valor;
        $result = [];
        $result['campo_frm'] = $nombre_campo_frm;
        if(preg_match("/-IC1S-/", $aux_guarda_resultado_mensaje)){
            if(preg_match("/-MAS-/", $aux_guarda_resultado_mensaje)){
                $result['style'] = 'invalid';
            }else{
                $result['style'] = 'valid';
            }
            $result['hide'] = true;
        }
        if(preg_match("/-IC2S-/", $aux_guarda_resultado_mensaje)){
            if(preg_match("/-MAS-/", $aux_guarda_resultado_mensaje)){
                $style = 'class="invalid"';
            }else{
                $style = 'class="valid"';
            }
            $mensaje_campo2 .= '<input type="text"  name="frm_resultado2'.$id_ensayo_vs_muestra.'" id="frm_resultado2'.$id_ensayo_vs_muestra.'"   value="'.$valor.'" '.$style.' disabled>';
            $result['campo_respuesta'] = "campo_repuesta2_".$id_ensayo_vs_muestra;
            $result['hide'] = true; 
        }elseif(!preg_match("/-NMC2-/", $aux_guarda_resultado_mensaje)){
            $mensaje_campo2 .= '<input type="text"  name="frm_resultado2'.$id_ensayo_vs_muestra.'" id="frm_resultado2'.$id_ensayo_vs_muestra.'"  onblur="js_cambiar_campos(\'campo_repuesta_'.$id_ensayo_vs_muestra.'\',this.value, \'frm_resultado2'.$id_ensayo_vs_muestra.'\', \'resultado_analisis2\',  \''.$id_ensayo_vs_muestra.'\', \''.$id_tecnica.'\')" value="">';
        }
        // $procedencia = ['name' => 'frm_id_procedencia', 'value' => 2];
        if($mensaje_campo2){
             $result["campo_respuesta"] = "campo_respuesta2_".$id_ensayo_vs_muestra;
             $result["campo_respuesta2_".$id_ensayo_vs_muestra] = $mensaje_campo2;
        }
            // return json_encode($aux_guarda_resultado_mensaje); 
        
        $result["campo_mensajes"] = "campo_mensajes_".$id_ensayo_vs_muestra;//.$aux_guarda_resultado_mensaje
        $result["campo_mensajes_".$id_ensayo_vs_muestra] = $mensaje;
        
        $result["prubea"] = $aux_guarda_resultado_mensaje;
        // return json_encode($valor);
        return json_encode($result);
        // return json_encode($id_tecnica);
    }
    public function analisis(){
        $consulta = $this->request->getPost('consulta');
        switch ($consulta) {
            case true:
                $date = $this->request->getPost('date_download');
                $tipo_analisis = $this->request->getPost('tipo_analisis');
                $type = $this->request->getPost('type');
                $certificados   = new Certificacion();
                $certificados->select('*')
                    ->join('muestreo', 'certificacion.id_muestreo = muestreo.id_muestreo')
                    ->join('muestreo_detalle', 'muestreo_detalle.id_muestra_detalle = certificacion.id_muestreo_detalle')
                    ->like('mue_fecha_muestreo', $date);

                if($tipo_analisis > 0)$certificados->where('id_tipo_analisis', $tipo_analisis);
                else{
                    if($type == 0) $certificados->whereNotIn('id_tipo_analisis', [3, 5]);
                    else $certificados->whereNotIn('id_tipo_analisis', [1, 2, 4]);
                }

                $muestras = $certificados->get()->getResult();
                if(empty($muestras)){
                    $fechasUtiles = procesar_registro_fetch('fecha_vida_util', 'fecha', $date);
                    if(empty($fechasUtiles))
                        return json_encode(false);
                    else
                        return json_encode(true);
                }else return json_encode(true);
                break;
            
            default:
                $date = $this->request->getPost('date_download');
                $tipo_analisis = $this->request->getPost('tipo_analisis');
                $type = $this->request->getPost('type');
                $db = \Config\Database::connect();
                $certificados   = new Certificacion();
                $certificados->select([ 
                    'muestra_tipo_analisis.mue_sigla',
                    'muestra_tipo_analisis.id_muestra_tipo_analsis',
                    'muestreo.mue_fecha_muestreo',
                    'certificacion.certificado_nro',
                    'certificacion.id_muestreo_detalle',
                    'producto.pro_nombre',
                    'producto.id_producto',
                    'muestreo_detalle.mue_identificacion',
                    'muestreo_detalle.id_muestra_detalle',
                    'muestreo_detalle.mue_dilucion',
                    // 'fecha_vida_util.fecha'
                ])
                    ->distinct('certificacion.certificado_nro')
                    ->join('muestreo', 'certificacion.id_muestreo = muestreo.id_muestreo')
                    ->join('usuario', 'muestreo.id_cliente = usuario.id')
                    ->join('muestreo_detalle', 'muestreo_detalle.id_muestra_detalle = certificacion.id_muestreo_detalle')
                    ->join('fecha_vida_util', 'fecha_vida_util.id_detalle_muestreo = muestreo_detalle.id_muestra_detalle', 'left')
                    ->join('muestra_tipo_analisis', 'muestreo_detalle.id_tipo_analisis = muestra_tipo_analisis.id_muestra_tipo_analsis')
                    ->join('producto', 'producto.id_producto = muestreo_detalle.id_producto', 'left')
                    ->like('mue_fecha_muestreo', $date)
                    ->orWhere([
                        'fecha_vida_util.fecha' => $date
                    ]);
                if($tipo_analisis > 0) $certificados->having('id_muestra_tipo_analsis', $tipo_analisis);
                else {
                    if($type == 0)$certificados->havingIn('id_muestra_tipo_analsis', [1, 2, 4]); 
                    else $certificados->havingIn('id_muestra_tipo_analsis', [3, 5, 6]);
                }
                // $muestras = $certificados->get()->getResult();
                $muestras = $certificados->orderBy('certificado_nro', 'ASC')->get()->getResult();
                $aux_where = "p.id_producto in (select d.id_producto from certificacion c, muestreo m,
                    muestreo_detalle d where c.id_muestreo = m.id_muestreo and c.id_muestreo_detalle = d.id_muestra_detalle)";
                $parametros = $db->table('producto p')->select('
                    distinct (select par_nombre from parametro where id_parametro= e.id_parametro) parametro,
                    e.id_parametro,
                    (select par_descripcion from parametro where id_parametro= e.id_parametro  ) parametro_descripcion,
                    (select concat(t.id_tecnica,"-",t.nor_nombre) nombre from parametro p inner join tecnica t on p.id_tecnica=t.id_tecnica where id_parametro=  e.id_parametro ) tecnica,
                    (select t.id_tecnica from parametro p inner join tecnica t on p.id_tecnica=t.id_tecnica where id_parametro=  e.id_parametro ) id_tecnica')->join('ensayo e', 'p.id_producto=e.id_producto')
                    ->where($aux_where)
                    // ->whereNotIn('id_muestra_tipo_analsis', [3, 5])
                    ->orderBy('id_parametro', 'ASC')->get()->getResult();
                foreach($muestras as $key => $muestra){
                    $fechasUtiles = procesar_registro_fetch('fecha_vida_util', 'id_detalle_muestreo', $muestra->id_muestreo_detalle);
                    $muestra->fechasUtiles = $fechasUtiles;
                    $aux_parametro = [];
                    foreach($parametros as $llave => $parametro){
                        $ensayo = procesar_registro_fetch('ensayo', 'id_producto', $muestra->id_producto, 'id_parametro', $parametro->id_parametro);
                        if(!empty($ensayo[0])){
                            $ensayo_vs_muestra = procesar_registro_fetch('ensayo_vs_muestra', 'id_ensayo', $ensayo[0]->id_ensayo, 'id_muestra', $muestra->id_muestra_detalle);
                            if(!empty($ensayo_vs_muestra[0])){
                                array_push($aux_parametro, $parametro);
                            }
                        }
                    }
                    $muestra->parametros = $aux_parametro;
                }
                $count = 0;
                $validador = 0;
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 5,
                    "margin_right" => 5,
                    "margin_top" => 30,
                    "margin_bottom" => 20,
                    "margin_header" => 5.5,
                    "margin_bottom_right" => 0,
                    "margin_bottom_left" => 0,
                ]);
                $mpdf->SetFooter('{PAGENO}');
                $css  = file_get_contents('assets/css/analisis.css');
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                foreach ($muestras as $key => $muestra) {
                    // $count += count($muestra->parametros);
                    $aux_codigo =   construye_codigo_amc($muestra->id_muestreo_detalle);
                    // // return var_dump($muestra);
                    // if($count > 50){
                    //     $mpdf->AddPage();
                    //     $count = count($muestra->parametros);
                    // }
                    $mpdf->SetHTMLHeader('
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 30%">
                                        <img src="assets/img/logo_1.jpeg" height="60">
                                    </th>
                                    <th style="width: 40%">
                                        <div class="asb-header">
                                            <strong>Hoja de trabajo</strong>
                                        </div>
                                    </th>
                                    <th style="width: 30%">
                                        <div class="asb-header">
                                            <span style="font-size: 15px">
                                                Fecha de vigencia: 2022-08-24 <br>
                                                Código: PRO-F-012 <br>
                                                Versión: 02 <br>
                                            </span>
                                        </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                        ');
                        $html = view('views_mpdf/analisis', [
                            'muestra' => $muestra,
                            'date' => $date,
                            'codigo' => $aux_codigo,
                        ]);
                        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
                }
                $name = "hoja_trabajo-$date.pdf";
                $this->response->setHeader('Content-Type', 'application/pdf');
                $mpdf->Output($name,'I');
                break;
        }
    }
    
    public function confirmacion(){
        $data = $this->request->getJson();
        $em_model = new EnsayoMuestra();
        $ensayo_vs_muestra = $em_model->asObject()->find($data->id);
        if(!empty($ensayo_vs_muestra->confirmacion_a)){
            $con_a = explode(':', $ensayo_vs_muestra->confirmacion_a);
            $con_b = explode(':', $ensayo_vs_muestra->confirmacion_b);
            $con_c = explode(':', $ensayo_vs_muestra->confirmacion_c);
            if($data->resultado == 1){
                $data_save = [
                    'confirmacion_a' => "{$data->confirmacion_a}:{$con_a[1]}",
                    'confirmacion_b' => "{$data->confirmacion_b}:{$con_b[1]}",
                    'confirmacion_c' => "{$data->confirmacion_c}:{$con_c[1]}",
                ];
            }else{
                $data_save = [
                    'confirmacion_a' => "{$con_a[0]}:{$data->confirmacion_a}",
                    'confirmacion_b' => "{$con_b[0]}:{$data->confirmacion_b}",
                    'confirmacion_c' => "{$con_c[0]}:{$data->confirmacion_c}",
                ];
            }
        }else{
            if($data->resultado == 1){
                $data_save = [
                    'confirmacion_a' => "{$data->confirmacion_a}:",
                    'confirmacion_b' => "{$data->confirmacion_b}:",
                    'confirmacion_c' => "{$data->confirmacion_c}:",
                ];
            }else{
                $data_save = [
                    'confirmacion_a' => ":{$data->confirmacion_a}",
                    'confirmacion_b' => ":{$data->confirmacion_b}",
                    'confirmacion_c' => ":{$data->confirmacion_c}",
                ];
            }
        }
        $em_model = new EnsayoMuestra();
        $em_model->set($data_save)->where(['id_ensayo_vs_muestra' => $data->id])->update();
        return $this->respond([$data]);
    }

    public function date_fecha(){
        $data = $this->request->getJson();
        $fvu_model = new FechaVidaUtil();
        $fvu_model->save([
            'id'    => $data->id,
            'fecha' => $data->fecha
        ]);
        return $this->respond($data);
    }
}