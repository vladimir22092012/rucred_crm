{$meta_title = 'Список тем КП' scope=parent}

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
                        $('#modal_edit_theme').find('input[name="number"]').val(theme['number']);
                        $('#modal_edit_theme').find('textarea[name="name"]').val(theme['name']);
                        $('#modal_edit_theme').find('input[name="head"]').val(theme['head']);
                        $('#modal_edit_theme').find('textarea[name="text"]').val(theme['text']);
                        $('#modal_edit_theme').find('input[name="theme_id"]').val(theme['id']);
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
                <h3 class="text-themecolor mb-0 mt-0">Справочник тем КП</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Справочник тем КП</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                {if !in_array($manager->role, ['employer', 'underwriter'])}
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
                                        <th class="">ID</th>
                                        <th class="">Номер</th>
                                        <th class="" style="width: 300px">Наименование</th>
                                        <th>Заголовок</th>
                                        <th>Текст тикета</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if isset($themes)}
                                        {foreach $themes as $theme}
                                            <tr>
                                                <td>{$theme->id}</td>
                                                <td style="width: 60px">{$theme->number}</td>
                                                <td style="width: 500px">{$theme->name}</td>
                                                <td>{$theme->head}</td>
                                                <td>{$theme->text}</td>
                                                {if !in_array($manager->role,['employer', 'underwriter'])}
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
                    <div class="btn btn-danger " data-dismiss="modal">Отмена</div>
                    <div class="btn btn-success save_edit">Сохранить</div>
                </form>
            </div>
        </div>
    </div>
</div>