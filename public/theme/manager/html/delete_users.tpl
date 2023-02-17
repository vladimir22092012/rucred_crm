{$meta_title = 'Общие Настройки' scope=parent}

{capture name='page_scripts'}
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
        type="text/javascript"></script>
<script>
    $(function () {
        $('.delete').on('click', function (e) {
            e.preventDefault();

            let fields = $('.js-search-fields input'),
                data = {
                action: 'delete_user',
            }

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
                        Swal.fire({
                            title: 'Клиенты успешно удалены',
                            confirmButtonText: 'ОК'
                        });
                    }
                }
            })
        })

        $(document).on('input', '.mask_number', function () {
            let value = $(this).val();
            value = value.replace(new RegExp(/[^.\d]/, 'g'), '');
            $(this).val(value);
        });

        $('.mask_number').click().mask('+7(999)999-99-99');
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
                        <div class="col-md-12">
                            <h3 class="box-title">
                                Введите данные клиента
                            </h3><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="text" placeholder="Телефон" name="phone_mobile" class="form-control mask_number" autocomplete="off">
                            <p style="text-align: center; margin-top: 10px;margin-bottom: 10px;">ИЛИ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="text" name="email" class="form-control" placeholder="Email" autocomplete="off">
                            <p style="text-align: center; margin-top: 10px;margin-bottom: 10px;">ИЛИ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="text" name="passport_serial" class="form-control" placeholder="Паспортные данные" autocomplete="off">
                            <p style="text-align: center; margin-top: 10px;margin-bottom: 10px;">ИЛИ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="text" name="inn" class="form-control" placeholder="ИНН" autocomplete="off">
                            <p style="text-align: center; margin-top: 10px;margin-bottom: 10px;">ИЛИ</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="text" name="snils" class="form-control" placeholder="Снилс" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="btn btn-outline-danger delete" style="margin-top: 15px">Удалить</div>
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




