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

            $('.close_ticket').on('click', function (e) {

                let ticket_id = $(this).attr('data-ticket');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'close_ticket',
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
                    <li class="breadcrumb-item">Коммуникации</li>
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
                                                    {if $ticket->status == 0}
                                                        <small class="label label-warning"
                                                               style="margin-left: 10px; width: 7%">Новый
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 1}
                                                        <small class="label label-primary"
                                                               style="margin-left: 10px; width: 7%">Принят
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 2}
                                                        <small class="label label-primary"
                                                               style="margin-left: 10px; width: 7%">На проверку
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 3}
                                                        <small class="label label-success"
                                                               style="margin-left: 10px; width: 7%">Исполнено
                                                        </small>
                                                    {/if}
                                                    {if $ticket->status == 4}
                                                        <small class="label label-danger"
                                                               style="margin-left: 10px; width: 7%">На доработку
                                                        </small>
                                                    {/if}
                                                    <span class="label label-text" style="width: 65%">
                                                        Дата тикета: {$ticket->created|date} {$ticket->created|time}
                                                    </span>
                                                    {if $manager->company_id == $ticket->company_id && $ticket->status != 3}
                                                        <button data-ticket="{$ticket->id}"
                                                                class="btn btn-primary close_ticket">
                                                            <span>Закрыть тикет</span>
                                                        </button>
                                                    {/if}
                                                    {if $ticket->status == 0}
                                                        <button data-ticket="{$ticket->id}"
                                                                class="btn btn-success accept_ticket">
                                                            <i class="fas fa-check-circle"></i>
                                                            <span>Принять тикет</span>
                                                        </button>
                                                    {/if}
                                                </h6>
                                                <div class="row pt-2 view-block">
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">{$ticket->text}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="width: 100%; size: 5px">
                                                {if isset($ticket->docs)}
                                                    <div class="row pt-2 view-block">
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md-4">Приложенные
                                                                    документы:</label>
                                                                <hr style="width: 100%; size: 5px">
                                                                {foreach $ticket->docs as $dock}
                                                                    <div class="col-md-12">
                                                                        <p class="form-control-static"><i
                                                                                    class="fas fa-file-pdf fa-lg"></i>&nbsp;
                                                                            &nbsp;<a
                                                                                    href="{$config->back_url}/files/users/{$dock->file_name}">{$dock->name|escape}</a>
                                                                        </p>
                                                                    </div>
                                                                    {if $dock@iteration != count($ticket->docs)}
                                                                        <hr style="width: 100%; size: 5px">
                                                                    {/if}
                                                                {/foreach}
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            </form>
                                            <div type="button"
                                                 class="btn btn-outline-primary float-right">
                                                Ответить
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