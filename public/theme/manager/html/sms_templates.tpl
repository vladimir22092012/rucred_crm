{$meta_title = 'Шаблоны сообщений' scope=parent}

{capture name='page_styles'}
<link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
<style>
    .js-text-admin-name,
    .js-text-client-name {
//        max-width:300px
    }
</style>
{/capture}

{capture name='page_scripts'}

    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    
    <script src="theme/manager/js/apps/sms_templates.app.js"></script>
    
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
                <h3 class="text-themecolor mb-0 mt-0">Шаблоны сообщений</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Главная</a></li>
                    <li class="breadcrumb-item active">Шаблоны сообщений</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                {if in_array($manager->role, ['developer', 'admin'])}
                <button class="btn float-right hidden-sm-down btn-success js-open-add-modal">
                    <i class="mdi mdi-plus-circle"></i> Добавить
                </button>
                {/if}
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
            
            <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"></h4>
                        <h6 class="card-subtitle"></h6>
                        <div class="table-responsive m-t-40">
                            <div class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">    
                                <table id="config-table" class="table display table-striped dataTable">
                                    <thead>
                                        <tr>
                                            <th class="">ID</th>
                                            <th class="">Название</th>
                                            <th class="">Шаблон</th>
                                            <th class="">Тип</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                        
                                        {foreach $sms_templates as $st}
                                        <tr class="js-item">
                                            <td>
                                                <div class="js-text-id">
                                                    {$st->id}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="js-visible-view js-text-name">
                                                    {$st->name|escape}
                                                </div>
                                                <div class="js-visible-edit" style="display:none">
                                                    <input type="hidden" name="id" value="{$st->id}" />
                                                    <input type="text" class="form-control form-control-sm" name="name" value="{$st->name|escape}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="js-visible-view js-text-template">
                                                    {$st->template|escape}
                                                </div>
                                                <div class="js-visible-edit" style="display:none">
                                                    <input type="text" class="form-control form-control-sm" name="template" value="{$st->template|escape}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="js-visible-view js-text-type">
                                                    {$template_types[$st->type]|escape}
                                                </div>
                                                <div class="js-visible-edit" style="display:none">
                                                    <select name="type" class="form-control form-control-sm">
                                                        {foreach $template_types as $ttk => $ttv}
                                                        <option value="{$ttk}" {if $ttk == $st->type}selected{/if}>{$ttv|escape}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                {if in_array($manager->role, ['developer', 'admin'])}
                                                <div class="js-visible-view">
                                                    <a href="#" class="text-info js-edit-item" title="Редактировать"><i class=" fas fa-edit"></i></a>
                                                    <a href="#" class="text-danger js-delete-item" title="Удалить"><i class="far fa-trash-alt"></i></a>
                                                </div>
                                                <div class="js-visible-edit" style="display:none">
                                                    <a href="#" class="text-success js-confirm-edit-item" title="Сохранить"><i class="fas fa-check-circle"></i></a>
                                                    <a href="#" class="text-danger js-cancel-edit-item" title="Отменить"><i class="fas fa-times-circle"></i></a>
                                                </div>
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

<div id="modal_add_item" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title">Добавить шаблон сообщения</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_add_item">
                
                    <div class="alert" style="display:none"></div>
                    
                    <div class="form-group">
                        <label for="name" class="control-label">Название:</label>
                        <input type="text" class="form-control" name="name" id="name" value="" />
                    </div>
                    <div class="form-group">
                        <label for="template" class="control-label">Сообщение:</label>
                        <input type="text" class="form-control" name="template" id="template" value="" />
                    </div>
                    <div class="form-group">
                        <label for="type" class="control-label">Тип:</label>
                        <select name="type" class="form-control" id="type">
                            {foreach $template_types as $ttk => $ttv}
                            <option value="{$ttk}">{$ttv|escape}</option>
                            {/foreach}
                        </select>
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