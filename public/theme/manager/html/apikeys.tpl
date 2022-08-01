{$meta_title = 'Ключи для API' scope=parent}

{capture name='page_scripts'}


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
                    Ключи для API
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Ключи для API</li>
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
        <form class="" method="POST" >

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Best2Pay
                                <a href="//best2pay.net" target="_blank"> <small> best2pay.net</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Пароль (сектор 2516)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[best2pay][2516]" value="{$apikeys['best2pay'][2516]}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Пароль (сектор 2241)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[best2pay][2241]" value="{$apikeys['best2pay'][2241]}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Пароль (сектор 2242)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[best2pay][2242]" value="{$apikeys['best2pay'][2242]}" placeholder=""{if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Пароль (сектор 2243)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[best2pay][2243]" value="{$apikeys['best2pay'][2243]}" placeholder=""{if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Пароль (сектор 2244)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[best2pay][2244]" value="{$apikeys['best2pay'][2244]}" placeholder=""{if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Звонобот
                                <a href="//zvonobot.ru/" target="_blank"> <small> zvonobot.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <h4>МКК</h4>
                            <div class="form-group mb-3">
                                <label class=" col-form-label">apiKey</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[zvonobot][apiKey]" value="{$apikeys['zvonobot']['apiKey']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class=" col-form-label">outgoingPhone</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[zvonobot][outgoingPhone]" value="{$apikeys['zvonobot']['outgoingPhone']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4>ЮК</h4>
                            <div class="form-group mb-3">
                                <label class=" col-form-label">apiKey</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[zvonobot_yuk][apiKey]" value="{$apikeys['zvonobot_yuk']['apiKey']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class=" col-form-label">outgoingPhone</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[zvonobot_yuk][outgoingPhone]" value="{$apikeys['zvonobot_yuk']['outgoingPhone']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>

                    </div>
                    <hr class="mt-3 mb-4" />

                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Cloudkassir
                                <a href="//cloudkassir.ru/" target="_blank"> <small> cloudkassir.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">ck_API</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[cloudkassir][ck_API]" value="{$apikeys['cloudkassir']['ck_API']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class=" col-form-label">ck_PublicId</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[cloudkassir][ck_PublicId]" value="{$apikeys['cloudkassir']['ck_PublicId']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">INN</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[cloudkassir][ck_INN]" value="{$apikeys['cloudkassir']['ck_INN']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Easy SMS
                                <a href="//smstec.ru" target="_blank"> <small> smstec.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">login</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[sms][login]" value="{$apikeys['sms']['login']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">password</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[sms][password]" value="{$apikeys['sms']['password']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">originator</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[sms][originator]" value="{$apikeys['sms']['originator']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">connect_id</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[sms][connect_id]" value="{$apikeys['sms']['connect_id']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-3 mb-4" />
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Dadata
                                <a href="//dadata.ru" target="_blank"> <small> dadata.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">API-ключ</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[dadata][api_key]" value="{$apikeys['dadata']['api_key']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">Секретный ключ для стандартизации</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[dadata][secret_key]" value="{$apikeys['dadata']['secret_key']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-3 mb-4" />
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Mango Office
                                <a href="//mango-office.ru" target="_blank"> <small> mango-office.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">API ключ (Уникальный код вашей АТС)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[mango][api_key]" value="{$apikeys['mango']['api_key']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">API соль (Ключ для создания подписи)</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[mango][api_salt]" value="{$apikeys['mango']['api_salt']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-3 mb-4" />
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                Anticaptcha
                                <a href="//anti-captcha.com" target="_blank"> <small> anti-captcha.com</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">API ключ</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[anticaptcha][api_key]" value="{$apikeys['anticaptcha']['api_key']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-3 mb-4" />
                    <div class="row">
                        <div class="col-12">
                            <h3 class="box-title">
                                ФССП
                                <a href="//fssp.gov.ru" target="_blank"> <small> fssp.gov.ru</small></a>
                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class=" col-form-label">API ключ</label>
                                <div class="">
                                    <input type="text" class="form-control" name="apikeys[fssp][api_key]" value="{$apikeys['fssp']['api_key']}" placeholder="" {if !in_array($manager->role, ['developer', 'admin'])}disabled{/if}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3 mb-4" />
                </div>
            </div>

            {if in_array($manager->role, ['developer', 'admin'])}
            <div class="col-12 grid-stack-item" data-gs-x="0" data-gs-y="0" data-gs-width="12">
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Сохранить</button>
                </div>
            </div>
            {/if}
        </form>
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