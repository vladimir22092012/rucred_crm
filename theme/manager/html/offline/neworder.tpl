{$meta_title="Новая заявка" scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/neworder.js?v=1.02"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/validation.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
        !function (window, document, $) {
            "use strict";
            $("input,select,textarea").not("[type=submit]").jqBootstrapValidation()
        }(window, document, jQuery);
    </script>
    <script>
        $(function () {

            $('.date_to_select').on('change', function () {
                $('#orders_sum').val('');
                $('#final_sum').val('');
            });

            $('#orders_sum').on('input', function (e) {

                let sum = Number($(this).val());
                let percents = $('#loan_percents').val();

                let date_from = $('#start_date').data('daterangepicker').startDate._d;
                date_from = new Date(date_from);


                let date_to;

                if ($('#end_date').is(':visible')) {
                    date_to = $('#end_date').data('daterangepicker').startDate._d;
                    date_to = new Date(date_to);

                    let date_diff = new Date(date_to - date_from) / 1000 / 60 / 60 / 24;

                    let final_sum = (((sum * percents) * date_diff) / 100) + sum;

                    $('#final_sum').val(final_sum);
                }
                else {

                    let select = $('.date_to_select').val();

                    date_to = $('#start_date').data('daterangepicker').startDate._d;
                    date_to = date_to.setMonth(date_to.getMonth() + Number(select));
                    date_to = new Date(date_to);


                    let date_diff = new Date(date_to - date_from) / 1000 / 60 / 60 / 24;

                    let final_sum = ((((sum * percents) * date_diff) / 100) + sum) / Number(select);

                    $('#final_sum').val(Math.round(final_sum));
                }

            });


            $('.price_container').on('click', function (e) {
                e.preventDefault();

                let percents = $(this).children().find('.loantype-percents').text();
                let loan_id = $(this).attr('data-loan');

                if (loan_id == 2) {
                    $('#calendar').hide();
                    $('#return_sum').hide();
                    $('#month_sum').show();
                    $('#calendar_selector').show();
                }

                else {
                    $('#calendar_selector').hide();
                    $('#month_sum').hide();
                    $('#return_sum').show();
                    $('#calendar').show();
                }

                $('#orders_sum').val('');
                $('#final_sum').val('');
                $('#loan_percents').val(percents);
            });

            moment.locale('ru');

            $('.daterange').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'MMMM DD, YYYY'
                },
            });

            $('input[name="change_fio"]').on('click', function (e) {
                $('#change_fio_toggle').toggle();
            });

            $('input[name="sex"]').on('click', function (e) {
                $('#sex_toggle').toggle();
            });

            $('input[name="foreign_husb_wife"]').on('click', function (e) {
                $('#foreign_husb_wife_toggle').toggle();
            });

            $('input[name="foreign_relative"]').on('click', function (e) {
                $('#foreign_relative_toggle').toggle();
            });

            $('input[name="profunion"]').on('click', function (e) {
                $('#profunion_toggle').toggle();
            });

            $('input[name="actual_address"]').on('click', function (e) {
                $('#actual_address_toggle').toggle();
            });

        })
    </script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
{/capture}

{capture name='page_styles'}
    <style>

        .price {
            width: 100% !important;
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
        }

        .price_container {
            margin: 0 auto;
            display: flex;
            padding-top: 20px;
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
            height: 220px;
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
    </style>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
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
                        <form method="POST" id="">
                            <div class="form-body">


                                <div class="row">

                                    {if $error}
                                        <div class="col-12">
                                            <div class="alert alert-danger">{$error}</div>
                                        </div>
                                    {/if}


                                    <div class="price_info">
                                        <h2>Off-line заявка</h2>
                                        <p><span>Вид займа</span></p>
                                    </div>
                                    <div class="price" id="pricelist" style="width: 100%;">
                                        {foreach $loantypes as $loantype}
                                            <div class="price_container" data-loan="{$loantype->id}">
                                                <div class="price_basic"><br>
                                                    <div class="height">
                                                        <h4>{$loantype->name}</h4>
                                                        <h5>от <span class="sum">{$loantype->min_amount}</span> Р до
                                                            <span class="sum">{$loantype->max_amount}</span> Р</h5>
                                                    </div>
                                                    <hr style="width: 80%; size: 5px">
                                                    <div>
                                                        <h6>
                                                            <span class="sum loantype-percents">{$loantype->percent}</span>%
                                                        </h6>
                                                        <span>За каждый день использования микрозаймом</span>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>

                                    <hr style="width: 100%; size: 5px">

                                    <div class="loantype_settings">
                                        <label style="font-size: 12px">Сумма:</label>
                                        <input id="orders_sum" class="form-control"
                                               style="width: 100px">
                                        <label style="font-size: 12px">Желаемая дата выдачи займа:</label>
                                        <input type="text" style="width: 180px" id="start_date"
                                               name="daterange"
                                               class="form-control daterange">
                                        <div id="calendar">
                                            <label style="font-size: 12px">Возврат до:</label>
                                            <input type="text" style="margin-left: 10px; width: 180px" id="end_date"
                                                   name="daterange"
                                                   class="form-control daterange">
                                        </div>
                                        <div id="calendar_selector" style="display: none">
                                            <label style="font-size: 12px">Заем на:</label>
                                            <select class="form-control date_to_select"
                                                    style="margin-left: 10px; width: 180px">
                                                <option value="5" selected>на 5 месяцев</option>
                                                <option value="11">на 11 месяцев</option>
                                            </select>
                                        </div>
                                        <label style="font-size: 12px" id="return_sum">Сумма возврата:</label>
                                        <label id="month_sum" style="display: none; font-size: 12px">Ежемесячный платеж:</label>
                                        <input class="form-control" id="final_sum"
                                               style="margin-left: 10px; width: 100px">
                                        <input id="loan_percents" class="form-control"
                                               style="margin-left: 10px; width: 80px" disabled>
                                    </div>
                                    <br><br>

                                    <div class="col-12">
                                        <hr class="mt-0 mb-3"/>
                                    </div>
                                    <div style="width: 100%; margin: 30px 30px">
                                        <h3>Членство в профсоюзе</h3><br>
                                        <div style="display: flex;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="profunion"
                                                       id="profunion1" checked>
                                                <label class="form-check-label" for="profunion1">
                                                    Я член профсоюза
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="radio" name="profunion"
                                                       id="profunion2">
                                                <label class="form-check-label" for="profunion2">
                                                    Я не являюсь членом профсоюза
                                                </label>
                                            </div>
                                            <div class="form-check" style="display: none; margin-left: 25px" id="profunion_toggle">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="flexCheckIndeterminate">
                                                <label class="form-check-label" for="flexCheckIndeterminate">
                                                    Оповестить о желании вступления в профсоюз
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr style="width: 100%; size: 5px">

                                    <div style="width: 100%; margin: 30px 30px">
                                        <h3>ФИО</h3><br>
                                        <div style="width: 100%">
                                            <input style="width: 350px; margin-left: 25px" type="text" name="lastname"
                                                   value="{$order->lastname}"
                                                   class="form-control js-lastname-input"
                                                   placeholder="Фамилия" required="true"/>
                                            <input style="width: 350px; margin-left: 25px" type="text" name="firstname"
                                                   value="{$order->firstname}"
                                                   class="form-control js-firstname-input"
                                                   placeholder="Имя" required="true"/>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   placeholder="Отчество(если есть)" type="text">
                                            <br><br><br>
                                            <h4>Менялись ли ваши фамилия, имя, отчество?</h4><br>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="radio" name="change_fio"
                                                       id="change_fio1" checked>
                                                <label class="form-check-label" for="change_fio1">
                                                    Нет
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="radio" name="change_fio"
                                                       id="change_fio2">
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
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                            <input class="form-control" style="width: 180px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Место рождения</label>
                                            <label class="control-label" style="margin-left: 240px">Дата
                                                рождения</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                            <input class="form-control" style="width: 180px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Телефон</label><br>
                                            <input class="form-control" style="width: 190px; margin-left: 25px"
                                                   type="text"
                                                   name="phone" data-mask="7(999) 999-9999"
                                                   placeholder="7(900)000-00-00"/>
                                        </div>

                                        <hr style="width: 100%; size: 5px">

                                        <div style="width: 100%">
                                            <h3>Основные данные</h3><br>
                                            <div style="width: 100%">
                                                <label class="control-label">Паспорт</label>
                                                <label class="control-label" style="margin-left: 185px">Код
                                                    подразделения</label>
                                                <label class="control-label" style="margin-left: 50px">Кем выдан</label>
                                                <label class="control-label" style="margin-left: 245px">Когда
                                                    выдан</label><br>
                                                <input class="form-control" style="width: 100px; margin-left: 25px"
                                                       placeholder="----" type="text"/>
                                                <input class="form-control" style="width: 100px; margin-left: 25px"
                                                       placeholder="----" type="text"/>
                                                <input class="form-control" style="width: 180px; margin-left: 25px"
                                                       type="text"/>
                                                <input class="form-control" style="width: 300px; margin-left: 25px"
                                                       type="text"/>
                                                <input type="text" style="width: 180px; margin-left: 25px"
                                                       id="passport_date"
                                                       name="daterange"
                                                       class="form-control daterange">
                                            </div>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">СНИЛС</label>
                                            <label class="control-label" style="margin-left: 70px">ИНН</label><br>
                                            <input class="form-control" style="width: 100px; margin-left: 25px"
                                                   placeholder="----------" type="text"/>
                                            <input class="form-control" style="width: 100px; margin-left: 25px"
                                                   placeholder="----------" type="text"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Адрес регистрации</label><br>
                                            <input class="form-control" id="Regadress"
                                                   style="width: 500px; margin-left: 25px" type="text"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%;">
                                            <label class="control-label">Место жительства</label><br>
                                            <div class="custom-checkbox">
                                                <input type="checkbox" style="margin-left: 30px" name="actual_address" value="1"/>
                                                <label class="" for="equal_address">Совпадает с адресом
                                                    регистрации</label>
                                            </div>
                                            <input class="form-control" id="actual_address_toggle"
                                                   style="width: 500px; margin-left: 25px" type="text"/>
                                        </div>
                                        <br>
                                        <h4>Семейное положение</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="sex"
                                                   id="sex1">
                                            <label class="form-check-label" for="sex1">
                                                Состою в браке
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="sex"
                                                   id="sex2" checked>
                                            <label class="form-check-label" for="sex2">
                                                Не состою в браке
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none;" id="sex_toggle">
                                            <label class="control-label">ФИО Супруги(-а)</label>
                                            <label class="control-label" style="margin-left: 240px">Моб. телефон</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                            <input class="form-control" style="width: 180px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                        </div>
                                        <br>

                                        <hr style="width: 100%; size: 5px">

                                        <h4>Контактные данные</h4><br>
                                        <div style="width: 100%">
                                            <label class="control-label">Электронная почта*</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" placeholder="ivanov@mail.ru"
                                                   required="true"/>
                                            <input type="button" style="margin-left: 25px" class="btn btn-success"
                                                   value="Отправить">
                                        </div>
                                        <br>
                                        <h4>Мессенджер для связи</h4><br>
                                        <div style="width: 100%">
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" placeholder="ivanov@mail.ru"><br><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" placeholder="+7(909)123-45-67"><br><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" placeholder="@">
                                        </div>
                                        <br>
                                        <h4>Основные каналы связи</h4>
                                        <div class="form-check" style="display:flex; margin-left: 25px">
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" value="" checked>
                                                <label class="form-check-label">
                                                    Push-уведомления
                                                </label>
                                            </div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" value="">
                                                <label class="form-check-label">
                                                    SMS-уведомления
                                                </label></div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" value="" checked>
                                                <label class="form-check-label">
                                                    Электронная почта
                                                </label></div>
                                            <div class="form-check" style="margin-left: 25px">
                                                <input class="form-check-input" type="checkbox" value="">
                                                <label class="form-check-label">
                                                    Мессенджеры
                                                </label></div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>

                                        <h3>Работодатель</h3><br>
                                        <div style="width: 100%; margin-left: 25px">
                                            <select style="width: 500px" class="form-control">
                                                <option>ООО "Название компании" 2</option>
                                            </select>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text" placeholder="Введите ООО работодателя здесь">
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: flex">
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Среднемесячный доход
                                                    руб.</label><br>
                                                <input class="form-control" style="width: 300px; margin-left: 25px"
                                                       type="text">
                                            </div>
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Среднемесячные расходы
                                                    руб.</label><br>
                                                <input class="form-control" style="width: 300px; margin-left: 25px"
                                                       type="text">
                                            </div>
                                            <div class="form-group">
                                                <label style="margin-left: 25px!important;">Количество
                                                    иждивенцев</label><br>
                                                <input class="form-control" style="width: 300px; margin-left: 25px"
                                                       type="text">
                                            </div>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы иностранным публичным должностным лицом?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign"
                                                   id="foreign1">
                                            <label class="form-check-label" for="foreign1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign"
                                                   id="foreign2" checked>
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
                                                   id="foreign_husb_wife1" checked>
                                            <label class="form-check-label" for="foreign_husb_wife1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_husb_wife"
                                                   id="foreign_husb_wife2">
                                            <label class="form-check-label" for="foreign_husb_wife2">
                                                Да
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none" id="foreign_husb_wife_toggle">
                                            <label class="control-label">ФИО Супруги(-а)</label><br>
                                            <input class="form-control" id="Regadress"
                                                   style="width: 500px; margin-left: 25px" type="text"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Являетесь ли вы близким родственником иностранного публичного должностного
                                            лица?</h4><br>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_relative"
                                                   id="foreign_relative1" checked>
                                            <label class="form-check-label" for="foreign_relative1">
                                                Нет
                                            </label>
                                        </div>
                                        <div class="form-check" style="margin-left: 30px">
                                            <input class="form-check-input" type="radio" name="foreign_relative"
                                                   id="foreign_relative2">
                                            <label class="form-check-label" for="foreign_relative2">
                                                Да
                                            </label>
                                        </div>
                                        <br>
                                        <div style="width: 100%; display: none" id="foreign_relative_toggle">
                                            <label class="control-label">ФИО родственника</label><br>
                                            <input class="form-control" id="Regadress"
                                                   style="width: 500px; margin-left: 25px" type="text"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Перечислить микрозайм по следующим реквизитам:</h4><br>
                                        <div style="width: 100%">
                                            <label class="control-label">ФИО держателя счета</label>
                                            <label class="control-label" style="margin-left: 230px">Номер
                                                счета</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                            <input class="form-control" style="width: 180px; margin-left: 25px"
                                                   type="text" placeholder="-------------------"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">Наименование банка</label>
                                            <label class="control-label" style="margin-left: 240px">БИК
                                                банка</label><br>
                                            <input class="form-control" style="width: 350px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                            <input class="form-control" style="width: 180px; margin-left: 25px"
                                                   type="text" placeholder="-------------------"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <h4>Или по реквизитам банковской карты</h4><br>
                                        <div style="width: 100%">
                                            <label class="control-label">Номер банковской карты</label>
                                            <label class="control-label" style="margin-left: 100px">Срок действия
                                                карты</label><br>
                                            <input class="form-control" style="width: 200px; margin-left: 25px"
                                                   type="text" placeholder="---- ---- ---- ----"
                                                   required="true"/>
                                            <input class="form-control" style="width: 50px; margin-left: 100px"
                                                   type="text" placeholder="-------------------"
                                                   required="true"/>
                                            <span>/</span>
                                            <input class="form-control" style="width: 50px;"
                                                   type="text" placeholder="-------------------"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <label class="control-label">ФИО держателя счета</label><br>
                                            <input class="form-control" style="width: 500px; margin-left: 25px"
                                                   type="text"
                                                   required="true"/>
                                        </div>
                                        <br>
                                        <hr style="width: 100%; size: 5px">
                                        <br>
                                        <div class="form-check" style="margin-left: 25px">
                                            <input class="form-check-input" type="checkbox" value="" checked>
                                            <label class="form-check-label">
                                                Дано согласие на передачу информации от работодателя
                                            </label>
                                        </div>
                                        <br>
                                        <br>
                                        <div style="display: flex; width: 500px; margin-left: 80px">
                                            <input type="button" class="btn btn-success" value="Создать заявку">
                                            <input type="button" style="margin-left: 100px" class="btn btn-primary" value="Сохранить черновик">
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {include file='footer.tpl'}

</div>