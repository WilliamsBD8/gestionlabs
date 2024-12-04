<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="<?= isset(configInfo()['meta_description']) ? configInfo()['meta_description'] : 'Name' ?>">
    <meta name="keywords"
          content="<?= isset(configInfo()['meta_keywords']) ? configInfo()['meta_keywords'] : 'Name' ?>">
    <meta name="author" content="IPlanet Colombia S.A.S">
    <title><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : 'Name' ?></title>
    <link rel="apple-touch-icon" href="<?= !isset(configInfo()['favicon']) ||  empty(configInfo()['favicon']) ? base_url().'/assets/img/logo.png' :  base_url().'/assets/upload/images/'.configInfo()['favicon']   ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= !isset(configInfo()['favicon']) ||  empty(configInfo()['favicon']) ? base_url().'/assets/img/logo.png' :  base_url().'/assets/img/'.configInfo()['favicon']   ?>">
    <title><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : '' ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/vendors.min.css">
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/style.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/pdf.css">

</head>
<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   "
      data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
  <header class="page-topbar" id="header">
    <div class="navbar">
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-dark gradient-45deg-purple-deep-orange gradient-shadow">
        <div class="nav-wrapper">
          <a href="<?= base_url() ?>" class="brand-logo center"><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : 'IPLANET' ?></a>
        </div>
      </nav>
    </div>
  </header>


    <!-- BEGIN: Page Main-->
  <div class="row pt-2 pb-5">
      <div class="col s12">
          <div class="container">

          <div class="row">
            <div class="col s12">
              <ul class="tabs">
                <?php if($certificado->id_tipo_analisis_primer_informe != '0'): ?>
                  <li class="tab col m6"><a href="#informe_1">Primer informe</a></li>
                <?php endif ?>
                <li class="tab col <?= $certificado->cer_fecha_preinforme != '0000-00-00 00:00:00' ? 'm6' : 'm12' ?>"><a class="active" href="#informe_final">Informe final</a></li>
              </ul>
            </div>
            <?php if($certificado->id_tipo_analisis_primer_informe != '0'): ?>
              <div id="informe_1" class="col s12">
                <div class="row">
                  <div class="col s12 l8">
                    <div class="card pdf_container">
                      <iframe src="<?= base_url(['certificado', 'view_page', $certificado->clave_documento_final, 1]) ?>" height="600px" width="100%"></iframe>
                    </div>
                  </div>
                  <div class="col s12 l4">
                    <div class="card pdf_container">
                      <table class="striped responsive-table">
                        <tbody>
                          <tr>
                            <td>Tipo:</td>
                            <td>Primer Informe</td>
                          </tr>
                          <tr>
                            <td>Informe Nro:</td>
                            <td><?= $certificado->certificado_nro ?>-1</td>
                          </tr>
                          <tr>
                            <td>Fecha de emisión:</td>
                            <td><?= date_fecha($certificado->cer_fecha_publicacion) ?></td>
                          </tr>
                          <tr>
                            <td>Hora de emisión:</td>
                            <td><?= date('h:i:s a', strtotime($certificado->cer_fecha_publicacion)) ?></td>
                          </tr>
                          <tr>
                            <td>Tipo de analisis:</td>
                            <td><?= $analisis_1[0]->mue_nombre ?></td>
                          </tr>
                          <?php if(!empty($certificado->doc_primer_informe)): ?>
                            <tr>
                              <td>Documento:</td>
                              <td><a href="<?= base_url(['assets', 'img', 'docs_informes', $certificado->doc_primer_informe]) ?>" target="_blank"><?= $certificado->doc_primer_informe ?></a></td>
                            </tr>
                          <?php endif ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif ?>
            <div id="informe_final" class="col s12">
              <div class="row">
                <div class="col s12 l8">
                  <div class="card pdf_container">
                    <iframe src="<?= base_url(['certificado', 'view_page', $certificado->clave_documento_final, 2]) ?>" height="600px" width="100%"></iframe>
                  </div>
                </div>
                <div class="col s12 l4">
                  <div class="card pdf_container">
                    <table class="striped responsive-table">
                      <tbody>
                        <tr>
                          <td>Tipo:</td>
                          <td>Informe final</td>
                        </tr>
                        <tr>
                          <td>Informe Nro:</td>
                          <td><?= $certificado->certificado_nro ?></td>
                        </tr>
                        <tr>
                          <td>Fecha de emisión:</td>
                          <td><?= date_fecha($certificado->cer_fecha_publicacion) ?></td>
                        </tr>
                        <tr>
                          <td>Hora de emisión:</td>
                          <td><?= date('h:i:s a', strtotime($certificado->cer_fecha_publicacion)) ?></td>
                        </tr>
                        <tr>
                          <td>Tipo de analisis:</td>
                          <td><?= $analisis_2[0]->mue_nombre ?></td>
                        </tr>
                        <?php if(!empty($certificado->doc_informe_final)): ?>
                            <tr>
                              <td>Documento:</td>
                              <td><a href="<?= base_url(['assets', 'img', 'docs_informes', $certificado->doc_informe_final]) ?>" target="_blank"><?= $certificado->doc_informe_final ?></a></td>
                            </tr>
                          <?php endif ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          </div>
      </div>
  </div>

<?= view('layouts/footer') ?>