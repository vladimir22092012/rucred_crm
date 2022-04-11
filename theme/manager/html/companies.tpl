{$meta_title = 'Компании' scope=parent}

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

            $('.add_company').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    method: 'POST',
                    data: $('#add_company_form').serialize(),
                    success: function (resp) {
                        if (resp) {
                            $('.alert-danger').text(resp);
                            $('.alert-danger').fadeIn();
                        }
                        else {
                            location.reload();
                        }
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
                <h3 class="text-themecolor mb-0 mt-0">Компании</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Главная</li>
                    <li class="breadcrumb-item">Справочники</li>
                    <li class="breadcrumb-item active">Компании</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                <button class="btn float-right hidden-sm-down btn-success add-company-modal">
                    <i class="mdi mdi-plus-circle"></i> Добавить
                </button>
            </div>
            {if !empty($error)}
                <div class="alert alert-danger">{$error}</div>
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
                                        <th class="">Группа</th>
                                        <th class="">Номер</th>
                                        <th class="">Компания</th>
                                        <th class="">Наименование группы</th>
                                        <th class="">Наименование компании</th>
                                        <th class="">Должность ЕИО</th>
                                        <th class="">ФИО ЕИО</th>
                                        <th class="">ИНН</th>
                                        <th class="">ОГРН</th>
                                        <th class="">КПП</th>
                                        <th class="">Юридический адрес</th>
                                        <th class="">Адрес местонахождения</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if !empty($companies)}
                                        {foreach $companies as $company}
                                            <tr onclick="location.href='company/{$company->id}'"
                                                onmouseover="this.style.backgroundColor='#AEA8F5';"
                                                onmouseout="this.style.backgroundColor='white';">
                                                <td>{$company->gr_number}</td>
                                                <td>{$company->com_number}</td>
                                                <td>{$company->gr_number}{$company->com_number}</td>
                                                <td>{$company->gr_name}</td>
                                                <td>{$company->com_name}</td>
                                                <td>{$company->eio_position}</td>
                                                <td>{$company->eio_fio}</td>
                                                <td>{$company->inn}</td>
                                                <td>{$company->ogrn}</td>
                                                <td>{$company->kpp}</td>
                                                <td>{$company->jur_address}</td>
                                                <td>{$company->phys_address}</td>
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
                <h4 class="modal-title">Добавить компанию</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_company_form">
                    <input type="hidden" name="action" value="add_company">
                    <div class="form-group">
                        <label for="group_id" class="control-label">Группа:</label>
                        <select class="form-control" id="group_id" name="group">
                            {foreach $groups as $group}
                                <option value="{$group->number}">{$group->name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование компании</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="eio_position" class="control-label">Должность ЕИО:</label>
                        <input type="text" class="form-control" name="eio_position" id="eio_position" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="eio_fio" class="control-label">ФИО ЕИО:</label>
                        <input type="text" class="form-control" name="eio_fio" id="eio_fio" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="inn" class="control-label">ИНН:</label>
                        <input type="text" class="form-control" name="inn" id="inn" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="ogrn" class="control-label">ОГРН:</label>
                        <input type="text" class="form-control" name="ogrn" id="ogrn" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="kpp" class="control-label">КПП:</label>
                        <input type="text" class="form-control" name="kpp" id="kpp" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="jur_address" class="control-label">Юридический адрес:</label>
                        <input type="text" class="form-control" name="jur_address" id="jur_address" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="phys_address" class="control-label">Адрес местонахождения:</label>
                        <input type="text" class="form-control" name="phys_address" id="phys_address" value=""/>
                    </div>
                    <div>
                        <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                        <input type="button" class="btn btn-success add_company" value="Сохранить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>