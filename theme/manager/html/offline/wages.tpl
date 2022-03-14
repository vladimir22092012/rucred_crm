{$meta_title='Зарплаты сотрудников' scope=parent}

{capture name='page_scripts'}

    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
    $(function(){
        $('.daterange').daterangepicker({
            autoApply: true,
            locale: {
                format: 'DD.MM.YYYY'
            },
            default:''
        });
    })
    </script>

    <script>
        function WagesApp()
        {
            var app = this;
           
            app.init = function(){
                
                app.send_operation();
                
                app.init_search();
            };
            
            app.send_operation = function(){

                $(document).on('click', '.js-send-operation', function(e){
                    e.preventDefault();
                    
                    var operation_id = $(this).data('operation');
                    
                    $.ajax({
                        data: {
                            operation_id: operation_id
                        },
                        success: function(resp){
                            if (!!resp.error)
                                Swal.fire('Ошибка', resp.error, 'error');
                            else
                                Swal.fire('Успешно', resp.success, 'success');
                        }
                    });
                });
            };
            
            app.load = function(_url, loading){
                $.ajax({
                    url: _url,
                    beforeSend: function(){
                        if (loading)
                        {
                            $('.jsgrid-load-shader').show();
                            $('.jsgrid-load-panel').show();
                        }
                    },
                    success: function(resp){
                        
                        
                        if (loading)
                        {
                            $('html, body').animate({
                                scrollTop: $("#basicgrid").offset().top-80  
                            }, 1000);
                            
                            $('.jsgrid-load-shader').hide();
                            $('.jsgrid-load-panel').hide();
                        }
                        
                    }
                })
            };
            
            app.init_search = function(){
                $(document).on('change', '.js-search-block input', function(){

                    var _searches = {};
                    $('.js-search-block input').each(function(){
                        if ($(this).val() != '')
                        {
                            _searches[$(this).attr('name')] = $(this).val();
                        }
                    });     
                    var _request = {

                    };
                    var _query = Object.keys(_request).map(
                        k => encodeURIComponent(k) + '=' + encodeURIComponent(_request[k])
                    ).join('&');
            
                    _request.search = _searches;
                    if (!$.isEmptyObject(_searches))
                    {
                        _query_searches = '';
                        for (key in _searches) {
                          _query_searches += '&search['+key+']='+_searches[key];
                        }
                        _query += _query_searches;
                    }
                    
                    $.ajax({
                        data: _request,
                        beforeSend: function(){
                        },
                        success: function(resp){
                            var _table = $(resp).find('#table_content').html();
console.log(_table)
                            $('#table_content').html(_table)
                        }
                    })
                });
            };
            
            ;(function(){
                app.init();
            })();
        };
        $(function(){
            new WagesApp();
        });
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
                    <span>Зарплаты сотрудников</span>
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Зарплаты сотрудников</li>
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
                        <h4 class="card-title">Зарплаты сотрудников за период {if $date_from}{$date_from|date} - {$date_to|date}{/if}</h4>
                        <form>
                            <input type="hidden" name="report" value="1" />
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="input-group mb-3">
                                        <select class="form-control" name="manager_id">
                                            <option value=""></option>
                                            {foreach $managers as $m}
                                            {if $m->role == 'user'}
                                            <option value="{$m->id}" {if $filter_manager_id == $m->id}selected{/if}>{$m->name}</option>
                                            {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="input-group mb-3">
                                        <select class="form-control" name="month">
                                            {foreach $monthes as $mi => $mn}
                                            <option value="{$mi}" {if $mi == $filter_month}selected{/if}>{$mn}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="input-group mb-3">
                                        <select class="form-control" name="year">
                                            {foreach $years as $y}
                                            <option value="{$y}" {if $y == $filter_year}selected{/if}>{$y}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-info">Сформировать</button>
                                </div>
                            </div>
                            
                        </form>     
                        
                        {if $error}
                        
                        <div class="alert alert-danger">
                            {$error}
                        </div>
                        
                        {elseif $report}                   
                        
                        <table class="table table-hover table-bordered table-striped" id="basicgrid">
                            <thead>
                                <tr class="table-secondary">
                                    <th>Дата</th>
                                    <th>Филиал</th>
                                    <th>Время работы</th>
                                    <th>ЗП по часам</th>
                                    <th>Оплаченные %</th>
                                    <th>ЗП с %</th>
                                    <th>Кол-во НК</th>
                                    <th>Кол-во ПК</th>
                                    <th>Выдачи</th>
                                    <th>ЗП за день</th>
                                </tr>
                            
                            </thead>
                            
                            <tbody id="table_content">
                                {foreach $report->days as $day}
                                <tr>
                                    <td>
                                        {$day->date|date:'d.m'} 
                                    </td>
                                    <td>
                                        {$day->filial}
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        {$day->day_percents}
                                    </td>
                                    <td>
                                        {$day->percents_zp}
                                    </td>
                                    <td>
                                        {$day->day_nk}
                                    </td>
                                    <td>
                                        {$day->day_pk}
                                    </td>
                                    <td>
                                        {$day->day_inssuance}
                                    </td>
                                    <td class="text-center">
                                        {$day->day_wage}
                                    </td>
                                </tr>
                                {/foreach}
                                <tr>
                                    <td colspan="3">
                                        <strong>Всего:</strong>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        {$report->total_percents}
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        {$report->total_nk}
                                    </td>
                                    <td>
                                        {$report->total_pk}
                                    </td>
                                    <td>
                                        {$report->total_inssuance}
                                    </td>
                                    <td class="text-center">
                                        {$report->total_wage}
                                    </td>
                                </tr>
                                
                                {if $report->card}
                                <tr>
                                    <td colspan="9">
                                        <strong>Ежемесячное обслуживание:</strong>
                                    </td>
                                    <td class="text-center">
                                        {$report->card}
                                    </td>
                                </tr>
                                {/if}
                                
                                {if $report->sbor_dr}
                                <tr>
                                    <td colspan="9">
                                        <strong>Сбор на ДР:</strong>
                                    </td>
                                    <td class="text-center">
                                        -{$report->sbor_dr}
                                    </td>
                                </tr>
                                {/if}
                                
                                {if $report->premia_nk}
                                <tr>
                                    <td colspan="9">
                                        <strong>Премия за перевыполнение плана по НК:</strong>
                                    </td>
                                    <td class="text-center">
                                        {$report->premia_nk}
                                    </td>
                                </tr>
                                {/if}
                                
                                {if $report->premia_sbor}
                                <tr>
                                    <td colspan="9">
                                        <strong>Премия за сбор:</strong>
                                    </td>
                                    <td class="text-center">
                                        {$report->premia_sbor}
                                    </td>
                                </tr>
                                {/if}

                                {if $report->total}
                                <tr>
                                    <td colspan="9">
                                        <h5><strong>Итого к выплате:</strong></h5>
                                    </td>
                                    <td class="text-center">
                                        <h5>{$report->total}</h5>
                                    </td>
                                </tr>
                                {/if}

                            </tbody>
                            
                        </table>
                        {else}
                            <div class="alert alert-info">
                                <h4>Выберите параметры для формирования отчета</h4>
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