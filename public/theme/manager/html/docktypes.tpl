{$meta_title = 'Типы документов' scope=parent}

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
            $('.add_dock_modal').on('click', function (e) {
                $('#add_dock_modal').modal();

                $('.add_dock').on('click', function (e) {
                    e.preventDefault();

                    let form = $(this).closest('form').serialize();

                    $.ajax({
                        method: 'POST',
                        data: form,
                        success: function () {
                            location.reload();
                        }
                    })
                })
            });

            $('.change_permission').on('click', function () {

                if ($(this).is(':checked'))
                    $(this).val(1);
                else
                    $(this).val(0);

                let doc_id = $(this).attr('data-doc');
                let role_id = $(this).attr('data-role');
                let value = $(this).val();

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_permission',
                        doc_id: doc_id,
                        role_id: role_id,
                        value: value
                    }
                });
            })
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
                <h3 class="text-themecolor mb-0 mt-0">Типы документов</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Типы документов</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="col-6 text-right">
                    {if $manager->role == 'developer'}
                        <div class="btn btn-outline-success add_dock_modal">
                            <i class="fas fa-plus-circle"></i>
                            <span>Новый документ</span>
                        </div>
                    {/if}
                </div>
                <div class="card">
                    <div class="card-body">
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <input type="hidden" id="document_std_link" value="{$config->back_url}/document/">
                                <table class="jsgrid-table table table-striped table-hover" style="display: inline-block; vertical-align: top; max-width: 100%;
                            overflow-x: auto; white-space: nowrap;-webkit-overflow-scrolling: touch;">
                                    <thead>
                                    <tr class="jsgrid-header-row">
                                        <th>Наименование документа</th>
                                        <th>Доступность</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {foreach $docs as $doc}
                                        <tr style="text-align: left;">
                                            <td style="width: 50%">{$doc->name}</td>
                                            <td style="width: 50%">
                                                <div style="display: flex;">
                                                    {foreach $roles as $role}
                                                        <input class="change_permission" data-doc="{$doc->id}"
                                                               data-role="{$role->id}" id="{$role->id}"
                                                               style="margin: 2px 15px" type="checkbox" value="1"
                                                                {foreach $permissions as $permission}
                                                            {if $permission->role_id == $role->id && $permission->docktype_id == $doc->id}checked{/if}
                                                                {/foreach}>
                                                        <label for="{$role->id}"
                                                               style="margin-left: 5px">{$role->translate}</label>
                                                    {/foreach}
                                                </div>
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

<div id="add_dock_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавить документ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_dock_form">
                    <input type="hidden" name="action" value="add_dock">
                    <div class="form-group">
                        <label for="name" class="control-label">Название документа</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="templates" class="control-label">Название шаблона (вместе с .tpl):</label>
                        <input type="text" class="form-control" name="templates" id="templates" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="client_visible" class="control-label">Видим для клиента:</label>
                        <select class="form-control" name="client_visible" id="client_visible">
                            <option value="1">Да</option>
                            <option value="0">Нет</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="online_offline_flag" class="control-label">Видимость:</label>
                        <select class="form-control" name="online_offline_flag" id="online_offline_flag">
                            <option value="1">Онлайн</option>
                            <option value="0">Оффлайн</option>
                        </select>
                    </div>
                    <div>
                        <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                        <input type="button" class="btn btn-success add_dock" value="Сохранить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>