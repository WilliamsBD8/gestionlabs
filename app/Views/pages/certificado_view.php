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
                                                Certificado <?= $id ?>
                                            </h4>
                                            <div class="row">
                                                <div class="col s12 l12 m12 x13">
                                                    <?php session() ?>
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