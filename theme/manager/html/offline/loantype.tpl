{if $loantype->id}
    {$meta_title="Редактировать вид кредитования" scope=parent}
{else}
    {$meta_title="Создать новый вид кредитования" scope=parent}
{/if}

{capture name='page_scripts'}
    <script>
        $(function () {

            $('.edit-company-tarif').on('click', function (e) {
                e.preventDefault();

                $('#modal_add_item').modal();

                let standart_percents = $(this).attr('data-standart-percents');
                let preferential_percents = $(this).attr('data-preferential-percents');
                let loantype_id = {{$loantype->id}};
                let group_id = $(this).attr('data-group');

                $('#standart_percents').val(standart_percents);
                $('#preferential_percents').val(preferential_percents);
                $('input[name=loantype_id]').val(loantype_id);
                $('input[name=group_id]').val(group_id);
            })
        })
    </script>
{/capture}

{capture  name='page_styles'}
    <style>
        .onoffswitch {
            display: inline-block !important;
            vertical-align: top !important;
            width: 60px !important;
            text-align: left;
        }

        .onoffswitch-switch {
            right: 38px !important;
            border-width: 1px !important;
        }

        .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
            right: 0px !important;
        }

        .onoffswitch-label {
            margin-bottom: 0 !important;
            border-width: 1px !important;
        }

        .onoffswitch-inner::after,
        .onoffswitch-inner::before {
            height: 18px !important;
            line-height: 18px !important;
        }

        .onoffswitch-switch {
            width: 20px !important;
            margin: 1px !important;
        }

        .onoffswitch-inner::before {
            content: 'ВКЛ' !important;
            padding-left: 10px !important;
            font-size: 10px !important;
        }

        .onoffswitch-inner::after {
            content: 'ВЫКЛ' !important;
            padding-right: 6px !important;
            font-size: 10px !important;
        }

        .scoring-content {
            position: relative;
            z-index: 999;
            border: 1px solid rgba(120, 130, 140, 0.13);;
            border-top: 0;
            background: #fff;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
            margin-top: -5px;
        }

        .collapsed .fa-minus-circle::before {
            content: "\f055";
        }

        h4.text-white {
            display: inline-block
        }

        .move-zone {
            display: inline-block;
            color: #fff;
            padding-right: 15px;
            margin-right: 10px;
            border-right: 1px solid #30b2ff;
            cursor: move
        }

        .move-zone span {
            font-size: 24px;
        }

        .dd {
            max-width: 100%;
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
                    {if $loantype->id}
                        {$loantype->name|escape}
                    {else}
                        Новый вид кредитования
                    {/if}
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="loantypes">Виды кредитования</a></li>
                    {if $loantype->id}
                        <li class="breadcrumb-item active">{$loantype->name}</li>
                    {else}
                        <li class="breadcrumb-item active">Новый</li>
                    {/if}
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
        <div class="card card-outline-info">
            <div class="card-body">
                <form method="POST">

                    <input type="hidden" name="action" value="edit_loan"/>

                    <div class="row">

                        {if $success}
                            <div class="col-12">
                                <div class="alert alert-success">
                                    {$success}
                                </div>
                            </div>
                        {/if}

                        {if $error}
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <h3 class="pl-5">Ошибка</h3>
                                    <ul>
                                        <li>{$error}</li>
                                    </ul>
                                </div>
                            </div>
                        {/if}

                        <div class="col-md-6">
                            <div class="border">
                                <h5 class="card-header"><span class="text-white">Общие</span></h5>

                                <input type="hidden" name="id" value="{$loantype->id}"/>

                                <div class="p-2 pt-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Наименование</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="name"
                                                       value="{$loantype->name|escape}" required=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Организация</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select name="organization_id" class="form-control">
                                                    <option value=""></option>
                                                    {foreach $organizations as $org}
                                                        <option value="{$org->id}"
                                                                {if $org->id == $loantype->organization_id}selected{/if}>{$org->name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <table class="table display table-striped dataTable">
                                            <thead>
                                            <tr>
                                                <th align="center">Группа</th>
                                                <th align="center">Процентная ставка</th>
                                                <th align="center">Льготная ставка</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {if !empty($groups)}
                                                {foreach $groups as $group}
                                                    <tr>
                                                        <td valign="middle">{$group['name']}</td>
                                                        <td valign="middle">{$group['standart_percents']}</td>
                                                        <td valign="middle">{$group['preferential_percents']}</td>
                                                        <td><input type="button"
                                                                   data-group="{$group['id']}"
                                                                   data-standart-percents="{$group['standart_percents']}"
                                                                   data-preferential-percents="{$group['preferential_percents']}"
                                                                   class="btn btn-outline-warning edit-company-tarif"
                                                                   value="Редактировать"></td>
                                                    </tr>
                                                {/foreach}
                                            {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border">
                                <h5 class="card-header"><span class="text-white">Параметры кредитования</span></h5>
                                <div class="p-2 pt-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Процентная ставка, %/день</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="percent"
                                                       {if $loantype}value="{$loantype->percent}" {/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Процентная ставка профсоюз, %/день</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="profunion"
                                                       {if $loantype}value="{$loantype->profunion}" {/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Скидка, %/день</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="discount"
                                                       {if $loantype}value="{$loantype->discount}" {/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Минимальная сумма, руб</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="min_amount"
                                                       value="{$loantype->min_amount}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Максимальная сумма, руб</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="max_amount"
                                                       value="{$loantype->max_amount}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Количество выплат</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="max_period"
                                                       value="{$loantype->max_period}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Цели использования
                                                    Заёмщиком микрозайма</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select class="form-control" name="reason_flag">
                                                    <option value="1" {if $loantype->reason_flag == 0} selected{/if}>На
                                                        неотложные нужды
                                                    </option>
                                                    <option value="2" {if $loantype->reason_flag == 1} selected{/if}>На
                                                        рефинансирование обязательств перед третьими лицами
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="m-2"/>
                            <div class="text-right">
                                <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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

<div id="modal_add_item" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить группу</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="alert" style="display:none"></div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_tarif">
                    <input type="hidden" name="loantype_id" value="">
                    <input type="hidden" name="group_id" value="">
                    <div class="form-group">
                        <label for="standart_percents" class="control-label">Процентная ставка</label>
                        <input type="text" class="form-control" name="standart_percents" id="standart_percents"
                               value=""/>
                    </div>
                    <div class="form-group">
                        <label for="preferential_percents" class="control-label">Льготная ставка</label>
                        <input type="text" class="form-control" name="preferential_percents" id="preferential_percents"
                               value=""/>
                    </div>
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Отмена">
                    <input type="submit" formmethod="post" class="btn btn-success" value="Сохранить">
                </form>
            </div>
        </div>
    </div>
</div>


