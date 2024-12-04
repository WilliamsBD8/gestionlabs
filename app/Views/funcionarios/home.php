<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <!--card stats start-->
                    <div id="card-stats" class="pt-0">
                        <h3 class="center-align">Portal de Funcionarios GestionLabs</h3>
                    </div>
                </div>
                <div class="card animate fadeUp">
                    <div class="card-content">
                        <table class="centered striped">
                            <tbody>
                                <tr>
                                    <td>Fecha:</td>
                                    <td><?= date_fecha(date('Y-m-d', strtotime(session('user')->session_date))) ?></td>
                                </tr>
                                <tr>
                                    <td>Hora: </td>
                                    <td><?= date('h:i:s a', strtotime(session('user')->session_date)) ?></td>
                                </tr>
                                <tr>
                                    <td>Tipo de usuario: </td>
                                    <td><?= session('user')->cms_rol ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="content-overlay"></div>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>