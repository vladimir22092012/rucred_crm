{$meta_title = 'Справочник коммуникаций' scope=parent}

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
            $(document).on('click', '.add_theme', function () {
                $('#modal_add_theme').modal();

                $(document).on('click', '.save', function (e) {
                    e.preventDefault();

                    let form = $('#add_theme_form').serialize();

                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: form,
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
            });

            $('.to_edit').on('click', function () {

                let id = $(this).attr('id');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'get_theme',
                        id: id
                    },
                    success: function (theme) {
                        $('#edit_theme_form')[0].reset();
                        $('#modal_edit_theme').find('input[name="number"]').val(theme['number']);
                        $('#modal_edit_theme').find('textarea[name="name"]').val(theme['name']);
                        $('#modal_edit_theme').find('input[name="head"]').val(theme['head']);
                        $('#modal_edit_theme').find('textarea[name="text"]').val(theme['text']);
                        $('#modal_edit_theme').find('input[name="theme_id"]').val(theme['id']);
                        if (theme['need_response'] == 1) {
                            $('#modal_edit_theme').find('input[name="need_response"]').prop('checked', true);
                        } else {
                            $('#modal_edit_theme').find('input[name="need_response"]').prop('checked', false);
                        }

                        for(let permission of theme['manager_permissions']){
                            $('#modal_edit_theme').find('.role-id-'+permission['role_id']).prop('checked', true);
                        }

                        $('#modal_edit_theme').modal();
                    }
                });
            });

            $('.save_edit').on('click', function (e) {
                e.preventDefault();

                let form = $('#edit_theme_form').serialize();


                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: form,
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
                });
            });

            $('.delete').on('click', function (e) {
                e.preventDefault();

                let id = $(this).attr('data-theme');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'delete_theme',
                        id: id
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
                });
            });

            $('.searchable').on('change', function (e) {
                e.preventDefault();

                $('table tbody tr').show();

                $('.searchable').each(function () {
                    let value = $(this).val();
                    value = value.toLowerCase();
                    let index = $(this).parent().index() + 1;

                    if (value && value.length > 0) {
                        $('td:nth-child(' + index + ')').each(function () {
                            let find_value = $(this).text().toLowerCase();
                            console.log(find_value, value);
                            if (find_value.includes(value) === false) {
                                $(this).closest('tr[class="codes"]').hide();
                            }
                        });
                    }
                });
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
                <h3 class="text-themecolor mb-0 mt-0">Справочник коммуникаций</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Справочник коммуникаций</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                {if in_array($manager->role, ['developer', 'admin'])}
                    <button class="btn float-right hidden-sm-down btn-success add_theme">
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
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'id asc'}<a href="{url page=null sort='id desc'}">
                                                    ID</a>
                                            {else}<a href="{url page=null sort='id asc'}">ID</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'number asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'number desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'number asc'}<a href="{url page=null sort='number desc'}">
                                                    Номер</a>
                                            {else}<a href="{url page=null sort='number asc'}">Номер</a>{/if}
                                        </th>
                                        <th class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'name asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'name desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'name asc'}<a href="{url page=null sort='name desc'}">
                                                    Наименование</a>
                                            {else}<a href="{url page=null sort='name asc'}">Наименование</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'head asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'head desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'head asc'}<a href="{url page=null sort='head desc'}">
                                                    Заголовок</a>
                                            {else}<a href="{url page=null sort='head asc'}">Заголовок</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'text asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'text desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'text asc'}<a href="{url page=null sort='text desc'}">
                                                    Текст тикета</a>
                                            {else}<a href="{url page=null sort='text asc'}">Текст тикета</a>{/if}
                                        </th>
                                        <th>Доступность для ролей</th>
                                        <th>Need response</th>
                                        <th></th>
                                    </tr>
                                    <tr class="jsgrid-filter-row">
                                        <td></td>
                                        <td></td>
                                        <td style="width: 300px;" class="jsgrid-cell"><input type="text"
                                                                                             class="form-control searchable">
                                        </td>
                                        <td class="jsgrid-cell"><input type="text" class="form-control searchable">
                                        </td>
                                        <td class="jsgrid-cell" style="width: 250px;"><input type="text"
                                                                                             class="form-control searchable">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if isset($themes)}
                                        {foreach $themes as $theme}
                                            <tr class="codes">
                                                <td>{$theme->id}</td>
                                                <td style="width: 60px">{$theme->number}</td>
                                                <td style="width: 300px">{$theme->name}</td>
                                                <td>{$theme->head}</td>
                                                <td style="width: 250px;">{$theme->text}</td>
                                                <td>
                                                    <div style="display: flex; flex-direction: column">
                                                        {foreach $manager_roles as $role}
                                                            <div style="display: flex;">
                                                                <input type="checkbox" disabled
                                                                        {foreach $theme->manager_permissions as $permissions}
                                                                            {if $role->id == $permissions->role_id}
                                                                                checked
                                                                            {/if}
                                                                        {/foreach}>
                                                                <label style="margin-left: 10px">{$role->translate}</label>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="checkbox" name="need_response"
                                                               class="custom-checkbox" disabled
                                                               {if $theme->need_response == 1}checked{/if}>
                                                    </div>
                                                </td>
                                                {if in_array($manager->role, ['developer', 'admin'])}
                                                    <td>
                                                        <div class="btn btn-outline-warning to_edit" id="{$theme->id}">
                                                            Редактировать
                                                        </div>
                                                    </td>
                                                    <td><input type="button"
                                                               data-theme="{$theme->id}"
                                                               class="btn btn-outline-danger delete"
                                                               value="Удалить">
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

<div id="modal_add_theme" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить тему</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form id="add_theme_form">
                    <input type="hidden" name="action" value="add_theme">
                    <input type="hidden" name="theme_id" value="">
                    <div class="form-group">
                        <label for="name" class="control-label">Номер:</label>
                        <input type="text" class="form-control" name="number" value="">
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование:</label>
                        <input type="text" class="form-control" name="name" value="">
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Заголовок:</label>
                        <input type="text" class="form-control" name="head" value="">
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Текст тикета:</label>
                        <textarea type="text" class="form-control" name="text"></textarea>
                    </div>
                    <div class="btn btn-danger " data-dismiss="modal">Отмена</div>
                    <div class="btn btn-success save">Сохранить</div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="modal_edit_theme" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать тему</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form id="edit_theme_form">
                    <input type="hidden" name="action" value="update_theme">
                    <input type="hidden" name="theme_id" value="">
                    <div class="form-group">
                        <label for="name" class="control-label">Номер:</label>
                        <input type="text" class="form-control" name="number" value="">
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование:</label>
                        <textarea class="form-control" name="name"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Заголовок:</label>
                        <input type="text" class="form-control" name="head" value="">
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Текст тикета:</label>
                        <textarea type="text" class="form-control" name="text"></textarea>
                    </div>
                    <div class="form-group" style="display: flex; flex-direction: column">
                        <label class="control-label">Кто видит во входящих: </label>
                        {foreach $manager_roles as $role}
                            <div style="display: flex;">
                                <input type="checkbox" class="role-id-{$role->id}" name="manager_permissions[][id]" value="{$role->id}">
                                <label style="margin-left: 10px">{$role->translate}</label>
                            </div>
                        {/foreach}
                    </div>
                    <div class="form-group">
                        <label for="need_response" class="control-label">Need response:</label>
                        <input type="checkbox" name="need_response" class="custom-checkbox" value="1">
                    </div>
                    <div class="btn btn-danger " data-dismiss="modal">Отмена</div>
                    <div class="btn btn-success save_edit">Сохранить</div>
                </form>
            </div>
        </div>
    </div>
</div>