{$meta_title='Продукты' scope=parent}

{capture name='page_scripts'}
    <script>
        $(function () {

            // Удаление записи
            $(document).on('click', '.js-delete-item', function (e) {
                e.preventDefault();

                var $item = $(this).closest('.js-item');

                var _id = $item.find('.js-item-id').val();
                var _name = $item.find('.js-item-name').val();

                Swal.fire({
                    text: "Вы действительно хотите удалить " + _name + "?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Да, удалить!",
                    cancelButtonText: "Отмена",
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                    },
                    allowOutsideClick: () => !Swal.isLoading()

                }).then((result) => {

                    if (result.value) {
                        $.ajax({
                            type: 'POST',
                            data: {
                                action: 'delete',
                                id: _id
                            },
                            success: function () {

                                $item.remove();

                                Swal.fire({
                                    timer: 5000,
                                    text: 'Вид кредитования удален!',
                                    type: 'success',
                                });
                            }
                        })
                    }
                });
            });


        })
    </script>
{/capture}

{capture name='page_styles'}
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/css-chart/css-chart.css"/>
    <style>
        .jsgrid-table {
            margin-bottom: 0
        }

        .label {
            white-space: pre;
        }

        .js-open-hide {
            display: block;
        }

        .js-open-show {
            display: none;
        }

        .open.js-open-hide {
            display: none;
        }

        .open.js-open-show {
            display: block;
        }

        .form-control.js-contactperson-status,
        .form-control.js-contact-status {
            font-size: 12px;
            padding-left: 0px;
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
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i>Продукты</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Продукты</li>
                </ol>
            </div>
            {if $manager->role != 'employer'}
                <div class="col-md-6 col-4 align-self-center">
                    <div class="text-right">
                        <a href="loantype" class="btn btn-success btn-large">
                            <i class="fas fa-plus-circle"></i>
                            <span>Добавить новый</span>
                        </a>
                    </div>
                </div>
            {/if}
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
                        <div class="clearfix">
                            <h4 class="card-title  float-left">Виды кредитования</h4>

                        </div>

                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row bg-grey">
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-align-left jsgrid-header-sortable {if $sort == 'order_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'order_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            Наименование
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-align-left jsgrid-header-sortable {if $sort == 'order_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'order_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            Тип
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Процентная ставка
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Льготная ставка
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Мин. сумма
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Макс.&nbsp;сумма
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Количество выплат
                                        </th>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tbody>
                                    {foreach $loantypes as $loantype}
                                        <tr class="jsgrid-row js-item">
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-left">
                                                <input type="hidden" class="js-item-id" value="{$loantype->id}"/>
                                                <input type="hidden" class="js-item-name" value="{$loantype->name}"/>
                                                <a href="loantype/{$loantype->id}">
                                                    <strong>{$loantype->name|escape}</strong>
                                                </a>
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-center">
                                                {if $loantype->type == 'annouitet'}
                                                    Аннуитет
                                                {else}
                                                    {$loantype->type|escape}
                                                {/if}
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-center">
                                                {$loantype->percent|number_format:3:',':' '}
                                            </td>
                                            <td style="width: 60px;" class="jsgrid-cell jsgrid-align-center">
                                                {$loantype->profunion|number_format:3:',':' '}
                                            </td>
                                            <td style="width: 70px;" class="jsgrid-cell jsgrid-align-center">
                                                {$loantype->min_amount|number_format:0:',':' '}
                                            </td>
                                            <td style="width: 70px;" class="jsgrid-cell jsgrid-align-center">
                                                {$loantype->max_amount|number_format:0:',':' '}
                                            </td>
                                            <td style="width: 70px;" class="jsgrid-cell jsgrid-align-center">
                                                {$loantype->max_period}
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>

                            {include file='pagination.tpl'}

                        </div>
                    </div>
                </div>
                <!-- Column -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
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

