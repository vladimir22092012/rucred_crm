function SudblockContractsApp()
{
    var app = this;
    
    app.update_page_interval;
    
    var _init = function(){
        
        _init_change_manager();
        
        _init_pagination();

        _init_sortable();
        _init_filter();
        _init_period();
        _init_filter_status();
        _init_filter_client();        
        _init_workout();
        
    };
    
    
    
    var _init_change_manager = function(){
        $(document).on('change', '.js-collection-manager', function(){
            var manager_id = $(this).val();
            var contract_id = $(this).data('contract');
            
            var manager_name = $(this).find('option:selected').text();
            
            $.ajax({
                type: 'POST',
                data: {
                    action: 'manager',
                    manager_id: manager_id,
                    contract_id: contract_id
                },
                success: function(resp){
                    if (manager_id == 0)
                        $('.js-collection-manager-block.js-dopinfo-'+contract_id).html('');
                    else
                        $('.js-collection-manager-block.js-dopinfo-'+contract_id).html(manager_name);
                }
            })
        })
        
    }

    
    
    var _init_period = function(){
        $(document).on('click', '.js-period-link', function(e){
            e.preventDefault();
            
            $('.js-daterange-filter').hide();
            
            var _url = $(this).attr('href');
            
            app.load(_url, false);
        });

        $(document).on('change', '.js-manager-filter', function(e){
            e.preventDefault();
            
            var _url = $(this).find('option:selected').val();
            
            app.load(_url, false);
        });
    };
    
    var _init_pagination = function(){
        $(document).on('click', '.jsgrid-pager a', function(e){
            e.preventDefault();
            
            var _url = $(this).attr('href');
            
            app.load(_url, true);
        });
        
        $(document).on('change', '.js-page-count', function(e){
            e.preventDefault();
            
            var _url = $(this).val();
            
            app.load(_url, true);
        });
        
    };
    
    var _init_sortable = function(){
        $(document).on('click', '.jsgrid-header-sortable a', function(e){
            e.preventDefault();
            
            var _url = $(this).attr('href');
            
            app.load(_url, true);
        });
    };
    
    var _init_filter_status = function(){
        $(document).on('click', '.js-filter-status a', function(e){
            e.preventDefault();
            
            var _url = $(this).attr('href');
            
            app.load(_url, false);
        });
    };
    
    var _init_filter_client = function(){
        $(document).on('click', '.js-filter-client a', function(e){
            e.preventDefault();
            
            var _url = $(this).attr('href');
            
            app.load(_url, false);
        });
    };
    
    var _init_filter = function(){
//        $(document).on('blur', '.jsgrid-filter-row input', app.filter);
        $(document).on('keyup', '.jsgrid-filter-row input', function(e){
            if (e.keyCode == 13){
                app.filter();
            }   
        });
        $(document).on('change', '.jsgrid-filter-row input', app.filter);
        $(document).on('change', '.jsgrid-filter-row select', app.filter);
    };
    
    app.filter = function(){
        var $form = $('#search_form');
        var _sort = $form.find('[name=sort]').val()
        var _searches = {};
        $form.find('input[type=text], select').each(function(){
            if ($(this).val() != '')
            {
                _searches[$(this).attr('name')] = $(this).val();
            }
        });     
        
        var _request = {
//            search: _searches,
            sort: _sort,
        };
        var _query = Object.keys(_request).map(
            k => encodeURIComponent(k) + '=' + encodeURIComponent(_request[k])
        ).join('&');

        _request.search = _searches;
        if (!$.isEmptyObject(_searches))
        {
            _query_searches = '';
            for (key in _searches) {
              _query_searches += '&search['+key+']='+_searches[key];
            }
            _query += _query_searches;
        }
        
        _request.manager_id = {};
        $('.js-filter-managers:checked').each(function(k, v){
            _request.manager_id[k] = $(this).val();
        })
        
        _request.status = {};
        $('.js-filter-statuses:checked').each(function(k, v){
            _request.status[k] = $(this).val();
        })
        
console.log(_request)        
        
        $.ajax({
            data: _request,
            beforeSend: function(){
            },
            success: function(resp){
                
                $('#basicgrid .jsgrid-grid-body').html($(resp).find('#basicgrid .jsgrid-grid-body').html());
                $('#basicgrid .jsgrid-header-row').html($(resp).find('#basicgrid .jsgrid-header-row').html());
                $('#basicgrid .jsgrid-footer-row').html($(resp).find('#basicgrid .jsgrid-footer-row').html());

                $('.jsgrid-pager-container').html($(resp).find('.jsgrid-pager-container').html() || '');
                
                if (!!_query)
                    location.hash = _query;
            }
        })
    
    };
    
    var _init_workout = function(){
        $(document).on('change', '.js-workout-input', function(){
            var $this = $(this);
            
            var _order = $this.val();
            var _workout = $this.is(':checked') ? 1 : 0;
                
            $.ajax({
                url: 'order/'+_order,
                type: 'POST',
                data: {
                    action: 'workout',
                    order_id: _order,
                    workout: _workout
                },
                beforeSend: function(){
                    $('.jsgrid-load-shader').show();
                    $('.jsgrid-load-panel').show();
                },
                success: function(resp){
                    
                    if (_workout)
                        $this.closest('.js-order-row').addClass('workout-row');
                    else
                        $this.closest('.js-order-row').removeClass('workout-row');

                    $('.jsgrid-load-shader').hide();
                    $('.jsgrid-load-panel').hide();
                        
                    /*
                    $.ajax({
                        success: function(resp){
                            $('#basicgrid .jsgrid-grid-body').html($(resp).find('#basicgrid .jsgrid-grid-body').html());
                            $('#basicgrid .jsgrid-header-row').html($(resp).find('#basicgrid .jsgrid-header-row').html());
                            $('.js-period-filter').html($(resp).find('.js-period-filter').html());
                            $('.js-filter-status').html($(resp).find('.js-filter-status').html());
                            $('.js-filter-client').html($(resp).find('.js-filter-client').html());
            
                            $('.jsgrid-pager-container').html($(resp).find('.jsgrid-pager-container').html());

                            $('.jsgrid-load-shader').hide();
                            $('.jsgrid-load-panel').hide();
                        }
                    });
                    */
                }
            })
                
        });
    }
    
    app.load = function(_url, loading){

        var _split = _url.split('?');

        $.ajax({
            url: _url,
            beforeSend: function(){
                if (loading)
                {
                    $('.jsgrid-load-shader').show();
                    $('.jsgrid-load-panel').show();
                }
            },
            success: function(resp){
                
                $('#basicgrid .jsgrid-grid-body').html($(resp).find('#basicgrid .jsgrid-grid-body').html());
                $('#basicgrid .jsgrid-header-row').html($(resp).find('#basicgrid .jsgrid-header-row').html());
                $('#basicgrid .jsgrid-footer-row').html($(resp).find('#basicgrid .jsgrid-footer-row').html());
                $('.js-period-filter').html($(resp).find('.js-period-filter').html());
                $('.js-filter-status').html($(resp).find('.js-filter-status').html());
                $('.js-filter-client').html($(resp).find('.js-filter-client').html());

                $('.jsgrid-pager-container').html($(resp).find('.jsgrid-pager-container').html() || '');
                
                if (!!_split[1])
                    location.hash = _split[1];

                if (loading)
                {
                    $('html, body').animate({
                        scrollTop: $("#basicgrid").offset().top-80  
                    }, 1000);
                    
                    $('.jsgrid-load-shader').hide();
                    $('.jsgrid-load-panel').hide();
                }
                 
            }
        })
    };
    
    ;(function(){
        
        _init();
        
        if (location.hash != '#' && location.hash != '')
        {
            app.load((location.href).replace('#', '?'));
        }
        
    })();
};

new SudblockContractsApp();