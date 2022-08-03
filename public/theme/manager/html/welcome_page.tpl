{$meta_title='Добро пожаловать' scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script>
        $(function () {
            setTimeout(function () {
                $('#logo').fadeIn(1500);
            }, 500);

            setTimeout(function () {
                $('#welcome').fadeIn(1500);
            }, 1000);
        });
    </script>
{/capture}
{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
{/capture}
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Column -->
                <div>
                    <div class="card-body">
                        <div>
                            <br><br><br>
                        </div>
                        <div style="margin-left: 300px; width: 100%; display: none" id="logo">
                            <img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png">
                        </div>
                        <div>
                            <br><br><br><br><br><br><br>
                        </div>
                        <div style="margin-left: 380px; display: none" id="welcome">
                            <h1 style="color: #0b5ed7">Добро пожаловать!</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>