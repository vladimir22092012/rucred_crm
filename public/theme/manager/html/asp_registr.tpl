{$meta_title = 'ЭЦП реестр' scope=parent}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css"
          href="theme/manager/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
{/capture}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="theme/manager/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <script>
        $(function () {

            let document_link = $('#document_href').attr('href');

            $('#document_link').on('change', function () {
                let document_id = $(this).val();

                $(this).closest('td').find('#document_href').attr('href', document_link + document_id);
            });

            $('.searchable:not(select)').on('change', function (e) {
                e.preventDefault();

                $('table tbody tr').show();

                $('.searchable:not(select)').each(function () {
                    let value = $(this).val();
                    let index = $(this).parent().index() + 1;

                    if (value && value.length > 0) {
                        $('td:nth-child(' + index + ')').each(function () {
                            let find_value = $(this).text().toLowerCase();
                            if (find_value.includes(value) === false) {
                                $(this).closest('tr[class="codes"]').hide();
                            }
                        });
                    }
                });
            });

            $('#manager_filter').on('change', function () {
                let value = $(this).val();

                if (value != 'none') {
                    $('tr[class="codes"]').show();
                    $('tr[class="codes"]').find('td[class="manager_id"]').not('#' + value + '').parent().hide();
                }
                else {
                    $('tr[class="codes"]').show();
                }

            });

            $('#type_filter').on('change', function () {
                let value = $(this).val();

                if (value != 'none') {
                    $('tr[class="codes"]').show();
                    $('tr[class="codes"]').find('td[class="code_type"]').not('#' + value + '').parent().hide();
                }
                else {
                    $('tr[class="codes"]').show();
                }

            });
        })
    </script>
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
                <h3 class="text-themecolor mb-0 mt-0">ЭЦП реестр</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">ЭЦП реестр</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"></h4>
                        <h6 class="card-subtitle"></h6>
                        <div class="table-responsive m-t-40">
                            <div class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <table id="config-table" class="table display table-striped dataTable" style="text-align: center; font-size: 13px">
                                    <thead>
                                    <tr>
                                        <th>ID ПЭПа</th>
                                        <th>Дата и врема</th>
                                        <th style="width: 180px">Пользователь</th>
                                        <th>Список документов</th>
                                        <th style="width: 100px">Канал запроса ПЭП</th>
                                        <th>Куда отправлен код</th>
                                        <th>Код подтверждения</th>
                                        <th>Ссылка на заявку / сделку</th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td>
                                            <select class="form-control" id="manager_filter">
                                                <option value="none">Пользователь</option>
                                                {foreach $managers as $manager}
                                                    <option value="{$manager->id}">{$manager->name}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td></td>
                                        <td>
                                            <select class="form-control" id="type_filter">
                                                <option value="sms">смс</option>
                                                <option value="email">почта</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td><input type="text" class="form-control searchable"></td>
                                        <td><input type="text" class="form-control searchable"></td>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body" style="font-size: 14px">
                                    {foreach $codes as $code}
                                        <tr class="codes">
                                            <td>
                                                {$code->id}
                                            </td>
                                            <td>
                                                {$code->created|date} {$code->created|time}
                                            </td>
                                            <td class="manager_id" id="{$code->manager->id}">
                                                <a target="_blank"
                                                   href="{$config->back_url}/manager/{$code->manager->id}">
                                                    {$code->manager->name}
                                                </a>
                                            </td>
                                            <td style="display: flex">
                                                <select class="form-control" id="document_link" style="width: 500px;">
                                                    {foreach $code->documents as $document}
                                                        <option value="{$document->id}">
                                                            {$document->numeration} {$document->name}
                                                        </option>
                                                    {/foreach}
                                                </select>
                                                <a target="_blank" id="document_href"
                                                   href="{$config->back_url}/document/">
                                                    <input type="button" class="btn btn-outline-info" value="Открыть"
                                                           style="margin-left: 5px"></a>
                                            </td>
                                            <td class="code_type" id="{$code->type}">
                                                {$code->type}
                                            </td>
                                            <td>
                                                {$code->recepient}
                                            </td>
                                            <td>
                                                {$code->code}
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                   href="{$config->root_url}/offline_order/{$code->order_id}">
                                                    {$code->order_id}</a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file='footer.tpl'}
</div>