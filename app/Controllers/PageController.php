<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Muestreo;
use App\Models\Certificacion;
use Config\Services;



class PageController extends BaseController
{
    
    public function certificado($codigo){
        return $this->response->setStatusCode(200)->setJson($codigo);
    }

    public function consulta(){
        $codigo =  $this->request->getPost('codigo');
        $certificacion = new Certificacion();
        $certificado = $certificacion->where(["clave_documento_pre" => $codigo])->get()->getResult();
        if(!empty($certificado)){
            
            return json_encode([
                'response' => true,
                "certificado" => $certificado[0],
                'type' => 1
            ]);
        }
        else{
            $certificado = $certificacion->where(["clave_documento_final" => $codigo])->get()->getResult();
            if(!empty($certificado)){
                if(!empty($certificado[0]->cer_fecha_publicacion))
                    return json_encode([
                        'response' => true,
                        "certificado" => $certificado[0],
                        'type' => 2
                    ]);
                else
                    return json_encode(['response' => false]);
                
            }
            else
                return json_encode(['response' => false]);
        }
    }
    
    public function view($codigo){
        $certificacion = new Certificacion();
        $certificado = $certificacion->where(["clave_documento_final" => $codigo])->get()->getResult();
        if(!empty($certificado)){
            if(!empty($certificado[0]->cer_fecha_publicacion)){
                $muestreo = procesar_registro_fetch('muestreo', 'id_muestreo', $certificado[0]->id_muestreo);
                $detalle_para_tipo_muestreo = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $certificado[0]->id_muestreo_detalle);
                $id_tipo = $certificado[0]->id_tipo_analisis_primer_informe != 0 ? $certificado[0]->id_tipo_analisis_primer_informe : $certificado[0]->id_tipo_analisis_informe_final;
				$analisis_1 = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $id_tipo);
				$analisis_2 = procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis', $certificado[0]->id_tipo_analisis_informe_final);
                // $fecha_analisis = recortar_fecha($muestreo[0]->mue_fecha_muestreo,1);
                $cliente = procesar_registro_fetch('usuario ', 'id', $muestreo[0]->id_cliente);
                // return var_dump($id_tipo);
                return view('funcionarios/pages/correo', [
                    'certificado' => $certificado[0],
                    'muestreo' => $muestreo[0],
                    'detalle' => $detalle_para_tipo_muestreo[0],
                    'cliente' => $cliente[0],
                    'analisis_1' => $analisis_1,
                    'analisis_2' => $analisis_2
                ]);
            }
            else
                return view('errors/html/error_404.php');
        }else
            return view('errors/html/error_404.php');
    }

    public function view_page($codigo, $type){
        $certificacion = new Certificacion();
        $certificado = $certificacion->where(["clave_documento_final" => $codigo])->get()->getResult();
        if(!empty($certificado)){
            if(!empty($certificado[0]->cer_fecha_publicacion)){
                $db = \Config\Database::connect();
                $certificado_nro = $certificado[0]->certificado_nro; //183342;
                $id_mensaje_tipo = $type; //183342;
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
                
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 5,
                    "margin_right" => 5,
                    "margin_top" => 35,
                    "margin_bottom" => 13.5,
                    "margin_header" => 5.5
                ]);
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
                    'query_ensayos'     => $query_ensayos,
                    'campo_primer_informe' => $campo_primer_informe
                ];
                
                $css  = file_get_contents('assets/css/styles.css');
                $html = view('views_mpdf/cliente/plantilla',$parametros_view);
                
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
                                    '.$onac.'
                                </th>
                                <th class="right data_aux">
                                        CÓDIGO: PRO-F-008
                                        <br>
                                        VERSIÓN: 07
                                        <br>
                                        FECHA DE VIGENCIA: 2022-09-02
                                </th>
                            </tr>
                        </thead>	
                    </table>
                ');
                
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
                // return var_dump($html);
                // $mpdf->SetWatermarkImage("assets/img/image001.jpg");
                $mpdf->showWatermarkImage = true;
                $mpdf->SetHTMLFooter('
                <table width="100%">
                <tr>
                <td width="100%" align="right">Pagina {PAGENO}/{nbpg}</td>
                </tr>
                </table>');
                $this->response->setHeader('Content-Type', 'application/pdf');
                if($frm_plantilla == 2){
                    $sql_norma_producto = "SELECT * FROM ensayo e, producto p, norma n
							where e.id_producto=p.id_producto and p.id_norma=n.id_norma
							and e.id_ensayo=(SELECT MIN(id_ensayo) FROM ensayo_vs_muestra e where id_muestra=".$detalle_para_tipo_muestreo->id_muestra_detalle." )";
					$norma_producto = $db->query($sql_norma_producto)->getResult();
                    // firma de sistema
                    $firma	 = procesar_registro_fetch('cms_firma', 'id_firma', $frm_mensaje_firma);
                    $firma1 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_1);
                    $firma2 = procesar_registro_fetch('cms_users', 'id', $firma[0]->id_firma_2);
        
                    $aux_nombre1    = $firma1[0]->nombre;
                    $aux_cargo1     = $firma1[0]->cargo;
                    $aux_firma1     = $firma1[0]->firma;
                    $aux_nombre2    = $firma2[0]->nombre;
                    $aux_cargo2     = $firma2[0]->cargo;
                    $aux_firma2     = $firma2[0]->firma;
                    $documento     = $tipo_mensajes == 1 ? $certificado->clave_documento_pre : $certificado->clave_documento_final;
                    $mpdf->AddPage();
                    $text = '<body style="background: rgb(51, 51, 51); display: flex; justify-content: center;">
                    <div style="width:100%" class="container" >
                        <div class="container_2" >
                            <div style="background:rgba(250, 250, 250, .5) !important; margin-top:125px;">
                                <p><b>Especificacion: </b>'.$norma_producto[0]->nor_nombre.' - '.$norma_producto[0]->pro_nombre .'</p>
                                <p>( * ) Ensayos acreditados: En Asbioquim SAS contamos con acreditación ONAC, vigente a la fecha con código de acreditación 19-LAB-002, bajo la norma NTC-ISO/IEC 17025:2017.</p>
                                <p>( <sup>1</sup> ) La declaración de conformidad (Cumple/No cumple) del resultado obtenido frente a una especificación normativa, se determinó aplicando como regla de decisión, que el valor máximo de probabilidad de no cumplimiento será del 5% aplicando la fórmula del “Límite de tolerancia superior único” según la norma JCGM 106:2012.</p>
                                <p>( <sup>2</sup> ) Análisis subcontratados
                                <p>( <sup>3</sup> ) Información suministrada por el cliente. Asbioquim SAS no se hace responsable por la información suministrada por el cliente. </p>
                                <p>Los resultados son válidos únicamente para la muestra analizada. Estos análisis no pueden ser reproducidos parcial o totalmente sin autorización del laboratorio Asbioquim SAS.</p>
                                <p>Confirme la validez de este documento ingresando a <a href="https://gestionlabs.com">gestionlabs.com </a> y el codigo '. $documento .'</p>
                            </div>
                            <br>
                            <table width="100%" class="firmas">
                                <tr>
                                    <td >
                                        <img src="assets/img/firmas/'. $aux_firma1.'" width="100">
                                    </td>
                                    <td >
                                        <img src="assets/img/firmas/'. $aux_firma2.'" width="100">
                                    </td>                      
                                </tr>
                                <tr>
                                    <td >
                                        <br><strong>'.$aux_cargo1.'</strong>
                                    </td>      
                                    <td >
                                        <br><strong>'.$aux_cargo2.'</strong>
                                    </td>          
                                </tr>
                            </table>
                            <div id="amc-header2" class="amc-centrado">                        
                                <strong> - FIN DE INFORME - </strong><br>
                            </div>
                        </div>
                    </div>
                </body>';
                    $mpdf->WriteHTML($text);
                    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                }
                $name = 'Informe-'.$certificado_nro;
                $name .= $tipo_mensajes == 1 ? '-1':'';
                $name .= '.pdf';
                $name = str_replace(' ', '_', $name);
                $name = strtolower($name);
                $mpdf->Output($name,'I');

            }
            else
                return view('errors/html/error_404.php');
        }else
            return view('errors/html/error_404.php');
    }
    
    public function contacto(){
        $info_contact = procesar_registro_fetch('contacto', 0, 0);
        $info_contact = $info_contact[0];
        
        $info = (object) $this->request->getPost();
        
        $sAsunto    = "Consulta pagina web $info->subject";
        
        $stexto = view('pages/correo', ['info' => $info]);
        
    	$email = \Config\Services::email();
        $email->setFrom($info->email, !empty(configInfo()['name_app']) ? configInfo()['name_app'] : 'IPlanet Colombia S.A.S');
        $email->setTo($info_contact->email);
        $email->setSubject($sAsunto);
        $email->setMessage($stexto);
        $email->send();
        
        $url = str_replace('public', "contact", base_url());
        
        return  redirect()->to($url);
    }
}