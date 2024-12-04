<?php

use App\Models\NotificationCliente;
use App\Models\NotificationFuncionario;

function notification()
{
    if(session('user')->usr_usuario)
        $notification = new NotificationFuncionario();
    else
        $notification = new NotificationCliente();
    $data =  $notification->findAll();
    return $data;
}

function countNotification()
{
    if(session('user')->usr_usuario)
        $notification = new NotificationFuncionario();
    else
        $notification = new NotificationCliente();
    $data =  $notification->findAll();
    return  count($data);
}