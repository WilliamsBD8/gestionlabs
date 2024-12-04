<?php


namespace App\Models;


use CodeIgniter\Model;

class Cliente extends Model
{
    protected $table            = 'usuario';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'name',
        'username',
        'email',
        'password',
        'usertype',
        'block',
        'registerDate',
        'lastvisitDate',
        'use_cargo',
        'use_nombre_encargado',
        'use_telefono',
        'use_fax',
        'use_direccion',
        'pyme',
        ];

}