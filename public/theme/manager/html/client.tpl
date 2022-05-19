{$meta_title="`$client->lastname` `$client->firstname` `$client->patronymic`" scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/order.js?v=1.16"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/movements.app.js"></script>
    <script>
        $(function () {
            let phone_num = "{$client->phone_mobile}";
            let firstname = "{$client->firstname}";
            let lastname = "{$client->lastname}";
            let patronymic = "{$client->patronymic}";

            $.ajax({
                url: "ajax/BlacklistCheck.php",
                data: {
                    phone_num: phone_num,
                    firstname: firstname,
                    lastname: lastname,
                    patronymic: patronymic
                },
                method: 'POST',
                success: function (suc) {
                    if (suc == 1) {
                        $('.form-check-input').attr('checked', 'checked');
                    }
                }
            });

            $(document).on('click', '.js-blocked-input', function () {
                var _blocked = $(this).is(':checked') ? 1 : 0;
                var _user = $(this).data('user');

                $.ajax({
                    data: {
                        action: 'blocked',
                        user_id: _user,
                        blocked: _blocked
                    },
                    type: 'POST'
                })
            });

            $(document).on('click', '.form-check-input', function () {
                $.ajax({
                    url: "ajax/BlacklistAddDelete.php",
                    data: {
                        phone_num: phone_num,
                        firstname: firstname,
                        lastname: lastname,
                        patronymic: patronymic
                    },
                    method: 'POST'
                });
            });

            $('.edit_personal_number').on('click', function (e) {
                e.preventDefault();


                $(this).hide();
                $('.show_personal_number').hide();
                $('.number_edit_form').show();

                $('.cancel_edit').on('click', function () {
                    $('.number_edit_form').hide();
                    $('.edit_personal_number').show();
                    $('.show_personal_number').show();
                });

                $('.accept_edit').on('click', function () {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let number = $('input[class="form-control number_edit_form number"]').val();

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'edit_personal_number',
                            user_id: user_id,
                            number: number
                        },
                        success: function (resp) {
                            if (resp == 'error') {
                                Swal.fire({
                                    title: 'Такой номер уже зарегистрирован',
                                    confirmButtonText: 'ОК'
                                });
                            }
                            else {
                                location.reload();
                            }
                        }
                    });
                });
            })
        })
    </script>
{/capture}


{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css" rel="stylesheet"/>
{/capture}


<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0"><i
                            class="mdi mdi-account-card-details"></i> {$client->lastname|escape} {$client->firstname|escape} {$client->patronymic|escape}
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="clients">Клиенты</a></li>
                    <li class="breadcrumb-item active">Карточка клиента</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-top">
                <div class="float-right">{$client->UID}</div>
            </div>
        </div>


        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">

                    <div class="card-body">

                        <div class="form-body">

                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="border p-2">
                                        <div class="row">
                                            <h3 class="form-control-static col-md-2">
                                                {if $client->loaded_from_1c}
                                                    <span class="label label-primary">1С</span>
                                                {/if}
                                                {if $client->have_crm_closed}
                                                    <span class="label label-primary"
                                                          title="Клиент уже имеет погашенные займы в CRM">ПК CRM</span>
                                                {elseif $client->loan_history|count > 0}
                                                    <span class="label label-success"
                                                          title="Клиент уже имеет погашенные займы в CRM">ПК</span>
                                                {elseif $client->orders|count == 1}
                                                    <span class="badge badge-success">Новый клиент</span>
                                                {elseif $client->orders|count > 1}
                                                    <span class="label label-warning">Повтор</span>
                                                {else}<span class="label label-info">Лид {$client->stages}/6</span>
                                                {/if}
                                            </h3>
                                            <div class="col-md-4">
                                                <h3>
                                                    {$client->lastname|escape}
                                                    {$client->firstname|escape}
                                                    {$client->patronymic|escape}
                                                </h3>
                                                <small>Дата регистрации:
                                                    {$client->created|date}</small>
                                                <br>
                                                <small>Номер
                                                    клиента:
                                                </small>
                                                <small class="show_personal_number">{$client->personal_number}</small>
                                                <a data-user="{$client->id}"
                                                   class="text-info edit_personal_number"><i
                                                            class=" fas fa-edit"></i></a>
                                                <input type="text" class="form-control number_edit_form number"
                                                       style="width: 80px; display: none"
                                                       value="{$client->personal_number}">
                                                <input type="button" style="display: none"
                                                       data-user="{$client->id}"
                                                       class="btn btn-success number_edit_form accept_edit"
                                                       value="Сохранить">
                                                <input type="button" style="display: none"
                                                       class="btn btn-danger number_edit_form cancel_edit"
                                                       value="Отмена">
                                            </div>
                                            <div class="col-md-2">
                                                <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                                                    <input type="checkbox" class="custom-control-input js-blocked-input"
                                                           id="blocked" value="1" data-user="{$client->id}"
                                                           {if $client->blocked}checked{/if}>
                                                    <label class="custom-control-label" for="blocked"><strong
                                                                class="text-danger">Заблокирован</strong></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        Находится в ч/с
                                                    </label>
                                                </div>
                                            </div>
                                            <h3 class="col-md-4 text-right">
                                                <span>{$client->phone_mobile|escape}</span>
                                                <button class="js-mango-call mango-call"
                                                        data-phone="{$client->phone_mobile|escape}"
                                                        title="Выполнить звонок">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </button>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <ul class="mt-2 nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#info" role="tab"
                                   aria-selected="false">
                                    <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                    <span class="hidden-xs-down">Персональная информация</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#comments" role="tab" aria-selected="false">
                                    <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                    <span class="hidden-xs-down">
                                            Комментарии {if $comments|count>0}<span
                                                class="label label-rounded label-primary">{$comments|count}</span>{/if}
                                        </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#documents" role="tab" aria-selected="true">
                                    <span class="hidden-sm-up"><i class="ti-email"></i></span>
                                    <span class="hidden-xs-down">Документы</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#logs" role="tab" aria-selected="true">
                                    <span class="hidden-sm-up"><i class="ti-email"></i></span>
                                    <span class="hidden-xs-down">Логирование</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#history" role="tab" aria-selected="true">
                                    <span class="hidden-sm-up"><i class="ti-email"></i></span>
                                    <span class="hidden-xs-down">Кредитная история</span>
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content tabcontent-border">
                            <div class="tab-pane active" id="info" role="tabpanel">
                                <div class="form-body p-2 pt-3">


                                    <div class="row">
                                        <div class="col-md-8 ">

                                            <!-- Контакты -->
                                            <form action="{url}" class="mb-3 border js-order-item-form"
                                                  id="personal_data_form">

                                                <input type="hidden" name="action" value="contactdata"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>

                                                <h5 class="card-header">
                                                    <span class="text-white ">Контакты</span>
                                                    <a href="javascript:void(0);"
                                                       class="float-right text-white js-edit-form"><i
                                                                class=" fas fa-edit"></i></a></h3>
                                                </h5>

                                                <div class="row pt-2 view-block {if $contactdata_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Email:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->email|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Дата рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->birth|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Место
                                                                рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->birth_place|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Паспорт:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->passport_serial}
                                                                    ,
                                                                    от {$client->passport_date|escape} {$client->subdivision_code|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Кем выдан:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->passport_issued|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Соцсети:</label>
                                                            <div class="col-md-8">
                                                                <ul class="list-unstyled form-control-static pl-0">
                                                                    {if $client->social}
                                                                        <li>
                                                                            <a target="_blank"
                                                                               href="{$client->social|escape}">{$client->social|escape}</a>
                                                                        </li>
                                                                    {/if}
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row p-2 edit-block {if !$contactdata_error}hide{/if}">
                                                    {if $contactdata_error}
                                                        <div class="col-md-12">
                                                            <ul class="alert alert-danger">
                                                                {if in_array('empty_email', (array)$contactdata_error)}
                                                                    <li>Укажите Email!</li>
                                                                {/if}
                                                                {if in_array('empty_birth', (array)$contactdata_error)}
                                                                    <li>Укажите Дату рождения!</li>
                                                                {/if}
                                                                {if in_array('empty_passport_serial', (array)$contactdata_error)}
                                                                    <li>Укажите серию и номер паспорта!</li>
                                                                {/if}
                                                                {if in_array('empty_passport_date', (array)$contactdata_error)}
                                                                    <li>Укажите дату выдачи паспорта!</li>
                                                                {/if}
                                                                {if in_array('empty_subdivision_code', (array)$contactdata_error)}
                                                                    <li>Укажите код подразделения выдавшего паспорт!
                                                                    </li>
                                                                {/if}
                                                                {if in_array('empty_passport_issued', (array)$contactdata_error)}
                                                                    <li>Укажите кем выдан паспорт!</li>
                                                                {/if}
                                                            </ul>
                                                        </div>
                                                    {/if}

                                                    <div class="col-md-6">
                                                        <div class="form-group mb-1 {if in_array('empty_email', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Email</label>
                                                            <input type="text" name="email"
                                                                   value="{$client->email|escape}" class="form-control"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_email', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите Email!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-1 {if in_array('empty_birth', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Дата рождения</label>
                                                            <input type="text" name="birth"
                                                                   value="{$client->birth|escape}" class="form-control"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_birth', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите дату
                                                                    рождения!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-2">
                                                            <label class="control-label">Соцсети</label>
                                                            <input type="text" class="form-control" name="social"
                                                                   value="{$client->social|escape}" placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-2 {if in_array('empty_birth_place', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Место рождения</label>
                                                            <input type="text" name="birth_place"
                                                                   value="{$client->birth_place|escape}"
                                                                   class="form-control" placeholder="" required="true"/>
                                                            {if in_array('empty_birth_place', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите место
                                                                    рождения!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4">
                                                        <div class="form-group mb-1 {if in_array('empty_passport_serial', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Серия и номер паспорта</label>
                                                            <input type="text" class="form-control"
                                                                   name="passport_serial"
                                                                   value="{$client->passport_serial|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_passport_serial', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите серию и
                                                                    номер паспорта!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-1 {if in_array('empty_passport_date', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Дата выдачи</label>
                                                            <input type="text" class="form-control" name="passport_date"
                                                                   value="{$client->passport_date|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_passport_date', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите дату выдачи
                                                                    паспорта!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-1 {if in_array('empty_subdivision_code', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Код подразделения</label>
                                                            <input type="text" class="form-control"
                                                                   name="subdivision_code"
                                                                   value="{$client->subdivision_code|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_subdivision_code', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите код
                                                                    подразделения выдавшего паспорт!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group {if in_array('empty_passport_issued', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Кем выдан</label>
                                                            <input type="text" class="form-control"
                                                                   name="passport_issued"
                                                                   value="{$client->passport_issued|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_passport_issued', (array)$contactdata_errors)}
                                                                <small class="form-control-feedback">Укажите кем выдан
                                                                    паспорт!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- / Контакты-->

                                            <!-- /Контактные лица -->
                                            <form action="{url}" class="js-order-item-form mb-3 border"
                                                  id="contact_persons_form">

                                                <input type="hidden" name="action" value="contacts"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>

                                                <h5 class="card-header">
                                                    <span class="text-white">Контактные лица</span>
                                                    <a href="javascript:void(0);"
                                                       class="text-white float-right js-edit-form"><i
                                                                class=" fas fa-edit"></i></a></h3>
                                                </h5>

                                                <div class="row view-block m-0 {if $contacts_error}hide{/if}">
                                                    <table class="table table-hover mb-0">
                                                        <tr>
                                                            <td>{$client->contact_person_name|escape}</td>
                                                            <td>{$client->contact_person_relation|escape}</td>
                                                            <td class="text-right">{$client->contact_person_phone|escape}</td>
                                                            <td>
                                                                <button class="js-mango-call mango-call"
                                                                        data-phone="{$client->contact_person_phone|escape}"
                                                                        title="Выполнить звонок">
                                                                    <i class="fas fa-mobile-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{$client->contact_person2_name|escape}</td>
                                                            <td>{$client->contact_person2_relation|escape}</td>
                                                            <td class="text-right">{$client->contact_person2_phone|escape}</td>
                                                            <td>
                                                                <button class="js-mango-call mango-call"
                                                                        data-phone="{$client->contact_person2_phone|escape}"
                                                                        title="Выполнить звонок">
                                                                    <i class="fas fa-mobile-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <div class="row m-0 pt-2 pb-2 edit-block {if !$contacts_error}hide{/if}">
                                                    {if $contacts_error}
                                                        <div class="col-md-12">
                                                            <ul class="alert alert-danger">
                                                                {if in_array('empty_contact_person_name', (array)$contacts_error)}
                                                                    <li>Укажите ФИО контакного лица!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person_phone', (array)$contacts_error)}
                                                                    <li>Укажите тел. контакного лица!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person_relation', (array)$contacts_error)}
                                                                    <li>Укажите кем приходится контакное лицо!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person2_name', (array)$contacts_error)}
                                                                    <li>Укажите ФИО контакного лица 2!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person2_phone', (array)$contacts_error)}
                                                                    <li>Укажите тел. контакного лица 2!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person2_relation', (array)$contacts_error)}
                                                                    <li>Укажите кем приходится контакное лицо 2!</li>
                                                                {/if}
                                                            </ul>
                                                        </div>
                                                    {/if}
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person_name', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">ФИО контакного лица</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person_name"
                                                                   value="{$client->contact_person_name|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_contact_person_name', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите ФИО
                                                                    контакного лица!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person_relation', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">Кем приходится</label>
                                                            <select class="form-control custom-select"
                                                                    name="contact_person_relation">
                                                                <option value=""
                                                                        {if $client->contact_person_relation == ''}selected=""{/if}>
                                                                    Выберите значение
                                                                </option>
                                                                <option value="мать/отец"
                                                                        {if $client->contact_person_relation == 'мать/отец'}selected=""{/if}>
                                                                    мать/отец
                                                                </option>
                                                                <option value="муж/жена"
                                                                        {if $client->contact_person_relation == 'муж/жена'}selected=""{/if}>
                                                                    муж/жена
                                                                </option>
                                                                <option value="сын/дочь"
                                                                        {if $client->contact_person_relation == 'сын/дочь'}selected=""{/if}>
                                                                    сын/дочь
                                                                </option>
                                                                <option value="коллега"
                                                                        {if $client->contact_person_relation == 'коллега'}selected=""{/if}>
                                                                    коллега
                                                                </option>
                                                                <option value="друг/сосед"
                                                                        {if $client->contact_person_relation == 'друг/сосед'}selected=""{/if}>
                                                                    друг/сосед
                                                                </option>
                                                                <option value="иной родственник"
                                                                        {if $client->contact_person_relation == 'иной родственник'}selected=""{/if}>
                                                                    иной родственник
                                                                </option>
                                                            </select>
                                                            {if in_array('empty_contact_person_relation', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите кем
                                                                    приходится контакное лицо!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person_phone', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">Тел. контакного лица</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person_phone"
                                                                   value="{$client->contact_person_phone|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_contact_person_phone', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите тел.
                                                                    контакного лица!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person2_name', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">ФИО контакного лица 2</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person2_name"
                                                                   value="{$client->contact_person2_name|escape}"
                                                                   placeholder="" required="true"/>
                                                            {if in_array('empty_contact_person2_name', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите ФИО
                                                                    контакного лица!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person2_relation', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">Кем приходится</label>
                                                            <select class="form-control custom-select"
                                                                    name="contact_person2_relation">
                                                                <option value=""
                                                                        {if $client->contact_person2_relation == ''}selected=""{/if}>
                                                                    Выберите значение
                                                                </option>
                                                                <option value="мать/отец"
                                                                        {if $client->contact_person2_relation == 'мать/отец'}selected=""{/if}>
                                                                    мать/отец
                                                                </option>
                                                                <option value="муж/жена"
                                                                        {if $client->contact_person2_relation == 'муж/жена'}selected=""{/if}>
                                                                    муж/жена
                                                                </option>
                                                                <option value="сын/дочь"
                                                                        {if $client->contact_person2_relation == 'сын/дочь'}selected=""{/if}>
                                                                    сын/дочь
                                                                </option>
                                                                <option value="коллега"
                                                                        {if $client->contact_person2_relation == 'коллега'}selected=""{/if}>
                                                                    коллега
                                                                </option>
                                                                <option value="друг/сосед"
                                                                        {if $client->contact_person2_relation == 'друг/сосед'}selected=""{/if}>
                                                                    друг/сосед
                                                                </option>
                                                                <option value="иной родственник"
                                                                        {if $client->contact_person2_relation == 'иной родственник'}selected=""{/if}>
                                                                    иной родственник
                                                                </option>
                                                            </select>
                                                            {if in_array('empty_contact_person2_relation', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите кем
                                                                    приходится контакное лицо!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person2_phone', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">Тел. контакного лица 2</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person2_phone"
                                                                   value="{$client->contact_person2_phone|escape}"
                                                                   placeholder=""/>
                                                            {if in_array('empty_contact_person2_phone', (array)$contacts_error)}
                                                                <small class="form-control-feedback">Укажите тел.
                                                                    контакного лица!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <form action="{url}" class="js-order-item-form mb-3 border"
                                                  id="address_form">

                                                <input type="hidden" name="action" value="addresses"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>

                                                <h5 class="card-header">
                                                    <span class="text-white">Адрес</span>
                                                    <a href="javascript:void(0);"
                                                       class="text-white float-right js-edit-form"><i
                                                                class=" fas fa-edit"></i></a></h3>
                                                </h5>

                                                <div class="row view-block {if $addresses_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover mb-0">
                                                            <tr>
                                                                <td>Адрес прописки</td>
                                                                <td>
                                                                    {if $client->Regindex}{$client->Regindex|escape}, {/if}
                                                                    {$client->Regregion} {$client->Regregion_shorttype|escape}
                                                                    ,
                                                                    {if $client->Regcity}{$client->Regcity|escape} {$client->Regcity_shorttype|escape},{/if}
                                                                    {if $client->Regdistrict}{$client->Regdistrict|escape} {$client->Regdistrict_shorttype|escape},{/if}
                                                                    {if $client->Reglocality}{$client->Reglocality|escape} {$client->Reglocality_shorttype|escape},{/if}
                                                                    {if $client->Regstreet}{$client->Regstreet|escape} {$client->Regstreet_shorttype|escape},{/if}
                                                                    д.{$client->Reghousing|escape},
                                                                    {if $client->Regbuilding}стр. {$client->Regbuilding|escape},{/if}
                                                                    {if $client->Regroom}кв.{$client->Regroom|escape}{/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Адрес проживания</td>
                                                                <td>
                                                                    {if $client->Faktindex}{$client->Faktindex|escape}, {/if}
                                                                    {$client->Faktregion} {$client->Faktregion_shorttype|escape}
                                                                    ,
                                                                    {if $client->Faktcity}{$client->Faktcity|escape} {$client->Faktcity_shorttype|escape},{/if}
                                                                    {if $client->Faktdistrict}{$client->Faktdistrict|escape} {$client->Faktdistrict_shorttype|escape},{/if}
                                                                    {if $client->Faktlocality}{$client->Faktlocality|escape} {$client->Faktlocality_shorttype|escape},{/if}
                                                                    {if $client->Faktstreet}{$client->Faktstreet|escape} {$client->Faktstreet_shorttype|escape},{/if}
                                                                    д.{$client->Fakthousing|escape},
                                                                    {if $client->Faktbuilding}стр. {$client->Faktbuilding|escape},{/if}
                                                                    {if $client->Faktroom}кв.{$client->Faktroom|escape}{/if}

                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="edit-block m-0 {if !$addresses_error}hide{/if}">

                                                    <div class="row m-0 mb-2 mt-2 js-dadata-address">
                                                        <h6 class="col-12 nav-small-cap">Адрес прописки</h6>
                                                        {if $addresses_error}
                                                            <div class="col-md-12">
                                                                <ul class="alert alert-danger">
                                                                    {if in_array('empty_regregion', (array)$addresses_error)}
                                                                        <li>Укажите область!</li>
                                                                    {/if}
                                                                    {if in_array('empty_regcity', (array)$addresses_error)}
                                                                        <li>Укажите город!</li>
                                                                    {/if}
                                                                    {if in_array('empty_regstreet', (array)$addresses_error)}
                                                                        <li>Укажите улицу!</li>
                                                                    {/if}
                                                                    {if in_array('empty_reghousing', (array)$addresses_error)}
                                                                        <li>Укажите дом!</li>
                                                                    {/if}
                                                                    {if in_array('empty_faktregion', (array)$addresses_error)}
                                                                        <li>Укажите область!</li>
                                                                    {/if}
                                                                    {if in_array('empty_faktcity', (array)$addresses_error)}
                                                                        <li>Укажите город!</li>
                                                                    {/if}
                                                                    {if in_array('empty_faktstreet', (array)$addresses_error)}
                                                                        <li>Укажите улицу!</li>
                                                                    {/if}
                                                                    {if in_array('empty_fakthousing', (array)$addresses_error)}
                                                                        <li>Укажите дом!</li>
                                                                    {/if}
                                                                </ul>
                                                            </div>
                                                        {/if}
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_regregion', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Область</label>
                                                                <input type="text" class="form-control js-dadata-region"
                                                                       name="Regregion"
                                                                       value="{$client->Regregion|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_regregion', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        область!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_regcity', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Город</label>
                                                                <input type="text" class="form-control js-dadata-city"
                                                                       name="Regcity" value="{$client->Regcity|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_regcity', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        город!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Район</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-district"
                                                                       name="Regdistrict"
                                                                       value="{$client->Regdistrict|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Нас. пункт</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-locality"
                                                                       name="Reglocality"
                                                                       value="{$client->Reglocality|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 {if in_array('empty_regstreet', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Улица</label>
                                                                <input type="text" class="form-control js-dadata-street"
                                                                       name="Regstreet"
                                                                       value="{$client->Regstreet|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_regstreet', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        улицу!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group {if in_array('empty_reghousing', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Дом</label>
                                                                <input type="text" class="form-control js-dadata-house"
                                                                       name="Reghousing"
                                                                       value="{$client->Reghousing|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_reghousing', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите дом!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Строение</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-building"
                                                                       name="Regbuilding"
                                                                       value="{$client->Regbuilding|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Квартира</label>
                                                                <input type="text" class="form-control js-dadata-room"
                                                                       name="Regroom" value="{$client->Regroom|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Индекс</label>
                                                                <input type="text" class="form-control js-dadata-index"
                                                                       name="Regindex"
                                                                       value="{$client->Regindex|escape}" placeholder=""
                                                                       required="true"/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row m-0 js-dadata-address">
                                                        <h6 class="col-12 nav-small-cap">Адрес проживания</h6>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_faktregion', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Область</label>
                                                                <input type="text" class="form-control js-dadata-region"
                                                                       name="Faktregion"
                                                                       value="{$client->Faktregion|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_faktregion', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        область!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_faktcity', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Город</label>
                                                                <input type="text" class="form-control js-dadata-city"
                                                                       name="Faktcity"
                                                                       value="{$client->Faktcity|escape}" placeholder=""
                                                                       required="true"/>
                                                                {if in_array('empty_faktcity', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        город!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1">
                                                                <label class="control-label">Район</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-district"
                                                                       name="Faktdistrict"
                                                                       value="{$client->Faktdistrict|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1">
                                                                <label class="control-label">Нас. пункт</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-locality"
                                                                       name="Faktlocality"
                                                                       value="{$client->Faktlocality|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 {if in_array('empty_faktstreet', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Улица</label>
                                                                <input type="text" class="form-control js-dadata-street"
                                                                       name="Faktstreet"
                                                                       value="{$client->Faktstreet|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_faktstreet', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        улицу!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group {if in_array('empty_fakthousing', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Дом</label>
                                                                <input type="text" class="form-control js-dadata-house"
                                                                       name="Fakthousing"
                                                                       value="{$client->Fakthousing|escape}"
                                                                       placeholder="" required="true"/>
                                                                {if in_array('empty_fakthousing', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите дом!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Строение</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-building"
                                                                       name="Faktbuilding"
                                                                       value="{$client->Faktbuilding|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Квартира</label>
                                                                <input type="text" class="form-control js-dadata-room"
                                                                       name="Faktroom"
                                                                       value="{$client->Faktroom|escape}" placeholder=""
                                                                       required="true"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Индекс</label>
                                                                <input type="text" class="form-control js-dadata-index"
                                                                       name="Faktindex"
                                                                       value="{$client->Faktindex|escape}"
                                                                       placeholder="" required="true"/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row m-0 mt-2 mb-2">
                                                        <div class="col-md-12">
                                                            <div class="form-actions">
                                                                <button type="submit" class="btn btn-success"><i
                                                                            class="fa fa-check"></i> Сохранить
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-inverse js-cancel-edit">Отмена
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>


                                            <!-- Данные о работе -->
                                            <form action="{url}"
                                                  class="border js-order-item-form mb-3">

                                                <input type="hidden" name="action" value="work"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$client->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Информация о работодателе</span>
                                                    <span class="float-right">
                                                    <a href="javascript:void(0);"
                                                       class="text-white"
                                                       data-user="{$client->id}"><i
                                                                class="fas fa-eraser"></i></a>
                                                        </span>
                                                </h6>

                                                <div class="row m-0 pt-2 view-block">
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-0 row employer_show">
                                                            <label class="control-label col-md-3">Группа:</label>
                                                            <div class="col-md-6">
                                                                <p>{$group_name}</p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group  mb-0 row employer_show">
                                                            <label class="control-label col-md-3">Компания:</label>
                                                            <div class="col-md-6">
                                                                <p>{$company_name}</p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group  mb-0 row employer_show">
                                                            <label class="control-label col-md-3">Филиал:</label>
                                                            <div class="col-md-6">
                                                                <p>{$branch_name}</p>
                                                            </div>
                                                        </div>
                                                        <div id="employer_edit" style="display: none">
                                                            <div class="form-group  mb-0 row">
                                                                <label class="control-label col-md-3">Группа:</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="group_select"
                                                                            name="group"
                                                                            {if $manager->role =='employer'}disabled{/if}>
                                                                        <option value="none" selected>Отсутствует
                                                                            группа
                                                                        </option>
                                                                        {foreach $groups as $group}
                                                                            <option value="{$group->id}"
                                                                                    {if $client->group_id == $group->id}selected{/if}>{$group->name}</option>
                                                                        {/foreach}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="form-group  mb-0 row">
                                                                <label class="control-label col-md-3">Компания:</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="company_select"
                                                                            name="company"
                                                                            {if $manager->role =='employer'}disabled{/if}>
                                                                        <option value="none" selected>Отсутствует
                                                                            компания
                                                                        </option>
                                                                        {foreach $companies as $company}
                                                                            <option value="{$company->id}"
                                                                                    {if $client->company_id != null && $client->company_id == $company->id}selected{/if}>{$company->name}</option>
                                                                        {/foreach}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="form-group  mb-0 row">
                                                                <label class="control-label col-md-3">Филиал:</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="branch_select"
                                                                            name="branch">
                                                                        <option value="none" selected>Отсутствует
                                                                            филиал
                                                                        </option>
                                                                        {foreach $branches as $branch}
                                                                            <option value="{$branch->id}"
                                                                                    {if $client->branche_id != null && $client->branche_id == $branch->id}selected{/if}>{$branch->name}</option>
                                                                        {/foreach}
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div style="display: flex; justify-content: space-between">
                                                                <div type="button"
                                                                     data-order="{$order->order_id}"
                                                                     class="btn btn-success accept_employer">
                                                                    Сохранить
                                                                </div>
                                                                <div type="button" class="btn btn-dark cancel_employer">
                                                                    Отменить
                                                                </div>
                                                            </div>
                                                            <br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>


                                            <!-- /Данные о работе -->


                                            <!--
                                            <h3 class="box-title mt-5">UTM-метки</h3>
                                            <hr>
                                            -->
                                        </div>
                                        <div class="col-md-4 ">
                                            {*}
                                            <div class="mb-3 border">
                                                <h5 class=" card-header">
                                                    <span class="text-white ">Скоринги</span>
                                                    <a class="text-white float-right js-run-scorings" data-type="all" data-order="{$client->order_id}" href="javascript:void(0);">
                                                        <i class="far fa-play-circle"></i>
                                                    </a>
                                                </h2>
                                                <div class="message-box">

                                                    {foreach $scoring_types as $scoring_type}
                                                    <div class="pl-2 pr-2 {if is_null($scorings[$scoring_type->name]->success)}bg-light-warning{elseif $scorings[$scoring_type->name]->success}bg-light-success{else}bg-light-danger{/if}">
                                                        <div class="row {if !$scoring_type@last}border-bottom{/if}">
                                                            <div class="col-12 col-sm-12 pt-2">
                                                                <h5 class="float-left">{$scoring_type->title}</h5>
                                                                {if is_null($scorings[$scoring_type->name]->success)}
                                                                    <span class="label label-warning float-right">Нет результата</span>
                                                                {elseif $scorings[$scoring_type->name]->success}
                                                                    <span class="label label-success label-sm float-right">Пройден</span>
                                                                {else}
                                                                    <span class="label label-danger float-right">Не пройден</span>
                                                                {/if}
                                                            </div>
                                                            <div class="col-8 col-sm-8 pb-2">
                                                                <span class="mail-desc" title="{$scorings[$scoring_type->name]->string_result}">{$scorings[$scoring_type->name]->string_result}</span>
                                                                <span class="time">
                                                                    {$scorings[$scoring_type->name]->created|date} {$scoring->created|time}
                                                                </span>
                                                            </div>
                                                            <div class="col-4 col-sm-4 pb-2">
                                                                {if is_null($scorings[$scoring_type->name]->success)}
                                                                    <a class="load-btn text-info js-run-scorings run-scoring-btn float-right" data-type="{$scoring_type->name}" data-order="{$client->order_id}" href="javascript:void(0);">
                                                                        <i class="far fa-play-circle"></i>
                                                                    </a>
                                                                {else}
                                                                    <a class="text-info load-btn js-run-scorings run-scoring-btn float-right" data-type="{$scoring_type->name}" data-order="{$client->order_id}" href="javascript:void(0);">
                                                                        <i class="fas fa-undo"></i>
                                                                    </a>
                                                                {/if}

                                                            </div>
                                                        </div>
                                                    </div>
                                                    {/foreach}
                                                </div>
                                            </div>
                                            {*}

                                            <form action="{url}" class="mb-3 border js-order-item-form"
                                                  id="services_form">

                                                <input type="hidden" name="action" value="services"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>


                                                <h5 class="card-header text-white">
                                                    <span>Услуги</span>
                                                    <a href="javascript:void(0);"
                                                       class="js-edit-form float-right text-white"><i
                                                                class=" fas fa-edit"></i></a>
                                                </h5>

                                                <div class="row view-block p-2 {if $services_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        {*}
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7">Смс информирование:</label>
                                                            <div class="col-md-4 col-5">
                                                                <p class="form-control-static text-right">
                                                                    {if $client->service_sms}
                                                                        <span class="label label-success">Вкл</span>
                                                                    {else}
                                                                        <span class="label label-danger">Выкл</span>
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        {*}
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7">Причина
                                                                отказа:</label>
                                                            <div class="col-md-4 col-5">
                                                                <p class="form-control-static text-right">
                                                                    {if $client->service_reason}
                                                                        <span class="label label-success">Вкл</span>
                                                                    {else}
                                                                        <span class="label label-danger">Выкл</span>
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7">Страхование:</label>
                                                            <div class="col-md-4 col-5">
                                                                <p class="form-control-static text-right">
                                                                    {if $client->service_insurance}
                                                                        <span class="label label-success">Вкл</span>
                                                                    {else}
                                                                        <span class="label label-danger">Выкл</span>
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row p-2 edit-block {if !$services_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        {*}
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" name="service_sms" id="service_sms" value="1" {if $client->service_sms}checked="true"{/if} />
                                                                <label class="custom-control-label" for="service_sms">Смс информирование</label>
                                                            </div>
                                                        </div>
                                                        {*}
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       name="service_reason" id="service_reason"
                                                                       value="1"
                                                                       {if $client->service_reason}checked="true"{/if} />
                                                                <label class="custom-control-label"
                                                                       for="service_reason">Причина отказа</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                       name="service_insurance" id="service_insurance"
                                                                       value="1"
                                                                       {if $client->service_insurance}checked="true"{/if} />
                                                                <label class="custom-control-label"
                                                                       for="service_insurance">Страхование</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <form action="{url}" class="mb-3 border js-order-item-form" id="cards_form">

                                                <input type="hidden" name="action" value="cards"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>

                                                <h5 class="card-header text-white">
                                                    <span>Карты</span>
                                                </h5>

                                                <div class="row view-block p-2 {if $services_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <table class="table table-stripped">
                                                            {foreach $cards as $card}
                                                                <tr class="{if $card->deleted}bg-light-danger{/if}">
                                                                    <td>
                                                                        <div>
                                                                            <strong>{$card->pan}</strong>
                                                                        </div>

                                                                    </td>
                                                                    <td>
                                                                        {if $card->deleted}
                                                                            <span class="label label-danger">Удалена</span>
                                                                        {/if}
                                                                        {if $card->base_card}
                                                                            <span class="label label-primary">Основная</span>
                                                                        {/if}
                                                                    </td>
                                                                    <td>
                                                                        <div>{$card->expdate}</div>
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                        </table>
                                                    </div>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                    <!-- -->
                                    <form action="{url}" class="border js-order-item-form mb-3" id="images_form">

                                        <input type="hidden" name="action" value="images"/>
                                        <input type="hidden" name="user_id" value="{$client->id}"/>

                                        <h5 class="card-header">
                                            <span class="text-white">Фотографии</span>

                                        </h5>

                                        <div class="row p-2 view-block {if $socials_error}hide{/if}">
                                            <ul class="col-md-12 list-inline">
                                                {foreach $files as $file}
                                                    {if $file->status == 0}
                                                        {$item_class="border-warning"}
                                                        {$ribbon_class="ribbon-warning"}
                                                        {$ribbon_icon="fas fa-question"}
                                                    {elseif $file->status == 1}
                                                        {$item_class="border-primary"}
                                                        {$ribbon_class="ribbon-primary"}
                                                        {$ribbon_icon="fas fa-clock"}
                                                    {elseif $file->status == 2}
                                                        {$item_class="border-success border border-bg"}
                                                        {$ribbon_class="ribbon-success"}
                                                        {$ribbon_icon="fa fa-check-circle"}
                                                    {elseif $file->status == 3}
                                                        {$item_class="border-danger border"}
                                                        {$ribbon_class="ribbon-danger"}
                                                        {$ribbon_icon="fas fa-times-circle"}
                                                    {/if}
                                                    <li class="order-image-item ribbon-wrapper rounded-sm border {$item_class}">
                                                        <a class="image-popup-fit-width" href="javascript:void(0)"
                                                           onclick="window.open('{$config->back_url}/files/users/{$file->name}')">
                                                            <div class="ribbon ribbon-corner {$ribbon_class}"><i
                                                                        class="{$ribbon_icon}"></i></div>
                                                            <img src="{$config->back_url}/files/users/{$file->name}"
                                                                 alt="" class="img-responsive" style=""/>
                                                        </a>
                                                        <div class="order-image-actions">
                                                            <div class="dropdown mr-1 show ">
                                                                <button type="button"
                                                                        class="btn {if $file->status==2}btn-success{elseif $file->status==3}btn-danger{else}btn-secondary{/if} dropdown-toggle"
                                                                        id="dropdownMenuOffset" data-toggle="dropdown"
                                                                        aria-haspopup="true" aria-expanded="true">
                                                                    {if $file->status == 2}Принят
                                                                    {elseif $file->status == 3}Отклонен
                                                                    {else}Статус
                                                                    {/if}
                                                                </button>
                                                                <div class="dropdown-menu"
                                                                     aria-labelledby="dropdownMenuOffset"
                                                                     x-placement="bottom-start">
                                                                    <div class="p-1 dropdown-item">
                                                                        <button class="btn btn-sm btn-block btn-success js-image-accept"
                                                                                data-id="{$file->id}" type="button">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            <span>Принят</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="p-1 dropdown-item">
                                                                        <button class="btn btn-sm btn-block btn-danger js-image-reject"
                                                                                data-id="{$file->id}" type="button">
                                                                            <i class="fas fa-times-circle"></i>
                                                                            <span>Отклонен</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>

                                        <div class="row edit-block {if !$images_error}hide{/if}">
                                            {foreach $files as $file}
                                                <div class="col-md-4 col-lg-3 col-xlg-3">
                                                    <div class="card card-body">
                                                        <div class="row">

                                                            <div class="col-md-6 col-lg-8">
                                                                <div class="form-group">
                                                                    <label class="control-label">Статус</label>
                                                                    <input type="text" id="status_{$file->id}"
                                                                           name="status[{$file->id}]"
                                                                           value="{$file->status}"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/foreach}
                                            <div class="col-md-12">
                                                <div class="form-actions">
                                                    <button type="submit" class="btn btn-success"><i
                                                                class="fa fa-check"></i> Сохранить
                                                    </button>
                                                    <button type="button" class="btn btn-inverse js-cancel-edit">
                                                        Отмена
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- -->


                                </div>
                            </div>

                            <div class="tab-pane p-3" id="comments" role="tabpanel">

                                <div class="row">
                                    <div class="col-12">

                                    </div>
                                    <hr class="m-3"/>
                                    <div class="col-12">
                                        {if $comments}
                                            <h4>Комментарии к заявкам</h4>
                                            <div class="message-box">
                                                <div class="message-widget">
                                                    {foreach $comments as $comment}
                                                        <a href="order/{$comment->order_id}">
                                                            <div class="user-img">
                                                                <span class="round">{$comment->letter|escape}</span>
                                                            </div>
                                                            <div class="mail-contnet">
                                                                <h5>{$managers[$comment->manager_id]->name|escape}</h5>
                                                                <span class="mail-desc">
                                                                {$comment->text|nl2br}
                                                            </span>
                                                                <span class="time">
                                                                {$comment->created|date} {$comment->created|time}
                                                                    <i>Комментарий оставлен к заявке №{$comment->order_id}</i>
                                                            </span>
                                                            </div>

                                                        </a>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        {/if}
                                        {if $comments_1c}
                                            <h3>Комментарии из 1С</h3>
                                            <table class="table">
                                                <tr>
                                                    <th>Дата</th>
                                                    <th>Блок</th>
                                                    <th>Комментарий</th>
                                                </tr>
                                                {foreach $comments_1c as $comment}
                                                    <tr>
                                                        <td>{$comment->created|date} {$comment->created|time}</td>
                                                        <td>{$comment->block|escape}</td>
                                                        <td>{$comment->text|nl2br}</td>
                                                    </tr>
                                                {/foreach}
                                            </table>
                                        {/if}

                                        {if !$comments && !$comments_1c}
                                            <h4>Нет комментариев</h4>
                                        {/if}
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane p-3" id="documents" role="tabpanel">
                                {if $documents}
                                    <table class="table">
                                        {foreach $documents as $document}
                                            <tr>
                                                <td class="text-info">
                                                    <a target="_blank"
                                                       href="http://51.250.26.168/document/{$document->id}">
                                                        <i class="fas fa-file-pdf fa-lg"></i>&nbsp;
                                                        {$document->name|escape}
                                                    </a>
                                                </td>
                                                <td class="text-right">
                                                    {$document->created|date}
                                                    {$document->created|time}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                {else}
                                    <h4>Нет доступных документов</h4>
                                {/if}
                            </div>

                            <div class="tab-pane p-3" id="logs" role="tabpanel">
                                {if $changelogs}
                                    <table class="table table-hover ">
                                        <tbody>
                                        {foreach $changelogs as $changelog}
                                            <tr class="">
                                                <td>
                                                    <div class="button-toggle-wrapper">
                                                        <button class="js-open-order button-toggle"
                                                                data-id="{$changelog->id}" type="button"
                                                                title="Подробнее"></button>
                                                    </div>
                                                    <span>{$changelog->created|date}</span>
                                                    {$changelog->created|time}
                                                </td>
                                                <td>
                                                    {if $changelog_types[$changelog->type]}{$changelog_types[$changelog->type]}
                                                    {else}{$changelog->type|escape}{/if}
                                                </td>
                                                <td>
                                                    <a href="manager/{$changelog->manager->id}">{$changelog->manager->name|escape}</a>
                                                </td>
                                                <td>
                                                    <a href="client/{$changelog->user->id}">
                                                        {$changelog->user->lastname|escape}
                                                        {$changelog->user->firstname|escape}
                                                        {$changelog->user->patronymic|escape}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="order-details" id="changelog_{$changelog->id}"
                                                style="display:none">
                                                <td colspan="4">
                                                    <div class="row">
                                                        <ul class="dtr-details col-md-6 list-unstyled">
                                                            {foreach $changelog->old_values as $field => $old_value}
                                                                <li>
                                                                    <strong>{$field}: </strong>
                                                                    <span>{$old_value}</span>
                                                                </li>
                                                            {/foreach}
                                                        </ul>
                                                        <ul class="col-md-6 dtr-details list-unstyled">
                                                            {foreach $changelog->new_values as $field => $new_value}
                                                                <li>
                                                                    <strong>{$field}: </strong>
                                                                    <span>{$new_value}</span>
                                                                </li>
                                                            {/foreach}
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                {/if}
                            </div>

                            <div id="history" class="tab-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        {*}
                                        <ul class="nav nav-pills mt-4 mb-4">
                                            <li class=" nav-item"> <a href="#navpills-orders" class="nav-link active" data-toggle="tab" aria-expanded="false">Заявки</a> </li>
                                            <li class="nav-item"> <a href="#navpills-loans" class="nav-link" data-toggle="tab" aria-expanded="false">Кредиты</a> </li>
                                        </ul>
                                        {*}
                                        <div class="tab-content br-n pn">
                                            <div id="navpills-orders" class="tab-pane active">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3>Заявки</h3>
                                                        <table class="table">
                                                            <tr>
                                                                <th>Дата</th>
                                                                <th>Заявка</th>
                                                                <th>Договор</th>
                                                                <th class="text-center">Сумма</th>
                                                                <th class="text-center">Период</th>
                                                                <th class="text-right">Статус</th>
                                                            </tr>
                                                            {foreach $client->orders as $order}
                                                                {if $order->contract->type != 'onec'}
                                                                    <tr>
                                                                        <td>{$order->date|date} {$order->date|time}</td>
                                                                        <td>
                                                                            <a href="order/{$order->order_id}"
                                                                               target="_blank">{$order->order_id}</a>
                                                                        </td>
                                                                        <td>{$order->contract->number}</td>
                                                                        <td class="text-center">{$order->amount}</td>
                                                                        <td class="text-center">{$order->period}</td>
                                                                        <td class="text-right">
                                                                            {$order_statuses[$order->status]}
                                                                            {if $order->contract->status==3}
                                                                                <br/>
                                                                                <small>{$order->contract->close_date|date} {$order->contract->close_date|time}</small>{/if}
                                                                        </td>
                                                                    </tr>
                                                                {/if}
                                                            {/foreach}
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="navpills-loans" class="tab-pane active">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h3>Кредитная история 1C</h3>
                                                        {if $client->loan_history|count > 0}
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Дата</th>
                                                                    <th>Договор</th>
                                                                    <th class="text-right">Статус</th>
                                                                    <th class="text-center">Сумма</th>
                                                                    <th class="text-center">Остаток ОД</th>
                                                                    <th class="text-right">Остаток процентов</th>
                                                                    <th>&nbsp;</th>
                                                                </tr>
                                                                {foreach $client->loan_history as $loan_history_item}
                                                                    <tr>
                                                                        <td>
                                                                            {$loan_history_item->date|date}
                                                                        </td>
                                                                        <td>
                                                                            {$loan_history_item->number}
                                                                        </td>
                                                                        <td class="text-right">
                                                                            {if $loan_history_item->loan_percents_summ > 0 || $loan_history_item->loan_body_summ > 0}
                                                                                <span class="label label-success">Активный</span>
                                                                            {else}
                                                                                <span class="label label-danger">Закрыт</span>
                                                                            {/if}
                                                                        </td>
                                                                        <td class="text-center">{$loan_history_item->amount}</td>
                                                                        <td class="text-center">{$loan_history_item->loan_body_summ}</td>
                                                                        <td class="text-right">{$loan_history_item->loan_percents_summ}</td>
                                                                        <td>
                                                                            <button type="button"
                                                                                    class="btn btn-xs btn-info js-get-movements"
                                                                                    data-number="{$loan_history_item->number}">
                                                                                Операции
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                {/foreach}
                                                            </table>
                                                        {else}
                                                            <h4>Нет кредитов</h4>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            {*}
                            <div class="tab-pane p-3" id="history" role="tabpanel">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Срок</th>
                                            <th>Менеджер</th>
                                            <th>Статус</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        {foreach $client->orders as $order}
                                        <tr>
                                            <td><a href="order/{$order->order_id}">{$order->order_id}</a></td>
                                            <td>{$order->date|date} {$order->date|time}</td>
                                            <td>{$order->amount} руб</td>
                                            <td>{$order->period} {$order->period|plural:'день':'дней':'дня'}</td>
                                            <td>{$managers[$order->manager_id]->name}</td>
                                            <td>
                                                {if $order->status == 0}Новый
                                                {elseif $order->status == 1}Принят
                                                {elseif $order->status == 2}Одобрен
                                                {elseif $order->status == 3}Отказ
                                                {elseif $order->status == 4}
                                                {/if}
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                            {*}
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->

    {include file='footer.tpl'}

</div>
<div class="modal fade" id="loan_operations" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loan_operations_title">Операции по договору</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>