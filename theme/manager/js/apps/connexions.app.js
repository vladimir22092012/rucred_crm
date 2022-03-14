function ConnexionsApp()
{
    var app = this;
    
    app.init = function(){
        $('.js-run-connexions').click(function(e){
            e.preventDefault();
            
            if ($(this).hasClass('loading'))
                return false;
            
            app.run();
        })
    }
    
    app.run = function(){
        var _user = $('.js-app-connexions').data('user');
        $.ajax({
            url: 'ajax/connexions.php',
            data: {
                user_id: _user
            },
            beforeSend: function(){
                $('.js-app-connexions').html('<h3 class="m-5 p-5 text-center">Загрузка</h3>')
                $('.js-run-connexions').addClass('loading');
            },
            success: function(resp){
                $('.js-app-connexions').html(resp)
                
                $('.js-run-connexions').removeClass('loading');
            }
        })
    }
    
    ;(function(){
        app.init();
    })();
}

$(function(){
    if ($('.js-app-connexions').length > 0)
        new ConnexionsApp();
})