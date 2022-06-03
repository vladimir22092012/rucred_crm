<!DOCTYPE html>
<html lang="en">

<head>
    <base href="{$config->root_url}/"/>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" href="/favicon.ico"/>
    <title>{$meta_title}</title>

    {if isset($canonical)}
        <link rel="canonical" href="{$canonical}"/>
    {/if}
    <!-- Bootstrap Core CSS -->
    <link href="theme/{$settings->theme|escape}/assets/plugins/bootstrap/css/bootstrap.min.css?v=1.02" rel="stylesheet">
    <!--alerts CSS -->
    <link href="theme/{$settings->theme|escape}/assets/plugins/sweetalert2/dist/sweetalert2.min.css?v=1.02"
          rel="stylesheet">
    <!-- Custom CSS -->
    {$smarty.capture.page_styles}

    <link href="theme/{$settings->theme|escape}/css/style.css?v=1.02" rel="stylesheet">
    <link href="theme/{$settings->theme|escape}/css/colors/purple.css?v=1.02" id="theme" rel="stylesheet">
    <link href="theme/{$settings->theme|escape}/css/custom.css?v=1.06" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript">
        var _front_url = '{$config->front_url}';
    </script>
    <style>
        .badge1 {
            position: relative;
        }

        .badge1[data-badge]:after {
            content: attr(data-badge);
            position: absolute;
            top: -10px;
            right: -170px;
            font-size: .6em;
            color: #ddded4;
            background-color: #f62d51;
            width: 25px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            border-radius: 15%;
        }
    </style>
</head>

<body class="fix-header fix-sidebar card-no-border">
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->


<div id="main-wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light" style="min-height:66px;">
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <div class="navbar-header">
                <a class="navbar-brand" href="/">
                    <b>
                        <!--img src="theme/{$settings->theme|escape}/assets/images/short_logo.png" alt="homepage" class="dark-logo" /-->
                    </b>
                    <span>
                     <img src="theme/{$settings->theme|escape}/assets/images/logo.png" alt="homepage"
                          class="dark-logo"/>
                    </span>
                </a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse">
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav mr-auto mt-md-0 ">
                    <!-- This is  -->
                    <li class="nav-item"><a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                                            href="javascript:void(0)"><i class="ti-menu"></i></a></li>
                    <li class="nav-item"><a
                                class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                                href="javascript:void(0)"><i class="icon-arrow-left-circle"></i></a></li>

                    {if isset($manager)}
                        {if in_array('notifications', $manager->permissions) || in_array('penalties', $manager->permissions)}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark"
                                   href=""
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-message"></i>
                                    {if $active_notifications || $penalty_notifications}
                                        <div class="notify">
                                            <span class="heartbit"></span>
                                            <span class="point"></span>
                                        </div>
                                    {/if}
                                </a>
                                <div class="dropdown-menu mailbox animated bounceInDown">
                                    <ul>
                                        {if $active_notifications}
                                            <li>
                                                <div class="drop-title">Напоминания</div>
                                            </li>
                                            <li>
                                                <div class="message-center">
                                                    <!-- Message -->
                                                    {foreach $active_notifications as $an}
                                                        <a href="sudblock_notifications">
                                                            <div class="btn btn-danger btn-circle">
                                                                !
                                                            </div>
                                                            <div class="mail-contnet">
                                                                <h5>{$an->event->action}</h5>
                                                                <span class="mail-desc">{$an->comment}</span>
                                                                <span class="time">{$an->notification_date|date}</span>
                                                            </div>
                                                        </a>
                                                    {/foreach}
                                                    <!-- Message -->
                                                </div>
                                            </li>
                                        {/if}

                                        {if $penalty_notifications}
                                            <li>
                                                <div class="drop-title">Штрафы</div>
                                            </li>
                                            <li>
                                                <div class="message-center">
                                                    <!-- Message -->
                                                    {foreach $penalty_notifications as $pn}
                                                        <a href="order/{$pn->order_id}">
                                                            <div class="btn btn-danger btn-circle">
                                                                <i class="mdi-alert-octagon mdi"></i>
                                                            </div>
                                                            <div class="mail-contnet">
                                                                <h5>{$pn->type->name}</h5>
                                                                <span class="mail-desc">{$pn->comment}</span>
                                                                <span class="time">{$pn->created|date} {$pn->created|time}</span>
                                                            </div>
                                                        </a>
                                                    {/foreach}
                                                    <!-- Message -->
                                                </div>
                                            </li>
                                        {/if}
                                    </ul>
                                </div>
                            </li>
                        {/if}
                    {/if}

                </ul>

                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                {if isset($manager)}
                    <ul class="navbar-nav my-lg-0">

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-user-circle"></i>
                                {$manager->name|escape}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated flipInY">
                                <ul class="dropdown-user">
                                    <li>
                                        <div class="dw-user-box">
                                            <div class="u-text">
                                                <h4>{$manager->name|escape}</h4>
                                                <p class="text-muted">{$manager->email}</p>
                                                {if $manager->role == 'developer'}<span
                                                        class="badge badge-danger">{$manager->role}</span>
                                                {elseif $manager->role == 'admin'}<span
                                                        class="badge badge-success">{$manager->role}</span>
                                                {elseif $manager->role == 'manager'}<span
                                                        class="badge badge-primary">{$manager->role}</span>
                                                {else}<span class="badge badge-info">{$manager->role}</span>{/if}
                                            </div>
                                        </div>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="manager/{$manager->id}?main='true'"><i class="ti-user"></i> Профиль</a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="logout"><i class="fa fa-power-off"></i> Выход</a></li>
                                </ul>
                            </div>
                        </li>

                    </ul>
                {/if}
            </div>
        </nav>
    </header>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar">
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
                {if isset($manager)}
                    <ul id="sidebarnav">
                        <li {if in_array($module, ['TicketsController'])}class="active"{/if}>
                        <li class="nav-small-cap">Коммуникации</li>
                        <li {if in_array($module, ['TicketsController'])}class="active"{/if}>
                            <a href="tickets?in=true"><i class="mdi mdi-email-open badge1" {if $count_in != 0}data-badge="{$count_in}"{/if}></i><span>Входящие запросы</span></a>
                        </li>
                        <li {if in_array($module, ['TicketsController'])}class="active"{/if}><a
                                    href="tickets?out=true"><i class="mdi mdi-email-variant badge1" {if $count_out != 0}data-badge="{$count_out}"{/if}></i><span>Исходящие запросы</span></a>
                        </li>
                        <li {if in_array($module, ['TicketsController'])}class="active"{/if}><a
                                    href="tickets?archive=true"><i class="mdi mdi-mailbox"></i><span>Архив запросов</span></a>
                        </li>
                        <li class="nav-small-cap">Онлайн заявки</li>
                        <li {if in_array($module, ['OrderController', 'OrdersController'])}class="active"{/if}>
                            <a class="" href="orders/" aria-expanded="false"><i class="mdi mdi-animation"></i><span
                                        class="hide-menu">Заявки</span></a>
                        </li>
                        <li {if in_array($module, ['ClientController', 'ClientsController'])}class="active"{/if}>
                            <a class="" href="clients/" aria-expanded="false"><i
                                        class="mdi mdi-chart-bubble"></i><span
                                        class="hide-menu">Клиенты</span></a>
                        </li>
                        {if $manager->role != 'employer'}
                            <li {if in_array($module, ['MissingController'])}class="active"{/if}>
                                <a class="" href="missing/" aria-expanded="false"><i class="mdi mdi-sleep"></i><span
                                            class="hide-menu">Отвалы</span></a>
                            </li>
                        {/if}
                        {if in_array('offline_settings', $manager->permissions) || in_array('offline', $manager->permissions)}
                            <li class="nav-small-cap">Оффлайн заявки</li>
                            {if in_array('offline', $manager->permissions)}
                                <li {if isset($offline)}class="active"{/if}>
                                    <a class="" href="offline_orders/" aria-expanded="false"><i
                                                class="mdi mdi-animation"></i><span class="hide-menu">Заявки</span></a>
                                </li>
                            {/if}
                            {if in_array('offline', $manager->permissions) && $manager->role != 'employer'}
                                <li{if in_array($module, ['OfflineOrdersController'])} class="active"{/if}>
                                    <a class="" href="drafts/" aria-expanded="false"><i
                                                class="mdi mdi-database"></i><span
                                                class="hide-menu">Черновики</span></a>
                                </li>
                            {/if}

                        {/if}
                        {if in_array('managers', $manager->permissions)}
                            <li class="nav-small-cap">Сделки</li>
                            <li {if in_array($module, ['RegistrController'])}class="active"{/if}>
                                <a class="" href="registr/" aria-expanded="false"><i
                                            class="mdi mdi-book-open-page-variant"></i><span class="hide-menu">Реестр сделок</span></a>
                            </li>
                            <li {if in_array($module, ['RegistrController'])}class="active"{/if}>
                                <a class="" href="registr?monthly='true'" aria-expanded="false"><i
                                            class="mdi mdi-calendar"></i><span class="hide-menu">Сводные реестры</span></a>
                            </li>
                        {/if}
                        {if in_array('managers', $manager->permissions) && $manager->role != 'employer'}
                            <li class="nav-small-cap">Администрирование</li>
                            <li {if in_array($module, ['ManagerController', 'ManagersController'])}class="active"{/if}>
                                <a class="" href="managers/" aria-expanded="false"><i
                                            class="mdi mdi-account-multiple-outline"></i><span class="hide-menu">Пользователи</span></a>
                            </li>
                        {/if}
                        {if in_array('managers', $manager->permissions)}
                            <li {if in_array($module, ['ChangelogsController'])}class="active"{/if}>
                                <a class="" href="changelogs/" aria-expanded="false"><i
                                            class="mdi mdi-book-open-page-variant"></i><span
                                            class="hide-menu">Логирование</span></a>
                            </li>
                        {/if}
                        {if in_array('managers', $manager->permissions)}
                            <li class="nav-small-cap">Управление</li>
                            {if $manager->role != 'employer'}
                                <li {if in_array($module, ['GroupsController'])}class="active"{/if}><a
                                            href="groups"><i class="mdi mdi-group"></i>Группы</a></li>
                            {/if}
                            <li {if in_array($module, ['CompaniesController'])}class="active"{/if}><a
                                        href="companies"><i class="mdi mdi-compass"></i>Компании</a></li>
                            <li {if in_array($module, ['LoantypesController','LoantypeController'])}class="active"{/if}>
                                <a href="loantypes"><i class="mdi mdi-magnet"></i>Продукты</a></li>
                            <li {if in_array($module, ['WhitelistController'])}class="active"{/if}>
                                <a href="/whitelist"><i class="mdi mdi-tooltip"></i>Blacklist</a></li>
                            <li {if in_array($module, ['BlacklistController'])}class="active"{/if}>
                                <a href="/blacklist"><i class="mdi mdi-tooltip"></i>Whitelist</a></li>
                            <li {if in_array($module, ['ThemesController'])}class="active"{/if}>
                                <a href="/themes"><i class="mdi mdi-chart-arc"></i>Справочник тем КП</a></li>
                        {/if}
                        {if in_array('managers', $manager->permissions) && $manager->role != 'employer'}
                            <li class="nav-small-cap">Настройки</li>
                            {if !in_array($manager->role, ['employer', 'underwriter'])}
                                <li {if in_array($module, ['SettingsController'])}class="active"{/if}><a
                                            href="settings/"><i class="mdi mdi-settings"></i>Общие</a></li>
                            {/if}
                            <li {if in_array($module, ['ScoringsController'])}class="active"{/if}>
                                <a href="scorings/"><i class="mdi mdi-tooltip"></i>СПР</a></li>
                            <li {if in_array($module, ['ReasonsController'])}class="active"{/if}><a
                                        href="reasons/"><i class="mdi mdi-react"></i>Причины отказа</a></li>
                            <li {if in_array($module, ['SmsTemplatesController'])}class="active"{/if}><a
                                        href="sms_templates"><i class="mdi mdi-sigma"></i>Шаблоны сообщений</a></li>
                            {if !in_array($manager->role, ['employer', 'underwriter'])}
                                <li {if in_array($module, ['ApikeysController'])}class="active"{/if}><a
                                            href="apikeys/"><i class="mdi mdi-apple-finder"></i>Ключи для API</a></li>
                            {/if}
                        {/if}
                    </ul>
                {/if}
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
    <!-- ============================================================== -->
    <!-- End Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->


    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    {$content}
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->

</div>

<div id="sms_code_modal"></div>

<script src="theme/{$settings->theme|escape}/assets/plugins/jquery/jquery.min.js?v=1.01"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/popper.min.js?v=1.02"></script>
<script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/bootstrap.js?v=1.01"></script>

<script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.min.js?v=1.01"></script>
<link rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css?v=1.01"/>

<!-- slimscrollbar scrollbar JavaScript -->
<script src="theme/{$settings->theme|escape}/js/jquery.slimscroll.js?v=1.01"></script>
<!--Wave Effects -->
<script src="theme/{$settings->theme|escape}/js/waves.js?v=1.01"></script>
<!--Menu sidebar -->
<script src="theme/{$settings->theme|escape}/js/sidebarmenu.js?v=1.01"></script>
<!--sticky kit -->
<script src="theme/{$settings->theme|escape}/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js?v=1.01"></script>

<script src="theme/{$settings->theme|escape}/assets/plugins/sweetalert2/dist/sweetalert2.all.min.js?v=1.01"></script>
<!--Custom JavaScript -->
<script src="theme/{$settings->theme|escape}/js/custom.min.js?v=1.01"></script>
<!-- ============================================================== -->

<link rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/autocomplete/styles.css?v=1.01"/>
<script src="theme/{$settings->theme|escape}/assets/plugins/autocomplete/jquery.autocomplete-min.js?v=1.01"></script>
<script src="theme/{$settings->theme|escape}/js/apps/dadata.app.js?v=1.04"></script>

<script src="theme/{$settings->theme|escape}/js/apps/run_scorings.app.js?v=1.01"></script>
<script src="theme/{$settings->theme|escape}/js/apps/sms.app.js?v=1.01"></script>

<script src="theme/{$settings->theme|escape}/js/apps/eventlogs.app.js?v=1.01"></script>

<script src="theme/{$settings->theme|escape}/js/apps/connexions.app.js?v=1.10"></script>

{$smarty.capture.page_scripts}
<!-- Style switcher -->
<!-- ============================================================== -->
<script src="theme/{$settings->theme|escape}/assets/plugins/styleswitcher/jQuery.style.switcher.js"></script>


</body>

</html>
