{$meta_title = 'Компании' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet"/>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
{/capture}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/companies.js"></script>
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script src="https://unpkg.com/validator@latest/validator.min.js"></script>
    <script>
        $(function () {

            let token_dadata = "25c845f063f9f3161487619f630663b2d1e4dcd7";

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
            });

            $('#inn').suggestions({
                token: token_dadata,
                type: "party",
                minChars: 3,
                onSelect: function (suggestion) {
                    $(this).val(suggestion.data.inn);
                    $('#kpp').val(suggestion.data.kpp);
                    $('#ogrn').val(suggestion.data.ogrn);
                    $('#name').val(suggestion.value);
                    $('#eio_fio').val(suggestion.data.management.name);
                    $('#eio_position').val(suggestion.data.management.post);
                    $('#jur_address').val(suggestion.data.address.value);
                }
            });

            $('#name').suggestions({
                token: token_dadata,
                type: "party",
                minChars: 3,
                onSelect: function (suggestion) {
                    $(this).val(suggestion.value);
                    $('#kpp').val(suggestion.data.kpp);
                    $('#ogrn').val(suggestion.data.ogrn);
                    $('#inn').val(suggestion.data.inn);
                    $('#eio_fio').val(suggestion.data.management.name);
                    $('#eio_position').val(suggestion.data.management.post);
                    $('#jur_address').val(suggestion.data.address.value);
                }
            });

            $('.searchable:not(select)').on('change', function (e) {
                e.preventDefault();

                $('table tbody tr').show();

                $('.searchable:not(select)').each(function () {
                    let value = $(this).val();
                    let index = $(this).parent().index() + 1;

                    if (value && value.length > 0) {
                        $('td:nth-child(' + index + ')').each(function () {
                            let find_value = $(this).text().toLowerCase();
                            if (find_value.includes(value) === false) {
                                $(this).closest('tr').hide();
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
                <h3 class="text-themecolor mb-0 mt-0">Компании</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/">Справочники</a></li>
                    <li class="breadcrumb-item active"><a href="/companies">Компании</a></li>
                </ol>
            </div>
            {if !in_array($manager->role, ['employer', 'underwriter'])}
                <div class="col-md-6 col-4 align-self-center">
                    <button class="btn float-right hidden-sm-down btn-success add-company-modal">
                        Добавить
                    </button>
                </div>
            {/if}
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
                                <table id="config-table" class="table display table-striped dataTable"
                                       style="font-size: 14px">
                                    <thead>
                                    <tr>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'group_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'group_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'group_asc'}<a href="{url page=null sort='group_desc'}">
                                                    Группа, №</a>
                                            {else}<a href="{url page=null sort='group_asc'}">Группа, №</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'company_num_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'company_num_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'company_num_asc'}<a
                                                href="{url page=null sort='company_num_desc'}">
                                                    Компания №</a>
                                            {else}<a href="{url page=null sort='company_num_asc'}">Компания №</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'company_name_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'company_name_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'company_name_asc'}<a
                                                href="{url page=null sort='company_name_desc'}">
                                                    Компания</a>
                                            {else}<a href="{url page=null sort='company_name_asc'}">Компания</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'eio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'eio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'eio_asc'}<a href="{url page=null sort='eio_desc'}">
                                                    Должность</a>
                                            {else}<a href="{url page=null sort='eio_asc'}">Должность</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'fio_asc'}<a href="{url page=null sort='fio_desc'}">
                                                    ФИО</a>
                                            {else}<a href="{url page=null sort='fio_asc'}">ФИО</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'inn_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'inn_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'inn_asc'}<a href="{url page=null sort='inn_desc'}">
                                                    ИНН</a>
                                            {else}<a href="{url page=null sort='inn_asc'}">ИНН</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'ogrn_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'ogrn_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'ogrn_asc'}<a href="{url page=null sort='ogrn_desc'}">
                                                    ОГРН</a>
                                            {else}<a href="{url page=null sort='ogrn_asc'}">ОГРН</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'kpp_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'kpp_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'kpp_asc'}<a href="{url page=null sort='kpp_desc'}">
                                                    КПП</a>
                                            {else}<a href="{url page=null sort='kpp_asc'}">КПП</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'jur_addr_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'jur_addr_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'jur_addr_asc'}<a href="{url page=null sort='jur_addr_desc'}">
                                                    Юр Адрес</a>
                                            {else}<a href="{url page=null sort='jur_addr_asc'}">Юр Адрес</a>{/if}
                                        </th>
                                        <th
                                                class="jsgrid-header-cell jsgrid-header-sortable{if $sort == 'fakt_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fakt_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'fakt_asc'}<a href="{url page=null sort='fakt_desc'}">
                                                    Местонахождение</a>
                                            {else}<a href="{url page=null sort='fakt_asc'}">Местонахождение</a>{/if}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="width: 120px">
                                            <select class="form-control" id="group_filter">
                                                <option value="none" selected>Фильтр</option>
                                                {foreach $groups as $group}
                                                    <option value="{$group->number}">{$group->number}
                                                        , {$group->name}</option>
                                                {/foreach}
                                            </select>
                                        </th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                        <th class=""><input type="text" class="form-control searchable"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {if !empty($companies)}
                                        {foreach $companies as $company}
                                            <tr class="companies" id="{$company->gr_number}"
                                                onclick="location.href='company/{$company->com_id}'"
                                                onmouseover="this.style.backgroundColor='#AEA8F5';"
                                                onmouseout="this.style.backgroundColor='white';">
                                                <td>{$company->gr_number}, {$company->gr_name}
                                                    {if $company->offline_blocked == 1}
                                                        <br>
                                                        <label class="badge badge-danger">Оффлайн блокировка</label>
                                                    {/if}{if $company->online_blocked == 1}
                                                        <br>
                                                        <label class="badge badge-danger">Онлайн блокировка</label>
                                                    {/if}</td>
                                                <td class="com-number">{$company->gr_number}{$company->com_number}</td>
                                                <td class="com-name">{$company->com_name} {if ($company->blocked)}
                                                        <span class="label label-danger">Blocked</span>
                                                    {/if}</td>
                                                <td class="eio-pos">{$company->eio_position}</td>
                                                <td class="eio-fio">{$company->eio_fio}</td>
                                                <td class="inn">{$company->inn}</td>
                                                <td class="ogrn">{$company->ogrn}</td>
                                                <td class="kpp">{$company->kpp}</td>
                                                <td class="jur">{$company->jur_address}</td>
                                                <td class="phys">{$company->phys_address}</td>
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

<div id="add-company-modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
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
                                <option value="{$group->id}">{$group->name}</option>
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
                        <input type="button" class="btn btn-success add_company" value="Сохранить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>