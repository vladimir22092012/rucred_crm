{$meta_title = 'Общие Настройки' scope=parent}

{capture name='page_scripts'}
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
        type="text/javascript"></script>
    <script
        src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
<script>
    $(function () {

        $(document).on('click', '.delete_user', function() {
            let email = $(this).attr('data-email'),
                id = $(this).attr('data-id'),
                data = {
                    action: 'delete_user',
                    userId: id
                }

            Swal.fire({
                title: 'Вы действительно хотите удалить пользователя ' + email + '?',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: "Удалить",
                cancelButtonText: "Отмена",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.value !== undefined) {
                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: data,
                        success: function (resp) {
                            if(resp['error']){
                                Swal.fire({
                                    title: resp['error'],
                                    confirmButtonText: 'ОК'
                                });
                            } else {
                                $('#user_'+id).remove();
                            }
                        }
                    })
                }
            });
        })

        $('.search').on('click', function (e) {
            e.preventDefault();

            let fields = $('.js-search-fields input'),
                data = {
                action: 'search_users',
            }

            let table = $('#clients');
            table.hide();
            table.find('tbody').html('');

            fields.map((key, field) => {
                data[$(field).attr('name')] = $(field).val();
            })
            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: data,
                success: function (resp) {
                    if(resp['error']){
                        Swal.fire({
                            title: resp['error'],
                            confirmButtonText: 'ОК'
                        });
                    }else{
                        if (resp.users.length > 0) {
                            resp.users.forEach((client) => {
                                let html = `
                                    <tr id='user_`+client.id+`'>
                                        <td>`+client.personal_number+`</td>
                                        <td>`+client.firstname+` `+client.lastname+` `+client.patronymic+`</td>
                                        <td>`+client.phone_mobile+` / `+client.email+`</td>
                                        <td>
                                            №`+client.passport_serial+`
                                            выдан `+client.passport_date+`
                                            `+client.passport_issued+`
                                            `+client.subdivision_code+`
                                        </td>
                                        <td>`+client.inn+` / `+client.snils+`</td>
                                        <td>где замечен</td>
                                        <td>`+client.companyName+`</td>
                                        <td>заявки</td>
                                        <td>сделки</td>
                                        <td>
                                            <button class='btn btn-xs btn-danger delete_user'
                                                    data-email='`+client.email+`'
                                                    data-id='`+client.id+`'>
                                                Удалить
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                table.find('tbody').append(html);
                            });
                            table.show();
                        }
                    }
                }
            })
        })

        $(document).on('input', '.mask_number', function () {
            let value = $(this).val();
            value = value.replace(new RegExp(/[^.\d]/, 'g'), '');
            $(this).val(value);
        });

        $.fn.setCursorPosition = function (pos) {
            if ($(this).get(0).setSelectionRange) {
                $(this).get(0).setSelectionRange(pos, pos);
            } else if ($(this).get(0).createTextRange) {
                var range = $(this).get(0).createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        };

        $('.phone_mask').click(function () {
            $(this).setCursorPosition(3);
        }).mask('+7(999)999-99-99');

        $('.inn_mask').click(function() {
            $(this).setCursorPosition(0);
        }).mask('999999999999');

        $('.snils_mask').click(function () {
            $(this).setCursorPosition(0);
        }).mask('999-999-999 99');

        $('.passport_mask').click(function () {
            $(this).setCursorPosition(0);
        }).mask('99 99 999999');

        Inputmask({
            casing: 'upper'
        }).mask($('.casing-upper-mask'));
    });
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
                    Удаление тестовых клиентов и их заявок
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Удаление тестовых клиентов и их заявок</li>
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

            <div class="card">
                <div class="card-body js-search-fields">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="box-title">
                                    Введите данные клиента
                                </h3><br>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2">
                                    <input type="text" placeholder="Телефон" name="phone_mobile" class="form-control phone_mask" autocomplete="off">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" name="email" class="form-control email_mask casing-upper-mask" placeholder="Email" autocomplete="off">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" name="passport_serial" class="form-control passport_mask" placeholder="Паспортные данные" autocomplete="off">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" name="inn" class="form-control inn_mask" placeholder="ИНН" autocomplete="off">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" name="snils" class="form-control snils_mask" placeholder="Снилс" autocomplete="off">
                                </div>
                                <div class="col-lg-2">
                                    <div class="btn btn-outline-info search">Найти</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <table class="jsgrid-table table table-striped table-hover" style="display: none" id="clients">
                                <thead>
                                    <tr>
                                        <th>Номер клиента</th>
                                        <th>ФИО</th>
                                        <th>Телефон / Email</th>
                                        <th>Паспортные данные</th>
                                        <th>ИНН / СНИЛС</th>
                                        <th>В каких реестрах отметился</th>
                                        <th>Работодатель</th>
                                        <th>Наличие сформированных заявок</th>
                                        <th>Наличие сделок</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mb-3 mt-3"/>
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




