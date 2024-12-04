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
                                    <?php if (session('error')): ?>
                                        <div class="card-alert card red">
                                            <div class="card-content white-text">
                                                <p><?= session('error') ?></p>
                                            </div>
                                            <button type="button"class="close white-text" data-dismiss="alert"
                                                    aria-label="Close">
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (session('success')): ?>
                                        <div class="card-alert card green">
                                            <div class="card-content white-text">
                                                <p><?= session('success') ?></p>
                                            </div>
                                            <button type="button"class="close white-text" data-dismiss="alert"
                                                    aria-label="Close">
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col s12">
                                    <div id="revenue-chart" class="card animate fadeUp">
                                        <div class="card-content">
                                            <h4 class="card-title">
                                                Cambiar clave
                                            </h4>
                                            <hr>
                                            <p>Por favor ingrese la clave actual, la nueva clave y repitala, la clave debe tener minimo 4 caracteres y maximo 32</p>
                                            <div class="row">
                                                <div class="col s12 l6 m6 x13">
                                                    <?php session() ?>
                                                    <form action="<?= base_url(['cliente', 'password', 'password_update']) ?>" method="POST">
                                                        <div class="row margin">
                                                            <div class="input-field col s12">
                                                                <i class="material-icons prefix pt-2">lock</i>
                                                                <input id="password_actual" type="password" name="password_actual">
                                                                <label for="password_actual">Contraseña actual</label>
                                                                <small class=" red-text text-darken-4"><?= $validation->getError('password_actual') ?></small>
                                                                    <small class=" red-text text-darken-4"><?= session('errors') ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="row margin">
                                                            <div class="input-field col s12">
                                                                <i class="material-icons prefix pt-2">lock_outline</i>
                                                                <input id="password_new" type="password" name="password_new">
                                                                <label for="password_new">Contraseña nueva</label>
                                                                <small class=" red-text text-darken-4"><?= $validation->getError('password_new') ?></small>
                                                            </div>
                                                        </div>
                                                        <div class="row margin">
                                                            <div class="input-field col s12">
                                                                <i class="material-icons prefix pt-2">lock_outline</i>
                                                                <input id="password_confirm" type="password" name="password_confirm">
                                                                <label for="password_confirm">Confirmar contraseña</label>
                                                                <small class=" red-text text-darken-4"><?= $validation->getError('password_confirm') ?></small>
                                                            </div>
                                                        </div>
                                                        <button class="waves-effect waves-light btn-small purple" type="submit"><i class="material-icons left">refresh</i>Actualizar</button>
                                                    </form>
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
        </div>
    </div>
<?php endif; ?>


<?= view('layouts/footer') ?>