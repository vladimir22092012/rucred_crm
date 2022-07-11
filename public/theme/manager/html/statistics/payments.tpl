{$meta_title='Отчет по оплатам' scope=parent}

{capture name='page_scripts'}

    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
    $(function(){
        moment.locale('ru');

        $('.daterange').daterangepicker({
            locale: {
                format: 'DD.MM.YYYY',
                "customRangeLabel": "Произвольно",
                "applyLabel": "Применить",
                "cancelLabel": "Отменить",
            },
            default: '',
            ranges: {
                'Cегодня': [moment(), moment()],
                'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                'Текущая неделя': [moment().startOf('week'), moment()],
                'Прошлая неделя': [moment().startOf('week').subtract(7, 'days'), moment().startOf('week').subtract(1, 'days')],
                'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                'Текущий месяц': [moment().startOf('month'), moment().endOf('month')],
                'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Текущий год': [moment().startOf('year'), moment()]
            }
        });
    })
    </script>
{/capture}

{capture name='page_styles'}
    
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">

    <style>
    .table th td {
        text-align:center!important;
    }
    </style>
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
                    <i class="mdi mdi-file-chart"></i> 
                    <span>Отчет по оплатам</span>
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="statistics">Статистика</a></li>
                    <li class="breadcrumb-item active">Оплаты</li>
                </ol>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <!-- Column -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Отчет по оплатам за период {if $date_from}{$date_from|date} - {$date_to|date}{/if}</h4>
                        <form>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="input-group mb-3">
                                        <input type="text" name="daterange" class="form-control daterange" value="{if $from && $to}{$from}-{$to}{/if}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="ti-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-info">Сформировать</button>
                                </div>
                                {if $date_from || $date_to}
                                <div class="col-12 col-md-4 text-right">
                                    <a href="{url download='excel'}" class="btn btn-success ">
                                        <i class="fas fa-file-excel"></i> Скачать
                                    </a>
                                </div>
                                {/if}
                            </div>
                            
                        </form>     
                        
                        {if $from}                   
                        <table class="table table-hover" id="basicgrid">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Дата</th>
                                <th>Договор</th>
                                <th>ФИО</th>
                                <th>Сумма</th>
                            </tr>
                            
                            <tr class="js-search-block">
                                <td>
                                </td>
                                <td>
                                    <input type="text" name="created" value="{$search['created']}" class="form-control input-sm daterange">
                                </td>
                                <td>
                                    <input type="text" name="number" value="{$search['number']}" class="form-control input-sm">
                                </td>
                                <td>
                                    <input type="text" name="fio" value="{$search['fio']}" class="form-control input-sm">
                                </td>
                                <td>
                                    <input type="text" name="amount" value="{$search['amount']}" class="form-control input-sm">
                                </td>
                            </tr>
                            </thead>
                            
                            <tbody id="table_content">
                                {foreach $operations as $operation}
                                <tr>
                                    <td>{$operation@iteration}</td>
                                    <td>
                                        {$operation->created|date} 
                                        <br />
                                        {$operation->created|time}
                                    </td>
                                    <td>
                                        <a target="_blank" href="order/{$operation->order_id}">{$operation->contract_number}</a>
                                        {if $operation->sector == '7036'}<span class="label label-danger">ЮК</span>
                                        {else}<span class="label label-success">МКК</span>{/if}
                                    </td>
                                    <td>
                                        <a href="client/{$operation->user_id}" target="_blank">
                                            <strong>
                                            {$operation->lastname|escape} 
                                            {$operation->firstname|escape} 
                                            {$operation->patronymic|escape}
                                            </strong><br />
                                            {$operation->birth|escape}
                                        </a>
                                    </td>
                                    <td>
                                        <strong class="text-success">{$operation->amount*1}</strong>
                                    </td>
                                    <td>
                                        <small class="">{$operation->pan}</small>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                            
                        </table>
                        {else}
                            <div class="alert alert-info">
                                <h4>Укажите даты для формирования отчета</h4>
                            </div>
                        {/if}
                        
                    </div>
                </div>
                <!-- Column -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>