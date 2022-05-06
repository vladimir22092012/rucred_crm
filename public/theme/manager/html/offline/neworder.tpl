{$meta_title="Новая заявка" scope=parent}

{capture name='page_scripts'}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/neworder_new.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(function () {
            let order = {{json_encode($order)}};
            if (order) {
                $('.price_basic[id="' + order['loan_type'] + '"]').trigger('click');

                if (parseInt(order['profunion']) == 3) {
                    $('input[id="profunion2"]').trigger('click');
                    $('input[id="want_profunion"]').trigger('click');
                }
                else {
                    $('input[id="profunion' + order['profunion'] + '"]').trigger('click');
                }

                if (order['prev_fio'] != null) {
                    $('input[id="change_fio2"]').trigger('click');
                }
                else {
                    $('input[id="change_fio1"]').trigger('click');
                }

                if (order['Regadressfull'] == order['Faktadressfull'])
                    $('input[name="actual_address"]').trigger('click');

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
                }, 50);
            }
        })
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

        .icon_messag {
            position: relative;
            right: 31em;
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
                        <small class="text-white float-right">{$offline_points[$manager->offline_point_id]->address}</small>
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
                                                   class="form-control daterange" value="{$order->date|date}">
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
                                    <div class="alert alert-danger"
                                         style="display: none; width: 25%; margin-left: 30px">
                                    </div>
                                    <hr style="width: 100%; size: 5px">
                                    <div style="width: 100%; margin: 30px 30px">
                                        <h3>ФИО</h3><br>
                                        <div style="width: 100%">
                                            <input style="width: 350px; margin-left: 25px" type="text" name="lastname"
                                                   class="form-control js-lastname-input"
                                                   placeholder="Фамилия" required value="{$order->lastname}"/>
                                            <input style="width: 350px; margin-left: 25px" type="text" name="firstname"
                                                   class="form-control js-firstname-input"
                                                   placeholder="Имя" required value="{$order->firstname}"/>
                                            <input class="form-control js-patronymic-input"
                                                   style="width: 350px; margin-left: 25px"
                                                   name="patronymic"
                                                   placeholder="Отчество(если есть)" type="text"
                                                   value="{$order->patronymic}">
                                            <br><br><br>
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
                                        </div>
                                        <br><br>
                                        <div style="width: 100%; display:none" id="change_fio_toggle">
                                            <label class="control-label">Предыдущие ФИО</label>
                                            <label class="control-label" style="margin-left: 230px">Дата
                                                изменения</label><br>
                                            <input class="form-control prev_fio" style="width: 350px; margin-left: 25px"
                                                   type="text" name="prev_fio" value="{$order->prev_fio}"
                                            />
                                            <input type="text" style="width: 180px; margin-left: 25px"
                                                   name="fio_change_date"
                                                   class="form-control daterange fio_change_date"
                                                   value="{$order->fio_change_date|date}">
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Место рождения</label>
                                            <label class="control-label" style="margin-left: 240px">Дата
                                                рождения</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" name="birth_place" value="{$order->birth_place}"
                                            />
                                            <input type="text" style="width: 180px; margin-left: 25px"
                                                   name="birth"
                                                   class="form-control daterange" value="{$order->birth|date}">
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Телефон</label><br>
                                            <input class="form-control phone_num"
                                                   style="width: 190px; margin-left: 25px"
                                                   type="text"
                                                   name="phone"
                                                   placeholder="+7(900)000-00-00" value="{$order->phone_mobile}"/>
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
                                                <input class="form-control passport_issued"
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
                                            <input class="form-control Regadress" name="Regadressfull"
                                                   style="width: 700px; margin-left: 25px" type="text"
                                                   value="{$order->Regadressfull}"/>
                                            <input style="display: none" class="Registration" name="Regadress"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Место жительства</label><br>
                                            <div class="custom-checkbox">
                                                <input type="checkbox" style="margin-left: 30px" name="actual_address"
                                                       value="1"/>
                                                <label class="" for="equal_address">Совпадает с адресом
                                                    регистрации</label>
                                            </div>
                                            <input class="form-control Faktaddress" id="actual_address_toggle"
                                                   style="width: 700px; margin-left: 25px" name="Faktadressfull"
                                                   value="{$order->Faktadressfull}"
                                                   type="text"/>
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
                                            <label class="control-label">ФИО Супруги(-а)</label>
                                            <label class="control-label" style="margin-left: 240px">Моб. телефон</label><br>
                                            <input class="form-control fio_spouse"
                                                   style="width: 350px; margin-left: 25px"
                                                   type="text" name="fio_spouse" value="{$order->fio_spouse}"/>
                                            <input class="form-control phone_num phone_spouse"
                                                   style="width: 200px; margin-left: 25px"
                                                   type="text" name="phone_spouse" value="{$order->phone_spouse}"/>
                                        </div>
                                        <br>

                                        <hr style="width: 100%; size: 5px">

                                        <h4>Контактные данные</h4><br>
                                        <div style="width: 100%">
                                            <label class="control-label">Электронная почта*</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" name="email" placeholder="ivanov@mail.ru(необязательно)"
                                                   value="{$order->email}"/>
                                            <input type="button" style="margin-left: 25px" class="btn btn-success"
                                                   value="Отправить">
                                        </div>
                                        <br>
                                        <h4>Мессенджер для связи</h4><br>
                                        <div style="width: 100%">
                                            <input class="form-control phone_num"
                                                   style="width: 450px; margin-left: 25px"
                                                   type="text" name="viber" value="{$order->viber_num}">
                                            <img class="icon_messag"
                                                 src="https://img.icons8.com/ios-glyphs/344/viber.png" width="30"
                                                 height="30"><br><br>
                                            <input class="form-control phone_num"
                                                   style="width: 450px; margin-left: 25px"
                                                   type="text" name="whatsapp" value="{$order->whatsapp_num}">
                                            <img class="icon_messag"
                                                 src="https://img.icons8.com/office/344/whatsapp--v1.png" width="30"
                                                 height="30"><br><br>
                                            <input class="form-control phone_num"
                                                   style="width: 450px; margin-left: 25px"
                                                   type="text" name="telegram" value="{$order->telegram_num}">
                                            <img class="icon_messag"
                                                 src="https://img.icons8.com/color/344/telegram-app--v1.png" width="30"
                                                 height="30"><br><br>
                                        </div>
                                        <br>
                                        <h4>Основные каналы связи</h4>
                                        <div class="form-check" style="display:flex; margin-left: 25px">
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" name="push_not"
                                                       value="1">
                                                <label class="form-check-label">
                                                    Push-уведомления
                                                </label>
                                            </div>
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
                                                <input class="form-control" name="children"
                                                       style="width: 300px; margin-left: 25px"
                                                       placeholder="необязательно" type="text">
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Аттестация</h4><br>
                                        <table class="jsgrid-table table table-striped">
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
                                            <tr>
                                                <td><input class="form-control daterange"
                                                           name="date[][date]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control"
                                                           name="comment[][comment]" type="text"
                                                           value=""></td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы иностранным публичным должностным лицом?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign"
                                                   id="foreign1" value="1" checked>
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
                                                   id="foreign_husb_wife1" checked value="1">
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
                                            <input class="form-control fio_public_spouse" name="fio_public_spouse"
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
                                                   id="foreign_relative1" checked value="1">
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
                                            <input class="form-control fio_relative" name="fio_relative"
                                                   style="width: 500px; margin-left: 25px" type="text"
                                                   value="{$order->fio_relative}"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Перечислить микрозайм по следующим реквизитам:</h4><br>
                                        <div style="width: 100%">
                                            <label class="control-label">ФИО держателя счета</label>
                                            <label class="control-label" style="margin-left: 200px">Номер
                                                счета</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" name="fio_acc_holder" value="{$order->fio_acc_holder}"/>
                                            <input class="form-control account_number"
                                                   style="width: 300px; margin-left: 25px"
                                                   type="text" name="account_number" value="{$order->account_number}"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Наименование банка</label>
                                            <label class="control-label" style="margin-left: 210px">БИК
                                                банка</label><br>
                                            <input class="form-control bank_name"
                                                   style="width: 350px; margin-left: 25px"
                                                   type="text" name="bank_name" value="{$order->bank_name}"/>
                                            <input class="form-control bik" style="width: 180px; margin-left: 25px"
                                                   type="text" name="bik_bank" value="{$order->bik_bank}"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
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
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="credits_table">
                                            <tr>
                                                <td><input class="form-control"
                                                           name="credits_bank_name[][credits_bank_name]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control mask_number"
                                                           name="credits_rest_sum[][credits_rest_sum]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control mask_number"
                                                           name="credits_month_pay[][credits_month_pay]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control validity_period"
                                                           name="credits_return_date[][credits_return_date]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control"
                                                           name="credits_percents[][credits_percents]" type="text"
                                                           value=""></td>
                                                <td><select class="form-control" name="credits_delay[][credits_delay]">
                                                        <option value="Да">Да</option>
                                                        <option value="Нет">Нет</option>
                                                    </select></td>
                                                <td><input type="button"
                                                           class="btn btn-outline-success add_to_credits_table"
                                                           value="+"></td>
                                            </tr>
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
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="cards_table">
                                            <tr>
                                                <td><input class="form-control "
                                                           name="cards_bank_name[][cards_bank_name]" type="text"
                                                           value=""></td>
                                                <td><input class="form-control mask_number" name="cards_limit[][cards_limit]"
                                                           type="text"
                                                           value=""></td>
                                                <td><input class="form-control mask_number" name="cards_rest_sum[][cards_rest_sum]"
                                                           type="text"
                                                           value=""></td>
                                                <td><input class="form-control validity_period"
                                                           name="cards_validity_period[][cards_validity_period]"
                                                           type="text" value=""></td>
                                                <td><select class="form-control" name="cards_delay[][cards_delay]">
                                                        <option value="Да">Да</option>
                                                        <option value="Нет">Нет</option>
                                                    </select
                                                </td>
                                                <td><input type="button"
                                                           class="btn btn-outline-success add_to_cards_table"
                                                           value="+"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <br>
                                        <br>
                                        <div style="display: flex; width: 500px;" id="buttons_append">
                                            <input style="display: none" type="submit" name="create_new_order"
                                                   class="btn btn-success buttons_append"
                                                   value="Создать заявку">
                                            <input type="submit" name="draft" style="margin-left: 100px; display: none;"
                                                   class="btn btn-primary buttons_append"
                                                   value="Сохранить черновик">
                                        </div>
                                        <br><br>
                                        <input style="display: none" name="loan_type_to_submit"
                                               class="loan_type_to_submit" value="{$order->loan_type}">
                                        <input style="display: none" name="order_id" value="{$order->order_id}">
                                        <input style="display: none" name="user_id" value="{$order->user_id}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {include file='footer.tpl'}

        </div>