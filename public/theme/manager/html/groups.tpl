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
    <script>
        $(function () {

            $(document).on('click', '.add_group', function () {
                let addGroupModal = $('#add_group_modal');

                addGroupModal.find('.modal-title').text("Создать группу");
                addGroupModal.find('#add-group-btn').show();
                addGroupModal.find('#edit-group-btn').hide();
                addGroupModal.find('form')[0].reset()

                addGroupModal.find('#number-input').parent().hide();

                addGroupModal.modal({
                    backdrop: 'static'
                }, 'show');
            });

            $('.to_edit').on('click', function () {
                let group = $(this).parents('tr').first().data('group');
                let editGroupModal = $('#add_group_modal');
                let numberInput = editGroupModal.find('#number-input');
                let nameInput = editGroupModal.find('#name-input');
                let blockedSelect = editGroupModal.find('#blocked-select');

                editGroupModal.data('group', group.id);
                numberInput.val(group.number);
                nameInput.val(group.name).attr('selected', true);
                blockedSelect.val(group.blocked);

                editGroupModal.find('#number-input').parent().show();
                editGroupModal.find('.modal-title').text("Редактировать группу");
                editGroupModal.find('#add-group-btn').hide();
                editGroupModal.find('#edit-group-btn').show();

                editGroupModal.modal({
                    backdrop: 'static'
                }, 'show');
            });

            $('#edit-group-btn').on('click', function (e) {
                e.preventDefault();
                let editGroupModal = $('#add_group_modal');
                let nameInput = editGroupModal.find('#name-input').val();
                let numberInput = editGroupModal.find('#number-input').val();
                let blockedSelect = editGroupModal.find('#blocked-select').val();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'update_group',
                        group_id: editGroupModal.data('group'),
                        group_name: nameInput,
                        blocked: blockedSelect,
                        number: numberInput
                    },
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            location.reload();
                        }
                    }
                })

            });

            $('.cancel_edit').on('click', function () {
                $('.group_edit').hide();
                $('.group_front').show();
                $('.to_edit').show();
            });

            $('#add-group-btn').on('click', function (e) {
                e.preventDefault();
                let addGroupModal = $('#add_group_modal');
                let nameInput = addGroupModal.find('#name-input').val();
                let blockedSelect = addGroupModal.find('#blocked-select').val();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'add_group',
                        group_name: nameInput,
                        blocked: blockedSelect,
                    },
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            location.reload();
                        }
                    }
                })

            });

            $('.delete_group').on('click', function () {

                let group_id = $(this).attr('data-group');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'delete_group',
                        group_id: group_id
                    },
                    success: function (resp) {

                        if (resp.length > 0) {
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
            });

            $('.blocked').on('change', function () {

                let group_id = $(this).attr('data-group');
                let value    = $(this).val();

                $.ajax({
                    method: 'POST',
                    data:{
                        action: 'blocked',
                        group_id: group_id,
                        value: value
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
                <h3 class="text-themecolor mb-0 mt-0">Группы</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Группы</li>
                </ol>
            </div>
            {if in_array($manager->role,['admin', 'developer'])}
                <div class="col-md-6 col-4 align-self-center">
                    <button class="btn float-right hidden-sm-down btn-success add_group">
                        <i class="mdi mdi-plus-circle"></i> Добавить
                    </button>
                </div>
            {/if}
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
                                        <th class="" style="width: 150px">Номер группы</th>
                                        <th>Наименование группы</th>
                                        <th>Доступность</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if !empty($groups)}
                                        {foreach $groups as $group}
                                            <tr data-group='{$group|json_encode}'>
                                                <td class="group_front">{$group->number}</td>
                                                <td class="group_front">{$group->name}</td>
                                                <td>
                                                    {if $group->blocked === 'all'}
                                                        Везде
                                                    {elseif $group->blocked === 'online'}
                                                        Онлайн
                                                    {elseif $group->blocked === 'offline'}
                                                        Оффлайн
                                                    {elseif $group->blocked === 'nowhere'}
                                                        Нигде
                                                    {/if}
                                                </td>
                                                {if in_array($manager->role,['admin', 'developer'])}
                                                    <td>
                                                        <div class="btn btn-outline-warning to_edit">
                                                            Редактировать
                                                        </div>
                                                        {if $group->number != '00'}
                                                            <input type="button"
                                                                data-group="{$group->id}"
                                                                class="btn btn-outline-danger delete_group"
                                                                value="Удалить"
                                                            >
                                                        {/if}
                                                    </td>
                                                {/if}

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

<div
    id="add_group_modal"
    class="modal fade bd-example-modal-sm"
    tabindex="-1"
    role="dialog"
    data-group=""
    aria-labelledby="mySmallModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавить группу</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST">
                    <div class="form-group">
                        <label for="number-input" class="control-label">Номер группы</label>
                        <input id="number-input" type="text" class="form-control" name="number" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование группы</label>
                        <input id="name-input" type="text" class="form-control" name="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Доступность</label>
                        <select id="blocked-select" class="form-control" name="blocked">
                            <option value="all">Везде</option>
                            <option value="online">Онлайн</option>
                            <option value="offline">Оффлайн</option>
                            <option value="nowhere">Нигде</option>
                        </select>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input id="add-group-btn" type="button" class="btn btn-success" value="Создать">
                    <input id="edit-group-btn" type="button" class="btn btn-success" value="Редактировать">
                </form>
            </div>
        </div>
    </div>
</div>
