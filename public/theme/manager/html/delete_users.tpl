{$meta_title = 'Общие Настройки' scope=parent}

{capture name='page_scripts'}
<script>
    $(function () {
        $('.delete').on('click', function (e) {
            e.preventDefault();

            let phone = $('input[name="phone"]').val();
            
            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'delete_user',
                    phone: phone
                },
                success: function (resp) {
                    if(resp['error']){
                        Swal.fire({
                            title: resp['error'],
                            confirmButtonText: 'ОК'
                        });
                    }else{
                        Swal.fire({
                            title: 'Клиент успешно удален',
                            confirmButtonText: 'ОК'
                        });
                    }
                }
            })
        })
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
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="box-title">
                                Введите номер клиента в формате 79999999999
                            </h3><br>
                            <div class="col-3" style="display: flex">
                                <input type="text" class="form-control" name="phone">
                                <div class="btn btn-outline-danger delete" style="margin-left: 25px">Удалить</div>
                            </div>
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




