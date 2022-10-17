{$meta_title="Тест калькулятор" scope=parent}

{capture name='page_scripts'}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/popper.min.js?v=1.02"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/bootstrap/js/bootstrap.js?v=1.01"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js?v=1.01"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
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
    <script src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/graphic_test.js"></script>
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
                        }
                        else {
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

                let email = $('input[class="form-control email"]').val();
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
                        }
                        else {
                            $('.show_email_code').show();
                        }
                    }
                })
            });

            $('.accept_edit_email_with_code').click(function (e) {
                e.preventDefault();

                let email = $('input[class="form-control email"]').val();
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
                }
                else {
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

                console.log(phone);

                $('.' + attr + '').toggle();

                if ($(this).is(':checked')) {
                    if ($(this).attr('name') == 'viber_same')
                        $('.confirm_viber').attr('data-phone', phone);

                    if ($(this).attr('name') == 'telegram_same')
                        $('.confirm_telegram').attr('data-phone', phone);
                }
            });

            $(document).on('click', '.confirm_telegram, .confirm_viber', function (e) {

                let user_id = $(this).attr('data-user');
                let type = '';
                let same_flag = 0;
                let phone = $(this).parent().find('.phone_num').val();

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
                        user_id: user_id
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
                                    }, 500);
                                    break;
                            }
                        } else {
                            Swal.fire({
                                title: 'Произошла ошибка',
                                confirmButtonText: 'ОК'
                            });
                        }
                    }
                });
            });

            $(document).on('input', '.fio', function () {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            })
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
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i>Тест-калькулятор</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Тест-калькулятор</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">

            </div>
        </div>

        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row" style="width: 100%">
                                <br><br>
                                <hr style="width: 100%; size: 5px">
                                <form method="POST" id="forms" style="width: 100%">
                                    <h3>Доступность</h3><br>
                                    <div style="width: 100%; margin-left: 25px">
                                        <select style="width: 300px" class="form-control permission">
                                            <option value="none">Выберите режим</option>
                                            <option value="all">Все компании и тарифы</option>
                                            <option value="online">Компании и тарифы в онлайне</option>
                                            <option value="offline">Компании и тарифы в офлайне</option>
                                        </select>
                                    </div><br>
                                    <h3>Работодатель</h3><br>
                                    <div style="width: 100%; margin-left: 25px">
                                        <select style="width: 500px; display: none;"
                                                class="form-control groups"
                                                name="group">
                                            <option value="none" selected>Выберите из списка</option>
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
                                            <div class="form-check" style="margin-left: 25px"
                                                 id="profunion_toggle">
                                                <input class="form-check-input want_profunion" type="checkbox"
                                                       value="1"
                                                       id="want_profunion" name="want_profunion">
                                                <label class="form-check-label" for="want_profunion">
                                                    Оповестить о желании вступления в профсоюз
                                                </label>
                                            </div>
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
                                                   value="{$order->probably_return_sum}">
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
                                    <br>
                                    <div class="graphic">

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {include file='footer.tpl'}

        </div>
