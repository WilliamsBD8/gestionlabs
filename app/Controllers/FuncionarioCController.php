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
use Config\Services;



class FuncionarioCController extends BaseController
{
    public function index(){
        $funcion = $this->request->getPost('funcion');
        switch ($funcion){
            case 'certificado_facturacion':
                $certificado_nro = $this->request->getPost('certificado_nro');
                $response = certificado_facturacion($certificado_nro);
                break;
            case 'certificado_autorizacion':
                $certificado_nro = $this->request->getPost('certificado_nro');
                $response = certificado_autorizacion($certificado_nro);
                break;
            case 'lista_resultados':
                $certificado_nro = $this->request->getPost('certificado_nro');
                $que_mostrar = $this->request->getPost('que_mostrar');
                $response = lista_resultados($certificado_nro, $que_mostrar);
                break;
            case 'cambiar_campos': 
                $type = $this->request->getPost('type');
                $campo_salida = $this->request->getPost('id_campo');
                $valor = $this->request->getPost('valor');
                $nombre_campo_frm = $this->request->getPost('nombre_campo_frm');
                $nombre_campo_bd = $this->request->getPost('nombre_campo_bd');
                $tabla_update = $this->request->getPost('tabla_update');
                $id_operacion = $this->request->getPost('id_operacion');
                $response = cambiar_campos($type, $campo_salida, $valor, $nombre_campo_frm, $nombre_campo_bd, $tabla_update, $id_operacion);
                break;
            case 'muestra_mensaje':
                $id_mensaje = $this->request->getPost('id_mensaje');
                $tabla = $this->request->getPost('tabla');
                $response = muestra_mensaje($id_mensaje, $tabla);
                break;
            case 'file_document':
                $file = $this->request->getFile('file_certificado');
                if(!empty($file)){
                    $form = $this->request->getPost();
                    $db = \Config\Database::connect();
                    if($form['frm_id_procedencia'] == 2){
                        if($file->getSize() > 0){
                            $newName = $file->getName();
                            if($file->move('assets/img/docs_informes', $file->getName())){
                                $sql_guardar = "update certificacion set doc_informe_final = '".$file->getName()."' where certificado_nro='".$form['frm_id_certificado']."'";
                                $db->query($sql_guardar);
                            }
                        }
                    }else{
                        if($file->getSize() > 0){
                            $newName = $file->getName();
                            $file->move('assets/img/docs_informes', $file->getName());
                            $sql_guardar = "update certificacion set doc_primer_informe = '".$file->getName()."' where certificado_nro='".$form['frm_id_certificado']."'";
                            $db->query($sql_guardar);
                        }
                    }
                }
                $response = [];
                break;
            case 'guardar':
                $form = $this->request->getPost();
                // return json_encode($form);
                $certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $form['frm_id_certificado']);
                $certificado = $certificado[0];
                $response = guardar_certificado($form, $certificado);
                $aux_mensaje = '';
                if($form['frm_id_procedencia'] == 2){
                    if($certificado->cer_fecha_publicacion > '0000-00-00 00:00:00'){
                        $mensajes = procesar_registro_fetch('mensaje_resultado', 'id_mensaje', $form['frm_mensaje_resultado']);
                        $aux_mensaje = $mensajes[0]->mensaje_titulo;
                        if($certificado->cer_fecha_facturacion > '0000-00-00 00:00:00' ){
                            $aux_btn_i = "fad fa-usd-circle";
                            $aux_btn_c = 'cyan';
                            $aux_metodo = 3;
                        }else{
                            $aux_btn_i = "fad fa-thumbs-up";
                            $aux_btn_c = 'deep-orange';
                            $aux_metodo = 2;
                        }
                    }else{
                        $aux_btn_i = "fad fa-check-circle";
                        $aux_btn_c = 'green';
                        $aux_metodo = 1;
                    }
                    $aux_bttn .= '<button class="btn '.$aux_btn_c.' white-text" onClick="actualizar_informe(`'.$certificado->certificado_nro.'`, `'.$aux_metodo.'`, '.session('user')->usr_rol.', 2)"><i class="'.$aux_btn_i.'"></i></button>';
                    $aux_div = '#certificado_'.$form['frm_id_certificado'];
                }else{
                    $aux_bttn .= '<button class="btn green white-text" onClick="descargar_info(`'.$certificado->certificado_nro.'`, 0, `'.session('user')->usr_rol.'`, 2)"><i class="fad fa-check-circle"></i></button>';
                    $aux_div = '#pre_informe_'.$form['frm_id_certificado'];
                }
                $boton = [
                    'button' => $aux_bttn,
                    'div' => $aux_div
                ];
                $mensaje_resultado = [
                    'div' => '#div_resultado_'.$certificado->certificado_nro,
                    'mensaje' => $aux_mensaje
                ];
                $response = [
                    'mensaje' => $response,
                    'boton' => $boton,
                    'mensaje_resultado' => $mensaje_resultado
                ];
                break;
            case 'previsualizar':
                $form = $this->request->getPost();
                $response = previsualizar($form);
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 0,
                    "margin_right" => 0,
                    "margin_top" => 0,
                    "margin_bottom" => 0,
                    "margin_header" => 0,
                    "margin_footer" => 0
                ]);
                $tipo_mensajes = $form['frm_id_procedencia'] == 1 ? 2:1;
                $parametros_view = [
                    'aux_mensaje' => $response['aux_mensaje'],
                    'certificado' => $response['certificado'],
                    'cliente' => $response['cliente'],
                    'muestreo' => $response['muestreo'],
                    'aux_fecha_informe' => $response['aux_fecha_informe'],
                    'fecha_analisis' => $response['fecha_analisis'],
                    'detalle_para_tipo_muestreo' =>  $response['fila_detalle_para_tipo_muestreo'],
                    'frm_plantilla' => $form['frm_plantilla'],
                    'frm_form_valo' => $form['frm_form_valo'],
                    'frm_mensaje_resultado' =>  $form['frm_mensaje_resultado'],
                    'tipo_mensajes' => $tipo_mensajes,
                    'frm_mensaje_firma' => $form['frm_mensaje_firma'],
                    'primer_informe' => $form['primer_informe']
                ];
                $css  = file_get_contents('assets/css/styles.css');
                $salida = view('views_mpdf/cliente/plantilla',$parametros_view);
                // return var_dump($form);
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($salida, \Mpdf\HTMLParserMode::HTML_BODY);
                $this->response->setHeader('Content-Type', 'application/pdf');
                $mpdf->Output('arjun.pdf','I');
                break;
            case 'presentar_preinforme':
                $form = $this->request->getPost();
                $response = presentar_preinforme($form);
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 0,
                    "margin_right" => 0,
                    "margin_top" => 0,
                    "margin_bottom" => 0,
                    "margin_header" => 0,
                    "margin_footer" => 0
                ]);
                $salida = view('views_mpdf/preliminar',[
                    'db' => $response['db'],
                    'certificado' => $response['certificado'],
                    'cliente' => $response['cliente'],
                    'muestreo' => $response['muestreo'],
                    'aux_fecha_informe' => $response['aux_fecha_informe'],
                    'aux_mensaje' => $response['aux_mensaje'],
                    'fecha_analisis' => $response['fecha_analisis'],
                    'fila_detalle_para_tipo_muestreo' => $response['fila_detalle_para_tipo_muestreo'],
                    'plantilla' => $form['frm_plantilla'],
                    'form_entrada' => $form
                ]);
                if($form['envio'] == 2){
                    $certificado_aux = $response['certificado'];
                    $aux_mensaje = '';
                    if($form['frm_id_procedencia'] == 2){
                        if($certificado_aux->cer_fecha_publicacion > '0000-00-00 00:00:00'){
                            $mensajes = procesar_registro_fetch('mensaje_resultado', 'id_mensaje', $form['frm_mensaje_resultado']);
                            $aux_mensaje = $mensajes[0]->mensaje_titulo;
                            if($certificado_aux->cer_fecha_facturacion > '0000-00-00 00:00:00' ){
                                $aux_btn_i = "fad fa-usd-circle";
                                $aux_btn_c = 'cyan';
                                $aux_metodo = 3;
                            }else{
                                $aux_btn_i = "fad fa-thumbs-up";
                                $aux_btn_c = 'deep-orange';
                                $aux_metodo = 2;
                            }
                        }else{
                            $aux_btn_i = "fad fa-check-circle";
                            $aux_btn_c = 'green';
                            $aux_metodo = 1;
                        }
                        $aux_bttn .= '<button class="btn '.$aux_btn_c.' white-text" onClick="actualizar_informe(`'.$certificado_aux->certificado_nro.'`, `'.$aux_metodo.'`, '.session('user')->usr_rol.', 1)"><i class="'.$aux_btn_i.'"></i></button>';
                        $aux_div = '#certificado_'.$form['frm_id_certificado'];
                    }else{
                        $aux_bttn .= '<button class="btn green white-text" onClick="descargar_info(`'.$certificado_aux->certificado_nro.'`, 0, `'.session('user')->usr_rol.'`, 0)"><i class="fad fa-check-circle"></i></button>';
                        $aux_div = '#pre_informe_'.$form['frm_id_certificado'];
                    }
                    $boton = [
                        'button' => $aux_bttn,
                        'div' => $aux_div
                    ];
                    $mensaje_resultado = [
                        'div' => '#div_resultado_'.$certificado_aux->certificado_nro,
                        'mensaje' => $aux_mensaje
                    ];
                    $response = [
                        'boton' => $boton,
                        'mensaje' => $mensaje_resultado
                    ];
                }else{
                    $css  = file_get_contents('assets/css/styles-f.css');
                    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                    $mpdf->WriteHTML($salida, \Mpdf\HTMLParserMode::HTML_BODY);
                    $name = strtolower($response['aux_mensaje']);
                    $name = str_replace(' ', '_', $name);
                    $name = $response['certificado']->certificado_nro.'_'.$name.'.pdf';
                    $mpdf->Output($name,'D');
                }
                break;
            case 'presentar_preinforme2':
                $certificado_nro = $this->request->getPost('certificado_nro');
                $que_mostrar = $this->request->getPost('que_mostrar');
                $user_rol_id = $this->request->getPost('user_rol_id');
                $response = presentar_preinforme2($certificado_nro, $que_mostrar, $user_rol_id);
                break;
            case 'descargar':
                $certificado_nro = $this->request->getPost('certificado_nro');
                $que_mostrar = $this->request->getPost('que_mostrar');
                $user_rol_id = $this->request->getPost('user_rol_id');
                $response = presentar_preinforme2($certificado_nro, $que_mostrar, $user_rol_id, true);
                $css  = file_get_contents('assets/css/styles-f.css');
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 0,
                    "margin_right" => 0,
                    "margin_top" => 0,
                    "margin_bottom" => 0,
                    "margin_header" => 0,
                    "margin_footer" => 0
                ]);
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                // return $response;
                $mpdf->WriteHTML($response, \Mpdf\HTMLParserMode::HTML_BODY);
                // $this->response->setHeader('Content-Type', 'application/pdf');
                // $mpdf->Output('arjun.pdf','I');
                $name = $que_mostrar == 0 ? $certificado_nro.'-PRELIMINAR.pdf':$certificado_nro.'-REPORTE_DE_ENSAYO_3.pdf';
                $name = strtolower($name);
                $mpdf->Output($name,'D');
                break;
                default:
                $response = 'Funcion no definida';
                break;
            }
        return json_encode(['data' => $response]);
    }
        
    public function certificado_down(){
        $certificados_reporte = $this->request->getPost('certificado_reporte');
        $certificados_preliminar = $this->request->getPost('certificado_preliminar');
        if (!empty($certificados_reporte) || !empty($certificados_preliminar)){
            $db = \Config\Database::connect();
            $zip = new \ZipArchive();
            $count_preliminar = !empty($certificados_preliminar) ? count($certificados_preliminar) : 0;
            $count_reporte = !empty($certificados_reporte) ? count($certificados_reporte) : 0;
            $count = $count_reporte + $count_preliminar;
            $certificados = [];
            $i = 0;
            while ($i < $count) {
                if (!empty($certificados_preliminar)) {
                    foreach ($certificados_preliminar as $key => $value) {
                        $certificados[$i]['certificado_nro'] = $value;
                        $certificados[$i]['id_mensaje_tipo'] = 1;
                        $i++;
                    }
                }
                if(!empty($certificados_reporte)){
                    foreach ($certificados_reporte as $key => $value) {
                        $certificados[$i]['certificado_nro'] = $value;
                        $certificados[$i]['id_mensaje_tipo'] = 2;
                        $i++;
                    }
                }
            }
            $archivo = 'informes_GestionLabs.zip';
            foreach ($certificados as $key => $value) {
                $certificado_nro = $value['certificado_nro']; //183342;
                $id_mensaje_tipo = $value['id_mensaje_tipo']; //183342;
                $c_v_m = procesar_registro_fetch('certificacion_vs_mensaje', 'id_certificacion', $certificado_nro, 'id_mensaje_tipo', $id_mensaje_tipo);
                $certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $certificado_nro);
                $certificado = $certificado[0];
                //formateo de muestreo
                $sql = "select * from muestreo where id_muestreo=$certificado->id_muestreo  group by id_muestreo";
                $query = $db->query($sql)->getResult();
                $muestreo = $query[0];
                $cliente = procesar_registro_fetch('usuario ', 'id', $muestreo->id_cliente);
                $cliente = $cliente[0];
                $detalle_para_tipo_muestreo = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
                $detalle_para_tipo_muestreo = $detalle_para_tipo_muestreo[0];
                $fecha_analisis = recortar_fecha($muestreo->mue_fecha_muestreo,1);
                $frm_form_valo = $c_v_m[0]->form_valo; //tipo de formateo de la plantilla
                $frm_plantilla = $c_v_m[0]->id_plantilla;
                $frm_mensaje_resultado = $c_v_m[0]->id_mensaje_resultado; // cero para cuando venga de creacion de construccion de documento
                $frm_mensaje_observacion = $c_v_m[0]->id_mensaje_comentario;
                $frm_mensaje_firma = $c_v_m[0]->id_firma;
                $frm_complemento = $c_v_m[0]->complemento;
                $frm_modificacion = $c_v_m[0]->modificacion;
                
                $frm_id_procedencia = $c_v_m[0]->id_mensaje_tipo == 1 ? 0 : 1;
                if($frm_id_procedencia == 0){// Primer informe
                    $tipo_mensajes = 1;
                    if($certificado->cer_fecha_preinforme == '0000-00-00 00:00:00'){
                        $aux_fecha_informe=date("Y-m-d H:i:s");
                    }else{
                        $aux_fecha_informe=$certificado->cer_fecha_preinforme;
                    }
                }else{
                    $tipo_mensajes = 2;
                    if($certificado->cer_fecha_informe == '0000-00-00 00:00:00'){
                        $aux_fecha_informe=date("Y-m-d H:i:s");
                    }else{
                        $aux_fecha_informe=$certificado->cer_fecha_informe;
                    }
                    
                }
                $zip->open($archivo, \ZIPARCHIVE::CREATE);
		        $campo_primer_informe = $tipo_mensajes == 2 ? 0:1;
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
                    and m.id_producto = p.id_producto
                    and e.campo_primer_informe=$campo_primer_informe
                    and c.certificado_nro=$certificado->certificado_nro "; // group by e.id_ensayo
                $query_ensayos = $db->query($sql_ensayos)->getResult();
                // var_dump([$query_ensayos, $certificado->certificado_nro]); die;
                $onac = '';
                foreach($query_ensayos as $fila_ensayos){
                    $parametro = procesar_registro_fetch('parametro', 'id_parametro', $fila_ensayos->id_parametro);
                    if( preg_match('/[*]/', $parametro[0]->par_descripcion) ){
                        $onac = '<img src="assets/img/onac_2.png" height="105">';
                        break;
                    }

                }
                $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
                $sql_norma_producto = 
                "SELECT * FROM ensayo e, producto p, norma n
                        where e.id_producto=p.id_producto and p.id_norma=n.id_norma
                        and e.id_ensayo=(SELECT MAX(id_ensayo) FROM ensayo_vs_muestra e where id_muestra=".$detalle[0]->id_muestra_detalle." )";
                $norma_producto = $db->query($sql_norma_producto)->getResult();
                
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 5,
                    "margin_right" => 5,
                    "margin_top" => 35,
                    "margin_bottom" => 13.5,
                    "margin_header" => 5.5
                ]);
                
                $css  = file_get_contents('assets/css/styles.css');
                $fecha_version_v9 = '2024-02-05';
                $fecha_publicacion_c =  !empty($certificado->cer_fecha_informe) ? $certificado->cer_fecha_informe : date('Y-m-d');
                
                $fecha_eliminar_info_onac_inicio    = strtotime('2024-05-23');
                $fecha_eliminar_info_onac_final     = strtotime('2024-06-14');
                $fecha_muestreo                     = strtotime($muestreo->mue_fecha_muestreo);
                $muestra_logo                       = false;

                $fechaV9 = strtotime(($fecha_version_v9));
                $fechaCertificado = strtotime(($fecha_publicacion_c));

                if($fechaCertificado < $fechaV9){
                    $page = 'plantilla';
                    $version = '08';
                    $vigencia = '2023-07-24';
                    $accre = '( * ) Ensayos acreditados: En Asbioquim SAS contamos con acreditación ONAC, vigente a la fecha con código de acreditación 19-LAB-002, bajo la norma NTC-ISO/IEC 17025:2017.';
                }else{
                    if($norma_producto[0]->n_analizar_1407 != 0) $page = 'plantilla_1407';
                    else $page = 'plantilla_v9';
                    if($fecha_muestreo >= $fecha_eliminar_info_onac_inicio && $fecha_muestreo < $fecha_eliminar_info_onac_final){
                        $accre = "";
                    }else{
                        $accre = '<br>En Asbioquim SAS contamos con acreditación ONAC vigente a la fecha con código de acreditación 19-LAB-002, bajo la norma NTC-ISO/IEC 17025:2017.
                        <br>*Este ensayo NO están incluido en el certificado de acreditación';
                        $muestra_logo = true;
                    }
                    $version = '09';
                    $vigencia = '2024-02-05';
                }
                
                
                $parametros_view = [
                    'type_informe' => $tipo_mensajes,
                    'certificado' => $certificado,
                    'cliente' => $cliente,
                    'muestreo' => $muestreo,
                    'aux_fecha_informe' => $aux_fecha_informe,
                    'fecha_analisis' => $fecha_analisis,
                    'detalle_para_tipo_muestreo' => $detalle_para_tipo_muestreo,
                    'frm_plantilla' => $frm_plantilla,
                    'frm_form_valo' => $frm_form_valo,
                    'frm_mensaje_resultado' => $frm_mensaje_resultado,
                    'tipo_mensajes' => $tipo_mensajes, 
                    'frm_mensaje_firma' => $frm_mensaje_firma,
                    'complemento'       => $frm_complemento,
                    'modificacion'      => $frm_modificacion,
                    'query_ensayos'     => $query_ensayos,
                    'campo_primer_informe' => $campo_primer_informe,
                    'accre' => $accre
                ];
                
                $html = view("views_mpdf/cliente/{$page}",$parametros_view);
                
                $mpdf->SetDefaultBodyCSS('background-image', "assets/img/image001.jpg");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->SetDefaultBodyCSS('background-image-resolution', '300dpi');
                $mpdf->SetDefaultBodyCSS('background-image-opacity', 0.6);
                $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="100%" align="right">Pagina {PAGENO}/{nbpg}</td>
                        </tr>
                    </table>');
                    
                $mpdf->SetHTMLHeader('
                    <table style="width: 100%;">
                        <thead>
                            <tr class="amc-centrado">
                                <th>
                                </th>
                                <th style="padding-left:11.5px">
                                    '.($muestra_logo ? $onac : '').'
                                </th>
                                <th class="right data_aux">
                                        CÓDIGO: PRO-F-008
                                        <br>
                                        VERSIÓN: '.$version.'
                                        <br>
                                        FECHA DE VIGENCIA: '.$vigencia.'
                                </th>
                            </tr>
                        </thead>	
                    </table>
                ');
                
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
                $this->response->setHeader('Content-Type', 'application/pdf');
                if($frm_plantilla != 1){
                    $db = \Config\Database::connect();
                    $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado->id_muestreo_detalle);
                    $sql_norma_producto = 
					    "SELECT * FROM ensayo e, producto p, norma n
							where e.id_producto=p.id_producto and p.id_norma=n.id_norma
							and e.id_ensayo=(SELECT MAX(id_ensayo) FROM ensayo_vs_muestra e where id_muestra=".$detalle[0]->id_muestra_detalle." )";
					$norma_producto = $db->query($sql_norma_producto)->getResult();
					$plantilla = '';
					$norma_producto[0]->pro_descripcion = $norma_producto[0]->pro_descripcion != '' ? $norma_producto[0]->pro_descripcion : $norma_producto[0]->pro_nombre;
							
                    $plantilla .= '
                        <div class="texto-especificacion">
    					<p><b>Especificacion: </b>'.$norma_producto[0]->nor_nombre.' - '.$norma_producto[0]->pro_descripcion;
					$fechasUtiles = procesar_registro_fetch('fecha_vida_util', 'id_detalle_muestreo', $certificado->id_muestreo_detalle);
            		if(!empty($fechasUtiles)){
            			$fecha_aux = [];
            			foreach ($fechasUtiles as $key => $fecha)
            				array_push($fecha_aux, $fecha->fecha);
            			$fecha_analisis_aux = implode(', ', $fecha_aux);
            		}
					if(!empty($fechasUtiles)){
						$plantilla .= !empty($complemento) ? "<br>$frm_complemento":'';
					}
					$plantilla .= '
    					<br>'.$accre.'
    					<br>( <sup>1</sup> ) La declaración de conformidad (Cumple/No cumple) del resultado obtenido frente a una especificación normativa, se determinó aplicando como regla de decisión, que el valor máximo de probabilidad de no cumplimiento será del 5% aplicando la fórmula del “Límite de tolerancia superior único” según la norma JCGM 106:2012.
    					<br>( <sup>2</sup> ) Análisis subcontratados
    					<br>( <sup>3</sup> ) Información suministrada por el cliente. Asbioquim SAS no se hace responsable por la información suministrada por el cliente.
    					<br>Los resultados son válidos únicamente para el ítem de ensayo analizado. Estos análisis no pueden ser reproducidos parcial o totalmente sin autorización del laboratorio Asbioquim SAS.
    					<br>Confirme la validez de este documento ingresando a <a href="https://asbioquim.com.co" target="_blank">asbioquim.com.co </a> y el código ';
    				$plantilla .= $tipo_mensajes == 1 ? $certificado->clave_documento_pre : $certificado->clave_documento_final;
    				if(!empty($frm_modificacion)){
    					$plnatilla .= !empty($frm_modificacion) ? "<br>Nota: $frm_modificacion":'';
    				}
    				$plantilla .= '	</p>
    				</div>
                    ';
                    
                    $firma	 = procesar_registro_fetch('cms_firma', 'id_firma', $frm_mensaje_firma);
    				$firma1 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_1);
    				$firma2 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_2);
                    $aux_nombre1    = $firma1[0]->nombre;
    				$aux_cargo1     = $firma1[0]->cargo;
    				$aux_firma1     = $firma1[0]->firma;
    				$aux_nombre2     = $firma2[0]->nombre;
    				$aux_cargo2      = $firma2[0]->cargo;
    				$aux_firma2     = $firma2[0]->firma;
                    $plantilla .= '<table width="100%" class="firmas">
    				    <thead>
        					<tr>
        						<th>';
        						    if(!empty($aux_firma1)){
        							    $plantilla .= '<img src="assets/img/firmas/'.$aux_firma1.'" width="100">';
        						    }
        						$plantilla .= '</th>
        						<th>';
        						    if(!empty($aux_firma2)){
        							    $plantilla .= '<img src="assets/img/firmas/'.$aux_firma2.'" width="100">';
        						    }
        						$plantilla .= '</th>  
        					</tr>
    				    </thead>
    				    <tbody>
        					<tr>
        						<td class="firmas_2">
        							<br><strong>'.$aux_cargo1.'</strong>
        						</td>
        						<td class="firmas_2">
        							<br><strong>'.$aux_cargo2.'</strong>
        						</td>
        					</tr>
    				    </tbody>
    				</table>
    				<div id="amc-header2" class="amc-centrado">                        
    					<strong> - FIN DE INFORME - </strong><br>
    				</div>
    				';
                    $mpdf->AddPage();
                    $mpdf->WriteHTML($plantilla);
                }
                $mpdf->Output('arjun.pdf','I');
                $name = 'Informe-'.$certificado_nro;
                $name .= $tipo_mensajes == 1 ? '-1':'';
                $name .= '.pdf';
                $name = str_replace(' ', '_', $name);
                $name = strtolower($name);
                if($count == 1 ){
                    $mpdf->Output($name,'D');
                    exit;
                }
                $mpdf->Output($name,'F');
                $zip->addFile($name, $name);
                $zip->close();
                unlink($name);
            }
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=$archivo");
            readfile($archivo);
            unlink($archivo);
            $certificados_reporte = null;
            $certificados_preliminar = null;
        }else{
            return redirect()->back();
        }
    }
} 