<?php

use App\Models\MenuCliente;
use App\Models\MenuFuncionarios;
use App\Models\PermissionCliente;
use App\Models\PermissionFuncionarios;


function menu($type = null)
{
    if (session('user')->usr_usuario){
        $menu = new MenuFuncionarios();
        $permission = new PermissionFuncionarios();
        $aux_menu = 'menus_funcionarios';
        $aux_perm = 'permissions_funcionarios';
    }
    else{
        $menu = new MenuCliente();
        $permission = new PermissionCliente();
        $aux_menu = 'menus_cliente';
        $aux_perm = 'permissions_cliente';
    }

    if (session('user')->usertype == 'Administrador' || session('user')->usr_rol == 1) {
        if(!empty($type) && session('user')->usr_usuario)
            $menu->where(['type_menu' => $type]);
        $data = $menu->where(['type' => 'primario', 'status' => 'active'])
            ->orderBy('position', 'ASC')
            ->get()
            ->getResult();
    } else {
        $permission->select($aux_menu.'.*');
        if(session('user')->usr_usuario)
            $permission->where('usr_rol', session()->get('user')->usr_rol);
        else
            $permission->where('typeUser', session()->get('user')->usertype);
        if(!empty($type) && session('user')->usr_usuario)
            $permission->where($aux_menu.'.type_menu', $type);
        $data = $permission->where($aux_menu.'.type', 'primario')
            ->join($aux_menu, $aux_menu.'.id = '.$aux_perm.'.menu_id')
            ->orderBy('position', 'ASC')
            // ->join('roles', 'roles.id = permissions.role_id')
            ->get()->getResult();
    }
    return $data;
}

function submenu($refences, $type = null)
{
    if (session('user')->usr_usuario){
        $menu = new MenuFuncionarios();
        $menu->where(['type_menu' => $type]);
        $permission = new PermissionFuncionarios();
        $aux_menu = 'menus_funcionarios';
        $aux_perm = 'permissions_funcionarios';
    }
    else{
        $menu = new MenuCliente();
        $permission = new PermissionCliente();
        $aux_menu = 'menus_cliente';
        $aux_perm = 'permissions_cliente';
    }

    if (session('user')->usertype == 'Administrador' || session('user')->usr_rol == 1) {
        $data = $menu->where(['type' => 'secundario', 'status' => 'active', 'references' => $refences])
            ->orderBy('position', 'ASC')
            ->get()
            ->getResult();
    } else {
        $permission->select($aux_menu.'.*');
        if(session('user')->usr_usuario)
            $permission->where('usr_rol', session()->get('user')->usr_rol);
        else
            $permission->where('typeUser', session()->get('user')->usertype);
        if(!empty($type))
            $permission->where(['type_menu' => $type]);
        $data = $permission
            ->where($aux_menu.'.type', 'secundario')
            ->where($aux_menu.'.references', $refences)
            ->orderBy('position', 'ASC')
            ->join($aux_menu, $aux_menu.'.id = '.$aux_perm.'.menu_id')
            ->get()
            ->getResult();
    }
    return $data;
}

function countMenu($references)
{
    if (session('user')->usr_usuario) $menu = new MenuFuncionarios();
    else $menu = new MenuCliente();
    $data = $menu->where(['type' => 'secundario', 'status' => 'active', 'references' => $references])
        ->get()
        ->getResult();
    if (count($data) > 0) {
        return true;
    }
    return false;
}

function urlOption($references = null)
{
    if ($references) {
        if (session('user')->usr_usuario) $menu = new MenuFuncionarios();
        else $menu = new MenuCliente();
        $data = $menu->find($references);
        if ($data['component'] == 'table') {
            return base_url().'/table/' . $data['url'];
        } else if ($data['component'] == 'controller') {
            if(session('user')->funcionario)
                return base_url(['funcionario']).'/' . $data['url'];
            else
                return base_url(['cliente']).'/' . $data['url'];
        }
    } else {
        return 'JavaScript:void(0)';
    }

}

function isActive($data)
{
    if(base_url(uri_string()) == base_url($data)) {
        return 'active';
    }
}