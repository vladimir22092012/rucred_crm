{$meta_title="Тикет №`$ticket->id`" scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.js"></script>
    <script>
        $(function () {
            $('.accept_ticket').on('click', function (e) {
                e.preventDefault();

                let ticket_id = $(this).attr('data-ticket');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'accept_ticket',
                        ticket_id: ticket_id
                    },
                    success: function (resp) {
                        if (resp == 'error') {
                            Swal.fire({
                                title: 'Ошибка смена статуса',
                                showCancelButton: false,
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $('.close_ticket, .return_ticket').on('click', function (e) {

                let ticket_id = $(this).attr('data-ticket');
                let action = 'return_ticket';

                if ($(this).hasClass('close_ticket'))
                    action = 'close_ticket';


                $.ajax({
                    method: 'POST',
                    data: {
                        action: action,
                        ticket_id: ticket_id
                    },
                    success: function (resp) {
                        if (resp == 'error') {
                            Swal.fire({
                                title: 'Ошибка смена статуса',
                                showCancelButton: false,
                                confirmButtonText: 'ОК'
                            });
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $('.add_message').on('click', function () {
                $('#add-message-modal').modal();
            });

            $('.send_message').on('click', function (e) {
                e.preventDefault();

                let docs = $('#docs').files;
                let form_data = new FormData($('#add_message_form')[0]);
                form_data.append('docs', docs);

                $.ajax({
                    method: 'POST',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        location.reload();
                    }
                })
            });

            setInterval(function () {
                if ($('#accept_ticker').hasClass('btn btn-outline-success accept_ticket')) {
                    $('#accept_ticker').removeClass('btn btn-outline-success accept_ticket');
                    $('#accept_ticker').addClass('btn btn-success accept_ticket');
                } else {
                    $('#accept_ticker').removeClass('btn btn-success accept_ticket');
                    $('#accept_ticker').addClass('btn btn-outline-success accept_ticket');
                }
            }, 300);
        });
    </script>
{/capture}

{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css" rel="stylesheet"/>
{/capture}

<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
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
                <h3 class="text-themecolor mb-0 mt-0">Тикет</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/tickets?in=true">Коммуникации</a></li>
                    <li class="breadcrumb-item active">Тикет № {$ticket->id}</li>
                </ol>
            </div>
        </div>
        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-body">
                        <div class="tab-content tabcontent-border" id="order_tabs_content">
                            <div class="tab-pane active" id="info" role="tabpanel">
                                <div class="form-body p-2 pt-3">

                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <!-- Контакты -->
                                            <form class="mb-3 border">
                                                <h6 class="card-header card-success">
                                                    <span class="text-white" style="width: 20%">{$ticket->head}</span>
                                                    {if !empty($ticket->order_id)}
                                                        <span class="text-white" style="width: 20%; margin-left: 50px">Номер заявки: <a
                                                                    href="{if $offline == 1}/offline_order/{else}/order/{/if}{$ticket->order_id}">{$ticket->order_id}</a></span>
                                                    {/if}
                                                    {if in_array($ticket->status, [0,1])}
                                                        <small class="label label-warning"
                                                               style="margin-left: 50px; width: 7%">К принятию
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 2}
                                                        <small class="label label-primary"
                                                               style="margin-left: 50px; width: 7%">Принят/В работе
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 4}
                                                        <small class="label label-success"
                                                               style="margin-left: 50px; width: 7%">Исполнено
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 6}
                                                        <small class="badge badge-dark"
                                                               style="margin-left: 50px; width: 7%">Закрыт
                                                        </small>
                                                    {/if}
                                                    <span class="label label-text" style="width: 65%">
                                                        Дата тикета: {$ticket->created|date} {$ticket->created|time}
                                                    </span>
                                                </h6>
                                                {if !empty($ticket->order_id)}
                                                    <br>
                                                    <div class="row pt-2 view-block" style="margin-left: 3px">
                                                        <div class="col-md-12">
                                                            <div class="btn btn-info">
                                                                <a target="_blank"
                                                                   style="text-decoration: none; color: #f8fff7;"
                                                                   href="{if $offline == 1}/offline_order/{else}/order/{/if}{$ticket->order_id}">Просмотреть
                                                                    заявку</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                {/if}
                                                {foreach $messages as $message}
                                                    <div class="row pt-2 view-block">
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md-1">Дата:</label>
                                                                <label class="control-label col-md-4">{$message->created|date} {$message->created|time}</label>
                                                            </div>
                                                        </div>
                                                        <br><br>
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md-1">Автор:</label>
                                                                <label class="control-label col-md-4">{$message->manager_name}</label>
                                                            </div>
                                                        </div>
                                                        <br><br>
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md-2">Комментарий:</label>
                                                                <label class="control-label col-md-10">{$message->message}</label>
                                                            </div>
                                                        </div>
                                                        <br><br>
                                                        {if !empty($message->docs)}
                                                            <div class="col-md-12">
                                                                <label class="control-label col-md-2">Приложенные
                                                                    документы:</label>
                                                                {foreach $message->docs as $dock}
                                                                    <i class="fas fa-file-pdf fa-lg"
                                                                       style="margin-left: 25px"></i>
                                                                <a href="{$config->back_url}/files/users/{$dock->file_name}">
                                                                    {$dock->file_name|escape}
                                                                    </a>{$dock->size}
                                                                {/foreach}
                                                            </div>
                                                        {/if}
                                                    </div>
                                                    {if $message@iteration != count($messages)}
                                                        <hr>
                                                    {/if}
                                                {/foreach}
                                            </form>
                                            {if $need_response == 1}
                                                <div style="display: flex; justify-content: flex-end">
                                                    {if $manager->id != $ticket->executor || !empty($can_take_it)}
                                                        <div data-ticket="{$ticket->id}"
                                                             class="btn btn-outline-success accept_ticket"
                                                             id="accept_ticker">
                                                            Назначить ответственным себя
                                                        </div>
                                                    {/if}
                                                    {if in_array($manager->id, [$ticket->executor, $ticket->creator]) && $ticket->status != 6}
                                                        <div style="margin-left: 5px" type="button"
                                                             class="btn btn-outline-primary add_message">
                                                            Ответить
                                                        </div>
                                                    {/if}
                                                </div>
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

<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

{include file='footer.tpl'}

<div id="add-message-modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить ответ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST" id="add_message_form">
                    <input type="hidden" name="action" value="add_message">
                    <input type="hidden" name="ticket_id" value="{$ticket->id}">
                    <input type="hidden" name="manager_id" value="{$manager->id}">
                    <div class="form-group">
                        <label for="message" class="control-label">Комментарий</label>
                        <textarea class="form-control" name="message" id="message"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="docs" class="control-label">Прикрепить документы</label>
                        <input type="file" class="custom-file-control" name="docs[]" id="docs" multiple="multiple">
                    </div>
                    <div>
                        <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                        <input type="button" class="btn btn-success send_message" value="Отправить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>