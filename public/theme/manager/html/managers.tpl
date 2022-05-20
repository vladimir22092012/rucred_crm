{$meta_title="Пользователи" scope=parent}

{capture name='page_scripts'}
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/managers.js"></script>
{/capture}

{capture name='page_styles'}
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css" />
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css" />
    <style>
        .jsgrid-table { margin-bottom:0}
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
                    Пользователи
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item">Пользователи</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                {if in_array('create_managers', $manager->permissions)}
                <a href="manager" class="btn float-right hidden-sm-down btn-success">
                    <i class="mdi mdi-plus-circle"></i>
                    Создать пользователя
                </a>
                {/if}
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Список пользователей</h4>
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row">
                                        <th style="width: 60px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'id_asc'}<a href="{url page=null sort='id_desc'}">#</a>
                                            {else}<a href="{url page=null sort='id_asc'}">#</a>{/if}
                                        </th>
                                        <th style="width: 80px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'name_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'name_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'name_asc'}<a href="{url page=null sort='name_desc'}">Пользователь</a>
                                            {else}<a href="{url page=null sort='name_asc'}">Пользователь</a>{/if}
                                        </th>
                                        <th style="width: 120px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'company_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'company_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'company_asc'}<a href="{url page=null sort='company_desc'}">Компания</a>
                                            {else}<a href="{url page=null sort='company_asc'}">Компания</a>{/if}
                                        </th>
                                        <th style="width: 80px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'last_ip_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'last_ip_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'last_ip_asc'}<a href="{url page=null sort='last_ip_desc'}">IP адрес</a>
                                            {else}<a href="{url page=null sort='last_ip_asc'}">IP адрес</a>{/if}
                                        </th>
                                        <th style="width: 100px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'last_visit_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'last_visit_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'last_visit_asc'}<a href="{url page=null sort='last_visit_desc'}">Активность</a>
                                            {else}<a href="{url page=null sort='last_visit_asc'}">Активность</a>{/if}
                                        </th>
                                        <th style="width: 100px;" class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'role_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'role_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'role_asc'}<a href="{url page=null sort='role_desc'}">Роль</a>
                                            {else}<a href="{url page=null sort='role_asc'}">Роль</a>{/if}
                                        </th>
                                    </tr>

                                    <tr class="jsgrid-filter-row" id="search_form">
                                        <td style="width: 60px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="hidden" name="sort" value="{$sort}" />
                                            <input type="text" name="user_id" value="{$search['user_id']}" class="form-control input-sm">
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="text" name="name" value="{$search['name']}" class="form-control input-sm">
                                        </td>
                                        <td style="width: 120px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="text" name="company" value="{$search['company']}" class="form-control input-sm">
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="text" name="last_ip" value="{$search['last_ip']}" class="form-control input-sm">
                                        </td>
                                        <td style="width: 100px;" class="jsgrid-cell">
                                            <input type="text" name="last_visit" value="{$search['last_visit']}" class="form-control input-sm">
                                        </td>
                                        <td style="width: 100px;" class="jsgrid-cell">
                                            <select class="form-control" name="role" id="role">
                                                <option value="">Выберите роль</option>
                                                <option value="admin">Администратор</option>
                                                <option value="underwriter">Андеррайтер</option>
                                                <option value="middle">Мидл</option>
                                                <option value="employer">Работодатель</option>
                                                <option value="boss">Босс</option>
                                                <option value="developer">Разработчик</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover" style="text-align: center">
                                    <tbody>
                                    {foreach $managers as $manager}
                                        <tr class="jsgrid-row ">
                                            <td style="width: 60px;" class="jsgrid-cell">
                                                <a href="/manager/{$manager->id}">{$manager->id}</a>
                                            </td>
                                            <td style="width: 80px;" class="jsgrid-cell">
                                                <a href="/manager/{$manager->id}">{$manager->name}</a>
                                            </td>
                                            <td style="width: 120px;" class="jsgrid-cell">
                                                {$manager->company_name}
                                            </td>
                                            <td style="width: 80px;" class="jsgrid-cell">
                                                {$manager->last_ip}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                {if $manager->last_visit}
                                                    {$manager->last_visit|date} {$manager->last_visit|time}
                                                {/if}
                                            </td>
                                            <td style="width: 100px;" class="jsgrid-cell">
                                                {$label_class="info"}
                                                {if $manager->role == 'developer' || $manager->role == 'technic'}{$label_class="danger"}{/if}
                                                {if $manager->role == 'admin' || $manager->role == 'chief_collector' || $m->role == 'team_collector'}{$label_class="success"}{/if}
                                                {if $manager->role == 'verificator' || $manager->role == 'user'}{$label_class="warning"}{/if}
                                                {if $manager->role == 'collector'}{$label_class="primary"}{/if}

                                                <span class="label label-{$label_class}">
                                                {if $roles[$manager->role]}
                                                    {$roles[$manager->role]}
                                                {else}
                                                    {$manager->role}
                                                {/if}
                                            </span>
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
                                            <span class="jsgrid-pager-nav-button "><a href="{url page=null}">Пред.</a></span>
                                        {elseif $current_page_num > 2}
                                            <span class="jsgrid-pager-nav-button "><a href="{url page=$current_page_num-1}">Пред.</a></span>
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
                                        {if $current_page_num==$total_pages_num}{$total_pages_num}{else}<a href="{url page=$total_pages_num}">{$total_pages_num}</a>{/if}
                                    </span>

                                        {if $current_page_num<$total_pages_num}
                                            <span class="jsgrid-pager-nav-button"><a href="{url page=$current_page_num+1}">След.</a></span>
                                        {/if}
                                        &nbsp;&nbsp; {$current_page_num} из {$total_pages_num}
                                    </div>
                                </div>
                            {/if}

                            <div class="jsgrid-load-shader" style="display: none; position: absolute; inset: 0px; z-index: 10;">
                            </div>
                            <div class="jsgrid-load-panel" style="display: none; position: absolute; top: 50%; left: 50%; z-index: 1000;">
                                Идет загрузка...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row -->
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
