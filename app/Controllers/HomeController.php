<?php

namespace App\Controllers;

use App\Models\Certificacion;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Muestreo;
use Config\Services;



class HomeController extends BaseController
{
	
	public function index()
	{
		if ( session('user')->funcionario ){
			return  view('pages/home');
		}
		$id = session('user')->id;
		for ($i=0;$i<= 11;$i++){ 
	       	$date_init = date('Y-n-01', mktime(0, 0, 0, (date("n")-$i), 1, date("Y") ) );
	    }
	    $date_finish = date('Y-m-t');
		$muestreo = new Muestreo();
		$pendientes = $muestreo
			->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
			->where(['id_cliente' => $id, 'cer_fecha_publicacion' => NULL ])
			->countAllResults();

		$recientes = $muestreo
			->select('
					muestreo.id_muestreo,
					certificacion.certificado_nro,
					certificacion.cer_fecha_publicacion as fecha_publicacion,
					muestreo_detalle.mue_lote as lote,
					muestreo_detalle.mue_identificacion as producto
				')
			->join('certificacion', 'muestreo.id_muestreo = certificacion.id_muestreo')
			->join('muestreo_detalle', 'certificacion.id_muestreo_detalle = muestreo_detalle.id_muestra_detalle')
			->where(['id_cliente' => $id, 'cer_fecha_publicacion !=' => NULL])
			->orderBy('certificado_nro', 'DESC')
			->limit(5, 0)
			->get()->getResult();
		$solicitudes = $muestreo
			->where(['id_cliente' => $id ])
			->countAllResults();

		$mes = $muestreo
			->where(['id_cliente' => $id, 'mue_fecha_recepcion >= ' => date('Y-n-01'), 'mue_fecha_recepcion <= ' => date('Y-n-t')])
			->countAllResults();

		$historial = $muestreo
			->where(['id_cliente' => $id, 'mue_fecha_recepcion >= ' => $date_init, 'mue_fecha_recepcion <= ' => $date_finish])
			->get()->getResult();

		$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		$contador = 0;
		$array_h = [];
		while(strtotime($date_init) <= strtotime($date_finish)){
			foreach($historial as $value){
				if($value->mue_fecha_recepcion >= $date_init.' 00:00:00' && $value->mue_fecha_recepcion <= date('Y-m-t', strtotime($date_init)).' 23:59:59'){
					$contador++;
				}
			}
			$fecha['total'] = $contador;
			$fecha['mes'] = date("Y", strtotime($date_init)).' - '.$meses[(date("m", strtotime($date_init))-1)];
			$contador = 0;
			array_push($array_h, $fecha);
			$date_init = date('Y-m-01', strtotime($date_init.' +1 month'));
		}

		return  view('pages/home',[
			'ensayos_r' 	=> $recientes,
			'solicitudes' 	=> $solicitudes,
			'historial' 	=> $array_h,
			'total_mes'		=> $mes,
			'pendientes'	=> $pendientes
		]);
	}

	public function about()
    {
        return view('pages/about');
    }
    
}
