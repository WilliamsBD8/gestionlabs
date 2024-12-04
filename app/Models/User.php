<?php


namespace App\Models;


use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'cms_users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['usr_usuario', 'usr_clave', 'usr_correo', 'usr_estado', 'usr_rol', 'nombre', 'cargo', 'firma'];

}