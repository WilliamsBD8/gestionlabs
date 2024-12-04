<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Muestreo;
use App\Models\CertificacionView;
use Config\Services;



class ClienteController extends BaseController
{
    public function __construct()
    {
	    // parent::__construct();
        $this->get_certificaciones();
    }
    //  MisDatos
    public function user(){
        return view('clientes/user');
    }

	//  Password
	public function password(){
    	$validation = Services::validation();
        return view('clientes/new_password', ['validation' => $validation]);
    }

    public function password_update(){
    	$pwd_actual 	= $this->request->getPost('password_actual');
    	$pwd_new 		= $this->request->getPost('password_new');
        if (empty($pwd_actual)) {
            return redirect()->back()->with('errors', 'La contraseña actual es necesaria.');
        }
		$password = session('user')->password;
        $user     = session('user')->username;
        $pwd_new  = md5($pwd_new);
        if (md5($pwd_actual) != $password) 
			return redirect()->back()->with('errors', 'La contraseña no concuerdan.');

    	if ($this->validate([
    		'password_new' 		=> 'required|min_length[4]|max_length[32]',
    		'password_confirm' 	=> 'required|matches[password_new]',
    	],[
    		'password_new' 		=> [
    			'required' 		=> 'La nueva contraseña es necesaria.',
    			'min_length' 	=> 'La nueva contraseña es muy corta.',
    			'max_length' 	=> 'La nueva contraseña es muy larga.'
    		],
    		'password_confirm' 	=> [
    			'required' 		=> 'La confirmacion de contraseña es necesaria.',
    			'matches' 		=> 'Las contraseñas no concuerdan.'
    		]
    	]))
    	{
            $user = new Cliente();
            $user->set(['password' => $pwd_new])
            ->where(['id' => session('user')->id])
            ->update();
            session('user')->password = $pwd_new;
            return redirect()->back()->with('success', '$result');
    	}else{
    		return redirect()->back()->withInput();
    	}
    }

    //  Certificados
    public function model($data){
        $table = "view_certificados".session('user')->id;
        $certificados = new CertificacionView();
        $certificados->join('muestreo_detalle', "$table.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle", 'left');
        foreach ($data->columnas as $key => $column) {
            $column = (object) $column;
            $column->search = (object) $column->search;
            if(!empty($column->search->value)){
                switch ($column->name) {
                    case 'mue_fecha_muestreo':
                        $value = json_decode($column->search->value);
                        if(!empty($value->date_start))
                            $certificados = $certificados->where('mue_fecha_muestreo >=', "$value->date_start 00:00:00");
                        if(!empty($value->date_finish))
                            $certificados = $certificados->where('mue_fecha_muestreo <=', "$value->date_finish 23:59:59");
                        break;
                    case 'parametro':
                        $certificados = $certificados
                        ->join('ensayo_vs_muestra', "ensayo_vs_muestra.id_muestra = $table.id_muestreo", 'left')
                        ->join('ensayo', 'ensayo.id_ensayo = ensayo_vs_muestra.id_ensayo', 'left')
                        ->join('parametro', 'ensayo.id_parametro = parametro.id_parametro', 'left');
                        $certificados = $certificados->where(['parametro.id_parametro' => $column->search->value]);
                        break;
                    case 'tipo_analisis':
                        $certificados = $certificados->where('muestreo_detalle.id_tipo_analisis', $column->search->value);
                        break;
                    case 'conformidad':
                        $certificados = $certificados->where("$table.$column->name", $column->search->value);
                        break;
                    
                    default:
                        $certificados = $certificados->like("$table.$column->name", $column->search->value);
                    break;
                }
            }
        }
        return $certificados;
    }
    public function certificado(){
        $filtros                = $this->filtros();
        return view('clientes/certificado',[
            'filtros'               => $filtros,
        ]);
    }

    public function certificado_filtrar(){
        $data = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 0,
            'columnas'  => $_GET['columns'] ?? [],
        ];

        return json_encode([
            'data' => $this->model($data)->get($data->length, $data->start)->getResult(),
            "draw" => $data->draw,
            "recordsTotal" => $this->model($data)->countAllResults(),
            "recordsFiltered" => $this->model($data)->countAllResults(),
            "c" => $data
        ]);
    }
    public function certificado_download($certificado){
            // $mpdf = new \Mpdf\Mpdf([
            //         'mode' => 'utf-8',
            //         'format' => 'Letter',
            //         "margin_left" => 0,
            //         "margin_right" => 0,
            //         "margin_top" => 0,
            //         "margin_bottom" => 0,
            //         "margin_header" => 0,
            //         "margin_footer" => 0
            //     ]);
            // $html = view('clientes/generate_pdf',['value' => $certificado]);
            // $css  = file_get_contents('assets/css/styles.css');
            // $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
            // $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
            // // $this->response->setHeader('Content-Type', 'application/pdf');
            // // $mpdf->Output('arjun.pdf','I');
            // $name = 'certificado_'.$certificado.'.pdf';
            // $mpdf->Output($name,'D');
    }
    public function certificado_down(){
        $certificados_preliminar = $this->request->getPost('certificado_preliminar');
        $certificados_reporte = $this->request->getPost('certificado_reporte');
    	if (!empty($certificados_reporte) || !empty($certificados_preliminar)){
            $db = \Config\Database::connect();
            $zip = new \ZipArchive();
            $count_preliminar = !empty($certificados_preliminar) ? count($certificados_preliminar) : 0;
            // return var_dump($certificados_reporte);
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
            $archivo = 'certificados_GestionLabs.zip';
    		foreach ($certificados as $key => $value) {
                $certificado_nro = $value['certificado_nro']; //183342;
                $id_mensaje_tipo = $value['id_mensaje_tipo']; //183342;
                // $frm_plantilla= $_POST['plantilla'] ;
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
                $frm_id_procedencia = 1; // 0 PRELIMINAR - 1 REPORTE DE ENSAYO
                $frm_id_procedencia = $c_v_m[0]->id_mensaje_tipo == 2 ? 1 : 0;
                if($frm_id_procedencia == 0){//preinformes
                    $aux_mensaje='PRELIMINAR';
                    $tipo_mensajes = 1;
                    if($certificado->cer_fecha_preinforme == '0000-00-00 00:00:00'){
                        $aux_fecha_informe=date("Y-m-d H:i:s");
                    }else{
                        $aux_fecha_informe=$certificado->cer_fecha_preinforme;
                    }
                    
                }else{
                    $aux_mensaje='REPORTE DE ENSAYO';
                    $tipo_mensajes = 2;
                    if($certificado->cer_fecha_informe == '0000-00-00 00:00:00'){
                        $aux_fecha_informe=date("Y-m-d H:i:s");
                    }else{
                        $aux_fecha_informe=$certificado->cer_fecha_informe;
                    }
                    
                }
                $zip->open($archivo, \ZIPARCHIVE::CREATE);
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'Letter',
                    "margin_left" => 0,
                    "margin_right" => 0,
                    "margin_top" => 0,
                    "margin_bottom" => 0,
                ]);
                $mpdf->showImageErrors = true;
                $css  = file_get_contents('assets/css/styles.css');
                $html = view('views_mpdf/cliente/plantilla', [
                    'aux_mensaje' => $aux_mensaje,
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
                    'frm_mensaje_firma' => $frm_mensaje_firma
                ]);
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
                $name = $certificado_nro.'_'.$aux_mensaje.'.pdf';
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
    	}else{
    		return redirect()->back();
    	}
    }


     // Reportes
    public function reporte(){
        $filtros    = $this->filtros();
        $data = (object)['columnas' => []];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $sistema = $this->model($data, true)->select(
            ['YEAR(mue_fecha_muestreo) as año', 'MONTH(mue_fecha_muestreo) as mes', 'count(*) as total']
            )->groupBy(['YEAR(mue_fecha_muestreo)', 'MONTH(mue_fecha_muestreo)'])
            ->orderBy('YEAR(mue_fecha_muestreo)', 'ASC')
            ->get()->getResult();
        foreach($sistema as $key => $sistem) {
            $sistem->mes = $meses[(intval($sistem->mes) - 1)].' '.$sistem->año;
        }
        return view('clientes/reporte', [
            'filtros'   => $filtros,
            'historial' => $sistema,
        ]);
    }
    public function reporte_post(){
        $data = (object) [
            "columnas" => [
                [
                    "name" => 'mue_fecha_muestreo',
                    "search" => [
                        "value" => json_encode([
                            "date_start" => $this->request->getPost('date_start'),
                            "date_finish" => $this->request->getPost('date_finish'),
                        ])
                    ]
                ],
                [
                    "name" => 'parametro',
                    'search' => ['value' => $this->request->getPost('parametros')]
                ],
                [
                    "name" => 'tipo_analisis',
                    'search' => ['value' => $this->request->getPost('tipo_analisis')]
                ],
                [
                    "name" => 'mue_identificacion',
                    'search' => ['value' => $this->request->getPost('producto')]
                ],
                [
                    "name" => 'mensaje',
                    'search' => ["value" => $this->request->getPost('concepto')]
                ],
                [
                    "name" => 'mue_subtitulo',
                    'search' => ['value' => $this->request->getPost('seccional')]
                ],
                [
                    "name" => 'conformidad',
                    'search' => ['value' => $this->request->getPost('conformidad')]
                ],
                
            ]
        ];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $sistema = $this->model($data)->select(
            ['YEAR(mue_fecha_muestreo) as año', 'MONTH(mue_fecha_muestreo) as mes', 'count(*) as total']
            )->groupBy(['YEAR(mue_fecha_muestreo)', 'MONTH(mue_fecha_muestreo)'])
            ->orderBy('YEAR(mue_fecha_muestreo)', 'ASC')
            ->get()->getResult();
        foreach($sistema as $key => $sistem) {
            $sistem->mes = $meses[(intval($sistem->mes) - 1)].' '.$sistem->año;
        }
        return json_encode([
            'data' => $sistema,
            'report' => $data
        ]);
    }
    
    // Accreditations
    public function accreditations(){
        $accreditations = procesar_registro_fetch('accreditations', 0, 0);
        return view('clientes/accreditations', ['accreditations' => $accreditations]);
    }
    public function accreditations_d(){
        $id = $this->request->getPost("accreditation");
        $file = procesar_registro_fetch('accreditations', 'id', $id);
        $direction ='page/documents/accreditations/'.$file[0]->document;
        $filename = $file[0]->document;
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-type: application/pdf");
        readfile($direction);
    }
    // Filtros
    public function filtros(){
        $id = session('user')->id;
        $muestreo = new Muestreo();
        $mensaje_resultado = $muestreo
            ->distinct('mensaje_resultado.mensaje_titulo')
            ->select('mensaje_resultado.mensaje_titulo as mensaje_titulo,
                mensaje_resultado.id_mensaje as id_mensaje')
            ->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
            ->join('mensaje_resultado', 'certificacion.id_mensaje = mensaje_resultado.id_mensaje')
            ->where(['id_cliente' => $id])
            ->orderBy('mensaje_titulo', 'asc')
            ->get()->getResult();

        $parametros_resultado = $muestreo
            ->distinct('parametro.id_parametro')
            ->select('parametro.id_parametro,parametro.par_descripcion')
            ->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
            ->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle')
            ->join('producto', 'producto.id_producto = muestreo_detalle.id_producto')
            ->join('ensayo_vs_muestra', 'ensayo_vs_muestra.id_muestra = muestreo.id_muestreo')
            ->join('ensayo', 'ensayo.id_ensayo = ensayo_vs_muestra.id_ensayo')
            ->join('parametro', 'ensayo.id_parametro = parametro.id_parametro')
            ->where(['id_cliente' => $id])
            ->orderBy('par_descripcion', 'asc')
            ->get()->getResult();

        $productos_resultado = $muestreo
            ->distinct('producto.pro_nombre')
            ->select('producto.pro_nombre as producto,
                producto.id_producto as id_producto')
            ->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
            ->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle')
            ->join('producto', 'producto.id_producto = muestreo_detalle.id_producto')
            ->where(['id_cliente' => $id])
            ->orderBy('producto', 'asc')
            ->get()->getResult();

        $analisis_resultado = $muestreo
            ->distinct('muestreo_detalle.id_tipo_analisis')
            ->select('muestreo_detalle.id_tipo_analisis as id_muestra_tipo_analsis,
                muestra_tipo_analisis.mue_nombre as mue_nombre')
            ->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
            ->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle')
            ->join('muestra_tipo_analisis', 'muestreo_detalle.id_tipo_analisis = muestra_tipo_analisis.id_muestra_tipo_analsis')
            ->where(['id_cliente' => $id])
            ->orderBy('mue_nombre', 'asc')
            ->get()->getResult();

        $resultado_seccional = $muestreo
            ->distinct('muestreo.mue_subtitulo')
            ->select('muestreo.mue_subtitulo as seccional')
            ->where(['id_cliente' => $id])
            ->orderBy('muestreo.mue_subtitulo', 'asc')
            ->get()->getResult();

        $array_seccional = [];
        foreach($resultado_seccional as $seccional){
            $aux_seccional = true;
            $aux_seccion = explode(' ',$seccional->seccional);
            $aux_seccion = implode(' ', $aux_seccion);
            foreach($array_seccional as $seccion){
                if($seccion == $aux_seccion)
                    $aux_seccional = false;
            }
            if($aux_seccional){
                array_push($array_seccional, $aux_seccion);
            }
        }
        return (object)[
            'resultado_concepto'    => $mensaje_resultado,
            'resultado_muestra'     => $analisis_resultado,
            'resultado_parametros'  => $parametros_resultado,
            'resultado_productos'   => $productos_resultado,
            'resultado_seccional'   => $array_seccional
        ];
    }

    function get_certificaciones()   
    {
        $db      = \Config\Database::connect();
        $aux_id =" and m.id_cliente=".session('user')->id;    
        $aux_columna_lote =" d.mue_lote,"; 
        
        $sql = "CREATE OR REPLACE VIEW view_certificados".session('user')->id." 
            AS
            select
            c.id_muestreo,
            c.id_muestreo_detalle,
            m.mue_fecha_muestreo,
            c.certificado_nro,
            m.mue_subtitulo,
            ".$aux_columna_lote."
            d.mue_identificacion,        
            c.certificado_estado resultados,
            c.cer_fecha_preinforme preinforme,
            c.cer_fecha_analisis informe,
            c.cer_fecha_informe informe2,
            c.cer_fecha_publicacion fecha_publicacion,
            c.cer_fecha_facturacion fecha_facturacion,
            c.conformidad,
            (select mensaje_titulo from mensaje_resultado where id_mensaje=c.id_mensaje) mensaje,
            c.id_mensaje
                    
            from certificacion c, muestreo m, muestreo_detalle d
            where c.id_muestreo = m.id_muestreo and c.id_muestreo_detalle = d.id_muestra_detalle
            and m.mue_estado <> 0  ".$aux_id." order by certificado_nro desc ";
        $db->query($sql);

    }
}