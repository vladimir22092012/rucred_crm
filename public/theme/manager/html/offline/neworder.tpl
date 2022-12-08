{$meta_title="Новая заявка" scope=parent}

{capture name='page_scripts'}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/popper.min.js?v=1.02"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/bootstrap.js?v=1.01"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js?v=1.01"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <!--Menu sidebar -->
    <script src="theme/{$settings->theme|escape}/js/sidebarmenu.js?v=1.01"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="theme/{$settings->theme|escape}/js/jquery.slimscroll.js?v=1.01"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/neworder_new.js?v=1.07"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(function () {

            $('.show_phone_code').hide();
            $('.show_phone_confirmed').hide();

            $('.accept_edit').click(function (e) {
                e.preventDefault();

                let phone = $('input[class="form-control phone_num"]').val();
                let user_id = $(this).attr('data-user');

                $.ajax({
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
                    method: 'POST',
                    data: {
                        action: 'edit_phone_with_code',
                        phone: phone,
                        code: phone_code,
                    },
                    success: function (response) {
                        if (response.error === 1) {
                            Swal.fire({
                                title: response.reason,
                                confirmButtonText: 'ОК'
                            });
                        } else {
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

            let order = {{json_encode($order)}};
            if (order) {

                if (order['prev_fio'] != null) {
                    $('input[id="change_fio2"]').trigger('click');
                } else {
                    $('input[id="change_fio1"]').trigger('click');
                }

                $('input[id="sex' + order['sex'] + '"]').trigger('click');

                if (order['push_not'] == 1)
                    $('input[name="push_not"]').prop('checked', true);
                else
                    $('input[name="push_not"]').prop('checked', false);

                if (order['sms_not'] == 1)
                    $('input[name="sms_not"]').prop('checked', true);
                else
                    $('input[name="sms_not"]').prop('checked', false);

                if (order['email_not'] == 1)
                    $('input[name="email_not"]').prop('checked', true);
                else
                    $('input[name="email_not"]').prop('checked', false);

                if (order['massanger_not'] == 1)
                    $('input[name="massanger_not"]').prop('checked', true);
                else
                    $('input[name="massanger_not"]').prop('checked', false);

                $('input[id="foreign' + order['foreign_flag'] + '"]').trigger('click');
                $('input[id="foreign_husb_wife' + order['foreign_husb_wife'] + '"]').trigger('click');
                $('input[id="foreign_relative' + order['foreign_relative'] + '"]').trigger('click');

                $('select[name="group"] option[value="' + order['group_id'] + '"]').prop('selected', true);
                $('select[name="group"] option[value="' + order['group_id'] + '"]').change();

                setTimeout(function () {
                    $('select[name="company"] option[value="' + order['company_id'] + '"]').prop('selected', true);
                    $('select[name="company"] option[value="' + order['company_id'] + '"]').change();
                }, 500);

                setTimeout(function () {
                    $('#' + order['loan_type'] + '').trigger('click');

                    if (order['profunion'] == 2) {
                        $('input[id="profunion2"]').trigger('click');
                        $('input[id="want_profunion"]').trigger('click');
                    }
                    if (order['profunion'] == 0) {
                        $('input[id="profunion2"]').trigger('click');
                    }
                    if (order['profunion'] == 1) {
                        $('input[id="profunion1"]').trigger('click');
                    }

                    $('.to_form_loan').trigger('click');

                    if (order['branche_id'].length < 2)
                        $('select[name="branch"] option[value="4"]').prop('selected', true);
                    else
                        $('select[name="branch"] option[value="' + order['branche_id'] + '"]').prop('selected', true);

                }, 900);
            }

            $('.create_new_order, .create_new_draft').click(function (e) {
                e.preventDefault();

                let form = $('#forms').serialize() + '&action=create_new_order';

                if ($(this).hasClass('create_new_draft')) {
                    form = form + '&draft=1';
                }

                $.ajax({
                    method: 'POST',
                    data: form,
                    success: function (response) {
                        if (response.error === 1) {
                            Swal.fire({
                                title: response.reason,
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            window.location.replace(response.redirect);
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

            $(document).on('input', '.fio, .bank_validate', function () {
                let val = $(this).val().toUpperCase();
                val = val.replace(new RegExp(/[^а-яА-Я\s-]+$/, 'g'), '');
                $(this).val(val);
            });

            $('select[name="settlement"]').on('change', function () {

                if ($(this).val() == 2) {
                    $('select[name="payout_type"]').show();

                    if (order['card_id']) {
                        $('.requisits').hide();
                        $('.cardes').show();
                    }
                    if (order['requisite_id']) {
                        $('.cardes').hide();
                        $('.requisits').show();
                    }
                } else {
                    $('select[name="payout_type"]').hide();
                    $('.cardes').hide();
                    $('.requisits').show();
                }
            });

            $('.delete_draft').on('click', function () {
                let orderId = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'delete_draft',
                        orderId: orderId
                    },
                    success: function () {
                        location.replace('/drafts')
                    }
                })
            });
        });
    </script>
{/capture}

{capture name='page_styles'}
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet"/>
    <style>
        .price {
            width: 100%;
        }

        .price_container {
            padding-top: 20px;
            float: left;
        }

        .price_basic:hover {
            background: #b3eeff;
        }

        .price_basic {
            transition: 1s;
            display: flex;
            flex-direction: column;
            background-color: white;
            border: 1px solid #b0b0b0;
            border-radius: 15px;
            height: 250px;
            width: 250px;
            align-items: center;
            text-align: center;
        }

        .loantype_settings {
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        label {
            margin-left: 30px;
        }

        .suggestions-suggestions {
            position: static !important;
        }

    </style>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
{/capture}

<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">

        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Новая заявка</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="orders/offline">Заявки оффлайн</a></li>
                    <li class="breadcrumb-item active">Новая заявка</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">

            </div>
        </div>

        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="mb-0 text-white float-left">Заявка {$order->order_id}</h4>
                        <small
                            class="text-white float-right">{$offline_points[$manager->offline_point_id]->address}</small>
                    </div>
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row" style="width: 100%">
                                <div class="price_info" style="width: 100%">
                                    <h2>Off-line заявка</h2>
                                </div>
                                <br><br>
                                <hr style="width: 100%; size: 5px">
                                <form method="POST" id="forms" style="width: 100%">
                                    <h3>Работодатель</h3><br>
                                    <div style="width: 100%; margin-left: 25px">
                                        <select style="width: 500px" class="form-control groups"
                                                name="group">
                                            <option value="none" selected>Выберите из списка</option>
                                            {if !empty($groups)}
                                                {foreach $groups as $group}
                                                    <option value="{$group->id}">{$group->name}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                        <select style="width: 500px; margin-left:10px; display: none;"
                                                class="form-control my_company"
                                                name="company">
                                            <option value="none" selected>Выберите из списка</option>
                                        </select>
                                        <select style="width: 300px; margin-left:10px; display: none;"
                                                class="form-control branches"
                                                name="branch">
                                            <option value="none" selected>Выберите из списка</option>
                                        </select>
                                    </div>
                                    <hr style="width: 100%; size: 5px">
                                    <br>
                                    <h3>Членство в профсоюзе</h3><br>
                                    <div style="width: 100%; margin: 30px 30px">
                                        <div style="display: flex;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="profunion"
                                                       id="profunion1" value="1">
                                                <label class="form-check-label" for="profunion1">
                                                    Я член профсоюза
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="radio" name="profunion"
                                                       id="profunion2" value="0" checked>
                                                <label class="form-check-label" for="profunion2">
                                                    Я не являюсь членом профсоюза
                                                </label>
                                            </div>
                                            {*
                                            <div class="form-check" style="margin-left: 25px"
                                                 id="profunion_toggle">
                                                <input class="form-check-input want_profunion" type="checkbox"
                                                       value="1"
                                                       id="want_profunion" name="want_profunion">
                                                <label class="form-check-label" for="want_profunion">
                                                    Оповестить о желании вступления в профсоюз
                                                </label>
                                            </div>
                                            *}
                                        </div>
                                    </div>
                                    <br>
                                    <hr style="width: 100%; size: 5px">
                                    <br>
                                    <div class="price" id="pricelist">

                                    </div>

                                    <hr style="width: 100%; size: 5px">
                                    <div class="loantype_settings" style="width: 100%; display: none">
                                        <div>
                                            <label style="font-size: 12px">Сумма:</label>
                                            <input id="order_sum" name="amount" class="form-control mask_number"
                                                   style="width: 100px" value="{$order->amount}" required>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px">Желаемая дата выдачи займа:</label>
                                            <input type="text" style="width: 130px" id="start_date"
                                                   name="start_date"
                                                   class="form-control daterange"
                                                   value="{$order->probably_start_date|date}">
                                        </div>
                                        <div id="calendar">
                                            <label style="font-size: 12px">Возврат до:</label>
                                            <input type="text" style="margin-left: 10px; width: 130px" id="end_date"
                                                   name="end_date"
                                                   class="form-control daterange"
                                                   value="{$order->probably_return_date|date}" readonly>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px" id="return_sum">Сумма возврата:</label>
                                            <label id="month_sum" style="display: none; font-size: 12px">Ежемесячный
                                                платеж:</label>
                                            <input class="form-control" name="probably_return_sum"
                                                   id="final_sum"
                                                   style="margin-left: 10px; width: 100px" required
                                                   value="{$order->probably_return_sum}" readonly>
                                        </div>
                                        <div>
                                            <input type="button" class="btn btn-outline-info to_form_loan"
                                                   value="Сформировать">
                                        </div>
                                        <input id="loan_percents" name="percent" class="form-control"
                                               style="margin-left: 10px; width: 80px; display: none">
                                        <input type="hidden" id="max_period" name="max_period" class="form-control"
                                               style="margin-left: 10px; width: 80px">
                                    </div>
                                    <br>
                                    <div class="alert alert-danger"
                                         style="display: none; width: 25%; margin-left: 30px">
                                    </div>
                                    <hr style="width: 100%; size: 5px">
                                    <div style="width: 100%; margin: 30px 30px">
                                        <h3>ФИО</h3><br>
                                        <div style="width: 100%">
                                            <input style="width: 350px; margin-left: 25px" type="text" name="lastname"
                                                   class="form-control js-lastname-input fio"
                                                   placeholder="Фамилия" required value="{$order->lastname}"/>
                                            <input style="width: 350px; margin-left: 25px" type="text" name="firstname"
                                                   class="form-control js-firstname-input fio"
                                                   placeholder="Имя" required value="{$order->firstname}"/>
                                            <input class="form-control js-patronymic-input fio"
                                                   style="width: 350px; margin-left: 25px"
                                                   name="patronymic"
                                                   placeholder="Отчество(если есть)" type="text"
                                                   value="{$order->patronymic}">
                                        </div>
                                        <br><br>
                                        <div style="width: 100%">
                                            <label class="control-label">Место рождения</label>
                                            <label class="control-label" style="margin-left: 240px">Дата
                                                рождения</label><br>
                                            <input class="form-control casing-upper-mask"
                                                   style="width: 350px; margin-left: 25px"
                                                   type="text" name="birth_place" value="{$order->birth_place}"/>
                                            <input type="text" style="width: 180px; margin-left: 25px"
                                                   name="birth"
                                                   class="form-control daterange" value="{$order->birth|date}">
                                        </div>
                                        <br>
                                        <div class="phone_edit_form" style="width: 100%">
                                            <label class="control-label">Телефон{if $order->phone_mobile_confirmed == 1}
                                                <span>(подтвержден)</span>{/if}</label>
                                            <div class="form-row">
                                                <div class="col" style="position: relative">
                                                    <input style="width: calc(100% - 45px); margin-left: 25px"
                                                           class="form-control phone_num"
                                                           type="text"
                                                           name="phone"
                                                           placeholder="+7(900)000-00-00"
                                                           value="{$order->phone_mobile}"
                                                           autocomplete="off"/>
                                                    <div class="phone_send_code badge badge-danger"
                                                         style="position: absolute; margin-left: 350px; margin-top: 5px;right: 30px;display: none">
                                                    </div>
                                                    <input type="hidden" name="phone_confirmed"
                                                           class="phone_confirmed" value="false"/>
                                                </div>
                                                {if $order->phone_mobile_confirmed == 0}
                                                    <div class="col">
                                                        <input type="button"
                                                               data-user="{$user->id}"
                                                               class="btn btn-success accept_edit"
                                                               value="Сохранить">
                                                    </div>
                                                {/if}
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
                                        <br>
                                        {if $order->original != 1}
                                            <div type="button" style="margin-left: 25px"
                                                 class="btn btn-outline-info check_users">
                                                Проверить совпадения
                                            </div>
                                        {/if}
                                        <br><br>
                                        <div style="display: none" id="users_same"></div>
                                        <br><br>
                                        <h4>Менялись ли ваши фамилия, имя, отчество?</h4><br>
                                        <div class="form-check" style="margin-left: 25px">
                                            <input class="form-check-input" type="radio" name="change_fio"
                                                   id="change_fio1" value="0" checked>
                                            <label class="form-check-label" for="change_fio1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 25px">
                                            <input class="form-check-input" type="radio" name="change_fio"
                                                   id="change_fio2" value="1">
                                            <label class="form-check-label" for="change_fio2">
                                                Да(Укажите дополнительно)
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display:none" id="change_fio_toggle">
                                            <label class="control-label">Предыдущие ФИО</label>
                                            <label class="control-label" style="margin-left: 230px">Дата
                                                изменения</label><br>
                                            <input class="form-control prev_fio fio"
                                                   style="width: 350px; margin-left: 25px"
                                                   type="text" name="prev_fio" value="{$order->prev_fio}"
                                            />
                                            <input type="text" style="width: 180px; margin-left: 25px"
                                                   name="fio_change_date"
                                                   class="form-control daterange fio_change_date"
                                                   value="{$order->fio_change_date|date}">
                                        </div>

                                        <hr style="width: 100%; size: 5px">

                                        <div style="width: 100%">
                                            <h3>Основные данные</h3><br>
                                            <div style="width: 100%">
                                                <label class="control-label">Паспорт</label>
                                                <label class="control-label" style="margin-left: 185px">Код
                                                    подразделения</label><br>
                                                <input class="form-control passport_serial"
                                                       style="width: 100px; margin-left: 25px"
                                                       type="text" name="passport_serial"
                                                       value="{$order->passport_serial}"/>
                                                <input class="form-control passport_number"
                                                       style="width: 100px; margin-left: 25px"
                                                       type="text" name="passport_number"
                                                       value="{$order->passport_number}"/>
                                                <input class="form-control subdivision_code"
                                                       style="width: 180px; margin-left: 25px"
                                                       type="text" name="subdivision_code"
                                                       value="{$order->subdivision_code}"/>
                                            </div>
                                            <br>
                                            <div style="width: 100%">
                                                <label class="control-label">Кем выдан</label>
                                                <label class="control-label" style="margin-left: 440px">Когда
                                                    выдан</label><br>
                                                <input readonly class="form-control passport_issued"
                                                       style="width: 500px; margin-left: 25px"
                                                       type="text" name="passport_issued"
                                                       value="{$order->passport_issued}"/>
                                                <input type="text" style="width: 180px; margin-left: 25px"
                                                       id="passport_date"
                                                       name="passport_date"
                                                       class="form-control daterange"
                                                       value="{$order->passport_date|date}"/>
                                            </div>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">СНИЛС</label>
                                            <label class="control-label" style="margin-left: 170px">ИНН</label><br>
                                            <input class="form-control snils" style="width: 200px; margin-left: 25px"
                                                   type="text" name="snils" value="{$order->snils}"/>
                                            <input class="form-control inn" style="width: 200px; margin-left: 25px"
                                                   type="text" name="inn" value="{$order->inn}"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Адрес регистрации</label><br>
                                            <input class="form-control casing-upper-mask Regadress" name="Regadressfull"
                                                   style="width: 700px; margin-left: 25px" type="text"
                                                   {if !empty($Regaddressfull)}value="{$Regaddressfull->adressfull}"{/if}/>
                                            <input style="display: none" class="Registration" name="Regadress"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Место жительства</label><br>
                                            <div class="custom-checkbox">
                                                <input type="checkbox" style="margin-left: 30px" name="actual_address"
                                                       value="1" {if $order->actual_address == 1}checked{/if}/>
                                                <label class="" for="actual_address">Совпадает с адресом
                                                    регистрации</label>
                                            </div>
                                            <input class="form-control casing-upper-mask Faktaddress"
                                                   id="actual_address_toggle"
                                                   style="width: 700px; margin-left: 25px; {if $order->actual_address == 1} display:none; {/if}"
                                                   name="Faktadressfull"
                                                   {if !empty($Faktaddressfull)}value="{$Faktaddressfull->adressfull}"{/if}
                                                   type="text"/>
                                        </div>
                                        <br>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Часовой пояс</label><br>
                                            <select class="form-control"
                                                    name="timezone"
                                                    style="width: 200px; margin-left: 25px">
                                                {for $i= -1 to 9}
                                                    <option value="{$i}"
                                                            {if empty($order->timezone) && $i == 0}selected{elseif $i == $order->timezone}selected{/if}>
                                                        МСК {if $i > 0}+ {/if} {if $i != 0}{$i}{/if}</option>
                                                {/for}
                                            </select>
                                        </div>
                                        <br>
                                        <h4>Семейное положение</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="sex"
                                                   id="sex1" value="1">
                                            <label class="form-check-label" for="sex1">
                                                Состою в браке
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="sex"
                                                   id="sex2" checked value="0">
                                            <label class="form-check-label" for="sex2">
                                                Не состою в браке
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none;" id="sex_toggle">
                                            <label class="control-label">ФИО Супруги(-а)</label><br>
                                            <div style="display: flex">
                                                <input class="form-control fio_spouse fio"
                                                       style="width: 350px; margin-left: 25px"
                                                       type="text" name="fio_spouse[lastname]" placeholder="Фамилия"
                                                       value="{$fio_spouse[0]}"/>
                                                <input class="form-control fio_spouse fio"
                                                       style="width: 350px; margin-left: 25px"
                                                       type="text" name="fio_spouse[firstname]" placeholder="Имя"
                                                       value="{$fio_spouse[1]}"/><br><br>
                                                <input class="form-control fio_spouse fio"
                                                       style="width: 350px; margin-left: 25px"
                                                       type="text" name="fio_spouse[patronymic]"
                                                       placeholder="Отчество(если есть)" value="{$fio_spouse[2]}"/>
                                            </div>
                                            <br>
                                            <div style="display: flex; flex-direction: column">
                                                <label class="control-label">Моб. телефон</label>
                                                <input class="form-control phone_num phone_spouse"
                                                       style="width: 200px; margin-left: 25px"
                                                       type="text" name="phone_spouse"
                                                       value="{$order->phone_spouse}" autocomplete="off"/>
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <h4>Контактные данные</h4><br>
                                        <div>
                                            <div class="col-12">
                                                <div class="phone_edit_form">
                                                    <div class="mb-3">
                                                        <label class="control-label">Электронная почта
                                                            {if $order->email_confirmed == 1}<span>(подтверждена)</span></label>{/if}
                                                        <div class="form-row">
                                                            <div style="display: flex">
                                                                <input style="width: 400px"
                                                                       class="form-control casing-upper-mask email"
                                                                       type="text" name="email"
                                                                       placeholder="ivanov@mail.ru"
                                                                       value="{$order->email}" autocomplete="off"/>
                                                                <div class="email_code_show badge badge-danger"
                                                                     style="position: absolute; margin-left: 350px; margin-top: 5px; display: none"></div>
                                                                <input type="hidden" name="email_confirmed"
                                                                       class="email_confirmed" value="false"/>
                                                            </div>
                                                            {if $order->email_confirmed == 0}
                                                                <div class="col">
                                                                    <input type="button"
                                                                           data-user="{$order->user_id}"
                                                                           class="btn btn-success accept_email_edit"
                                                                           value="Подтвердить">
                                                                </div>
                                                            {/if}
                                                            <div class="col-4">
                                                                <div class="input-group show_email_code"
                                                                     style="display: none">
                                                                    <input type="text" class="form-control email_code"
                                                                           placeholder="Код из письма">
                                                                    <div class="input-group-append">
                                                                        <button
                                                                            class="btn btn-primary accept_edit_email_with_code"
                                                                            type="button" data-user="{$order->user_id}">
                                                                            Подтвердить
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <h4>Мессенджер для связи</h4><br>
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
                                                       style="width: 450px; margin-left: 25px; {if isset($order) && $order->viber_num == $order->phone_mobile}display: none{/if}"
                                                       type="text" name="viber" value="{$order->viber_num}"
                                                       autocomplete="off">
                                                <label style="margin-left: 25px; display: none"
                                                       class="label label-success viber_confirmed">Cсылка для привязки
                                                    отправлена</label>
                                                <input style="margin-left: 20px" type="checkbox" class="custom-checkbox"
                                                       name="viber_same"
                                                       {if isset($order) && $order->viber_num == $order->phone_mobile}checked{/if}
                                                       value="1">
                                                <label class="viber_same_label">Совпадает с номером мобильного</label>
                                                <div style="margin-left: 20px" class="btn btn-success confirm_viber"
                                                     data-user="{$order->user_id}">Подтвердить
                                                </div>
                                                <br><br>
                                            </div>
                                            {*
                                            <div>
                                                <img class="icon_messag"
                                                     src="https://img.icons8.com/office/344/whatsapp--v1.png" width="30"
                                                     height="30">
                                                <input class="form-control phone_num whatsapp_same"
                                                       style="width: 450px; margin-left: 25px; {if isset($order) && $order->whatsapp_num == $order->phone_mobile}display: none{/if}"
                                                       type="text" name="whatsapp" value="{$order->whatsapp_num}"
                                                       autocomplete="off">
                                                <input style="margin-left: 20px" type="checkbox" class="custom-checkbox"
                                                       name="whatsapp_same"
                                                       {if isset($order) && $order->whatsapp_num == $order->phone_mobile}checked{/if}
                                                       value="1">
                                                <label>Совпадает с номером мобильного</label><br><br>
                                            </div>
                                            *}
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
                                                       style="width: 450px; margin-left: 25px; {if isset($order) && $order->telegram_num == $order->phone_mobile}display: none{/if}"
                                                       type="text" name="telegram" value="{$order->telegram_num}"
                                                       autocomplete="off">
                                                <label style="margin-left: 25px; display: none"
                                                       class="label label-success telegram_confirmed">Cсылка для
                                                    привязки отправлена</label>
                                                <input style="margin-left: 20px" type="checkbox" class="custom-checkbox"
                                                       name="telegram_same"
                                                       {if isset($order) && $order->telegram_num == $order->phone_mobile}checked{/if}
                                                       value="1">
                                                <label class="telegram_same_label">Совпадает с номером
                                                    мобильного</label>
                                                <div style="margin-left: 20px" class="btn btn-success confirm_telegram"
                                                     data-user="{$order->user_id}">
                                                    Подтвердить
                                                </div>
                                                <br><br>
                                            </div>
                                        </div>
                                        <br>
                                        <h4>Основные каналы связи</h4>
                                        <div class="form-check" style="display:flex; margin-left: 25px">
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" name="sms_not" value="1"
                                                       checked>
                                                <label class="form-check-label">
                                                    SMS-уведомления
                                                </label></div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" name="email_not"
                                                       value="1">
                                                <label class="form-check-label">
                                                    Электронная почта
                                                </label></div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" name="massanger_not"
                                                       value="1">
                                                <label class="form-check-label">
                                                    Мессенджеры
                                                </label></div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" name="push_not"
                                                       value="1">
                                                <label class="form-check-label">
                                                    Push-уведомления
                                                </label>
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Работодатель</h4><br>
                                        <div style="width: 100%; display: flex">
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Среднемесячный доход
                                                    руб.</label><br>
                                                <input class="form-control mask_number"
                                                       style="width: 300px; margin-left: 25px"
                                                       type="text" name="income_medium" value="{$order->income}">
                                            </div>
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Среднемесячные расходы
                                                    руб.</label><br>
                                                <input class="form-control mask_number" name="outcome_medium"
                                                       style="width: 300px; margin-left: 25px"
                                                       type="text" value="{$order->expenses}">
                                            </div>
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Количество
                                                    иждивенцев</label><br>
                                                <input class="form-control mask_number" name="dependents"
                                                       style="width: 300px; margin-left: 25px"
                                                       placeholder="необязательно" type="text"
                                                       value="{$order->dependents}">
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <div style="width: 100%; display: flex">
                                            <h4>
                                                Аттестация
                                            </h4>
                                            <div style="margin-left: 50px">
                                                <input class="form-check-input" id="no_attestation" type="checkbox"
                                                       name="no_attestation"
                                                       value="1" {if $order->attestation == null}checked{/if}>
                                                <label for="no_attestation">Нет аттестации</label>
                                            </div>
                                        </div>
                                        <table
                                            class="jsgrid-table table table-striped attestation_table" {if empty($order->attestation)} style="display: none" {/if}>
                                            <thead>
                                            <tr>
                                                <th>Дата окончания</th>
                                                <th>Комментарий</th>
                                                <th><input type="button"
                                                           class="btn btn-outline-success add_to_attestation_table"
                                                           value="+"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="attestation_table">
                                            {if !empty($order->attestation) && $order->attestation != null}
                                                {foreach json_decode($order->attestation) as $attestation}
                                                    <tr>
                                                        <td><input class="form-control daterange"
                                                                   name="date[][date]" type="text"
                                                                   value="{$attestation->date}"></td>
                                                        <td><input class="form-control"
                                                                   name="comment[][comment]" type="text"
                                                                   value="{$attestation->comment}"></td>
                                                        <td>
                                                            <div type="button"
                                                                 class="btn btn-outline-danger remove_from_attestation_table">
                                                                -
                                                            </div>
                                                        </td>
                                                    </tr>
                                                {/foreach}
                                            {else}
                                                <tr>
                                                    <td><input class="form-control daterange"
                                                               name="date[][date]" type="text"
                                                               value=""></td>
                                                    <td><input class="form-control"
                                                               name="comment[][comment]" type="text"
                                                               value=""></td>
                                                    <td></td>
                                                </tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы иностранным публичным должностным лицом?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign"
                                                   id="foreign1" value="0" checked>
                                            <label class="form-check-label" for="foreign1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign"
                                                   id="foreign2" value="2">
                                            <label class="form-check-label" for="foreign2">
                                                Да
                                            </label>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы супругом(-ой) иностранного публичного должностного
                                            лица?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_husb_wife"
                                                   id="foreign_husb_wife1" checked value="0">
                                            <label class="form-check-label" for="foreign_husb_wife1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_husb_wife"
                                                   id="foreign_husb_wife2" value="2">
                                            <label class="form-check-label" for="foreign_husb_wife2">
                                                Да
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none" id="foreign_husb_wife_toggle">
                                            <label class="control-label">ФИО Супруги(-а)</label><br>
                                            <input class="form-control fio_public_spouse fio" name="fio_public_spouse"
                                                   style="width: 500px; margin-left: 25px" type="text"
                                                   value="{$order->fio_public_spouse}"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы близким родственником иностранного публичного должностного
                                            лица?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_relative"
                                                   id="foreign_relative1" checked value="0">
                                            <label class="form-check-label" for="foreign_relative1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_relative"
                                                   id="foreign_relative2" value="2">
                                            <label class="form-check-label" for="foreign_relative2">
                                                Да
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none" id="foreign_relative_toggle">
                                            <label class="control-label">ФИО родственника</label><br>
                                            <input class="form-control fio_relative fio" name="fio_relative"
                                                   style="width: 500px; margin-left: 25px" type="text"
                                                   value="{$order->fio_relative}"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Перечислить микрозайм с банковского счета:</h4><br>
                                        <div style="width: 100%; display: flex">
                                            <select class="form-control" name="settlement"
                                                    style="width: 300px; margin-left: 25px">
                                                {foreach $settlements as $settlement}
                                                    <option value="{$settlement->id}"
                                                            {if $order->settlement_id == $settlement->id}selected{/if}>{$settlement->name}</option>
                                                {/foreach}
                                            </select>
                                            <select class="form-control" name="payout_type"
                                                    style="width: 300px; margin-left: 25px; {if !empty($order->settlement_id) && $order->settlement_id ==3}display: none{/if}">
                                                <option value="card" {if !empty($order->card_id)}selected{/if}>
                                                    Банковская карта
                                                </option>
                                                <option value="bank"
                                                        {if !empty($order->requisite_id) || empty($order->card_id) && empty($order->requisite_id)}selected{/if}>
                                                    Расчетный счет
                                                </option>
                                            </select>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <div class="requisits" {if !empty($order->card_id)}style="display: none"{/if}>
                                            <h4>Перечислить микрозайм по следующим реквизитам:</h4><br>
                                            <div style="width: 100%; display: flex">
                                                <input type="hidden" name="requisite[id]"
                                                       value="{$order->requisite_id}"/>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Фамилия держателя счета</label>
                                                    <input class="form-control fio"
                                                           readonly
                                                           style="width: 350px; margin-left: 30px"
                                                           type="text" name="requisite[holder][lastname]"
                                                           value="{$holder_lastname}"/>
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Имя держателя счета</label>
                                                    <input class="form-control fio"
                                                           readonly
                                                           style="width: 350px; margin-left: 30px"
                                                           type="text" name="requisite[holder][firstname]"
                                                           value="{$holder_firstname}"/>
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Отчество держателя счета</label>
                                                    <input class="form-control fio"
                                                           readonly
                                                           style="width: 350px; margin-left: 30px"
                                                           type="text" name="requisite[holder][patronymic]"
                                                           value="{$holder_patronymic}"/>
                                                </div>
                                            </div>
                                            <br>
                                            <div style="width: 100%; display: flex">
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Номер счета</label>
                                                    <input class="form-control account_number"
                                                           style="width: 300px; margin-left: 30px"
                                                           type="text" name="requisite[number]"
                                                           value="{$order->requisite->number}"/>
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">БИК
                                                        банка</label>
                                                    <input class="form-control bik"
                                                           style="width: 180px; margin-left: 30px"
                                                           type="text" name="requisite[bik]"
                                                           value="{$order->requisite->bik}"/>
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Наименование
                                                        банка</label>
                                                    <input readonly class="form-control bank_name"
                                                           style="width: 350px;margin-left: 30px"
                                                           type="text" name="requisite[name]"
                                                           value="{$order->requisite->name}"/>
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Кор. счет</label>
                                                    <input readonly class="form-control cor"
                                                           style="width: 350px;margin-left: 30px"
                                                           type="text" name="requisite[correspondent_acc]"
                                                           value="{$order->requisite->correspondent_acc}"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cardes"
                                             {if !empty($order->requisite_id) || empty($order->card_id) && empty($order->requisite_id)}style="display: none"{/if}>
                                            <h4>Перечислить микрозайм на карту:</h4><br>
                                            <div style="width: 100%; display: flex">
                                                <input type="hidden" name="card_id" value="{$order->card_id}">
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Держатель карты</label>
                                                    <input class="form-control"
                                                           style="width: 300px; margin-left: 30px"
                                                           name="card_name"
                                                           value="{$order->card_name}"
                                                           type="text">
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Номер карты</label>
                                                    <input class="form-control card_mask"
                                                           style="width: 300px; margin-left: 30px"
                                                           name="pan"
                                                           value="{$order->pan}"
                                                           type="text">
                                                </div>
                                                <div style="display: flex; flex-direction: column">
                                                    <label class="control-label">Срок годности карты</label>
                                                    <input class="form-control card_exp"
                                                           style="width: 300px; margin-left: 30px"
                                                           name="expdate"
                                                           value="{$order->expdate}"
                                                           type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Текущие банковские кредиты и займы:</h4>
                                        <table class="jsgrid-table table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Банк / МФО</th>
                                                <th>Текущий долг, руб.</th>
                                                <th>Ежемесячный платеж, руб.</th>
                                                <th>Срок погашения, месяц и год</th>
                                                <th>Ставка, % годовых</th>
                                                <th>Наличие просрочек</th>
                                                <th><input type="button"
                                                               class="btn btn-outline-success add_to_credits_table"
                                                               value="+"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="credits_table">
                                            {if !empty($order->credits_story)}
                                                {foreach json_decode($order->credits_story) as $credits_story}
                                                    <tr>
                                                        <td><input class="form-control bank_validate"
                                                                   name="credits_bank_name[][credits_bank_name]"
                                                                   type="text"
                                                                   value="{$credits_story->credits_bank_name}"></td>
                                                        <td><input class="form-control mask_number"
                                                                   name="credits_rest_sum[][credits_rest_sum]"
                                                                   type="text"
                                                                   value="{$credits_story->credits_rest_sum}"></td>
                                                        <td><input class="form-control mask_number"
                                                                   name="credits_month_pay[][credits_month_pay]"
                                                                   type="text"
                                                                   value="{$credits_story->credits_month_pay}"></td>
                                                        <td><input class="form-control validity_period"
                                                                   name="credits_return_date[][credits_return_date]"
                                                                   type="text"
                                                                   value="{$credits_story->credits_return_date}"></td>
                                                        <td><input class="form-control credit_procents"
                                                                   name="credits_percents[][credits_percents]"
                                                                   type="text"
                                                                   value="{$credits_story->credits_percents}"></td>
                                                        <td><select class="form-control"
                                                                    name="credits_delay[][credits_delay]">
                                                                <option value="Да">Да</option>
                                                                <option value="Нет" selected>Нет</option>
                                                            </select></td>
                                                        <td><div class="btn btn-outline-danger delete_credit">-</div></td>
                                                    </tr>
                                                {/foreach}
                                            {else}
                                                <tr>
                                                    <td><input class="form-control bank_validate"
                                                               name="credits_bank_name[][credits_bank_name]" type="text"
                                                               value=""></td>
                                                    <td><input class="form-control mask_number"
                                                               name="credits_rest_sum[][credits_rest_sum]" type="text"
                                                               value=""></td>
                                                    <td><input class="form-control mask_number"
                                                               name="credits_month_pay[][credits_month_pay]" type="text"
                                                               value=""></td>
                                                    <td><input class="form-control validity_period"
                                                               name="credits_return_date[][credits_return_date]"
                                                               type="text"
                                                               value=""></td>
                                                    <td><input class="form-control credit_procents"
                                                               name="credits_percents[][credits_percents]" type="text"
                                                               value=""></td>
                                                    <td><select class="form-control"
                                                                name="credits_delay[][credits_delay]">
                                                            <option value="Да">Да</option>
                                                            <option value="Нет" selected>Нет</option>
                                                        </select></td>
                                                    <td><div class="btn btn-outline-danger delete_credit">-</div></td>
                                                </tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <h4>Используемые банковские карты:</h4>
                                        <table class="jsgrid-table table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Банк / МФО</th>
                                                <th>Лимит по банковской карте, руб.</th>
                                                <th>Текущая задолженность, руб.</th>
                                                <th>Срок действия карты, месяц и год</th>
                                                <th>Наличие просрочек</th>
                                                <th><input type="button"
                                                           class="btn btn-outline-success add_to_cards_table"
                                                           value="+"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="cards_table">
                                            {if !empty($order->cards_story)}
                                                {foreach json_decode($order->cards_story) as $cards_story}
                                                    <tr>
                                                        <td><input class="form-control bank_validate"
                                                                   name="cards_bank_name[][cards_bank_name]" type="text"
                                                                   value="{$cards_story->cards_bank_name}"></td>
                                                        <td><input class="form-control mask_number"
                                                                   name="cards_limit[][cards_limit]"
                                                                   type="text"
                                                                   value="{$cards_story->cards_limit}"></td>
                                                        <td><input class="form-control mask_number"
                                                                   name="cards_rest_sum[][cards_rest_sum]"
                                                                   type="text"
                                                                   value="{$cards_story->cards_rest_sum}"></td>
                                                        <td><input class="form-control validity_period"
                                                                   name="cards_validity_period[][cards_validity_period]"
                                                                   type="text"
                                                                   value="{$cards_story->cards_validity_period}"></td>
                                                        <td><select class="form-control"
                                                                    name="cards_delay[][cards_delay]">
                                                                <option value="Да">Да</option>
                                                                <option value="Нет" selected>Нет</option>
                                                            </select
                                                        </td>
                                                        <td><div class="btn btn-outline-danger delete_card">-</div></td>
                                                    </tr>
                                                {/foreach}
                                            {else}
                                                <tr>
                                                    <td><input class="form-control bank_validate"
                                                               name="cards_bank_name[][cards_bank_name]" type="text"
                                                               value=""></td>
                                                    <td><input class="form-control mask_number"
                                                               name="cards_limit[][cards_limit]"
                                                               type="text"
                                                               value=""></td>
                                                    <td><input class="form-control mask_number"
                                                               name="cards_rest_sum[][cards_rest_sum]"
                                                               type="text"
                                                               value=""></td>
                                                    <td><input class="form-control validity_period"
                                                               name="cards_validity_period[][cards_validity_period]"
                                                               type="text" value=""></td>
                                                    <td><select class="form-control" name="cards_delay[][cards_delay]">
                                                            <option value="Да">Да</option>
                                                            <option value="Нет" selected>Нет</option>
                                                        </select
                                                    </td>
                                                    <td><div class="btn btn-outline-danger delete_card">-</div></td>
                                                </tr>
                                            {/if}
                                            </tbody>
                                        </table>
                                        <br>
                                        <br>
                                        <div style="display: flex; width: 500px;" id="buttons_append">
                                            <input style="display: none" type="submit" name="create_new_order"
                                                   class="btn btn-success buttons_append create_new_order"
                                                   value="Создать заявку">
                                            <input type="button" name="draft" style="margin-left: 100px; display: none;"
                                                   class="btn btn-primary buttons_append create_new_draft"
                                                   value="Сохранить черновик">
                                        </div>
                                        {if isset($order->order_id)}
                                            <div data-order="{$order->order_id}"
                                                 class="btn btn-outline-danger delete_draft" style="float: right">
                                                Удалить черновик
                                            </div>
                                        {/if}
                                        <br><br>
                                        <input style="display: none" name="loan_type_to_submit"
                                               class="loan_type_to_submit" value="{$order->loan_type}">
                                        <input style="display: none" name="order_id" value="{$order->order_id}">
                                        <input style="display: none" name="user_id" value="{$order->user_id}">
                                        <input style="display: none" name="check_same_users"
                                               value="{if $order->original == 1}1{else}0{/if}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {include file='footer.tpl'}

        </div>
