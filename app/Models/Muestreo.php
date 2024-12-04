<?php


namespace App\Models;


use CodeIgniter\Model;

class Muestreo extends Model
{
    protected $table        = 'muestreo';
    protected $primaryKey   = 'id';
    protected $allowedFields    = ['id_muestreo', 'id_cliente', 'mue_estado', 'mue_fecha_muestreo', 'mue_fecha_recepcion', 'mue_fecha_analisis', 'mue_fecha_informe', 'mue_entrega_muestra', 'mue_recibe_muestra', 'mue_responsable_op', 'mue_observaciones', 'mue_fecha_preinforme', 'mue_subtitulo'];

}