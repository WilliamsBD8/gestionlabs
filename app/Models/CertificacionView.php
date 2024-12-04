<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificacionView extends Model
{
  
  public function __construct(){
    $this->setTable("view_certificados".session('user')->id);
  }

  protected $table = 'certificacion';
  
} 