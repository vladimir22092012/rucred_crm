{$meta_title = 'Реестр компании' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet"/>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
{/capture}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/companies.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script>
        $(function () {

            let token_dadata = "25c845f063f9f3161487619f630663b2d1e4dcd7";

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

            $('.searchable').on('change', function (e) {
                e.preventDefault();

                $('.branches_list').show();

                $('.searchable').each(function () {
                    let value = $(this).val();
                    let index = $(this).parent().index() + 1;

                    if (value && value.length > 0) {
                        $('td:nth-child(' + index + ')').each(function () {
                            let find_value = $(this).text();
                            if (find_value.includes(value) === false) {
                                $(this).closest('.branches_list').hide();
                            }
                        });
                    }
                });
            });

            $('.show_attestations').hide();
            $('.show_payments').hide();
            $('.show_extras').hide();

            $('.select_document_type').on('change', function (e) {
                e.preventDefault();

                const selected_option = $('.select_document_type option:selected').val();

                $('div[class^="show_"]').hide();

                $('.show_' + selected_option).show();

                $('.import_workers_list').val('import_workers_list_' + selected_option);
            });

            $(document).on('click', '.send_file', function (e) {
                let form_data = new FormData();

                let doc_type = $('.select_document_type').val();

                form_data.append('file', $('#upload_file')[0].files[0]);
                form_data.append('company_id', $('#upload_file').attr('data-company'));
                form_data.append('date_attestation', $('.date_attestation').val());

                switch (doc_type) {
                    case 'attestations':
                        form_data.append('action', 'import_workers_list_attestations');
                        form_data.append('fio', $('input[name="fio"]').val());
                        form_data.append('created', $('input[name="created"]').val());
                        form_data.append('creator', $('input[name="creator"]').val());
                        form_data.append('category', $('input[name="category"]').val());
                        form_data.append('birth_date', $('input[name="birth_date"]').val());
                        break;

                    case 'payments':
                        form_data.append('action', 'import_payments_list');
                        form_data.append('fio', $('.show_payments').find('input[name="fio"]').val());
                        form_data.append('income', $('input[name="income"]').val());
                        form_data.append('avanse', $('input[name="avanse"]').val());
                        form_data.append('saved', $('input[name="saved"]').val());
                        form_data.append('payed', $('input[name="payed"]').val());
                        form_data.append('middle', $('input[name="middle"]').val());
                        form_data.append('ndfl', $('input[name="ndfl"]').val());
                        break;

                    case 'extras':
                        form_data.append('action', 'import_workers_list');
                        break;
                }

                $.ajax({
                    data: form_data,
                    dataType: 'JSON',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        console.log(resp);

                        if (resp['error']) {
                            Swal.fire({
                                title: resp['text'],
                                confirmButtonText: 'ОК',
                            });
                        } else {
                            Swal.fire({
                                title: 'Успешно'
                            });
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
                <h3 class="text-themecolor mb-0 mt-0">Реестр компании</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/">Справочники</a></li>
                    <li class="breadcrumb-item"><a href="/companies">Компании</a></li>
                    <li class="breadcrumb-item active">{$company->com_name}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">{$company->com_name}</h2>
                        <div class="table-responsive m-t-40">
                            <div class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <table id="config-table" class="table display table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th>Позиция</th>
                                        <th>Код</th>
                                        <th colspan="2">Описание</th>
                                        {if !in_array($manager->role, ['employer', 'underwriter'])}
                                            <th>
                                                <span>Блокировка</span>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" name="blocked_flag"
                                                           data-company-id="{$company->com_id}"
                                                           class="onoffswitch-checkbox action-block-company"
                                                           id="on_off_flag_{$company->com_id}"
                                                            {if $company->blocked} checked="true" value="1" {else} value="0"{/if}>
                                                    <label class="onoffswitch-label"
                                                           for="on_off_flag_{$company->com_id}">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </th>
                                            <th><input type="button"
                                                       class="btn btn-outline-info action-edit-company button-fixed"
                                                       value="Редактировать компанию"></th>
                                            <th><input type="button" data-company-id="{$company->com_id}"
                                                       class="btn btn-outline-danger action-delete-company button-fixed"
                                                       value="Удалить компанию"></th>
                                        {/if}
                                        <th>
                                            <div data-company="{$company->com_id}" data-group="{$company->gr_id}"
                                                 class="btn btn-outline-warning wrong_info" style="width: 200px">
                                                Сообщить о неточности
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Наименование компании</td>
                                        <td>{$company->gr_number}{$company->com_number}</td>
                                        <td colspan="7">{$company->com_name}</td>
                                    </tr>
                                    <tr>
                                        <td>Позиция</td>
                                        <td>{$company->gr_number}</td>
                                        <td colspan="7">{$company->gr_name}</td>
                                    </tr>
                                    <tr>
                                        <td>ИНН</td>
                                        <td colspan="7">{$company->inn}</td>
                                    </tr>
                                    <tr>
                                        <td>ОГРН</td>
                                        <td colspan="7">{$company->ogrn}</td>
                                    </tr>
                                    <tr>
                                        <td>КПП</td>
                                        <td colspan="7">{$company->kpp}</td>
                                    </tr>
                                    <tr>
                                        <td>Юридический адрес</td>
                                        <td colspan="7">{$company->jur_address}</td>
                                    </tr>
                                    <tr>
                                        <td>Адрес местонахождения</td>
                                        <td colspan="7">{$company->phys_address}</td>
                                    </tr>
                                    <tr>
                                        <td>Руководитель</td>
                                        <td colspan="7">{$company->eio_position} {$company->eio_fio}</td>
                                    </tr>
                                    <tr>
                                        <td {if !empty($docs)}rowspan="{count($docs)+1}"{/if}>Документы компании</td>
                                        <td>Дата документа</td>
                                        <td>Название документа</td>
                                        <td>Комментарий</td>
                                        <td>Скан</td>
                                        <td></td>
                                        <td>{if !in_array($manager->role, ['employer', 'underwriter'])}
                                                <input type="button" class="btn btn-outline-success add_document"
                                                       value="Добавить документ">
                                            {/if}
                                        </td>
                                    </tr>
                                    {if !empty($docs)}
                                        {foreach $docs as $doc}
                                            <tr>
                                                <td>{$doc->created|date}</td>
                                                <td>{$doc->name}</td>
                                                <td>{$doc->description}</td>
                                                <td><a download target="_blank"
                                                       href="{$config->back_url}/files/users/{$doc->filename}"><input
                                                                type="button" class="btn btn-outline-success"
                                                                value="Скачать"></a></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        {/foreach}
                                    {/if}
                                    <tr style="height: 50px">
                                        <td colspan="7"></td>
                                    </tr>
                                    {if $company->com_id == 2}
                                        <tr>
                                            <td rowspan="{count($settlements)+1}">Расчетные счета</td>
                                            <td>Наименование банка</td>
                                            <td>Номер расчетного счета</td>
                                            <td>Номер корреспондентского счета</td>
                                            <td>БИК</td>
                                            <td align="center">По умолчанию</td>
                                            <td>{if !in_array($manager->role, ['employer', 'underwriter'])}
                                                    <input type="button" class="btn btn-outline-success add_settlement"
                                                           value="Добавить счет">
                                                {/if}</td>
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
                                                           {if $settlement->std == 1}checked{/if}
                                                            {if in_array($manager->role, ['employer', 'underwriter'])}disabled{/if}>
                                                </td>
                                                <td>
                                                    {if !in_array($manager->role, ['employer', 'underwriter'])}
                                                        <input type="button" data-settlement="{$settlement->id}"
                                                               class="btn btn-outline-warning update_settlement"
                                                               value="Ред">
                                                        <input type="button" data-settlement="{$settlement->id}"
                                                               class="btn btn-outline-danger delete_settlement"
                                                               value="Удалить">
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    {/if}
                                    <tr style="height: 50px">
                                        <td colspan="7"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="{count($branches)+2}">Филиалы и даты выплат</td>
                                        <td>Код</td>
                                        <td>Наименование филиала</td>
                                        <td>Дата выплаты</td>
                                        <td>Контактная информация:</td>
                                        <td colspan="2">
                                            {if !in_array($manager->role, ['employer', 'underwriter'])}
                                                <button class="btn hidden-sm-down btn-outline-success add-company-modal">
                                                    <i class="mdi mdi-plus-circle"></i> Добавить филиал
                                                </button>
                                            {/if}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td></td>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td colspan="2"></td>
                                    </tr>
                                    {foreach $branches as $branch}
                                        <tr class="branches_list">
                                            <td>{$company->gr_number}{$company->com_number}-{$branch->number}</td>
                                            <td>{$branch->name}</td>
                                            <td>{$branch->payday}</td>
                                            <td>{$branch->fio} {$branch->phone}</td>
                                            {if !in_array($manager->role, ['employer', 'underwriter'])}
                                                <td>
                                                    {if $branch->number != '00'}
                                                        <input type="button" data-branch-id="{$branch->id}"
                                                               class="btn btn-outline-danger delete_branch"
                                                               value="Удалить">
                                                    {/if}
                                                </td>
                                                <td>
                                                    <input type="button" data-branch-id="{$branch->id}"
                                                           class="btn btn-outline-warning edit_branch"
                                                           value="Редактировать">
                                                </td>
                                                <td>
                                                    <div class="btn btn-outline-primary branch_wrong">
                                                        Сообщить о неточности
                                                    </div>
                                                </td>
                                            {/if}
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jsgrid-grid-body">
                    {if !empty($managers)}
                        <h4>Пользователи CRM, связанные с данной компанией</h4>
                        <table style="width: 100%" class="jsgrid-table table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Логин</th>
                                <th>ФИО</th>
                                <th>Роль</th>
                                <th>Срок доступа</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $managers as $manager}
                                <tr>
                                    <td>{$manager->login}</td>
                                    <td><a target="_blank" href="/manager/{$manager->id}">{$manager->name}</a></td>
                                    <td>{$label_class="info"}
                                        {if $manager->role == 'developer' || $manager->role == 'technic'}{$label_class="danger"}{/if}
                                        {if $manager->role == 'admin' || $manager->role == 'chief_collector' || $m->role == 'team_collector'}{$label_class="success"}{/if}
                                        {if $manager->role == 'verificator' || $manager->role == 'user'}{$label_class="warning"}{/if}
                                        {if $manager->role == 'collector'}{$label_class="primary"}{/if}

                                        <span class="label label-{$label_class}">
                                                        {if $manager->role == 'developer'}
                                                            Разработчик
                                                        {elseif $manager->role == 'admin'}
                                                            Админ
                                                        {elseif $manager->role == 'middle'}
                                                            Миддл
                                                        {elseif $manager->role == 'underwriter'}
                                                            Андерайтер
                                                        {elseif $manager->role == 'employer'}
                                                            Работодатель
                                                        {/if}</span>
                                    </td>
                                    <td>{$manager->credential_type}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    {/if}
                </div>
                <br>
                <div class="card">
                    <div class="card-body">
                        <div style="display: flex">
                            <h4 class="card-title" style="margin-top: 8px">Проверки сотрудников от </h4>
                            <input class="form-control daterange date_attestation"
                                   style="margin-left: 25px; width: 200px">
                        </div>
                        <br>
                        <p class="card-text">В этот раздел можно импортировать проверочные списки сотрудников компании
                            для проверки
                            при одобрении заявки на кредит</p>
                        <div style="color: darkred;">
                            <strong>Внимание! Перед загрузкой необходимо удалить все заголовки столбцов, до самих
                                данных, которые необходимо загрузить.</strong>
                        </div>
                        <br>
                        <div>
                            <form class="dropzone import_workers_list_form" id="file-employers-upload">
                                <label>Выберите тип документа: </label>
                                <select class="select_document_type mb-4" aria-label="Выберите тип документа">
                                    <option selected>Выберите тип документа</option>
                                    <option value="attestations">Аттестации</option>
                                    <option value="payments">Выплаты</option>
                                    <option value="extras">Дополнительно</option>
                                </select>
                                <input type="file" id="upload_file" data-company="{$company->com_id}"
                                       style="margin-left: 25px">
                                <div class="btn btn-outline-success float-right send_file">Отправить</div>
                                <div class="show_attestations">
                                    <h3>Поля для документов об аттестации</h3>
                                    <div>
                                        <table class="table">
                                            <tr>
                                                <th>ФИО</th>
                                                <th>Дата действия</th>
                                                <th>Кем выдано</th>
                                                <th>Категория</th>
                                                <th>Дата рождения</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input class="form-control" name="fio" placeholder="Например, A">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="created"
                                                           placeholder="Например, B">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="creator"
                                                           placeholder="Например, C">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="category"
                                                           placeholder="Например, D">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="birth_date"
                                                           placeholder="Например, E">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="show_payments">
                                    <h3>Поля для документов о выплатах</h3>
                                    <div>
                                        <table class="table">
                                            <tr>
                                                <th>ФИО</th>
                                                <th>Всего начислено</th>
                                                <th>Всего удержано</th>
                                                <th>Выплата аванса</th>
                                                <th>Выплата зарплаты</th>
                                                <th>Выплата в межрасчетный период</th>
                                                <th>НФДЛ</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input class="form-control" name="fio" placeholder="Например, A">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="income" placeholder="Например, B">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="saved" placeholder="Например, C">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="avanse" placeholder="Например, D">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="payed" placeholder="Например, E">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="middle" placeholder="Например, F">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="ndfl" placeholder="Например, G">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </form>
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
                        <label for="name_settlement" class="control-label">Наименование банка:</label>
                        <input type="text" class="form-control" name="name" id="name_settlement" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="payment" class="control-label">Расчетный счет:</label>
                        <input type="text" class="form-control" name="payment" id="payment" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="correspondent_account" class="control-label">Корреспондентский счет:</label>
                        <input type="text" class="form-control" name="cors" id="correspondent_account" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="bik_settlement" class="control-label">БИК: </label>
                        <input type="text" class="form-control" name="bik" id="bik_settlement" value=""/>
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

<div id="add_document" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить счет</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_document_form">
                    <input type="hidden" name="action" value="add_document">
                    <input type="hidden" name="company_id" value="{$company->com_id}">
                    <div class="form-group">
                        <label for="date_doc" class="control-label">Дата документа:</label>
                        <input type="text" class="form-control daterange" name="date_doc" id="date_doc" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Название документа:</label>
                        <input type="text" class="form-control" name="name" id="name" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">Комментарий:</label>
                        <input type="text" class="form-control" name="comment" id="comment"
                               value=""/>
                    </div>
                    <div class="form-group">
                        <label for="doc" class="control-label">Прикрепить документ</label>
                        <input type="file" class="custom-file-control" name="doc" id="doc" value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="button" class="btn btn-success action_add_doc" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>
