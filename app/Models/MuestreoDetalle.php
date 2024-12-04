<?php


namespace App\Models;


use CodeIgniter\Model;

class MuestreoDetalle extends Model
{
    protected $table            = 'muestreo_detalle';
    protected $primaryKey       = 'id_muestra_detalle';
    protected $allowedFields    = ['id_muestra_detalle', 'id_tipo_analisis', 'id_producto', 'ano_codigo_amc', 'id_codigo_amc', 'mue_adicional', 'mue_procedencia', 'mue_identificacion', 'mue_lote', 'mue_fecha_produccion', 'mue_fecha_vencimiento', 'mue_temperatura_muestreo', 'mue_temperatura_laboratorio', 'mue_condiciones_recibe', 'mue_cantidad', 'mue_momento_muestreo', 'mue_parametro', 'mue_area', 'mue_tipo_muestreo', 'mue_unidad_medida', 'mue_dilucion', 'mue_empaque', 'duplicado'];

}