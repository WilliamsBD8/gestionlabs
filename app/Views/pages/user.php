<?= view('layouts/header') ?>
<?= view('layouts/navbar_vertical') ?>
<?= view('layouts/navbar_horizontal') ?>
    <!-- BEGIN: Page Main-->
<?php if ( !empty(configInfo()['intro']) && isset(configInfo()['intro'])): ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div class="card">
                            <div class="card-content">
                                <?= configInfo()['intro'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div id="sales-chart">
                            <div class="row">
                                <div class="col s12">
                                </div>
                                <div class="col s12">
                                    <div id="revenue-chart" class="card animate fadeUp">
                                        <div class="card-content">
                                            <table class="striped centered">
                                                <h4 class="center-align">Mis datos</h4>
                                                <tbody>
                                                  <tr>
                                                    <td>Nombre</td>
                                                    <td><?= session('user')->name ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Usuario</td>
                                                    <td><?= session('user')->username ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Email</td>
                                                    <td><?= session('user')->email ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Rol</td>
                                                    <td><?= session('user')->usertype ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Fecha de registro</td>
                                                    <td><?= date_user(session('user')->registerDate) ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Cargo</td>
                                                    <td><?= session('user')->use_cargo ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Encargado</td>
                                                    <td><?= session('user')->use_nombre_encargado ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Télefono</td>
                                                    <td><?= session('user')->use_telefono ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Fax</td>
                                                    <td><?= session('user')->use_fax ?></td>
                                                  </tr>
                                                  <tr>
                                                    <td>Dirección</td>
                                                    <td><?= session('user')->use_direccion ?></td>
                                                  </tr>
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
        </div>
    </div>
<?php endif; ?>


<?= view('layouts/footer') ?>