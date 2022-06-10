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
                                        <th>Тип документа</th>
                                        <th>Дата</th>
                                        <th>Название</th>
                                        <th>Клиент</th>
                                        <th>Заявка/Сделка</th>
                                        <th>Подписан(Да/Нет)</th>
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
                                                    {$document->user->lastname} {$document->user->firstname} {$document->user->patronymic}</a>
                                            </td>
                                            <td><a type="_blank"
                                                   href="{$config->root_url}/offline_order/{$document->order_id}">{$document->order->uid}</a>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file='footer.tpl'}
</div>