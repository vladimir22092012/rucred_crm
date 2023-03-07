{$meta_title="`$client->lastname` `$client->firstname` `$client->patronymic`" scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/order.js?v=1.16"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/movements.app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script>
            let token_dadata = "25c845f063f9f3161487619f630663b2d1e4dcd7";

        $(function () {

            $('.daterange').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD.MM.YYYY'
                },
            });

            $.fn.setCursorPosition = function (pos) {
                if ($(this).get(0).setSelectionRange) {
                    $(this).get(0).setSelectionRange(pos, pos);
                } else if ($(this).get(0).createTextRange) {
                    var range = $(this).get(0).createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', pos);
                    range.moveStart('character', pos);
                    range.select();
                }
            };

            $('.phone_num').click(function () {
                $(this).setCursorPosition(3);
            }).mask('+7(999)999-99-99');

            $('#editinnValue').click(function() {
                $(this).setCursorPosition(0);
            }).mask('999999999999');

            $('#editsnilsValue').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999-999-999 99');

            $('.subdivision_code').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999-999');

            $('.passport_serial').click(function () {
                $(this).setCursorPosition(0);
            }).mask('99 99 999999');

            Inputmask({
                casing: 'upper'
            }).mask($('.casing-upper-mask'));

            $('.show_phone_code').hide();
            $('.show_phone_confirmed').hide();

            $('.accept_edit').click(function (e) {
                e.preventDefault();

                let phone = $('input[class="form-control phone_num"]').val();
                let user_id = $(this).attr('data-user');

                $.ajax({
                    url: '/neworder',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'edit_phone',
                        phone: phone,
                        user_id: user_id
                    },
                    success: function (response) {
                        if (response['error']) {
                            Swal.fire({
                                title: response['error'],
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            let code = response['code'];
                            $('.phone_send_code').show().text(code);
                            $('.show_phone_code').show();
                        }
                    }
                })
            });

            $('.accept_edit_with_code').click(function (e) {
                e.preventDefault();

                let phone = $('input[class="form-control phone_num"]').val();
                let phone_code = $('input[class="form-control phone_code"]').val();

                $.ajax({
                    url: '/neworder',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'edit_phone_with_code',
                        phone: phone,
                        code: phone_code,
                    },
                    success: function (resp) {
                        if (resp['error'] == 1) {
                            Swal.fire({
                                title: 'Код не верный',
                                confirmButtonText: 'ОК'
                            });
                        }
                        if (resp['success'] == 1) {
                            $('.show_phone_code').hide();
                            $('.show_phone_confirmed').show();
                            $('.accept_edit').hide();
                            $('.phone_confirmed').val('1');
                        }
                    }
                })
            });

            $('.show_email_code').hide();
            $('.show_email_confirmed').hide();

            $('.accept_email_edit').click(function (e) {
                e.preventDefault();

                let email = $('input[class="form-control casing-upper-mask email"]').val().toLowerCase();
                let user_id = $(this).attr('data-user');

                $.ajax({
                    url: '/neworder',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'edit_email',
                        email: email,
                        user_id: user_id
                    },
                    success: function (response) {
                        if (response['error']) {
                            Swal.fire({
                                title: response['error'],
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            $('.show_email_code').show();
                            $('.email_code_show').text(response['success']);
                            $('.email_code_show').show();
                        }
                    }
                })
            });

            $('.accept_edit_email_with_code').click(function (e) {
                e.preventDefault();

                let email = $('input[class="form-control casing-upper-mask email"]').val();
                let email_code = $('input[class="form-control email_code"]').val();

                $.ajax({
                    url: '/neworder',
                    method: 'POST',
                    data: {
                        action: 'edit_email_with_code',
                        email: email,
                        code: email_code,
                    },
                    success: function (response) {
                        if (response.error === 1) {
                            Swal.fire({
                                title: response.reason,
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            $('.show_email_code').hide();
                            $('.show_email_confirmed').show();
                            $('.accept_email_edit').hide();
                            $('.email_confirmed').val('1');
                        }
                    }
                })
            });

            $('input[name="viber_same"], input[name="telegram_same"], input[name="whatsapp_same"]').on('click', function () {
                let attr = $(this).attr('name');
                let phone = $('input[name="phone"]').val();

                $('.' + attr + '').toggle();

                if ($(this).is(':checked')) {
                    if ($(this).attr('name') == 'viber_same')
                        $('.confirm_viber').attr('data-phone', phone);

                    if ($(this).attr('name') == 'telegram_same')
                        $('.confirm_telegram').attr('data-phone', phone);
                }
            });

            $(document).on('click', '.confirm_telegram, .confirm_viber', function (e) {

                let user_id = $('input[name="user_id"]').val();
                let type = '';
                let same_flag = 0;
                let phone = $(this).parent().find('.phone_num').val();
                let email = $('input[name="email"]').val();

                if ($(this).hasClass('confirm_telegram')) {
                    if ($('input[name="telegram_same"]').is(':checked')) {
                        phone = $(this).attr('data-phone');
                    }

                    type = 'telegram';
                }

                if ($(this).hasClass('confirm_viber')) {
                    if ($('input[name="viber_same"]').is(':checked')) {
                        phone = $(this).attr('data-phone');
                    }

                    type = 'viber';
                }

                $.ajax({
                    url: '/neworder',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'confirm_messengers',
                        type: type,
                        phone: phone,
                        user_id: user_id,
                        email: email
                    },
                    success: function (resp) {
                        if (resp['success']) {
                            switch (resp['type']) {
                                case 'telegram':
                                    $('input[name="telegram_same"]').fadeOut();
                                    $('.telegram_same_label').fadeOut();
                                    $('.confirm_telegram').fadeOut();
                                    setTimeout(function () {
                                        $('.telegram_confirmed').fadeIn();
                                    }, 1500);
                                    break;

                                case 'viber':
                                    $('input[name="viber_same"]').fadeOut();
                                    $('.viber_same_label').fadeOut();
                                    $('.confirm_viber').fadeOut();
                                    setTimeout(function () {
                                        $('.viber_confirmed').fadeIn();
                                    }, 1500);
                                    break;
                            }
                            $('input[name="user_id"]').attr('value', resp['user_id'])
                        } else {
                            Swal.fire({
                                title: 'Произошла ошибка',
                                confirmButtonText: 'ОК'
                            });
                        }
                    }
                });
            });

            $(document).on('change', '.photo_status', function () {

                if ($(this).val() != 1)
                    $('div[class="order-image-actions"]').show();
                else
                    $('div[class="order-image-actions"]').hide();

                let status = $(this).val();
                let file_id = $(this).attr('data-file');

                $.ajax({
                    url: '/order',
                    method: 'POST',
                    data: {
                        action: 'change_photo_status',
                        status: status,
                        file_id: file_id
                    }
                });
            });

            $(document).on('click', '.delete-photo', function() {
                let file_id = $(this).attr('data-file-id');
                if (file_id.length) {
                    $('.fileContainer[data-file-id="'+file_id+'"]').remove()
                    $.ajax({
                        url: '/upload_files',
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            action: 'remove',
                            id: file_id,
                        },
                        success: function (resp) {

                        }
                    })
                }
            });

            $('.findRegAddress').suggestions({
                token: token_dadata,
                type: "ADDRESS",
                minChars: 3,
                onSelect: function (suggestion) {
                    $(this).val(suggestion.value.toUpperCase());
                    $('.reg_address_json').val(JSON.stringify(suggestion.data))
                }
            });

            $('.findFaktAddress').suggestions({
                token: token_dadata,
                type: "ADDRESS",
                minChars: 3,
                onSelect: function (suggestion) {
                    $(this).val(suggestion.value.toUpperCase());
                    $('.fakt_address_json').val(JSON.stringify(suggestion.data))
                }
            });

            $('.js-open-user-data-form').on('click', function() {
                let field = $(this).attr('data-type');
                if (field.length) {
                    $('.show_edit_block_' + field).show();
                    $('#editClientDataModal').modal();
                }
            });

            $('.js-close-user-data-form').on('click', function() {
                let field = $(this).attr('data-type');
                if (field.length) {
                    $('.show_edit_block_' + field).hide();
                    $('#editClientDataModal').modal('hide');
                }
            });

            $('.js-save-user-data-form').on('click', function() {
                let field = $(this).attr('data-type');
                if (field.length) {

                    let data = {
                        action: 'edit_user_value',
                        field: field,
                        user_id: $('#js-user-id').val(),
                        comment: $('#edit'+field+'CommentValue').val()
                    };

                    if (field == 'inn' || field == 'snils') {
                        data.values = [];
                        data.values.push(
                            {
                                field: field,
                                value: $('#edit'+field+'Value').val()
                            }
                        );
                    }
                    if (field == 'passport') {
                        let form_fields = $('#'+field+'Form').find('input');
                        data.values = [];
                        form_fields.each((key, item) => {
                            data.values.push({
                                field: $(item).attr('name'),
                                value: $(item).val()
                            });
                        });
                    }
                    if (field == 'addresses') {
                        let form_fields = $('#'+field+'Form').find('input');
                        data.values = {};
                        form_fields.each((key, item) => {
                            data.values[$(item).attr('name')] = $(item).val();
                        });
                    }

                    $('.show_edit_block_' + field).hide();
                    $('#editClientDataModal').modal('hide');

                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: data,
                        success: function (resp) {
                            if (resp['error']) {
                                Swal.fire({
                                    title: resp['error'],
                                    confirmButtonText: 'Ок',
                                })
                            } else {
                                let message = 'Данные успешно изменены';
                                if (field == 'addresses') {
                                    message = 'Адреса успешно изменены';
                                }
                                if (field == 'passport') {
                                    message = 'Паспортные данные успешно изменены';
                                }
                                if (field == 'snils') {
                                    message = 'СНИЛС успешно изменён';
                                }
                                if (field == 'inn') {
                                    message = 'ИНН успешно изменён';
                                }
                                Swal.fire({
                                    title: message,
                                }).then((result) => {
                                    location.reload();
                                });
                            }
                        }
                    })
                }
            });

            $('#blacklist').on('click', function () {
                let user_id = $(this).attr('data-user');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'blacklist',
                        user_id: user_id
                    }
                });
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

            $('.fa-eraser').on('click', function (e) {
                e.preventDefault();

                $('.employer_show, #employer_edit').toggle();

                $('.cancel_employer').on('click', function () {
                    $('#employer_edit').hide();
                    $('.employer_show').show();
                });
            });

            $('.accept_employer').on('click', function (e) {
                e.preventDefault();

                let group_id = $('#group_select').val();
                let company_id = $('#company_select').val();
                let branch_id = $('#branch_select').val();
                let user_id = $(this).attr('data-user');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_employer_info',
                        user_id: user_id,
                        group: group_id,
                        company: company_id,
                        branch: branch_id
                    },
                    success: function () {
                        location.reload();
                    }
                })
            });

            $('#group_select').on('change', function () {
                let group_id = $(this).val();

                $('#company_select').empty();
                $('#company_select').append('<option value="none">Выберите из списка</option>');
                $('#branch_select').empty();
                $('#branch_select').append('<option value="none">Выберите из списка</option>');

                if (group_id != 'none') {
                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            action: 'get_companies',
                            group_id: group_id
                        },
                        success: function (resp) {
                            for (let key in resp['companies']) {
                                $('#company_select').append('<option value="' + resp['companies'][key]['id'] + '">' + resp['companies'][key]['name'] + '</option>')
                            }
                        }
                    });
                }
            });

            $('#company_select').on('change', function () {
                let company_id = $(this).val();

                $('#branch_select').empty();
                $('#branch_select').append('<option value="none">Выберите из списка</option>');

                if (company_id != 'none') {
                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            action: 'get_branches',
                            company_id: company_id
                        },
                        success: function (resp) {
                            for (let key in resp['branches']) {
                                $('#branch_select').append('<option value="' + resp['branches'][key]['id'] + '">' + resp['branches'][key]['name'] + '</option>')
                            }
                        }
                    });
                }
            });

            $('.delete_client').on('click', function (e) {
                e.preventDefault();

                let user_id = $(this).attr('data-user');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'delete_client',
                        user_id: user_id
                    },
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'Ок',
                            })
                        } else {
                            Swal.fire({
                                title: 'Пользователь успешно удален',
                            }).then((result) => {
                                location.replace('/clients');
                            });
                        }
                    }
                })
            });

            $('#canSendOnec, #canSendYaDisk').on('click', function () {

                let value = 0;
                let userId = $(this).attr('data-user');
                let action = 'sendOnecTrigger';

                if ($(this).attr('id') == 'canSendYaDisk')
                    action = 'sendYaDiskTrigger';

                if ($(this).is(':checked'))
                    value = 1;

                $.ajax({
                    method: 'POST',
                    data: {
                        action: action,
                        userId: userId,
                        value: value
                    }
                });
            });
        })
    </script>
{/capture}


{capture name='page_styles'}
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css" rel="stylesheet"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
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
        {if $in_blacklist == 1}
            <div class="col-md-4 col-12">
                <ul class="alert alert-danger" style="list-style-type: none">
                    <li>Клиент находится в черном списке</li>
                </ul>
            </div>
        {/if}


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
                                                <input type="hidden" id="js-user-id" value="{$client->id}"/>
                                                <h3>
                                                    {$client->lastname|escape}
                                                    {$client->firstname|escape}
                                                    {$client->patronymic|escape}
                                                </h3>
                                                <small>Дата регистрации:
                                                    {$client->created|date}</small>
                                                <br>
                                                <small>Номер клиента:</small>
                                                <small class="show_personal_number">{$client->personal_number}</small>
                                                <br>
                                                <small>ID клиента:</small>
                                                <small class="show_personal_number">{$client->id}</small>
                                            </div>
                                            {if $manager->role != 'employer'}
                                                <div class="col-md-2">
                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                                                        <input type="checkbox"
                                                               class="custom-control-input js-blocked-input"
                                                               id="blocked" value="1" data-user="{$client->id}"
                                                               {if $client->blocked}checked{/if}>
                                                        <label class="custom-control-label" for="blocked"><strong
                                                                    class="text-danger">Заблокирован</strong></label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                                                        <input data-user="{$client->id}" class="custom-control-input"
                                                               id="blacklist" type="checkbox"
                                                               {if $in_blacklist == 1}checked{/if}>
                                                        <label class="custom-control-label" for="blacklist">
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
                                            {/if}
                                        </div>
                                        {if in_array($manager->role, ['admin', 'developer'])}
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       data-user="{$client->id}" id="canSendOnec"
                                                       {if $client->canSendOnec}checked{/if}>
                                                <label class="custom-control-label" for="canSendOnec"><strong class="text-danger">Отправлять в 1с</strong></label>
                                                <span style="position: relative;font-size: 13px;left: 10px;">{$lastUpdateOnec}</span>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       data-user="{$client->id}" id="canSendYaDisk"
                                                       {if $client->canSendYaDisk}checked{/if}>
                                                <label class="custom-control-label" for="canSendYaDisk"><strong class="text-danger">Отправлять в Я.Диск</strong></label>
                                                <span style="position: relative;font-size: 13px;left: 10px;">{$lastUploadDisk}</span>
                                            </div>
                                        {/if}
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
                                <span class="hidden-xs-down">Общая информация</span>
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
                                                <span class="text-white ">Общая информация</span>
                                                {if in_array($manager->role, ['admin', 'middle'])}
                                                <span class="float-right">
                                                    <a href="javascript:void(0);" class="text-white js-open-user-data-form" data-type="passport">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </span>
                                                {/if}
                                            </h5>

                                                <div class="row pt-2 show_view_block_passport view-block {if $contactdata_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">ФИО:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {$client->lastname|escape}
                                                                    {$client->firstname|escape}
                                                                    {$client->patronymic|escape}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Дата рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->birth|date}</p>
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
                                                                    от {$client->passport_date|date}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Кем выдан, код
                                                                подразделения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {$client->passport_issued|escape}
                                                                    {$client->subdivision_code|escape}
                                                                </p>
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
                                        <!-- Контакты -->
                                        <form action="{url}" class="mb-3 border js-order-item-form"
                                              id="personal_data_form">

                                            <input type="hidden" name="action" value="contactdata"/>
                                            <input type="hidden" name="user_id" value="{$client->id}"/>

                                            <h5 class="card-header">
                                                <span class="text-white ">Контактная информация</span>
                                                {if in_array($manager->role, ['admin', 'middle'])}
                                                    <span class="float-right">
                                                    <a href="javascript:void(0);" class="text-white js-open-user-data-form" data-type="numbers">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </span>
                                                {/if}
                                            </h5>
                                            <div class="row pt-2 view-block {if $contactdata_error}hide{/if}">
                                                <div class="col-md-12">
                                                    <div class="form-group row m-0">
                                                        <label class="control-label col-md-4">Телефон:</label>
                                                        <div class="col-md-8">
                                                            <p class="form-control-static">{$client->phone_mobile|escape}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group row m-0">
                                                        <label class="control-label col-md-4">Email:</label>
                                                        <div class="col-md-8">
                                                            <p class="form-control-static">{$client->email|escape}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $client->viber_num}
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md 4">Viber:</label><br>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->viber_num}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                {if $client->telegram_num}
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label
                                                                class="control-label col-md 4">Telegram:</label><br>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->telegram_num}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                {if $client->whatsapp_num}
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label
                                                                class="control-label col-md 4">WhatsApp:</label><br>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$client->whatsapp_num}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            </div>
                                        </form>
                                        <!-- / Контакты-->
                                        <!-- / Адреса-->
                                        <form action="{url}" class="js-order-item-form mb-3 border"
                                              id="address_form">

                                            <input type="hidden" name="action" value="addresses"/>
                                            <input type="hidden" name="user_id" value="{$client->id}"/>

                                            <h5 class="card-header">
                                                <span class="text-white">Адрес</span>
                                                {if in_array($manager->role, ['admin', 'middle'])}
                                                <span class="float-right">
                                                    <a href="javascript:void(0);" class="text-white js-open-user-data-form" data-type="addresses">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </span>
                                                {/if}
                                            </h5>

                                                <div class="row show_view_block_addresses view-block {if $addresses_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover mb-0">
                                                            <tr>
                                                                <td>Адрес регистрации</td>
                                                                <td>
                                                                    {$client->regaddress->adressfull}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Адрес проживания</td>
                                                                <td>
                                                                    {$client->faktaddress->adressfull}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            <div class="edit-block m-0 {if !$addresses_error}hide{/if}">

                                                    <div class="row m-0 mb-2 mt-2 js-dadata-address">
                                                        <h6 class="col-12 nav-small-cap">Адрес регистрации</h6>
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
                                                                <input type="hidden" name="regaddress_id"
                                                                       value="<br />{$client->regaddress_id}"/>
                                                                <input type="text" class="form-control js-dadata-region"
                                                                       name="Regregion"
                                                                       value="{$client->regaddress->region|escape}"
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
                                                                       name="Regcity"
                                                                       value="{$client->regaddress->city|escape}"
                                                                       placeholder=""/>
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
                                                                       value="{$client->regaddress->district|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Нас. пункт</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-locality"
                                                                       name="Reglocality"
                                                                       value="{$client->regaddress->locality|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group mb-1 {if in_array('empty_regstreet', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Улица</label>
                                                                <input type="text" class="form-control js-dadata-street"
                                                                       name="Regstreet"
                                                                       value="{$client->regaddress->street|escape}"
                                                                       placeholder=""/>
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
                                                                       value="{$client->regaddress->house|escape}"
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
                                                                       value="{$client->regaddress->building|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Квартира</label>
                                                                <input type="text" class="form-control js-dadata-room"
                                                                       name="Regroom"
                                                                       value="{$client->regaddress->room|escape}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Индекс</label>
                                                                <input type="text" class="form-control js-dadata-index"
                                                                       name="Regindex"
                                                                       value="{$client->regaddress->index|escape}"
                                                                       placeholder=""
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>

                                                <div class="row m-0 js-dadata-address">
                                                    <h6 class="col-12 nav-small-cap">Адрес проживания</h6>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-1 {if in_array('empty_faktregion', (array)$addresses_error)}has-danger{/if}">
                                                            <label class="control-label">Область</label>
                                                            <input type="hidden" name="faktaddress_id"
                                                                   value="{$client->faktaddress_id}"/>
                                                            <input type="text" class="form-control js-dadata-region"
                                                                   name="Faktregion"
                                                                   value="{$client->faktaddress->region|escape}"
                                                                   placeholder=""/>
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
                                                                   value="{$client->faktaddress->city|escape}"
                                                                   placeholder=""
                                                            />
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
                                                                   value="{$client->faktaddress->district|escape}"
                                                                   placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-1">
                                                            <label class="control-label">Нас. пункт</label>
                                                            <input type="text"
                                                                   class="form-control js-dadata-locality"
                                                                   name="Faktlocality"
                                                                   value="{$client->faktaddress->locality|escape}"
                                                                   placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mb-1 {if in_array('empty_faktstreet', (array)$addresses_error)}has-danger{/if}">
                                                            <label class="control-label">Улица</label>
                                                            <input type="text" class="form-control js-dadata-street"
                                                                   name="Faktstreet"
                                                                   value="{$client->faktaddress->street|escape}"
                                                                   placeholder=""/>
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
                                                                   value="{$client->faktaddress->house|escape}"
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
                                                                   value="{$client->faktaddress->building|escape}"
                                                                   placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Квартира</label>
                                                            <input type="text" class="form-control js-dadata-room"
                                                                   name="Faktroom"
                                                                   value="{$client->faktaddress->room|escape}"
                                                                   placeholder=""
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Индекс</label>
                                                            <input type="text" class="form-control js-dadata-index"
                                                                   name="Faktindex"
                                                                   value="{$client->faktaddress->index|escape}"
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
                                        <!-- / Фото-->
                                        {if in_array($manager->role, ['admin', 'middle'])}
                                            <h5 class="card-header">
                                                <span class="text-white">Фотографии</span>
                                            </h5>
                                            <form action="{url}" method="POST" class="border js-order-item-form mb-3"
                                                  id="images_form">
                                                <input type="hidden" name="action" value="images"/>
                                                <input type="hidden" name="id" value="{$client->id}"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>
                                                <div class="row p-2 view-block">
                                                    <ul class="col-md-12 list-inline"
                                                        style="display: flex; justify-content: left">
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
                                                            {elseif $file->status == 4}
                                                                {$item_class="border-info border"}
                                                                {$ribbon_class="ribbon-info"}
                                                                {$ribbon_icon="fab fa-cloudversify"}
                                                            {/if}
                                                            <div data-file-id="{$file->id}" class="fileContainer" style="display: flex; flex-direction: column; margin-left: 15px">
                                                                {if isset($file->format)}
                                                                    <label class="badge badge-danger">Это PDF</label>
                                                                {else}
                                                                    <label class="badge badge-primary">Это фото</label>
                                                                {/if}
                                                                <li class="order-image-item ribbon-wrapper rounded-sm border {$item_class}">
                                                                    <a class="image-popup-fit-width js-event-add-click"
                                                                       href="javascript:void(0);"
                                                                       onclick="window.open('{$config->back_url}/files/users/{$client->id}/{$file->name}');"
                                                                       data-event="50" data-manager="{$manager->id}"
                                                                       data-order="{$order->order_id}"
                                                                       data-user="{$client->id}">
                                                                        <div class="ribbon ribbon-corner {$ribbon_class}">
                                                                            <i
                                                                                class="{$ribbon_icon}"></i></div>
                                                                        <img src="{$config->back_url}/files/users/{$client->id}/{$file->name}"
                                                                             alt="" class="img-responsive" style=""/>
                                                                    </a>
                                                                    <div class="order-image-actions"
                                                                         {if !in_array($order->status, [0]) || $file->type == 'document'}style="display: none"{/if}>
                                                                        {if $manager->role != 'employer'}
                                                                            <div class="dropdown mr-1 show ">
                                                                                <button type="button"
                                                                                        class="btn {if $file->status==2}btn-success{elseif $file->status==3}btn-danger{else}btn-secondary{/if} dropdown-toggle"
                                                                                        id="dropdownMenuOffset"
                                                                                        data-toggle="dropdown"
                                                                                        aria-haspopup="true"
                                                                                        aria-expanded="true">
                                                                                    {if $file->status == 2}Принят
                                                                                    {elseif $file->status == 3}Отклонен
                                                                                    {else}Статус
                                                                                    {/if}
                                                                                </button>
                                                                                <div class="dropdown-menu"
                                                                                     aria-labelledby="dropdownMenuOffset"
                                                                                     x-placement="bottom-start">
                                                                                    <div class="p-1 dropdown-item">
                                                                                        <button
                                                                                            class="btn btn-sm btn-block btn-outline-success js-image-accept js-event-add-click"
                                                                                            data-event="51"
                                                                                            data-manager="{$manager->id}"
                                                                                            data-order="{$order->order_id}"
                                                                                            data-user="{$client->id}"
                                                                                            data-id="{$file->id}"
                                                                                            type="button">
                                                                                            <i class="fas fa-check-circle"></i>
                                                                                            <span>Принять</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="p-1 dropdown-item">
                                                                                        <button
                                                                                            class="btn btn-sm btn-block btn-outline-danger js-image-reject js-event-add-click"
                                                                                            data-event="52"
                                                                                            data-manager="{$manager->id}"
                                                                                            data-order="{$order->order_id}"
                                                                                            data-user="{$client->id}"
                                                                                            data-id="{$file->id}"
                                                                                            type="button">
                                                                                            <i class="fas fa-times-circle"></i>
                                                                                            <span>Отклонить</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="p-1 pt-3 dropdown-item">
                                                                                        <button
                                                                                            class="btn btn-sm btn-block btn-danger js-image-remove js-event-add-click"
                                                                                            data-event="53"
                                                                                            data-manager="{$manager->id}"
                                                                                            data-order="{$order->order_id}"
                                                                                            data-user="{$client->id}"
                                                                                            data-id="{$file->id}"
                                                                                            type="button">
                                                                                            <i class="fas fa-trash"></i>
                                                                                            <span>Удалить</span>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        {/if}
                                                                    </div>
                                                                    <button data-file-id="{$file->id}" class="delete-photo btn btn-danger">Удалить</button>
                                                                </li>
                                                                <select class="form-control photo_status"
                                                                        data-file="{$file->id}"
                                                                        name="photo_status">
                                                                    <option value="1"
                                                                            {if $file->type == 'document'}selected{/if}>
                                                                        Выберите тип документа
                                                                    </option>
                                                                    <option value="2"
                                                                            {if $file->type == 'Паспорт: разворот'}selected{/if}>
                                                                        Паспорт: разворот
                                                                    </option>
                                                                    <option value="3"
                                                                            {if $file->type == 'Паспорт: регистрация'}selected{/if}>
                                                                        Паспорт: регистрация
                                                                    </option>
                                                                    <option value="4"
                                                                            {if $file->type == 'Селфи с паспортом'}selected{/if}>
                                                                        Селфи с паспортом
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        {/foreach}
                                                    </ul>
                                                    <div class="col-md-12">
                                                        <div class="form_file_item">
                                                            <input type="file" name="new_file" class="new_file"
                                                                   id="new_file"
                                                                   data-user="{$client->id}" value=""
                                                                   style="display:none"/>
                                                            <label for="new_file" class="btn btn-large btn-primary">
                                                                <i class="fa fa-plus-circle"></i>
                                                                <span>Добавить фото</span>
                                                            </label>
                                                        </div>
                                                    </div>
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
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">
                                                                Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        {else}
                                            <form action="{url}"  class="border js-order-item-form mb-3"
                                                  id="images_form">

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
                                                                <a class="image-popup-fit-width"
                                                                   href="javascript:void(0)"
                                                                   onclick="window.open('{$config->back_url}/files/users/{$client->id}/{$file->name}')">
                                                                    <div class="ribbon ribbon-corner {$ribbon_class}"><i
                                                                            class="{$ribbon_icon}"></i></div>
                                                                    <img src="{$config->back_url}/files/users/{$client->id}{$file->name}"
                                                                         alt="" class="img-responsive" style=""/>
                                                                </a>
                                                                <div class="order-image-actions">
                                                                    <div class="dropdown mr-1 show ">
                                                                        <button type="button"
                                                                                class="btn {if $file->status==2}btn-success{elseif $file->status==3}btn-danger{else}btn-secondary{/if} dropdown-toggle"
                                                                                id="dropdownMenuOffset"
                                                                                data-toggle="dropdown"
                                                                                aria-haspopup="true"
                                                                                aria-expanded="true">
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
                                                                                        data-id="{$file->id}"
                                                                                        type="button">
                                                                                    <i class="fas fa-check-circle"></i>
                                                                                    <span>Принят</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="p-1 dropdown-item">
                                                                                <button class="btn btn-sm btn-block btn-danger js-image-reject"
                                                                                        data-id="{$file->id}"
                                                                                        type="button">
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
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">
                                                                Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        {/if}
                                        <!-- Дополнительная информация -->
                                        <form action="{url}"
                                              class="border js-order-item-form mb-3" id="work_data_form">

                                            <input type="hidden" name="action" value="work"/>
                                            <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                            <input type="hidden" name="user_id" value="{$client->id}"/>

                                            <h6 class="card-header">
                                                <span class="text-white">Дополнительная информация</span>
                                                <span class="float-right"></span>
                                            </h6>

                                            <div class="row m-0 pt-2 view-block {if $work_error}hide{/if}">
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-0 row">
                                                        <label class="control-label col-md-3">Состоит ли в
                                                            браке:</label>
                                                        <div class="col-md-6">
                                                            <p class="form-control-static">
                                                                {if $client->sex == 0}
                                                                    Не состоит
                                                                {elseif $client->sex == 1}
                                                                    Состоит
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $client->sex == 1}
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-6">ФИО
                                                                супруга(-и):</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$client->fio_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Телефон
                                                                супруга(-и):</label>
                                                            <div class="col-md-5">
                                                                <p class="form-control-static">
                                                                    {$client->phone_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                {if $client->prev_fio != null || $client->prev_fio}
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-6">Предыдущие
                                                                ФИО:</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$client->prev_fio}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Дата смены
                                                                ФИО:</label>
                                                            <div class="col-md-5">
                                                                <p class="form-control-static">
                                                                    {$client->fio_change_date|date}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли
                                                            иностранным публичным должностным лицом:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if in_array($client->foreign_flag, [0,1])}
                                                                    Не является
                                                                {elseif $client->foreign_flag == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли
                                                            супругом(-ой) иностранного публичного должностного
                                                            лица:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if in_array($client->foreign_husb_wife, [0,1])}
                                                                    Не является
                                                                {elseif $client->foreign_husb_wife == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $client->foreign_husb_wife == 2}
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-2 row">
                                                            <label class="control-label col-md-8">ФИО
                                                                супруга(-и):</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$client->fio_public_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли близким
                                                            родственником иностранного публичного должностного
                                                            лица:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if in_array($client->foreign_relative, [0,1])}
                                                                    Не является
                                                                {elseif $client->foreign_relative == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $client->foreign_relative == 2}
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-2 row">
                                                            <label class="control-label col-md-8">ФИО
                                                                родственника:</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$client->fio_relative}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
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
                                            </h6>

                                            <div class="row m-0 pt-2 view-block">
                                                <div class="col-md-12">
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
                                                                    <option value="none" selected>По умолчанию
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
                                                                 data-user="{$client->id}"
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
                                        <div class="mb-3 border">
                                            <h5 class=" card-header">
                                                <span class="text-white ">Скоринги</span>
                                                {if $manager->role != 'employer'}
                                                    <a class="text-white float-right js-run-scorings"
                                                       data-type="all" data-order="{$client->order_id}"
                                                       href="javascript:void(0);">
                                                        <i class="far fa-play-circle"></i>
                                                    </a>
                                                {/if}
                                            </h5>
                                            <div class="message-box">

                                                {foreach $scoring_types as $scoring_type}
                                                    {if $manager->role == 'employer'}
                                                        {if $scoring_type->name != 'employer'}
                                                            {continue}
                                                        {/if}
                                                    {/if}
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
                                                                    <span class="mail-desc"
                                                                          title="{$scorings[$scoring_type->name]->string_result}">{$scorings[$scoring_type->name]->string_result}</span>
                                                                <span class="time">
                                                                    {$scorings[$scoring_type->name]->created|date} {$scoring->created|time}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <div class="mb-3 border">
                                            <h6 class="card-header text-white">
                                                <span>ИНН</span>
                                                {if in_array($manager->role, ['admin', 'middle'])}
                                                <span class="float-right">
                                                    <a href="javascript:void(0);" class="text-white js-open-user-data-form" data-type="inn">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </span>
                                                {/if}
                                            </h6>
                                            <div class="row view-block p-2 inn-front">
                                                <div class="col-md-12 show_view_block_inn">
                                                    <div class="form-group mb-0 row">
                                                        <label class="control-label col-md-8 col-7 inn-number">{$client->inn}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4 border">
                                            <h6 class="card-header text-white">
                                                <span>СНИЛС</span>
                                                {if in_array($manager->role, ['admin', 'middle'])}
                                                <span class="float-right">
                                                    <a href="javascript:void(0);" class="text-white js-open-user-data-form" data-type="snils">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </span>
                                                {/if}
                                            </h6>
                                            <div class="row view-block p-2 snils-front">
                                                <div class="col-md-12 show_view_block_snils">
                                                    <div class="form-group mb-0 row">
                                                        <label class="control-label col-md-8 col-7 snils-number">{$client->snils}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 border">
                                            <h6 class="card-header text-white">
                                                <span>ПДН</span>
                                            </h6>
                                            <div class="row view-block p-2 snils-front">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 row">
                                                        <label class="control-label col-md-8 col-7 snils-number">{$client->pdn}% {if $client->pdn_time != null} <span>({date('d.m.Y', $client->pdn_time)})</span> {/if}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 border">
                                            <h6 class="card-header text-white">
                                                <span>Общий баланс</span>
                                            </h6>
                                            <div class="row view-block p-2 snils-front">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 row">
                                                        <label class="control-label col-md-8 col-7 snils-number">{$balances}
                                                            рублей</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 border js-order-item-form">
                                            <h6 class="card-header text-white">
                                                <span>Расчетный счет</span>
                                            </h6>
                                            {if $same_holder == 0}
                                                <input type="hidden" name="action" value="cors_change"/>
                                                <input type="hidden" name="requisite[id]"
                                                       value="{$client->requisite->id}"/>
                                                <div class="cors-front">
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">ФИО
                                                                    держателя
                                                                    счета:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 fio-hold-front">{$client->requisite->holder}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Номер
                                                                    счета:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 acc-num-front">{$client->requisite->number}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Наименование
                                                                    банка:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 bank-name-front">{$client->requisite->name}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">БИК:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 bik-front-name">{$client->requisite->bik}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Кор
                                                                    счет:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 cor-account">{$client->requisite->correspondent_acc}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $manager->role != 'employer'}
                                                        <div class="row view-block p-2">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-0 row">
                                                                    <label class="control-label col-md-8 col-7">Перечислить
                                                                        из:</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-0 row">
                                                                    <label
                                                                            class="control-label col-md-12 bik-front">{$settlement->name}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>
                                            {else}
                                                <div class="row view-block p-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7">Перечисление
                                                                денежных средств на р/с счет третьего лица</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <!-- -->
                                {*
                                <div style="display: flex; justify-content: right">
                                    <div type="button" class="btn btn-outline-danger delete_client"
                                         data-user="{$client->id}" style="height: 38px;">
                                        Удалить клиента
                                    </div>
                                </div>
                                *}
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
                            {if $sort_docs}
                                <table class="table">
                                    {foreach $sort_docs as $order_id => $array}
                                        {foreach $array as $uid => $documents}
                                            <tr>
                                                <td class="text-info"><h5>Заявка номер
                                                        <a target="_blank"
                                                           href="{$config->root_url}/offline_order/{$order_id}">{$uid}</a>
                                                    </h5></td>
                                            </tr>
                                            {foreach $documents as $document}
                                                <tr>
                                                    <td class="text-info">
                                                        <a target="_blank"
                                                           href="{$config->root_url}/document?id={$document->id}&action=download_file">
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
                                        {/foreach}
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
                                                            <tr>
                                                                <td>{$order->date|date} {$order->date|time}</td>
                                                                <td>
                                                                    <a href="{if $order->offline == 1}offline_order{else}order{/if}/{$order->order_id}"
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

<div class="modal fade" id="editClientDataModal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loan_operations_title">Редактирование данных</h5>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row pt-2 show_edit_block_passport" style="padding: 20px;display: none;">
                    <span id="passportForm" style="display: flex; flex-wrap: wrap;">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Паспорт:</label>
                                <input type="text" name="passport_serial" value="{$client->passport_serial|escape}" class="form-control passport_serial" required="true"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Код подразделения:</label>
                                <input type="text" name="subdivision_code" value="{$client->subdivision_code|escape}" class="form-control subdivision_code" required="true"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Дата выдачи:</label>
                                <input type="text" name="passport_date" value="{$client->passport_date|date}" class="form-control daterange" required="true"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Кем выдано:</label>
                                <input type="text" name="passport_issued" value="{$client->passport_issued|escape}" class="form-control passport_issued" required="true"/>
                            </div>
                        </div>
                    </span>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="editpassportCommentValue">Комментарий</label>
                            <textarea id="editpassportCommentValue" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-xs js-save-user-data-form" data-type="passport">Отправить</a>
                        </span>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-xs js-close-user-data-form" data-type="passport">Отмена</a>
                        </span>
                    </div>
                </div>
                <div class="row pt-2 show_edit_block_addresses" style="padding: 20px; display: none;">
                    <span id="addressesForm" style="display: flex; flex-wrap: wrap;">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Адрес регистрации:</label>
                                <input type="hidden" class="reg_address_json" name="reg_address_json" value="">
                                <input type="text" name="reg_address" value="{$client->regaddress->adressfull}" class="findRegAddress form-control" required="true"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label class="control-label">Адрес проживания:</label>
                                <input type="hidden" class="fakt_address_json" name="fakt_address_json" value="">
                                <input type="text" name="fakt_address" value="{$client->faktaddress->adressfull}" class="findFaktAddress form-control" required="true"/>
                            </div>
                        </div>
                    </span>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="editaddressesCommentValue">Комментарий</label>
                            <textarea id="editaddressesCommentValue" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-xs js-save-user-data-form" data-type="addresses">Отправить</a>
                        </span>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-xs js-close-user-data-form" data-type="addresses">Отмена</a>
                        </span>
                    </div>
                </div>
                <div class="row pt-2 show_edit_block_inn" style="display: none;padding: 0 30px;">
                    <div class="form-group mb-0 row">
                        <label for="editinnValue">ИНН</label>
                        <input type="text" class="form-control" style="margin-bottom:10px;" id="editinnValue" value="{$client->inn}">
                        <label for="editinnCommentValue">Комментарий</label>
                        <textarea id="editinnCommentValue" style="margin-bottom:10px;" class="form-control"></textarea>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-xs js-save-user-data-form" data-type="inn">Отправить</a>
                        </span>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-xs js-close-user-data-form" data-type="inn">Отмена</a>
                        </span>
                    </div>
                </div>
                <div class="row pt-2 show_edit_block_snils" style="display: none;padding: 0 30px;">
                    <div class="form-group mb-0 row">
                        <label for="editsnilsValue">СНИЛС</label>
                        <input type="text" class="form-control" style="margin-bottom:10px;" id="editsnilsValue" value="{$client->snils}">
                        <label for="editsnilsCommentValue">Комментарий</label>
                        <textarea id="editsnilsCommentValue" style="margin-bottom:10px;" class="form-control"></textarea>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-xs js-save-user-data-form" data-type="snils">Отправить</a>
                        </span>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-xs js-close-user-data-form" data-type="snils">Отмена</a>
                        </span>
                    </div>
                </div>
                <div class="row pt-2 show_edit_block_numbers" style="display: none;padding: 0 30px;">
                    <div class="col-md-12">
                        <div class="form-row" style="margin-bottom: 10px;">
                            <div class="col-3">
                                <label class="control-label">
                                    Телефон
                                    {if $client->phone_mobile_confirmed == 1}
                                        <span>(подтвержден)</span>
                                    {/if}
                                    :
                                </label>
                            </div>
                            <div class="col-4" style="position: relative">
                                <input class="form-control phone_num"
                                       type="text"
                                       name="phone"
                                       placeholder="+7(900)000-00-00"
                                       value="{$client->phone_mobile}"
                                       autocomplete="off"/>
                                <div class="phone_send_code badge badge-danger"
                                     style="position: absolute; margin-left: 265px; margin-top: 5px;right: 8px;display: none">
                                </div>
                                <input type="hidden" name="phone_confirmed"
                                       class="phone_confirmed" value="false"/>
                            </div>
                            <div class="col-2">
                                <input type="button"
                                       data-user="{$client->id}"
                                       class="btn btn-success accept_edit"
                                       value="Сохранить">
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: 10px;">
                            <div class="col-3"></div>
                            <div class="col-4">
                                <div class="input-group show_phone_code"
                                     style="display: none">
                                    <input type="text" class="form-control phone_code"
                                           placeholder="Введите код из смс">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary accept_edit_with_code"
                                                type="button" data-user="{$client->id}">
                                            Подтвердить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-row" style="margin-bottom: 10px;">
                            <div class="col-3">
                                <label class="control-label">
                                    Электронная почта
                                    {if $client->email_confirmed == 1}
                                        <span>(подтверждена)</span>
                                    {/if}
                                </label>
                            </div>
                            <div class="col-4">
                                <div style="display: flex">
                                    <input class="form-control casing-upper-mask email"
                                           type="text" name="email"
                                           placeholder="ivanov@mail.ru"
                                           value="{$client->email}" autocomplete="off"/>
                                    <div class="email_code_show badge badge-danger"
                                         style="position: absolute; margin-left: 265px; margin-top: 5px; display: none"></div>
                                    <input type="hidden" name="email_confirmed"
                                           class="email_confirmed" value="false"/>
                                </div>
                            </div>
                            <div class="col-2">
                                <input type="button"
                                       data-user="{$client->id}"
                                       class="btn btn-success accept_email_edit"
                                       value="Сохранить">
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: 10px;">
                            <div class="col-3"></div>
                            <div class="col-4">
                                <div class="input-group show_email_code"
                                     style="display: none">
                                    <input type="text" class="form-control email_code"
                                           placeholder="Код из письма">
                                    <div class="input-group-append">
                                        <button
                                            class="btn btn-primary accept_edit_email_with_code"
                                            type="button" data-user="{$client->user_id}">
                                            Подтвердить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4>Мессенджеры для связи</h4><br>
                        <div style="width: 100%; display: flex; flex-direction: column">
                            <div>
                                <img class="icon_messag"
                                     src="https://img.icons8.com/ios-glyphs/344/viber.png" width="30"
                                     height="30">
                                {if isset($viber_confirmed)}
                                    <small class="badge badge-success">
                                        Привязан
                                    </small>
                                {/if}
                                <input class="form-control phone_num viber_same"
                                       style="width: 180px; margin-left: 10px; {if isset($order) && $order->viber_num == $order->phone_mobile}display: none{/if}"
                                       type="text" name="viber" value="{$client->viber_num}"
                                       autocomplete="off">
                                <label style="margin-left: 25px; display: none"
                                       class="label label-success viber_confirmed">Cсылка для привязки
                                    отправлена</label>
                                <input style="margin-left: 20px" type="checkbox" class="custom-checkbox"
                                       name="viber_same"
                                       {if $client->viber_num == $client->phone_mobile}checked{/if}
                                       value="1">
                                <label class="viber_same_label">Совпадает с номером мобильного</label>
                                <div style="margin-left: 20px" class="btn btn-success confirm_viber"
                                     data-user="{$client->user_id}">Подтвердить
                                </div>
                                <br><br>
                            </div>
                            <div>
                                <img class="icon_messag"
                                     src="https://img.icons8.com/color/344/telegram-app--v1.png"
                                     width="30"
                                     height="30">
                                {if isset($telegram_confirmed)}
                                    <small class="badge badge-success">
                                        Привязан
                                    </small>
                                {/if}
                                <input class="form-control phone_num telegram_same"
                                       style="width: 180px; margin-left: 10px; {if isset($order) && $order->telegram_num == $order->phone_mobile}display: none{/if}"
                                       type="text" name="telegram" value="{$client->telegram_num}"
                                       autocomplete="off">
                                <label style="margin-left: 25px; display: none"
                                       class="label label-success telegram_confirmed">Cсылка для
                                    привязки отправлена</label>
                                <input style="margin-left: 20px" type="checkbox" class="custom-checkbox"
                                       name="telegram_same"
                                       {if $client->telegram_num == $client->phone_mobile}checked{/if}
                                       value="1">
                                <label class="telegram_same_label">Совпадает с номером
                                    мобильного</label>
                                <div style="margin-left: 20px" class="btn btn-success confirm_telegram"
                                     data-user="{$client->user_id}">
                                    Подтвердить
                                </div>
                                <br><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-xs js-close-user-data-form" data-type="numbers">Готово</a>
                        </span>
                        <span>
                            <a href="javascript:void(0);" class="btn btn-outline-primary btn-xs js-close-user-data-form" data-type="numbers">Отмена</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
