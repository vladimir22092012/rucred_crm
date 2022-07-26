{$meta_title = 'ЭЦП реестр' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
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

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script>
        $(function () {

            let document_link = $('#document_std_link').val();

            $('.document_link').on('change', function () {
                let document_id = $(this).val();
                $(this).parent().find('a').attr('href', document_link + document_id);
            });

            $('.searchable:not(select)').on('change', function (e) {
                e.preventDefault();

                $('table tbody tr').show();

                $('.searchable:not(select)').each(function () {
                    let value = $(this).val();
                    let index = $(this).parent().index() + 1;

                    if (value && value.length > 0) {
                        $('td:nth-child(' + index + ')').each(function () {
                            let find_value = $(this).text().toLowerCase();
                            if (find_value.includes(value) === false) {
                                $(this).closest('tr[class="codes"]').hide();
                            }
                        });
                    }
                });
            });

            $('#user_filter').on('change', function () {
                let value = $(this).val();

                if (value != 'none') {
                    $('tr[class="codes"]').show();
                    $('tr[class="codes"]').find('td[class="user_id"]').not('#' + value + '').parent().hide();
                }
                else {
                    $('tr[class="codes"]').show();
                }

            });

            $('#type_filter').on('change', function () {
                let value = $(this).val();

                if (value != 'none') {
                    $('tr[class="codes"]').show();
                    $('tr[class="codes"]').find('td[class="code_type"]').not('#' + value + '').parent().hide();
                }
                else {
                    $('tr[class="codes"]').show();
                }

            });
        })
    </script>
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
                <h3 class="text-themecolor mb-0 mt-0">ЭЦП реестр</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">ЭЦП реестр</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <input type="hidden" id="document_std_link" value="{$config->root_url}/document/">
                                <table class="jsgrid-table table table-striped table-hover" style="text-align: center">
                                    <thead>
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'id desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'id asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'id asc'}<a href="{url page=null sort='id desc'}">
                                                    ID</a>
                                            {else}<a href="{url page=null sort='id asc'}">ID</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'created asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'created desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'created asc'}<a href="{url page=null sort='created desc'}">
                                                    Дата /
                                                    Время</a>
                                            {else}<a href="{url page=null sort='created asc'}">Дата / Время</a>{/if}
                                        </th>
                                        <th style="width: 120px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'manager_id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'manager_id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'manager_id asc'}<a
                                                href="{url page=null sort='manager_id desc'}">
                                                    Подписант</a>
                                            {else}<a href="{url page=null sort='manager_id asc'}">Подписант</a>{/if}
                                        </th>
                                        <th style="width: 70px;">Количество<br>док-тов</th>
                                        <th style="width: 350px;">Подписанные
                                            документы
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'type asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'type desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'type asc'}<a
                                                href="{url page=null sort='type desc'}">Канал</a>
                                            {else}<a href="{url page=null sort='type asc'}">Канал</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'recepient asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'recepient desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'recepient asc'}<a
                                                href="{url page=null sort='recepient desc'}">
                                                    Получатель</a>
                                            {else}<a href="{url page=null sort='recepient asc'}">Получатель</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'code asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'code desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'code asc'}<a href="{url page=null sort='code desc'}">
                                                    Код</a>
                                            {else}<a href="{url page=null sort='code asc'}">Код</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'order_id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'order_id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'order_id asc'}<a href="{url page=null sort='order_id desc'}">
                                                    Сделка</a>
                                            {else}<a href="{url page=null sort='order_id asc'}">Сделка</a>{/if}
                                        </th>
                                    </tr>
                                    <tr class="jsgrid-filter-row">
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" class="form-control searchable">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" class="form-control searchable">
                                        </td>
                                        <td style="width: 120px;" class="jsgrid-cell">
                                            <input type="text" class="form-control searchable">
                                        </td>
                                        <td style="width: 70px;"></td>
                                        <td style="width: 350px;"></td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <select class="form-control" id="type_filter">
                                                <option value="sms">смс</option>
                                                <option value="email">почта</option>
                                            </select>
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell"><input type="text"
                                                                                            class="form-control searchable">
                                        </td>
                                        <td style="width: 60px;" class="jsgrid-cell"><input type="text"
                                                                                            class="form-control searchable">
                                        </td>
                                        <td style="width: 60px;" class="jsgrid-cell"><input type="text"
                                                                                            class="form-control searchable">
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body" style="font-size: 14px">
                                    {foreach $codes as $code}
                                        <tr class="codes">
                                            <td>
                                                {$code->uid}
                                            </td>
                                            <td>
                                                {$code->created|date} {$code->created|time}
                                            </td>
                                            {if $code->type == 'rucred_sms'}
                                                <td class="user_id" id="{$code->user->id}">
                                                    <a target="_blank"
                                                       href="{$config->back_url}manager/{$code->manager->id}">
                                                        {$code->manager->name}
                                                    </a>
                                                </td>
                                            {else}
                                                <td class="user_id" id="{$code->user->id}">
                                                    <a target="_blank"
                                                       href="{$config->back_url}client/{$code->user->id}">
                                                        {$code->user->lastname} {$code->user->firstname} {$code->user->patronymic}
                                                    </a>
                                                </td>
                                            {/if}
                                            <td>
                                                {count($code->documents)}
                                            </td>
                                            <td style="display: flex">
                                                <select class="form-control document_link">
                                                    {foreach $code->documents as $document}
                                                        <option value="{$document->id}">
                                                            {$document->numeration} {$document->name}
                                                        </option>
                                                    {/foreach}
                                                </select>
                                                <a target="_blank" id="document_href"
                                                   href="{$config->root_url}/document/{$code->documents[0]->id}">
                                                    <div class="btn btn-outline-info" style="margin-left: 5px">
                                                        Открыть
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="code_type" id="{$code->type}">
                                                {$code->type}
                                            </td>
                                            <td>
                                                {$code->recepient}
                                            </td>
                                            <td>
                                                {$code->code}
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                   href="{$config->root_url}/offline_order/{$code->order_id}">
                                                    {$code->order_id}</a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file='footer.tpl'}
</div>