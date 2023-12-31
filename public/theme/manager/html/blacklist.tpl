{$meta_title="Blacklist" scope=parent}

{capture name='page_scripts'}
<script>
    function ImportApp()
    {
        var app = this;
        
        app.count = 1;
        app.total_updated = 0;
        app.total_added = 0;
        app.remove_all = '{$remove_all}';
        
        
        app.import = function(from){
    		from = typeof(from) != 'undefined' ? from : 0;
    		$.ajax({
 			 	type: 'POST',
                data: {
                    from: from,
                    import: 1,
                    remove_all: app.remove_all
                },
 			 	dataType: 'json',
  				success: function(data){

   					$("#progressbar").css({ width: 100*data.from/data.totalsize +'%'});
                    $("#progressbar").html((parseInt(10000*data.from/data.totalsize))/100 +'%')
                    
                    
    				if(data != false && !data.end)
    				{
    					app.import(data.from);
    				}
    				else
    				{
    					$("#progressbar").css({ width: '100%'}).removeClass('active');
    					in_process = false;
                        $("#progressbar").html('Загрузка завершена');
    				}
  				},
				error: function(xhr, status, errorThrown) {
					alert(errorThrown+'\n'+xhr.responseText);
        		}  				
    		});
	
	
        };
        
        _init_submit = function(){
            
        };
        
        ;(function(){
            if ($('#go_import').length > 0)
            {
                app.import(0);
            }
            else
            {
                _init_submit();
            }
        })();
    }
    
    $(function(){
        new ImportApp();
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
                <h3 class="text-themecolor mb-0 mt-0">
                    Blacklist
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Blacklist</li>
                </ol>
            </div>
        </div>
        {if isset($complete)}
            <div class="col-md-2 col-4">
                <ul class="alert alert-success" style="list-style-type: none">
                    <li>{$complete}</li>
                </ul>
            </div>
        {/if}
        {if isset($error)}
            <div class="col-md-2 col-4">
                <ul class="alert alert-danger" style="list-style-type: none">
                    <li>{$error}</li>
                </ul>
            </div>
        {/if}
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        <div class="row">
            <div class="col-md-12">
                
                {if $go_import}
                
                <div class="card" id="go_import">
                    <div class="card-body">
                        <div class="js-import-process">
                            <h4 class="card-title">Загрузка</h4>
                            <div class="collapse mt-3" id="pgr2"> <pre class="line-numbers language-javascript"><code>&lt;div class="progress"&gt;<br>&lt;div class="progress-bar bg-success" role="progressbar" style="width: 75%;height:15px;" role="progressbar""&gt; 75% &lt;/div&gt;<br>&lt;/div&gt;</code></pre> </div>
                            <div class="progress mt-3">
                                <div id="progressbar" class="progress-bar progress-bar-striped active bg-success" style="width: 0%; height:25px;" role="progressbar">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {else}
                
                <div class="card card-outline-info">
                    <div class="card-body">
                        <form action="{url}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                            
                            {if $error}
                            <div class="alert alert-danger">
                                {$error}
                            </div>
                            {/if}
                            
                            {if $success}
                            <div class="alert alert-success">
                                {$success}
                            </div>
                            {/if}
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="new_image_input" class="form-group">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="hidden" name="run" value="1" />
                                                <input type="file" name="import_file" class="custom-file-input js-image-input" id="" />
                                                <label style="white-space: nowrap;overflow: hidden;" class="custom-file-label" for="">Выбрать</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="checkbox checkbox-danger">
                                            <input type="checkbox" name="remove_all" value="1" id="remove_all" checked="" />
                                            <label for="remove_all">Удалить предыдущие записи</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Начать импорт</button>
                                </div>
                            </div>
                            
                            <hr />
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                    Для загрузки используйте формат csv с разделителями "точка с запятой", либо xls формат
                                    <br />
                                    В первой строке должны быть наименования ячеек (телефон, фио).
                                    <br />
                                    <a href="/files/import/example.csv">скачать пример файла</a>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
                
                {/if}
                
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


