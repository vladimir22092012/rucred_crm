{$meta_title="Заявка №`$order->order_id`" scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/order.js?v=1.17"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/movements.app.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script>
        $(function () {

            $('.get-docs').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');
                let documents = {{json_encode($documents)}};

                if(Object.keys(documents).length > 0){
                    Swal.fire({
                        title: 'Формирование документов приведет к удалению всех сканов',
                        showCancelButton: true,
                        confirmButtonText: 'Согласен',
                        cancelButtonText: 'Не согласен',
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $.ajax({
                                method: 'post',
                                data: {
                                    create_documents: true,
                                    order_id: order_id
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    });
                }
                else{
                    $.ajax({
                        method: 'post',
                        data: {
                            create_documents: true,
                            order_id: order_id
                        },
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });

            $('.new_scan').on('change', function (e) {

                console.log($(this));


                let form_data = new FormData();

                form_data.append('file', e.target.files[0]);
                form_data.append('user_id', $(this).attr('data-user'));
                form_data.append('type', 'document');
                form_data.append('action', 'add');
                form_data.append('template', $(this).attr('id'));
                form_data.append('order_id', $(this).attr('data-order'));
                form_data.append('notreplace', '1');
                form_data.append('is_it_scans', 'yes');

                $.ajax({
                    url: '/ajax/upload.php',
                    data: form_data,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        location.reload();
                    }
                });
            });

            $('.inn-edit').on('click', function (e) {
                e.preventDefault();

                $('.inn-front').toggle();
                $('.inn-editor').toggle();

                $('.inn-edit-cancel').on('click', function () {
                    $('.inn-number-edit').val($('.inn-number').text());
                    $('.inn-editor').hide();
                    $('.inn-front').show();
                });

                $('.inn-edit-success').on('click', function () {
                    e.preventDefault();

                    let inn_number = $('.inn-number-edit').val();
                    let user_id = {{$order->user_id}};

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'inn_change',
                            inn_number: inn_number,
                            user_id: user_id
                        },
                        success: function (resp) {

                            $('.inn-editor').hide();
                            $('.inn-front').show();
                            $('.inn-number').text(inn_number);
                        }
                    });
                })
            });

            $('.cors-edit').on('click', function (e) {
                e.preventDefault();

                let fio_hold_front = $('.fio-hold-front').text();

                let acc_num_front = $('.acc-num-front').text();

                let bank_name_front = $('.bank-name-front').text();

                let bik_front = $('.bik-front').text();

                $('.cors-front').toggle();
                $('.cors-editor').toggle();

                $('.cors-edit-cancel').on('click', function () {
                    $('.cors-editor').hide();
                    $('.cors-front').show();

                    $('.fio-hold-edit').val(fio_hold_front);
                    $('.acc-num-edit').val(acc_num_front);
                    $('.bank-name-edit').val(bank_name_front);
                    $('.bik-edit').val(bik_front);

                });

                $('.cors-edit-success').on('click', function () {
                    e.preventDefault();
                    let user_id = {{$order->user_id}};
                    let fio_acc_holder = $('.fio-hold-edit').val();
                    let account_number = $('.acc-num-edit').val();
                    let bank_name = $('.bank-name-edit').val();
                    let bik_bank = $('.bik-edit').val();

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'cors_change',
                            user_id: user_id,
                            fio_acc_holder: fio_acc_holder,
                            account_number: account_number,
                            bank_name: bank_name,
                            bik_bank: bik_bank
                        },
                        success: function (resp) {
                            location.reload();
                        }
                    });
                })
            });

            $('.snils-edit').on('click', function (e) {
                e.preventDefault();

                $('.snils-number-edit').mask('999-999-999 99');

                $('.snils-front').toggle();
                $('.snils-editor').toggle();

                $('.snils-edit-cancel').on('click', function () {
                    $('.snils-number-edit').val($('.snils-number').text());
                    $('.snils-editor').hide();
                    $('.snils-front').show();
                });

                $('.snils-edit-success').on('click', function () {
                    e.preventDefault();

                    let snils_number = $('.snils-number-edit').val();
                    let user_id = {{$order->user_id}};

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'snils_change',
                            snils_number: snils_number,
                            user_id: user_id
                        },
                        success: function (resp) {

                            $('.snils-editor').hide();
                            $('.snils-front').show();
                            $('.snils-number').text(snils_number);
                        }
                    });
                })
            });

        });
    </script>
{/capture}

{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css" rel="stylesheet"/>
    <style>
        label, p, .btn {
            font-size: 13px !important;
        }
    </style>
{/capture}

{function name='penalty_button'}

    {if in_array('add_penalty', $manager->permissions)}
        {if !$penalties[$penalty_block]}
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-danger waves-effect js-add-penalty "
                    data-block="{$penalty_block}">
                <i class="fas fa-ban"></i>
                <span>Штраф</span>
            </button>
        {elseif $penalties[$penalty_block] && in_array($penalties[$penalty_block]->status, [1,2])}
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-primary waves-effect js-reject-penalty "
                    data-penalty="{$penalties[$penalty_block]->id}">
                <i class="fas fa-ban"></i>
                <span>Отменить</span>
            </button>
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-warning waves-effect js-strike-penalty "
                    data-penalty="{$penalties[$penalty_block]->id}">
                <i class="fas fa-ban"></i>
                <span>Страйк</span>
            </button>
        {/if}
        {if in_array($penalties[$penalty_block]->status, [4])}
            <span class="label label-warning">Страйк ({$penalties[$penalty_block]->cost} руб)</span>
        {/if}
    {elseif $penalties[$penalty_block]->manager_id == $manager->id}
        {if in_array($penalties[$penalty_block]->status, [1])}
            <button class="pb-0 pt-0 mr-2 btn btn-sm btn-primary js-correct-penalty"
                    data-penalty="{$penalties[$penalty_block]->id}" type="button">Исправить
            </button>
        {/if}
        {if in_array($penalties[$penalty_block]->status, [4])}
            <span class="label label-warning">Страйк ({$penalties[$penalty_block]->cost} руб)</span>
        {/if}
    {/if}

{/function}

<div class="page-wrapper js-event-add-load" data-event="1" data-manager="{$manager->id}" data-order="{$order->order_id}"
     data-user="{$order->user_id}">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">

        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h4 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Заявка №{$order->order_id}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="offline_orders">Заявки</a></li>
                    <li class="breadcrumb-item active">Заявка №{$order->order_id}</li>
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
                            <div class="row">
                                <div class="col-4 col-md-3 col-lg-2">
                                    <h4 class="form-control-static">
                                        {if $order->client_status}
                                            {if $order->client_status == 'pk'}
                                                <span class="label label-success"
                                                      title="Клиент уже имеет погашенные займы">ПК</span>
                                            {elseif $order->client_status == 'crm'}
                                                <span class="label label-primary"
                                                      title="Клиент уже имеет погашенные займы в CRM">ПК CRM</span>
                                            {elseif $order->client_status == 'rep'}
                                                <span class="label label-warning"
                                                      title="Клиент уже подавал ранее заявки">Повтор</span>
                                            {elseif $order->client_status == 'nk'}
                                                <span class="label label-info" title="Новый клиент">Новая</span>
                                            {/if}
                                        {else}
                                            {if $order->have_crm_closed}
                                                <span class="label label-primary"
                                                      title="Клиент уже имеет погашенные займы в CRM">ПК CRM</span>
                                            {elseif $order->first_loan}
                                                <span class="label label-info" title="Новый клиент">Новая</span>
                                            {else}
                                                <span class="label label-warning"
                                                      title="Клиент уже подавал ранее заявки">Повтор</span>
                                            {/if}
                                        {/if}
                                    </h4>
                                </div>
                                <div class="col-8 col-md-3 col-lg-4">
                                    <h6 class="form-control-static float-left">
                                        дата заявки: {$order->date|date} {$order->date|time}
                                    </h6>
                                    {if $order->penalty_date}
                                        <h6 class="form-control-static float-left">
                                            дата решения: {$order->penalty_date|date} {$order->penalty_date|time}
                                        </h6>
                                    {/if}
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 ">
                                    {if $looker_link && !$order->offline}
                                        <a href="{$looker_link}" target="_blank" class="btn btn-info float-right"><i
                                                    class=" fas fa-address-book"></i> Смотреть ЛК</a>
                                    {/if}
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 ">
                                    <h6 class="js-order-manager text-right">
                                        {if in_array($manager->role, ['developer', 'admin', 'big_user'])}
                                            <select class="js-order-manager form-control"
                                                    data-order="{$order->order_id}" name="manager_id">
                                                <option value="0" {if !$order->manager_id}selected="selected"{/if}>Не
                                                    принята
                                                </option>
                                                {foreach $managers as $m}
                                                    <option value="{$m->id}"
                                                            {if $m->id == $order->manager_id}selected="selected"{/if}>{$m->name|escape}</option>
                                                {/foreach}
                                            </select>
                                        {else}
                                            {if $order->manager_id}
                                                {$managers[$order->manager_id]->name|escape}
                                            {/if}
                                        {/if}
                                    </h6>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-12 col-md-4 col-lg-3">
                                    <form action="{url}" class="js-order-item-form " id="fio_form">

                                        <input type="hidden" name="action" value="fio"/>
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                        <div class="border p-2 view-block">
                                            <h6>
                                                <a href="client/{$order->user_id}" title="Перейти в карточку клиента">
                                                    {$order->lastname|escape}
                                                    {$order->firstname|escape}
                                                    {$order->patronymic|escape}
                                                </a>
                                            </h6>
                                            <h4>
                                                <span>{$order->phone_mobile}</span>
                                                <button class="js-mango-call mango-call js-event-add-click"
                                                        data-phone="{$order->phone_mobile}" title="Выполнить звонок"
                                                        data-event="60" data-manager="{$manager->id}"
                                                        data-order="{$order->order_id}" data-user="{$order->user_id}">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </button>
                                                <button class="js-open-sms-modal mango-call {if $order->contract->sold}js-yuk{/if}"
                                                        data-user="{$order->user_id}" data-order="{$order->order_id}">
                                                    <i class=" far fa-share-square"></i>
                                                </button>
                                            </h4>
                                            <a href="javascript:void(0);"
                                               class="text-info js-edit-form edit-amount js-event-add-click"
                                               data-event="30" data-manager="{$manager->id}"
                                               data-order="{$order->order_id}" data-user="{$order->user_id}"><i
                                                        class=" fas fa-edit"></i></a>
                                        </div>

                                        <div class="edit-block hide">
                                            <div class="form-group mb-1">
                                                <input type="text" name="lastname" value="{$order->lastname}"
                                                       class="form-control" placeholder="Фамилия"/>
                                            </div>
                                            <div class="form-group mb-1">
                                                <input type="text" name="firstname" value="{$order->firstname}"
                                                       class="form-control" placeholder="Имя"/>
                                            </div>
                                            <div class="form-group mb-1">
                                                <input type="text" name="patronymic" value="{$order->patronymic}"
                                                       class="form-control" placeholder="Отчество"/>
                                            </div>
                                            <div class="form-group mb-1">
                                                <input type="text" name="phone_mobile" value="{$order->phone_mobile}"
                                                       class="form-control" placeholder="Телефон"/>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-success js-event-add-click"
                                                        data-event="40" data-manager="{$manager->id}"
                                                        data-order="{$order->order_id}" data-user="{$order->user_id}"><i
                                                            class="fa fa-check"></i> Сохранить
                                                </button>
                                                <button type="button" class="btn btn-inverse js-cancel-edit">Отмена
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="col-12 col-md-8 col-lg-6">
                                    <form action="{url}" class="mb-3 p-2 border js-order-item-form js-check-amount"
                                          id="amount_form">

                                        <input type="hidden" name="action" value="amount"/>
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="user_id" value="{$order->user_id}"/>
                                        {if $amount_error}
                                            <div class="text-danger pt-3">
                                                <ul>
                                                    {foreach $amount_error as $er}
                                                        <li>{$er}</li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        {/if}
                                        <div class="row view-block ">
                                            <div class="col-6 text-center">
                                                <h6>Сумма</h6>
                                                <h4 class="text-primary">{$order->amount|number_format:0:',':' '}
                                                    руб</h4>
                                            </div>
                                            <div class="col-6 text-center">
                                                <h6>Срок</h6>
                                                <h4 class="text-primary">{$order->period} {$order->period|plural:"день":"дней":"дня"}</h4>
                                            </div>
                                            {if $order->antirazgon_amount}
                                                <div class="col-12 text-center">
                                                    <h4 class="text-danger">Максимальная
                                                        сумма: {$order->antirazgon_amount} руб</h4>
                                                </div>
                                            {/if}
                                            {if $order->status <= 2 || in_array($manager->role, ['admin','developer'])}
                                                <a href="javascript:void(0);"
                                                   class="text-info js-edit-form edit-amount js-event-add-click"
                                                   data-event="31" data-manager="{$manager->id}"
                                                   data-order="{$order->order_id}" data-user="{$order->user_id}"><i
                                                            class=" fas fa-edit"></i></a>
                                                </h4>
                                            {/if}
                                        </div>

                                        <div class="row edit-block hide">
                                            <div class="col-6 col-md-3 text-center">
                                                <h6>Сумма</h6>
                                                <input type="text" class="form-control" name="amount"
                                                       value="{$order->amount}"/>
                                            </div>
                                            <div class="col-6 col-md-3 text-center">
                                                <h6>Период</h6>
                                                <input type="text" class="form-control" name="period"
                                                       value="{$order->period}"/>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-actions">
                                                    <h6>&nbsp;</h6>
                                                    <button type="submit" class="btn btn-success js-event-add-click"
                                                            data-event="41" data-manager="{$manager->id}"
                                                            data-order="{$order->order_id}"
                                                            data-user="{$order->user_id}"><i class="fa fa-check"></i>
                                                        Сохранить
                                                    </button>
                                                    <button type="button" class="btn btn-inverse js-cancel-edit">
                                                        Отмена
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    {if !$order->manager_id && $order->status == 0}
                                        <div class="pt-3 js-accept-order-block">
                                            <button class="btn btn-info btn-lg btn-block js-accept-order js-event-add-click"
                                                    data-event="10" data-manager="{$manager->id}"
                                                    data-order="{$order->order_id}" data-user="{$order->user_id}">
                                                <i class="fas fa-hospital-symbol"></i>
                                                <span>Принять</span>
                                            </button>
                                        </div>
                                    {/if}

                                    {if $order->status == 1 && $order->manager_id != $manager->id}
                                        <div class="pt-1 pb-2 js-accept-order-block">
                                            <button class="btn btn-info btn-block js-accept-order js-event-add-click"
                                                    data-event="11" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                <i class="fas fa-hospital-symbol"></i>
                                                <span>Перепринять</span>
                                            </button>
                                        </div>
                                    {/if}

                                    {if $order->status == 1}
                                        <div class="js-approve-reject-block {if !$order->manager_id}hide{/if}">
                                            <button class="btn btn-success btn-block js-approve-order js-event-add-click"
                                                    data-event="12" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Одобрить</span>
                                            </button>
                                            <button class="btn btn-danger btn-block js-reject-order js-event-add-click"
                                                    data-event="13" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                <i class="fas fa-times-circle"></i>
                                                <span>Отказать</span>
                                            </button>
                                        </div>
                                    {/if}

                                    <div class="js-order-status">
                                        {if $order->status == 2}
                                            <div class="card card-success mb-1">
                                                <div class="box text-center">
                                                    <h4 class="text-white mb-0">Одобрена</h4>
                                                </div>
                                            </div>
                                            <button class="btn btn-danger btn-block js-reject-order js-event-add-click"
                                                    data-event="13" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                <i class="fas fa-times-circle"></i>
                                                <span>Отказать</span>
                                            </button>
                                            <form class=" pt-1 js-confirm-contract">
                                                <div class="input-group">
                                                    <input type="hidden" name="contract_id" class="js-contract-id"
                                                           value="{$order->contract_id}"/>
                                                    <input type="hidden" name="phone" class="js-contract-phone"
                                                           value="{$order->phone_mobile}"/>
                                                    <input type="text" class="form-control js-contract-code"
                                                           placeholder="SMS код"
                                                           value="{if $is_developer}{$contract->accept_code}{/if}"/>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-info js-event-add-click" type="submit"
                                                                data-event="14" data-user="{$order->user_id}"
                                                                data-order="{$order->order_id}"
                                                                data-manager="{$manager->id}">Подтвердить
                                                        </button>
                                                    </div>
                                                </div>
                                                <a href="javascript:void(0);" class="js-sms-send"
                                                   data-contract="{$order->contract_id}">
                                                    <span>Отправить смс код</span>
                                                    <span class="js-sms-timer"></span>
                                                </a>
                                            </form>
                                        {/if}
                                        {if $order->status == 3}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Отказ</h4>
                                                    <small title="Причина отказа">
                                                        <i>{$reject_reasons[$order->reason_id]->admin_name}</i></small>
                                                    {if $order->antirazgon_date}
                                                        <br/>
                                                        <strong class="text-white">
                                                            <small>Мараторий до {$order->antirazgon_date|date}</small>
                                                        </strong>
                                                    {/if}
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 4}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Подписан</h4>
                                                    <h6>Договор {$contract->number}</h6>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 5}
                                            {if $contract->status == 4}
                                                <div class="card card-danger mb-1">
                                                    <div class="box text-center">
                                                        <h4 class="text-white">Просрочен</h4>
                                                        <h6>Договор {$contract->number}</h6>
                                                        <h6 class="text-center text-white">
                                                            Погашение: {$contract->loan_body_summ+$contract->loan_percents_summ+$contract->loan_charge_summ+$contract->loan_peni_summ}
                                                            руб
                                                        </h6>
                                                        <h6 class="text-center text-white">
                                                            Продление:
                                                            {if $contract->prolongation > 0 && !$contract->sold}
                                                                {$settings->prolongation_amount+$contract->loan_percents_summ+$contract->loan_charge_summ} руб
                                                            {else}
                                                                {$contract->loan_percents_summ+$contract->loan_charge_summ} руб
                                                            {/if}
                                                        </h6>
                                                    </div>
                                                </div>
                                            {else}
                                                <div class="card card-primary mb-1">
                                                    <div class="box text-center">
                                                        <h4 class="text-white">Выдан</h4>
                                                        <h6>Договор {$contract->number}</h6>
                                                        <h6 class="text-center text-white">
                                                            Погашение: {$contract->loan_body_summ+$contract->loan_percents_summ+$contract->loan_charge_summ+$contract->loan_peni_summ}
                                                            руб
                                                        </h6>
                                                        <h6 class="text-center text-white">
                                                            Продление:
                                                            {if $contract->prolongation > 0}
                                                                {$settings->prolongation_amount+$contract->loan_percents_summ} руб
                                                            {else}
                                                                {$contract->loan_percents_summ} руб
                                                            {/if}
                                                        </h6>
                                                    </div>
                                                </div>
                                            {/if}
                                            {if in_array('close_contract', $manager->permissions)}
                                                <button class="btn btn-danger btn-block js-open-close-form js-event-add-click"
                                                        data-event="15" data-user="{$order->user_id}"
                                                        data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                    Закрыть договор
                                                </button>
                                            {/if}
                                        {/if}
                                        {if $order->status == 6}
                                            <div class="card card-danger mb-1">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Не удалось выдать</h4>
                                                    <h6>Договор {$contract->number}</h6>
                                                    {if $p2p->response_xml}
                                                        <i>
                                                            <small>B2P: {$p2p->response_xml->message}</small>
                                                        </i>
                                                    {else}
                                                        <i>
                                                            <small>Нет ответа от B2P. <br/>Если повторить выдачу, это
                                                                может привести к двойной выдаче!
                                                            </small>
                                                        </i>
                                                    {/if}
                                                </div>
                                            </div>
                                            {if $have_newest_order}
                                                <div class="text-center">
                                                    <a href="order/{$have_newest_order}"><strong
                                                                class="text-danger text-center">У клиента есть новая
                                                            заявка</strong></a>
                                                </div>
                                            {else}
                                                {if in_array('repay_button', $manager->permissions)}
                                                    <button type="button"
                                                            class="btn btn-primary btn-block js-repay-contract js-event-add-click"
                                                            data-event="16" data-user="{$order->user_id}"
                                                            data-order="{$order->order_id}"
                                                            data-manager="{$manager->id}"
                                                            data-contract="{$contract->id}">Повторить выдачу
                                                    </button>
                                                {/if}
                                            {/if}
                                        {/if}

                                        {if $order->status == 7}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Погашен</h4>
                                                    <h6>Договор #{$contract->number}</h6>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 8}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Отказ клиента</h4>
                                                    <small title="Причина отказа">
                                                        <i>{$reject_reasons[$order->reason_id]->admin_name}</i></small>
                                                </div>
                                            </div>
                                        {/if}

                                        {if $contract->accept_code}
                                            <h4 class="text-danger mb-0">АСП: {$contract->accept_code}</h4>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <ul class="mt-2 nav nav-tabs" role="tablist" id="order_tabs">
                            <li class="nav-item">
                                <a class="nav-link active js-event-add-click" data-toggle="tab" href="#info" role="tab"
                                   aria-selected="false" data-event="20" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                    <span class="hidden-xs-down">Персональная информация</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-event-add-click" data-toggle="tab" href="#comments" role="tab"
                                   aria-selected="false" data-event="21" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                    <span class="hidden-xs-down">
                                            Комментарии {if $comments|count > 0}<span
                                                class="label label-rounded label-primary">{$comments|count}</span>{/if}
                                        </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-event-add-click" data-toggle="tab" href="#logs" role="tab"
                                   aria-selected="true" data-event="23" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-server"></i></span>
                                    <span class="hidden-xs-down">Логирование</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-event-add-click" data-toggle="tab" href="#operations" role="tab"
                                   aria-selected="true" data-event="24" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-list-ol"></i></span>
                                    <span class="hidden-xs-down">Операции</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link js-event-add-click" data-toggle="tab" href="#history" role="tab"
                                   aria-selected="true" data-event="25" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-save-alt"></i></span>
                                    <span class="hidden-xs-down">Кредитная история</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content tabcontent-border" id="order_tabs_content">
                            <div class="tab-pane active" id="info" role="tabpanel">
                                <div class="form-body p-2 pt-3">

                                    <div class="row">
                                        <div class="col-md-8 ">

                                            <!-- Контакты -->
                                            <form action="{url}"
                                                  class="mb-3 border js-order-item-form {if $penalties['personal'] && $penalties['personal']->status!=3}card-outline-danger{/if}"
                                                  id="personal_data_form">

                                                <input type="hidden" name="action" value="contactdata"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header card-success">
                                                    <span class="text-white ">Контакты</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='personal'}
                                                        <a href="javascript:void(0);"
                                                           class=" text-white js-edit-form js-event-add-click"
                                                           data-event="32" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a></h4>
                                                        </span>
                                                </h6>

                                                <div class="row pt-2 view-block {if $contactdata_error}hide{/if}">

                                                    {if $penalties['personal'] && (in_array($manager->permissions, ['add_penalty']) || $penalties['personal']->manager_id==$manager->id)}
                                                        <div class="col-md-12">
                                                            <div class="alert alert-danger m-2">
                                                                <h6 class="text-danger mb-1">{$penalty_types[$penalties['personal']->id]->name}</h6>
                                                                <small>{$penalties['personal']->comment}</small>
                                                            </div>
                                                        </div>
                                                    {/if}

                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Email:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->email|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Дата рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->birth|date}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Место
                                                                рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->birth_place|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Паспорт:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->passport_serial} {$order->subdivision_code}
                                                                    , от {$order->passport_date}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Кем выдан:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->passport_issued}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $order->viber_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md 4">Viber:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->viber_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if $order->telegram_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md 4">Telegram:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->telegram_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if $order->whatsapp_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md 4">WhatsApp:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->whatsapp_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
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
                                                            <input type="text" name="email" value="{$order->email}"
                                                                   class="form-control" placeholder=""/>
                                                            {if in_array('empty_email', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите Email!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-1 {if in_array('empty_birth', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Дата рождения</label>
                                                            <input type="text" name="birth" value="{$order->birth}"
                                                                   class="form-control" placeholder="" required="true"/>
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
                                                                   value="{$order->social}" placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-2 {if in_array('empty_birth_place', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Место рождения</label>
                                                            <input type="text" name="birth_place"
                                                                   value="{$order->birth_place|escape}"
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
                                                                   value="{$order->passport_serial}" placeholder=""
                                                                   required="true"/>
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
                                                                   value="{$order->passport_date}" placeholder=""
                                                                   required="true"/>
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
                                                                   value="{$order->subdivision_code}" placeholder=""
                                                                   required="true"/>
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
                                                                   value="{$order->passport_issued}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_passport_issued', (array)$contactdata_errors)}
                                                                <small class="form-control-feedback">Укажите кем выдан
                                                                    паспорт!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit"
                                                                    class="btn btn-success js-event-add-click"
                                                                    data-event="42" data-manager="{$manager->id}"
                                                                    data-order="{$order->order_id}"
                                                                    data-user="{$order->user_id}"><i
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

                                            {*}<!-- /Контактные лица -->
                                            <form action="{url}"
                                                  class="js-order-item-form mb-3 border {if $penalties['contactpersons'] && $penalties['contactpersons']->status!=3}card-outline-danger{/if}"
                                                  id="contact_persons_form">

                                                <input type="hidden" name="action" value="contacts"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Контактные лица</span>
                                                    <span class="float-right">

                                                            {penalty_button penalty_block='contactpersons'}
                                                        <a href="javascript:void(0);"
                                                           class="text-white js-edit-form js-event-add-click"
                                                           data-event="33" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a></h4>
                                                        </span>
                                                </h6>

                                                <div class="row view-block m-0 {if $contacts_error}hide{/if}">
                                                    <table class="table table-hover mb-0">
                                                        <tr>
                                                            <td>{$order->contact_person_name}</td>
                                                            <td>{$order->contact_person_relation}</td>
                                                            <td class="text-right">{$order->contact_person_phone}</td>
                                                            <td>
                                                                <button class="js-mango-call mango-call js-event-add-click"
                                                                        data-phone="{$order->contact_person_phone}"
                                                                        title="Выполнить звонок" data-event="61"
                                                                        data-manager="{$manager->id}"
                                                                        data-order="{$order->order_id}"
                                                                        data-user="{$order->user_id}">
                                                                    <i class="fas fa-mobile-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>{$order->contact_person2_name}</td>
                                                            <td>{$order->contact_person2_relation}</td>
                                                            <td class="text-right">{$order->contact_person2_phone}</td>
                                                            <td>
                                                                <button class="js-mango-call mango-call js-event-add-click"
                                                                        data-event="61" data-manager="{$manager->id}"
                                                                        data-order="{$order->order_id}"
                                                                        data-user="{$order->user_id}"
                                                                        data-phone="{$order->contact_person2_phone}"
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
                                                                {if in_array('empty_contact_person2_name', (array)$contacts_error)}
                                                                    <li>Укажите ФИО контакного лица 2!</li>
                                                                {/if}
                                                                {if in_array('empty_contact_person2_phone', (array)$contacts_error)}
                                                                    <li>Укажите тел. контакного лица 2!</li>
                                                                {/if}
                                                            </ul>
                                                        </div>
                                                    {/if}
                                                    <div class="col-md-4">
                                                        <div class="form-group {if in_array('empty_contact_person_name', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">ФИО контакного лица</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person_name"
                                                                   value="{$order->contact_person_name}" placeholder=""
                                                                   required="true"/>
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
                                                                        {if $order->contact_person_relation == ''}selected=""{/if}>
                                                                    Выберите значение
                                                                </option>
                                                                <option value="мать/отец"
                                                                        {if $order->contact_person_relation == 'мать/отец'}selected=""{/if}>
                                                                    мать/отец
                                                                </option>
                                                                <option value="муж/жена"
                                                                        {if $order->contact_person_relation == 'муж/жена'}selected=""{/if}>
                                                                    муж/жена
                                                                </option>
                                                                <option value="сын/дочь"
                                                                        {if $order->contact_person_relation == 'сын/дочь'}selected=""{/if}>
                                                                    сын/дочь
                                                                </option>
                                                                <option value="коллега"
                                                                        {if $order->contact_person_relation == 'коллега'}selected=""{/if}>
                                                                    коллега
                                                                </option>
                                                                <option value="друг/сосед"
                                                                        {if $order->contact_person_relation == 'друг/сосед'}selected=""{/if}>
                                                                    друг/сосед
                                                                </option>
                                                                <option value="иной родственник"
                                                                        {if $order->contact_person_relation == 'иной родственник'}selected=""{/if}>
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
                                                                   value="{$order->contact_person_phone}" placeholder=""
                                                                   required="true"/>
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
                                                                   value="{$order->contact_person2_name}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_contact_person2_name', (array)$contacts_error)}
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
                                                                    name="contact_person2_relation">
                                                                <option value=""
                                                                        {if $order->contact_person2_relation == ''}selected=""{/if}>
                                                                    Выберите значение
                                                                </option>
                                                                <option value="мать/отец"
                                                                        {if $order->contact_person2_relation == 'мать/отец'}selected=""{/if}>
                                                                    мать/отец
                                                                </option>
                                                                <option value="муж/жена"
                                                                        {if $order->contact_person2_relation == 'муж/жена'}selected=""{/if}>
                                                                    муж/жена
                                                                </option>
                                                                <option value="сын/дочь"
                                                                        {if $order->contact_person2_relation == 'сын/дочь'}selected=""{/if}>
                                                                    сын/дочь
                                                                </option>
                                                                <option value="коллега"
                                                                        {if $order->contact_person2_relation == 'коллега'}selected=""{/if}>
                                                                    коллега
                                                                </option>
                                                                <option value="друг/сосед"
                                                                        {if $order->contact_person2_relation == 'друг/сосед'}selected=""{/if}>
                                                                    друг/сосед
                                                                </option>
                                                                <option value="иной родственник"
                                                                        {if $order->contact_person2_relation == 'иной родственник'}selected=""{/if}>
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
                                                        <div class="form-group {if in_array('empty_contact_person2_phone', (array)$contacts_error)}has-danger{/if}">
                                                            <label class="control-label">Тел. контакного лица 2</label>
                                                            <input type="text" class="form-control"
                                                                   name="contact_person2_phone"
                                                                   value="{$order->contact_person2_phone}"
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
                                                            <button type="submit"
                                                                    class="btn btn-success js-event-add-click"
                                                                    data-event="43" data-manager="{$manager->id}"
                                                                    data-order="{$order->order_id}"
                                                                    data-user="{$order->user_id}"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <form action="{url}"
                                                  class="js-order-item-form mb-3 border {if $penalties['addresses'] && $penalties['addresses']->status!=3}card-outline-danger{/if}"
                                                  id="address_form">

                                                <input type="hidden" name="action" value="addresses"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Адрес</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='addresses'}
                                                        <a href="javascript:void(0);"
                                                           class="text-white js-edit-form js-event-add-click"
                                                           data-event="34" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a></h4>
                                                        </span>
                                                </h6>

                                                <div class="row view-block {if $addresses_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover mb-0">
                                                            <tr>
                                                                <td>Адрес прописки</td>
                                                                <td>
                                                                    {if $order->Regindex}{$order->Regindex}, {/if}
                                                                    {$order->Regregion} {$order->Regregion_shorttype},
                                                                    {if $order->Regcity}{$order->Regcity} {$order->Regcity_shorttype},{/if}
                                                                    {if $order->Regdistrict}{$order->Regdistrict} {$order->Regdistrict_shorttype},{/if}
                                                                    {if $order->Reglocality}{$order->Reglocality_shorttype} {$order->Reglocality},{/if}
                                                                    {$order->Regstreet} {$order->Regstreet_shorttype},
                                                                    д.{$order->Reghousing},
                                                                    {if $order->Regbuilding}стр. {$order->Regbuilding},{/if}
                                                                    {if $order->Regroom}кв.{$order->Regroom}{/if}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Адрес проживания</td>
                                                                <td>
                                                                    {if $order->Faktindex}{$order->Faktindex}, {/if}
                                                                    {$order->Faktregion} {$order->Faktregion_shorttype},
                                                                    {if $order->Faktcity}{$order->Faktcity} {$order->Faktcity_shorttype},{/if}
                                                                    {if $order->Faktdistrict}{$order->Faktdistrict} {$order->Faktdistrict_shorttype},{/if}
                                                                    {if $order->Faktlocality}{$order->Faktlocality_shorttype} {$order->Faktlocality},{/if}
                                                                    {if $order->Faktstreet}{$order->Faktstreet} {$order->Faktstreet_shorttype},{/if}
                                                                    д.{$order->Fakthousing},
                                                                    {if $order->Faktbuilding}стр. {$order->Faktbuilding},{/if}
                                                                    {if $order->Faktroom}кв.{$order->Faktroom}{/if}
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
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-region"
                                                                               name="Regregion"
                                                                               value="{$order->Regregion}"
                                                                               placeholder="" required="true"/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-region-type"
                                                                               name="Regregion_shorttype"
                                                                               value="{$order->Regregion_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
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
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-city"
                                                                               name="Regcity" value="{$order->Regcity}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-city-type"
                                                                               name="Regcity_shorttype"
                                                                               value="{$order->Regcity_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                                {if in_array('empty_regcity', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        город!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Район</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-district"
                                                                               name="Regdistrict"
                                                                               value="{$order->Regdistrict}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-district-type"
                                                                               name="Regdistrict_shorttype"
                                                                               value="{$order->Regdistrict_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Нас. пункт</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-locality"
                                                                               name="Reglocality"
                                                                               value="{$order->Reglocality}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-locality-type"
                                                                               name="Reglocality_shorttype"
                                                                               value="{$order->Reglocality_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_regstreet', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Улица</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-street"
                                                                               name="Regstreet"
                                                                               value="{$order->Regstreet}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-street-type"
                                                                               name="Regstreet_shorttype"
                                                                               value="{$order->Regstreet_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                                {if in_array('empty_regstreet', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        улицу!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Индекс</label>
                                                                <input type="text" class="form-control js-dadata-index"
                                                                       name="Regindex" value="{$order->Regindex}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group {if in_array('empty_reghousing', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Дом</label>
                                                                <input type="text" class="form-control js-dadata-house"
                                                                       name="Reghousing" value="{$order->Reghousing}"
                                                                       placeholder=""/>
                                                                {if in_array('empty_reghousing', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите дом!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Строение</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-building"
                                                                       name="Regbuilding" value="{$order->Regbuilding}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Квартира</label>
                                                                <input type="text" class="form-control js-dadata-room"
                                                                       name="Regroom" value="{$order->Regroom}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="row m-0 js-dadata-address">
                                                        <h6 class="col-12 nav-small-cap">Адрес проживания</h6>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_faktregion', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Область</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-region"
                                                                               name="Faktregion"
                                                                               value="{$order->Faktregion}"
                                                                               placeholder="" required="true"/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-region-type"
                                                                               name="Faktregion_shorttype"
                                                                               value="{$order->Faktregion_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
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
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-city"
                                                                               name="Faktcity"
                                                                               value="{$order->Faktcity}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-city-type"
                                                                               name="Faktcity_shorttype"
                                                                               value="{$order->Faktcity_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                                {if in_array('empty_faktcity', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        город!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Район</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-district"
                                                                               name="Faktdistrict"
                                                                               value="{$order->Faktdistrict}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-district-type"
                                                                               name="Faktdistrict_shorttype"
                                                                               value="{$order->Faktdistrict_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 ">
                                                                <label class="control-label">Нас. пункт</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-locality"
                                                                               name="Faktlocality"
                                                                               value="{$order->Faktlocality}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-locality-type"
                                                                               name="Faktlocality_shorttype"
                                                                               value="{$order->Faktlocality_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-1 {if in_array('empty_faktstreet', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Улица</label>
                                                                <div class="row">
                                                                    <div class="col-9">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-street"
                                                                               name="Faktstreet"
                                                                               value="{$order->Faktstreet}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <input type="text"
                                                                               class="form-control js-dadata-street-type"
                                                                               name="Faktstreet_shorttype"
                                                                               value="{$order->Faktstreet_shorttype}"
                                                                               placeholder=""/>
                                                                    </div>
                                                                </div>
                                                                {if in_array('empty_faktstreet', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите
                                                                        улицу!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Индекс</label>
                                                                <input type="text" class="form-control js-dadata-index"
                                                                       name="Faktindex" value="{$order->Faktindex}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group {if in_array('empty_fakthousing', (array)$addresses_error)}has-danger{/if}">
                                                                <label class="control-label">Дом</label>
                                                                <input type="text" class="form-control js-dadata-house"
                                                                       name="Fakthousing" value="{$order->Fakthousing}"
                                                                       placeholder=""/>
                                                                {if in_array('empty_fakthousing', (array)$addresses_error)}
                                                                    <small class="form-control-feedback">Укажите дом!
                                                                    </small>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Строение</label>
                                                                <input type="text"
                                                                       class="form-control js-dadata-building"
                                                                       name="Faktbuilding"
                                                                       value="{$order->Faktbuilding}" placeholder=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Квартира</label>
                                                                <input type="text" class="form-control js-dadata-room"
                                                                       name="Faktroom" value="{$order->Faktroom}"
                                                                       placeholder=""/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row m-0 mt-2 mb-2">
                                                        <div class="col-md-12">
                                                            <div class="form-actions">
                                                                <button type="submit"
                                                                        class="btn btn-success js-event-add-click"
                                                                        data-event="44" data-manager="{$manager->id}"
                                                                        data-order="{$order->order_id}"
                                                                        data-user="{$order->user_id}"><i
                                                                            class="fa fa-check"></i> Сохранить
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-inverse js-cancel-edit">Отмена
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form> {*}


                                            <!-- Данные о работе -->
                                            <form action="{url}"
                                                  class="border js-order-item-form mb-3 {if $penalties['work'] && $penalties['work']->status!=3}card-outline-danger{/if}"
                                                  id="work_data_form">

                                                <input type="hidden" name="action" value="work"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Данные о работе</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='work'}
                                                        <a href="javascript:void(0);"
                                                           class="text-white float-right js-edit-form js-event-add-click"
                                                           data-event="35" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a>
                                                        </span>
                                                </h6>

                                                <div class="row m-0 pt-2 view-block {if $work_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Состоит ли в
                                                                профсоюзе:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {if $order->profunion == 0}
                                                                        Не является членом профсоюза и не желает вступить
                                                                    {elseif $order->profunion == 1}
                                                                        Является членом профсоюза
                                                                    {elseif $order->profunion == 2}
                                                                        Не является членом профсоюза и желает вступить
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Работодатель:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {$order->workplace}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Доход:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {$order->income|number_format:0:',':' '}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Расход:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">
                                                                    {$order->expenses|number_format:0:',':' '}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- /Данные о работе -->

                                            <!-- Дополнительная информация -->
                                            <form action="{url}"
                                                  class="border js-order-item-form mb-3 {if $penalties['work'] && $penalties['work']->status!=3}card-outline-danger{/if}"
                                                  id="work_data_form">

                                                <input type="hidden" name="action" value="work"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Дополнительная информация</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='work'}
                                                        <a href="javascript:void(0);"
                                                           class="text-white float-right js-edit-form js-event-add-click"
                                                           data-event="35" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a>
                                                        </span>
                                                </h6>

                                                <div class="row m-0 pt-2 view-block {if $work_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-3">Состоит ли в
                                                                браке:</label>
                                                            <div class="col-md-6">
                                                                <p class="form-control-static">
                                                                    {if $order->sex == 0}
                                                                        Не состоит
                                                                    {elseif $order->sex == 1}
                                                                        Состоит
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $order->sex == 1}
                                                        <div class="col-md-6">
                                                            <div class="form-group  mb-0 row">
                                                                <label class="control-label col-md-6">ФИО
                                                                    супруга(-и):</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static">
                                                                        {$order->fio_spouse}
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
                                                                        {$order->phone_spouse}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if $order->prev_fio != null || $order->prev_fio}
                                                        <div class="col-md-6">
                                                            <div class="form-group  mb-0 row">
                                                                <label class="control-label col-md-6">Предыдущие
                                                                    ФИО:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static">
                                                                        {$order->prev_fio}
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
                                                                        {$order->fio_change_date|date}
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
                                                                    {if $order->foreign_flag == 0}
                                                                        Не является
                                                                    {elseif $order->foreign_flag == 1}
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
                                                                    {if $order->foreign_husb_wife == 0}
                                                                        Не является
                                                                    {elseif $order->foreign_husb_wife == 1}
                                                                        Является
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $order->foreign_husb_wife == 1}
                                                        <div class="col-md-12">
                                                            <div class="form-group  mb-2 row">
                                                                <label class="control-label col-md-8">ФИО
                                                                    супруга(-и):</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static">
                                                                        {$order->fio_public_spouse}
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
                                                                    {if $order->foreign_relative == 0}
                                                                        Не является
                                                                    {elseif $order->foreign_relative == 1}
                                                                        Является
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $order->foreign_relative == 1}
                                                        <div class="col-md-12">
                                                            <div class="form-group  mb-2 row">
                                                                <label class="control-label col-md-8">ФИО
                                                                    родственника:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static">
                                                                        {$order->fio_relative}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>
                                            </form>
                                            <!-- /Данные о работе -->

                                            <!-- Документы -->
                                            <form action="{url}"
                                                  class="border"
                                                  id="documents_form">

                                                <input type="hidden" name="action" value="work"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Документы</span>
                                                    <input style="margin-left: 30px" type="button"
                                                           class="btn btn-primary get-docs"
                                                           data-order="{$order->order_id}"
                                                           value="Сформировать документы">

                                                </h6><br>
                                                {if !empty($documents)}
                                                    {foreach $documents as $document}
                                                        <div style="width: 100%!important; height: 30px; margin-left: 15px; display: flex; vertical-align: middle"
                                                             id="{$document->id}">
                                                            <div class="form-group" style="width: 55%!important">
                                                                <label class="control-label">{$document->name}</label>
                                                            </div>
                                                            <div>
                                                                <a target="_blank"
                                                                   href="http://51.250.26.168/document?id={$document->id}&action=download_file"><input
                                                                            type="button"
                                                                            class="btn btn-outline-success download_doc"
                                                                            value="Сохранить"></a>
                                                            </div>
                                                            <div style="margin-left: 10px">
                                                                <a target="_blank"
                                                                   href="http://51.250.26.168/document/{$document->id}"><input
                                                                            type="button"
                                                                            class="btn btn-outline-warning print_doc"
                                                                            value="Распечатать"></a>
                                                            </div>
                                                            <div class="btn-group"
                                                                 style="margin-left: 10px; height: 35px">
                                                                {if $document->scan}
                                                                    <a target="_blank"
                                                                       style="text-decoration: none!important;"
                                                                       href="javascript:void(0);"
                                                                       onclick="window.open('{$config->back_url}/files/users/{$document->scan->name}');">
                                                                        <input type="button"
                                                                               class="btn btn-outline-info {$scan->type}"
                                                                               value="Скан">
                                                                    </a>
                                                                {/if}
                                                                <button type="button"
                                                                        class="btn btn-outline-info dropdown-toggle dropdown-toggle-split"
                                                                        data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                    <span class="sr-only">Toggle Dropdown</span>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <input type="file" name="new_scan"
                                                                           id="{$document->template}"
                                                                           class="new_scan"
                                                                           data-user="{$order->user_id}"
                                                                           data-order="{$order->order_id}"
                                                                           value="" style="display:none" multiple/>
                                                                    <label for="{$document->template}"
                                                                           class="dropdown-item">Приложить
                                                                        скан</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr style="width: 100%; size: 2px">
                                                    {/foreach}
                                                {/if}
                                            </form>
                                        </div>
                                        <div class="col-md-4 ">

                                            {if $order->autoretry_result}
                                                <div class="card mb-1 {if $order->autoretry_summ}card-success{else}card-danger{/if}">
                                                    <div class="box ">
                                                        <h6 class="card-title mb-0 text-white text-center">
                                                            Автоповтор</h6>
                                                        <div class="text-white text-center">
                                                            <small class="text-white">
                                                                {$order->autoretry_result}
                                                            </small>
                                                        </div>
                                                        {if $order->autoretry_summ && $order->status == 1}
                                                            <button data-order="{$order->order_id}"
                                                                    class="mt-2 btn btn-block btn-info btn-sm js-autoretry-accept js-event-add-click"
                                                                    data-event="17" data-manager="{$manager->id}"
                                                                    data-order="{$order->order_id}"
                                                                    data-user="{$order->user_id}">
                                                                Выдать {$order->autoretry_summ} руб
                                                            </button>
                                                        {/if}
                                                    </div>
                                                </div>
                                            {/if}
                                            <div class="mb-3 border  {if $penalties['scorings'] && $penalties['scorings']->status!=3}card-outline-danger{/if}">
                                                <h6 class=" card-header">
                                                    <span class="text-white ">Скоринги</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='scorings'}
                                                        {if ($order->status == 1 && ($manager->id == $order->manager_id)) || $is_developer}
                                                            <a class="text-white js-run-scorings" data-type="all"
                                                               data-order="{$order->order_id}"
                                                               href="javascript:void(0);">
                                                                <i class="far fa-play-circle"></i>
                                                            </a>
                                                        {/if}
                                                        </span>
                                                </h6>
                                                <div class="message-box js-scorings-block {if $need_update_scorings}js-need-update{/if}"
                                                     data-order="{$order->order_id}">

                                                    {foreach $scoring_types as $scoring_type}
                                                        
                                                        <div class="pl-2 pr-2 {if $scorings[$scoring_type->name]->status == 'new'}bg-light-warning{elseif $scorings[$scoring_type->name]->success}bg-light-success{else}bg-light-danger{/if}">
                                                            <div class="row {if !$scoring_type@last}border-bottom{/if}">
                                                                <div class="col-12 col-sm-12 pt-2">
                                                                    <h6 class="float-left">
                                                                        {$scoring_type->title}
                                                                        {if $scoring_type->name == 'fssp'}
                                                                            {if $scorings[$scoring_type->name]->found_46}
                                                                                <span class="label label-danger">46</span>
                                                                            {/if}
                                                                            {if $scorings[$scoring_type->name]->found_47}
                                                                                <span class="label label-danger">47</span>
                                                                            {/if}
                                                                        {/if}
                                                                    </h6>

                                                                    {if $scorings[$scoring_type->name]->status == 'new'}
                                                                        <span class="label label-warning float-right">Ожидание</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'process'}
                                                                        <span class="label label-info label-sm float-right">Выполняется</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'error' || $scorings[$scoring_type->name]->status == 'stopped'}
                                                                        <span class="label label-danger label-sm float-right">Ошибка</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'completed'}
                                                                        {if $scorings[$scoring_type->name]->success}
                                                                            <span class="label label-success label-sm float-right">Пройден</span>
                                                                        {else}
                                                                            <span class="label label-danger float-right">Не пройден</span>
                                                                        {/if}
                                                                    {/if}
                                                                </div>
                                                                <div class="col-8 col-sm-8 pb-2">
                                                                        <span class="mail-desc"
                                                                              title="{$scorings[$scoring_type->name]->string_result}">
                                                                            {$scorings[$scoring_type->name]->string_result}
                                                                        </span>
                                                                    <span class="time">
                                                                            {if $scorings[$scoring_type->name]->created}
                                                                                {$scorings[$scoring_type->name]->created|date} {$scorings[$scoring_type->name]->created|time}
                                                                            {/if}
                                                                        {if $scoring_type->name == 'fssp'}
                                                                            <a href="javascript:void(0);"
                                                                               class="js-get-fssp-info float-right"
                                                                               data-scoring="{$scorings[$scoring_type->name]->id}">Подробнее</a>
                                                                        {/if}
                                                                        {if $scoring_type->name == 'efrsb' && $scorings[$scoring_type->name]->body}
                                                                            {foreach $scorings[$scoring_type->name]->body as $efrsb_link}
                                                                                <a href="{$efrsb_link}"
                                                                                   target="_blank"
                                                                                   class="float-right">Подробнее</a>
                                                                            {/foreach}
                                                                        {/if}
                                                                        {if $scoring_type->name == 'nbki'}
                                                                            <a href="http://45.147.176.183/nal-plus-nbki/{$scorings[$scoring_type->name]->id}?api=F1h1Hdf9g_h"
                                                                               target="_blank">Подробнее</a>
                                                                        {/if}
                                                                        </span>
                                                                </div>
                                                                <div class="col-4 col-sm-4 pb-2">
                                                                    {if $order->status < 2 || $is_developer}
                                                                        {if $scorings[$scoring_type->name]->status == 'new' || $scorings[$scoring_type->name]->status == 'process' }
                                                                            <a class="btn-load text-info run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <div class="spinner-border text-info"
                                                                                     role="status"></div>
                                                                            </a>
                                                                        {elseif $scorings[$scoring_type->name]}
                                                                            <a class="btn-load text-info js-run-scorings run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <i class="fas fa-undo"></i>
                                                                            </a>
                                                                        {else}
                                                                            <a class="btn-load {if in_array(, $audit_types)}loading{/if} text-info js-run-scorings run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <i class="far fa-play-circle"></i>
                                                                            </a>
                                                                        {/if}
                                                                    {/if}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </div>

                                            <form class="mb-4 border">
                                                <h6 class="card-header text-white">
                                                    <span>ИНН</span>
                                                    <span class="float-right">
                                                                <a href="" class="text-white inn-edit"><i
                                                                            class=" fas fa-edit"></i></a>
                                                        </span>
                                                </h6>
                                                <div class="row view-block p-2 inn-front">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7 inn-number">{$user->inn}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="inn-editor" style="display: none;">
                                                    <br>
                                                    <div>
                                                        <input type="text" class="form-control inn-number-edit"
                                                               value="{$user->inn}">

                                                    </div>
                                                    <br>
                                                    <div>
                                                        <input type="button" class="btn btn-success inn-edit-success"
                                                               value="Сохранить">
                                                        <input type="button" style="float: right;"
                                                               class="btn btn-inverse inn-edit-cancel" value="Отмена">
                                                    </div>
                                                </div>
                                            </form>

                                            <form class="mb-4 border">
                                                <h6 class="card-header text-white">
                                                    <span>СНИЛС</span>
                                                    <span class="float-right">
                                                                <a href="" class="text-white snils-edit"><i
                                                                            class=" fas fa-edit"></i></a>
                                                        </span>
                                                </h6>
                                                <div class="row view-block p-2 snils-front">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7 snils-number">{$user->snils}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="snils-editor" style="display: none;">
                                                    <br>
                                                    <div>
                                                        <input type="text" class="form-control snils-number-edit"
                                                               value="{$user->snils}">

                                                    </div>
                                                    <br>
                                                    <div>
                                                        <input type="button" class="btn btn-success snils-edit-success"
                                                               value="Сохранить">
                                                        <input type="button" style="float: right;"
                                                               class="btn btn-inverse snils-edit-cancel" value="Отмена">
                                                    </div>
                                                </div>
                                            </form>
                                            <form class="mb-3 border js-order-item-form">
                                                <h6 class="card-header text-white">
                                                    <span>Расчетный счет</span>
                                                    <span class="float-right"><a class="text-white cors-edit" href=""><i
                                                                    class=" fas fa-edit"></i></a></span>
                                                </h6>
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
                                                                <label class="control-label col-md-12 fio-hold-front">{$user->fio_acc_holder}</label>
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
                                                                <label class="control-label col-md-12 acc-num-front">{$user->account_number}</label>
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
                                                                <label class="control-label col-md-12 bank-name-front">{$user->bank_name}</label>
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
                                                                <label class="control-label col-md-12 bik-front">{$user->bik_bank}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="cors-editor" style="display: none;">
                                                    <div style="width: 100%">
                                                        <label class="control-label">ФИО держателя счета</label>
                                                        <input class="form-control fio-hold-edit"
                                                               style="width: 350px; margin-left: 25px"
                                                               type="text" name="fio_acc_holder"
                                                               value="{$order->fio_acc_holder}"/><br>
                                                        <label class="control-label">Номер счета</label><br>
                                                        <input class="form-control acc-num-edit"
                                                               style="width: 350px; margin-left: 25px"
                                                               type="text" name="account_number"
                                                               value="{$order->account_number}"/>
                                                    </div>
                                                    <div style="width: 100%">
                                                        <label class="control-label">Наименование банка</label>
                                                        <input class="form-control bank-name-edit"
                                                               style="width: 350px; margin-left: 25px"
                                                               type="text" name="bank_name"
                                                               value="{$order->bank_name}"/><br>
                                                        <label class="control-label">БИК банка</label><br>
                                                        <input class="form-control bik-edit"
                                                               style="width: 180px; margin-left: 25px"
                                                               type="text" name="bik_bank" value="{$order->bik_bank}"/>
                                                    </div>
                                                    <br>
                                                    <div>
                                                        <input type="button" class="btn btn-success cors-edit-success"
                                                               value="Сохранить">
                                                        <input type="button" style="float: right;"
                                                               class="btn btn-inverse cors-edit-cancel" value="Отмена">
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                    <!-- -->

                                    <form action="{url}"
                                          class="border js-order-item-form mb-3 {if $penalties['images'] && $penalties['images']->status!=3}card-outline-danger{/if}"
                                          id="images_form">

                                        <input type="hidden" name="action" value="images"/>
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                        <h6 class="card-header">
                                            <span class="text-white">Фото</span>
                                            <span class="float-right">
                                                    {penalty_button penalty_block='images'}
                                                </span>
                                        </h6>

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
                                                    {elseif $file->status == 4}
                                                        {$item_class="border-info border"}
                                                        {$ribbon_class="ribbon-info"}
                                                        {$ribbon_icon="fab fa-cloudversify"}
                                                    {/if}
                                                    <li class="order-image-item ribbon-wrapper rounded-sm border {$item_class}">
                                                        <a class="image-popup-fit-width js-event-add-click"
                                                           href="javascript:void(0);"
                                                           onclick="window.open('{$config->back_url}/files/users/{$file->name}');"
                                                           data-event="50" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}">
                                                            <span class="badge badge-primary"
                                                                  style="position:absolute;right:10px;top:10px">
                                                                {if $file->type == 'passport1'}Паспорт1
                                                                {elseif $file->type == 'passport2'}Паспорт2
                                                                {elseif $file->type == 'card'}Карта
                                                                {elseif $file->type == 'face'}Селфи
                                                                {else}Нет типа{/if}
                                                            </span>
                                                            <div class="ribbon ribbon-corner {$ribbon_class}"><i
                                                                        class="{$ribbon_icon}"></i></div>
                                                            <img src="{$config->back_url}/files/users/{$file->name}"
                                                                 alt="" class="img-responsive" style=""/>
                                                        </a>
                                                        {if $order->status == 1 && ($manager->id == $order->manager_id)}
                                                            <div class="order-image-actions">
                                                                <div class="dropdown mr-1 show ">
                                                                    <button type="button"
                                                                            class="btn {if $file->status==2}btn-success{elseif $file->status==3}btn-danger{else}btn-secondary{/if} dropdown-toggle"
                                                                            id="dropdownMenuOffset"
                                                                            data-toggle="dropdown" aria-haspopup="true"
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
                                                                            <button class="btn btn-sm btn-block btn-outline-success js-image-accept js-event-add-click"
                                                                                    data-event="51"
                                                                                    data-manager="{$manager->id}"
                                                                                    data-order="{$order->order_id}"
                                                                                    data-user="{$order->user_id}"
                                                                                    data-id="{$file->id}" type="button">
                                                                                <i class="fas fa-check-circle"></i>
                                                                                <span>Принять</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="p-1 dropdown-item">
                                                                            <button class="btn btn-sm btn-block btn-outline-danger js-image-reject js-event-add-click"
                                                                                    data-event="52"
                                                                                    data-manager="{$manager->id}"
                                                                                    data-order="{$order->order_id}"
                                                                                    data-user="{$order->user_id}"
                                                                                    data-id="{$file->id}" type="button">
                                                                                <i class="fas fa-times-circle"></i>
                                                                                <span>Отклонить</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="p-1 pt-3 dropdown-item">
                                                                            <button class="btn btn-sm btn-block btn-danger js-image-remove js-event-add-click"
                                                                                    data-event="53"
                                                                                    data-manager="{$manager->id}"
                                                                                    data-order="{$order->order_id}"
                                                                                    data-user="{$order->user_id}"
                                                                                    data-id="{$file->id}" type="button">
                                                                                <i class="fas fa-trash"></i>
                                                                                <span>Удалить</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        {/if}
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
                                                                    <input type="text" class="js-file-status"
                                                                           id="status_{$file->id}"
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
                                    <form method="POST" enctype="multipart/form-data">

                                        <div class="form_file_item">
                                            <input type="file" name="new_file" class="new_file" id="new_file"
                                                   data-user="{$order->user_id}" value="" style="display:none"/>
                                            <label for="new_file" class="btn btn-large btn-primary">
                                                <i class="fa fa-plus-circle"></i>
                                                <span>Добавить</span>
                                            </label>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            <!-- Комментарии -->
                            <div class="tab-pane p-3" id="comments" role="tabpanel">

                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="float-left">Комментарии к клиенту</h4>
                                        <button class="btn float-right hidden-sm-down btn-success js-open-comment-form">
                                            <i class="mdi mdi-plus-circle"></i>
                                            Добавить
                                        </button>
                                    </div>
                                    <hr class="m-3"/>
                                    <div class="col-12">
                                        {if $comments}
                                            <div class="message-box">
                                                <div class="message-widget">
                                                    {foreach $comments as $comment}
                                                        <a href="javascript:void(0);">
                                                            <div class="user-img">
                                                                <span class="round">{$comment->letter|escape}</span>
                                                            </div>
                                                            <div class="mail-contnet">
                                                                <div class="clearfix">
                                                                    <h6>{$managers[$comment->manager_id]->name|escape}</h6>
                                                                    {if $comment->official}
                                                                        <span class="label label-success">Оффициальный</span>
                                                                    {/if}
                                                                    {if $comment->organization=='mkk'}
                                                                        <span class="label label-info">МКК</span>
                                                                    {/if}
                                                                    {if $comment->organization=='yuk'}
                                                                        <span class="label label-danger">ЮК</span>
                                                                    {/if}
                                                                </div>
                                                                <span class="mail-desc">
                                                                {$comment->text|nl2br}
                                                            </span>
                                                                <span class="time">{$comment->created|date} {$comment->created|time}</span>
                                                            </div>

                                                        </a>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        {/if}

                                        {if $comments_1c}
                                            <h4>Комментарии из 1С</h4>
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
                            <!-- /Комментарии -->

                            <!-- Документы -->
                            <div class="tab-pane p-3" id="documents" role="tabpanel">
                                {if $documents}
                                    <table class="table">
                                        {foreach $documents as $document}
                                            <tr>
                                                <td class="text-info">
                                                    <a target="_blank"
                                                       href="{$config->front_url}/document/{$document->user_id}/{$document->id}">
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
                            <!-- /Документы -->


                            <div class="tab-pane p-3" id="logs" role="tabpanel">

                                <ul class="nav nav-pills mt-4 mb-4">
                                    <li class=" nav-item"><a href="#eventlogs" class="nav-link active" data-toggle="tab"
                                                             aria-expanded="false">События</a></li>
                                    <li class="nav-item"><a href="#changelogs" class="nav-link" data-toggle="tab"
                                                            aria-expanded="false">Данные</a></li>
                                </ul>

                                <div class="tab-content br-n pn">
                                    <div id="eventlogs" class="tab-pane active">
                                        <h4>События</h4>
                                        {if $eventlogs}
                                            <table class="table table-hover ">
                                                <tbody>
                                                {foreach $eventlogs as $eventlog}
                                                    <tr class="">
                                                        <td>
                                                            <span>{$eventlog->created|date}</span>
                                                            {$eventlog->created|time}
                                                        </td>
                                                        <td>
                                                            {$events[$eventlog->event_id]|escape}
                                                        </td>
                                                        <td>
                                                            <a href="manager/{$eventlog->manager_id}">{$managers[$eventlog->manager_id]->name|escape}</a>
                                                        </td>
                                                    </tr>
                                                {/foreach}
                                                </tbody>
                                            </table>
                                            <a href="http://45.147.176.183/get/html_to_sheet?name={$order->order_id}&code=3Tfiikdfg6">...</a>
                                        {else}
                                            Нет записей
                                        {/if}

                                    </div>

                                    <div id="changelogs" class="tab-pane">
                                        <h4>Изменение данных</h4>
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
                                        {else}
                                            Нет записей
                                        {/if}

                                    </div>

                                </div>

                            </div>

                            <div class="tab-pane p-3" id="operations" role="tabpanel">
                                {if $contract_operations}
                                    <table class="table table-hover ">
                                        <tbody>
                                        {foreach $contract_operations as $operation}
                                            <tr class="
                                                    {if in_array($operation->type, ['PAY'])}table-success{/if} 
                                                    {if in_array($operation->type, ['PERCENTS', 'CHARGE', 'PENI'])}table-danger{/if} 
                                                    {if in_array($operation->type, ['P2P'])}table-info{/if} 
                                                    {if in_array($operation->type, ['INSURANCE'])}table-warning{/if}
                                                ">
                                                <td>
                                                    {*}
                                                    <div class="button-toggle-wrapper">
                                                        <button class="js-open-order button-toggle" data-id="{$changelog->id}" type="button" title="Подробнее"></button>
                                                    </div>
                                                    {*}
                                                    <span>{$operation->created|date}</span>
                                                    {$operation->created|time}
                                                </td>
                                                <td>
                                                    {if $operation->type == 'P2P'}Выдача займа{/if}
                                                    {if $operation->type == 'PAY'}
                                                        {if $operation->transaction->prolongation}
                                                            Пролонгация
                                                        {else}
                                                            Оплата займа
                                                        {/if}
                                                    {/if}
                                                    {if $operation->type == 'RECURRENT'}Оплата займа{/if}
                                                    {if $operation->type == 'PERCENTS'}Начисление процентов{/if}
                                                    {if $operation->type == 'INSURANCE'}Страховка{/if}
                                                    {if $operation->type == 'CHARGE'}Ответственность{/if}
                                                    {if $operation->type == 'PENI'}Пени{/if}
                                                </td>
                                                <td>
                                                    {$operation->amount} руб
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                {else}
                                    <h4>Нет операций</h4>
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
                                                        <h4>Заявки</h4>
                                                        <table class="table">
                                                            <tr>
                                                                <th>Дата</th>
                                                                <th>Заявка</th>
                                                                <th>Договор</th>
                                                                <th class="text-center">Сумма</th>
                                                                <th class="text-center">Период</th>
                                                                <th class="text-right">Статус</th>
                                                            </tr>
                                                            {foreach $orders as $o}
                                                                {if $o->contract->type != 'onec'}
                                                                    <tr>
                                                                        <td>{$o->date|date} {$o->date|time}</td>
                                                                        <td>
                                                                            <a href="order/{$o->order_id}"
                                                                               target="_blank">{$o->order_id}</a>
                                                                        </td>
                                                                        <td>
                                                                            {$o->contract->number}
                                                                        </td>
                                                                        <td class="text-center">{$o->amount}</td>
                                                                        <td class="text-center">{$o->period}</td>
                                                                        <td class="text-right">
                                                                            {$order_statuses[$o->status]}
                                                                            {if $o->contract->status==3}
                                                                                <br/>
                                                                                <small>{$o->contract->close_date|date} {$o->contract->close_date|time}</small>{/if}
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
                                                        <h4>Кредитная история 1С</h4>
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


<div id="modal_reject_reason" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Отказать в выдаче кредита?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">


                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#reject_mko" role="tab"
                                   aria-controls="home5" aria-expanded="true" aria-selected="true">
                                    <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                    <span class="hidden-xs-down">Отказ МКО</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#reject_client" role="tab"
                                   aria-controls="profile" aria-selected="false">
                                    <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                    <span class="hidden-xs-down">Отказ клиента</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tabcontent-border p-3" id="myTabContent">
                            <div role="tabpanel" class="tab-pane fade active show" id="reject_mko"
                                 aria-labelledby="home-tab">
                                <form class="js-reject-form">
                                    <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                    <input type="hidden" name="action" value="reject_order"/>
                                    <input type="hidden" name="status" value="3"/>
                                    <div class="form-group">
                                        <label for="admin_name" class="control-label">Выберите причину отказа:</label>
                                        <select name="reason" class="form-control">
                                            {foreach $reject_reasons as $reject_reason}
                                                {if $reject_reason->type == 'mko'}
                                                    <option value="{$reject_reason->id|escape}">{$reject_reason->admin_name|escape}</option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-action clearfix">
                                        <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                data-dismiss="modal">Отменить
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg float-right waves-effect waves-light">Да,
                                            отказать
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="reject_client" role="tabpanel" aria-labelledby="profile-tab">
                                <form class="js-reject-form">
                                    <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                    <input type="hidden" name="action" value="reject_order"/>
                                    <input type="hidden" name="status" value="8"/>
                                    <div class="form-group">
                                        <label for="admin_name" class="control-label">Выберите причину отказа:</label>
                                        <select name="reason" class="form-control">
                                            {foreach $reject_reasons as $reject_reason}
                                                {if $reject_reason->type == 'client'}
                                                    <option value="{$reject_reason->id|escape}">{$reject_reason->admin_name|escape}</option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-action clearfix">
                                        <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                data-dismiss="modal">Отменить
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg float-right waves-effect waves-light">Да,
                                            отказать
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_add_comment" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить комментарий</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_add_comment" action="order/{$order->order_id}">

                    <input type="hidden" name="order_id" value="{$order->order_id}"/>
                    <input type="hidden" name="user_id" value="{$order->user_id}"/>
                    <input type="hidden" name="block" value=""/>
                    <input type="hidden" name="action" value="add_comment"/>

                    <div class="alert" style="display:none"></div>

                    <div class="form-group">
                        <label for="name" class="control-label">Комментарий:</label>
                        <textarea class="form-control" name="text"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                            <input class="custom-control-input" type="checkbox" name="official" value="1"
                                   id="official"/>
                            <label for="official" class="custom-control-label">Оффициальный:</label>
                        </div>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect js-event-add-click" data-event="70"
                                data-manager="{$manager->id}" data-order="{$order->order_id}"
                                data-user="{$order->user_id}" data-dismiss="modal">Отмена
                        </button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_close_contract" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Закрыть договор</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_close_contract" action="order/{$order->order_id}">

                    <input type="hidden" name="order_id" value="{$order->order_id}"/>
                    <input type="hidden" name="user_id" value="{$order->user_id}"/>
                    <input type="hidden" name="action" value="close_contract"/>

                    <div class="alert" style="display:none"></div>

                    <div class="form-group">
                        <label for="close_date" class="control-label">Дата закрытия:</label>
                        <input type="text" class="form-control" name="close_date" required="" placeholder="ДД.ММ.ГГГГ"
                               value="{''|date}"/>
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">Комментарий:</label>
                        <textarea class="form-control" id="comment" name="comment" required=""></textarea>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_fssp_info" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Результаты проверки</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <th>Номер, дата</th>
                        <th>Документ</th>
                        <th>Производство</th>
                        <th>Департамент</th>
                        <th>Закрыт</th>
                    </tr>
                    <tbody class="js-fssp-info-result">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="loan_operations" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="loan_operations_title">Операции по договору</h6>
                <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="modal_add_penalty" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Оштрафовать</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_add_penalty" action="order/{$order->order_id}">

                    <input type="hidden" name="order_id" value="{$order->order_id}"/>
                    <input type="hidden" name="manager_id" value="{$order->manager_id}"/>
                    <input type="hidden" name="control_manager_id" value="{$manager->id}"/>
                    <input type="hidden" name="block" value=""/>
                    <input type="hidden" name="action" value="add_penalty"/>

                    <div class="alert" style="display:none"></div>

                    <div class="form-group">
                        <label for="close_date" class="control-label">Причина:</label>
                        <select name="type_id" class="form-control">
                            <option value=""></option>
                            {foreach $penalty_types as $t}
                                <option value="{$t->id}">{$t->name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">Комментарий:</label>
                        <textarea class="form-control" id="comment" name="comment"></textarea>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_send_sms" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Отправить смс-сообщение?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">


                <div class="card">
                    <div class="card-body">

                        <div class="tab-content tabcontent-border p-3" id="myTabContent">
                            <div role="tabpanel" class="tab-pane fade active show" id="waiting_reason"
                                 aria-labelledby="home-tab">
                                <form class="js-sms-form">
                                    <input type="hidden" name="user_id" value=""/>
                                    <input type="hidden" name="order_id" value=""/>
                                    <input type="hidden" name="yuk" value=""/>
                                    <input type="hidden" name="action" value="send_sms"/>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Выберите шаблон сообщения:</label>
                                        <select name="template_id" class="form-control">
                                            {foreach $sms_templates as $sms_template}
                                                <option value="{$sms_template->id}"
                                                        title="{$sms_template->template|escape}">
                                                    {$sms_template->name|escape} ({$sms_template->template})
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-action clearfix">
                                        <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                data-dismiss="modal">Отменить
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg float-right waves-effect waves-light">Да,
                                            отправить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>