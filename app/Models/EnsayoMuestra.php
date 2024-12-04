<?php

namespace App\Models;

use CodeIgniter\Model;

class EnsayoMuestra extends Model
{
	protected $table                = 'ensayo_vs_muestra';
	protected $primaryKey           = 'id_ensayo_vs_muestra';
	protected $allowedFields        = ['confirmacion_a', 'confirmacion_b', 'confirmacion_c'];
}
