function SmsTemplatesApp()
{
    var app = this;            
    
    var _init_events = function(){
        
        // редактирование записи
        $(document).on('click', '.js-edit-item', function(e){
            e.preventDefault();
            
            var $item = $(this).closest('.js-item');
            
            $item.find('.js-visible-view').hide();
            $item.find('.js-visible-edit').fadeIn();
        });
        
        // Удаление записи
        $(document).on('click', '.js-delete-item', function(e){
            e.preventDefault();
            
            var $item = $(this).closest('.js-item');
            
            var _id = $item.find('[name=id]').val();
            var _name = $item.find('[name=name]').val();
            
            Swal.fire({
                text: "Вы действительно хотите удалить шаблон"+_name+"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Да, удалить!",
                cancelButtonText: "Отмена",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                },
                allowOutsideClick: () => !Swal.isLoading()

            }).then((result) => {

                if (result.value) 
                {
                    $.ajax({
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: _id
                        },
                        success: function(){

                            $item.remove();

                            Swal.fire({
                              timer: 5000,
                              text: 'Шаблон удален!',
                              type: 'success',
                            });                                
                        }
                    })
                }
            });
        });
        
        
        // Сохранение редактируемой записи
        $(document).on('click', '.js-confirm-edit-item', function(e){
            e.preventDefault();
            
            var $item = $(this).closest('.js-item');
            
            var _id = $item.find('[name=id]').val();
            var _name = $item.find('[name=name]').val();
            var _template = $item.find('[name=template]').val();
            var _type = $item.find('[name=type]').val();
            
            $.ajax({
                type: 'POST',
                data: {
                    action: 'update',
                    id: _id,
                    name: _name,
                    type: _type,
                    template: _template,
                },
                success: function(resp){
                    if (!!resp.error)
                    {
                        Swal.fire({
                            text: resp.error,
                            type: 'error',
                        });                                
                        
                    }
                    else
                    {
                        $item.find('[name=name]').val(resp.name);
                        $item.find('.js-text-name').html(resp.name);
                        $item.find('[name=template]').val(resp.template);
                        $item.find('.js-text-template').html(resp.template);
                        $item.find('[name=type]').val(resp.type);
                        
                        if (resp.type == 'order')
                            $item.find('.js-text-type').html('Выдача');
                        else if (resp.type == 'collection')
                            $item.find('.js-text-type').html('Коллекшин');
                        else
                            $item.find('.js-text-type').html(resp.type);

                        $item.find('.js-visible-edit').hide();
                        $item.find('.js-visible-view').fadeIn();

                    }
                }
            });
            
        });
        
        // Отмена редактирования записи
        $(document).on('click', '.js-cancel-edit-item', function(e){
            e.preventDefault();
            
            var $item = $(this).closest('.js-item');
            
            $item.find('.js-visible-edit').hide();
            $item.find('.js-visible-view').fadeIn();
        });
        
        // Открытие окна для добавления
        $(document).on('click', '.js-open-add-modal', function(e){
            e.preventDefault();
            
            $('#modal_add_item').find('.alert').hide();
            $('#modal_add_item').find('[name=name]').val('');
            $('#modal_add_item').find('[name=template]').val('');
            $('#modal_add_item').find('[name=type]').val('');
            
            $('#modal_add_item').modal();
            
            $('#modal_add_item').find('[name=name]').focus();
        });
        
        // Сохранение новой записи
        $(document).on('submit', '#form_add_item', function(e){
            e.preventDefault();
            
            var $form = $(this);
            
            var _name = $form.find('[name=name]').val();
            var _type = $form.find('[name=type]').val();
            var _template = $form.find('[name=template]').val();
            
            $.ajax({
                type: 'POST',
                data: {
                    action: 'add',
                    name: _name,
                    type: _type,
                    template: _template,
                },
                beforeSend: function(){
                    
                },
                success: function(resp){
                    if (!!resp.error)
                    {
                        $form.find('.alert').removeClass('alert-success').addClass('alert-danger').html(resp.error).fadeIn();
                    }
                    else
                    {
                        var new_row = '<tr class="js-item">';
                        new_row += '<td><div class="js-text-id">'+resp.id+'</div></td>';
                        new_row += '<td>';
                        new_row += '<div class="js-visible-view js-text-name">'+resp.name+'</div>';
                        new_row += '<div class="js-visible-edit" style="display:none">';
                        new_row += '<input type="hidden" name="id" value="'+resp.id+'" />';
                        new_row += '<input type="text" class="form-control form-control-sm" name="name" value="'+resp.name+'" />';
                        new_row += '</div>';
                        new_row += '</td>';
                        new_row += '<td>';
                        new_row += '<div class="js-visible-view js-text-template">'+resp.template+'</div>';
                        new_row += '<div class="js-visible-edit" style="display:none">';
                        new_row += '<input type="text" class="form-control form-control-sm" name="template" value="'+resp.template+'" />';
                        new_row += '</div>';
                        new_row += '</td>';

                        new_row += '<td>';
                        new_row += '<div class="js-visible-view js-text-type">'+resp.type+'</div>';
                        new_row += '<div class="js-visible-edit" style="display:none">';
                        new_row += '<input type="text" class="form-control form-control-sm" name="type" value="'+resp.type+'" />';
                        new_row += '</div>';
                        new_row += '</td>';

                        new_row += '<td class="text-right">';
                        new_row += '<div class="js-visible-view">';
                        new_row += '<a href="#" class="text-info js-edit-item" title="Редактировать"><i class=" fas fa-edit"></i></a> ';
                        new_row += '<a href="#" class="text-danger js-delete-item" title="Удалить"><i class="far fa-trash-alt"></i></a>';
                        new_row += '</div>';
                        new_row += '<div class="js-visible-edit" style="display:none">';
                        new_row += '<a href="#" class="text-success js-confirm-edit-item" title="Сохранить"><i class="fas fa-check-circle"></i></a> ';
                        new_row += '<a href="#" class="text-danger js-cancel-edit-item" title="Отменить"><i class="fas fa-times-circle"></i></a>';
                        new_row += '</div>';
                        new_row += '</td>';
                        new_row += '</tr>';
                        
                        $('#table-body').append(new_row);
                                                        
                        $('#modal_add_item').modal('hide');
                        Swal.fire({
                            timer: 5000,
                            text: 'Шаблон сообщения "'+resp.name+'" добавлен!',
                            type: 'success',
                        });                                

                    }
                }
            })
        });
    };
    
    ;(function(){
        _init_events();
    })();
};
$(function(){
    new SmsTemplatesApp();
})
