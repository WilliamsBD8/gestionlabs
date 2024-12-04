<?php


namespace App\Models;


use CodeIgniter\Model;

class Emails extends Model
{
    protected $table            = 'reference_emails';
    protected $primaryKey       = 'id';

    protected $allowedFields    = ['email'];

}