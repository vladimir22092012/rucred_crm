{$meta_title='Оффлайн заявки' scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/orders.js?v=1.11"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/order.js?v=1.16"></script>
{/capture}

{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .jsgrid-table {
            margin-bottom: 0
        }

        .label {
            white-space: pre;
        }

        .workout-row > td {
            background: #b2ffaf !important;
        }

        .workout-row a, .workout-row small, .workout-row span {
            color: #555 !important;
            font-weight: 300;
        }
    </style>
{/capture}

<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Оффлайн заявки</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Оффлайн заявки</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                <div class="row">

                    <div class="col-6 text-right">
                        {if in_array('neworder', $manager->permissions)}
                            <a href="neworder" class="btn btn-success btn-large">
                                <i class="fas fa-plus-circle"></i>
                                <span>Новая заявка</span>
                            </a>
                        {/if}
                    </div>

                    <div class="col-6 dropdown text-right hidden-sm-down js-period-filter">
                        <input type="hidden" value="{$period}" id="filter_period"/>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i>
                            {if $period == 'today'}Сегодня
                            {elseif $period == 'yesterday'}Вчера
                            {elseif $period == 'week'}На этой неделе
                            {elseif $period == 'month'}В этом месяце
                            {elseif $period == 'year'}В этом году
                            {elseif $period == 'all'}За все время
                            {elseif $period == 'optional'}Произвольный
                            {else}{$period}{/if}

                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item js-period-link {if $period == 'today'}active{/if}"
                               href="{url period='today' page=null}">Сегодня</a>
                            <a class="dropdown-item js-period-link {if $period == 'yesterday'}active{/if}"
                               href="{url period='yesterday' page=null}">Вчера</a>
                            <a class="dropdown-item js-period-link {if $period == 'month'}active{/if}"
                               href="{url period='month' page=null}">В этом месяце</a>
                            <a class="dropdown-item js-period-link {if $period == 'year'}active{/if}"
                               href="{url period='year' page=null}">В этом году</a>
                            <a class="dropdown-item js-period-link {if $period == 'all'}active{/if}"
                               href="{url period='all' page=null}">За все время</a>
                            <a class="dropdown-item js-open-daterange {if $period == 'optional'}active{/if}"
                               href="{url period='optional' page=null}">Произвольный</a>
                        </div>

                        <div class="js-daterange-filter input-group mt-3"
                             {if $period!='optional'}style="display:none"{/if}>
                            <input type="text" name="daterange" class="form-control daterange js-daterange-input"
                                   value="{if $from && $to}{$from}-{$to}{/if}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <span class="ti-calendar"></span>
                                </span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <!-- Column -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Список заявок </h4>
                        <div style="display: flex; justify-content: space-between;">
                            {if $manager->role == 'employer'}
                                <div class="js-filter-status">
                                    <a href="{if $filter_status=='new'}{url status=null page=null}{else}{url status='new' page=null}{/if}"
                                       class="btn btn-xs {if $filter_status=='new'}btn-warning{else}btn-outline-warning{/if}">Новая</a>

                                    <a href="{if $filter_status==13}{url status=null page=null}{else}{url status=13 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==13}btn-warning{else}btn-outline-warning{/if}">Р.Нецелесообразно</a>

                                    <a href="{if $filter_status==14}{url status=null page=null}{else}{url status=14 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==14}btn-success{else}btn-outline-success{/if}">Р.Подтверждена</a>

                                    <a href="{if $filter_status==15}{url status=null page=null}{else}{url status=15 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==15}btn-danger{else}btn-outline-danger{/if}">Р.Отклонена</a>
                                    {if $filter_status}
                                        <input type="hidden" value="{$filter_status}" id="filter_status"/>
                                    {/if}
                                </div>
                            {else}
                                <div class="js-filter-status">
                                    <a href="{if $filter_status=='new'}{url status=null page=null}{else}{url status='new' page=null}{/if}"
                                       class="btn btn-xs {if $filter_status=='new'}btn-warning{else}btn-outline-warning{/if}">Новая</a>
                                    <a href="{if $filter_status==1}{url status=null page=null}{else}{url status=1 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==1}btn-info{else}btn-outline-info{/if}">Принята</a>
                                    <a href="{if $filter_status==2}{url status=null page=null}{else}{url status=2 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==2}btn-success{else}btn-outline-success{/if}">А.Подготовлена</a>
                                    <a href="{if $filter_status==3}{url status=null page=null}{else}{url status=3 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==3}btn-danger{else}btn-outline-danger{/if}">Отказ</a>
                                    <a href="{if $filter_status==4}{url status=null page=null}{else}{url status=4 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==4}btn-inverse{else}btn-outline-inverse{/if}">Подписан</a>
                                    <a href="{if $filter_status==6}{url status=null page=null}{else}{url status=6 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==6}btn-danger{else}btn-outline-danger{/if}">Не
                                        удалось выдать</a>
                                    <a href="{if $filter_status==8}{url status=null page=null}{else}{url status=8 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==8}btn-danger{else}btn-outline-danger{/if}">Отказ
                                        клиента</a>
                                    <a href="{if $filter_status==10}{url status=null page=null}{else}{url status=10 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==10}btn-primary{else}btn-outline-primary{/if}">{if $manager->role == 'middle'}Готово к выдаче{else}У миддла{/if}</a>
                                    <a href="{if $filter_status==14}{url status=null page=null}{else}{url status=14 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==14}btn-success{else}btn-outline-success{/if}">Р.Подтверждена</a>
                                    <a href="{if $filter_status==13}{url status=null page=null}{else}{url status=13 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==13}btn-warning{else}btn-outline-warning{/if}">Р.Нецелесообразно</a>
                                    <a href="{if $filter_status==15}{url status=null page=null}{else}{url status=15 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==15}btn-danger{else}btn-outline-danger{/if}">Р.Отклонена</a>
                                    {if $filter_status}
                                        <input type="hidden" value="{$filter_status}" id="filter_status"/>
                                    {/if}
                                </div>
                            {/if}
                            <div>
                                <a href="{if $filter_client=='new'}{url client=null page=null}{else}{url client='new' page=null}{/if}"
                                   class="btn btn-xs {if $filter_client=='new'}btn-info{else}btn-outline-info{/if}">Новая</a>
                                <a href="{if $filter_client=='repeat'}{url client=null page=null}{else}{url client='repeat' page=null}{/if}"
                                   class="btn btn-xs {if $filter_client=='repeat'}btn-warning{else}btn-outline-warning{/if}">Повтор</a>
                                <a href="{if $filter_client=='pk'}{url client=null page=null}{else}{url client='pk' page=null}{/if}"
                                   class="btn btn-xs {if $filter_client=='pk'}btn-success{else}btn-outline-success{/if}">ПК</a>
                                {if $filter_client}
                                    <input type="hidden" value="{$filter_client}" id="filter_client"/>
                                {/if}
                            </div>
                        </div>

                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover" style="text-align: center">
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'order_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'order_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'order_id_asc'}<a href="{url page=null sort='order_id_desc'}">
                                                    Номер заявки</a>
                                            {else}<a href="{url page=null sort='order_id_asc'}">Номер заявки</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'date_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'date_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'date_asc'}<a href="{url page=null sort='date_desc'}">Дата /
                                                Время</a>
                                            {else}<a href="{url page=null sort='date_asc'}">Дата / Время</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'amount_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'amount_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'amount_asc'}<a href="{url page=null sort='amount_desc'}">
                                                    Сумма, руб</a>
                                            {else}<a href="{url page=null sort='amount_asc'}">Сумма, руб</a>{/if}
                                        </th>
                                        <th style="width: 150px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'fio_asc'}<a href="{url page=null sort='fio_desc'}">ФИО</a>
                                            {else}<a href="{url page=null sort='fio_asc'}">ФИО</a>{/if}
                                        </th>
                                        <th style="width: 150px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'company_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'company_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'company_id_asc'}<a
                                                href="{url page=null sort='company_id_desc'}">Работодатель</a>
                                            {else}<a href="{url page=null sort='fio_asc'}">Работодатель</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'phone_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'phone_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'phone_asc'}<a href="{url page=null sort='phone_desc'}">
                                                    Телефон</a>
                                            {else}<a href="{url page=null sort='phone_asc'}">Телефон</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'status_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'status_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'manager_asc'}<a href="{url page=null sort='manager_desc'}">
                                                    Менеджер</a>
                                            {else}<a href="{url page=null sort='manager_asc'}">Менеджер</a>{/if}
                                        </th>
                                    </tr>
                                    <tr class="jsgrid-filter-row" id="search_form">

                                        <td style="width: 70px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="hidden" name="sort" value="{$sort}"/>
                                            <input type="text" name="order_id" value="{$search['order_id']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="date" value="{$search['date']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="amount" value="{$search['amount']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 150px;" class="jsgrid-cell">
                                            <input type="text" name="fio" value="{$search['fio']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 150px;" class="jsgrid-cell">
                                            <input type="text" name="fio" value="{$search['company']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <input type="text" name="phone" value="{$search['phone']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 60px;" class="jsgrid-cell">
                                            <select name="manager_id" class="form-control">
                                                <option value="0"></option>
                                                <option value="none">Без менеджера</option>
                                                {foreach $managers as $m}
                                                    <option value="{$m->id}"
                                                            {if $search['manager_id'] == $m->id}selected{/if}>{$m->name|escape}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover" style="text-align: center">
                                    <tbody>
                                    {foreach $orders as $order}
                                        <tr class="jsgrid-row js-order-row {if $manager->role == 'quality_control' && $order->quality_workout}workout-row{/if}">
                                            <td style="width: 70px;" class="jsgrid-cell">
                                                <a href="offline_order/{$order->order_id}">{$order->uid} ({$order->number})</a>
                                                {if $order->contract}
                                                    <div>
                                                    <small>{$order->contract->number}</small></div>{/if}
                                                <small>
                                                    {if $order->status == 0}
                                                        <span class="label label-warning">Новая</span>
                                                    {elseif $order->status == 1}
                                                        <span class="label label-info">Принята</span>
                                                    {elseif $order->status == 2}
                                                        <span class="label label-success">А.Подготовлена</span>
                                                    {elseif $order->status == 3}
                                                        <span class="label label-danger">Отказ</span>
                                                    {elseif $order->status == 4}
                                                        <span class="label label-inverse">Подписан</span>
                                                    {elseif $order->status == 5}
                                                        <span class="label label-primary">Выдан</span>
                                                    {elseif $order->status == 6}
                                                        <span class="label label-danger">Не удалось выдать</span>
                                                    {elseif $order->status == 7}
                                                        <span class="label label-inverse">Погашен</span>
                                                    {elseif $order->status == 8}
                                                        <span class="label label-danger">Отказ клиента</span>
                                                    {elseif $order->status == 9}
                                                        <span class="label label-primary">Выдан</span>
                                                    {elseif $order->status == 10}
                                                        <span class="label label-primary">{if $manager->role == 'middle'}Готово к выдаче{else}У миддла{/if}</span>
                                                    {elseif $order->status == 14}
                                                        <span class="label label-success">Р.Подтверждена</span>
                                                    {elseif $order->status == 13}
                                                        <span class="label label-warning">Р.Нецелесообразно</span>
                                                    {elseif $order->status == 15}
                                                        <span class="label label-danger">Р.Отклонена</span>
                                                    {/if}
                                                </small>
                                            </td>
                                            <td style="width: 70px;" class="jsgrid-cell">
                                                {$order->date|date}
                                                {$order->date|time}
                                            </td>
                                            <td style="width: 70px;" class="jsgrid-cell">
                                                {$order->amount|number_format:0:',':' '}
                                            </td>
                                            <td style="width: 150px;" class="jsgrid-cell">
                                                <a href="client/{$order->user_id}">
                                                    {$order->lastname}
                                                    {$order->firstname}
                                                    {$order->patronymic}
                                                </a>
                                                <br/>
                                                    {if $order->client_status == "ПК"}
                                                        <span class="label label-success"
                                                              title="Клиент уже имеет погашенные займы">ПК</span>
                                                    {elseif $order->client_status == 'Новая'}
                                                        <span class="label label-info" title="Новый клиент">Новая</span>
                                                    {elseif $order->client_status == 'Повтор'}
                                                        <span class="label label-warning"
                                                              title="Клиент уже подавал ранее заявки">Повтор</span>
                                                    {/if}
                                                {if $order->autoretry}
                                                    <span class="label label-danger" title="">Автоповтор</span>
                                                {/if}
                                                {if $order->antirazgon}
                                                    <span class="label label-danger"
                                                          title="">АвтоАнтиРазгон {if $order->antirazgon == 1}0-2{elseif $order->antirazgon == 2}3-5{elseif $order->antirazgon == 3}6-10{/if}</span>
                                                {/if}
                                            </td>
                                            <td style="width: 150px;" class="jsgrid-cell">
                                                {$order->company_name|escape}
                                            </td>
                                            <td style="width: 80px;" class="jsgrid-cell">
                                                {$order->phone_mobile}
                                                <button class="js-mango-call mango-call"
                                                        data-phone="{$order->phone_mobile}" title="Выполнить звонок">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </button>
                                                <button class="js-open-sms-modal mango-call {if $order->contract->sold}js-yuk{/if}"
                                                        data-user="{$order->user_id}" data-order="{$order->order_id}">
                                                    <i class=" far fa-share-square"></i>
                                                </button>
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell">
                                                {$managers[$order->manager_id]->name|escape}

                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>

                            {include file='pagination.tpl'}

                        </div>
                    </div>
                </div>
                <!-- Column -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>

<div id="modal_send_sms" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Отправить смс-сообщение?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">


                <div class="card">
                    <div class="card-body">

                        <div class="tab-content tabcontent-border p-3" id="myTabContent">
                            <div role="tabpanel" class="tab-pane fade active show" id="waiting_reason"
                                 aria-labelledby="home-tab">
                                <form class="js-sms-form">
                                    <input type="hidden" name="user_id" value=""/>
                                    <input type="hidden" name="order_id" value=""/>
                                    <input type="hidden" name="yuk" value=""/>
                                    <input type="hidden" name="action" value="send_sms"/>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Выберите шаблон сообщения:</label>
                                        <select name="template_id" class="form-control">
                                            {foreach $sms_templates as $sms_template}
                                                <option value="{$sms_template->id}"
                                                        title="{$sms_template->template|escape}">
                                                    {$sms_template->name|escape} ({$sms_template->template})
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-action clearfix">
                                        <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                data-dismiss="modal">Отменить
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg float-right waves-effect waves-light">Да,
                                            отправить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>