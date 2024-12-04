<?php


namespace App\Controllers;


use App\Models\Configuration;
use App\Traits\Grocery;

class ConfigController extends BaseController
{
    use Grocery;

    private $crud;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
    }

    public function index($data)
    {

        $this->crud->setTable($data);
        switch ($data) {
            case 'cms_users':
                $title = 'Usuarios';
                $subtitle = 'Listado de usuarios.';
                $this->crud->unsetColumns(['usr_clave']);
                $this->crud->fieldType('usr_clave', 'password');
                $this->crud->where(['cms_users.usr_rol > ?' => '1']);
                $this->crud->setRelation('usr_rol', 'cms_rol', 'nombre', ['usr_rol > ?' => '1']);
                $this->crud->displayAs([
                    'usr_usuario'   => 'Usuario',
                    'usr_correo'    => 'Email',
                    'usr_rol'       => 'Rol',
                    'usr_estado'    => 'Estado',
                    'usr_foto'      => 'Foto',
                ]);
                $this->crud->setFieldUpload('firma', 'assets/img/firmas', base_url().'/assets/img/firmas');
                $this->crud->setFieldUpload('usr_foto', 'upload/images', base_url().'/upload/images');
                $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        // if($fieldName == 'firma'){
                            if (!preg_match('/\.(png|jpg|jpeg)$/',$filename))
                                return (new \GroceryCrud\Core\Error\ErrorMessage())
                                    ->setMessage("No se permite este archivo: '" . $fieldName . ", procure cargar imagenes con extención png, jpg o jpeg");
                            
                        // }
                        return $uploadData;
                    });
                if(session('user')->funcionario){
                    if(session('user')->usr_rol == 2){
                        $this->crud->unsetDelete();
                    }
                }
                break;
            case 'cms_firma':
                $this->crud->setRelation('id_firma_1', 'cms_users', '{nombre} - {usr_estado}', ['usr_rol > ?' => 1]);
                $this->crud->setRelation('id_firma_2', 'cms_users', '{nombre} - {usr_estado}', ['usr_rol > ?' => 1]);
                $this->crud->requiredFields(['id_firma_1', 'id_firma_2']);
                $this->crud->displayAs([
                    'id_firma_1'   => 'Firma 1',
                    'id_firma_2'    => 'Firma 2',
                ]);
                break;
            case 'usuario':
                $title = 'Usuarios';
                $subtitle = 'Listado de usuarios.';
                $this->crud->unsetColumns(['password']);
                $this->crud->fieldType('password', 'password');
                $this->crud->callbackBeforeInsert(function ($stateParameters) {
                    $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                    return $stateParameters;
                });
                $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                    if(strlen($stateParameters->data['password']) < 20) {
                        $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                    }
                    return $stateParameters;
                });
                $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                    $password = $stateParameters->data['password'];
                    if (!strstr($password, '[BEFORE UPDATE]')) {
                        $stateParameters->data['password'] = md5($password);
                    }
                
                    return $stateParameters;
                });
                $this->crud->callbackBeforeDelete(function ($stateParameters) {
                    $id = $stateParameters->primaryKeyValue;
                    $muestreo = new Muestreo();
                    $muestreo = $muestreo->where(['id_cliente' => $id])->get()->getResult();
                    if(count($muestreo) > 0 ){
                        // Custom error messages are only available on Grocery CRUD Enterprise
                        $message = "No puede eliminar esta Empresa. Existen '".count($muestreo)."' informes relacionados a esta empresa ";
                        if(count($muestreo) == 1)
                            $message = "No puede eliminar esta Empresa. Existe '".count($muestreo)."' informe relacionado a esta empresa ";
                        $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                        return $errorMessage->setMessage($message);
                    }
                    return $stateParameters;
                });
                // $this->crud->setRelation('role_id', 'roles', 'name');
                $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');

                break;
            case 'permissions_cliente':
                $title = 'Permisos';
                $subtitle = 'Listado de permisos.';
                //$this->crud->setRelation('role_id', 'roles', 'name');
                $this->crud->setRelation('menu_id', 'menus_cliente', '{option} - {type}');
                break;
            case 'permissions_funcionarios':
                $title = 'Permisos';
                $subtitle = 'Listado de permisos.';
                $this->crud->setRelation('usr_rol', 'cms_rol', '{nombre} - {usr_rol}');
                $this->crud->setRelation('menu_id', 'menus_funcionarios', '{option} - {type}');
                break;
            case 'menus_cliente':
                $title = 'Opciones del Menu';
                $subtitle = 'Listado de opciones de menu.';
                $this->crud->setTexteditor(['description']);
                $this->crud->setRelation('references', 'menus_cliente', 'option');
                break;
            case 'menus_funcionarios':
                $title = 'Opciones del Menu';
                $subtitle = 'Listado de opciones de menu.';
                $this->crud->setTexteditor(['description']);
                $this->crud->setRelation('references', 'menus_funcionarios', 'option');
                break;
            case 'roles':
                $title = 'Roles';
                $subtitle = 'Listado de roles.';
                break;
            case 'cms_rol':
                $title = 'Roles';
                $subtitle = 'Listado de roles.';
                break;
            case 'notifications':
                $title = 'Notificaciones';
                $subtitle = 'Listado de Notificaciones.';
                $id = session()->get('user');
                $this->crud->fieldType('user_id', 'number', session()->get('user')->id );
                break;
            case 'configurations':
                $title = 'Configuraciones';
                $subtitle = 'Listado de configuraciones.';
                $config = new Configuration();
                $data = $config->findAll();
                $this->crud->setTexteditor(['footer', 'intro']);
                $this->crud->setFieldUpload('background_image', 'assets/img', base_url().'/assets/img');
                $this->crud->setFieldUpload('favicon', 'assets/img', base_url().'/assets/img');
                $this->crud->setFieldUpload('background_img_vertical', 'assets/img', base_url().'/assets/img');

                if (count($data)  > 0) {
                    $this->crud->unsetAdd();
                    $this->crud->unsetDelete();
                }
                break;
        }
        $output = $this->crud->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

        $this->viewTable($output, $title, $subtitle);
    }


}