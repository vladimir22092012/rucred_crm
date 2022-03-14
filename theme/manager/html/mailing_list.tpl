{$meta_title='Список рассылок' scope=parent}

{capture name='page_scripts'}
    
{/capture}

{capture name='page_styles'}
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css" />
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css" />
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/css-chart/css-chart.css" />
        <style>
        .jsgrid-table { margin-bottom:0}
        .label { white-space: pre; }
        
        .js-open-hide {
            display:block;
        }
        .js-open-show {
            display:none;
        }
        .open.js-open-hide {
            display:none;
        }
        .open.js-open-show {
            display:block;
        }
        .form-control.js-contactperson-status,
        .form-control.js-contact-status {
            font-size: 12px;
            padding-left: 0px;
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
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Список рассылок</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Рассылки</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                <div class="text-right">
                    <a href="mailing/new" class="btn btn-success btn-large">
                        <i class="fas fa-plus-circle"></i>
                        <span>Новая рассылка</span>
                    </a>
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
                        <div class="clearfix">
                            <h4 class="card-title  float-left">Список рассылок</h4>

                        </div>
                        
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 60px;" class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'date_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'date_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            Дата
                                        </th>
                                        <th style="width: 60px;" class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'order_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'order_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            Инициатор
                                        </th>
                                        <th style="width: 60px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Инструменты
                                        </th>
                                        <th style="width: 100px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'phone_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'phone_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            МКК текст
                                        </th>
                                        <th style="width: 100px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'result_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'result_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            ЮК текст
                                        </th>
                                        <th style="width: 120px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'status_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'status_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Коллекторы
                                        </th>
                                        <th style="width: 60px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'sms_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'sms_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Прогресс
                                        </th>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tbody>
                                    {foreach $mailings as $item}
                                        <tr class="jsgrid-row js-contract-row">
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-right">                                                
                                                {$item->created|date} {$item->created|time}
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-left">                                                
                                                {$managers[$item->manager_id]->name_1c|escape}
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-center">
                                                {if $item->sms}<span class="label label-info">СМС</span>{/if}
                                                {if $item->zvonobot}<span class="label label-warning">Звонобот</span>{/if}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                <small>{$item->mkk_text}</small>
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                <small>{$item->yuk_text}</small>
                                            </td>
                                            <td style="width: 120px;" class="jsgrid-cell">
                                                <ul class="p-0 pl-2">
                                                    {foreach $item->collectors as $c}
                                                    <li><small>{$managers[$c]->name}</small></li>
                                                    {/foreach}
                                                </ul>
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-center">
                                                <div data-label="{($item->sent_mail/$item->total_mail*100)|round}%" class="css-bar css-bar-sm mb-0 css-bar-success css-bar-{($item->sent_mail/$item->total_mail*10)|round*10}"></div>
                                                <div>{$item->sent_mail}(<span class="text-success" title="Отправлено успешно">{$item->success_mail}</span>) / {$item->total_mail}</div>
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

<div id="modal_add_comment" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title">Добавить комментарий</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_add_comment" action="">
                    
                    <input type="hidden" name="order_id" value="" />
                    <input type="hidden" name="contactperson_id" value="" />
                    <input type="hidden" name="action" value="" />
                    
                    <div class="alert" style="display:none"></div>
                    
                    <div class="form-group">
                        <label for="name" class="control-label">Комментарий:</label>
                        <textarea class="form-control" name="text"></textarea>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>