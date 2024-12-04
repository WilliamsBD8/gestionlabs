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
                                <div class="col s12 m12 l12">
                                    <div class="card animate fadeUp">
                                        <div class="card-content">
                                            <h2 class="card-title">
                                                Acreditaciones 
                                            </h2>
                                            <table class="striped centered responsive-table">
                                              <thead>
                                                <tr>
                                                    <th>Titulo</th>
                                                    <th>Documento</th>
                                                </tr>
                                              </thead>

                                              <tbody>
                                                <?php foreach($accreditations as $value): ?>
                                                    <?php if(!empty($value->document)): ?>
                                                      <tr>
                                                        <td><?= $value->title ?></td>
                                                        <td>
                                                          <form action="<?= base_url(['cliente', 'accreditations']) ?>" method="POST">
                                                            <input type="hidden" name="accreditation" value="<?= $value->id ?>">
                                                            <button class="btn" type="submit"><i class="fad fa-file-download"></i> <?= $value->document ?></button>
                                                          </form>
                                                        </a></td>
                                                      </tr>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="content-overlay"></div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= view('layouts/footer') ?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
