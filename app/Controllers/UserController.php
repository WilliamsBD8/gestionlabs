<?php


namespace App\Controllers;


use App\Models\Funcionario; // Tabla cms_user - Funcionario
use App\Models\Cliente; // Tabla usuario - Empresa
use Config\Services;


class UserController extends BaseController
{
    public function perfile()
    {
        $validation = Services::validation();
        // $user = new User();
        // $data = $user->select('*, roles.name as role_name, users.name as name')
        //     ->join('roles', 'users.role_id = roles.id')
        //     ->where('users.id', session('user')->id)
        //     ->get()->getResult()[0];
        $data = session('user');

        return view('users/perfile',[ 'data' => $data, 'validation' => $validation]);
    }

    public function updateUser()
    {
        $data = [];
        $id = session('user')->id;
        if(session('user')->funcionario){
            $data = [
                    'name'              => 'required|max_length[45]',
                    'username'          => 'required|max_length[40]|is_unique[cms_users.usr_usuario, id, '.$id.']',
                    'email'             => 'required|valid_email|max_length[100]|is_unique[cms_users.usr_correo, id, '.$id.']',
                    'password'          => 'required|min_length[8]'
                ];
        }else{
            $data = [
                    'name'              => 'required|max_length[45]',
                    'username'          => 'required|max_length[40]|is_unique[usuario.username, id, '.$id.']',
                    'email'             => 'required|valid_email|max_length[100]|is_unique[usuario.email, id, '.$id.']',
                ];
        }
        $massage = [
            'name' => [
                'required'      => 'El campo Nombres y Apellidos es obligatorio.',
                'max_length'    => 'El campo Nombres Y Apellidos no debe tener mas de 45 caracteres.'
            ],
            'username' => [
                'required'      => 'El campo Nombre de Usuario es obligatorio',
                'max_length'    => 'El campo Nombre de Usuario no puede superar mas de 20 caracteres.',
                'is_unique'     => 'El usuario ya se encuentra registrado.'
            ],
            'email' => [
                'required'      => 'El campo Correo Electronico es obligatorio.',
                'is_unique'     => 'El correo ya se encuentra registrado.'
            ]

        ];
        if(session('user')->funcionario){
            $massage['password'] = [
                'required'      => 'El campo Contraseña es obligatorio.',
                'min_length'    => 'El campo Contraseña debe tener minimo 8 caracteres.'
            ];
        }
        if ($this->validate($data, $massage)) {
            
            if(session('user')->funcionario){
                
                $data = [
                    'nombre'        => $this->request->getPost('name'),
                    'usr_usuario'   => $this->request->getPost('username'),
                    'usr_correo'    => $this->request->getPost('email'),
                    'phone'         => $this->request->getPost('phone'),
                    'direction'     => $this->request->getPost('direction'),
                    'id'            => session('user')->id,
                ];
                
                $password_post = $this->request->getPost('password');
                $password = session('user')->usr_clave;
                if ($password != $password_post)
                    $data['usr_clave'] = $password_post;
    
                $user = new Funcionario();
                $user->set($data)->where(['id' => session('user')->id])->update();
                session('user')->nombre = $data['nombre'];
                session('user')->usr_usuario = $data['usr_usuario'];
                session('user')->usr_correo = $data['usr_correo'];
                if ($password != $password_post)
                    session('user')->usr_clave = $data['usr_clave'];
                
            }else{
                $data = [
                    'name'          => $this->request->getPost('name'),
                    'username'      => $this->request->getPost('username'),
                    'email'         => $this->request->getPost('email'),
                    'phone'         => $this->request->getPost('phone'),
                    'direction'     => $this->request->getPost('direction'),
                    'id'            => session('user')->id,
                ];
    
                $user = new Cliente();
                $user->set($data)->where(['id' => session('user')->id])->update();
                
                session('user')->name = $data['name'];
                session('user')->username = $data['username'];
                session('user')->email = $data['email'];
                
            }
            return redirect()->back()->with('success', 'Datos guardado correctamente.');
        } else {
            return redirect()->back()->withInput();
        }
    }


    public function updatePhoto()
    {
        $user = new Funcionario();
        $newName = '';
        $img = $this->request->getFile('photo');
        if($img->getSize() > 0){
            $newName = $img->getRandomName();
            $img->move('upload/images', $newName);
        }
        $user->set(['usr_foto' => $newName])->where(['id' => session('user')->id])->update();
        session('user')->usr_foto = $newName;
        return redirect()->back()->with('success', 'Foto guardada correctamente.');
    }
}