{if isset($user->id)}
    {$meta_title="Профиль пользователя `$user->name`" scope=parent}
{else}
    {$meta_title="Новый пользователь" scope=parent}
{/if}

{capture name='page_scripts'}
    <script>

        $(function () {

            $('.edit_phone').click(function (e) {
                e.preventDefault();

                $(this).hide();
                $('.show_phone').hide();
                $('.show_phone_code').hide();
                $('.phone_edit_form').show();

                $('.cancel_edit').click(function (e) {
                    e.preventDefault();

                    $('.phone_edit_form').hide();
                    $('.edit_phone').show();
                    $('.show_phone').show();
                });

                $('.accept_edit').click(function (e) {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let phone = $('input[class="form-control phone"]').val();

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'edit_phone',
                            user_id: user_id,
                            phone: phone,
                        },
                        success: function (resp) {
                            if (resp == 'error') {
                                Swal.fire({
                                    title: 'Такой номер уже зарегистрирован',
                                    confirmButtonText: 'ОК'
                                });
                            }
                            else {
                                $('.show_phone_code').show();
                            }
                        }
                    })
                });

                $(document).keypress(function (e) {
                    if (e.which == 13) {
                        let selector = $('.accept_edit_with_code');
                        let phone = $('input[class="form-control phone"]').val();
                        let phone_code = $('input[class="form-control phone_code"]').val();

                        edit_with_sms(selector, phone, phone_code);
                    }
                });

                $('.accept_edit_with_code').on('click', function (e) {
                    e.preventDefault();

                    let selector = $(this);
                    let phone = $('input[class="form-control phone"]').val();
                    let phone_code = $('input[class="form-control phone_code"]').val();

                    edit_with_sms(selector, phone, phone_code);

                });
            });

            $('.edit_email').click(function (e) {
                e.preventDefault();

                $(this).hide();
                $('.show_email').hide();
                $('.show_email_code').hide();
                $('.email_edit_form').show();

                $('.cancel_edit').click(function (e) {
                    e.preventDefault();

                    $('.email_edit_form').hide();
                    $('.edit_email').show();
                    $('.show_email').show();
                });

                $('.accept_edit').click(function (e) {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let email = $('input[class="form-control email"]').val();

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'edit_email',
                            user_id: user_id,
                            email: email,
                        },
                        success: function (resp) {
                            if (resp == 'error') {
                                Swal.fire({
                                    title: 'Такой номер уже зарегистрирован',
                                    confirmButtonText: 'ОК'
                                });
                            }
                            else {
                                $('.show_email_code').show();
                            }
                        }
                    })
                });

                $('.accept_edit_email_with_code').click(function (e) {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let email = $('input[class="form-control email"]').val();
                    let code = $('input[class="form-control code"]').val();

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'edit_email_with_code',
                            user_id: user_id,
                            email: email,
                            code: code,
                        },
                        success: function (response) {
                            console.log(JSON.parse(response));
                            if (JSON.parse(response).error === 1) {
                                Swal.fire({
                                    title: 'Неверный код',
                                    confirmButtonText: 'ОК'
                                });
                            } else {
                                location.reload();
                            }
                        }
                    });
                });
            });

            $('.edit_password').click(function (e) {
                e.preventDefault();

                $(this).hide();
                $('.show_password').hide();
                $('.password_edit_form').show();

                $('.cancel_edit').click(function (e) {
                    e.preventDefault();

                    $('.password_edit_form').hide();
                    $('.edit_password').show();
                    $('.show_password').show();
                });

                $('.accept_edit').click(function (e) {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let old_password = $('input[class="form-control old_password"]').val();
                    let new_password = $('input[class="form-control new_password"]').val();
                    let new_password_confirmation = $('input[class="form-control new_password_confirmation"]').val();

                    if (new_password === new_password_confirmation) {
                        $.ajax({
                            method: 'POST',
                            data: {
                                action: 'edit_password',
                                user_id: user_id,
                                old_password: old_password,
                                new_password: new_password,
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
                        })
                    }
                });
            });

            $('.js-block-button').click(function (e) {
                e.preventDefault();

                if ($(this).hasClass('loading'))
                    return false;

                var manager_id = $(this).data('manager');

                $.ajax({
                    data: {
                        action: 'blocked',
                        manager_id: manager_id,
                        block: 1
                    },
                    beforeSend: function () {
                        $('.js-block-button').addClass('loading');
                    },
                    success: function (resp) {
                        $('.js-block-button').removeClass('loading').hide();
                        $('.js-unblock-button').show();
                    }
                })
            });
            $('.js-unblock-button').click(function (e) {
                e.preventDefault();

                if ($(this).hasClass('loading'))
                    return false;

                var manager_id = $(this).data('manager')

                $.ajax({
                    data: {
                        action: 'blocked',
                        manager_id: manager_id,
                        block: 0
                    },
                    beforeSend: function () {
                        $('.js-unblock-button').addClass('loading');
                    },
                    success: function (resp) {
                        $('.js-unblock-button').removeClass('loading').hide();
                        $('.js-block-button').show();
                    }
                })
            });
        })
        ;

        $('.js-filter-status').click(function (e) {
            e.preventDefault();

            var _id = $(this).data('status');

            if ($(this).hasClass('active')) {
                $(this).removeClass('active');

                $('.js-status-item').fadeIn();

            }
            else {
                $('.js-filter-status.active').removeClass('active');

                $(this).addClass('active');

                $('.js-status-item').hide();
                $('.js-status-' + _id).fadeIn();
            }
        });

        $('.groups').on('change', function (e) {
            e.preventDefault();

            $('.company_block').show();
            $('.companies').empty();
            $('.companies').append('<option value="none">Выберите из списка</option>');

            let group_id = $(this).val();

            if (group_id != 'none') {
                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'get_companies',
                        group_id: group_id
                    },
                    success: function (companies) {
                        for (let company in companies) {
                            $('.companies').append('<option value="' + companies[company]['id'] + '">' + companies[company]['name'] + '</option>')
                        }
                    }
                });
            } else {
                $('.company_block').hide();
            }
        });

        $('#block_manager').on('change', function () {
            let value = ($(this).val() == 0) ? 1 : 0;
            let manager_id = $(this).attr('data-manager');

            $.ajax({
                method: 'POST',
                data: {
                    action: 'block_manager',
                    value: value,
                    manager_id: manager_id
                }
            });
        });

        $('.delete_manager').on('click', function () {

            let manager_id = $(this).attr('data-manager');

            Swal.fire({
                title: 'Удалить пользователя',
                showCancelButton: true,
                confirmButtonText: 'Да',
                cancelButtonText: 'Нет',
            }).then((result) => {
                if (result.value === true) {
                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'delete_manager',
                            manager_id: manager_id,
                        },
                        success: function (resp) {
                            if (resp !== 'success') {
                                Swal.fire({
                                    title: resp,
                                    confirmButtonText: 'Ок'
                                });
                            } else {
                                location.replace('/managers')
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script>
        function edit_with_sms(selector, phone, phone_code) {

            let user_id = selector.attr('data-user');

            $.ajax({
                method: 'POST',
                data: {
                    action: 'edit_phone_with_code',
                    user_id: user_id,
                    phone: phone,
                    code: phone_code,
                },
                success: function (response) {
                    console.log(JSON.parse(response));
                    if (JSON.parse(response).error === 1) {
                        Swal.fire({
                            title: 'Неверный код',
                            confirmButtonText: 'ОК'
                        });
                    } else {
                        window.location.reload();
                    }
                }
            });
        }
    </script>
{/capture}

{capture name='page_styles'}

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
                <h3 class="text-themecolor mb-0 mt-0">
                    {if isset($user->id)}Профиль {$user->name|escape}
                    {else}Создать нового пользователя{/if}
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="managers">Пользователи</a></li>
                    <li class="breadcrumb-item active">
                        {if isset($user->id)}Профиль {$user->name|escape}
                        {else}Создать нового пользователя{/if}
                    </li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-md-12 col-lg-4 col-xlg-3">
                <div class="card">
                    <div class="card-body">
                        <center class="mt-4">

                            <h4 class="card-title mt-2">{$user->name|default:""}</h4>
                            <h6 class="card-subtitle">
                                {$roles[$user->role|default:""]}
                            </h6>
                            {*}
                            <div class="row text-center justify-content-md-center">
                                <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <font class="font-medium">254</font></a></div>
                                <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-picture"></i> <font class="font-medium">54</font></a></div>
                            </div>
                            {*}
                        </center>
                    </div>
                    <div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">Последний IP адрес</small>
                        <h6>{$user->last_ip|default:""}</h6>
                        <small class="text-muted p-t-30 db">Последняя активность</small>
                        <h6>
                            {if isset($user->last_visit)}
                                {$user->last_visit|date} {$user->last_visit|time}
                            {/if}
                        </h6><br>
                        {if in_array($manager->role, ['admin', 'developer']) && !isset($lk)}
                            <h6>
                                Заблокирован
                            </h6>
                            <div class="clearfix">
                                <div class="float-left">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="block_manager" id="block_manager"
                                               class="onoffswitch-checkbox block_manager" data-manager="{$user->id}"
                                                {if $user->blocked == 1} checked="true" value="1" {else} value="0"{/if}>
                                        <label class="onoffswitch-label" for="block_manager">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>

                    {if isset($user->id)}
                        {if $user->id && in_array('block_manager', $manager->permissions)}
                            <div class="mt-2 pt-2 pb-2 text-center">
                                <button {if $user->blocked}style="display:none"{/if}
                                        class="btn btn-danger btn-lg js-block-button" data-manager="{$user->id}">
                                    Заблокировать
                                </button>
                                <button {if !$user->blocked}style="display:none"{/if}
                                        class="btn btn-success btn-lg js-unblock-button" data-manager="{$user->id}">
                                    Разблокировать
                                </button>
                            </div>
                        {/if}
                    {/if}
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-md-12 col-lg-8 col-xlg-9">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#settings" role="tab">Основные</a>
                        </li>
                        <li class="nav-item"
                            {if isset($user)}{if $user->role!='team_collector'}style="display:none"{/if}{/if}>
                            <a class="nav-link" data-toggle="tab" href="#team" role="tab">Команда</a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <form class="form-horizontal" method="POST">
                        <div class="tab-content">

                            <div class="tab-pane active" id="settings" role="tabpanel">
                                <div class="card-body">

                                    {if isset($user->id)}<input type="hidden" name="id" value="{$user->id}"/>{/if}

                                    {if isset($errors) && count($errors)}
                                        <div class="col-md-12">
                                            <ul class="alert alert-danger" style="list-style-type: none">
                                                {if in_array('empty_role', (array)$errors)}
                                                    <li>Выберите роль пользователя!</li>
                                                {/if}
                                                {if in_array('login_exists', (array)$errors)}
                                                    <li>Такой логин уже существует!</li>
                                                {/if}
                                                {if in_array('empty_name', (array)$errors)}
                                                    <li>Укажите имя пользователя!</li>
                                                {/if}
                                                {if in_array('empty_login', (array)$errors)}
                                                    <li>Укажите логин для входа!</li>
                                                {/if}
                                                {if in_array('empty_password', (array)$errors)}
                                                    <li>Укажите пароль!</li>
                                                {/if}
                                                {if in_array('name_1c_not_found', (array)$errors)}
                                                    <li>Имя для обмена В 1С не найдено, проверьте правильность
                                                        написания!
                                                    </li>
                                                {/if}
                                            </ul>
                                        </div>
                                    {/if}

                                    {if isset($message_success)}
                                        <div class="col-md-12">
                                            <div class="alert alert-success">
                                                {if $message_success == 'added'}Новый пользователь добавлен{/if}
                                                {if $message_success == 'updated'}Данные сохранены{/if}
                                            </div>
                                        </div>
                                    {/if}

                                    <div class="form-group {if in_array('empty_role', (array)$errors)}has-danger{/if}">
                                        {if isset($user->id)}
                                            {if $user->id != $manager->id || ($user->id == $manager->id && in_array($manager->role, ['admin', 'developer']))}
                                                <label class="col-sm-12">Роль</label>
                                                <div class="col-sm-12">
                                                    <select name="role" class="form-control form-control-line"
                                                            required="true">
                                                        <option value=""></option>
                                                        {foreach $roles as $role => $role_name}
                                                            {if $manager->role == 'chief_collector' || $manager->role == 'team_collector'}
                                                                {if $role == 'collector' || $role == 'team_collector' || $role == 'user'}
                                                                    <option value="{$role}"
                                                                            {if $user->role == $role}selected="true"{/if}>{$role_name|escape}</option>
                                                                {/if}
                                                            {elseif $manager->role == 'city_manager'}
                                                                {if $role == 'cs_pc'}
                                                                    <option value="{$role}"
                                                                            {if $user->role == $role}selected="true"{/if}>{$role_name|escape}</option>
                                                                {/if}
                                                            {else}
                                                                <option value="{$role}"
                                                                        {if $user->role == $role}selected="true"{/if}>{$role_name|escape}</option>
                                                            {/if}
                                                        {/foreach}
                                                    </select>
                                                    {if in_array('empty_role', (array)$errors)}
                                                        <small class="form-control-feedback">Выберите роль!</small>
                                                    {/if}
                                                </div>
                                            {else}
                                                <input type="hidden" name="role" value="{$user->role}"/>
                                            {/if}
                                        {else}
                                            <label class="col-sm-12">Роль</label>
                                            <div class="col-sm-12">
                                                <select name="role" class="form-control form-control-line"
                                                        required="true">
                                                    <option value=""></option>
                                                    {foreach $roles as $role => $role_name}
                                                        {if $manager->role == 'chief_collector' || $manager->role == 'team_collector'}
                                                            {if $role == 'collector' || $role == 'team_collector' || $role == 'user'}
                                                                <option value="{$role}">{$role_name|escape}</option>
                                                            {/if}
                                                        {elseif $manager->role == 'city_manager'}
                                                            {if $role == 'cs_pc'}
                                                                <option value="{$role}">{$role_name|escape}</option>
                                                            {/if}
                                                        {else}
                                                            <option value="{$role}">{$role_name|escape}</option>
                                                        {/if}
                                                    {/foreach}
                                                </select>
                                                {if in_array('empty_role', (array)$errors)}
                                                    <small class="form-control-feedback">Выберите роль!</small>
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>
                                    {if in_array('admins', $manager->permissions)}
                                        <div class="form-group {if in_array('empty_name', (array)$errors)}has-danger{/if}">
                                            <label class="col-md-12">Пользователь</label>
                                            <div class="col-md-12">
                                                <input type="text" name="name"
                                                       value="{if isset($user)}{$user->name|escape}{/if}"
                                                       class="form-control form-control-line" required="true"/>
                                                {if in_array('empty_name', (array)$errors)}
                                                    <small class="form-control-feedback">Укажите имя!</small>
                                                {/if}
                                            </div>
                                        </div>
                                        {*
                                        <div class="form-group">
                                            <label class="col-md-12">Имя для обмена 1С</label>
                                            <div class="col-md-12">
                                                <input type="text" name="name_1c" value="{$user->name_1c|escape}" class="form-control form-control-line" />
                                            </div>
                                        </div>
                                        *}
                                        <div class="form-group {if in_array('empty_login', (array)$errors)}has-danger{/if}">
                                            <label for="login" class="col-md-12">Логин для входа</label>
                                            <div class="col-md-12">
                                                <input type="text" id="login" name="login"
                                                       value="{if isset($user)}{$user->login|escape}{/if}"
                                                       class="form-control form-control-line" required="true"/>
                                                {if in_array('empty_login', (array)$errors)}
                                                    <small class="form-control-feedback">Укажите логин!</small>
                                                {/if}
                                            </div>
                                        </div>
                                    {/if}
                                    {if in_array($manager->role, ['admin', 'developer'])}
                                        <div class="form-group {if in_array('empty_password', (array)$errors)}has-danger{/if}">
                                            <label class="col-md-12">{if isset($user->id)}Новый пароль{else}Пароль{/if}</label>
                                            <div class="col-md-12">
                                                <input type="password" name="password" value=""
                                                       class="form-control form-control-line"
                                                       {if !isset($user->id)}required="true"{/if} />
                                                {if in_array('empty_password', (array)$errors)}
                                                    <small class="form-control-feedback">Укажите пароль!</small>
                                                {/if}
                                            </div>
                                        </div>
                                    {else}
                                        <div class="col-12">
                                            <h5 class="form-control-static">Пароль <a href="#" data-user="{$client->id}"
                                                                                      class="text-info edit_password"><i
                                                            class="fas fa-edit"></i></a></h5>
                                            <div class="show_password">*********</div>
                                            <div class="password_edit_form" style="display: none">
                                                <div class="mb-3">
                                                    <label>Старый пароль</label>
                                                    <div>
                                                        <input type="password" name="old_password" value=""
                                                               class="form-control old_password"
                                                               {if !isset($user->id)}required="true"{/if} />
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Новый пароль</label>
                                                    <div>
                                                        <input type="password" name="new_password" value=""
                                                               class="form-control new_password"
                                                               {if !isset($user->id)}required="true"{/if} />
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Повторите пароль</label>
                                                    <div>
                                                        <input type="password" name="new_password_confirmation" value=""
                                                               class="form-control new_password_confirmation"
                                                               {if !isset($user->id)}required="true"{/if} />
                                                    </div>
                                                </div>
                                                <div>
                                                    <input type="button"
                                                           data-user="{$user->id}"
                                                           class="btn btn-success accept_edit"
                                                           value="Сохранить">
                                                    <input type="button"
                                                           class="btn btn-danger cancel_edit"
                                                           value="Отмена">
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {if in_array($manager->role, ['admin', 'developer'])}
                                        <div class="form-group">
                                            <label class="col-md-12">Группа</label>
                                            <div class="col-md-12">
                                                <select class="form-control groups" name="groups">
                                                    <option value="none" selected>Выберите из списка</option>
                                                    {if !empty($groups)}
                                                        {foreach $groups as $group}
                                                            <option value="{$group->id}"
                                                                    {if $user->group_id == $group->id}selected{/if}>{$group->name}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group company_block"
                                             {if $user->company_id == 0 && $user->group_id == 0}style="display:none"{/if}>
                                            <label class="col-md-12">Компания</label>
                                            <div class="col-md-12">
                                                <select class="form-control companies" name="companies">
                                                    <option value="none" selected>Выберите из списка</option>
                                                    {if !empty($companies)}
                                                        {foreach $companies as $company}
                                                            <option value="{$company->id}"
                                                                    {if $user->company_id == $company->id}selected{/if}>{$company->name}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
                                            </div>
                                        </div>
                                    {/if}
                                    {if in_array($manager->role, ['admin', 'developer'])}
                                        <div class="form-group">
                                            <label class="col-md-5">Email</label>
                                            <div class="col-md-12">
                                                <input type="text" name="email" value="{$user->email|default: ""}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    {else}
                                        <div class="col-12">
                                            <h5 class="form-control-static">Email <a href="#" data-user="{$client->id}"
                                                                                     class="text-info edit_email"><i
                                                            class="fas fa-edit"></i></a></h5>
                                            <div class="show_email">{$user->email|default: "Email не введён"}</div>
                                            <h5 class="form-control-static">Компания</h5>
                                            <div class="show_email">{$user->company_name|default: "Отсутствует компания"}</div>
                                            <div class="email_edit_form" style="display: none">
                                                <div class="mb-3">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <input type="text" class="form-control email"
                                                                   value="{$user->email|default: ""}">
                                                        </div>
                                                        <div class="col">
                                                            <input type="button"
                                                                   data-user="{$user->id}"
                                                                   class="btn btn-success accept_edit"
                                                                   value="Сохранить">
                                                            <input type="button"
                                                                   class="btn btn-danger cancel_edit"
                                                                   value="Отмена">
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="input-group show_email_code"
                                                                 style="display: none">
                                                                <input type="text" class="form-control code"
                                                                       placeholder="Введите код из письма">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-primary accept_edit_email_with_code"
                                                                            type="button" data-user="{$user->id}">
                                                                        Подтвердить
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {if in_array($manager->role, ['admin', 'developer'])}
                                        <div class="form-group">
                                            <label class="col-md-5">Телефон</label>
                                            <div class="col-md-12">
                                                <input type="text" name="phone" value="{$user->phone|default: ""}"
                                                       class="form-control form-control-line" autocomplete="off">
                                            </div>
                                        </div>
                                    {else}
                                        <div class="col-12">
                                            <h5 class="form-control-static">Телефон <a href="#"
                                                                                       data-user="{$client->id}"
                                                                                       class="text-info edit_phone"><i
                                                            class="fas fa-edit"></i></a></h5>
                                            <div class="show_phone">{$user->phone|default: "Телефон не введён"}</div>
                                            <div class="phone_edit_form" style="display: none">
                                                <div class="mb-3">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <input type="text" class="form-control phone"
                                                                   value="{$user->phone|default: ""}">
                                                        </div>
                                                        <div class="col">
                                                            <input type="button"
                                                                   data-user="{$user->id}"
                                                                   class="btn btn-success accept_edit"
                                                                   value="Сохранить">
                                                            <input type="button"
                                                                   class="btn btn-danger cancel_edit"
                                                                   value="Отмена">
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="input-group show_phone_code"
                                                                 style="display: none">
                                                                <input type="text" class="form-control phone_code"
                                                                       placeholder="Введите код из смс">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-primary accept_edit_with_code"
                                                                            type="button" data-user="{$user->id}">
                                                                        Подтвердить
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {*
                                                                        {if in_array($manager->role, ['chief_collector','admin','developer'])}

                                                                        <div class="form-group">
                                                                            <label class="col-md-12">Статусы договоров для коллекторов</label>
                                                                            <div class="col-sm-12">
                                                                                <select name="collection_status_id" class="form-control form-control-line" >
                                                                                    <option value=""></option>
                                                                                    {foreach $collection_statuses as $collection_status_id => $collection_status_name}
                                                                                    <option value="{$collection_status_id}" {if $user->collection_status_id == $collection_status_id}selected="true"{/if}>{$collection_status_name|escape}</option>
                                                                                    {/foreach}
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        {else}

                                                                        <div class="form-group">
                                                                            <label class="col-md-12">Статусы договоров для коллекторов</label>
                                                                            <div class="col-sm-12">
                                                                                <p>{$collection_statuses[$user->collection_status_id]}</p>
                                                                                <input type="hidden" name="collection_status_id" value="{$user->collection_status_id}" />
                                                                            </div>
                                                                        </div>

                                                                        {/if}

                                                                        <div class="form-group">
                                                                            <label class="col-md-12">Mango-office внутренний номер</label>
                                                                            <div class="col-md-12">
                                                                                <input type="text" name="mango_number" value="{$user->mango_number}" class="form-control form-control-line" />
                                                                            </div>
                                                                        </div>
                                    *}
                                    {if in_array($manager->role, ['admin', 'developer'])}
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-outline-success" type="submit">Сохранить</button>
                                                <div data-manager="{$user->id}"
                                                     class="btn btn-outline-danger float-right delete_manager"
                                                     type="button">Удалить
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            <div class="tab-pane" id="team" role="tabpanel">
                                <div class="card-body">
                                    <div class="form-group pl-3 pr-3">
                                        <div class="clearfix pb-3">
                                            <div class="float-left">
                                                <h5>Команда коллектора</h5>
                                            </div>
                                            <div class="float-right">
                                                {foreach $collection_statuses as $cs_id => $cs}
                                                    {if in_array($cs_id, (array)$collection_manager_statuses)}
                                                        <a data-status="{$cs_id}"
                                                           class="js-filter-status btn btn-sm btn-outline-{if $cs_id==6}warning{elseif $cs_id==8}danger{elseif $cs_id==4}primary{/if}"
                                                           href="javascript:void();">{$cs}</a>
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        </div>

                                        <table class="table">
                                            {foreach $managers as $m}
                                                {if $m->role == 'collector'}
                                                    <tr class="js-status-item js-status-{$m->collection_status_id}">
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input {if !in_array($manager->role, ['chief_collector','admin','developer'])}disabled="true"{/if}
                                                                       class="custom-control-input" type="checkbox"
                                                                       name="team_id[]" id="team_id_{$m->id}"
                                                                       value="{$m->id}" {if in_array($m->id, (array)$user->team_id)}checked="true"{/if} />
                                                                <label class="custom-control-label"
                                                                       for="team_id_{$m->id}"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a class="{if $m->blocked}text-danger{/if}" target="_blank"
                                                               href="manager/{$m->id}">
                                                                <strong>{$m->name|escape}</strong>
                                                            </a>
                                                            {if $m->blocked}<span class="label label-danger">Заблокирован</span>{/if}
                                                        </td>
                                                        <td>
                                                            <label class="label {if $m->collection_status_id==6}label-warning{elseif $m->collection_status_id==8}label-danger{elseif $m->collection_status_id==4}label-primary{/if}">
                                                                {$collection_statuses[$m->collection_status_id]}
                                                            </label>
                                                        </td>
                                                        <td>
                                                            {foreach $managers as $man}
                                                                {if $man->role=='team_collector' && in_array($m->id, (array)$man->team_id)}
                                                                    <small>{$man->name|escape}</small>
                                                                    <br/>
                                                                {/if}
                                                            {/foreach}

                                                        </td>
                                                    </tr>
                                                {/if}
                                            {/foreach}
                                        </table>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-success" type="submit">Сохранить</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Column -->
        </div>
        <!-- Row -->
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
</div>
