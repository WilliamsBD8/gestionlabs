<?php


namespace App\Models;


use CodeIgniter\Model;

class FechaVidaUtil extends Model
{
    protected $table            = 'fecha_vida_util';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
      'id',
      'fecha',
      'dia',
      'id_detalle_muestreo',
      'type'
  ];
}