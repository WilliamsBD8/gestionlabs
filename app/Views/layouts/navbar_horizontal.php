<header class="page-topbar" id="header">
    <div class="navbar navbar-fixed">
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-dark gradient-45deg-purple-deep-orange gradient-shadow">
            <div class="nav-wrapper">
                <ul class="navbar-list right">
                    <li class="hide-on-large-only search-input-wrapper"><a class="waves-effect waves-block waves-light search-button" href="javascript:void(0);"><i class="material-icons">search</i></a></li>
                    <li><a class="waves-effect waves-block waves-light notification-button" href="javascript:void(0);"
                           data-target="notifications-dropdown"><i class="material-icons">notifications_none
                                <?php if(countNotification() > 0): ?>
                                    <small class="notification-badge"><?= countNotification() ?></small>
                                <?php endif; ?>
                            </i></a></li>
                    <li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);"data-target="profile-dropdown">
                        <span class="avatar-status avatar-online">
                            <img  style="height: 29px !important;" src="
                                <?php if(session('user')->funcionario): ?>
                                    <?= session('user') && session('user')->usr_foto ? base_url().'/upload/images/'.session('user')->usr_foto : base_url().'/assets/img/'.'user.png' ?>" alt="avatar">
                                <?php else: ?>
                                    <?= session('user') && session('user')->photo ? base_url().'/upload/images/'.session('user')->photo : base_url().'/assets/img/'.'user.png' ?>" alt="avatar">
                                <?php endif ?>
                            <i></i>
                            </span>
                            <small style="float: right; padding-left: 10px; font-size: 16px;"
                                   class="new badge">
                                <?= session('user')->username ? session('user')->username : session('user')->usr_usuario ?>     
                            </small>

                          </a>
                    </li>
                </ul>
                <ul class="dropdown-content" id="notifications-dropdown">
                    <li>
                        <h6>NOTIFICACIONES</h6>
                    </li>
                    <li class="divider"></li>
                    <?php foreach (notification() as $item): ?>
                        <li><a class="black-text" href="#!"><span class="material-icons icon-bg-circle <?= $item['color'] ?> small"><?= $item['icon'] ?></span>
                                <?= $item['title'] ?></a>
                            <time class="media-meta grey-text darken-2"><?= $item['body'] ?></time>
                            <br>
                            <time class="media-meta grey-text darken-2" datetime="2015-06-12T20:50:48+08:00"><?= different($item['created_at']) ?>
                            </time>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <!-- profile-dropdown-->
                <ul class="dropdown-content" id="profile-dropdown">
                    <li><a class="grey-text text-darken-1" href="<?= base_url() ?>/GestionLabs/perfile"><i class="material-icons">person_outline</i>
                            Perfil</a></li>
                    <?php if(session('user')->usr_rol == 2 ): ?>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/<?= session('user')->usr_usuario ? 'cms_users' : 'usuario' ?>">
                            <i class="material-icons">peoples</i>
                                Usuarios</a></li>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/cms_firma">
                            <i class="material-icons">border_color</i>
                                Firmas</a></li>
                    <?php endif ?>
                    <li><a class="grey-text text-darken-1" href="<?= base_url() ?>/GestionLabs/about"><i class="material-icons">help_outline</i> About</a></li>
                    <?php  if(session('user')->usertype == 'Administrador' || session('user')->usr_rol == 1): ?>

                        <?php if (session('user')->usr_usuario): ?>
                            <li><a class="grey-text text-darken-1" href="<?= base_url() ?>/config/configurations"><i class="material-icons">settings</i>
                                    Configure</a></li>
                            <li><a class="grey-text text-darken-1" href="<?= base_url() ?>/config/cms_rol"><i class="material-icons">face</i>
                                Roles</a></li>
                        <?php endif ?>

                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/<?= session('user')->usr_usuario ? 'cms_users' : 'usuario' ?>">
                            <i class="material-icons">peoples</i>
                                Usuarios</a></li>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/cms_firma">
                            <i class="material-icons">border_color</i>
                                Firmas</a></li>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/<?= session('user')->usr_usuario ? 'menus_funcionarios' : 'menus_cliente' ?>">
                            <i class="material-icons">menu</i>
                                Menu</a></li>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/<?= session('user')->usr_usuario ? 'permissions_funcionarios' : 'permissions_cliente' ?>">
                            <i class="material-icons">lock_outline</i>
                                Permisos</a></li>
                        <li><a class="grey-text text-darken-1"
                            href="<?= base_url() ?>/config/<?= session('user')->usr_usuario ? 'notifications_funcionarios' : 'notifications_cliente' ?>">
                            <i class="material-icons">contact_mail</i>
                                Notificar</a></li>

                        <li class="divider"></li>
                    <?php  endif; ?>

                    <li><a class="grey-text text-darken-1" href="<?= base_url() ?>/logout"><i
                                    class="material-icons">keyboard_tab</i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>
