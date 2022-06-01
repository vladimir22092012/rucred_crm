{$meta_title = 'Настройки cкорингов' scope=parent}

{capture name='page_scripts'}

    <script src="theme/{$settings->theme}/assets/plugins/nestable/jquery.nestable.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        // Nestable
        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };
        
        $('#nestable2').nestable({
            group: 1
        }).on('change', updateOutput);

        updateOutput($('#nestable2').data('output', $('#nestable2-output')));

    });
    </script>

{/capture}

{capture name='page_styles'}

    <!--nestable CSS -->
    <link href="theme/{$settings->theme}/assets/plugins/nestable/nestable.css" rel="stylesheet" type="text/css" />

    <style>
        .onoffswitch {
            display:inline-block!important;
            vertical-align:top!important;
            width:60px!important;
            text-align:left;
        }
        .onoffswitch-switch {
            right:38px!important;
            border-width:1px!important;
        }
        .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
            right:0px!important;
        }
        .onoffswitch-label {
            margin-bottom:0!important;
            border-width:1px!important;
        }
        .onoffswitch-inner::after, 
        .onoffswitch-inner::before {
            height:18px!important;
            line-height:18px!important;
        }
        .onoffswitch-switch {
            width:20px!important;
            margin:1px!important;
        }
        .onoffswitch-inner::before {
            content:'ВКЛ'!important;
            padding-left: 10px!important;
            font-size:10px!important;
        }
        .onoffswitch-inner::after {
            content:'ВЫКЛ'!important;
            padding-right: 6px!important;
            font-size:10px!important;
        }
        
        .scoring-content {
            position:relative;
            z-index:999;
            border:1px solid rgba(120, 130, 140, 0.13);;
            border-top:0;
            background:#fff;
            border-bottom-left-radius:4px;
            border-bottom-right-radius:4px;
            margin-top: -5px;
        }
        
        .collapsed .fa-minus-circle::before {
            content: "\f055";
        }
        h4.text-white {
            display:inline-block
        }
        .move-zone {
            display:inline-block;
            color:#fff;
            padding-right:15px;
            margin-right:10px;
            border-right:1px solid #30b2ff;
            cursor:move
        }
        .move-zone span {
            font-size:24px;
        }
        
        .dd {
            max-width:100%;
        }
    </style>
{/capture}

{function name=display_scoring}

<div class="col-12 grid-stack-item " data-gs-no-resize="yes" data-gs-x="0" data-gs-y="{$z}" data-gs-width="12" data-gs-height="1">
    <div class="card card-outline-info">
        <div class="card-header grid-stack-item-content">
            <div class="row">
                <div class="col-8 col-md-9 col-lg-10 text-left">
                    {if !in_array($manager->role, ['employer', 'underwriter'])}
                    <div class="move-zone">
                        <span class="mdi mdi-arrow-all"></span>
                    </div>
                    {/if}
                    <h4 class="mb-0 text-white ">
                        <a href="#{$scoring_name}_content" data-toggle="collapse" class="text-white collapsed">
                            {if !in_array($manager->role, ['employer', 'underwriter'])} <i class="fas fa-minus-circle"></i>{/if}
                            <span>
                                {if $scoring_name == 'local_time'}Локальное время
                                {elseif $scoring_name == 'location'}Местоположение
                                {elseif $scoring_name == 'fssp'}ФССП
                                {elseif $scoring_name == 'fms'}ФМС
                                {elseif $scoring_name == 'fns'}ФНС
                                {elseif $scoring_name == 'scorista'}Скориста
                                {elseif $scoring_name == 'juicescore'}Juicescore
                                {elseif $scoring_name == 'mbki'}МБКИ
                                {else}{$scoring_name}{/if}
                            </span>
                        </a>
                    </h4>
                </div>
                {if !in_array($manager_role, ['employer', 'underwriter'])}
                <div class="col-4 col-md-3 col-lg-2 text-right ">
                    <div class="onoffswitch">
                        <input type="checkbox" name="settings[{$scoring_name}][active]" class="onoffswitch-checkbox" value="1" id="{$scoring_name}_active" {if $scoring_params['active']}checked="true"{/if}
                                disabled/>
                        <label class="onoffswitch-label" for="{$scoring_name}_active">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                {/if}
            </div>
        </div>
        <div id="{$scoring_name}_content" class="card-body collapse scoring-content">
            <div class="row">

                {if $scoring_name == 'local_time'}
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="control-label">Максимальное отклонение, сек</label>
                        <input type="text" name="settings[local_time][max_diff]" value="{$scoring_settings['local_time']['max_diff']}" class="form-control" placeholder="" />
                    </div>
                </div>

                {elseif $scoring_name == 'location'}
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="control-label">Список регионов</label>
                        <textarea name="settings[location][regions]" class="form-control">{$scoring_settings['location']['regions']}</textarea>
                    </div>
                </div>

                {elseif $scoring_name == 'fssp'}
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="control-label">Сумма долга, руб</label>
                        <input type="text" name="settings[fssp][amount]" value="{$scoring_settings['fssp']['amount']}" class="form-control" placeholder="" />
                    </div>
                </div>

                {elseif $scoring_name == 'fms'}


                {elseif $scoring_name == 'fns'}


                {elseif $scoring_name == 'scorista'}
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="control-label">Проходной бал</label>
                        <input type="text" name="settings[scorista][scorebal]" value="{$scoring_settings['scorista']['scorebal']}" class="form-control" placeholder="" />
                    </div>
                </div>


                {elseif $scoring_name == 'juicescore'}
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="control-label">Проходной бал</label>
                        <input type="text" name="settings[juicescore][scorebal]" value="{$scoring_settings['juicescore']['scorebal']}" class="form-control" placeholder="" />
                    </div>
                </div>


                {elseif $scoring_name == 'mbki'}
                
                
                {/if}
                
            </div>
        </div>
    </div>
</div>

{/function}

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
                    Настройки скорингов
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Скоринги</li>
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
            
        <div class="row grid-stack" data-gs-width="12" data-gs-animate="yes">

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Скоринги</h4>
            <div class="myadmin-dd-empty dd" id="nestable2">
                <ol class="dd-list">
                    {foreach $scoring_types as $type}
                    <li class="dd-item dd3-item" data-id="{$type->id}">
                        {if !in_array($manager_role, ['employer', 'underwriter'])}<div class="dd-handle dd3-handle">
                            <input type="hidden" name="position[]" value="{$type->id}" />
                            <input type="hidden" name="settings[{$type->id}][id]" value="{$type->id}" />
                        </div>{/if}
                        <div class="dd3-content"> 
                            <div class="row">
                                <div class="col-8 col-sm-9 col-md-10">
                                    <a {if !in_array($manager_role, ['employer', 'underwriter'])} href="#content_{$type->id}" data-toggle="collapse" class="text-info collapsed"{/if}>
                                        {if !in_array($manager_role, ['employer', 'underwriter'])}<i class="fas fa-minus-circle"></i>{/if}
                                        <span>
                                            {$type->title}
                                        </span>
                                        {if $type->negative_action=='stop'}
                                        <span class="label label-danger">Остановить</span>
                                        {/if}
                                        {if $type->negative_action=='next'}
                                        <span class="label label-primary">Продолжить</span>
                                        {/if}
                                    </a>                                    
                                </div>
                                <div class="col-4 col-sm-3 col-md-2">
                                    <div class="onoffswitch">
                                        <input {if in_array($manager_role, ['employer', 'underwriter'])}disabled{/if} type="checkbox" name="settings[{$type->id}][active]" class="onoffswitch-checkbox" value="1" id="active_{$type->id}" {if $type->active}checked="true"{/if} />
                                        <label class="onoffswitch-label" for="active_{$type->id}">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>        
                                </div>
                            </div>
                        </div>
                        
                        <div id="content_{$type->id}" class="card-body collapse scoring-content">
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Если получен негативный тест</label>
                                        <select name="settings[{$type->id}][negative_action]" class="form-control">
                                            <option value="stop" {if $type->negative_action=='stop'}selected="true"{/if}>Остановить проверку</option>
                                            <option value="next" {if $type->negative_action=='next'}selected="true"{/if}>Продолжить проверку</option>
                                        </select>
                                    </div>
                                </div>
                                
                                {if $type->name == 'local_time'}
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Максимальное отклонение, сек</label>
                                        <input type="text" name="settings[{$type->id}][params][max_diff]" value="{$type->params['max_diff']}" class="form-control" placeholder="" />
                                    </div>
                                </div>
                                
                                {elseif $type->name == 'location'}
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Список регионов</label>
                                        <textarea name="settings[{$type->id}][params][regions]" class="form-control">{$type->params['regions']}</textarea>
                                    </div>
                                </div>
                
                                {elseif $type->name == 'fssp'}
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Сумма долга, руб</label>
                                        <input type="text" name="settings[{$type->id}][params][amount]" value="{$type->params['amount']}" class="form-control" placeholder="" />
                                    </div>
                                </div>
                
                                {elseif $type->name == 'fms'}
                
                
                                {elseif $type->name == 'fns'}
                
                
                                {elseif $type->name == 'scorista'}
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Проходной бал</label>
                                        <input type="text" name="settings[{$type->id}][params][scorebal]" value="{$type->params['scorebal']}" class="form-control" placeholder="" />
                                    </div>
                                </div>
                
                
                                {elseif $type->name == 'juicescore'}
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label class="control-label">Проходной бал</label>
                                        <input type="text" name="settings[{$type->id}][params][scorebal]" value="{$type->params['scorebal']}" class="form-control" placeholder="" />
                                    </div>
                                </div>
                
                
                                {elseif $type->name == 'mbki'}
                                
                                
                                {/if}
                                
                            </div>
                        </div>              
                        
                    </li>
                    {/foreach}
                    
                </ol>
            </div>
        </div>
    </div>
</div>




            
{*$z = 0}
{foreach $scoring_settings as $scoring_name => $scoring_params}
    {display_scoring scoring_name = $scoring_name scoring_params = $scoring_params z = $z}
    {$z = $z + 1}
{/foreach*}
           
        </div>
        
        <hr class="mb-3 mt-3" />
        
        <div class="row">
            {if !in_array($manager_role, ['employer', 'underwriter'])}
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




