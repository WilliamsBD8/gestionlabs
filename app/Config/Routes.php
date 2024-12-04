<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'AuthController::login');
$routes->get('/reset_password', 'AuthController::resetPassword');
$routes->get('/logout', 'AuthController::logout');
$routes->post('/validation', 'AuthController::validation');
$routes->post('/forgot_password', 'AuthController::forgotPassword');
// ----------------------- Fin de login --------------
$routes->group('cliente', function ($routes){
    $routes->get('password', 'ClienteController::password');
    $routes->post('password/password_update', 'ClienteController::password_update');
    $routes->get('certificado', 'ClienteController::certificado',['as' => 'certificado']);
    // $routes->get('certificado/(:segment)','ClienteController::certificado_download/$1');
    $routes->get('certificado/filtrar','ClienteController::certificado_filtrar',['as'=>'filtrar_certificado']);
    $routes->post('certificado/paginar','ClienteController::certificado_paginar',['as'=>'filtrar_paginar']);
    $routes->get('reporte', 'ClienteController::reporte');
    $routes->post('reporte', 'ClienteController::reporte_post');
    $routes->get('user', 'ClienteController::user');
    
    $routes->get('accreditations', 'ClienteController::accreditations');
    $routes->post('accreditations', 'ClienteController::accreditations_d');
});
    // --------------------- Fin Controller Cliente ---------

$routes->group('funcionario', function ($routes){
    $routes->group('remisiones', function ($routes){
        $routes->get('', 'FuncionarioController::remision');
        $routes->post('empresa', 'FuncionarioController::remision_empresa');
        $routes->post('muestra', 'FuncionarioController::remision_muestra');
        $routes->get('edit', 'FuncionarioController::remision_edit');
        $routes->post('edit/muestra', 'FuncionarioController::remision_edit_muestra');
        $routes->get('ticket/(:segment)', 'FuncionarioController::remision_ticket/$1');


        $routes->get('detail/(:num)/(:segment)', 'RemisionesController::detail/$1/$2');
        $routes->get('muestra/product/(:num)/(:segment)', 'RemisionesController::muestra_product/$1/$2');
    });

    $routes->get('resultados', 'FuncionarioRMController::resultados');
    $routes->post('resultados/ingreso', 'FuncionarioRMController::ingreso_muestras');
    $routes->post('resultados/ingreso/resultado', 'FuncionarioRMController::ingreso_muestras_resultado');
    $routes->post('resultados/analisis', 'FuncionarioRMController::analisis');
    $routes->post('resultados/date', 'FuncionarioRMController::cambio_date');
    $routes->post('resultados/confirmacion', 'FuncionarioRMController::confirmacion');
    $routes->post('resultados/date/fecha', 'FuncionarioRMController::date_fecha');

    $routes->get('resultados/alimentos', 'FuncionarioALFQController::index');
    $routes->post('resultados/alimentos', 'FuncionarioALFQController::buscar_muestra');
    $routes->post('resultados/alimentos/cambiar/fq', 'FuncionarioALFQController::cambiar_campos_resultados_fq');

    $routes->get('resultados/aguas', 'FuncionarioAGFQController::index');
    $routes->post('resultados/aguas', 'FuncionarioAGFQController::buscar_muestra');
    $routes->post('resultados/aguas/cambiar/fq', 'FuncionarioAGFQController::cambiar_campos_resultados_fq');

    $routes->post('certificacion', 'FuncionarioCController::index');
    
    $routes->get('certificacion/emails', 'FuncionarioEmailController::emails');
    $routes->post('certificacion/emails_certificado', 'FuncionarioEmailController::emails_certificado');
    $routes->post('certificacion/emails_certificado/send', 'FuncionarioEmailController::emails_certificado_send');
    
    $routes->get('certificacion/emails_get', 'FuncionarioEmailController::get_emails');
    $routes->post('certificacion/email/create', 'FuncionarioEmailController::create_email');

});
    // -------------------- Fin Controller Funcionario ------

$routes->group('GestionLabs', function ($routes){
    $routes->get('home', 'HomeController::index');
    $routes->get('about', 'HomeController::about');
    $routes->get('perfile', 'UserController::perfile');
    $routes->post('update_photo', 'UserController::updatePhoto');
    $routes->post('update_user', 'UserController::updateUser');

});

$routes->post('certificado/download','FuncionarioCController::certificado_down');

$routes->get('certificado/(:segment)','PageController::certificado/$1');
$routes->post('certificado/consulta','PageController::consulta');
$routes->post('contacto','PageController::contacto');

$routes->get('certificado/view/(:segment)', 'PageController::view/$1');
$routes->get('certificado/view_page/(:segment)/(:segment)', 'PageController::view_page/$1/$2');

$routes->post('/config/(:segment)', 'ConfigController::index/$1');
$routes->get('/config/(:segment)', 'ConfigController::index/$1');
$routes->post('/table/(:segment)', 'TableController::index/$1');
$routes->get('/table/(:segment)', 'TableController::index/$1');


/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
