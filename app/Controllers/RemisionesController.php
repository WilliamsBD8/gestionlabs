<?php

namespace App\Controllers;

use App\Models\Certificacion;
use App\Models\MuestreoDetalle;
use App\Models\Producto;

use Config\Services;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class RemisionesController extends BaseController
{
	use ResponseTrait;
	private $dataTable;
	private $id_muestreo;

	public function __construct(){
		$this->dataTable = (object) [
				'draw'      => $_GET['draw'] ?? 1,
				'length'    => $length = $_GET['length'] ?? 10,
				'start'     => $start = $_GET['start'] ?? 1,
				'page'      => $_GET['page'] ?? ceil(($start - 1) / $length + 1)
		];
}

	public function index()
	{
		//
	}

	public function detail($id_muestreo, $action){
	    try{
    		$c_model = new Certificacion();
    		$certificados = $c_model
    			->select([
    				'certificacion.id_muestreo_detalle',
    				'certificacion.id_muestreo',
    				'certificacion.certificado_nro',
    				'certificacion.id_certificacion',
    				'muestra_tipo_analisis.mue_nombre',
    				'producto.pro_nombre',
    				'producto.id_producto',
    				'muestreo_detalle.mue_identificacion',
    				'muestreo_detalle.mue_cantidad',
    				'muestreo_detalle.mue_unidad_medida',
    				'muestra_tipo_analisis.mue_sigla',
    				'muestreo_detalle.id_codigo_amc'
    			])
    			->where(['certificacion.id_muestreo' => $id_muestreo])
    			->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle', 'left')
    			->join('producto', 'muestreo_detalle.id_producto = producto.id_producto', 'left')
    			->join('muestra_tipo_analisis', 'muestra_tipo_analisis.id_muestra_tipo_analsis = muestreo_detalle.id_tipo_analisis', 'left')
    		->get()->getResult();
    		if(!empty($certificados)){
    			$cer = true;
    			foreach ($certificados as $key => $certificado) {
    				$certificado->key 				= $key;
    				$certificado->codigo_amc 	= construye_codigo_amc($certificado->id_muestreo_detalle);
    				if($action == 'edit'){
    					$detalle 									= $c_model->getDetalle($certificado->id_muestreo_detalle);
    					$certificado->detalle			= count($detalle) == 1 ? $detalle[0] : (object)[];
    					$certificado->fechas 			= $c_model->getFechas($certificado->id_muestreo_detalle);
    					$certificado->ensayos 		= $c_model->getEnsayos($certificado->id_producto);
    					$certificado->producto 		= $c_model->getProducto($certificado->id_producto);
    					foreach ($certificado->ensayos as $key => $ensayo) {
    						$filaChecked = procesar_registro_fetch('ensayo_vs_muestra', 'id_muestra', $certificado->id_muestreo_detalle, 'id_ensayo', $ensayo->id_ensayo);
    						$ensayo->is_checked = isset($filaChecked[0]->id_ensayo_vs_muestra) ? 'checked' : '';
    					}
    				}
    			}
    		}else {
    			$cer = false;
    			$certificados = [
    				(object)[
    					'key'									=> '1',
    					'certificado_nro' 		=> '@Informe',
    					'mue_nombre' 					=> '@Tipo',
    					'codigo_amc' 					=> '@C&oacute;digo',
    					'pro_nombre' 					=> '@Norma',
    					'mue_identificacion' 	=> '@Identificaci&oacute;n',
    					'mue_cantidad' 				=> '@Cantidad',
    					'mue_unidad_medida'		=> '@Unidad'
    				]
    			];
    		}
    			
    		return $this->respond([
    			'table' => $certificados,
    			'data' => $this->dataTable,
    			'draw' => $this->dataTable->draw,
    
    			'recordsTotal' 		=> count($certificados),
    			'recordsFiltered' => count($certificados),
    			'cer'							=> $cer
    		], 200);
	    } catch (\Exception $e) {
            return $this->respond([$e->getMessage()]);
        }
	}

	public function muestra_product($id_producto, $action){
		$data = new Producto();
		$producto = $data
			->join('norma', 'producto.id_norma = norma.id_norma')
			->where(['id_producto' => $id_producto])->asObject()->first();
		if(empty($producto)){
			return $this->respond(['message' => 'Producto no encontrado'], 404);
		}
		$p_model = new Producto();
		$ensayos = $p_model
			->select([
				'ensayo.*',
				'parametro.*'
			])
			->join('ensayo', 'ensayo.id_producto = producto.id_producto', 'left')
			->join('parametro', 'parametro.id_parametro = ensayo.id_parametro', 'left')
			// ->join('ensayo_vs_muestra','', 'left')
			->where(['producto.id_producto' => $id_producto, 'parametro.par_estado' => 'Activo'])->get()->getResult();
		$data = [
			'producto' 	=> $producto,
			'ensayos'		=> $ensayos
		];
		if($action != 'created'){
			foreach ($ensayos as $key => $ensayo) {
				$filaChecked = procesar_registro_fetch('ensayo_vs_muestra', 'id_muestra', $action, 'id_ensayo', $ensayo->id_ensayo);
				$ensayo->is_checked = isset($filaChecked[0]->id_ensayo_vs_muestra) ? 'checked' : '';
			}
			// $muestreoDetalle 					= procesar_registro_fetch('muestreo_detalle', 'id_muestra_detalle', $action);
			// $certificacion 						= procesar_registro_fetch('certificacion', 'id_muestreo_detalle', $muestreoDetalle[0]->id_muestra_detalle);
			$data['ensayos'] 					= $ensayos;
			// $data['muestreo_detalle'] = $muestreoDetalle[0];
			// $data['certificado'] 			= $certificacion[0];
		}
		return $this->respond($data);
	}
}
