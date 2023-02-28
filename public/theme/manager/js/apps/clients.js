function ClientsApp()
{
    var app = this;

    var _init_pagination = function(){
        $(document).on('click', '.jsgrid-pager a', function(e){
            e.preventDefault();

            var _url = $(this).attr('href');

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

    var _init_filter = function(){
//        $(document).on('blur', '.jsgrid-filter-row input', app.filter);
        $(document).on('keyup', '.jsgrid-filter-row input', app.filter);
        $(document).on('change', '.jsgrid-filter-row select', app.filter);
    };

    var _init_daterange = function(){
      $('.daterange').daterangepicker({
        autoApply: true,
        locale: {
          format: 'DD.MM.YYYY'
        },
        default:''
      });

      $('.js-daterange-input').change(function(){
        var _url = $('.js-open-daterange').attr('href');
        _url += '&daterange=' + $(this).val();
        app.load(_url, false);
      })

      $('.js-open-daterange').click(function(e){
        e.preventDefault();

        $('#filter_period').val('optional')

        $('.js-period-filter button .js-period-filter-text').text('Произвольный')
        $('.js-period-filter .dropdown-item').removeClass('active');
        $(this).addClass('active')

        $('.js-daterange-filter').show();
      });
    }

    var _init_period = function(){
      $(document).on('click', '.js-period-link', function(e){
        e.preventDefault();

        $('.js-daterange-filter').hide();
        $('.js-period-filter button .js-period-filter-text').text($(this).text());
        $('.js-period-filter .dropdown-item').removeClass('active');
        $(this).addClass('active')
        var _url = $(this).attr('href');

        app.load(_url, false);
      });

  };

    var _init_sms

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
        $.ajax({
            data: {
                search: _searches,
                sort: _sort
            },
            beforeSend: function(){
            },
            success: function(resp){
                $('#blockstatuses').html($(resp).find('#blockstatuses').html());
                $('#basicgrid .jsgrid-grid-body').html($(resp).find('#basicgrid .jsgrid-grid-body').html());
                $('#basicgrid .jsgrid-pager-container').html($(resp).find('#basicgrid .jsgrid-pager-container').html());


            }
        })

    };

    app.load = function(_url, loading){
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
                $('#blockstatuses').html($(resp).find('#blockstatuses').html());
                $('#basicgrid').html($(resp).find('#basicgrid').html());

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

    var _init_sms = function(){
            $(document).on('click', '.js-open-sms-modal', function(e){
                e.preventDefault();

                var _user_id = $(this).data('user');
                var _order_id = $(this).data('order');
                var _yuk = $(this).hasClass('is-yuk') ? 1 : 0;

                $('#modal_send_sms [name=user_id]').val(_user_id)
                $('#modal_send_sms [name=order_id]').val(_order_id)
                $('#modal_send_sms [name=yuk]').val(_yuk)
                $('#modal_send_sms').modal();
            });

            $(document).on('submit', '.js-sms-form', function(e){
                e.preventDefault();

                var $form = $(this);

                var _user_id = $form.find('[name=id]').val();

                if ($form.hasClass('loading'))
                    return false;

                $.ajax({
                    url: 'missings/'+_user_id,
                    type: 'POST',
                    data: $form.serialize(),
                    beforeSend: function(){
                        $form.addClass('loading')
                    },
                    success: function(resp){
                        $form.removeClass('loading');
                        $('#modal_send_sms').modal('hide');

                        if (!!resp.error)
                        {
                            Swal.fire({
                                timer: 5000,
                                title: 'Ошибка!',
                                text: resp.error,
                                type: 'error',
                            });
                        }
                        else
                        {
                            Swal.fire({
                                timer: 5000,
                                title: '',
                                text: 'Сообщение отправлено',
                                type: 'success',
                            });

                        }
                    },
                })

            });
        }


    ;(function(){
        _init_pagination();
        _init_sortable();
        _init_filter();
        _init_sms();
        _init_daterange();
        _init_period();
    })();
}
new ClientsApp();
