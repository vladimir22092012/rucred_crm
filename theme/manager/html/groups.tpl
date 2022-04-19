{$meta_title = 'Группы' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
{/capture}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/companies.js"></script>
    <script>
        $(function () {

            $('.to_edit').on('click', function () {
                $('.group_name_front').hide();
                $('.group_name_edit').show();
            });

            $('.cancel_edit').on('click', function () {
                $('.group_name_edit').hide();
                $('.group_name_front').show();
            });

            $('.save_edit').on('click', function (e) {
                e.preventDefault();

                let group_name = $(this).prev().val();
                let group_id = $(this).attr('data-group');


                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'update_group',
                        group_name: group_name,
                        group_id: group_id
                    },
                    success: function (resp) {
                        location.reload();
                    }
                })
            });

            $('.action_add_group').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    method: 'POST',
                    data: $('#add_group').serialize(),
                    success: function () {
                        location.reload();
                    }
                })
            });

            $('.delete_group').on('click', function (e) {

                let group_id = $(this).attr('data-group');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'delete_group',
                        group_id: group_id
                    },
                    success: function (resp) {

                        if (resp.length>0) {
                            Swal.fire({
                                title: resp,
                                showCancelButton: false,
                                confirmButtonText: 'ОК'
                            });
                        }
                        else {
                            location.reload();
                        }
                    }
                })
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
                <h3 class="text-themecolor mb-0 mt-0">Группы</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Главная</li>
                    <li class="breadcrumb-item">Справочники</li>
                    <li class="breadcrumb-item active">Группы</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                <button class="btn float-right hidden-sm-down btn-success add-company-modal">
                    <i class="mdi mdi-plus-circle"></i> Добавить
                </button>

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
                                        <th class="">Наименование группы</th>
                                        <th class="">Номер</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if !empty($groups)}
                                        {foreach $groups as $group}
                                            <tr>
                                                <td class="group_name_front">
                                                    <input type="button" class="btn btn-outline-info to_edit"
                                                           value="{$group->name}"></td>
                                                <td class="group_name_edit" style="display: none">
                                                    <input type="text" class="form-control group_name"
                                                           style="width: 300px" value="{$group->name}">
                                                    <input type="button" data-group="{$group->id}"
                                                           class="btn btn-outline-success save_edit"
                                                           value="Сохранить">
                                                    <input type="button" class="btn btn-outline-danger cancel_edit"
                                                           value="Отменить">
                                                </td>
                                                <td>{$group->number}</td>
                                                <td>{if $group->number != '00'}<input type="button" data-group="{$group->id}"
                                                           class="btn btn-outline-danger delete_group"
                                                           value="Удалить"></td>{/if}
                                            </tr>
                                        {/foreach}
                                    {/if}
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

<div id="modal_add_item" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить группу</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_group">
                    <input type="hidden" name="action" value="add_group">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование группы</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_add_group" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>