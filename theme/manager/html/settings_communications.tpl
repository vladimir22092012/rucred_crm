{$meta_title = 'Лимиты коммуникаций' scope=parent}

{capture name='page_scripts'}

    <script type="text/javascript">

    </script>

{/capture}

{capture name='page_styles'}

    
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
                    Лимиты коммуникаций
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Лимиты коммуникаций</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        <form class="" method="POST" >
            
        <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Максимальное количество контактов за период
                            </h3>
                        </div>
                        <div class="col-md-4 card-outline-info">
                            <div class="border border-radius">
                                <h5 class="card-header text-white">Смс, звонобот</h5>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В день</label>                                    
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="sms_limit_communications[day]" value="{$settings->sms_limit_communications['day']}" placeholder="">                                
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В неделю</label>                                    
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="sms_limit_communications[week]" value="{$settings->sms_limit_communications['week']}" placeholder="">                                    
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В месяц</label>                                    
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="sms_limit_communications[month]" value="{$settings->sms_limit_communications['month']}" placeholder="">                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 card-outline-info">
                            <div class="border">
                                <h5 class="card-header text-white">Звонки</h5>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В день</label>                                    
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="call_limit_communications[day]" value="{$settings->call_limit_communications['day']}" placeholder="">                                    
                                        </div>
                                    </div>
                                    <div class="">
                                    </div>
                                </div>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В неделю</label>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <input type="text" class="form-control" name="call_limit_communications[week]" value="{$settings->call_limit_communications['week']}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">В месяц</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="call_limit_communications[month]" value="{$settings->call_limit_communications['month']}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 card-outline-info">
                            <div class="border">
                                <h5 class="card-header text-white">Время коммуникаций</h5>
                                <div class="form-group mb-3  p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">Рабочие дни, часы</label>                                    
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="workday_worktime[from]" value="{$settings->workday_worktime['from']}" placeholder="">                                
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="workday_worktime[to]" value="{$settings->workday_worktime['to']}" placeholder="">                                
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3 p-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class=" col-form-label">Выходные, часы</label>                                    
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="holiday_worktime[from]" value="{$settings->holiday_worktime['from']}" placeholder="">                                    
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="holiday_worktime[to]" value="{$settings->holiday_worktime['to']}" placeholder="">                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        
        <hr class="mb-3 mt-3" />
        
        <div class="row">
            <div class="col-12 grid-stack-item" data-gs-x="0" data-gs-y="0" data-gs-width="12">
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Сохранить</button>
                </div>
            </div>
        </form>
        <!-- Row -->
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
</div>




