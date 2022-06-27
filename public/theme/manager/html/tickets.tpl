{$meta_title='Тикеты' scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
        $(function () {

            moment.locale('ru');

            $('.daterange').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD.MM.YYYY'
                },
            });

            $('#groups').on('change', function () {

                let group_id = $(this).val();

                if (group_id !== 'none') {
                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'get_companies',
                            group_id: group_id
                        },
                        success: function (companies) {
                            $('#companies_checkbox').show();
                            $('#companies_checkbox').html(companies);
                        }
                    })
                } else {
                    $('#companies_checkbox').empty();
                    $('#companies_checkbox').hide();
                }
            });

            $('.add_ticket').on('click', function () {
                $('#add_ticket').modal();

                $('.action_add_ticket').on('click', function () {
                    let docs = $('#docs').files;
                    let form_data = new FormData($('#add_ticket_form')[0]);
                    form_data.append('docs', docs);

                    $.ajax({
                        method: 'POST',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function (resp) {
                            location.reload();
                        }
                    })
                })
            });

            $('#lastname').autocomplete({
                serviceUrl: '/tickets',
                type: 'POST',
                dataType: 'json',
                params: {
                    action: 'search_user',
                },
                paramName: 'lastname',
                minChars: 3,
                onSelect: function (suggestion) {
                    let fio = suggestion.value.split(' ');
                    $('label[for="lastname"]').after('<label for="lastname" class="control-label" style="width: 20%">ID ' + fio[0] + '</label>');
                    $('#lastname').val(fio[1]);
                    $('#firstname').val(fio[2]);
                    $('#patronymic').val(fio[3]);
                }
            });

            $('.qr').on('click', function () {

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'qr'
                    }
                })
            });
        })
    </script>
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
    </style>
    <style>
        .blink {
            -webkit-animation: blink 3s linear infinite;
            animation: blink 3s linear infinite;
        }
        @-webkit-keyframes blink {
            0% { color: rgba(34, 34, 34, 1); }
            50% { color: rgba(34, 34, 34, 0); }
            100% { color: rgba(34, 34, 34, 1); }
        }
        @keyframes blink {
            0% { color: rgba(34, 34, 34, 1); }
            50% { color: rgba(34, 34, 34, 0); }
            100% { color: rgba(34, 34, 34, 1); }
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
                <h3 class="text-themecolor mb-0 mt-0"><i
                            class="mdi mdi-animation"></i>{if $in}Полученные запросы{/if}{if $out}Направленные запросы{/if}{if $archive}Архив запросов{/if}
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">{if $in}Полученные запросы{/if}{if $out}Направленные запросы{/if}{if $archive}Архив запросов{/if}</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                <div class="row">
                    {if !$archive}
                        <div class="col-6 text-right">
                            <div type="button" class="btn btn-success add_ticket">
                                <i class="fas fa-plus-circle"></i>
                                Создать тикет
                            </div>
                        </div>
                    {/if}
                    {*
                    <div class="col-6 text-right">
                        <div type="button" class="btn btn-success qr">
                            <i class="fas fa-plus-circle"></i>
                            QR
                        </div>
                    </div> *}
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
                        <h4 class="card-title">Список тикетов</h4>
                        {if !$archive}
                            <div class="clearfix">
                                <div class="js-filter-status mb-2 float-left">
                                    <a href="{if $filter_status=='new'}{url status=null page=null}{else}{url status='new' page=null}{/if}"
                                       class="btn btn-xs {if $filter_status=='new'}btn-warning{else}btn-outline-warning{/if}">Новая</a>

                                    <a href="{if $filter_status==1}{url status=null page=null}{else}{url status=1 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==1}btn-info{else}btn-outline-info{/if}">Принята</a>

                                    <a href="{if $filter_status==2}{url status=null page=null}{else}{url status=2 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==2}btn-success{else}btn-outline-primary{/if}">На
                                        проверку</a>

                                    <a href="{if $filter_status==3}{url status=null page=null}{else}{url status=3 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==3}btn-danger{else}btn-outline-success{/if}">Исполнено</a>

                                    <a href="{if $filter_status==4}{url status=null page=null}{else}{url status=4 page=null}{/if}"
                                       class="btn btn-xs {if $filter_status==4}btn-danger{else}btn-outline-danger{/if}">На
                                        доработку</a>
                                    {if $filter_status}
                                        <input type="hidden" value="{$filter_status}" id="filter_status"/>
                                    {/if}
                                </div>
                            </div>
                        {/if}
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 40px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'id desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'id asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'id asc'}<a href="{url page=null sort='id desc'}">
                                                    ID</a>
                                            {else}<a href="{url page=null sort='id asc'}">ID</a>{/if}
                                        </th>
                                        <th style="width: 70px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'created asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'created desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'created asc'}<a href="{url page=null sort='created desc'}">Дата /
                                                Время</a>
                                            {else}<a href="{url page=null sort='created asc'}">Дата / Время</a>{/if}
                                        </th>
                                        <th style="width: 70px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'creator asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'creator desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'creator asc'}<a href="{url page=null sort='creator desc'}">
                                                    Постановщик</a>
                                            {else}<a href="{url page=null sort='creator asc'}">Постановщик</a>{/if}
                                        </th>
                                        <th style="width: 70px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'executor asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'executor desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'executor asc'}<a href="{url page=null sort='executor desc'}">
                                                    Постановщик</a>
                                            {else}<a href="{url page=null sort='executor asc'}">Исполнитель</a>{/if}
                                        </th>
                                        <th style="width: 80px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'head asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'head desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'head asc'}<a href="{url page=null sort='head desc'}">
                                                    Заголовок</a>
                                            {else}<a href="{url page=null sort='head asc'}">Заголовок</a>{/if}
                                        </th>
                                        <th style="width: 130px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'text asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'text desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'text asc'}<a href="{url page=null sort='text desc'}">Текст</a>
                                            {else}<a href="{url page=null sort='text asc'}">Текст</a>{/if}
                                        </th>
                                        <th style="width: 70px; text-align: center"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'text asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'text desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'text asc'}<a href="{url page=null sort='text desc'}">
                                                    Вложения</a>
                                            {else}<a href="{url page=null sort='text asc'}">Вложения</a>{/if}
                                        </th>
                                    </tr>
                                    <tr class="jsgrid-filter-row" id="search_form">
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="hidden" name="sort" value="{$sort}"/>
                                            <input type="text" name="order_id" value="{$search['id']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="date" value="{$search['date']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="amount" value="{$search['maker']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="amount" value="{$search['maker']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 60px;" class="jsgrid-cell">
                                            <input type="text" name="period" value="{$search['head']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 150px;" class="jsgrid-cell">
                                            <input type="text" name="fio" value="{$search['text']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="birth" value="{$search['inside']}"
                                                   class="form-control input-sm">
                                        </td>
                                    </tr>
                                    <div class="jsgrid-grid-body">
                                        <table class="jsgrid-table table table-striped table-hover">
                                            <tbody style="text-align: center">
                                            {foreach $tickets as $ticket}
                                                <tr class="jsgrid-row">
                                                    <td style="width: 40px;" class="jsgrid-cell">
                                                        <a href="/ticket/{$ticket->id}/">{$ticket->id}</a><br>
                                                        {if !$archive}
                                                            {if in_array($ticket->status, [0,1])}
                                                                <small class="label label-warning">Новый</small><br>
                                                            {/if}
                                                            {if $ticket->status == 2}
                                                                <small class="label label-info">Принят</small><br>
                                                            {/if}
                                                            {if $ticket->status == 3}
                                                                <small class="label label-primary">На проверку</small><br>
                                                            {/if}
                                                            {if $ticket->status == 4}
                                                                <small class="label label-success">Исполнено</small><br>
                                                            {/if}
                                                            {if $ticket->status == 5}
                                                                <small class="label label-danger">На доработку</small><br>
                                                            {/if}
                                                        {/if}
                                                        {if empty($ticket->ticket_id)}
                                                            <small class="blink ">Новый!</small>
                                                        {/if}
                                                    </td>
                                                    <td style="width: 70px;" class="jsgrid-cell">
                                                        {$ticket->created|date}
                                                    </td>
                                                    <td style="width: 70px;" class="jsgrid-cell">
                                                        {if $ticket->creator == 0}
                                                            ООО МКК "РУССКОЕ КРЕДИТНОЕ ОБЩЕСТВО"
                                                        {else}
                                                            {$ticket->creator_company_name}
                                                        {/if}
                                                    </td>
                                                    <td style="width: 70px;" class="jsgrid-cell">
                                                        {if $ticket->executor == 0}
                                                            Нет ответственного
                                                        {else}
                                                            <a href="/manager/{$ticket->executor['id']}/">{$ticket->executor['name']}</a>
                                                        {/if}
                                                    </td>
                                                    <td style="width: 80px;" class="jsgrid-cell">
                                                        {$ticket->head|escape}
                                                    </td>
                                                    <td style="width: 130px;" class="jsgrid-cell">
                                                        {$ticket->text|escape}
                                                    </td>
                                                    <td style="width: 70px;" class="jsgrid-cell">
                                                        {if $ticket->files == 1}
                                                            Да
                                                        {else}
                                                            Нет
                                                        {/if}
                                                    </td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                    </div>
                                </table>
                            </div>
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
<div id="add_ticket" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="add_ticket" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Исходящее обращение</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_ticket_form">
                    <input type="hidden" name="action" value="add_ticket">
                    <input type="hidden" name="manager_id" value="{$manager->id}">
                    <div class="form-group">
                        <label for="groups" class="control-label">Группа:</label>
                        <select class="form-control" name="groups" id="groups"
                                {if $manager->role == 'employer'}readonly{/if}>
                            {if $manager->role == 'employer'}
                                <option value="2">Инфраструктура</option>
                            {else}
                                <option value="none">Выберите группу</option>
                                {foreach $groups as $group}
                                    <option value="{$group->id}">{$group->name}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                    <div class="form-group">
                        {if $manager->role == 'employer'}
                            <label for="companies" class="control-label">Компания:</label>
                            <select class="form-control" name="companies" id="companies"
                                    {if $manager->role == 'employer'}readonly{/if}>
                                <option value="2">ООО МКК "РУССКОЕ КРЕДИТНОЕ ОБЩЕСТВО"</option>
                            </select>
                        {else}
                            <div class="form-group" id="companies_checkbox" style="display: none">

                            </div>
                        {/if}
                    </div>
                    <div class="form-group">
                        <label for="creator_company" class="control-label">Компания от которой обращаетесь:</label>
                        <select class="form-control" name="creator_company" id="creator_company">
                            {foreach $managers_companies as $key => $companies}
                                <option value="{$key}">{$companies}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label" style="width: 80%">Фамилия клиента</label>
                        <input type="text" class="form-control" name="lastname" id="lastname" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="control-label">Имя клиента</label>
                        <input type="text" class="form-control" name="firstname" id="firstname" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="patronymic" class="control-label">Отчество клиента</label>
                        <input type="text" class="form-control" name="patronymic" id="patronymic" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="head" class="control-label">Заголовок:</label>
                        <input type="text" class="form-control" name="head" id="head" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="text" class="control-label">Текст:</label>
                        <input type="text" class="form-control" name="text" id="text" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="docs" class="control-label">Прикрепить документы</label>
                        <input type="file" class="custom-file-control" name="docs[]" id="docs" multiple="multiple">
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_add_ticket" value="Создать тикет">
                </form>
            </div>
        </div>
    </div>
</div>