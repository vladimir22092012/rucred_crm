{if $loantype->id}
    {$meta_title="Редактировать вид кредитования" scope=parent}
{else}
    {$meta_title="Создать новый вид кредитования" scope=parent}
{/if}

{capture name='page_scripts'}
    <!-- jQuery Validation JS -->
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js'></script>

    <script>
        $(function () {
            let loantype_id = $('#loantype_id').val();
            let product_type = $('#product_type');
            let number_of_payouts = $('#number_of_payouts');

            if (product_type.val() === 'pdl') {
                number_of_payouts.val('1').attr('readonly', 'readonly');
            }

            product_type.on('change', function (e) {
                if ($(this).val() === 'annouitet') {
                    number_of_payouts
                        .val('')
                        .attr({
                            min: 2
                        })
                        .removeAttr('readonly');
                } else if ($(this).val() === 'pdl') {
                    number_of_payouts
                        .val('1')
                        .attr('readonly', 'readonly');
                }
            });

            $('#loantype_form').validate({
                rules: {
                    percent: "required",
                    online_flag: "required",
                    max_amount: "required",
                    min_amount: "required",
                    free_days: "required",
                    profunion: "required",
                    max_period: {
                        required: true,
                        min: function () {
                            return (product_type.val() === 'pdl') ? 1 : 2;
                        },
                    },
                    description: {
                        maxlength: 20
                    }
                },

                errorElement: "em",

                errorPlacement: function ( error, element ) {
                    // Add the `invalid-feedback` class to the error element
                    error.addClass( "invalid-feedback" );
                    error.insertAfter(element);
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
                },
                messages: {
                    number: {
                        required: "Пожалуйста, укажите номер тарифа"
                    },
                    name: {
                        required: "Пожалуйста, укажите наименование вида кредита"
                    },
                    max_amount: {
                        required: "Пожалуйста, укажите максимальную сумму кредита"
                    },
                    min_amount: {
                        required: "Пожалуйста, укажите минимальную сумму кредита"
                    },
                    description: {
                        maxlength: "Длина описания не может быть более 20 символов"
                    },
                    percent: {
                        required: "Пожалуйста, укажите процентную ставку"
                    },
                    profunion: {
                        required: "Пожалуйста, укажите льготную ставку"
                    },
                    max_period: {
                        required: "Пожалуйста, укажите максимальный срок кредита",
                        min: "Для данного типа продукта, данное количество выплат недоступно"
                    }
                }
            });

            $('.edit-company-tarif').on('click', function (e) {
                e.preventDefault();

                $('#modal_add_item').modal();

                let standart_percents = $(this).attr('data-standart-percents');
                let preferential_percents = $(this).attr('data-preferential-percents');
                let group_id = $(this).attr('data-group');

                $('#standart_percents').val(standart_percents);
                $('#preferential_percents').val(preferential_percents);
                $('input[name=loantype_id]').val(loantype_id);
                $('input[name=group_id]').val(group_id);
            });

            $('#online_flag').on('change', function (e) {
                let flag = $(this).val();

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_online_flag',
                        loantype_id: loantype_id,
                        flag: flag
                    }
                });
            });

            $('.on_off_flag').on('change', function () {

                let val = $(this).val();
                let value = (val == 1) ? 0 : 1;
                let record_id = $(this).attr('data-record');
                let that = $(this);

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_on_off_flag',
                        value: value,
                        record_id: record_id
                    },
                    success: function () {
                        that.val(value);
                    }
                });
            });
        });
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
                    <li class="breadcrumb-item"><a href="loantypes">Продукты</a></li>
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
                <form id="loantype_form" method="POST">

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

                                <input id="loantype_id" type="hidden" name="id" value="{$loantype->id}"/>

                                <div class="p-2 pt-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Тип продукта</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select
                                                    id="product_type"
                                                    class="form-control"
                                                    name="product_type"
                                                    {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}
                                                        disabled
                                                    {/if}
                                                >
                                                    <option value="pdl" {if $loantype->type == 'pdl'}selected{/if}>Payroll PDL
                                                    </option>
                                                    <option value="annouitet"
                                                            {if $loantype->type == 'annouitet'}selected{/if}>Payroll Installment
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Номер тарифа</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="number"
                                                       value="{$loantype->number}" required=""
                                                       {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Наименование</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="name"
                                                       value="{$loantype->name|escape}" required=""
                                                       {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Доступность</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select name="online_flag" id="online_flag" class="form-control"
                                                        {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}>
                                                    <option value="1" {if $loantype->online_flag == 1}selected{/if}>
                                                        Онлайн
                                                    </option>
                                                    <option value="2" {if $loantype->online_flag == 2}selected{/if}>
                                                        Оффлайн
                                                    </option>
                                                    <option value="3" {if $loantype->online_flag == 3}selected{/if}>
                                                        Везде
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Описание</label>
                                            </div>
                                            <div class="col-7 ">
                                                <textarea class="form-control" name="description">{$loantype->description}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    {if $manager->role != 'employer'}
                                        <div class="form-group">
                                            <table class="table display table-striped dataTable">
                                                <thead>
                                                <tr>
                                                    <th align="center">Группа</th>
                                                    <th align="center">Процентная ставка</th>
                                                    <th align="center">Льготная ставка</th>
                                                    <th align="center">Вкл/Выкл</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {if !empty($groups)}
                                                    {foreach $groups as $group}
                                                        <tr>
                                                            <td valign="middle">{$group['name']}</td>
                                                            <td valign="middle">{$group['standart_percents']|number_format:3:',':' '}</td>
                                                            <td valign="middle">{$group['preferential_percents']|number_format:3:',':' '}</td>
                                                            <td valign="middle">
                                                                <div class="clearfix">
                                                                    <div class="float-left">
                                                                        <div class="onoffswitch">
                                                                            <input type="checkbox" name="on_off_flag"
                                                                                   data-record="{$group['record_id']}"
                                                                                   class="onoffswitch-checkbox on_off_flag"
                                                                                   id="on_off_flag_{$group['id']}"
                                                                                    {if $group['on_off_flag'] == 1} checked="true" value="1" {else} value="0"{/if}
                                                                                    {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}>
                                                                            <label class="onoffswitch-label"
                                                                                   for="on_off_flag_{$group['id']}">
                                                                                <span class="onoffswitch-inner"></span>
                                                                                <span class="onoffswitch-switch"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{if !in_array($manager->role, ['employer', 'underwriter', 'middle'])}
                                                                <input type="button"
                                                                       data-group="{$group['id']}"
                                                                       data-standart-percents="{$group['standart_percents']}"
                                                                       data-preferential-percents="{$group['preferential_percents']}"
                                                                       class="btn btn-outline-warning edit-company-tarif"
                                                                       value="Ред"></td>
                                                            {/if}
                                                        </tr>
                                                    {/foreach}
                                                {/if}
                                                </tbody>
                                            </table>
                                        </div>
                                    {/if}
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
                                                <label class="control-label">Регулярная ставка, % в день</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="percent"
                                                       {if $loantype}value="{$loantype->percent|number_format:3:',':' '}" {/if} {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Льготная ставка, % в день</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input type="text" class="form-control" name="profunion"
                                                       {if $loantype}value="{$loantype->profunion|number_format:3:',':' '}" {/if} {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Льготный период до первой выплаты %, дней</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select class="form-control" name="free_days" {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}>
                                                    {for $i=1 to 30}
                                                        <option value="{$i}" {if empty($loantype->free_days) && $i == 3}selected{/if}>{$i}</option>
                                                    {/for}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Льготный период до первой выплаты по осн.долгу, дней</label>
                                            </div>
                                            <div class="col-7 ">
                                                <select class="form-control" name="min_period" {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}>
                                                    {for $i=1 to 30}
                                                        <option value="{$i}" {if empty($loantype->free_days) && $i == 20}selected{/if}>{$i}</option>
                                                    {/for}
                                                </select>
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
                                                       value="{$loantype->min_amount|number_format:0:',':' '}"
                                                       {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
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
                                                       value="{$loantype->max_amount|number_format:0:',':' '}"
                                                       {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-5 ">
                                                <label class="control-label">Количество выплат</label>
                                            </div>
                                            <div class="col-7 ">
                                                <input
                                                    id="number_of_payouts"
                                                    type="number"
                                                    class="form-control"
                                                    name="max_period"
                                                    value="{$loantype->max_period}"
                                                    {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}
                                                />
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
                                                <select class="form-control" name="reason_flag"
                                                        {if in_array($manager->role, ['employer', 'underwriter', 'middle'])}disabled{/if}>
                                                    <option value="1" {if $loantype->reason_flag == 1} selected{/if}>На
                                                        неотложные нужды
                                                    </option>
                                                    <option value="2" {if $loantype->reason_flag == 2} selected{/if}>На
                                                        рефинансирование обязательств перед третьими лицами
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if !in_array($manager->role, ['employer', 'underwriter', 'middle'])}
                            <div class="col-12">
                                <hr class="m-2"/>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
                                </div>
                            </div>
                        {/if}
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
                <h4 class="modal-title">Редактировать компанию</h4>
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


