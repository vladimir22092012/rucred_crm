{$meta_title = 'Реестр компании' scope=parent}

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
                <h3 class="text-themecolor mb-0 mt-0">Реестр компании</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/">Справочники</a></li>
                    <li class="breadcrumb-item"><a href="/companies">Компании</a></li>
                    <li class="breadcrumb-item active">Реестр компании</li>
                </ol>
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
                                        <th>Позиция</th>
                                        <th>Код</th>
                                        <th colspan="2">Описание</th>
                                        <th>
                                            <span>Блокировка</span>
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="blocked_flag"
                                                       data-company-id="{$company->com_id}" class="onoffswitch-checkbox action-block-company"
                                                       id="on_off_flag_{$company->com_id}"
                                                        {if $company->blocked} checked="true" value="1" {else} value="0"{/if}>
                                                <label class="onoffswitch-label" for="on_off_flag_{$company->com_id}">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </th>
                                        <th><input type="button" class="btn btn-outline-info action-edit-company"
                                                   value="Редактировать компанию"></th>
                                        <th><input type="button" data-company-id="{$company->com_id}"
                                                   class="btn btn-outline-danger action-delete-company"
                                                   value="Удалить компанию"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Наименование компании</td>
                                        <td>{$company->gr_number}{$company->com_number}</td>
                                        <td colspan="5">{$company->com_name}</td>
                                    </tr>
                                    <tr>
                                        <td>Позиция</td>
                                        <td>{$company->gr_number}</td>
                                        <td colspan="5">{$company->gr_name}</td>
                                    </tr>
                                    <tr>
                                        <td>ИНН</td>
                                        <td colspan="6">{$company->inn}</td>
                                    </tr>
                                    <tr>
                                        <td>ОГРН</td>
                                        <td colspan="6">{$company->ogrn}</td>
                                    </tr>
                                    <tr>
                                        <td>КПП</td>
                                        <td colspan="6">{$company->kpp}</td>
                                    </tr>
                                    <tr>
                                        <td>Юридический адрес</td>
                                        <td colspan="6">{$company->jur_address}</td>
                                    </tr>
                                    <tr>
                                        <td>Адрес местонахождения</td>
                                        <td colspan="6">{$company->phys_address}</td>
                                    </tr>
                                    <tr>
                                        <td>Руководитель</td>
                                        <td colspan="6">{$company->eio_position} {$company->eio_fio}</td>
                                    </tr>
                                    {if $company->com_id == 2}
                                        <tr>
                                            <td rowspan="{count($settlements)+1}">Расчетные счета</td>
                                            <td>Наименование банка</td>
                                            <td>Номер расчетного счета</td>
                                            <td>Номер корреспондентского счета</td>
                                            <td>БИК</td>
                                            <td align="center">По умолчанию</td>
                                            <td><input type="button" class="btn btn-outline-success add_settlement"
                                                       value="Добавить счет"></td>
                                        </tr>
                                        {foreach $settlements as $settlement}
                                            <tr>
                                                <td>{$settlement->name}</td>
                                                <td>{$settlement->payment}</td>
                                                <td>{$settlement->cors}</td>
                                                <td>{$settlement->bik}</td>
                                                <td align="center">
                                                    <input type="radio" class="form-check-input std_flag" name="std"
                                                           id="std{$settlement->id}"
                                                           value="{$settlement->id}"
                                                           {if $settlement->std == 1}checked{/if}>
                                                </td>
                                                <td>
                                                    <input type="button" data-settlement="{$settlement->id}"
                                                           class="btn btn-outline-warning update_settlement"
                                                           value="Ред">
                                                    <input type="button" data-settlement="{$settlement->id}"
                                                           class="btn btn-outline-danger delete_settlement"
                                                           value="Удалить">
                                                </td>
                                            </tr>
                                        {/foreach}
                                    {/if}
                                    <tr style="height: 50px">
                                        <td colspan="7"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="{count($branches)+1}">Филиалы и даты выплат</td>
                                        <td>Код</td>
                                        <td>Наименование филиала</td>
                                        <td>Дата выплаты</td>
                                        <td>Контактная информация:</td>
                                        <td colspan="2">
                                            <button class="btn hidden-sm-down btn-outline-success add-company-modal">
                                                <i class="mdi mdi-plus-circle"></i> Добавить филиал
                                            </button>
                                        </td>
                                    </tr>
                                    {foreach $branches as $branch}
                                        <tr>
                                            <td>{$company->gr_number}{$company->com_number}-{$branch->number}</td>
                                            <td>{$branch->name}</td>
                                            <td>{$branch->payday}</td>
                                            <td>{$branch->fio} {$branch->phone}</td>
                                            <td>
                                                {if $branch->number != '00'}
                                                    <input type="button" data-branch-id="{$branch->id}"
                                                           class="btn btn-outline-danger delete_branch" value="Удалить">
                                                {/if}
                                            </td>
                                            <td>
                                                <input type="button" data-branch-id="{$branch->id}"
                                                       class="btn btn-outline-warning edit_branch"
                                                       value="Редактировать">
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

<div id="modal_add_branch" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить филиал</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="alert" style="display:none"></div>
                <form id="add_branche_form">
                    <input type="hidden" name="action" value="add_branch">
                    <input type="hidden" name="group_id" value="{$company->gr_id}">
                    <input type="hidden" name="company_id" value="{$company->com_id}">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование филиала</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="eio_position" class="control-label">День выплаты:</label>
                        <select class="form-control" name="payday" id="payday">
                            {for $i = 1 to 31}
                                <option value="{$i}" {if $i == 10}selected{/if}>{$i}</option>
                            {/for}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fio" class="control-label">Начальник ТБ:</label>
                        <input type="text" class="form-control" name="fio" id="fio" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="control-label">Контактный телефон:</label>
                        <input type="text" class="form-control" name="phone" id="phone" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="submit" class="btn btn-success add_branche" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_edit_company" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Редактировать компанию</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="alert" style="display:none"></div>
                <form method="POST" id="edit_company_form">
                    <input type="hidden" name="action" value="edit_company">
                    <input type="hidden" name="company_id" value="{$company->com_id}">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование компании</label>
                        <input type="text" class="form-control" name="name" id="name"
                               value="{$company->com_name|escape}"/>
                    </div>
                    <div class="form-group">
                        <label for="eio_position" class="control-label">Должность ЕИО:</label>
                        <input type="text" class="form-control" name="eio_position" id="eio_position"
                               value="{$company->eio_position}"/>
                    </div>
                    <div class="form-group">
                        <label for="eio_fio" class="control-label">ФИО ЕИО:</label>
                        <input type="text" class="form-control" name="eio_fio" id="eio_fio"
                               value="{$company->eio_fio}"/>
                    </div>
                    <div class="form-group">
                        <label for="inn" class="control-label">ИНН:</label>
                        <input type="text" class="form-control" name="inn" id="inn" value="{$company->inn}"/>
                    </div>
                    <div class="form-group">
                        <label for="ogrn" class="control-label">ОГРН:</label>
                        <input type="text" class="form-control" name="ogrn" id="ogrn" value="{$company->ogrn}"/>
                    </div>
                    <div class="form-group">
                        <label for="kpp" class="control-label">КПП:</label>
                        <input type="text" class="form-control" name="kpp" id="kpp" value="{$company->kpp}"/>
                    </div>
                    <div class="form-group">
                        <label for="jur_address" class="control-label">Юридический адрес:</label>
                        <input type="text" class="form-control" name="jur_address" id="jur_address"
                               value="{$company->jur_address}"/>
                    </div>
                    <div class="form-group">
                        <label for="phys_address" class="control-label">Адрес местонахождения:</label>
                        <input type="text" class="form-control" name="phys_address" id="phys_address"
                               value="{$company->phys_address}"/>
                    </div>
                    <div class="form-group">
                        <label for="payday" class="control-label">День выплаты по умолчанию:</label>
                        <select class="form-control" name="payday" id="payday">
                            {for $i = 1 to 31}
                                <option value="{$i}" {if $i == 10}selected{/if}>{$i}</option>
                            {/for}
                        </select>
                    </div>
                    <div>
                        <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                        <input type="button" class="btn btn-success save" value="Сохранить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="edit_branch" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Редактировать филиал</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="alert" style="display:none"></div>
                <form method="POST" id="edit_branch_form">
                    <input type="hidden" name="action" value="edit_branch">
                    <input type="hidden" class="edit_branch_form" name="branch_id" value="">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование филиала</label>
                        <input type="text" class="form-control edit_branch_form" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="eio_position" class="control-label">День выплаты:</label>
                        <select class="form-control edit_branch_form" name="payday" id="payday">
                            {for $i = 1 to 31}
                                <option value="{$i}">{$i}</option>
                            {/for}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fio" class="control-label">Начальник ТБ:</label>
                        <input type="text" class="form-control edit_branch_form" name="fio" id="fio" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="control-label">Контактный телефон:</label>
                        <input type="text" class="form-control edit_branch_form" name="phone" id="phone" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_edit_branch" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>

<div id="add_settlement" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить счет</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_settlement_form">
                    <input type="hidden" name="action" value="add_settlement">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование банка:</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="payment" class="control-label">Расчетный счет:</label>
                        <input type="text" class="form-control" name="payment" id="payment" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="cors" class="control-label">Корреспондентский счет:</label>
                        <input type="text" class="form-control" name="cors" id="cors" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="bik" class="control-label">БИК: </label>
                        <input type="text" class="form-control" name="bik" id="bik" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_add_settlement" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>

<div id="update_settlement" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить счет</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="update_settlement_form">
                    <input type="hidden" name="action" value="update_settlement">
                    <input type="hidden" class="update_settlement_form" name="settlement_id" value="">
                    <div class="form-group">
                        <label for="name" class="control-label">Наименование банка:</label>
                        <input type="text" class="form-control update_settlement_form" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="payment" class="control-label">Расчетный счет:</label>
                        <input type="text" class="form-control update_settlement_form" name="payment" id="payment"
                               value=""/>
                    </div>
                    <div class="form-group">
                        <label for="cors" class="control-label">Корреспондентский счет:</label>
                        <input type="text" class="form-control update_settlement_form" name="cors" id="cors" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="bik" class="control-label">БИК: </label>
                        <input type="text" class="form-control update_settlement_form" name="bik" id="bik" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_update_settlement" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>