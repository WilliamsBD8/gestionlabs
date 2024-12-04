<?php


namespace App\Controllers;


use App\Models\Cliente;
use App\Models\Funcionario;
use Config\Services;


class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function validation()
    {

        $errors = $this->validate([
            'username' => 'required|min_length[1]',
            'password' => 'required|max_length[20]'
        ]);
        if ($errors) {

            $username  = $this->request->getPost('username');
            $password  = $this->request->getPost('password');
            $rol       = $this->request->getPost('rol');

            if($rol == 1){
                $user = new Funcionario();
                $result = $user
                ->select('cms_users.*,
                        cms_rol.nombre as cms_rol
                    ')
                ->join('cms_rol', 'cms_rol.usr_rol = cms_users.usr_rol')
                ->where(['usr_usuario' => $username])->get()->getResult();
            }else{
                $user = new Cliente();
                $result = $user->where(['username' => $username])->get()->getResult();
            }
            if(!empty($result[0])){
                if($rol == 1){
                    if($result[0]->usr_clave != $password)
                        return json_encode(['errors' => 'Las credenciales no concuerdan.']);
                    else if($result[0]->usr_estado !== 'ACTIVO')
                        return json_encode(['errors' => 'La cuenta no se encuentra activa.']);
                }else{
                    if(md5($password) != $result[0]->password)
                        return json_encode(['errors' => 'Las credenciales no concuerdan.']);
                }
                $session = session();
                $result[0]->funcionario = $rol == 1 ? true : false;
                $session->set('user', $result[0]);
                session('user')->session_date = date('Y-m-d H:i:s');
                return json_encode(['login' => base_url().'/GestionLabs/home']);
            }
            return json_encode(['errors' => 'Las credenciales no concuerdan.']);
        } else {
            return json_encode(['errors' => 'Las credenciales no concuerdan.']);
        }

    }

    public function resetPassword()
    {
        return view('auth/reset_password');
    }

    public function forgotPassword()
    {
        $request = Services::request();
        $user = new Funcionario();
        $data = $user->where('usr_correo', $request->getPost('email'))->get()->getResult();
        // return var_dump($data);
        if (empty($data[0])) {
            $user = new Cliente();
            $data = $user->where('username', $request->getPost('email'))->get()->getResult();
            if (!isset($data[0])) {
                return redirect()->to(base_url().'/reset_password')
                    ->with('danger', 'Las credenciales no coinciden con los datos ingresados.');
            }else{
                // Usuario cliente
                $email = new EmailController();
                $password = $this->encript();
                $user->set(['password' => md5($password)]);
                $user->where('id', $data[0]->id);
                $user->update();
                $email->send('wabox324@gmail.com', 'wabox', $request->getPost('email'), 'Recuperacion de contraseña', password($password));
                return redirect()->to('/reset_password')
                    ->with('success', 'Valida el correo te enviamos una nueva contraseña');
            }
        } else{
            // Usuario Funcionario
            $email = new EmailController();
            $password = $this->encript();
            $user->set(['usr_clave' => $password ]);
            $user->where('id', $data[0]->id);
            $user->update();
            $email->send('wabox324@gmail.com', 'wabox', $request->getPost('email'), 'Recuperacion de contraseña', password($password));
            // var_dump($estado);
            return redirect()->to('/reset_password')
                ->with('success', 'Valida el correo te enviamos una nueva contraseña');
        }
        // if (count($data) > 0) {
        // } else {
        //     return redirect()->to(base_url().'/reset_password')
        //         ->with('danger', 'Las credenciales no coinciden con los datos ingresados.');
        // }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url().'/');
    }

    public function encript($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


}