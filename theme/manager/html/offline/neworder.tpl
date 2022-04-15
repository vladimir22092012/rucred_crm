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
            }

            $('.price').slick({
                infinite: false,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
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
                                    <p><span>Вид займа</span></p><br>
                                </div>
                                <br><br>
                                <div class="price" id="pricelist">
                                    {foreach $loantypes as $loantype}
                                        <div class="price_container">
                                            <div class="price_basic" data-loan-period="{$loantype->max_period}"
                                                 data-loan="{$loantype->id}" data-min-amount="{$loantype->min_amount}"
                                                 data-max-amount="{$loantype->max_amount}" data-loan-percents=""
                                                 id="{$loantype->id}"><br>
                                                <div class="height">
                                                    <h4>{$loantype->name}</h4>
                                                    <h5>от <span
                                                                class="sum">{$loantype->min_amount|number_format:0:',':' '}</span>
                                                        Р до
                                                        <span class="sum">{$loantype->max_amount|number_format:0:',':' '}</span>
                                                        Р</h5>
                                                </div>
                                                <hr style="width: 80%; size: 5px">
                                                <div class="out_profunion percents">
                                                    <h6>
                                                        <span class="loantype-percents">{$loantype->percent|number_format:2:',':' '}</span>%
                                                        <input type="hidden" class="percents"
                                                               value="{$loantype->percent}">
                                                    </h6>
                                                    <span>За каждый день использования микрозаймом</span>
                                                </div>
                                                <div class="in_profunion percents" style="display: none">
                                                    <h6>
                                                        <span class="loantype-percents-profunion">{$loantype->profunion|number_format:2:',':' '}</span>%
                                                        <input type="hidden" class="percents"
                                                               value="{$loantype->profunion}">
                                                    </h6>
                                                    <span>За каждый день использования микрозаймом</span>
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>

                                <hr style="width: 100%; size: 5px">

                                <form method="POST" id="forms" style="width: 100%">
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
                                                   value="{$order->probably_return_date|date}">
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
                                        <input type="hidden" id="loan_percents" name="percent" class="form-control"
                                               style="margin-left: 10px; width: 80px">
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

                                        <h3>Работодатель</h3><br>
                                        <div style="width: 100%; margin-left: 25px">
                                            <select style="width: 500px" class="form-control groups"
                                                    name="company_select">
                                                <option value="none" selected>Выберите из списка</option>
                                                {if !empty($groups)}
                                                    {foreach $groups as $group}
                                                        <option value="{$group->id}">{$group->name}</option>
                                                    {/foreach}
                                                {/if}
                                            </select>
                                            <select style="width: 500px; margin-left:10px; display: none;" class="form-control my_company"
                                                    name="company_select">
                                                <option value="none" selected>Выберите из списка</option>
                                            </select>
                                            <select style="width: 300px; margin-left:10px; display: none;"
                                                    class="form-control branches"
                                                    name="branch_select">
                                                <option value="none" selected>Выберите из списка</option>
                                                {if !empty($branches)}
                                                    {foreach $branches as $branch}
                                                        <option value="{$branch_id}">{$branch->name}</option>
                                                    {/foreach}
                                                {/if}
                                            </select>
                                        </div>
                                        <br>
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
                                        <br>
                                        <br>
                                        <div style="display: flex; width: 500px;" id="buttons_append">
                                            <input type="submit" name="create_new_order" class="btn btn-success"
                                                   value="Создать заявку">
                                            <input type="submit" name="draft" style="margin-left: 100px"
                                                   class="btn btn-primary"
                                                   value="Сохранить черновик">
                                            <input type="button" style="margin-left: 100px"
                                                   class="btn btn-info to_form_loan"
                                                   value="Сформировать условия">
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