{$meta_title='Отвалы клиентов' scope=parent}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/clients.js"></script>
{/capture}

{capture name='page_styles'}
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
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
            <div class="col-md-6 col-4 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0">
                    <i class="mdi mdi-sleep"></i>
                    <span>Отвалы клиентов</span>
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Отвалы</li>
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
                        <h4 class="card-title">Отвалы клиентов</h4>
                        <div class="clearfix">
                            <div class="row">
                                <div class="col-md-9">
                                    <div id="blockstatuses" class="js-filter-status mb-2 float-left">
                                        <a href="{if $filter_status==1}{url status=null page=null}{else}{url status=1 page=null}{/if}"
                                           class="btn btn-xs {if $filter_status==1}btn-success{else}btn-outline-success{/if}">Реанимируемый ({$clients_reable})</a>
                                        <a href="{if $filter_status==2}{url status=null page=null}{else}{url status=2 stage=null page=null}{/if}"
                                           class="btn btn-xs {if $filter_status==2}btn-danger{else}btn-outline-danger{/if}">Не
                                            реанимируемый ({$clients_unreable})</a>
                                        <a href="{if $filter_status==3}{url status=null page=null}{else}{url status=3 stage=null page=null}{/if}"
                                           class="btn btn-xs {if $filter_status==3}btn-primary{else}btn-outline-primary{/if}">У
                                            Андерайтера ({$clients_to_under})</a>
                                        <a href="{if $filter_status=='all'}{url status=null page=null}{else}{url status='all' stage=null page=null}{/if}"
                                           class="btn btn-xs {if $filter_status=='all'}btn-primary{else}btn-outline-primary{/if}">Все заявки ({$clients_all})</a>
                                        {if $filter_status}
                                            <input type="hidden" value="{$filter_status}" id="filter_status"/>
                                        {/if}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="col-12 dropdown text-right hidden-sm-down js-period-filter">
                                        <input type="hidden" value="{$period}" id="filter_period"/>
                                        <button class="btn btn-xs btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span class="js-period-filter-text">
                                                {if $period == 'today'}Сегодня
                                                {elseif $period == 'yesterday'}Вчера
                                                {elseif $period == 'week'}На этой неделе
                                                {elseif $period == 'month'}В этом месяце
                                                {elseif $period == 'year'}В этом году
                                                {elseif $period == 'all'}За все время
                                                {elseif $period == 'optional'}Произвольный
                                                {else}{$period}{/if}
                                            </span>

                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item js-period-link {if $period == 'today'}active{/if}"
                                               href="{url period='today' page=null}">Сегодня</a>
                                            <a class="dropdown-item js-period-link {if $period == 'yesterday'}active{/if}"
                                               href="{url period='yesterday' page=null}">Вчера</a>
                                            <a class="dropdown-item js-period-link {if $period == 'month'}active{/if}"
                                               href="{url period='month' page=null}">В этом месяце</a>
                                            <a class="dropdown-item js-period-link {if $period == 'year'}active{/if}"
                                               href="{url period='year' page=null}">В этом году</a>
                                            <a class="dropdown-item js-period-link {if $period == 'all'}active{/if}"
                                               href="{url period='all' page=null}">За все время</a>
                                            <a class="dropdown-item js-open-daterange {if $period == 'optional'}active{/if}"
                                               href="{url period='optional' page=null}">Произвольный</a>
                                        </div>

                                        <div class="js-daterange-filter input-group mt-3"
                                             {if $period!='optional'}style="display:none"{/if}>
                                            <input type="text" name="daterange" class="form-control daterange js-daterange-input"
                                                   value="{if $from && $to}{$from}-{$to}{/if}">
                                            <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="ti-calendar"></span>
                                        </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="basicgrid" class="jsgrid" style="position: relative; top:15px; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 100px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'id asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'id desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'id asc'}<a href="{url page=null sort='id desc'}">ID</a>
                                            {else}<a href="{url page=null sort='id asc'}">ID</a>{/if}
                                        </th>
                                        <th style="width: 100px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'begin_registration asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'begin_registration desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'begin_registration asc'}<a href="{url page=null sort='begin_registration desc'}">
                                                    Регистрация</a>
                                            {else}<a href="{url page=null sort='begin_registration asc'}">Регистрация</a>{/if}
                                        </th>
                                        <th style="width: 100px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'modified asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'modified desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'modified asc'}<a href="{url page=null sort='modified desc'}">
                                                    Посл.
                                                    действие</a>
                                            {else}<a href="{url page=null sort='modified asc'}">Посл. действие</a>{/if}
                                        </th>
                                        <th style="width: 200px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'lastname asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'lastname desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'lastname asc'}<a href="{url page=null sort='lastname desc'}">ФИО</a>
                                            {else}<a href="{url page=null sort='lastname asc'}">ФИО</a>{/if}
                                        </th>
                                        <th style="width: 100px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'phone_mobile asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'phone_mobile desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'phone_mobile asc'}<a href="{url page=null sort='phone_mobile desc'}">
                                                    Телефон</a>
                                            {else}<a href="{url page=null sort='phone_mobile asc'}">Телефон</a>{/if}
                                        </th>
                                        <th style="width:280px" class="jsgrid-header-cell ">
                                            Этапы
                                        </th>
                                    </tr>

                                    <tr class="jsgrid-filter-row" id="search_form">
                                        <td style="width: 100px;" class="jsgrid-cell">
                                            <input type="hidden" name="sort" value="{$sort}"/>
                                            <input type="text" name="user_id" value="{$search['user_id']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 100px;" class="jsgrid-cell">
                                            <input type="text" name="created" value="{$search['created']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 100px;" class="jsgrid-cell">
                                            <input type="text" name="created" value="{$search['created']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 200px;" class="jsgrid-cell">
                                            <input type="text" name="fio" value="{$search['fio']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 380px;" class="jsgrid-cell">
                                            <input type="text" name="phone" value="{$search['phone']}"
                                                   class="form-control input-sm">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover ">
                                    <tbody>
                                    {foreach $clients as $client}
                                        <tr class="jsgrid-row">
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                {if !empty($manager) && $manager->role == 'developer'}
                                                    <a href="neworder/draft/{$client->id}">{$client->id}</a>
                                                {else}
                                                    {$client->id}
                                                {/if}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                <span>{$client->date|date}</span>
                                                {$client->date|time}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                <span>{$client->updated|date}</span>
                                                {$client->updated|time}
                                            </td>
                                            <td style="width: 200px;" class="jsgrid-cell">
                                                {$client->user->lastname|escape}
                                                {$client->user->firstname|escape}
                                                {$client->user->patronymic|escape}<br>
                                                {if $client->status == 12 && $client->unreability == 0}
                                                    <span class="badge badge-success">Реанимируемый</span>
                                                {/if}
                                                {if $client->status == 12 && $client->unreability == 1}
                                                    <span class="badge badge-danger">Не реанимируемый</span>
                                                {/if}
                                                {if $client->status >= 0 && $client->status != 12 && $client->unreability == 0}
                                                    <span class="badge badge-primary">У андеррайтера</span>
                                                {/if}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                {$client->user->phone_mobile|escape}
                                            </td>
                                            <td style="width: 280px;" class="jsgrid-cell">
                                                <span class="label label-success">Телефон</span>
                                                <span class="label {if $client->user->stage_registration > 1}label-success{else}label-inverse{/if}">Анкета</span>
                                                <span class="label {if $client->user->stage_registration > 2}label-success{else}label-inverse{/if}">Контакты</span>
                                                <span class="label {if $client->user->stage_registration > 3}label-success{else}label-inverse{/if}">Работодатель</span><br>
                                                <span class="label {if $client->user->stage_registration > 4}label-success{else}label-inverse{/if}">Реквизиты</span>
                                                <span class="label {if $client->user->stage_registration > 5}label-success{else}label-inverse{/if}">Профсоюз</span>
                                                <span class="label {if $client->user->stage_registration > 6}label-success{else}label-inverse{/if}">Фото</span>
                                                <span class="label {if $client->user->stage_registration > 7}label-success{else}label-inverse{/if}">Подписание</span>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>

                            {if $total_pages_num>1}

                                {* Количество выводимых ссылок на страницы *}
                                {$visible_pages = 11}
                                {* По умолчанию начинаем вывод со страницы 1 *}
                                {$page_from = 1}

                                {* Если выбранная пользователем страница дальше середины "окна" - начинаем вывод уже не с первой *}
                                {if $current_page_num > floor($visible_pages/2)}
                                    {$page_from = max(1, $current_page_num-floor($visible_pages/2)-1)}
                                {/if}

                                {* Если выбранная пользователем страница близка к концу навигации - начинаем с "конца-окно" *}
                                {if $current_page_num > $total_pages_num-ceil($visible_pages/2)}
                                    {$page_from = max(1, $total_pages_num-$visible_pages-1)}
                                {/if}

                                {* До какой страницы выводить - выводим всё окно, но не более ощего количества страниц *}
                                {$page_to = min($page_from+$visible_pages, $total_pages_num-1)}
                                <div class="jsgrid-pager-container" style="">
                                    <div class="jsgrid-pager">
                                        Страницы:

                                        {if $current_page_num == 2}
                                            <span class="jsgrid-pager-nav-button "><a
                                                        href="{url page=null}">Пред.</a></span>
                                        {elseif $current_page_num > 2}
                                            <span class="jsgrid-pager-nav-button "><a
                                                        href="{url page=$current_page_num-1}">Пред.</a></span>
                                        {/if}

                                        <span class="jsgrid-pager-page {if $current_page_num==1}jsgrid-pager-current-page{/if}">
                                        {if $current_page_num==1}1{else}<a href="{url page=null}">1</a>{/if}
                                    </span>
                                        {section name=pages loop=$page_to start=$page_from}
                                            {* Номер текущей выводимой страницы *}
                                            {$p = $smarty.section.pages.index+1}
                                            {* Для крайних страниц "окна" выводим троеточие, если окно не возле границы навигации *}
                                            {if ($p == $page_from + 1 && $p != 2) || ($p == $page_to && $p != $total_pages_num-1)}
                                                <span class="jsgrid-pager-page {if $p==$current_page_num}jsgrid-pager-current-page{/if}">
                                            <a href="{url page=$p}">...</a>
                                        </span>
                                            {else}
                                                <span class="jsgrid-pager-page {if $p==$current_page_num}jsgrid-pager-current-page{/if}">
                                            {if $p==$current_page_num}{$p}{else}<a href="{url page=$p}">{$p}</a>{/if}
                                        </span>
                                            {/if}
                                        {/section}
                                        <span class="jsgrid-pager-page {if $current_page_num==$total_pages_num}jsgrid-pager-current-page{/if}">
                                        {if $current_page_num==$total_pages_num}{$total_pages_num}{else}<a
                                            href="{url page=$total_pages_num}">{$total_pages_num}</a>{/if}
                                    </span>

                                        {if $current_page_num<$total_pages_num}
                                            <span class="jsgrid-pager-nav-button"><a
                                                        href="{url page=$current_page_num+1}">След.</a></span>
                                        {/if}
                                        &nbsp;&nbsp; {$current_page_num} из {$total_pages_num}
                                    </div>
                                </div>
                            {/if}

                            <div class="jsgrid-load-shader"
                                 style="display: none; position: absolute; inset: 0px; z-index: 10;">
                            </div>
                            <div class="jsgrid-load-panel"
                                 style="display: none; position: absolute; top: 50%; left: 50%; z-index: 1000;">
                                Идет загрузка...
                            </div>
                        </div>
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
