<?php


namespace App\Controllers;


use App\Traits\Grocery;
use App\Models\MenuCliente;
use App\Models\MenuFuncionarios;
use App\Models\MuestreoDetalle;
use App\Models\Muestreo;
use App\Models\Certificacion;
use CodeIgniter\Exceptions\PageNotFoundException;

class TableController extends BaseController
{
    use Grocery;

    private $crud;
    private $certificado_aux;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
        $this->get_certificaciones();
        $this->get_auditoria();
        $this->get_auditoria_cer();
    }

    public function index($data)
    {
        if (session('user')->funcionario) $menu = new MenuFuncionarios();
        else $menu = new MenuCliente();
        $component = $menu->where(['table' => $data, 'component' => 'table'])->get()->getResult();
        if($component) {
            $this->crud->setTable($component[0]->table);
            switch ($component[0]->table) {
                case 'view_auditoria_cer':
                    $this->crud->unsetAdd();
                    $this->crud->unsetEdit();
                    $this->crud->unsetDelete();
                    $this->crud->displayAs(['certificado_nro' => 'Informe nro', 'id_auditoria' => 'Nro Auditoria']);
                    break;
                case 'view_auditoria':
                    $this->crud->unsetAdd();
                    $this->crud->unsetEdit();
                    $this->crud->unsetDelete();
                    $this->crud->displayAs(['certificado_nro' => 'Informe nro', 'id_auditoria' => 'Nro Auditoria']);
                    break;
                case 'certificacion':
                    $this->crud->setTable('view_certificados'.session('user')->id);
                    $this->crud->unsetAdd();
                    $this->crud->unsetEdit();
                    $this->crud->unsetDelete();
                    // $this->crud->fieldTypeColumn('id_tipo_analisis_primer_informe', 'invisible');
                    // $this->crud->fieldTypeColumn('id_tipo_analisis_informe_final', 'invisible');
                    // $this->crud->fieldTypeColumn('emision_preinforme', 'invisible');
                    // $this->crud->fieldTypeColumn('emision_informe', 'invisible');
                    $this->crud->fieldTypeColumn('conformidad', 'invisible');
                    if(session('user')->id == 10){    
                        $columnas = [
                            'certificado_nro',
                            'mue_fecha_muestreo',
                            'mue_lote',
                            'mue_subtitulo',
                            'mue_identificacion',
                            'id_tipo_analisis_primer_informe',
                            'id_tipo_analisis_informe_final','emision_preinforme',
                            'emision_informe',
                            'conformidad',
                            'id_producto',
                            'preinforme', 'informe', 'informe2'];
                    }else{
                        $columnas = [
                            'certificado_nro',
                            'mue_fecha_muestreo',
                            'id_cliente',
                            'mue_subtitulo',
                            'id_codigo_amc',
                            'mue_identificacion',
                            'id_tipo_analisis_primer_informe',
                            'id_tipo_analisis_informe_final','emision_preinforme',
                            'emision_informe',
                            'conformidad',
                            'id_producto',
                            'informe', 'preinforme', 'informe2'];
                    }
                    $this->crud->setRelation('id_tipo_analisis_primer_informe', 'muestra_tipo_analisis', '{mue_nombre} - {mue_sigla}');
                    $this->crud->setRelation('id_tipo_analisis_informe_final', 'muestra_tipo_analisis', '{mue_nombre} - {mue_sigla}');
                    $this->crud->setRelation('id_producto', 'producto', '{pro_nombre}');
                    $this->crud->columns($columnas);
                    $this->crud->displayAs([
                        'mue_fecha_muestreo' => 'Fecha de registro',
                        'certificado_nro' => 'Info Nro.',
                        'id_cliente' => 'Cliente',
                        'mue_subtitulo' => 'Seccional',
                        'id_codigo_amc' => 'Codigo',
                        'mue_identificacion' => 'Identificación',
                        'id_producto' => 'Producto',
                        'mensaje' => 'Resultado',
                        'informe' => 'Resultado',
                        'preinforme' => 'Primer informe',
                        'informe2' => 'Informe',
                        'id_tipo_analisis_primer_informe' => 'Análisis primer informe',
                        'id_tipo_analisis_informe_final' => 'Análisis informe final',
                        'emision_preinforme' => 'Fecha emisión primer informe',
                        'emision_informe' => 'Fecha emisión informe final'
                    ]);
                    $certificado = [];
                    $this->crud->callbackColumn('mensaje', function($resultado, $row){
                        $div = '<div id="div_resultado_'.$row->certificado_nro.'">'.$resultado.'</div>';
                        return $div;
                    });
                    $this->crud->callbackColumn('informe', function($fecha, $row){
                        $fecha_aux = explode('/', $row->mue_fecha_muestreo);
                        $fecha_aux2 = explode(' ', $fecha_aux[2]);
                        $fecha_m = $fecha_aux2[0].'-'.$fecha_aux[1].'-'.$fecha_aux[0].' '.$fecha_aux2[1];
                        $row = procesar_registro_fetch('certificacion', 'certificado_nro', $row->certificado_nro);
                        $this->certificado_aux[$row[0]->certificado_nro] = $row[0];
                        $row = $row[0];
                        $aux_bttn_preinforme = '<div class="button grocery">';
                        $aux_bttn_preinforme .= '<input type="hidden" id="table-plantilla_'.$row->certificado_nro.'" value="1">';
                        if ($row->certificado_estado == 3 || $row->certificado_estado == 5){//cer_fecha_preinforme 
                            $aux_bttn_preinforme .= '
                                <button class="btn green white-text" onClick="js_mostrar_detalle(`card-detalle`,`card-table`,'.$row->certificado_nro.',1,`php_lista_resultados`)"><i class="fad fa-check-circle"></i></button>
                            ';
                        } else {
                            $aux_bttn_preinforme .='
                                <button class="btn red white-text" onClick="js_mostrar_detalle(`card-detalle`,`card-table`,'.$row->certificado_nro.',2,`php_lista_resultados`)"><i class="fad fa-times-circle"></i></button>
                            ';
                        }
                        $aux_bttn_preinforme .= '</div>';
                        return $aux_bttn_preinforme;
                    });
                    $this->crud->callbackColumn('preinforme', function($fecha, $row){
                        $certificado_aux = $this->certificado_aux[$row->certificado_nro];
                        $aux_variable_preinforme = 0;
                        if ($certificado_aux->certificado_estado == 3 || $certificado_aux->certificado_estado == 5)
                            $aux_variable_preinforme = 1;
                        $aux_bttn_preinforme ='<div class="button grocery" id="pre_informe_'.$row->certificado_nro.'">';
                        if ($fecha == '0000-00-00 00:00:00'){//cer_fecha_preinforme
                            if($aux_variable_preinforme == 0){
                                $aux_bttn_preinforme .= '<button class="btn red white-text"><i class="fad fa-times-circle"></i></button>';
                                // $aux_bttn_preinforme .= '<button class="btn red white-text" onClick="js_mostrar_detalle(`card-detalle`,`card-table`,'.$row->certificado_nro.',2,`php_lista_resultados`)"><i class="fad fa-times-circle"></i></button>';
                            }else{
                                $aux_bttn_preinforme .= '<button class="btn red white-text" onClick="my_toast(`No puede generar preinforme, ya que posee resultados de an&aacute;lisis`, `red darken-4`, 5000)"><i class="fad fa-times-circle"></i></button>';
                            }
                        } else {
                            $aux_bttn_preinforme .= '<button class="btn green white-text" onClick="descargar_info(`'.$row->certificado_nro.'`, 0, `'.session('user')->usr_rol.'`, 0)"><i class="fad fa-check-circle"></i></button>';
                        }
                        $aux_bttn_preinforme .='</div>';
                        return $aux_bttn_preinforme;
                    });
                    $this->crud->callbackColumn('informe2', function($fecha, $row){
                        $certificado_aux = $this->certificado_aux[$row->certificado_nro];
                        $aux_bttn_preinforme='<div class="button grocery" id="certificado_'.$row->certificado_nro.'">';
                        // if ($certificado_aux->cer_fecha_analisis == '0000-00-00 00:00:00'){//cer_fecha_analisis
                        //     $aux_bttn_preinforme .= '<button class="btn red white-text" onClick="my_toast(`No puede generar informe, ya que &nbsp <b>NO</b> &nbsp posee los resultados del an&aacute;lisis`, `red darken-4`, 5000)"><i class="fad fa-times-circle"></i></button>';
                        // }else {
                            if ($certificado_aux->cer_fecha_informe == '0000-00-00 00:00:00'){//cer_fecha_informe
                                $aux_bttn_preinforme .= '<button class="btn red white-text"><i class="fad fa-times-circle"></i></button>';
                                // $aux_bttn_preinforme .= '<button class="btn red white-text" onClick="js_mostrar_detalle(`card-detalle`,`card-table`,`'.$row->certificado_nro.'`,3,`php_lista_resultados`)"><i class="fad fa-times-circle"></i></button>';
                            }else{
                                if($certificado_aux->cer_fecha_publicacion > '0000-00-00 00:00:00'){
                                    if($certificado_aux->cer_fecha_facturacion > '0000-00-00 00:00:00' ){
                                        $aux_btn_i = "fad fa-usd-circle";
                                        $aux_btn_c = 'cyan';
                                        $aux_metodo = 3;
                                    }else{
                                        $aux_btn_i = "fad fa-thumbs-up";
                                        $aux_btn_c = 'deep-orange';
                                        $aux_metodo = 2;
                                    }
                                }else{
                                    $aux_btn_i = "fad fa-check-circle";
                                    $aux_btn_c = 'green';
                                    $aux_metodo = 1;
                                }
                                $aux_bttn_preinforme .= '<button class="btn '.$aux_btn_c.' white-text" onClick="actualizar_informe(`'.$row->certificado_nro.'`, `'.$aux_metodo.'`, '.session('user')->usr_rol.', 2)"><i class="'.$aux_btn_i.'"></i></button>';
                            }
                        // }
                        $aux_bttn_preinforme .='</div>';
                        return $aux_bttn_preinforme;
                    });
                    break;
                case 'usuario':
                    $this->crud->displayAs([
                        'id'                    => 'Nit/Cédula',
                        'name'                  => 'Empresa',
                        'username'              => 'Usuario',
                        'email'                 => 'Email',
                        'usertype'              => 'Rol',
                        'registerDate'          => 'Registro',
                        'lastvisitDate'         => 'Ultima visita',
                        'use_cargo'             => 'Cargo',
                        'use_nombre_encargado'  => 'Encargado',
                        'use_telefono'          => 'Teléfono',
                        'use_fax'               => 'Fax',
                        'use_direccion'         => 'Dirección',
                        'password'              => 'Contraseña',
                        'block'                 => 'Estado',
                        'emails'                => 'Envio de emails'
                    ]);
                    $this->crud->fieldType('password', 'password');
                    $this->crud->columns(['id', 'name', 'username', 'email', 'usertype', 'registerDate', 'lastvisitDate', 'use_cargo','use_nombre_encargado','use_telefono','use_fax','use_direccion', 'emails']);                
                    
                    $this->crud->unsetEditFields(['usertype', 'registerDate', 'lastvisitDate']);
                    $this->crud->fieldTypeEditForm('id', 'hidden');
                    $this->crud->editFields([
                        'id',
                        'name',
                        'username',
                        'email',
                        'password',
                        'use_cargo',
                        'block',
                        'use_nombre_encargado',
                        'use_telefono',
                        'use_fax',
                        'use_direccion',
                        'pyme', 'emails'
                    ]);
                    $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                        if (strpos($stateParameters->data['username'], " "))
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("No se permiten espacion en el campo 'Usuario'");
                        $password = $stateParameters->data['password'];
                        if (!strstr($password, '[BEFORE UPDATE]')) {
                            $stateParameters->data['password'] = md5($password);
                        }
                        return $stateParameters;
                    });
                    if (!session('user')->funcionario){
                        $this->crud->where(['usuario.id = ?' => session('user')->id ]);
                        $this->crud->unsetOperations();
                    }
                    $this->crud->where(['usuario.usertype != ?' => 'Administrador']);
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

                    $this->crud->addFields([
                        'id', 'name', 'username', 'email', 'use_nombre_encargado', 'use_cargo', 'use_telefono', 'use_fax', 'use_direccion', 'emails'
                    ]);
                    $this->crud->uniqueFields(['id', 'username', 'email', 'name', 'use_telefono']); 
                    $this->crud->requiredFields(['id', 'username', 'email', 'name', 'use_telefono']);
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        if (strpos($stateParameters->data['username'], " "))
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("No se permiten espacion en el campo 'Usuario'");
                        $stateParameters->insertId = $stateParameters->data['id'];
                        $stateParameters->data['usertype'] = 'Registered';
                        $stateParameters->data['password'] = md5($stateParameters->data['id']);
                        $stateParameters->data['registerDate'] = date('Y-m-d H:i:s');
                        $stateParameters->data['lastvisitDate'] = date('Y-m-d H:i:s');
                        $stateParameters->data['block'] = 1;
                        $stateParameters->data['pyme'] = 'No';

                        // $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                        // return $errorMessage->setMessage($stateParameters->insertId);
                    
                        return $stateParameters;
                    });
                    break;
                case 'tecnica':
                    $this->crud->displayAs([
                        'nor_nombre'        => 'Nombre',
                        'nor_descripcion'   => 'Descripción'
                    ]);
                    break;
                case 'norma':
                    $this->crud->displayAs([
                        'nor_nombre'        => 'Nombre',
                        'nor_descripcion'   => 'Descripción'
                    ]);
                    break;
                case 'producto':
                    $this->crud->displayAs([
                        'pro_nombre'        => 'Nombre1 - (Aparece en remisión)',
                        'pro_descripcion'   => 'Nombre2 - (Aparece en Informe)',
                        'id_norma'          => 'Norma'
                    ]);
                    $this->crud->setRelation('id_norma', 'norma', 'nor_nombre');
                    break;
                case 'parametro':
                    $this->crud->displayAs([
                        'par_nombre'        => 'Nombre',
                        'par_descripcion'   => 'Descripcion',
                        'par_estado'        => 'Estado',
                        'par_irca'          => 'Irca',
                        'id_tecnica'        => 'Tecnica',
                        'par_metodo'        => 'Método',
                        'id_acreditacion'   => 'Acreditación',
                        'id_calculo'        => 'Calculo'
                    ]);
                    $this->crud->setRelation('id_tecnica', 'tecnica', 'nor_nombre');
                    $this->crud->setRelation('id_acreditacion', 'acreditaciones', 'nombre');
                    $this->crud->setRelation('id_calculo', 'calculos_fq', 'nombre');
                    break;
                case 'ensayo':
                    $this->crud->displayAs([
                        'id_producto'   => 'Producto',
                        'id_parametro'  => 'Parametro',
                        'refe_bibl'     => 'Referencia Bibliografica'
                    ]);
                    $this->crud->setRelation('id_producto', 'producto', 'pro_nombre');
                    $this->crud->setRelation('id_parametro', 'parametro', 'par_nombre');
                    $this->crud->callbackColumn('med_valor_min', function($resultado, $row){
                        $div = '<p>'.$resultado.'</p>';
                        return $div;
                    });
                    $this->crud->callbackColumn('med_valor_max', function($resultado, $row){
                        $div = '<p>'.$resultado.'</p>';
                        return $div;
                    });
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $id_producto = $stateParameters->data['id_producto'];
                        $id_parametro = $stateParameters->data['id_parametro'];
                        $exist = procesar_registro_fetch('ensayo', 'id_producto', $id_producto, 'id_parametro', $id_parametro);
                        if(!empty($exist))
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("Este producto ya se encuentra asigando a este parametro");
                        return $stateParameters;
                    });
                    break;
                    
                case 'muestra_tipo_analisis':
                        $this->crud->displayAs([
                            'mue_nombre'   => 'Nombre',
                            'mue_sigla'  => 'Sigla',
                            'mue_descripcion'     => 'Descripción'
                        ]);
                        $this->crud->unsetDelete();
                        $this->crud->unsetAdd();
                        $this->crud->callbackBeforeDelete(function ($stateParameters) {
                            $id = $stateParameters->primaryKeyValue;
                            $muestreoD = new MuestreoDetalle();
                            $certificado = new Certificacion();
                            $muestreo = $muestreoD->where(['id_tipo_analisis' => $id])->get()->getResult();
                            $certificado = $certificado->where(['id_tipo_analisis_primer_informe' => $id])->get()->getResult();
                            if(count($muestreo) > 0 || count($certificado) > 0){
                                // Custom error messages are only available on Grocery CRUD Enterprise
                                $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                                return $errorMessage->setMessage("No puede eliminar este tipo de analisis. Posee registros asignados a este analisis");
                            }
                            return $stateParameters;
                        });
                    break;
                    
                
                // Pagina
                
                case 'general':
                    $this->crud->displayAs([
                        'title' => 'Titulo',
                        'description' => 'Descripción',
                        'keywords' => 'Palabras claves'
                    ]);
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                    
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if (!preg_match('/\.(png|jpg|jpeg|svg|icon)$/',$filename))
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                        return $uploadData;
                    });
                    $this->crud->setFieldUpload('logo', 'page/images/shorticon', base_url().'/page/images/shorticon');
                    $this->crud->setFieldUpload('logo_menu', 'page/images/menu', base_url().'/page/images/menu');
                    $data = procesar_registro_fetch('general',0,0);
                    if (count($data)  > 0) {
                        $this->crud->unsetAdd();
                        $this->crud->unsetDelete();
                    }
                    break;
                case 'banner':
                    $this->crud->displayAs([
                        'title' => 'Titulo',
                        'description' => 'Descripción',
                        'img' => 'Imagen'
                    ]);
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                    
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if (!preg_match('/\.(png|jpg|jpeg)$/',$filename)) {
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                        }
                        // Don't forget to return the uploadData at the end
                        return $uploadData;
                    });
                    $this->crud->setFieldUpload('img', 'page/images/banner', base_url().'/page/images/banner');
                    break;

                case 'services':
                    $this->crud->displayAs([
                        'title' => 'Titulo',
                        'description' => 'Descripción',
                        'img' => 'Imagen'
                    ]);
                    $this->crud->setTexteditor(['description']);
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                    
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if (!preg_match('/\.(png|jpg|jpeg)$/',$filename)) {
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                        }
                        return $uploadData;
                    });
                    $this->crud->setFieldUpload('img', 'page/images/services', base_url().'/page/images/services');
                    break;
                case 'detail_services':
                    $this->crud->displayAs([
                        'services_id'        => 'Servicio',
                        'description'   => 'Descripción',
                    ]);
                    $this->crud->requiredFields(['description']);
                    $this->crud->setRelation('services_id', 'services', 'title');
                    break;
                case 'accreditations':
                    $this->crud->setFieldUpload('document', 'page/documents/accreditations', base_url().'/page/documents/accreditations');
                    $this->crud->setFieldUpload('img', 'page/images/accreditations', base_url().'/page/images/accreditations');
                    $this->crud->fieldTypeColumn('document', 'string');
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if($fieldName == 'document'){
                            if (!preg_match('/\.(pdf, doc, docx)$/',$filename)) {
                                return (new \GroceryCrud\Core\Error\ErrorMessage())
                                    ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                            }
                        }else{
                            if (!preg_match('/\.(png|jpg|jpeg)$/',$filename)) {
                                return (new \GroceryCrud\Core\Error\ErrorMessage())
                                    ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                            }
                        }
                        return $uploadData;
                    });
                    $this->crud->displayAs([
                        'title' => 'Titulo',
                        'description' => 'Description',
                        'document' => 'Documento',
                        'img' => 'Imagen'
                    ]);
                    break;
                case 'about_us':
                    $this->crud->displayAs([
                        'title'        => 'Titulo',
                        'description'   => 'Descripción',
                        'img'        => 'Imagen'
                    ]);
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                    
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if (!preg_match('/\.(png|jpg|jpeg)$/',$filename)) {
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                        }
                        // Don't forget to return the uploadData at the end
                        return $uploadData;
                    });
                    $this->crud->setFieldUpload('img', 'page/images/about_us', base_url().'/page/images/about_us');
                    $data = procesar_registro_fetch('about_us',0,0);
                    if (count($data)  > 0) {
                        $this->crud->unsetAdd();
                        $this->crud->unsetDelete();
                    }
                    break;
                case 'detail_about_us':
                    $this->crud->displayAs([
                        'title'        => 'Titulo',
                        'description'   => 'Descripción'
                    ]);
                    break;

                case 'contacto':
                    $this->crud->displayAs([
                        'description_redes' => 'Descripcion para redes',
                        'direction' => 'Dirección',
                        'phone' => 'Telefono',
                        'text_whatsapp' => 'Mensaje whatsapp',
                        'phone_whatsapp' => 'Telefono whatsapp'
                    ]);
                    $this->crud->setFieldUpload('imagen', 'page/images/contacto', base_url().'/page/images/contacto');
                    $this->crud->callbackBeforeUpload(function ($uploadData) {
                        $fieldName = $uploadData->field_name;
                    
                        $filename = isset($_FILES[$fieldName]) ? $_FILES[$fieldName]['name'] : null;
                        if (!preg_match('/\.(png|jpg|jpeg)$/',$filename)) {
                            return (new \GroceryCrud\Core\Error\ErrorMessage())
                                ->setMessage("The file extension for filename: '" . $filename. "'' is not supported!");
                        }
                        // Don't forget to return the uploadData at the end
                        return $uploadData;
                    });
                    $data = procesar_registro_fetch('contacto',0,0);
                    if (count($data)  > 0) {
                        $this->crud->unsetAdd();
                        $this->crud->unsetDelete();
                    }
                    break;
                case 'diluciones':
                    $this->crud->unsetDelete();
                    break;
                case 'acreditaciones':
                    $this->crud->unsetDelete();
                    break;
                case 'calculos_fq':
                    $this->crud->unsetDelete();
                    break;
                case 'factor_fq':
                    $this->crud->unsetDelete();
                    break;
                    
                default:
                    break;
            }
            $output = $this->crud->render();
            if (isset($output->isJSONResponse) && $output->isJSONResponse) {
                header('Content-Type: application/json; charset=utf-8');
                echo $output->output;
                exit;
            }

            $this->viewTable($output, $component[0]->title, $component[0]->description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function get_certificaciones(){
        $db = \Config\Database::connect();
        $aux_columna_cliente =" (select name from usuario where id=m.id_cliente) id_cliente,"; 
        $aux_columna_id_codigo_amc =" 
            if (d.ano_codigo_amc, CONCAT (d.ano_codigo_amc,\"-\",LPAD(d.id_codigo_amc,5,\"0\")) ,CONCAT ((select mue_sigla from muestra_tipo_analisis where id_muestra_tipo_analsis= d.id_tipo_analisis),\" \", d.id_codigo_amc)) id_codigo_amc,             
            "; 
        $aux_columna_lote =" "; 
        $sql = "CREATE OR REPLACE VIEW view_certificados".session('user')->id." 
                AS
                select
                c.id_muestreo,
                m.mue_fecha_muestreo,
                c.certificado_nro,
               ".$aux_columna_cliente."
                m.mue_subtitulo,
                ".$aux_columna_id_codigo_amc."
                ".$aux_columna_lote."
                d.mue_identificacion, 
                d.id_producto,
                c.certificado_estado resultados,
                c.cer_fecha_preinforme preinforme,
                c.cer_fecha_analisis informe,
                c.cer_fecha_informe informe2,
                c.cer_fecha_publicacion fecha_publicacion,
                c.cer_fecha_facturacion fecha_facturacion,
                c.id_tipo_analisis_primer_informe,
                c.id_tipo_analisis_informe_final,
                c.cer_fecha_preinforme emision_preinforme,
                c.cer_fecha_informe emision_informe,
                c.conformidad conformidad,
                (select mensaje_titulo from mensaje_resultado where id_mensaje=c.id_mensaje) mensaje
                from certificacion c, muestreo m, muestreo_detalle d
                where c.id_muestreo = m.id_muestreo and c.id_muestreo_detalle = d.id_muestra_detalle
                and m.mue_estado <> 0 order by certificado_nro desc ";
        $db->query($sql);
    }
    
    function get_auditoria()
    {
        $db = \Config\Database::connect();
         
        $sql = "CREATE OR REPLACE VIEW view_auditoria
        AS
        SELECT
            au.id_auditoria
            , ce.certificado_nro
            , md.id_codigo_amc prueba_nro
            ,(select pp.pro_nombre from producto pp where pp.id_producto=ee.id_producto) producto
            , (select pa.par_nombre from parametro pa where pa.id_parametro=ee.id_parametro) parametro
            ,au.columna campo_modificado, au.valor valor_registrado 
            ,  au.fecha fecha_modificacion
            , (select cu.usr_usuario from cms_users cu where cu.id=au.id ) usuario_responsable 
            FROM au_ensa_vs_mues au left JOIN ensayo_vs_muestra em on au.id_ensayo_vs_muestra=em.id_ensayo_vs_muestra 
            left join ensayo ee on em.id_ensayo=ee.id_ensayo
            LEFT JOIN muestreo_detalle md on md.id_muestra_detalle=em.id_muestra
            left join certificacion ce on ce.id_muestreo_detalle=md.id_muestra_detalle order by au.id_auditoria desc
        ";
        
         $db->query($sql);
       
    }
    
    function get_auditoria_cer()
    {
        $db = \Config\Database::connect();
         
        $sql = "CREATE OR REPLACE VIEW view_auditoria_cer
        AS
        SELECT
            au.id_auditoria
            , ce.certificado_nro informe_nro
            , md.id_codigo_amc prueba_nro
            , au.columna campo_modificado, au.valor valor_registrado 
            , au.fecha fecha_modificacion
            , (select cu.usr_usuario from cms_users cu where cu.id=au.id ) usuario_responsable 
            FROM au_ensa_vs_mues au
            left join certificacion ce on ce.id_muestreo_detalle = au.id_ensayo_vs_muestra
            LEFT JOIN muestreo_detalle md on md.id_muestra_detalle = au.id_ensayo_vs_muestra
            where ce.certificado_nro  is not null
            order by au.id_auditoria desc
        ";
        
         $db->query($sql);
       
    }
}