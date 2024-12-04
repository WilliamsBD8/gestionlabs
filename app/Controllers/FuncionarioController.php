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
use CodeIgniter\API\ResponseTrait;



class FuncionarioController extends BaseController
{
    use ResponseTrait;
    
    public function remision(){
        $analisis = new Analisis();
        $muestreo = new Muestreo();
        $muestreo_verifica = $muestreo->where(['mue_estado' => 0])->get()->getResult();
        $tabla = new Producto();
        $productos = $tabla->select(['id_producto','pro_nombre'])->orderBy('pro_nombre', 'ASC')->get()->getResult();
        $analisis = $analisis->get()->getResult(); 
        $validation = Services::validation();
        $diluciones  = procesar_registro_fetch('diluciones', 0, 0);
        return view('funcionarios/remision', [
            'analisis' => $analisis,
            'validation' => $validation,
            'muestreo_verifica' => $muestreo_verifica,
            'diluciones' => $diluciones,
            'productos'         => $productos
        ]);
    }

    public function remision_empresa(){
        $validation = Services::validation();
        $message = arreglo_validacion();
        $rules = [
                'frm_nombre_empresa'    => 'required|max_length[200]|is_unique[usuario.name]',
                'frm_contacto_cargo'    => 'required',
                'frm_contacto_nombre'   => 'required|max_length[200]',
                'frm_telefono'          => 'required|max_length[20]',
            ];
        $buscar = $this->request->getPost('buscar');
        switch($buscar){
            case 1:
                $empresa = $this->request->getPost('frm_nombre_empresa');
                $data = new Cliente();
                $empresas = $data->like('name', $empresa)->select('name as text, id')->orderBy('name', 'ASC')->get()->getResult();
                $array_aux =(object) [
                    'id' => $empresa,
                    'text' => $empresa,
                ];
                array_unshift($empresas, $array_aux);
                $return = $empresas;
                break;
            case 2:
                $empresa = $this->request->getPost('frm_nombre_empresa');
                if(intval($empresa)){
                    $data = new Cliente();
                    $empresas = $data->where(['id' => $empresa])->asObject()->first();
                    // if( empty($empresas) ){
                    //     return json_encode(['validation' => true, 'empresa' => $empresa]);
                    // }
                    $empresas->password = null;
                    $muestraM = new Muestreo();
                    $muestra = $muestraM->select('mue_subtitulo, mue_fecha_muestreo as fecha')->where(['id_muestreo' => $this->request->getPost('id_muestra')])->asObject()->first();
                    if(!empty($muestra)){
                        $empresas->fecha = date('Y-m-d', strtotime($muestra->fecha));
                        $empresas->hora = date('H:i:s', strtotime($muestra->fecha));
                        $empresas->sucursal = $muestra->mue_subtitulo;
                    }
    
                    $return = $empresas;
                }else{
                    return $this->respond(['validation' => true, 'empresa' => $empresa], 200);
                    // return json_encode(['validation' => true, 'empresa' => $empresa]);
                }
                break;
        }
        $empresa_nueva = $this->request->getPost('empresa_nueva');
        switch($empresa_nueva){
            case '0':
                $rules['frm_nit'] = 'required|max_length[12]|is_unique[usuario.id]';
                $rules['username']  = 'required|max_length[30]|is_unique[usuario.username]';
                $rules['frm_correo']     = 'required|valid_email|is_unique[usuario.email]|max_length[100]';
                if ($this->validate($rules, $message)){
                    $form = $this->request->getPost();
                    $data = [
                        'id' => $form['frm_nit'],
                        'name' => $form['frm_nombre_empresa'],
                        'username' => $form['username'],
                        'email' => $form['frm_correo'],
                        'password' => md5( $form['frm_nit'] ),
                        'usertype' => 'Registered',
                        'block' => 1,
                        'registerDate' => date('Y-m-d H:i:s'),
                        'lastvisitDate' => date('Y-m-d H:i:s'),
                        'use_cargo' => $form['frm_contacto_cargo'],
                        'use_nombre_encargado' => $form['frm_contacto_nombre'],
                        'use_telefono' => $form['frm_telefono'],
                        'use_fax' => $form['frm_fax'],
                        'use_direccion' => $form['frm_direccion'],
                        'pyme' => 'No'
                    ];
                    $cliente = new Cliente();
                    $cliente->insert($data);
                    $cliente = $cliente->where(['id' => $form['frm_nit']])->get()->getResult();
                    $return = [
                        'id' => $form['frm_nit'],
                        'procedencia' => 0,
                        'success' => 'Empresa creada con exito'
                    ];
                }else {
                    $return = $validation->getErrors();
                }
                break;
            case '1':
                $id = $this->request->getPost('frm_nombre_empresa2');
                $rules['frm_nit']       = 'required|max_length[12]|is_unique[usuario.id, id, '.$id.']';
                $rules['frm_nombre_empresa2']       = 'required|max_length[100]|is_unique[usuario.id, id, '.$id.']';
                $rules['frm_nombre_empresa']   = 'required|max_length[100]|is_unique[usuario.name, id, '.$id.']';
                $rules['frm_correo']     = 'max_length[100]|required|is_unique[usuario.email, id, '.$id.']';
                if ($this->validate($rules, $message)){
                    $data = [
                        // 'name' => $this->request->getPost('frm_nombre_empresa'),
                        'email' => $this->request->getPost('frm_correo'),
                        'use_cargo' => $this->request->getPost('frm_contacto_cargo'),
                        'use_nombre_encargado' => $this->request->getPost('frm_contacto_nombre'),
                        'use_telefono' => $this->request->getPost('frm_telefono'),
                        'use_fax' => $this->request->getPost('frm_fax'),
                        'use_direccion' => $this->request->getPost('frm_direccion'),
                    ];
                    $cliente = new Cliente();
                    $cliente
                        ->set($data)
                        ->where(['id' => $id])
                        ->update();
                    $cliente = $cliente->where(['id' => $id])->get()->getResult();
                    $return = [
                        'cliente' => $cliente,
                        'procedencia' => 1,
                        'success' => 'Empresa actualizada con exito'
                    ];
                } else {
                    $return = $validation->getErrors();
                }
                break;
        }
        
        return $this->respond($return, 200);
        // return json_encode();
    }

    public function remision_muestra(){
        $buscar = $this->request->getPost('buscar');
        try {
            switch($buscar){
                case 1:
                    $producto = $this->request->getPost('frm_producto');
                    $tabla = new Producto();
                    $productos = $tabla->like('pro_nombre', $producto)->select('pro_nombre')->get()->getResult();
                    $data = [];
                    foreach($productos as $key => $producto){
                        $data[$producto->pro_nombre] = null;
                    }
                    $return = $data;
                    break;
                case 2:
                    $producto = $this->request->getPost('id_producto');
                    $return = muestra_tabla($producto);
                    break;
                case 3:
                    $forms = $this->request->getPost();
                    $return = detalles_tabla($forms);
                    break;
                case 4:
                    $forms = $this->request->getPost();
                    $accion = $this->request->getPost('accion');
                    $return = guardar_remision($forms, $accion);
                    break;
                case 5:
                    $certificado = $this->request->getPost('id_certificacion');
                    $return = delete_detail_list($certificado);
                    break;
                default:
                    return var_dump($this->request->getPost());
                    break;
            }
            return $this->respond($return);
        } catch (\Exception $e) {
            return $this->respond([$e->getMessage()]);
        }
    }

    public function remision_ticket($id_certificacion){
        $certificado = new Certificacion();
        $certificado = $certificado->where(['id_certificacion' => $id_certificacion])->get()->getResult();
        $muestreo = new Muestreo();
        $muestreo = $muestreo->where(['id_muestreo' => $certificado[0]->id_muestreo])->get()->getResult();
        $muestreo_detalle = new MuestreoDetalle();
        $muestreo_detalle = $muestreo_detalle->where(['id_muestra_detalle' => $certificado[0]->id_muestreo_detalle])->get()->getResult();
        $muestreo_tipo =   procesar_registro_fetch('muestra_tipo_analisis', 'id_muestra_tipo_analsis',$muestreo_detalle[0]->id_tipo_analisis);
        $producto = new Producto();
        $producto = $producto->where(['id_producto' => $muestreo_detalle[0]->id_producto])->get()->getResult();
        $norma =   procesar_registro_fetch('norma', 'id_norma',$producto[0]->id_norma);
        $aux_codigo =   construye_codigo_amc($muestreo_detalle[0]->id_muestra_detalle);
        $ensayo = new Ensayo();
        $ensayo = $ensayo->where(['id_producto' => $muestreo_detalle[0]->id_producto])->get()->getResult();
        $data = [
            'certificado'       => $certificado[0],
            'muestreo'          => $muestreo[0],
            'muestreo_detalle'  => $muestreo_detalle[0],
            'muestreo_tipo'     => $muestreo_tipo[0],
            'producto'          => $producto[0],
            'norma'             => $norma[0],
            'codigo'            => $aux_codigo,
            'ensayos'           => $ensayo
        ];
        // return var_dump($data);
        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'Letter',
        ]);
        $html = view('views_mpdf/ticket_2',$data);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $name = 'muestras_analisis_'.$certificado[0]->id_certificacion.'.pdf';
        $mpdf->Output($name,'D');
    }


    // ------------------------------------- EDITAR REMISIONES
    public function remision_edit(){
        $analisis = new Analisis();
        $analisis = $analisis->get()->getResult(); 
        $tabla = new Producto();
        $productos = $tabla->select(['id_producto','pro_nombre'])->orderBy('pro_nombre', 'ASC')->get()->getResult();
        $certificados = new Certificacion();
        $certificados = $certificados->select('certificado_nro')->orderBy('certificado_nro', 'DESC')->limit(2000)->get()->getResult();
        foreach ($certificados as $key => $certificado) {
            $data[$certificado->certificado_nro] = null;
        }
        $ultimo = count($certificados) < 1999 ? $certificados[count($certificados) -1 ]->certificado_nro : $certificados[1999]->certificado_nro;
        $primero = $certificados[0]->certificado_nro;
        $diluciones  = procesar_registro_fetch('diluciones', 0, 0);
        return view('funcionarios/remision_edit', [
            'certificados'  => $data,
            'ultimo'        => $ultimo,
            'primero'       => $primero,
            'analisis'      => $analisis,
            'diluciones'    => $diluciones,
            'productos'     => $productos
        ]);
    }
    public function remision_edit_muestra(){
        $db = \Config\Database::connect();
        $buscar = $this->request->getPost('buscar');
        switch($buscar){
            case 0:
                $certificado = $this->request->getPost('frm_certificados_editar');
                $muestra_id = $this->request->getPost('frm_muestra_editar');
                $anio = $this->request->getPost('frm_muestra_editar_anio');
                if(!empty($certificado)){
                    $certificado = procesar_registro_fetch('certificacion', 'certificado_nro', $certificado);
                }else{
                    $muestra = procesar_registro_fetch('muestreo_detalle', 'id_codigo_amc', $muestra_id, 'ano_codigo_amc', $anio);
                    $certificado = procesar_registro_fetch('certificacion', 'id_muestreo_detalle', $muestra[0]->id_muestra_detalle);
                }
                if(empty($certificado[0]))
                    $return = ['result' => false];
                else{
                    $certificado = $certificado[0];
                    $muestra = procesar_registro_fetch('muestreo', 'id_muestreo', $certificado->id_muestreo);
                    $cliente = procesar_registro_fetch('usuario', 'id', $muestra[0]->id_cliente);
                    // return json_encode($cliente);
                    $cliente[0]->sucursal = $muestra[0]->mue_subtitulo;
                    $cliente[0]->fecha = date('Y-m-d', strtotime($muestra[0]->mue_fecha_muestreo));
                    $cliente[0]->hora = date('H:i:s', strtotime($muestra[0]->mue_fecha_muestreo));
                    $tabla = imprime_detalle_muestras($muestra[0]->id_muestreo, 1);
                    // $conceptos = mensaje_resultado($certificado->certificado_nro);
                    $return = [
                        'result'            =>  true,
                        'certificado'       =>  $certificado,
                        'muestra'           =>  $muestra[0],
                        'cliente'           =>  $cliente[0],
                        'tabla'             =>  $tabla,
                        // 'conceptos'         =>  $conceptos,
                    ];
                }
                break;
            case 1:
                $id_cliente = $this->request->getPost('frm_nit');
                $id_muestreo = $this->request->getPost('frm_id_muestra');
                $mue_subtitulo = $this->request->getPost('frm_nombre_empresa_subtitulo');
                $mue_fecha_muestreo = $this->request->getPost('frm_fecha_muestra').' '.$this->request->getPost('frm_hora_muestra'); 
                $mue_subtitulo = $this->request->getPost('frm_nombre_empresa_subtitulo');
                $mue_fecha_muestreo = $this->request->getPost('frm_fecha_muestra').' '.$this->request->getPost('frm_hora_muestra'); 
                if(empty($id_cliente)){
                    $return = ['vacio' => true, 'mensaje' => 'No se a seleccionado la empresa.'];
                }
                if(empty($id_muestreo)){
                    $return = ['vacio' => true, 'mensaje' => 'No se ha seleccionado el certificado'];
                }
                $muestra = new Muestreo();
                $muestra->set([
                    'id_cliente' => $id_cliente,
                    'mue_subtitulo' => $mue_subtitulo,
                    'mue_fecha_muestreo' => $mue_fecha_muestreo
                ])
                    ->where(['id_muestreo' => $id_muestreo])
                    ->update();
                $cliente = new Cliente();
                $data = [
                    'use_cargo'             => $this->request->getPost('frm_contacto_cargo'),
                    'use_nombre_encargado'  => $this->request->getPost('frm_contacto_nombre'),
                    'use_telefono'          => $this->request->getPost('frm_telefono'),
                    'use_fax'               => $this->request->getPost('frm_fax'),
                    'use_direccion'         => $this->request->getPost('frm_direccion'),
                ];
                $cliente->set($data)->where(['id' => $id_cliente])->update();
                $return = ['success' => true];
                break;
            case 2:
                $idMuestraDetalle = $this->request->getPost('id_muestra_detalle');
                $detalle = procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $idMuestraDetalle);
                $detalle[0]->fechas = procesar_registro_fetch('fecha_vida_util', 'id_detalle_muestreo', $detalle[0]->id_muestra_detalle);
                $producto = procesar_registro_fetch('producto', 'id_producto', $detalle[0]->id_producto);
                $tabla = muestra_tabla($producto[0]->id_producto, $idMuestraDetalle);
                if(!empty($producto[0]))
                    $producto = $producto[0];
                else
                    $producto = (object)['id_producto' => ''];
                $return =  [
                    'detalle'   => $detalle[0],
                    'producto'  => $producto,
                    'tabla'     => $tabla,
                ];
                break;
            case 3:
                $forms = $this->request->getPost();
                $idMuestraDetalle = $this->request->getPost('id_muestra_detalle');
                $data = detalles_tabla($forms, $idMuestraDetalle);
                $return = $data;
                break;
            case 4:
                $producto = $this->request->getPost('id_producto');
                $idMuestraDetalle = $this->request->getPost('id_muestra_detalle');
                $data = muestra_tabla($producto, $idMuestraDetalle);
                $return = $data;
                break;
            case 5:
                $data = [
                    'id_mensaje_resultado' => $this->request->getPost('frm_mensaje_resultado'),
                    'id_mensaje_comentario' => $this->request->getPost('frm_mensaje_observacion'),
                ];
                $id = $this->request->getPost('frm_id_certificado');
                $db->table('certificacion_vs_mensaje')->set($data)->where(['id_certificacion' => $id])->update();
                $return = 'Mensaje actualizado.';
                break;
            default:
                return 'var_dump($this->request->getPost())';
                break;
        }
        return $this->respond($return);
    }
}