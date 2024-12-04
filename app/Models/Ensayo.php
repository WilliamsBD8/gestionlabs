<?php


namespace App\Models;


use CodeIgniter\Model;

class Ensayo extends Model
{
    protected $table            = 'ensayo';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'username', 'email', 'password', 'status', 'role_id', 'photo', 'id'];

}