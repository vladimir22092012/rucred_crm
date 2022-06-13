{$meta_title = 'Реестр документов' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
{/capture}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script>
        $(function () {
            $('.input-sm').on('change', function () {

                let fields = {};

                $('.input-sm').each(function () {
                    let value = $(this).val();
                    let field = $(this).attr('name');
                    fields[field] = value;
                });

                $.ajax({
                    method: 'POST',
                    data: {
                        search: true,
                        fields: fields,
                    },
                    success: function (html) {
                        $('body').html(html);
                    }
                })
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
                <h3 class="text-themecolor mb-0 mt-0">Реестр документов</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Реестр документов</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="basicgrid" class="jsgrid">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover" align="center">
                                    <thead>
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'type asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'type desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'type asc'}<a href="{url page=null sort='type desc'}">
                                                    Тип документа</a>
                                            {else}<a href="{url page=null sort='type asc'}">Тип документа</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'created asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'created desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'created asc'}<a href="{url page=null sort='created desc'}">
                                                    Дата</a>
                                            {else}<a href="{url page=null sort='created asc'}">Дата</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'name asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'name desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'name asc'}<a href="{url page=null sort='name desc'}">
                                                    Название</a>
                                            {else}<a href="{url page=null sort='name asc'}">Название</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'user_id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'user_id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'user_id asc'}<a href="{url page=null sort='user_id desc'}">
                                                    Клиент</a>
                                            {else}<a href="{url page=null sort='user_id asc'}">Клиент</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'order_id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'order_id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'order_id asc'}<a href="{url page=null sort='order_id desc'}">
                                                    Заявка/Сделка</a>
                                            {else}<a href="{url page=null sort='order_id asc'}">Заявка/Сделка</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'asp_id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'asp_id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'asp_id asc'}<a href="{url page=null sort='asp_id desc'}">
                                                    Подписан(Да/Нет)</a>
                                            {else}<a href="{url page=null sort='asp_id asc'}">Подписан(Да/Нет)</a>{/if}
                                        </th>
                                    </tr>
                                    <tr class="jsgrid-filter-row" id="search_form">

                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="type" value="{$search['type']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="created" value="{$search['created']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell">
                                            <input type="text" name="name" value="{$search['name']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 150px;" class="jsgrid-cell">
                                            <input type="text" name="user" value="{$search['user']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 150px;" class="jsgrid-cell">
                                            <input type="text" name="order" value="{$search['order']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <select name="asp_id" class="form-control input-sm">
                                                <option value="" {if $search['asp_id'] == ''}selected{/if}>Фильтр</option>
                                                <option value="0" {if $search['asp_id'] == 0}selected{/if}>Нет</option>
                                                <option value="1" {if $search['asp_id'] == 1}selected{/if}>Да</option>
                                            </select>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {foreach $documents as $document}
                                        <tr>
                                            <td>{$document->type}</td>
                                            <td>{$document->created|date} {$document->created|time}</td>
                                            <td>{$document->name}</td>
                                            <td><a type="_blank"
                                                   href="{$config->root_url}/client/{$document->user->id}">
                                                    {$document->lastname} {$document->firstname} {$document->patronymic}</a>
                                            </td>
                                            <td><a type="_blank"
                                                   href="{$config->root_url}/offline_order/{$document->order_id}">{$document->uid}</a>
                                            </td>
                                            <td>
                                                {if !empty($document->asp_id)}
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
                            <div style="display: flex; justify-content: space-between">
                                {include file='pagination.tpl'}
                                <div class="float-right pt-1">
                                    <select onchange="if (this.value) window.location.href = this.value"
                                            class="form-control form-control-sm page_count" name="page-count">
                                        <option value="{url page_count=25}" {if $page_count==25}selected=""{/if}>
                                            Показывать 25
                                        </option>
                                        <option value="{url page_count=50}" {if $page_count==50}selected=""{/if}>
                                            Показывать 50
                                        </option>
                                        <option value="{url page_count=100}" {if $page_count==100}selected=""{/if}>
                                            Показывать 100
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file='footer.tpl'}
</div>