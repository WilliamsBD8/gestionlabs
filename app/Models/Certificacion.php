<?php

namespace App\Models;

use CodeIgniter\Model;

class Certificacion extends Model
{
    protected $table = 'certificacion';
    protected $primaryKey = 'id_certificacion';
    protected $allowedFields    = [
        'id_certificacion',
        'id_muestreo',
        'id_muestreo_detalle',
        'certificado_nro',
        'certificado_estado',
        'clave_documento_pre',
        'clave_documento_final',
        'cer_fecha_analisis',
        'cer_fecha_preinforme',
        'cer_fecha_informe',
        'cer_fecha_publicacion',
        'cer_usuario_publica',
        'id_mensaje',
        'cer_fecha_facturacion',
        'id_tipo_analisis_primer_informe',
        'id_tipo_analisis_informe_final',
        'conformidad',
        'doc_primer_informe',
        'doc_informe_final',
        'status_email'
    ];
    public function getDetalle($id){
        $detalle = $this->builder('muestreo_detalle')->where(["id_muestra_detalle" => $id])->get()->getResult();
        return $detalle;
    }

    public function getFechas($id){
        $fechas = $this->builder('fecha_vida_util')->where(["id_detalle_muestreo" => $id])->get()->getResult();
        return $fechas;
    }

    public function getEnsayos($id_producto){
        $ensayos = $this->builder('producto')->select([
            'ensayo.*',
            'parametro.*'
        ])
        ->join('ensayo', 'ensayo.id_producto = producto.id_producto', 'left')
        ->join('parametro', 'parametro.id_parametro = ensayo.id_parametro', 'left')
        ->where(['producto.id_producto' => $id_producto, 'parametro.par_estado' => 'Activo'])->get()->getResult();
        return $ensayos;
    }

    public function getProducto($id_producto){
        $producto = $this->builder('producto')
            ->join('norma', 'producto.id_norma = norma.id_norma', 'left')->where(['id_producto' => $id_producto])->get()->getResult();
        return $producto[0];
    }
} 