<aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-light navbar-full sidenav-active-rounded">
    <div class="brand-sidebar color_secundario_plantilla">
        <h1 class="logo-wrapper"><a class="brand-logo darken-1" href="/GestionLabs/home" style="vertical-align: center;">
                <i class="material-icons"><?= isset(configInfo()['icon_app']) ? configInfo()['icon_app'] : '' ?> </i>
                <span class="logo-text hide-on-med-and-down"
                      style="padding-top: 10px !important; display: block; "><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : 'IPLANET' ?></span></a><a
                    class="navbar-toggler" href="#"><i class="material-icons">radio_button_checked</i></a></h1>
    </div>


    <ul
        class="sidenav  slide-out sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow"
        id="slide-out" data-menu="menu-navigation" data-collapsible="menu-accordion">
        <li>
            <div class="user-view" style="">
                <div class="background" style="margin:0px;">
                <img src="<?= base_url().'/assets/img/logo_fondo.jpg' ?>" style="width: 100%; height: 100%">
                </div>
                <a href="#" style="margin-right: 0px;"><img class="circle"  style="width: 50px; height:50px;" src="<?= session('user') && session('user')->photo ? base_url().'/assets/upload/images/'.session('user')->photo : base_url().'/assets/img/'.'user.png' ?>"></a>
                <div class="datos_sesion">
                    <a href="#" style="margin-right: 0px;">
                        <small class="black-text name" style=" font-size: 12px !important; text-shadow: 0 0 1px #000;">
                        <?= session('user')->name ? session('user')->name : session('user')->nombre ?>
                        </small>
                    </a>
                    <a href="#">
                        <p class="black-text email" style="text-shadow: 0 0 1px #000;">
                            <b><?= session('user')->usertype ? session('user')->usertype : session('user')->cms_rol  ?></b>
                        </p>
                    </a>
                </div>
            </div>
        </li>
        <li class="bold"><a
                    class="waves-effect waves-cyan <?= base_url(uri_string()) == base_url() . '/GestionLabs/home' ? 'active' : '' ?> "
                    href="<?= base_url() ?>/GestionLabs/home"><i
                        class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Calendar"> Home</span></a>
        </li>
        <?php foreach (menu("Aplicativo") as $item): ?>
            <li class="bold <?= isActive(urlOption($item->id)); ?>"><a
                        class="waves-effect waves-cyan  <?= isActive(urlOption($item->id)); ?><?= countMenu($item->id) ? 'collapsible-header' : ''; ?>"

                        href="<?= countMenu($item->id) ? urlOption() : urlOption($item->id) ?>">
                        <?= preg_match('</i>',$item->icon) ? $item->icon : '<i
                                                class="material-icons">'.$item->icon.'</i>' ?>
                        <span class="menu-title"data-i18n="Calendar"><?= $item->option ?></span></a>

                <?php if (countMenu($item->id)): ?>
                    <div class="collapsible-body">
                        <ul class="collapsible collapsible-sub" data-collapsible="accordion">
                            <?php foreach (submenu($item->id, "Aplicativo") as $submenu): ?>
                                <li class="<?= isActive(urlOption($submenu->id)); ?>"><a
                                            href="<?= urlOption($submenu->id) ?>"
                                            class="<?= isActive(urlOption($submenu->id)); ?>"><i
                                                class="material-icons">radio_button_unchecked</i><span
                                                data-i18n="Modern"><?= $submenu->option ?></span></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        <?php if(session('user')->usr_usuario): ?>
            <?php foreach (menu("Pagina") as $key => $item): ?>
                <?= $key == 0 ? '<hr>':'' ?>
                <li class="bold <?= isActive(urlOption($item->id)); ?>"><a
                            class="waves-effect waves-cyan  <?= isActive(urlOption($item->id)); ?><?= countMenu($item->id) ? 'collapsible-header' : ''; ?>"

                            href="<?= countMenu($item->id) ? urlOption() : urlOption($item->id) ?>">
                                <?= preg_match('</i>',$item->icon) ? $item->icon : '<i
                                                    class="material-icons">'.$item->icon.'</i>' ?>
                                <span class="menu-title" data-i18n="Calendar"><?= $item->option ?></span></a>

                    <?php if (countMenu($item->id)): ?>
                        <div class="collapsible-body">
                            <ul class="collapsible collapsible-sub" data-collapsible="accordion">
                                <?php foreach (submenu($item->id, "Pagina") as $submenu): ?>
                                    <li class="<?= isActive(urlOption($submenu->id)); ?>"><a
                                                href="<?= urlOption($submenu->id) ?>"
                                                class="<?= isActive(urlOption($submenu->id)); ?>"><i
                                                    class="material-icons">radio_button_unchecked</i><span
                                                    data-i18n="Modern"><?= $submenu->option ?></span></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif ?>
    </ul>


    <div class="navigation-background"></div>
    <a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only"
       href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</aside>
