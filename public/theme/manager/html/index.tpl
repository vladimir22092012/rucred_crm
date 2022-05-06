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
                                <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href=""
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
                                <li><a href="manager/{$manager->id}"><i class="ti-user"></i> Профиль</a></li>
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
                    {if !in_array($manager->role, ['underwriter', 'employer'])}
                    <li class="nav-small-cap">Онлайн заявки</li>

                    <li {if in_array($module, ['OrderController', 'OrdersController'])}class="active"{/if}>
                        <a class="" href="orders/" aria-expanded="false"><i class="mdi mdi-animation"></i><span
                                    class="hide-menu">Заявки</span></a>
                    </li>
                    {/if}
                    {if !in_array($manager->role, ['employer'])}
                    <li {if in_array($module, ['ClientController', 'ClientsController'])}class="active"{/if}>
                        <a class="" href="clients/" aria-expanded="false"><i
                                    class="mdi mdi-chart-bubble"></i><span
                                    class="hide-menu">Клиенты</span></a>
                    </li>
                    {/if}
                    {*
                    <li {if in_array($module, ['MissingController'])}class="active"{/if}>
                        <a class="" href="missing/" aria-expanded="false"><i class="mdi mdi-sleep"></i><span
                                    class="hide-menu">Отвалы</span></a>
                    </li>
                    *}
                    {if in_array('offline_settings', $manager->permissions) || in_array('offline', $manager->permissions)}
                        <li class="nav-small-cap">Оффлайн заявки</li>
                        {if in_array('offline', $manager->permissions)}
                            <li {if $offline}class="active"{/if}>
                                <a class="" href="offline_orders/" aria-expanded="false"><i
                                            class="mdi mdi-animation"></i><span class="hide-menu">Заявки</span></a>
                            </li>
                        {/if}
                        {if in_array('offline', $manager->permissions) && $manager->role != 'employer'}
                            <li{if in_array($module, ['OfflineOrdersController'])} class="active"{/if}>
                                <a class="" href="drafts/" aria-expanded="false"><i class="mdi mdi-database"></i><span class="hide-menu">Черновики</span></a>
                            </li>
                        {/if}

                    {/if}
                    {if in_array('managers', $manager->permissions)}
                        <li class="nav-small-cap">Управление</li>
                        <li {if in_array($module, ['ManagerController', 'ManagersController'])}class="active"{/if}>
                            <a class="" href="managers/" aria-expanded="false"><i
                                        class="mdi mdi-account-multiple-outline"></i><span class="hide-menu">Пользователи</span></a>
                        </li>
                    {/if}
                    {if in_array('changelogs', $manager->permissions)}
                        <li {if in_array($module, ['ChangelogsController'])}class="active"{/if}>
                            <a class="" href="changelogs/" aria-expanded="false"><i
                                        class="mdi mdi-book-open-page-variant"></i><span
                                        class="hide-menu">Логирование</span></a>
                        </li>
                    {/if}
                    {if in_array('settings', $manager->permissions) || in_array('offline_settings', $manager->permissions) && $manager->role != 'underwriter'}
                        <li {if in_array($module, ['SettingsController', 'OfflinePointsController', 'ScoringsController', 'ApikeysController', 'WhitelistController', 'BlacklistController', 'PenaltyTypesController'])}class="active"{/if}>
                            <a class="has-arrow" href="settings" aria-expanded="false"><i
                                        class="mdi mdi-settings"></i><span class="hide-menu">Настройки</span></a>
                            <ul aria-expanded="false" class="collapse">
                                {if in_array('settings', $manager->permissions)}
                                {*}
                                <li {if in_array($module, ['SettingsController'])}class="active"{/if}><a
                                            href="settings/">Общие</a></li>
                                            {*}
                                <li {if in_array($module, ['ScoringsController'])}class="active"{/if}><a
                                            href="scorings/">Скоринги</a></li>
                                <li {if in_array($module, ['ApikeysController'])}class="active"{/if}><a
                                            href="apikeys/">Ключи для API</a></li>
                                {*}
                            <li {if in_array($module, ['WhitelistController'])}class="active"{/if}><a
                                        href="whitelist/">Whitelist</a></li>
                            <li {if in_array($module, ['BlacklistController'])}class="active"{/if}><a
                                        href="blacklist/">Blacklist</a></li>
                                        {*}
                        {/if}
                    </ul>
                </li>
            {/if}
                    {if in_array('handbooks', $manager->permissions) || in_array('sms_templates', $manager->permissions) || in_array('tags', $manager->permissions) || in_array('communications', $manager->permissions)}
                <li {if in_array($module, ['HandbooksController', 'ReasonsController', 'SmsTemplatesController', 'SettingsCommunicationsController', 'TicketStatusesController', 'TicketReasonsController'])}class="active"{/if}>
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="mdi mdi-database"></i><span
                                class="hide-menu">Справочники</span></a>
                    <ul aria-expanded="false" class="collapse">
                        {if in_array('handbooks', $manager->permissions)}
                            <li {if in_array($module, ['ReasonsController'])}class="active"{/if}><a
                                        href="reasons/">Причины отказа</a></li>
                        {/if}
                        {if in_array('sms_templates', $manager->permissions)}
                            <li {if in_array($module, ['SmsTemplatesController'])}class="active"{/if}><a
                                        href="sms_templates">Шаблоны сообщений</a></li>
                        {/if}
                        <li {if in_array($module, ['GroupsController'])}class="active"{/if}><a
                                    href="groups">Группы</a></li>

                        <li {if in_array($module, ['CompaniesController'])}class="active"{/if}><a
                                    href="companies">Компании</a></li>

                        <li {if in_array($module, ['LoantypesController','LoantypeController'])}class="active"{/if}>
                            <a href="loantypes">Продукты</a></li>
                    </ul>
                </li>
            {/if}
                    {*
            {if in_array('pages', $manager->permissions)}
                <li {if in_array($module, ['PageController', 'PagesController'])}class="active"{/if}>
                    <a class="" href="pages" aria-expanded="false"><i class="mdi mdi-application"></i><span
                                class="hide-menu">Страницы сайта</span></a>
                </li>
            {/if}

                    {if in_array('analitics', $manager->permissions) || in_array('penalty_statistics', $manager->permissions)}
                        <li class="nav-small-cap">Аналитика</li>
                        <li {if in_array($module, ['DashboardController'])}class="active"{/if}>
                            <a class="" href="dashboard" aria-expanded="false"><i class="mdi mdi-gauge"></i><span
                                        class="hide-menu">Dashboard</span></a>
                        </li>
                        <li {if in_array($module, ['StatisticsController'])}class="active"{/if}>
                            <a class="" href="statistics" aria-expanded="false"><i class="mdi mdi-file-chart"></i><span
                                        class="hide-menu">Статистика</span></a>
                        </li>
                    {/if}





                                        {if in_array('orders', $manager->permissions) || in_array('clients', $manager->permissions) || in_array('offline', $manager->permissions) || in_array('penalties', $manager->permissions)}
                                        <li class="nav-small-cap">Основные</li>

                                        {if in_array('orders', $manager->permissions)}
                                        <li {if !$offline && in_array($module, ['OrderController', 'OrdersController'])}class="active"{/if}>
                                            <a class="" href="orders/" aria-expanded="false"><i class="mdi mdi-animation"></i><span class="hide-menu">Заявки</span></a>
                                        </li>
                                        {/if}

                                        {if in_array('clients', $manager->permissions)}
                                        <li {if in_array($module, ['ClientController', 'ClientsController'])}class="active"{/if}>
                                            <a class="" href="clients/" aria-expanded="false"><i class="mdi mdi-chart-bubble"></i><span class="hide-menu">Клиенты</span></a>
                                        </li>
                                        {/if}

                                        {if in_array('penalties', $manager->permissions)}
                                        <li {if in_array($module, ['PenaltiesController'])}class="active"{/if}>
                                            <a class="" href="penalties" aria-expanded="false"><i class="mdi mdi-alert-octagon"></i><span class="hide-menu">Штрафы</span></a>
                                        </li>
                                        {/if}

                                        {/if}



                                        {if in_array('my_contracts', $manager->permissions) || in_array('collection_report', $manager->permissions) || in_array('zvonobot', $manager->permissions)}
                                        <li class="nav-small-cap">Коллекшин</li>

                                        {if in_array('my_contracts', $manager->permissions)}
                                        <li {if in_array($module, ['CollectorContractsController'])}class="active"{/if}>
                                            <a class="" href="my_contracts/" aria-expanded="false"><i class="mdi mdi-book-multiple"></i><span class="hide-menu">Мои договоры</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('collection_report', $manager->permissions)}
                                        <li {if in_array($module, ['CollectionReportController'])}class="active"{/if}>
                                            <a class="" href="collection_report/" aria-expanded="false"><i class="mdi mdi-chart-histogram"></i><span class="hide-menu">Отчет</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('collection_moving', $manager->permissions)}
                                        <li {if in_array($module, ['CollectorClientsController'])}class="active"{/if}>
                                            <a class="" href="collector_clients" aria-expanded="false"><i class="mdi mdi-chart-histogram"></i><span class="hide-menu">Перебросы клиентов</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('zvonobot', $manager->permissions)}
                                        <li {if in_array($module, ['ZvonobotController'])}class="active"{/if}>
                                            <a class="" href="zvonobot" aria-expanded="false"><i class="mdi mdi-deskphone"></i><span class="hide-menu">Звонобот</span></a>
                                        </li>
                                        {/if}

                                        <li {if in_array($module, ['NotificationsController'])}class="active"{/if}>
                                            <a class="" href="collection_notifications" aria-expanded="false"><i class="mdi-note-multiple-outline mdi"></i><span class="hide-menu">Напоминания</span></a>
                                        </li>

                                        {if in_array('collector_mailing', $manager->permissions)}
                                        <li {if in_array($module, ['MailingController'])}class="active"{/if}>
                                            <a class="" href="mailing/list" aria-expanded="false"><i class="mdi mdi-voicemail"></i><span class="hide-menu">Рассылка</span></a>
                                        </li>
                                        {/if}

                                        {/if}

                                        {if in_array('sudblock', $manager->permissions) || in_array('sudblock_settings', $manager->permissions)}
                                        <li class="nav-small-cap">Судблок</li>
                                        {if in_array('sudblock', $manager->permissions)}
                                        <li {if in_array($module, ['SudblockContractsController'])}class="active"{/if}>
                                            <a class="" href="sudblock_contracts" aria-expanded="false"><i class="mdi mdi-clipboard"></i><span class="hide-menu">Мои договоры</span></a>
                                        </li>
                                        <li {if in_array($module, ['SudblockNotificationsController'])}class="active"{/if}>
                                            <a class="" href="sudblock_notifications" aria-expanded="false"><i class="mdi-note-multiple-outline mdi"></i><span class="hide-menu">Напоминания</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('sudblock_settings', $manager->permissions)}
                                        <li {if in_array($module, ['SudblockStatusesController', 'SudblockDocumentsController'])}class="active"{/if}>
                                            <a class="has-arrow" href="settings" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Справочники</span></a>
                                            <ul aria-expanded="false" class="collapse">
                                                <li {if in_array($module, ['SudblockStatusesController'])}class="active"{/if}><a href="sudblock_statuses">Статусы</a></li>
                                                <li {if in_array($module, ['SudblockDocumentsController'])}class="active"{/if}><a href="sudblock_documents">Документы</a></li>
                                            </ul>
                                        </li>
                                        {/if}

                                        {/if}

                                        {if  in_array('managers', $manager->permissions) ||  in_array('changelogs', $manager->permissions) ||  in_array('settings', $manager->permissions) ||  in_array('handbooks', $manager->permissions) ||  in_array('pages', $manager->permissions)}
                                        <li class="nav-small-cap">Управление</li>
                                        {if in_array('managers', $manager->permissions)}
                                        <li {if in_array($module, ['ManagerController', 'ManagersController'])}class="active"{/if}>
                                            <a class="" href="managers/" aria-expanded="false"><i class="mdi mdi-account-multiple-outline"></i><span class="hide-menu">Пользователи</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('changelogs', $manager->permissions)}
                                        <li {if in_array($module, ['ChangelogsController'])}class="active"{/if}>
                                            <a class="" href="changelogs/" aria-expanded="false"><i class="mdi mdi-book-open-page-variant"></i><span class="hide-menu">Логирование</span></a>
                                        </li>
                                        {/if}
                                        {if in_array('settings', $manager->permissions) || in_array('offline_settings', $manager->permissions)}
                                        <li {if in_array($module, ['SettingsController', 'OfflinePointsController', 'ScoringsController', 'ApikeysController', 'WhitelistController', 'BlacklistController', 'PenaltyTypesController'])}class="active"{/if}>
                                            <a class="has-arrow" href="settings" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Настройки</span></a>
                                            <ul aria-expanded="false" class="collapse">
                                                {if in_array('settings', $manager->permissions)}
                                                <li {if in_array($module, ['SettingsController'])}class="active"{/if}><a href="settings/">Общие</a></li>
                                                <li {if in_array($module, ['ScoringsController'])}class="active"{/if}><a href="scorings/">Скоринги</a></li>
                                                <li {if in_array($module, ['ApikeysController'])}class="active"{/if}><a href="apikeys/">Ключи для API</a></li>
                                                <li {if in_array($module, ['WhitelistController'])}class="active"{/if}><a href="whitelist/">Whitelist</a></li>
                                                <li {if in_array($module, ['BlacklistController'])}class="active"{/if}><a href="blacklist/">Blacklist</a></li>
                                                <li {if in_array($module, ['PenaltyTypesController'])}class="active"{/if}><a href="penalty_types">Штрафы</a></li>
                                                <li {if in_array($module, ['CollectionPeriodsController'])}class="active"{/if}><a href="collection_periods">Периоды коллекшина</a></li>
                                                {/if}
                                            </ul>
                                        </li>
                                        {/if}
                                        {if in_array('handbooks', $manager->permissions) || in_array('sms_templates', $manager->permissions) || in_array('tags', $manager->permissions) || in_array('communications', $manager->permissions)}
                                        <li {if in_array($module, ['HandbooksController', 'ReasonsController', 'SmsTemplatesController', 'SettingsCommunicationsController', 'TicketStatusesController', 'TicketReasonsController'])}class="active"{/if}>
                                            <a class="has-arrow" href="#" aria-expanded="false"><i class="mdi mdi-database"></i><span class="hide-menu">Справочники</span></a>
                                            <ul aria-expanded="false" class="collapse">
                                                {if in_array('handbooks', $manager->permissions)}
                                                <li {if in_array($module, ['ReasonsController'])}class="active"{/if}><a href="reasons/">Причины отказа</a></li>
                                                {/if}
                                                {if in_array('sms_templates', $manager->permissions)}
                                                <li {if in_array($module, ['SmsTemplatesController'])}class="active"{/if}><a href="sms_templates">Шаблоны сообщений</a></li>
                                                {/if}
                                                {if in_array('tags', $manager->permissions)}
                                                <li {if in_array($module, ['CollectorTagsController'])}class="active"{/if}><a href="collector_tags">Теги для коллекторов</a></li>
                                                {/if}
                                                {if in_array('communications', $manager->permissions)}
                                                <li {if in_array($module, ['SettingsCommunicationsController'])}class="active"{/if}><a href="settings_communications">Лимиты коммуникаций</a></li>
                                                {/if}
                                                {if in_array('ticket_handbooks', $manager->permissions)}
                                                <li {if in_array($module, ['TicketStatusesController'])}class="active"{/if}><a href="ticket_statuses">Статусы тикетов</a></li>
                                                <li {if in_array($module, ['TicketReasonsController'])}class="active"{/if}><a href="ticket_reasons">Причины закрытия тикетов</a></li>
                                                {/if}
                                            </ul>
                                        </li>
                                        {/if}
                                        {if in_array('pages', $manager->permissions)}
                                        <li {if in_array($module, ['PageController', 'PagesController'])}class="active"{/if}>
                                            <a class="" href="pages" aria-expanded="false"><i class="mdi mdi-application"></i><span class="hide-menu">Страницы</span></a>
                                        </li>
                                        {/if}
                                        {/if}

                                        {if in_array('analitics', $manager->permissions) || in_array('penalty_statistics', $manager->permissions)}
                                        <li class="nav-small-cap">Аналитика</li>


                                        <li {if in_array($module, ['DashboardController'])}class="active"{/if}>
                                            <a class="" href="dashboard" aria-expanded="false"><i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</span></a>
                                        </li>
                                        <li {if in_array($module, ['StatisticsController'])}class="active"{/if}>
                                            <a class="" href="statistics" aria-expanded="false"><i class="mdi mdi-file-chart"></i><span class="hide-menu">Статистика</span></a>
                                        </li>
                                        {/if}
                    *}
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
<!--stickey kit -->
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
