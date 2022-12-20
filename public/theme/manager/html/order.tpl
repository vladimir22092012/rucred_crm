{$meta_title="Заявка №`$order->order_id`" scope=parent}

{capture name='page_scripts'}
    <script
            src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.js"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/offline_order.js?v=1.17"></script>
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/movements.app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/js/jquery.suggestions.min.js"></script>
    <script>
        $(function () {

            let token_dadata = "25c845f063f9f3161487619f630663b2d1e4dcd7";

            moment.locale('ru');

            $('.daterange').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD.MM.YYYY'
                },
            });

            $('input[name="snils"]').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999-999-999 99');

            $('input[name="inn"]').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999999999999');

            $('input[name="subdivision_code"]').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999-999');

            $('input[name="passport_serial"]').click(function () {
                $(this).setCursorPosition(0);
            }).mask('9999-999999');

            $('input[name="personal_number"]').click(function () {
                $(this).setCursorPosition(0);
            }).mask('999999');

            $('input[name="subdivision_code"]').suggestions({
                token: token_dadata,
                type: "fms_unit",
                minChars: 3,
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function (suggestion) {
                    $('textarea[name="passport_issued"]').empty();
                    $(this).empty();
                    $('textarea[name="passport_issued"]').val(suggestion.value);
                    $(this).trigger('input').val(suggestion.data.code);
                }
            });

            $('textarea[name="regaddress"]').suggestions({
                token: token_dadata,
                type: "ADDRESS",
                minChars: 3,
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function (suggestion) {
                    $(this).val(suggestion.value);
                    $('.Registration').val(JSON.stringify(suggestion));
                }
            });

            $('textarea[name="faktaddress"]').suggestions({
                token: token_dadata,
                type: "ADDRESS",
                minChars: 3,
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function (suggestion) {
                    $(this).val(suggestion.value);
                    $('.Faktadres').val(JSON.stringify(suggestion));
                }
            });

            $('.get-docs').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');
                let documents = {{json_encode($documents)}};

                if (Object.keys(documents).length > 0) {
                    Swal.fire({
                        title: 'Формирование документов приведет к удалению всех сканов',
                        showCancelButton: true,
                        confirmButtonText: 'Согласен',
                        cancelButtonText: 'Не согласен',
                    }).then((result) => {
                        if (result.value) {
                            get_docs(order_id);
                        }
                    });
                }
                else {
                    $.ajax({
                        method: 'post',
                        data: {
                            create_documents: true,
                            order_id: order_id
                        },
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });

            $('.new_scan').on('change', function (e) {
                let form_data = new FormData();

                form_data.append('file', e.target.files[0]);
                form_data.append('user_id', $(this).attr('data-user'));
                form_data.append('type', 'document');
                form_data.append('action', 'add');
                form_data.append('template', $(this).attr('id'));
                form_data.append('order_id', $(this).attr('data-order'));
                form_data.append('notreplace', '1');
                form_data.append('is_it_scans', 'yes');

                $.ajax({
                    url: '/upload_files',
                    data: form_data,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    success: function () {
                        window.location.reload();
                    }
                });
            });

            $('.inn-edit').on('click', function (e) {
                e.preventDefault();

                $('.inn-front').toggle();
                $('.inn-editor').toggle();

                $('.inn-edit-cancel').on('click', function () {
                    $('.inn-number-edit').val($('.inn-number').text());
                    $('.inn-editor').hide();
                    $('.inn-front').show();
                });

                $('.inn-edit-success').on('click', function () {
                    e.preventDefault();

                    let inn_number = $('.inn-number-edit').val();
                    let user_id = {{$order->user_id}};

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'inn_change',
                            inn_number: inn_number,
                            user_id: user_id
                        },
                        success: function (resp) {

                            $('.inn-editor').hide();
                            $('.inn-front').show();
                            $('.inn-number').text(inn_number);
                        }
                    });
                })
            });

            $('.edit_requisites').on('click', function (e) {
                e.preventDefault();

                let fio_hold_front = $('.fio-hold-front').text();
                let acc_num_front = $('.acc-num-front').text();
                let bank_name_front = $('.bank-name-front').text();
                let bik_front_name = $('.bik-front-name').text();
                let cor_account = $('.cor-account').text();

                $('#edit_requisites_modal').modal();


                $('.fio-hold-edit').val(fio_hold_front);
                $('.acc-num-edit').val(acc_num_front);
                $('.bank-name-edit').val(bank_name_front);
                $('.bik-edit').val(bik_front_name);
                $('.cor-acc').val(cor_account);

                $('.save_req').on('click', function () {
                    e.preventDefault();

                    var $form = $(this).closest('form');

                    $.ajax({
                        method: 'POST',
                        data: $form.serialize(),
                        success: function () {
                            location.reload();
                        }
                    });
                })
            });

            $('.snils-edit').on('click', function (e) {
                e.preventDefault();

                $('.snils-number-edit').mask('999-999-999 99');

                $('.snils-front').toggle();
                $('.snils-editor').toggle();

                $('.snils-edit-cancel').on('click', function () {
                    $('.snils-number-edit').val($('.snils-number').text());
                    $('.snils-editor').hide();
                    $('.snils-front').show();
                });

                $('.snils-edit-success').on('click', function () {
                    e.preventDefault();

                    let snils_number = $('.snils-number-edit').val();
                    let user_id = {{$order->user_id}};

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'snils_change',
                            snils_number: snils_number,
                            user_id: user_id
                        },
                        success: function (resp) {

                            $('.snils-editor').hide();
                            $('.snils-front').show();
                            $('.snils-number').text(snils_number);
                        }
                    });
                })
            });

            $('.edit_schedule').on('click', function (e) {
                e.preventDefault();

                $(this).hide();
                $('.restructuring, .restructuring').hide();
                $('.reset_shedule').show();
                $('.cancel').show();
                $('input[name="date[][date]"]').attr('readonly', false);

                $('.cancel').on('click', function () {
                    location.reload();
                })
            });

            $('.reform').on('click', function (e) {
                e.preventDefault();

                let restruct = 0;
                let schedule_id = $(this).attr('data-schedule');

                $(this).hide();
                $('.edit_schedule').hide();
                $('.reset_shedule').show();
                $('.cancel').show();
                $('.new_term_label').show();

                if ($(this).hasClass('reform')) {
                    $('input[name="date[][date]"]').attr('readonly', false);
                    $('input[name="loan_percents_pay[][loan_percents_pay]"]').attr('readonly', false);
                    $('input[name="loan_body_pay[][loan_body_pay]"]').attr('readonly', false);
                    $('input[name="comission_pay[][comission_pay]"]').attr('readonly', false);
                }

                $('.cancel').on('click', function () {
                    location.reload();
                });

                $('.reset_shedule').on('click', function (e) {
                    e.preventDefault();

                    reform_schedule(restruct, schedule_id);

                });
            });

            $('.reject_by_under').on('click', function (e) {
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'reject_by_under',
                        order_id: order_id,
                    },
                    success: function () {
                        location.reload();
                    }
                })
            });

            $('.send_money').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');

                Swal.fire({
                    title: 'Отправить деньги?',
                    showCancelButton: true,
                    confirmButtonText: 'Да',
                    cancelButtonText: 'Нет',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            method: 'POST',
                            dataType: 'JSON',
                            data: {
                                action: 'delivery_order',
                                order_id: order_id
                            },
                            success: function (resp) {
                                if (resp['success'] == 1) {
                                    Swal.fire({
                                        title: 'Деньги успешно отправлены'
                                    });
                                    setInterval(function () {
                                        location.reload();
                                    }, 5000);
                                } else {
                                    Swal.fire({
                                        title: 'Произошла ошибка',
                                        text: resp.error
                                    })
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.photo_status', function () {

                if ($(this).val() != 1)
                    $('div[class="order-image-actions"]').show();
                else
                    $('div[class="order-image-actions"]').hide();

                let status = $(this).val();
                let file_id = $(this).attr('data-file');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_photo_status',
                        status: status,
                        file_id: file_id
                    }
                });
            });

            $('.accept-order').on('click', function (e) {
                e.preventDefault();
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'accept_by_employer',
                        order_id: order_id
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('.question-order').on('click', function (e) {
                e.preventDefault();
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'question_by_employer',
                        order_id: order_id
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('.reject-order').on('click', function (e) {
                e.preventDefault();
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'reject_by_employer',
                        order_id: order_id
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('.ndfl').on('change', function (e) {

                let form_data = new FormData();

                form_data.append('file', e.target.files[0]);
                form_data.append('user_id', $(this).attr('data-user'));
                form_data.append('type', 'ndfl');
                form_data.append('action', 'add');
                form_data.append('template', $(this).attr('id'));
                form_data.append('order_id', $(this).attr('data-order'));
                form_data.append('notreplace', '1');
                form_data.append('ndfl', 'yes');
                form_data.append('name', e.target.files[0]['name']);

                $.ajax({
                    url: '/upload_files',
                    data: form_data,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        location.reload();
                    }
                });
            });

            $('.edit_personal_number').on('click', function (e) {
                e.preventDefault();


                $(this).hide();
                $('.show_personal_number').hide();
                $('.number_edit_form').show();

                $('.cancel_edit').on('click', function () {
                    $('.number_edit_form').hide();
                    $('.edit_personal_number').show();
                    $('.show_personal_number').show();
                });

                $('.accept_edit').on('click', function () {
                    e.preventDefault();

                    let user_id = $(this).attr('data-user');
                    let number = $('input[class="form-control number_edit_form number"]').val();
                    let order_id = {{$order->order_id}};

                    $.ajax({
                        method: 'POST',
                        data: {
                            action: 'edit_personal_number',
                            user_id: user_id,
                            number: number,
                            order_id: order_id
                        },
                        success: function (resp) {
                            if (resp == 'error') {
                                Swal.fire({
                                    title: 'Такой номер уже зарегистрирован',
                                    confirmButtonText: 'ОК'
                                });
                            }
                            else {
                                location.reload();
                            }
                        }
                    })

                });
            });

            let manager_role = {{json_encode($manager->role)}};

            if (manager_role == 'employer')
                $('.fa-edit').hide();


            $('.accept_changes').on('click', function (e) {

                let form = $('#loan_settings').serialize();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: form,
                    success: function (resp) {
                        if (resp['error']) {
                            let error = '<li>' + resp['error'] + '</li>';
                            $('.settings_error').show();
                            $('.settings_error_list').append(error);

                            setTimeout(function () {
                                $('.settings_error_list').empty();
                                $('.settings_error').fadeOut();
                            }, 3000);
                        }
                        if (resp['success']) {
                            location.reload();
                        }
                    }
                })
            });

            let error = 0;

            $('.restructure_od').on('change', function () {

                let loan_od = 0;
                let period = $('.rest_sum').length;

                $('.restructure_od').each(function () {
                    let val = $(this).val();
                    val = val.replace(' ', '');
                    val = val.replace(' ', '');
                    val = val.replace(',', '.');

                    console.log(val);

                    loan_od = loan_od + parseFloat(val);
                });

                let sum = loan_od.toFixed(2);

                let loan_amount = ({json_encode($order->amount)});

                let reason = loan_amount - sum;
                reason = reason.toFixed(2);

                if (reason == 0.00) {
                    $('.rest_sum').eq(period - 1).removeClass('warning_rest_sum');
                    $('input[name="result[all_loan_body_pay]"]').removeClass('warning_rest_sum');
                }
                else {
                    $('.rest_sum').eq(period - 1).addClass('warning_rest_sum');
                    $('input[name="result[all_loan_body_pay]"]').addClass('warning_rest_sum');
                    error = 1;
                }

                let last_od = loan_amount - reason;

                $('.rest_sum').eq(period - 1).val(reason);
                $('input[name="result[all_loan_body_pay]"]').val(new Intl.NumberFormat('ru-RU').format(last_od));


                let pay_od = $(this).val();
                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(',', '.');

                let percents_pay = $(this).closest('tr').find('.restructure_prc').val();

                percents_pay = percents_pay.replace(' ', '');
                percents_pay = percents_pay.replace(' ', '');
                percents_pay = percents_pay.replace(',', '.');

                let comission_pay = $(this).closest('tr').find('.restructure_cms').val();

                if (comission_pay) {
                    comission_pay = comission_pay.replace(' ', '');
                    comission_pay = comission_pay.replace(' ', '');
                    comission_pay = comission_pay.replace(',', '.');
                }

                let annouitet_sum = parseFloat(pay_od) + parseFloat(percents_pay) + parseFloat(comission_pay);

                $(this).closest('tr').find('.restructure_pay_sum').val(new Intl.NumberFormat('ru-RU').format(annouitet_sum));

                calculate_annouitet();

            });

            $('.restructure_prc').on('change', function () {

                let loan_prc = 0;

                $('.restructure_prc').each(function () {
                    let val = $(this).val();
                    val = val.replace(' ', '');
                    val = val.replace(' ', '');
                    val = val.replace(',', '.');

                    loan_prc = loan_prc + parseFloat(val);
                });

                let sum = loan_prc.toFixed(2);

                $('input[name="result[all_loan_percents_pay]"]').val(new Intl.NumberFormat('ru-RU').format(sum));


                let percents_pay = $(this).val();
                percents_pay = percents_pay.replace(' ', '');
                percents_pay = percents_pay.replace(' ', '');
                percents_pay = percents_pay.replace(',', '.');

                let pay_od = $(this).closest('tr').find('.restructure_od').val();

                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(',', '.');

                let comission_pay = $(this).closest('tr').find('.restructure_cms').val();
                comission_pay = comission_pay.replace(' ', '');
                comission_pay = comission_pay.replace(' ', '');
                comission_pay = comission_pay.replace(',', '.');

                let annouitet_sum = parseFloat(pay_od) + parseFloat(percents_pay) + parseFloat(comission_pay);

                $(this).closest('tr').find('.restructure_pay_sum').val(new Intl.NumberFormat('ru-RU').format(annouitet_sum));

                calculate_annouitet();

            });

            $('.restructure_cms').on('change', function () {

                let loan_cms = 0;

                $('.restructure_cms').each(function () {
                    let val = $(this).val();
                    val = val.replace(' ', '');
                    val = val.replace(' ', '');
                    val = val.replace(',', '.');

                    loan_cms = loan_cms + parseFloat(val);
                });

                let sum = loan_cms.toFixed(2);

                $('input[name="result[all_comission_pay]"]').val(new Intl.NumberFormat('ru-RU').format(sum));


                let comission_pay = $(this).val();
                comission_pay = comission_pay.replace(' ', '');
                comission_pay = comission_pay.replace(' ', '');
                comission_pay = comission_pay.replace(',', '.');

                let pay_od = $(this).closest('tr').find('.restructure_od').val();

                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(' ', '');
                pay_od = pay_od.replace(',', '.');

                let percents_pay = $(this).closest('tr').find('.restructure_prc').val();

                if (percents_pay) {
                    percents_pay = percents_pay.replace(' ', '');
                    percents_pay = percents_pay.replace(' ', '');
                    percents_pay = percents_pay.replace(',', '.');
                }

                let annouitet_sum = parseFloat(pay_od) + parseFloat(percents_pay) + parseFloat(comission_pay);

                $(this).closest('tr').find('.restructure_pay_sum').val(new Intl.NumberFormat('ru-RU').format(annouitet_sum));

                calculate_annouitet();

            });

            $('.delete_order').on('click', function (e) {
                e.preventDefault();
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'delete_order',
                        order_id: order_id
                    },
                    success: function () {
                        location.replace('/offline_orders');
                    }
                })
            });

            $('.fa-eraser').on('click', function (e) {
                e.preventDefault();

                $('.employer_show, #employer_edit').toggle();

                $('.cancel_employer').on('click', function () {
                    $('#employer_edit').hide();
                    $('.employer_show').show();
                });
            });

            $('.accept_employer').on('click', function (e) {
                e.preventDefault();

                let group_id = $('#group_select').val();
                let company_id = $('#company_select').val();
                let branch_id = $('#branch_select').val();
                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'change_employer_info',
                        order_id: order_id,
                        group: group_id,
                        company: company_id,
                        branch: branch_id
                    },
                    success: function () {
                        location.reload();
                    }
                })
            });

            $('.restructuring').on('click', function () {
                $('#modal_restruct').modal();
            });

            $('#new_term').on('change', function () {
                let order_id = $(this).attr('data-order');
                let pay_date = $('input[name="pay_date"]').val();
                let new_term = $('select[name="new_term"]').val();

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'restruct_term',
                        order_id: order_id,
                        pay_date: pay_date,
                        new_term: new_term
                    },
                    success: function (resp) {
                        $('#new_term_digit').show();
                        $('#new_term_digit').text(resp);
                    }
                })
            });

            $('.do_restruct, .accept_restruct').on('click', function (e) {
                e.preventDefault();

                let preview = 0;

                if ($(this).hasClass('do_restruct'))
                    preview = 1;

                do_restruct(preview);

                if ($(this).hasClass('accept_restruct'))
                    location.reload();
            });

            $('.cancel_restruct').on('click', function () {
                location.reload();
            });

            $('.send_asp_code').on('click', function (e) {
                e.preventDefault();

                $('#sms_confirm_modal').modal();

                let phone = $(this).attr('data-phone');
                let user = $(this).attr('data-user');
                let order = $(this).attr('data-order');

                $('.confirm_asp').fadeIn();
                $('.code_asp').fadeIn();


                send_asp(phone, user, order);
            });

            $(document).on('click', '.confirm_asp', function (e) {
                e.preventDefault();

                let phone = $(this).attr('data-phone');
                let user = $(this).attr('data-user');
                let order = $(this).attr('data-order');
                let code = $('input[class="form-control code_asp"]').val();
                let restruct = $(this).attr('data-restruct');

                confirm_asp(user, phone, code, order, restruct);

            });

            let order = {{json_encode($order)}};

            if ($.inArray(order['status'], ['4', '5', '6', '7', '8']) !== -1) {
                $('.fa-edit').remove();
            }

            $('.create_settlement').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'create_pay_rdr',
                        order_id: order_id
                    },
                    success: function () {

                    }
                })
            });

            $('.send_payment').on('click', function (e) {
                e.preventDefault();

                let form = $(this).closest('form').serialize();

                $.ajax({
                    method: 'POST',
                    data: form,
                    dataType: 'JSON',
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'Ок'
                            });
                        } else {
                            Swal.fire({
                                title: "Платежный документ успешно отправлен",
                                confirmButtonText: 'Ок'
                            });

                            $('#send_payment_form').fadeOut();
                            $('#rdr_payment_sent').fadeIn();
                        }
                    }
                })
            });


            $('.send_qr').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');
                let phone = $(this).attr('data-phone');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'send_qr',
                        order_id: order_id,
                        phone: phone
                    },
                    success: function (resp) {
                        if (resp['success']) {
                            Swal.fire({
                                title: 'Ссылка на оплату успешно отправлена',
                                confirmButtonText: 'Ок'
                            });
                        } else {
                            Swal.fire({
                                title: 'Произошла ошибка',
                                confirmButtonText: 'Ок'
                            });
                        }
                    }
                })
            });

            $('.approve_by_under').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');
                let manager_id = $(this).attr('data-manager');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'accept_approve_by_under',
                        order_id: order_id,
                        manager_id: manager_id
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('a[href^="#"]').click(function (e) {
                e.preventDefault();

                let anchor = $(this).attr('href');

                $('html, body').animate({
                    scrollTop: $(anchor).offset().top
                }, 600);
            });

            $('.phone_mobile_format').text(function (i, text) {
                return text.replace(/(\d)(\d\d\d)(\d\d\d)(\d\d)(\d\d)/, '+$1 ($2) $3-$4-$5');
            });

            $(document).on('click', '.accept_online_order', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');
                let manager_id = $(this).attr('data-manager');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'accept_online_order',
                        order_id: order_id,
                        manager_id: manager_id
                    },
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'Ок'
                            });
                        } else {
                            location.reload();
                        }
                    }
                });
            });

            $('.next_pay_date').on('change', function () {
                let date = $(this).val();
                let order = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'next_schedule_date',
                        date: date,
                        order: order

                    },
                    success: function (resp) {
                        $('input[name="pay_date"]').val(resp['date']);
                        $('.perspective_pay').show();
                        $('.perspective_pay').attr('style', 'display: flex; flex-direction:column');
                        $('#next_sum_pay').text(resp['payment']['pay_sum']);
                        $('#next_sum_od').text(resp['payment']['loan_body_pay']);
                        $('#next_sum_prc').text(resp['payment']['loan_percents_pay']);
                        $('#next_sum_com').text(resp['payment']['comission_pay']);
                    }
                })
            });

            $('.reject_by_middle').on('click', function (e) {
                e.preventDefault();

                let order_id = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'reject_by_middle',
                        order_id: order_id,
                    },
                    success: function () {
                        location.reload();
                    }
                })
            });

            $('.edit_fio').on('click', function (e) {
                e.preventDefault();

                $('#edit_fio_modal').modal();

                $('.save_fio').on('click', function () {

                    let form = $('#fio_form').serialize();

                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: form,
                        success: function (resp) {
                            if (resp['error']) {
                                Swal.fire({
                                    title: resp['error'],
                                    confirmButtonText: 'Ок'
                                })
                            } else if (resp['success'])
                                location.reload();
                        }
                    });
                });
            });

            $('.edit_settings').on('click', function () {
                $('#edit_settings_modal').modal();
            });

            $('#group_select').on('change', function () {

                let group_id = $(this).val();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'get_companies',
                        group_id: group_id
                    },
                    success: function (companies) {
                        if (companies['html'])
                            $('#company_select').html(companies['html']);
                    }
                });
            });

            $('#company_select').on('change', function () {

                let company_id = $(this).val();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'get_branches',
                        company_id: company_id
                    },
                    success: function (branches) {
                        if (branches['html'])
                            $('#branch_select').html(branches['html']);
                    }
                });
            });

            $(document).on('click', '.save_settings', function () {

                let form = $('#settings_form').serialize();
                let that = $(this);

                $.ajax({
                    dataType: 'JSON',
                    method: 'POST',
                    data: form,
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'ОК'
                            });
                        }

                        if (resp['success'] == 1) {
                            $('#edit_settings_modal').modal('hide');
                            $('#sms_confirm_modal').modal();
                            let order = that.attr('data-order');

                            send_sms(order);
                        }
                    }
                });
            });

            $(document).on('click', '.confirm_settings', function (e) {
                e.preventDefault();

                let order = $(this).attr('data-order');
                let code = $('.code_asp').val();

                confirm_sms(code, order);
            });

            $('.refreshConditions').on('click', function () {

                let orderId = $(this).attr('data-order');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'refreshConditions',
                        orderId: orderId
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('.editPdn').on('click', function () {
                $('#pdnModal').modal();
            });

            $('.add_to_credits_table').on('click', function (e) {
                e.preventDefault();

                let html = $(
                    '<tr>' +
                    '<td><input class="form-control bank_validate" name="credits_bank_name[][credits_bank_name]" type="text" value=""></td>' +
                    '<td><input class="form-control mask_number" name="credits_rest_sum[][credits_rest_sum]" type="text" value=""></td>' +
                    '<td><input class="form-control mask_number" name="credits_month_pay[][credits_month_pay]" type="text" value=""></td>' +
                    '<td><input class="form-control validity_period" name="credits_return_date[][credits_return_date]" type="text" value=""></td>' +
                    '<td><input class="form-control credit_procents" name="credits_percents[][credits_percents]" type="text" value=""></td>' +
                    '<td><select class="form-control" name="credits_delay[][credits_delay]"><option value="Да">Да</option>' +
                    '<option value="Нет" selected>Нет</option></select></td>' +
                    '<td><div class="btn btn-outline-danger delete_credit">-</div></td>' +
                    '</tr>'
                );

                $('#credits_table').append(html);

                $('.validity_period').click(function () {
                    $(this).setCursorPosition(0);
                }).mask('99/99');
            });

            $('.add_to_cards_table').on('click', function (e) {
                e.preventDefault();

                $('#cards_table').append(
                    '<tr>' +
                    '<td><input class="form-control bank_validate" name="cards_bank_name[][cards_bank_name]" type="text" value=""></td>' +
                    '<td><input class="form-control mask_number" name="cards_limit[][cards_limit]" type="text" value=""></td>' +
                    '<td><input class="form-control mask_number" name="cards_rest_sum[][cards_rest_sum]" type="text" value=""></td>' +
                    '<td><input class="form-control validity_period" name="cards_validity_period[][cards_validity_period]" type="text" value=""></td>' +
                    '<td><select class="form-control" name="cards_delay[][cards_delay]"><option value="Да">Да</option>' +
                    '<option value="Нет" selected>Нет</option></select></td>' +
                    '<td><div class="btn btn-outline-danger delete_card">-</div></td>' +
                    '</tr>');

                $('.validity_period').click(function () {
                    $(this).setCursorPosition(0);
                }).mask('99/99');
            });

            $(document).on('click', '.delete_credit, .delete_card', function () {
                $(this).closest('tr').remove();
            });

            $('.savePdn').on('click', function () {
                let form = $(this).closest('form').serialize();

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: form,
                    success: function (resp) {
                        if (resp['error']) {
                            Swal.fire({
                                title: resp['error'],
                                confirmButtonText: 'ОК'
                            });
                        }
                        if (resp['success']) {
                            Swal.fire({
                                title: 'Успешно!',
                                confirmButtonText: 'ОК'
                            });

                            location.reload();
                        }
                    }
                });
            });

            $(document).on('input', '.credit_procents, .daterange, .mask_number', function () {
                let value = $(this).val();
                value = value.replace(new RegExp(/[^. \d\s-]/, 'g'), '');
                $(this).val(value);
            });

            $(document).on('input', '.fioValidate', function () {
                let value = $(this).val();
                value = value.replace(new RegExp(/[^а-яёА-ЯЁ\s-]+$/, 'g'), '');
                $(this).val(value);
            });

            $('.acc-num-edit, .cor-acc').mask('99999999999999999999');
            $('.bik-edit').mask('999999999');

            $('#canSendOnec, #canSendYaDisk').on('click', function () {

                let value = 0;
                let orderId = $(this).attr('data-order');
                let action = 'sendOnecTrigger';

                if($(this).attr('id') == 'canSendYaDisk')
                    action = 'sendYaDiskTrigger';

                if($(this).is(':checked'))
                    value = 1;

                $.ajax({
                    method: 'POST',
                    data:{
                        action: action,
                        orderId: orderId,
                        value: value
                    }
                });
            });
        });
    </script>
    <script>
        function get_docs(order_id) {
            $.ajax({
                method: 'post',
                data: {
                    create_documents: true,
                    order_id: order_id
                },
                success: function () {
                    location.reload();
                }
            });
        }

        function send_asp(phone, user, order) {

            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'send_asp_code',
                    phone: phone,
                    user: user,
                    order: order
                },
                success: function (resp) {
                    if (resp['error']) {
                        Swal.fire({
                            title: resp['error'],
                            confirmButtonText: 'Да'
                        }).then((result) => {
                            if (result.value) {
                                get_docs(order);
                            }
                        });
                    }
                }
            });
        }

        function confirm_asp(user, phone, code, order, restruct) {

            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'confirm_asp',
                    user: user,
                    phone: phone,
                    code: code,
                    order: order,
                    restruct: restruct
                },
                success: function (response) {
                    if (response['error'] == 1) {
                        Swal.fire({
                            title: 'Неверный код',
                            confirmButtonText: 'ОК'
                        });
                    } else {
                        location.reload();
                    }
                }
            });
        }

        function send_sms(order) {

            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'send_sms',
                    order: order
                },
                success: function (resp) {
                    if (resp['error']) {
                        Swal.fire({
                            title: resp['error'],
                            confirmButtonText: 'Ок'
                        });
                    }
                    if (resp['code']) {
                        $('.phone_send_code').show();
                        $('.phone_send_code').text(resp['code']);
                        $('button[class="btn btn-info confirm_asp"]').removeClass('confirm_asp');
                        $('button[class="btn btn-info"]').addClass('confirm_settings');
                    }
                }
            });
        }

        function confirm_sms(code, order) {

            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'confirm_sms',
                    code: code,
                    order: order,
                },
                success: function (response) {
                    if (response['error'] == 1) {
                        Swal.fire({
                            title: 'Неверный код',
                            confirmButtonText: 'ОК'
                        });
                    }
                    if (response['success'] == 1) {
                        location.reload();
                    }
                }
            });
        }

        function do_restruct(preview) {

            let form = $('#restruct_form').serialize();

            if (preview == 1)
                form += '&preview=1';

            $.ajax({
                method: 'POST',
                dataType: 'JSON',
                data: form,
                success: function (html) {
                    if (preview == 1) {
                        $('.restructuring').hide();
                        $('.accept_restruct').show();
                        $('.cancel_restruct').show();
                        $('tbody').html(html['schedule']);
                        $('#psk').html(html['psk']);
                        $('#modal_restruct').modal('hide');
                    }
                    else {
                        location.reload()
                    }
                }
            });
        }

        function calculate_annouitet() {

            let loan_pay_sum = 0;

            $('.restructure_pay_sum').each(function () {
                let val = $(this).val();
                val = val.replace(' ', '');
                val = val.replace(' ', '');
                val = val.replace(',', '.');

                loan_pay_sum = loan_pay_sum + parseFloat(val);
            });

            let sum = loan_pay_sum.toFixed(2);

            $('input[name="result[all_sum_pay]"]').val(new Intl.NumberFormat('ru-RU').format(sum));
        }

        function reform_schedule(restruct, schedule_id = false) {

            let form = $('#payment_schedule').serialize() + '&restruct=' + restruct + '&schedule_id=' + schedule_id;

            if (restruct == 1) {
                Swal.fire({
                    title: 'Пожалуйста выберите вариант реструктуризации',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'С доп платежом',
                    denyButtonText: 'Без доп платежа',
                }).then((result) => {
                    if (result.value) {
                        form += '&add_period=1';
                    } else if (result.dismiss) {
                        form += '&add_period=0';
                    }
                    $.ajax({
                        method: 'POST',
                        data: form,
                        success: function () {
                            location.reload();
                        }
                    });
                });
            } else {
                $.ajax({
                    method: 'POST',
                    data: form,
                    success: function () {
                        location.reload();
                    }
                });
            }
        }
    </script>
{/capture}

{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link href="theme/{$settings->theme|escape}/assets/plugins/fancybox3/dist/jquery.fancybox.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.12.0/dist/css/suggestions.min.css" rel="stylesheet"/>
    <style>
        label, p, .btn {
            font-size: 13px !important;
        }

        .warning_rest_sum {
            border: 2px solid #a90009;
        }

        .warning_asp {
            border-bottom: 1px dotted #0077AA;
            cursor: help;
        }

        .warning_asp::after {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 8px 8px 8px 0px;
            box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
            color: #FFF;
            content: attr(data-tooltip); /* Главная часть кода, определяющая содержимое всплывающей подсказки */
            margin-top: -24px;
            opacity: 0; /* Наш элемент прозрачен... */
            padding: 3px 7px;
            position: absolute;
            visibility: hidden; /* ...и скрыт. */

            transition: all 0.4s ease-in-out; /* Добавить плавности по вкусу */
        }

        .warning_asp:hover::after {
            opacity: 1; /* Показываем его */
            visibility: visible;
        }

        .accordion__item {
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgb(0 0 0 / 15%);
        }

        .accordion__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #fff;
            font-weight: 500;
            background-color: #0d6efd;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.2s ease-out;
        }

        .accordion__header::after {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            margin-left: auto;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-size: 1.25rem;
            content: "";
        }

        .accordion__item_show .accordion__header::after {
            transform: rotate(-180deg);
        }

        .accordion__header:hover {
            background-color: #0b5ed7;
        }

        .accordion__item_hidden .accordion__header {
            border-bottom-right-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .accordion__body {
            padding: 0.75rem 1rem;
            overflow: hidden;
            background: #fff;
            border-bottom-right-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .accordion__item:not(.accordion__item_show) .accordion__body {
            display: none;
        }

        .modal-lg, .modal-xl {
            max-width: 1200px;
        }

    </style>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
{/capture}

{function name='penalty_button'}

    {if in_array('add_penalty', $manager->permissions)}
        {if !$penalties[$penalty_block]}
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-danger waves-effect js-add-penalty "
                    data-block="{$penalty_block}">
                <i class="fas fa-ban"></i>
                <span>Штраф</span>
            </button>
        {elseif $penalties[$penalty_block] && in_array($penalties[$penalty_block]->status, [1,2])}
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-primary waves-effect js-reject-penalty "
                    data-penalty="{$penalties[$penalty_block]->id}">
                <i class="fas fa-ban"></i>
                <span>Отменить</span>
            </button>
            <button type="button" class="pb-0 pt-0 mr-2 btn btn-sm btn-warning waves-effect js-strike-penalty "
                    data-penalty="{$penalties[$penalty_block]->id}">
                <i class="fas fa-ban"></i>
                <span>Страйк</span>
            </button>
        {/if}
        {if in_array($penalties[$penalty_block]->status, [4])}
            <span class="label label-warning">Страйк ({$penalties[$penalty_block]->cost} руб)</span>
        {/if}
    {elseif $penalties[$penalty_block]->manager_id == $manager->id}
        {if in_array($penalties[$penalty_block]->status, [1])}
            <button class="pb-0 pt-0 mr-2 btn btn-sm btn-primary js-correct-penalty"
                    data-penalty="{$penalties[$penalty_block]->id}" type="button">Исправить
            </button>
        {/if}
        {if in_array($penalties[$penalty_block]->status, [4])}
            <span class="label label-warning">Страйк ({$penalties[$penalty_block]->cost} руб)</span>
        {/if}
    {/if}

{/function}

<div class="page-wrapper" data-event="1" data-manager="{$manager->id}" data-order="{$order->order_id}"
     data-user="{$order->user_id}">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h4 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Заявка
                    № {if !empty($contract->number)}{$contract->number}{else}{$order->group_number} {$order->company_number} {$order->personal_number}{/if}
                    ({$order->order_id})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item">
                        {if isset($from_registr)}
                            <a href="registr">Реестр сделок</a>
                        {else}
                            <a href="orders">Заявки</a>
                        {/if}</li>
                    <li class="breadcrumb-item active">Заявка
                        № {if !empty($contract->number)}{$contract->number}{else}{$order->group_number} {$order->company_number} {$order->personal_number}{/if}
                        ({$order->order_id})
                    </li>
                </ol>
            </div>
        </div>
        {if in_array($order->status, [0,1,2,4,9,10,14,13,15])}
            <div style="display: flex; margin-left: 5px">
                <small class="badge badge-{if in_array($order->status, [0,1])}success{else}secondary{/if}">Заведение
                    заявки и подготовка документов (подписание + фото паспортов)
                </small>
                <small class="badge badge-{if in_array($order->status, [2])}success{else}secondary{/if}"
                       style="margin-left: 5px">Одобрение заявки андеррайтером и принятие в работу
                </small>
                <small class="badge badge-{if in_array($order->status, [4])}success{else}secondary{/if}"
                       style="margin-left: 5px">Одобрение заёмщика работодателем
                </small>
            </div>
            <div style="display: flex; margin-top: 10px">
                <small class="badge badge-{if in_array($order->status, [13,14])}success{else}secondary{/if}"
                       style="margin-left: 5px">Проверка заявки андеррайтером после работодателя
                </small>
                <small class="badge badge-{if in_array($order->status, [10])}success{else}secondary{/if}"
                       style="margin-left: 5px">Одобрение сделки миддлом и перечисление средств
                </small>
            </div>
            <br>
        {/if}
        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">

                    <div class="card-body">

                        <div class="form-body">
                            <div class="row">
                                <div class="col-4 col-md-3 col-lg-3" style="display: flex;">
                                    <small>
                                        {if $client_status == 'ПК'}
                                            <span class="label label-success">ПК</span>
                                        {elseif $client_status == 'Повтор'}
                                            <span class="label label-warning">Повтор</span>
                                        {elseif $client_status == 'Новая'}
                                            <span class="label label-info">Новый</span>
                                        {/if}
                                    </small>
                                    <small style="margin-left: 25px">
                                        {if $order->order_source_id == 1}
                                            <span class="label label-info">Клиентский сайт</span>
                                        {elseif $order->order_source_id == 2}
                                            <span class="label label-primary">Мобильное приложение</span>
                                        {elseif $order->offline == 1}
                                            <span class="label label-success">Црм</span>
                                        {/if}
                                    </small>
                                </div>
                                <div class="col-8 col-md-3 col-lg-3">
                                    <h5 class="form-control-static float-left">
                                        Дата заявки: {$order->date|date} {$order->date|time}
                                    </h5>
                                    {if $order->penalty_date}
                                        <h6 class="form-control-static float-left">
                                            дата решения: {$order->penalty_date|date} {$order->penalty_date|time}
                                        </h6>
                                    {/if}
                                </div>
                                <div class="col-12 col-md-3 col-lg-3">
                                    <h5 class="form-control-static">Номер
                                        клиента: <span class="show_personal_number">{$client->personal_number}</span>
                                        {*<a href="" data-user="{$client->id}"
                                                                                class="text-info edit_personal_number">
                                                <i class="fas fa-edit"></i></a>*}
                                    </h5>
                                    <input type="text" class="form-control number_edit_form number"
                                           style="width: 80px; display: none"
                                           value="{$client->personal_number}">
                                    <input type="button" style="display: none"
                                           data-user="{$client->id}"
                                           class="btn btn-success number_edit_form accept_edit"
                                           value="Сохранить">
                                    <input type="button" style="display: none"
                                           class="btn btn-danger number_edit_form cancel_edit"
                                           value="Отмена">
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 ">
                                    <h6 class="js-order-manager text-right">
                                        {if in_array($manager->role, ['developer', 'admin', 'big_user'])}
                                            <select class="js-order-manager form-control"
                                                    data-order="{$order->order_id}" name="manager_id">
                                                <option value="0" {if !$order->manager_id}selected="selected"{/if}>Не
                                                    принята
                                                </option>
                                                {foreach $managers as $m}
                                                    <option value="{$m->id}"
                                                            {if $m->id == $order->manager_id}selected="selected"{/if}>{$m->name|escape}</option>
                                                {/foreach}
                                            </select>
                                        {else}
                                            {if $order->manager_id}
                                                Ответственный: {$managers[$order->manager_id]->name|escape}
                                            {/if}
                                        {/if}
                                    </h6>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-12 col-md-4 col-lg-3">
                                    <div class="border p-2 view-block">
                                        <div style="display: flex; justify-content: space-between">
                                            <h6>
                                                <a href="client/{$order->user_id}" title="Перейти в карточку клиента">
                                                    {$order->lastname|escape}
                                                    {$order->firstname|escape}
                                                    {$order->patronymic|escape}
                                                </a>
                                            </h6>
                                        </div>
                                        <h4>
                                            <span class="phone_mobile_format">{$order->phone_mobile}</span>
                                        </h4>
                                    </div>
                                    <br>
                                    <div>
                                        ID клиента: {$order->user_id}
                                        {if !empty($order->contract_id)}
                                            <br>
                                            ID сделки: {$order->contract_id}{/if}
                                        {if in_array($manager->role, ['admin', 'developer'])}
                                        <br><br><div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                   data-order="{$order->order_id}" id="canSendOnec"
                                                   {if $order->canSendOnec}checked{/if}>
                                            <label class="custom-control-label" for="canSendOnec"><strong class="text-danger">Отравлять в 1с</strong></label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                   data-order="{$order->order_id}" id="canSendYaDisk"
                                                   {if $order->canSendYaDisk}checked{/if}>
                                            <label class="custom-control-label" for="canSendYaDisk"><strong class="text-danger">Отравлять в Я.Диск</strong></label>
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                                <div class="col-12 col-md-8 col-lg-6">
                                    <form class="mb-3 p-2 border" id="loan_settings">
                                        <input type="hidden" name="action" value="change_loan_settings"/>
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="user_id" value="{$order->user_id}"/>
                                        <div class="text-danger pt-3 settings_error" style="display: none">
                                            <ul class="settings_error_list" style="list-style-type: none">
                                            </ul>
                                        </div>
                                        <div class="row view-block ">
                                            <div class="col-4 text-center">
                                                <h6>Сумма</h6>
                                                <h4 class="text-primary loan_amount">{$order->amount|number_format:0:',':' '}
                                                    руб</h4>
                                            </div>
                                            <div class="col-4 text-center">
                                                <h6>Тарифный план</h6>
                                                <h4 class="text-primary loantype_name">{$loantype->name}</h4>
                                            </div>
                                            <div class="col-4 text-center">
                                                <h6>Дата выдачи</h6>
                                                <h4 class="text-primary probably_start_date">{$order->probably_start_date|date}</h4>
                                            </div>
                                            {*
                                                <a href="javascript:void(0);"
                                                   class="text-info js-edit-form edit-amount js-event-add-click"
                                                   data-event="31" data-manager="{$manager->id}"
                                                   data-order="{$order->order_id}" data-user="{$order->user_id}"><i
                                                            class=" fas fa-edit"></i></a>
                                                </h4>
                                            *}
                                        </div>

                                        <div class="row edit-block hide">
                                            <div class="col-2">
                                                <h6>Сумма</h6>
                                                <input type="text" class="form-control" name="amount"
                                                       value="{$order->amount}"/>
                                            </div>
                                            <div class="col-3">
                                                <h6>Тарифный план</h6>
                                                <select class="form-control" name="loan_tarif">
                                                    {foreach $loantypes as $loantype_select}
                                                        <option value="{$loantype_select['id']}"
                                                                {if $loantype_select['id'] == $loantype->id}selected{/if}>{$loantype_select['name']}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <h6>Дата выдачи</h6>
                                                <input type="text" style="margin-left: 10px; width: 130px"
                                                       id="probably_start_date"
                                                       name="probably_start_date"
                                                       class="form-control daterange"
                                                       value="{$order->probably_start_date|date}">
                                            </div>
                                            <div class="col-4">
                                                <div class="btn btn-success js-cancel-edit accept_changes"
                                                     data-manager="{$manager->id}"
                                                     data-order="{$order->order_id}"
                                                     data-user="{$order->user_id}">
                                                    Сохранить
                                                </div>
                                                <button type="button" class="btn btn-inverse js-cancel-edit">
                                                    Отмена
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <br>
                                    {if $order->status == 0}
                                        <div class="btn btn-outline-primary edit_fio">
                                            Редактирование основных данных
                                        </div>
                                        <div class="btn btn-outline-info edit_requisites">
                                            Редактирование платёжных реквизитов
                                        </div>
                                        <div class="btn btn-outline-success edit_settings">
                                            Редактирование условий займа
                                        </div>
                                    {/if}
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="js-order-status">
                                        {if $order->status == 2}
                                            <div class="card card-success mb-1">
                                                <div class="box text-center">
                                                    <h3 class="text-white mb-0">А.Подготовлена</h3>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 0}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Новая</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 1}
                                            <div class="card card-success">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Принята</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 10}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">{if $manager->role == 'middle'}Готово к выдаче{else}У миддла{/if}</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 13}
                                            <div class="card card-warning">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Р.Нецелесообразно</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 11}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">М.Отказ</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 20}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">А.Отказ</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if in_array($order->status, [4])}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Подписан со стороны клиента</h4>
                                                    <h6>Договор {$contract->number}</h6>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 7}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Погашен</h4>
                                                    <h6>Договор #{$contract->number}</h6>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 8}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Отказ клиента</h4>
                                                    <small title="Причина отказа">
                                                        <i>{$reject_reasons[$order->reason_id]->admin_name}</i></small>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 14}
                                            <div class="card card-success">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Р.Подтверждена</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 15}
                                            <div class="card card-danger">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Р.Отклонена</h4>
                                                </div>
                                            </div>
                                        {/if}
                                        {if $order->status == 0 && $order->probably_start_date|date < $date|date}
                                            <div class="card card-primary">
                                                <div data-order="{$order->order_id}"
                                                     class="btn btn-info btn-block refreshConditions">
                                                    Обновить условия
                                                </div>
                                            </div>
                                        {/if}
                                        {if in_array($order->status, [13,14,15]) && $manager->role != 'employer'}
                                            <div>
                                                <button class="btn btn-success btn-block approve_by_under"
                                                        data-order="{$order->order_id}"
                                                        data-manager="{$manager->id}">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Одобрить заявку</span>
                                                </button>
                                                <button class="btn btn-danger btn-block reject_by_under"
                                                        data-user="{$order->user_id}"
                                                        data-order="{$order->order_id}"
                                                        data-manager="{$manager->id}">
                                                    <i class="fas fa-times-circle"></i>
                                                    <span>Отклонить заявку</span>
                                                </button>
                                            </div>
                                        {/if}
                                        {if $order->status == 10}
                                            {if $order->settlement_id == 2 && in_array($manager->role, ['middle', 'admin', 'developer'])}
                                                <form class=" pt-1 js-confirm-contract">
                                                    <div class="pt-1 pb-2">
                                                        <button class="btn btn-info btn-lg btn-block send_money"
                                                                data-order="{$order->order_id}">
                                                            <i class="fas fa-hospital-symbol"></i>
                                                            <span>Одобрить заявку и выплатить средства</span>
                                                        </button>
                                                        <button class="btn btn-danger btn-lg btn-block reject_by_middle"
                                                                data-order="{$order->order_id}">
                                                            <i class="fas fa-hospital-symbol"></i>
                                                            <span>Отказать в предоставлении займа</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            {/if}
                                            {if $order->settlement_id == 3 && in_array($manager->role, ['middle', 'admin', 'developer'])}
                                                {if empty($issuance_transaction)}
                                                    <form id="send_payment_form">
                                                        <input type="hidden" name="action" value="create_pay_rdr">
                                                        <input type="hidden" name="order_id" value="{$order->order_id}">
                                                        <div class="pt-1 pb-2">
                                                            <div class="btn btn-info btn-lg btn-block send_payment">
                                                                Одобрить заявку и выплатить средства
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-danger btn-lg btn-block reject_by_middle"
                                                                data-order="{$order->order_id}">
                                                            <span>Отказать в предоставлении займа</span>
                                                        </button>
                                                    </form>
                                                {/if}
                                                <form id="rdr_payment_sent"
                                                      {if empty($issuance_transaction)}style="display: none;" {/if}>
                                                    <div class="pt-1 pb-2">
                                                        <div class="card card-warning">
                                                            <div class="box text-center">
                                                                <h4 class="text-white">Платежный документ
                                                                    отправлен</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            {/if}
                                        {/if}
                                        {if $order->status == 5}
                                            {if $contract->status == 4}
                                                <div class="card card-danger mb-1">
                                                    <div class="box text-center">
                                                        <h4 class="text-white">Просрочен</h4>
                                                        <h6>Договор {$contract->number}</h6>
                                                        <h6 class="text-center text-white">
                                                            Погашение: {$contract->loan_body_summ+$contract->loan_percents_summ+$contract->loan_charge_summ+$contract->loan_peni_summ}
                                                            руб
                                                        </h6>
                                                        <h6 class="text-center text-white">
                                                            Продление:
                                                            {if $contract->prolongation > 0 && !$contract->sold}
                                                                {$settings->prolongation_amount+$contract->loan_percents_summ+$contract->loan_charge_summ} руб
                                                            {else}
                                                                {$contract->loan_percents_summ+$contract->loan_charge_summ} руб
                                                            {/if}
                                                        </h6>
                                                    </div>
                                                </div>
                                            {else}
                                                <div class="card card-primary mb-1">
                                                    <div class="box text-center">
                                                        <h4 class="text-white">Выдан</h4>
                                                        <h6 class="text-white">Договор {$contract->number}</h6>
                                                        <h6 class="text-center text-white">
                                                            След.
                                                            платеж: {$next_payment|floatval|number_format:2:',':' '}
                                                            руб
                                                        </h6>
                                                        <h6 class="text-center text-white">
                                                            Баланс: {if empty($contract->overpay)}00.00{else}$contract->overpay{/if}
                                                            руб
                                                        </h6>
                                                        <h6 class="text-center text-white">
                                                            Сумма
                                                            ДП: {$contract->loan_body_summ + $contract->loan_percents_summ}
                                                            руб
                                                        </h6>
                                                        {*
                                                        <h6 class="text-center text-white">
                                                            Продление:
                                                            {if $contract->prolongation > 0}
                                                                {$settings->prolongation_amount+$contract->loan_percents_summ} руб
                                                            {else}
                                                                {$contract->loan_percents_summ} руб
                                                            {/if}
                                                        </h6> *}
                                                    </div>
                                                </div>
                                                <div class="pt-1 pb-2">
                                                    <div data-order="{$order->order_id}"
                                                         data-phone="{$order->phone_mobile}"
                                                         class="btn btn-info btn-lg btn-block send_qr">
                                                        Отправить ссылку на оплату
                                                    </div>
                                                </div>
                                            {/if}
                                            {if in_array('close_contract', $manager->permissions)}
                                                <button
                                                        class="btn btn-danger btn-block js-open-close-form js-event-add-click"
                                                        data-event="15" data-user="{$order->user_id}"
                                                        data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                    Закрыть договор
                                                </button>
                                            {/if}
                                        {/if}
                                        {if $order->status == 6}
                                            <div class="card card-danger mb-1">
                                                <div class="box text-center">
                                                    <h4 class="text-white">Не удалось выдать</h4>
                                                    <h6>Договор {$contract->number}</h6>
                                                    {if $p2p->response_xml}
                                                        <i>
                                                            <small>B2P: {$p2p->response_xml->message}</small>
                                                        </i>
                                                    {else}
                                                        <i>
                                                            <small>Нет ответа от B2P. <br/>Если повторить выдачу, это
                                                                может привести к двойной выдаче!
                                                            </small>
                                                        </i>
                                                    {/if}
                                                </div>
                                            </div>
                                            {if $have_newest_order}
                                                <div class="text-center">
                                                    <a href="order/{$have_newest_order}"><strong
                                                                class="text-danger text-center">У клиента есть новая
                                                            заявка</strong></a>
                                                </div>
                                            {else}
                                                {if in_array('repay_button', $manager->permissions)}
                                                    <button type="button"
                                                            class="btn btn-primary btn-block js-repay-contract js-event-add-click"
                                                            data-event="16" data-user="{$order->user_id}"
                                                            data-order="{$order->order_id}"
                                                            data-manager="{$manager->id}"
                                                            data-contract="{$contract->id}">Повторить выдачу
                                                    </button>
                                                {/if}
                                            {/if}
                                        {/if}
                                        {if $contract->accept_code}
                                            <h4 class="text-danger mb-0">АСП: {$contract->accept_code}</h4>
                                        {/if}
                                    </div>
                                    {if $order->status == 4}
                                        {if in_array($manager->role, ['developer', 'admin', 'employer'])}
                                            <div>
                                                <button class="btn btn-success btn-block accept-order warning_asp"
                                                        data-tooltip="Подтвердите нахождение сотрудника в данной организации"
                                                        data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Подтверждаю сотрудника,<br>заём может быть выдан</span>
                                                </button>
                                                <button class="btn btn-warning btn-block question-order warning_asp"
                                                        data-user="{$order->user_id}"
                                                        data-tooltip="Подтвердите нахождение сотрудника в данной организации"
                                                        data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <span>Подтверждаю сотрудника, выдача займа нецелесообразна</span>
                                                </button>
                                                <button class="btn btn-danger btn-block reject-order warning_asp"
                                                        data-user="{$order->user_id}"
                                                        data-tooltip="Подтвердите нахождение сотрудника в данной организации"
                                                        data-order="{$order->order_id}" data-manager="{$manager->id}">
                                                    <i class="fas fa-times-circle"></i>
                                                    <span>Нет такого сотрудника</span>
                                                </button>
                                            </div>
                                        {else}
                                            <div class="card card-primary">
                                                <div class="box text-center">
                                                    <h4 class="text-white">У работодателя</h4>
                                                    <h6>Договор {$contract->number}</h6>
                                                </div>
                                            </div>
                                        {/if}
                                    {/if}
                                    {if $order->status == 0 && in_array($manager->role, ['developer', 'admin', 'underwriter'])}
                                        <button class="btn btn-success btn-block accept_online_order"
                                                data-event="12" data-user="{$order->user_id}"
                                                data-order="{$order->order_id}" data-manager="{$manager->id}">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Принять в работу</span>
                                        </button>
                                        <button class="btn btn-danger btn-block js-reject-order js-event-add-click"
                                                data-event="13" data-user="{$order->user_id}"
                                                data-order="{$order->order_id}"
                                                data-manager="{$manager->id}">
                                            <span>Отказать без рассмотрения</span>
                                        </button>
                                    {/if}
                                    {if $order->status == 2 && in_array($manager->role, ['developer', 'admin', 'underwriter'])}
                                        <div class="col-12">
                                            <button
                                                    class="btn btn-success btn-block js-approve-order js-event-add-click"
                                                    data-event="12" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}"
                                                    data-manager="{$manager->id}">
                                                <span>Принять в работу и передать Работодателю</span>
                                            </button>
                                            <button class="btn btn-danger btn-block js-reject-order js-event-add-click"
                                                    data-event="13" data-user="{$order->user_id}"
                                                    data-order="{$order->order_id}"
                                                    data-manager="{$manager->id}">
                                                <span>Отказать без передачи Работодателю</span>
                                            </button>
                                        </div>
                                    {/if}
                                    {if $need_confirm_restruct == 1 && in_array($manager->role, ['admin', 'middle', 'developer'])}
                                        <div data-order="{$order->order_id}"
                                             style="margin-left: 15px;"
                                             class="btn btn-success confirm_restruct">
                                            Подтвердить реструктуризацию
                                        </div>
                                        <div data-order="{$order->order_id}"
                                             style="margin-left: 15px;"
                                             class="btn btn-danger cancell_restruct">
                                            Отменить реструктуризацию
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>


                        <ul class="mt-2 nav nav-tabs" role="tablist" id="order_tabs">
                            <li class="nav-item">
                                <a class="nav-link active js-event-add-click" data-toggle="tab" href="#info" role="tab"
                                   aria-selected="false" data-event="20" data-user="{$order->user_id}"
                                   data-order="{$order->order_id}" data-manager="{$manager->id}">
                                    <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                    <span class="hidden-xs-down">Информация по заявке</span>
                                </a>
                            </li>
                            {if $manager->role != 'employer'}
                                <li class="nav-item">
                                    <a class="nav-link js-event-add-click" data-toggle="tab" href="#comments" role="tab"
                                       aria-selected="false" data-event="21" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                        <span class="hidden-xs-down">
                                            Комментарии {if $comments|count > 0}<span
                                                    class="label label-rounded label-primary">{$comments|count}</span>{/if}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-event-add-click" data-toggle="tab" href="#logs" role="tab"
                                       aria-selected="true" data-event="23" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-server"></i></span>
                                        <span class="hidden-xs-down">Логирование</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-event-add-click" data-toggle="tab" href="#operations"
                                       role="tab"
                                       aria-selected="true" data-event="24" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-list-ol"></i></span>
                                        <span class="hidden-xs-down">Операции</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-event-add-click" data-toggle="tab" href="#history" role="tab"
                                       aria-selected="true" data-event="25" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-save-alt"></i></span>
                                        <span class="hidden-xs-down">Кредитная история</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link js-event-add-click" data-toggle="tab" href="#schedule"
                                       role="tab"
                                       aria-selected="true" data-event="25" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-save-alt"></i></span>
                                        <span class="hidden-xs-down">Графики платежей</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#uploads"
                                       role="tab"
                                       aria-selected="true" data-event="25" data-user="{$order->user_id}"
                                       data-order="{$order->order_id}" data-manager="{$manager->id}">
                                        <span class="hidden-sm-up"><i class="ti-save-alt"></i></span>
                                        <span class="hidden-xs-down">Выгрузки</span>
                                    </a>
                                </li>
                            {/if}
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content tabcontent-border" id="order_tabs_content">
                            <div class="tab-pane active" id="info" role="tabpanel">
                                <div class="form-body p-2 pt-3">

                                    <div class="row">
                                        <div class="col-md-8 ">

                                            <!-- Контакты -->
                                            <form action="{url}"
                                                  class="mb-3 border js-order-item-form {if $penalties['personal'] && $penalties['personal']->status!=3}card-outline-danger{/if}"
                                                  id="personal_data_form">

                                                <input type="hidden" name="action" value="contactdata"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header card-success"
                                                    style="display: flex; justify-content: space-between">
                                                    <span class="text-white ">Общая информация</span>
                                                </h6>

                                                <div class="row pt-2 view-block {if $contactdata_error}hide{/if}">

                                                    {if $penalties['personal'] && (in_array($manager->permissions, ['add_penalty']) || $penalties['personal']->manager_id==$manager->id)}
                                                        <div class="col-md-12">
                                                            <div class="alert alert-danger m-2">
                                                                <h6 class="text-danger mb-1">{$penalty_types[$penalties['personal']->id]->name}</h6>
                                                                <small>{$penalties['personal']->comment}</small>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">ФИО:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->lastname} {$order->firstname} {$order->patronymic}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Телефон:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->phone_mobile}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Email:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->email|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Дата рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->birth|date}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Место
                                                                рождения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->birth_place|escape}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Паспорт:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->passport_serial}
                                                                    от {$order->passport_date|date}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Код
                                                                подразделения:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->subdivision_code}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Кем выдан:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->passport_issued}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $order->viber_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label class="control-label col-md 4">Viber:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->viber_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if $order->telegram_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label
                                                                        class="control-label col-md 4">Telegram:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->telegram_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if $order->whatsapp_num}
                                                        <div class="col-md-12">
                                                            <div class="form-group row m-0">
                                                                <label
                                                                        class="control-label col-md 4">WhatsApp:</label><br>
                                                                <div class="col-md-8">
                                                                    <p class="form-control-static">{$order->whatsapp_num}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>


                                                <div class="row p-2 edit-block {if !$contactdata_error}hide{/if}">
                                                    {if $contactdata_error}
                                                        <div class="col-md-12">
                                                            <ul class="alert alert-danger">
                                                                {if in_array('empty_email', (array)$contactdata_error)}
                                                                    <li>Укажите Email!</li>
                                                                {/if}
                                                                {if in_array('empty_birth', (array)$contactdata_error)}
                                                                    <li>Укажите Дату рождения!</li>
                                                                {/if}
                                                                {if in_array('empty_passport_serial', (array)$contactdata_error)}
                                                                    <li>Укажите серию и номер паспорта!</li>
                                                                {/if}
                                                                {if in_array('empty_passport_date', (array)$contactdata_error)}
                                                                    <li>Укажите дату выдачи паспорта!</li>
                                                                {/if}
                                                                {if in_array('empty_subdivision_code', (array)$contactdata_error)}
                                                                    <li>Укажите код подразделения выдавшего паспорт!
                                                                    </li>
                                                                {/if}
                                                                {if in_array('empty_passport_issued', (array)$contactdata_error)}
                                                                    <li>Укажите кем выдан паспорт!</li>
                                                                {/if}
                                                            </ul>
                                                        </div>
                                                    {/if}

                                                    <div class="col-md-6">
                                                        <div
                                                                class="form-group mb-1 {if in_array('empty_email', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Email</label>
                                                            <input type="text" name="email" value="{$order->email}"
                                                                   class="form-control" placeholder=""/>
                                                            {if in_array('empty_email', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите Email!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div
                                                                class="form-group mb-1 {if in_array('empty_birth', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Дата рождения</label>
                                                            <input type="text" name="birth" value="{$order->birth}"
                                                                   class="form-control" placeholder="" required="true"/>
                                                            {if in_array('empty_birth', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите дату
                                                                    рождения!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-2">
                                                            <label class="control-label">Соцсети</label>
                                                            <input type="text" class="form-control" name="social"
                                                                   value="{$order->social}" placeholder=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div
                                                                class="form-group mb-2 {if in_array('empty_birth_place', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Место рождения</label>
                                                            <input type="text" name="birth_place"
                                                                   value="{$order->birth_place|escape}"
                                                                   class="form-control" placeholder="" required="true"/>
                                                            {if in_array('empty_birth_place', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите место
                                                                    рождения!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4">
                                                        <div
                                                                class="form-group mb-1 {if in_array('empty_passport_serial', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Серия и номер паспорта</label>
                                                            <input type="text" class="form-control"
                                                                   name="passport_serial"
                                                                   value="{$order->passport_serial}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_passport_serial', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите серию и
                                                                    номер паспорта!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div
                                                                class="form-group mb-1 {if in_array('empty_passport_date', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Дата выдачи</label>
                                                            <input type="text" class="form-control" name="passport_date"
                                                                   value="{$order->passport_date|date}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_passport_date', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите дату выдачи
                                                                    паспорта!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div
                                                                class="form-group mb-1 {if in_array('empty_subdivision_code', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Код подразделения</label>
                                                            <input type="text" class="form-control"
                                                                   name="subdivision_code"
                                                                   value="{$order->subdivision_code}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_subdivision_code', (array)$contactdata_error)}
                                                                <small class="form-control-feedback">Укажите код
                                                                    подразделения выдавшего паспорт!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div
                                                                class="form-group {if in_array('empty_passport_issued', (array)$contactdata_error)}has-danger{/if}">
                                                            <label class="control-label">Кем выдан</label>
                                                            <input type="text" class="form-control"
                                                                   name="passport_issued"
                                                                   value="{$order->passport_issued}" placeholder=""
                                                                   required="true"/>
                                                            {if in_array('empty_passport_issued', (array)$contactdata_errors)}
                                                                <small class="form-control-feedback">Укажите кем выдан
                                                                    паспорт!
                                                                </small>
                                                            {/if}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit"
                                                                    class="btn btn-success js-event-add-click"
                                                                    data-event="42" data-manager="{$manager->id}"
                                                                    data-order="{$order->order_id}"
                                                                    data-user="{$order->user_id}"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <form action="{url}" class="js-order-item-form mb-3 border"
                                                  id="address_form">

                                                <input type="hidden" name="action" value="addresses"/>
                                                <input type="hidden" name="user_id" value="{$client->id}"/>

                                                <h6 class="card-header card-success">
                                                    <span class="text-white">Адрес</span>
                                                </h6>
                                                <br>
                                                <div class="row view-block {if $addresses_error}hide{/if}">
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Адрес
                                                                прописки:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->regaddress->adressfull}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        <div class="form-group row m-0">
                                                            <label class="control-label col-md-4">Адрес
                                                                проживания:</label>
                                                            <div class="col-md-8">
                                                                <p class="form-control-static">{$order->faktaddress->adressfull}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- / Контакты-->
                                            <form action="{url}"
                                                  class="border js-order-item-form mb-3 {if $penalties['images'] && $penalties['images']->status!=3}card-outline-danger{/if}"
                                                  id="images_form">

                                                <input type="hidden" name="action" value="images"/>
                                                <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                                <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                                <h6 class="card-header">
                                                    <span class="text-white">Фото</span>
                                                    <span class="float-right">
                                                    {penalty_button penalty_block='images'}
                                                </span>
                                                </h6>

                                                <div class="row p-2 view-block {if $socials_error}hide{/if}">
                                                    <ul class="col-md-12 list-inline"
                                                        style="display: flex; justify-content: left">
                                                        {foreach $files as $file}
                                                            {if $file->status == 0}
                                                                {$item_class="border-warning"}
                                                                {$ribbon_class="ribbon-warning"}
                                                                {$ribbon_icon="fas fa-question"}
                                                            {elseif $file->status == 1}
                                                                {$item_class="border-primary"}
                                                                {$ribbon_class="ribbon-primary"}
                                                                {$ribbon_icon="fas fa-clock"}
                                                            {elseif $file->status == 2}
                                                                {$item_class="border-success border border-bg"}
                                                                {$ribbon_class="ribbon-success"}
                                                                {$ribbon_icon="fa fa-check-circle"}
                                                            {elseif $file->status == 3}
                                                                {$item_class="border-danger border"}
                                                                {$ribbon_class="ribbon-danger"}
                                                                {$ribbon_icon="fas fa-times-circle"}
                                                            {elseif $file->status == 4}
                                                                {$item_class="border-info border"}
                                                                {$ribbon_class="ribbon-info"}
                                                                {$ribbon_icon="fab fa-cloudversify"}
                                                            {/if}
                                                            <div style="display: flex; flex-direction: column; margin-left: 15px">
                                                                {if isset($file->format)}
                                                                    <label class="badge badge-danger">Это PDF</label>
                                                                {else}
                                                                    <label class="badge badge-primary">Это фото</label>
                                                                {/if}
                                                                <li class="order-image-item ribbon-wrapper rounded-sm border {$item_class}">
                                                                    <a class="image-popup-fit-width js-event-add-click"
                                                                       href="javascript:void(0);"
                                                                       onclick="window.open('{$config->back_url}/files/users/{$order->user_id}/{$file->name}');"
                                                                       data-event="50" data-manager="{$manager->id}"
                                                                       data-order="{$order->order_id}"
                                                                       data-user="{$order->user_id}">
                                                                        <div class="ribbon ribbon-corner {$ribbon_class}">
                                                                            <i
                                                                                    class="{$ribbon_icon}"></i></div>
                                                                        <img src="{$config->back_url}/files/users/{$order->user_id}/{$file->name}"
                                                                             alt="" class="img-responsive" style=""/>
                                                                    </a>
                                                                    <div class="order-image-actions"
                                                                         {if !in_array($order->status, [0]) || $file->type == 'document'}style="display: none"{/if}>
                                                                        {if $manager->role != 'employer'}
                                                                            <div class="dropdown mr-1 show ">
                                                                                <button type="button"
                                                                                        class="btn {if $file->status==2}btn-success{elseif $file->status==3}btn-danger{else}btn-secondary{/if} dropdown-toggle"
                                                                                        id="dropdownMenuOffset"
                                                                                        data-toggle="dropdown"
                                                                                        aria-haspopup="true"
                                                                                        aria-expanded="true">
                                                                                    {if $file->status == 2}Принят
                                                                                    {elseif $file->status == 3}Отклонен
                                                                                    {else}Статус
                                                                                    {/if}
                                                                                </button>
                                                                                <div class="dropdown-menu"
                                                                                     aria-labelledby="dropdownMenuOffset"
                                                                                     x-placement="bottom-start">
                                                                                    <div class="p-1 dropdown-item">
                                                                                        <button
                                                                                                class="btn btn-sm btn-block btn-outline-success js-image-accept js-event-add-click"
                                                                                                data-event="51"
                                                                                                data-manager="{$manager->id}"
                                                                                                data-order="{$order->order_id}"
                                                                                                data-user="{$order->user_id}"
                                                                                                data-id="{$file->id}"
                                                                                                type="button">
                                                                                            <i class="fas fa-check-circle"></i>
                                                                                            <span>Принять</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="p-1 dropdown-item">
                                                                                        <button
                                                                                                class="btn btn-sm btn-block btn-outline-danger js-image-reject js-event-add-click"
                                                                                                data-event="52"
                                                                                                data-manager="{$manager->id}"
                                                                                                data-order="{$order->order_id}"
                                                                                                data-user="{$order->user_id}"
                                                                                                data-id="{$file->id}"
                                                                                                type="button">
                                                                                            <i class="fas fa-times-circle"></i>
                                                                                            <span>Отклонить</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="p-1 pt-3 dropdown-item">
                                                                                        <button
                                                                                                class="btn btn-sm btn-block btn-danger js-image-remove js-event-add-click"
                                                                                                data-event="53"
                                                                                                data-manager="{$manager->id}"
                                                                                                data-order="{$order->order_id}"
                                                                                                data-user="{$order->user_id}"
                                                                                                data-id="{$file->id}"
                                                                                                type="button">
                                                                                            <i class="fas fa-trash"></i>
                                                                                            <span>Удалить</span>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        {/if}
                                                                    </div>
                                                                </li>
                                                                {if $manager->role != 'employer' && in_array($order->status, [0])}
                                                                    <select class="form-control photo_status"
                                                                            data-file="{$file->id}"
                                                                            name="photo_status">
                                                                        <option value="1"
                                                                                {if $file->type == 'document'}selected{/if}>
                                                                            Выберите тип документа
                                                                        </option>
                                                                        <option value="2"
                                                                                {if $file->type == 'Паспорт: разворот'}selected{/if}>
                                                                            Паспорт: разворот
                                                                        </option>
                                                                        <option value="3"
                                                                                {if $file->type == 'Паспорт: регистрация'}selected{/if}>
                                                                            Паспорт: регистрация
                                                                        </option>
                                                                        <option value="4"
                                                                                {if $file->type == 'Селфи с паспортом'}selected{/if}>
                                                                            Селфи с паспортом
                                                                        </option>
                                                                    </select>
                                                                {/if}
                                                            </div>
                                                        {/foreach}
                                                    </ul>
                                                </div>

                                                <div class="row edit-block {if !$images_error}hide{/if}">
                                                    {foreach $files as $file}
                                                        <div class="col-md-4 col-lg-3 col-xlg-3">
                                                            <div class="card card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 col-lg-8">
                                                                        <div class="form-group">
                                                                            <label class="control-label">Статус</label>
                                                                            <input type="text" class="js-file-status"
                                                                                   id="status_{$file->id}"
                                                                                   name="status[{$file->id}]"
                                                                                   value="{$file->status}"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/foreach}
                                                    <div class="col-md-12">
                                                        <div class="form-actions">
                                                            <button type="submit" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> Сохранить
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-inverse js-cancel-edit">
                                                                Отмена
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div style="display: flex; width: 100%; justify-content: space-between">
                                                {if in_array($order->status, [0])}
                                                    <form method="POST" style="width: 50%; margin-left: 15px"
                                                          enctype="multipart/form-data">
                                                        {if $manager->role != 'employer'}
                                                            <div class="form_file_item">
                                                                <input type="file" name="new_file" class="new_file"
                                                                       id="new_file"
                                                                       data-user="{$order->user_id}" value=""
                                                                       style="display:none"/>
                                                                <label for="new_file" class="btn btn-large btn-primary">
                                                                    <i class="fa fa-plus-circle"></i>
                                                                    <span>Добавить фото</span>
                                                                </label>
                                                            </div>
                                                        {/if}
                                                    </form>
                                                {/if}
                                            </div>
                                            <br>


                                            <!-- Данные о работе -->
                                            <h6 class="card-header">
                                                <span class="text-white">График платежей</span>
                                                {if $manager->role != 'employer'}
                                                    <input style="margin-left: 30px; display: none" type="button"
                                                           class="btn btn-success reset_shedule"
                                                           value="Сохранить график">
                                                    <input style="margin-left: 30px; display: none" type="button"
                                                           class="btn btn-danger cancel"
                                                           value="Отменить">
                                                    {if $order->status == 5}
                                                        <input style="margin-left: 30px" type="button"
                                                               class="btn btn-danger restructuring"
                                                               value="Реструктуризация">
                                                        <input style="margin-left: 30px; display: none" type="button"
                                                               class="btn btn-success accept_restruct"
                                                               value="Сохранить">
                                                        <input style="margin-left: 30px; display: none" type="button"
                                                               class="btn btn-danger cancel_restruct"
                                                               value="Отменить">
                                                        {if $need_form_restruct_docs == 1}
                                                            <div style="margin-left: 30px; {if $asp_restruct == 1}display: none{/if}"
                                                                 data-order="{$order->order_id}"
                                                                 class="btn btn-primary form_restruct_docs">Закрепить
                                                                график
                                                                и сформировать документы для реструктуризации
                                                            </div>
                                                        {/if}
                                                    {/if}
                                                    {if $asp_restruct == 10}
                                                        <div class="btn btn-primary"
                                                             style="margin-left: 15px">
                                                            <a href="#send_asp"
                                                               style="text-decoration: none!important; color: #ffffff!important;">
                                                                Подписать документы о реструктуризации</a>
                                                        </div>
                                                    {/if}
                                                    {*
                                                        <input style="margin-left: 30px" type="button"
                                                               data-schedule="{$payment_schedule->id}"
                                                               class="btn btn-warning reform"
                                                               value="Редактировать">
                                                    *}
                                                {/if}
                                            </h6>

                                            <form id="payment_schedule">
                                                <div class="row m-0 pt-2 view-block {if $work_error}hide{/if}">
                                                    <table border="2" style="font-size: 15px">
                                                        <thead align="center">
                                                        <tr style="width: 100%;">
                                                            <th rowspan="3">Дата</th>
                                                            <th rowspan="3">Сумма</th>
                                                            <th colspan="3">Структура платежа</th>
                                                            <th rowspan="3">Остаток долга, руб.
                                                            </th>
                                                        </tr>
                                                        <tr style="width: 100%;">
                                                            <th>Осн. долг</th>
                                                            <th>Проценты</th>
                                                            <th>Др. платежи</th>
                                                        </tr>
                                                        </thead>
                                                        <input type="hidden" name="action" value="edit_schedule">
                                                        <input type="hidden" name="order_id" value="{$order->order_id}">
                                                        <tbody>
                                                        {if !empty($payment_schedule)}
                                                            {foreach $payment_schedule->schedule as $date => $payment}
                                                                {if $date != 'result'}
                                                                    <tr>
                                                                        <td><input type="text"
                                                                                   class="form-control daterange"
                                                                                   name="date[][date]"
                                                                                   value="{$date}" readonly>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                   class="form-control restructure_pay_sum"
                                                                                   name="pay_sum[][pay_sum]"
                                                                                   value="{$payment['pay_sum']|floatval|number_format:2:',':' '}"
                                                                                   readonly>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                   class="form-control restructure_od"
                                                                                   name="loan_body_pay[][loan_body_pay]"
                                                                                   value="{$payment['loan_body_pay']|floatval|number_format:2:',':' '}"
                                                                                   readonly>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                   class="form-control restructure_prc"
                                                                                   name="loan_percents_pay[][loan_percents_pay]"
                                                                                   value="{$payment['loan_percents_pay']|floatval|number_format:2:',':' '}"
                                                                                   readonly>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                   class="form-control restructure_cms"
                                                                                   name="comission_pay[][comission_pay]"
                                                                                   value="{$payment['comission_pay']|floatval|number_format:2:',':' '}"
                                                                                   readonly>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                   class="form-control rest_sum"
                                                                                   name="rest_pay[][rest_pay]"
                                                                                   value="{$payment['rest_pay']|floatval|number_format:2:',':' '}"
                                                                                   readonly>
                                                                        </td>
                                                                    </tr>
                                                                {/if}
                                                            {/foreach}
                                                            <tr>
                                                                <td><input type="text" class="form-control"
                                                                           value="ИТОГО:" disabled></td>
                                                                <td><input type="text" class="form-control"
                                                                           name="result[all_sum_pay]"
                                                                           value="{$payment_schedule->schedule['result']['all_sum_pay']|floatval|number_format:2:',':' '}"
                                                                           readonly></td>
                                                                <td><input type="text" class="form-control"
                                                                           name="result[all_loan_body_pay]"
                                                                           value="{$payment_schedule->schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}"
                                                                           readonly></td>
                                                                <td><input type="text" class="form-control"
                                                                           name="result[all_loan_percents_pay]"
                                                                           value="{$payment_schedule->schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}"
                                                                           readonly></td>
                                                                <td><input type="text" class="form-control"
                                                                           name="result[all_comission_pay]"
                                                                           value="{$payment_schedule->schedule['result']['all_comission_pay']|floatval|number_format:2:',':' '}"
                                                                           readonly></td>
                                                                <td><input type="text" class="form-control"
                                                                           name="result[all_rest_pay_sum]"
                                                                           value="{$payment_schedule->schedule['result']['all_rest_pay_sum']|floatval|number_format:2:',':' '}"
                                                                           readonly></td>
                                                            </tr>
                                                        {/if}
                                                        </tbody>
                                                    </table>
                                                    <div style="display: flex; flex-direction: column">
                                                        <br>
                                                        <div class="form-group">
                                                            <label>Полная стоимость микрозайма, %
                                                                годовых:</label>
                                                            <span id="psk">{$payment_schedule->psk}%</span>
                                                        </div>
                                                        {if $payment_schedule->type == 'restruct'}
                                                            <div class="form-group">
                                                                <label>Причина:</label>
                                                                <span>{$payment_schedule->comment}</span>
                                                            </div>
                                                        {/if}
                                                    </div>
                                            </form>
                                        </div>
                                        <!-- /Данные о работе -->

                                        <!-- Дополнительная информация -->
                                        <form action="{url}"
                                              class="border js-order-item-form mb-3 {if $penalties['work'] && $penalties['work']->status!=3}card-outline-danger{/if}"
                                              id="work_data_form">

                                            <input type="hidden" name="action" value="work"/>
                                            <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                            <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                            <h6 class="card-header">
                                                <span class="text-white">Дополнительная информация</span>
                                                <span class="float-right">
                                                            {penalty_button penalty_block='work'}
                                                    {*
                                                        <a href="javascript:void(0);"
                                                           class="text-white float-right js-edit-form js-event-add-click"
                                                           data-event="35" data-manager="{$manager->id}"
                                                           data-order="{$order->order_id}"
                                                           data-user="{$order->user_id}"><i
                                                                    class=" fas fa-edit"></i></a>
                                                    *}
                                                </span>
                                            </h6>

                                            <div class="row m-0 pt-2 view-block {if $work_error}hide{/if}">
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-0 row">
                                                        <label class="control-label col-md-3">Состоит ли в
                                                            браке:</label>
                                                        <div class="col-md-6">
                                                            <p class="form-control-static">
                                                                {if $order->sex == 0}
                                                                    Не состоит
                                                                {elseif $order->sex == 1}
                                                                    Состоит
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $order->sex == 1}
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-6">ФИО
                                                                супруга(-и):</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$order->fio_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Телефон
                                                                супруга(-и):</label>
                                                            <div class="col-md-5">
                                                                <p class="form-control-static">
                                                                    {$order->phone_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                {if $order->prev_fio != null || $order->prev_fio}
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-6">Предыдущие
                                                                ФИО:</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$order->prev_fio}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group  mb-0 row">
                                                            <label class="control-label col-md-4">Дата смены
                                                                ФИО:</label>
                                                            <div class="col-md-5">
                                                                <p class="form-control-static">
                                                                    {$order->fio_change_date|date}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли
                                                            иностранным публичным должностным лицом:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if $order->foreign_flag == 1}
                                                                    Не является
                                                                {elseif $order->foreign_flag == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли
                                                            супругом(-ой) иностранного публичного должностного
                                                            лица:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if $order->foreign_husb_wife == 1}
                                                                    Не является
                                                                {elseif $order->foreign_husb_wife == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $order->foreign_husb_wife == 2}
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-2 row">
                                                            <label class="control-label col-md-8">ФИО
                                                                супруга(-и):</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$order->fio_public_spouse}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-2 row">
                                                        <label class="control-label col-md-10">Является ли близким
                                                            родственником иностранного публичного должностного
                                                            лица:</label>
                                                        <div class="col-md-2">
                                                            <p class="form-control-static">
                                                                {if $order->foreign_relative == 1}
                                                                    Не является
                                                                {elseif $order->foreign_relative == 2}
                                                                    Является
                                                                {/if}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {if $order->foreign_relative == 1}
                                                    <div class="col-md-12">
                                                        <div class="form-group  mb-2 row">
                                                            <label class="control-label col-md-8">ФИО
                                                                родственника:</label>
                                                            <div class="col-md-4">
                                                                <p class="form-control-static">
                                                                    {$order->fio_relative}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            </div>
                                        </form>

                                        <!-- Данные о работодателе -->
                                        <form action="{url}"
                                              class="border js-order-item-form mb-3">

                                            <input type="hidden" name="action" value="work"/>
                                            <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                            <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                            <h6 class="card-header">
                                                <span class="text-white">Информация о работодателе</span>
                                            </h6>
                                            <div class="row m-0 pt-2 view-block">
                                                <div class="col-md-12">
                                                    <div class="form-group  mb-0 row employer_show">
                                                        <label class="control-label col-md-3">Компания:</label>
                                                        <div class="col-md-6">
                                                            <p>{$company_name}</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group  mb-0 row employer_show">
                                                        <label class="control-label col-md-3">Филиал:</label>
                                                        <div class="col-md-6">
                                                            <p>{$branch_name}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Документы -->
                                        <form action="{url}"
                                              class="border"
                                              id="documents_form">

                                            <input type="hidden" name="action" value="work"/>
                                            <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                            <input type="hidden" name="user_id" value="{$order->user_id}"/>

                                            <h6 class="card-header">
                                                <span class="text-white">Документы</span>
                                                {*{if $manager->role != 'employer' && in_array($order->status, [0, 1, 2])}
                                                    <input style="margin-left: 30px" type="button"
                                                           class="btn btn-primary get-docs"
                                                           data-order="{$order->order_id}"
                                                           value="Сформировать документы">
                                                {/if}*}
                                            </h6>
                                            <br>
                                            {if !empty($sort_docs)}
                                                {foreach $sort_docs as $date => $documents}
                                                    <div style="width: 100%!important; margin-left: 15px; display: flex; vertical-align: middle;">
                                                        <strong>{$date|date}</strong>
                                                    </div>
                                                    <hr style="width: 100%; size: 2px">
                                                    {foreach $documents as $document}
                                                        <div style="width: 100%!important; height: 50px; margin-left: 5px; display: flex; vertical-align: middle;"
                                                             id="{$document->id}">
                                                            <div class="form-group"
                                                                 style="width: 10px!important; margin-left: 5px">
                                                                <label class="control-label">{$document->numeration}</label>
                                                            </div>
                                                            <div class="form-group"
                                                                 style="width: 40%!important; margin-left: 50px">
                                                                <label class="control-label">{$document->name}</label>
                                                            </div>
                                                            <div style="margin-left: 10px">
                                                                <a target="_blank"
                                                                   href="{$config->back_url}/document?id={$document->id}&action=download_file"><input
                                                                            type="button"
                                                                            class="btn btn-outline-success download_doc"
                                                                            value="Сохранить"></a>
                                                            </div>
                                                            <div style="margin-left: 10px">
                                                                <a target="_blank"
                                                                   href="{$config->back_url}/document/{$document->id}"><input
                                                                            type="button"
                                                                            class="btn btn-outline-warning print_doc"
                                                                            value="Распечатать"></a>
                                                            </div>
                                                            {*
                                                            <div class="btn-group"
                                                                 style="margin-left: 10px; height: 35px">
                                                                {if $document->scan}
                                                                    <a target="_blank"
                                                                       style="text-decoration: none!important;"
                                                                       href="javascript:void(0);"
                                                                       onclick="window.open('{$config->back_url}/files/users/{$order->user_id}/{$document->scan->name}');">
                                                                        <input type="button"
                                                                               class="btn btn-outline-info {$scan->type}"
                                                                               value="Скан">
                                                                    </a>
                                                                {/if}
                                                                {if $manager->role != 'employer' && in_array($order->status, [0, 1, 2])}
                                                                    {if $manager->role != 'employer'}
                                                                        <button type="button"
                                                                                class="btn btn-outline-info dropdown-toggle dropdown-toggle-split"
                                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                                aria-expanded="false">
                                                                            <span class="sr-only">Toggle Dropdown</span>
                                                                        </button>
                                                                        <div class="dropdown-menu">
                                                                            <input type="file" name="new_scan"
                                                                                   id="{$document->template}"
                                                                                   class="new_scan"
                                                                                   data-user="{$order->user_id}"
                                                                                   data-order="{$order->order_id}"
                                                                                   value="" style="display:none" multiple/>
                                                                            <label for="{$document->template}"
                                                                                   class="dropdown-item">Приложить
                                                                                скан</label>
                                                                        </div>
                                                                    {/if}
                                                                {/if}
                                                            </div>
                                                            *}
                                                        </div>
                                                        <hr style="width: 100%; size: 2px">
                                                    {/foreach}
                                                {/foreach}
                                                {*
                                                <div
                                                        style="width: 100%!important; height: 30px; margin-left: 15px; display: flex; vertical-align: middle">
                                                    <div class="form-group" style="width: 55%!important">
                                                        <label class="control-label">Справка 2-НФДЛ</label>
                                                    </div>
                                                    <div>
                                                        {if !empty($ndfl)}
                                                            <a download target="_blank"
                                                               href="{$config->back_url}/files/users/{$ndfl->name}"><input
                                                                        type="button"
                                                                        class="btn btn-outline-success"
                                                                        value="Сохранить"></a>
                                                        {/if}
                                                    </div>
                                                    {if empty($ndfl)}
                                                        <div class="btn-group"
                                                             style="margin-left: 210px; height: 35px">
                                                            <button type="button"
                                                                    class="btn btn-outline-info dropdown-toggle dropdown-toggle-split"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <input type="file" name="ndfl"
                                                                       class="ndfl"
                                                                       id="ndfl"
                                                                       data-user="{$order->user_id}"
                                                                       data-order="{$order->order_id}"
                                                                       value="" style="display:none" multiple/>
                                                                <label for="ndfl"
                                                                       class="dropdown-item">Приложить
                                                                    справку</label>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>
                                                <hr style="width: 100%; size: 2px">
                                                *}
                                            {/if}
                                        </form>
                                    </div>
                                    <div class="col-md-4 ">

                                        {if $order->autoretry_result}
                                            <div
                                                    class="card mb-1 {if $order->autoretry_summ}card-success{else}card-danger{/if}">
                                                <div class="box ">
                                                    <h6 class="card-title mb-0 text-white text-center">
                                                        Автоповтор</h6>
                                                    <div class="text-white text-center">
                                                        <small class="text-white">
                                                            {$order->autoretry_result}
                                                        </small>
                                                    </div>
                                                    {if $order->autoretry_summ && $order->status == 1}
                                                        <button data-order="{$order->order_id}"
                                                                class="mt-2 btn btn-block btn-info btn-sm js-autoretry-accept js-event-add-click"
                                                                data-event="17" data-manager="{$manager->id}"
                                                                data-order="{$order->order_id}"
                                                                data-user="{$order->user_id}">
                                                            Выдать {$order->autoretry_summ} руб
                                                        </button>
                                                    {/if}
                                                </div>
                                            </div>
                                        {/if}
                                        {if $manager->role != 'employer'}
                                            <div
                                                    class="mb-3 border  {if $penalties['scorings'] && $penalties['scorings']->status!=3}card-outline-danger{/if}">
                                                <h6 class=" card-header">
                                                    <span class="text-white ">Скоринги</span>
                                                    <span class="float-right">
                                                            {penalty_button penalty_block='scorings'}
                                                        {if ($order->status == 1 && ($manager->id == $order->manager_id)) || $is_developer}
                                                            <a class="text-white js-run-scorings" data-type="all"
                                                               data-order="{$order->order_id}"
                                                               href="javascript:void(0);">
                                                                <i class="far fa-play-circle"></i>
                                                            </a>
                                                        {/if}
                                                        </span>
                                                </h6>
                                                <div
                                                        class="message-box js-scorings-block {if $need_update_scorings}js-need-update{/if}"
                                                        data-order="{$order->order_id}">

                                                    {foreach $scoring_types as $scoring_type}
                                                        <div
                                                                class="pl-2 pr-2 {if $scorings[$scoring_type->name]->status == 'new'}bg-light-warning{elseif $scorings[$scoring_type->name]->success}bg-light-success{else}bg-light-danger{/if}">
                                                            <div class="row {if !$scoring_type@last}border-bottom{/if}">
                                                                <div class="col-12 col-sm-12 pt-2">
                                                                    <h6 class="float-left">
                                                                        {$scoring_type->title}
                                                                        {if $scoring_type->name == 'fssp'}
                                                                            {if $scorings[$scoring_type->name]->found_46}
                                                                                <span
                                                                                        class="label label-danger">46</span>
                                                                            {/if}
                                                                            {if $scorings[$scoring_type->name]->found_47}
                                                                                <span
                                                                                        class="label label-danger">47</span>
                                                                            {/if}
                                                                        {/if}
                                                                    </h6>

                                                                    {if $scorings[$scoring_type->name]->status == 'new'}
                                                                        <span class="label label-warning float-right">Ожидание</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'process'}
                                                                        <span
                                                                                class="label label-info label-sm float-right">Выполняется</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'error' || $scorings[$scoring_type->name]->status == 'stopped'}
                                                                        <span
                                                                                class="label label-danger label-sm float-right">Ошибка</span>
                                                                    {elseif $scorings[$scoring_type->name]->status == 'completed'}
                                                                        {if $scorings[$scoring_type->name]->success}
                                                                            <span
                                                                                    class="label label-success label-sm float-right">Пройден</span>
                                                                        {else}
                                                                            <span
                                                                                    class="label label-danger float-right">Не пройден</span>
                                                                        {/if}
                                                                    {/if}
                                                                </div>
                                                                <div class="col-8 col-sm-8 pb-2">
                                                                        <span class="mail-desc"
                                                                              title="{$scorings[$scoring_type->name]->string_result}">
                                                                            {$scorings[$scoring_type->name]->string_result}
                                                                        </span>
                                                                    <span class="time">
                                                                            {if $scorings[$scoring_type->name]->created}
                                                                                Дата скоринга: {$scorings[$scoring_type->name]->created|date} {$scorings[$scoring_type->name]->created|time}
                                                                            {/if}
                                                                        {if $scoring_type->name == 'fssp'}
                                                                            <a href="/ajax/show_fssp.php?id={$scorings[$scoring_type->name]->id}&password=Hjkdf8d"
                                                                               target="_blank">Подробнее</a>
                                                                        {/if}
                                                                        {if $scoring_type->name == 'okb' && $scorings[$scoring_type->name]->success}
                                                                            <a href="javascript:void(0);"
                                                                               class="js-get-okb-info float-right"
                                                                               data-scoring="{$scorings[$scoring_type->name]->id}">Подробнее</a>
                                                                        {/if}
                                                                        {if $scoring_type->name == 'efrsb' && $scorings[$scoring_type->name]->body}
                                                                            {foreach $scorings[$scoring_type->name]->body as $efrsb_link}
                                                                                <a href="{$efrsb_link}"
                                                                                   target="_blank"
                                                                                   class="float-right">Подробнее</a>
                                                                            {/foreach}
                                                                        {/if}
                                                                        {if $scoring_type->name == 'nbki'}
                                                                            <a href="http://45.147.176.183/nal-plus-nbki/{$scorings[$scoring_type->name]->id}?api=F1h1Hdf9g_h"
                                                                               target="_blank">Подробнее</a>
                                                                        {/if}
                                                                        </span>
                                                                </div>
                                                                <div class="col-4 col-sm-4 pb-2">
                                                                    {if $order->status < 2 || $is_developer}
                                                                        {if $scorings[$scoring_type->name]->status == 'new' || $scorings[$scoring_type->name]->status == 'process' }
                                                                            <a class="btn-load text-info run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <div class="spinner-border text-info"
                                                                                     role="status"></div>
                                                                            </a>
                                                                            {*
                                                                        {elseif $scorings[$scoring_type->name]}
                                                                            <a class="btn-load text-info js-run-scorings run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <i class="fas fa-undo"></i>
                                                                            </a>
                                                                        {else}
                                                                            <a class="btn-load text-info js-run-scorings run-scoring-btn float-right"
                                                                               data-type="{$scoring_type->name}"
                                                                               data-order="{$order->order_id}"
                                                                               href="javascript:void(0);">
                                                                                <i class="far fa-play-circle"></i>
                                                                            </a>
                                                                            *}
                                                                        {/if}
                                                                    {/if}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        {/if}
                                        <form class="mb-4 border">
                                            <h6 class="card-header text-white">
                                                <span>ИНН</span>
                                                {*
                                                    <span class="float-right">
                                                                <a href="" class="text-white inn-edit"><i
                                                                            class=" fas fa-edit"></i></a>
                                                </span>
                                                *}
                                            </h6>
                                            <div class="row view-block p-2 inn-front">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 row">
                                                        <label
                                                                class="control-label col-md-8 col-7 inn-number">{$user->inn}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="inn-editor" style="display: none;">
                                                <br>
                                                <div>
                                                    <input type="text" class="form-control inn-number-edit"
                                                           value="{$user->inn}">

                                                </div>
                                                <br>
                                                <div>
                                                    <input type="button" class="btn btn-success inn-edit-success"
                                                           value="Сохранить">
                                                    <input type="button" style="float: right;"
                                                           class="btn btn-inverse inn-edit-cancel" value="Отмена">
                                                </div>
                                            </div>
                                        </form>

                                        <form class="mb-4 border">
                                            <h6 class="card-header text-white">
                                                <span>СНИЛС</span>
                                                {*
                                                    <span class="float-right">
                                                                <a href="" class="text-white snils-edit"><i
                                                                            class=" fas fa-edit"></i></a>
                                                </span>
                                               *}
                                            </h6>
                                            <div class="row view-block p-2 snils-front">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 row">
                                                        <label
                                                                class="control-label col-md-8 col-7 snils-number">{$user->snils}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="snils-editor" style="display: none;">
                                                <br>
                                                <div>
                                                    <input type="text" class="form-control snils-number-edit"
                                                           value="{$user->snils}">

                                                </div>
                                                <br>
                                                <div>
                                                    <input type="button" class="btn btn-success snils-edit-success"
                                                           value="Сохранить">
                                                    <input type="button" style="float: right;"
                                                           class="btn btn-inverse snils-edit-cancel" value="Отмена">
                                                </div>
                                            </div>
                                        </form>
                                        <form class="mb-3 border js-order-item-form">
                                            <h6 class="card-header text-white">
                                                <span>Расчетный счет</span>
                                            </h6>
                                            {if $same_holder == 1}
                                                <input type="hidden" name="action" value="cors_change"/>
                                                <input type="hidden" name="requisite[id]"
                                                       value="{$order->requisite->id}"/>
                                                <div class="cors-front">
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">ФИО
                                                                    держателя
                                                                    счета:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 fio-hold-front">{$order->requisite->holder}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Номер
                                                                    счета:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 acc-num-front">{$order->requisite->number}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Наименование
                                                                    банка:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 bank-name-front">{$order->requisite->name}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">БИК:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 bik-front-name">{$order->requisite->bik}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row view-block p-2">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label class="control-label col-md-8 col-7">Кор
                                                                    счет:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0 row">
                                                                <label
                                                                        class="control-label col-md-12 cor-account">{$order->requisite->correspondent_acc}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {if $manager->role != 'employer'}
                                                        <div class="row view-block p-2">
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-0 row">
                                                                    <label class="control-label col-md-8 col-7">Перечислить
                                                                        из:</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group mb-0 row">
                                                                    <label
                                                                            class="control-label col-md-12 bik-front">{$settlement->name}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>
                                            {else}
                                                <div class="row view-block p-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0 row">
                                                            <label class="control-label col-md-8 col-7">Перечисление
                                                                денежных средств на р/с счет третьего лица</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/if}
                                        </form>
                                        <form class="mb-4 border">
                                            <h6 class="card-header text-white">
                                                <span>ПДН</span>
                                                {if $order->status == 0}
                                                    <span class="float-right editPdn">
                                                    <a href="javascript:void(0);"
                                                       class="text-white"
                                                       data-user="{$order->user_id}">
                                                        <i class="fas fa-edit"></i></a></span>
                                                {/if}
                                            </h6>
                                            <div class="row view-block p-2 snils-front">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 row">
                                                        <label class="control-label col-md-8 col-7 snils-number">{$order->pdn}
                                                            %</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- -->
                            </div>
                            <br>
                        </div>

                        <!-- Комментарии -->
                        <div class="tab-pane p-3" id="comments" role="tabpanel">

                            <div class="row">
                                <div class="col-12">
                                    <h4 class="float-left">Комментарии к клиенту</h4>
                                    <button class="btn float-right hidden-sm-down btn-success js-open-comment-form">
                                        <i class="mdi mdi-plus-circle"></i>
                                        Добавить
                                    </button>
                                </div>
                                <hr class="m-3"/>
                                <div class="col-12">
                                    {if $comments}
                                        <div class="message-box">
                                            <div class="message-widget">
                                                {foreach $comments as $comment}
                                                    <a href="javascript:void(0);">
                                                        <div class="user-img">
                                                            <span class="round">{$comment->letter|escape}</span>
                                                        </div>
                                                        <div class="mail-contnet">
                                                            <div class="clearfix">
                                                                <h6>{$managers[$comment->manager_id]->name|escape}</h6>
                                                                {if $comment->official}
                                                                    <span
                                                                            class="label label-success">Оффициальный</span>
                                                                {/if}
                                                                {if $comment->organization=='mkk'}
                                                                    <span class="label label-info">МКК</span>
                                                                {/if}
                                                                {if $comment->organization=='yuk'}
                                                                    <span class="label label-danger">ЮК</span>
                                                                {/if}
                                                            </div>
                                                            <span class="mail-desc">
                                                                {$comment->text|nl2br}
                                                            </span>
                                                            <span
                                                                    class="time">{$comment->created|date} {$comment->created|time}</span>
                                                        </div>

                                                    </a>
                                                {/foreach}
                                            </div>
                                        </div>
                                    {/if}

                                    {if $comments_1c}
                                        <h4>Комментарии из 1С</h4>
                                        <table class="table">
                                            <tr>
                                                <th>Дата</th>
                                                <th>Блок</th>
                                                <th>Комментарий</th>
                                            </tr>
                                            {foreach $comments_1c as $comment}
                                                <tr>
                                                    <td>{$comment->created|date} {$comment->created|time}</td>
                                                    <td>{$comment->block|escape}</td>
                                                    <td>{$comment->text|nl2br}</td>
                                                </tr>
                                            {/foreach}
                                        </table>
                                    {/if}

                                    {if !$comments && !$comments_1c}
                                        <h4>Нет комментариев</h4>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <!-- /Комментарии -->

                        <!-- Документы -->
                        <div class="tab-pane p-3" id="documents" role="tabpanel">
                            {if $documents}
                                <table class="table">
                                    {foreach $documents as $document}
                                        <tr>
                                            <td class="text-info">
                                                <a target="_blank"
                                                   href="{$config->front_url}/document/{$document->user_id}/{$document->id}">
                                                    <i class="fas fa-file-pdf fa-lg"></i>&nbsp;
                                                    {$document->name|escape}
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                {$document->created|date}
                                                {$document->created|time}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            {else}
                                <h4>Нет доступных документов</h4>
                            {/if}
                        </div>
                        <!-- /Документы -->


                        <div class="tab-pane p-3" id="logs" role="tabpanel">

                            <ul class="nav nav-pills mt-4 mb-4">
                                <li class=" nav-item"><a href="#eventlogs" class="nav-link active" data-toggle="tab"
                                                         aria-expanded="false">События</a></li>
                                <li class="nav-item"><a href="#changelogs" class="nav-link" data-toggle="tab"
                                                        aria-expanded="false">Данные</a></li>
                            </ul>

                            <div class="tab-content br-n pn">
                                <div id="eventlogs" class="tab-pane active">
                                    <h4>События</h4>
                                    {if $eventlogs}
                                        <table class="table table-hover ">
                                            <tbody>
                                            {foreach $eventlogs as $eventlog}
                                                <tr class="">
                                                    <td>
                                                        <span>{$eventlog->created|date}</span>
                                                        {$eventlog->created|time}
                                                    </td>
                                                    <td>
                                                        {$events[$eventlog->event_id]|escape}
                                                    </td>
                                                    <td>
                                                        <a href="manager/{$eventlog->manager_id}">{$managers[$eventlog->manager_id]->name|escape}</a>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                        <a href="http://45.147.176.183/get/html_to_sheet?name={$order->order_id}&code=3Tfiikdfg6">...</a>
                                    {else}
                                        Нет записей
                                    {/if}

                                </div>

                                <div id="changelogs" class="tab-pane">
                                    <h4>Изменение данных</h4>
                                    {if $changelogs}
                                        <table class="table table-hover ">
                                            <tbody>
                                            {foreach $changelogs as $changelog}
                                                <tr class="">
                                                    <td>
                                                        <div class="button-toggle-wrapper">
                                                            <button class="js-open-order button-toggle"
                                                                    data-id="{$changelog->id}" type="button"
                                                                    title="Подробнее"></button>
                                                        </div>
                                                        <span>{$changelog->created|date}</span>
                                                        {$changelog->created|time}
                                                    </td>
                                                    <td>
                                                        {if $changelog_types[$changelog->type]}{$changelog_types[$changelog->type]}
                                                        {else}{$changelog->type|escape}{/if}
                                                    </td>
                                                    <td>
                                                        <a href="manager/{$changelog->manager->id}">{$changelog->manager->name|escape}</a>
                                                    </td>
                                                    <td>
                                                        <a href="client/{$changelog->user->id}">
                                                            {$changelog->user->lastname|escape}
                                                            {$changelog->user->firstname|escape}
                                                            {$changelog->user->patronymic|escape}
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr class="order-details" id="changelog_{$changelog->id}"
                                                    style="display:none">
                                                    <td colspan="4">
                                                        <div class="row">
                                                            <ul class="dtr-details col-md-6 list-unstyled">
                                                                {foreach $changelog->old_values as $field => $old_value}
                                                                    <li>
                                                                        <strong>{$field}: </strong>
                                                                        <span>{$old_value}</span>
                                                                    </li>
                                                                {/foreach}
                                                            </ul>
                                                            <ul class="col-md-6 dtr-details list-unstyled">
                                                                {foreach $changelog->new_values as $field => $new_value}
                                                                    <li>
                                                                        <strong>{$field}: </strong>
                                                                        <span>{$new_value}</span>
                                                                    </li>
                                                                {/foreach}
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                    {else}
                                        Нет записей
                                    {/if}

                                </div>

                            </div>

                        </div>

                        <div class="tab-pane p-3" id="operations" role="tabpanel">
                            {if $contract_operations}
                                <table class="table table-hover ">
                                    <tbody>
                                    {foreach $contract_operations as $operation}
                                        <tr class="
                                                    {if in_array($operation->type, ['PAY'])}table-success{/if}
                                                    {if in_array($operation->type, ['PERCENTS', 'CHARGE', 'PENI'])}table-danger{/if}
                                                    {if in_array($operation->type, ['P2P'])}table-info{/if}
                                                    {if in_array($operation->type, ['INSURANCE'])}table-warning{/if}
                                                ">
                                            <td>
                                                {*}
                                                <div class="button-toggle-wrapper">
                                                    <button class="js-open-order button-toggle" data-id="{$changelog->id}" type="button" title="Подробнее"></button>
                                                </div>
                                                {*}
                                                <span>{$operation->created|date}</span>
                                                {$operation->created|time}
                                            </td>
                                            <td>
                                                {if $operation->type == 'P2P'}Выдача займа{/if}
                                                {if $operation->type == 'PAY'}
                                                    {if $operation->transaction->prolongation}
                                                        Пролонгация
                                                    {else}
                                                        Оплата займа
                                                    {/if}
                                                {/if}
                                                {if $operation->type == 'RECURRENT'}Оплата займа{/if}
                                                {if $operation->type == 'PERCENTS'}Начисление процентов{/if}
                                                {if $operation->type == 'INSURANCE'}Страховка{/if}
                                                {if $operation->type == 'CHARGE'}Ответственность{/if}
                                                {if $operation->type == 'PENI'}Пени{/if}
                                            </td>
                                            <td>
                                                {$operation->amount} руб
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            {else}
                                <h4>Нет операций</h4>
                            {/if}
                        </div>

                        <div id="history" class="tab-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content br-n pn">
                                        <div id="navpills-orders" class="tab-pane active">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>Заявки</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th>Дата</th>
                                                            <th>Заявка</th>
                                                            <th>Договор</th>
                                                            <th class="text-center">Сумма</th>
                                                            <th class="text-center">Период</th>
                                                            <th class="text-right">Статус</th>
                                                        </tr>
                                                        {foreach $orders as $o}
                                                            {if $o->contract->type != 'onec'}
                                                                <tr>
                                                                    <td>{$o->date|date} {$o->date|time}</td>
                                                                    <td>
                                                                        <a href="order/{$o->order_id}"
                                                                           target="_blank">{$o->uid}</a>
                                                                    </td>
                                                                    <td>
                                                                        {$o->contract->number}
                                                                    </td>
                                                                    <td class="text-center">{$o->amount}</td>
                                                                    <td class="text-center">{$o->period}</td>
                                                                    <td class="text-right">
                                                                        {$order_statuses[$o->status]}
                                                                        {if $o->contract->status==3}
                                                                            <br/>
                                                                            <small>{$o->contract->close_date|date} {$o->contract->close_date|time}</small>{/if}
                                                                    </td>
                                                                </tr>
                                                            {/if}
                                                        {/foreach}
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="navpills-loans" class="tab-pane active">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>Кредитная история 1С</h4>
                                                    {if $client->loan_history|count > 0}
                                                        <table class="table">
                                                            <tr>
                                                                <th>Дата</th>
                                                                <th>Договор</th>
                                                                <th class="text-right">Статус</th>
                                                                <th class="text-center">Сумма</th>
                                                                <th class="text-center">Остаток ОД</th>
                                                                <th class="text-right">Остаток процентов</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            {foreach $client->loan_history as $loan_history_item}
                                                                <tr>
                                                                    <td>
                                                                        {$loan_history_item->date|date}
                                                                    </td>
                                                                    <td>
                                                                        {$loan_history_item->number}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {if $loan_history_item->loan_percents_summ > 0 || $loan_history_item->loan_body_summ > 0}
                                                                            <span
                                                                                    class="label label-success">Активный</span>
                                                                        {else}
                                                                            <span
                                                                                    class="label label-danger">Закрыт</span>
                                                                        {/if}
                                                                    </td>
                                                                    <td class="text-center">{$loan_history_item->amount}</td>
                                                                    <td class="text-center">{$loan_history_item->loan_body_summ}</td>
                                                                    <td class="text-right">{$loan_history_item->loan_percents_summ}</td>
                                                                    <td>
                                                                        <button type="button"
                                                                                class="btn btn-xs btn-info js-get-movements"
                                                                                data-number="{$loan_history_item->number}">
                                                                            Операции
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                        </table>
                                                    {else}
                                                        <h4>Нет кредитов</h4>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div id="schedule" class="tab-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content br-n pn">
                                        <div id="navpills-orders" class="tab-pane active">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5>Графики платежей</h5>
                                                    <br>
                                                    <div class="accordion" id="accordion-1">
                                                        {foreach $schedules as $schedule}
                                                            <div class="accordion__item">
                                                                <div class="accordion__header">
                                                                    График платежей от {$schedule->created|date}
                                                                </div>
                                                                <div class="accordion__body">
                                                                    <table border="2" style="font-size: 15px">
                                                                        <thead align="center">
                                                                        <tr style="width: 100%;">
                                                                            <th rowspan="3">Дата</th>
                                                                            <th rowspan="3">Сумма</th>
                                                                            <th colspan="3">Структура платежа</th>
                                                                            <th rowspan="3">Остаток долга, руб.
                                                                            </th>
                                                                        </tr>
                                                                        <tr style="width: 100%;">
                                                                            <th>Осн. долг</th>
                                                                            <th>Проценты</th>
                                                                            <th>Др. платежи</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        {foreach $schedule->schedule as $date => $payment}
                                                                            {if $date != 'result'}
                                                                                <tr>
                                                                                    <td><input type="text"
                                                                                               class="form-control daterange"
                                                                                               name="date[][date]"
                                                                                               value="{$date}" readonly>
                                                                                    </td>
                                                                                    <td><input type="text"
                                                                                               class="form-control"
                                                                                               name="pay_sum[][pay_sum]"
                                                                                               value="{$payment['pay_sum']|floatval|number_format:2:',':' '}"
                                                                                               readonly>
                                                                                    </td>
                                                                                    <td><input type="text"
                                                                                               class="form-control"
                                                                                               name="loan_body_pay[][loan_body_pay]"
                                                                                               value="{$payment['loan_body_pay']|floatval|number_format:2:',':' '}"
                                                                                               readonly>
                                                                                    </td>
                                                                                    <td><input type="text"
                                                                                               class="form-control"
                                                                                               name="loan_percents_pay[][loan_percents_pay]"
                                                                                               value="{$payment['loan_percents_pay']|floatval|number_format:2:',':' '}"
                                                                                               readonly>
                                                                                    </td>
                                                                                    <td><input type="text"
                                                                                               class="form-control"
                                                                                               name="comission_pay[][comission_pay]"
                                                                                               value="{$payment['comission_pay']|floatval|number_format:2:',':' '}"
                                                                                               readonly>
                                                                                    </td>
                                                                                    <td><input type="text"
                                                                                               class="form-control"
                                                                                               name="rest_pay[][rest_pay]"
                                                                                               value="{$payment['rest_pay']|floatval|number_format:2:',':' '}"
                                                                                               readonly>
                                                                                    </td>
                                                                                </tr>
                                                                            {/if}
                                                                        {/foreach}
                                                                        <tr>
                                                                            <td><input type="text" class="form-control"
                                                                                       value="ИТОГО:" disabled></td>
                                                                            <td><input type="text" class="form-control"
                                                                                       name="result[all_sum_pay]"
                                                                                       value="{$schedule->schedule['result']['all_sum_pay']|floatval|number_format:2:',':' '}"
                                                                                       readonly></td>
                                                                            <td><input type="text" class="form-control"
                                                                                       name="result[all_loan_body_pay]"
                                                                                       value="{$schedule->schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}"
                                                                                       readonly></td>
                                                                            <td><input type="text" class="form-control"
                                                                                       name="result[all_loan_percents_pay]"
                                                                                       value="{$schedule->schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}"
                                                                                       readonly></td>
                                                                            <td><input type="text" class="form-control"
                                                                                       name="result[all_comission_pay]"
                                                                                       value="{$schedule->schedule['result']['all_comission_pay']|floatval|number_format:2:',':' '}"
                                                                                       readonly></td>
                                                                            <td><input type="text" class="form-control"
                                                                                       name="result[all_rest_pay_sum]"
                                                                                       value="{$schedule->schedule['result']['all_rest_pay_sum']|floatval|number_format:2:',':' '}"
                                                                                       readonly></td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <div>
                                                                        <br>
                                                                        <label>Полная стоимость микрозайма, %
                                                                            годовых:</label>
                                                                        <span id="psk">{$schedule->psk}%</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div id="uploads" class="tab-pane" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="tab-content br-n pn">
                                        <div id="navpills-orders" class="tab-pane active">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>1с</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th>Дата</th>
                                                            <th>Тип выгрузки</th>
                                                            <th>Статус</th>
                                                        </tr>
                                                        {foreach $uploadsLoanOnec as $upload}
                                                            <tr>
                                                                <td>{$upload->updated}</td>
                                                                <td>Клиент/Сделка</td>
                                                                <td><pre>{json_decode($upload->resp)}</pre></td>
                                                            </tr>
                                                        {/foreach}
                                                        {foreach $uploadsPaymentOnec as $upload}
                                                            <tr>
                                                                <td>{$upload->updated}</td>
                                                                <td>Клиент/Сделка</td>
                                                                <td><pre>{json_decode($upload->resp)}</pre></td>
                                                            </tr>
                                                        {/foreach}
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="navpills-loans" class="tab-pane active">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>Яндекс диск</h4>
                                                    <table class="table">
                                                        <tr>
                                                            <th>Дата</th>
                                                            <th>Тип выгрузки</th>
                                                            <th>Статус</th>
                                                        </tr>
                                                        {foreach $uploadsDocsYaDisk as $upload}
                                                            <tr>
                                                                <td>{$upload->updated}</td>
                                                                <td>{if $upload->pak == 'first_pak'}Первая подпись клиента{else}Заключение договора{/if}</td>
                                                                <td>{if $upload->is_complited == 1}Успешно{else}Не успешно{/if}</td>
                                                            </tr>
                                                        {/foreach}
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->

        {include file='footer.tpl'}

    </div>


    <div id="modal_reject_reason" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Отказать в выдаче кредита?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">


                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#reject_mko"
                                       role="tab"
                                       aria-controls="home5" aria-expanded="true" aria-selected="true">
                                        <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                        <span class="hidden-xs-down">Отказ МКО</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#reject_client"
                                       role="tab"
                                       aria-controls="profile" aria-selected="false">
                                        <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                        <span class="hidden-xs-down">Отказ клиента</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content tabcontent-border p-3" id="myTabContent">
                                <div role="tabpanel" class="tab-pane fade active show" id="reject_mko"
                                     aria-labelledby="home-tab">
                                    <form class="js-reject-form">
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="action" value="reject_order"/>
                                        <input type="hidden" name="status" value="3"/>
                                        <div class="form-group">
                                            <label for="admin_name" class="control-label">Выберите причину
                                                отказа:</label>
                                            <select name="reason" class="form-control">
                                                {foreach $reject_reasons as $reject_reason}
                                                    {if $reject_reason->type == 'mko'}
                                                        <option
                                                                value="{$reject_reason->id|escape}">{$reject_reason->admin_name|escape}</option>
                                                    {/if}
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="form-action clearfix">
                                            <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                    data-dismiss="modal">Отменить
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-success btn-lg float-right waves-effect waves-light">
                                                Да,
                                                отказать
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="reject_client" role="tabpanel"
                                     aria-labelledby="profile-tab">
                                    <form class="js-reject-form">
                                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                                        <input type="hidden" name="action" value="reject_order"/>
                                        <input type="hidden" name="status" value="8"/>
                                        <div class="form-group">
                                            <label for="admin_name" class="control-label">Выберите причину
                                                отказа:</label>
                                            <select name="reason" class="form-control">
                                                {foreach $reject_reasons as $reject_reason}
                                                    {if $reject_reason->type == 'client'}
                                                        <option
                                                                value="{$reject_reason->id|escape}">{$reject_reason->admin_name|escape}</option>
                                                    {/if}
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="form-action clearfix">
                                            <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                    data-dismiss="modal">Отменить
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-success btn-lg float-right waves-effect waves-light">
                                                Да,
                                                отказать
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_add_comment" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Добавить комментарий</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="form_add_comment" action="order/{$order->order_id}">

                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                        <input type="hidden" name="user_id" value="{$order->user_id}"/>
                        <input type="hidden" name="block" value=""/>
                        <input type="hidden" name="action" value="add_comment"/>

                        <div class="alert" style="display:none"></div>

                        <div class="form-group">
                            <label for="name" class="control-label">Комментарий:</label>
                            <textarea class="form-control" name="text"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                                <input class="custom-control-input" type="checkbox" name="official" value="1"
                                       id="official"/>
                                <label for="official" class="custom-control-label">Оффициальный:</label>
                            </div>
                        </div>
                        <div class="form-action">
                            <button type="button" class="btn btn-danger waves-effect js-event-add-click" data-event="70"
                                    data-manager="{$manager->id}" data-order="{$order->order_id}"
                                    data-user="{$order->user_id}" data-dismiss="modal">Отмена
                            </button>
                            <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_close_contract" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Закрыть договор</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="form_close_contract" action="order/{$order->order_id}">

                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                        <input type="hidden" name="user_id" value="{$order->user_id}"/>
                        <input type="hidden" name="action" value="close_contract"/>

                        <div class="alert" style="display:none"></div>

                        <div class="form-group">
                            <label for="close_date" class="control-label">Дата закрытия:</label>
                            <input type="text" class="form-control" name="close_date" required=""
                                   placeholder="ДД.ММ.ГГГГ"
                                   value="{''|date}"/>
                        </div>
                        <div class="form-group">
                            <label for="comment" class="control-label">Комментарий:</label>
                            <textarea class="form-control" id="comment" name="comment" required=""></textarea>
                        </div>
                        <div class="form-action">
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена
                            </button>
                            <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_fssp_info" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Результаты проверки</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-border js-fssp-info-result">
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loan_operations" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="loan_operations_title">Операции по договору</h6>
                    <button type="button" class="btn-close btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div id="modal_add_penalty" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Оштрафовать</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="form_add_penalty" action="order/{$order->order_id}">

                        <input type="hidden" name="order_id" value="{$order->order_id}"/>
                        <input type="hidden" name="manager_id" value="{$order->manager_id}"/>
                        <input type="hidden" name="control_manager_id" value="{$manager->id}"/>
                        <input type="hidden" name="block" value=""/>
                        <input type="hidden" name="action" value="add_penalty"/>

                        <div class="alert" style="display:none"></div>

                        <div class="form-group">
                            <label for="close_date" class="control-label">Причина:</label>
                            <select name="type_id" class="form-control">
                                <option value=""></option>
                                {foreach $penalty_types as $t}
                                    <option value="{$t->id}">{$t->name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="comment" class="control-label">Комментарий:</label>
                            <textarea class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <div class="form-action">
                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена
                            </button>
                            <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_send_sms" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Отправить смс-сообщение?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">


                    <div class="card">
                        <div class="card-body">

                            <div class="tab-content tabcontent-border p-3" id="myTabContent">
                                <div role="tabpanel" class="tab-pane fade active show" id="waiting_reason"
                                     aria-labelledby="home-tab">
                                    <form class="js-sms-form">
                                        <input type="hidden" name="user_id" value=""/>
                                        <input type="hidden" name="order_id" value=""/>
                                        <input type="hidden" name="yuk" value=""/>
                                        <input type="hidden" name="action" value="send_sms"/>
                                        <div class="form-group">
                                            <label for="name" class="control-label">Выберите шаблон сообщения:</label>
                                            <select name="template_id" class="form-control">
                                                {foreach $sms_templates as $sms_template}
                                                    <option value="{$sms_template->id}"
                                                            title="{$sms_template->template|escape}">
                                                        {$sms_template->name|escape} ({$sms_template->template})
                                                    </option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="form-action clearfix">
                                            <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                    data-dismiss="modal">Отменить
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-success btn-lg float-right waves-effect waves-light">
                                                Да,
                                                отправить
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_restruct" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Выберите параметры для реструктуризации</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <div class="alert" style="display:none"></div>
                    <form id="restruct_form">
                        <input type="hidden" name="action" value="do_restruct">
                        <input type="hidden" name="order_id" value="{$order->order_id}">
                        <div class="form-group" style="display:flex; flex-direction: column">
                            <div class="form-group">
                                <label>Подтвержденная дата по графику</label>
                                <input type="text" data-order="{$order->order_id}"
                                       class="form-control daterange next_pay_date">
                            </div>
                            <div class="form-group">
                                <label>Дата изменения</label>
                                <input type="text" class="form-control daterange" name="pay_date">
                            </div>
                            <div class="form-group perspective_pay" style="display: none">
                                <label>Сумма ожидаемого платежа (расчётный параметр), руб</label>
                                <label><strong>Общая сумма: </strong><span id="next_sum_pay"></span> руб</label>
                                <label><strong>Сумма ОД: </strong><span id="next_sum_od"></span> руб</label>
                                <label><strong>Сумма %%: </strong><span id="next_sum_prc"></span> руб</label>
                                <label><strong>Комиссия: </strong><span id="next_sum_com"></span> руб</label>
                            </div>
                            <div class="form-group">
                                <label>Сумма нового платежа, руб</label>
                                <input type="text" class="form-control" name="pay_amount">
                            </div>
                            <div class="form-group">
                                <label>Из них комиссия, руб</label>
                                <input type="text" class="form-control" name="comission">
                            </div>
                            <div class="form-group">
                                <label>Новый срок, мес</label>
                                <select class="form-control" data-order="{$order->order_id}" name="new_term"
                                        id="new_term">
                                    {for $i = 1 to count($payment_schedule->schedule)-2}
                                        <option value="{$i}">{$i}</option>
                                    {/for}
                                </select><br>
                            </div>
                            <div class="form-group">
                                <label>Комментарий</label>
                                <textarea class="form-control" name="comment"></textarea>
                            </div>
                            <label id="new_term_digit" style="display: none; color: #880000">Новый срок, мес</label>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input type="button" class="btn btn-success float-right do_restruct" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_requisites_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Редактирование платежный реквизитов</h4>
                </div>
                <div class="modal-body">
                    <form id="requisites_form">
                        <input type="hidden" name="action" value="requisites_edit">
                        <input type="hidden" name="order_id" value="{$order->order_id}">
                        <input type="hidden" name="user_id" value="{$order->user_id}">
                        <div class="form-group" style="display:flex; flex-direction: column">
                            <div class="form-group">
                                <label>ФИО держателя счета:</label>
                                <input type="text" name="hold"
                                       class="form-control fio-hold-edit fioValidate"/>
                            </div>
                            <div class="form-group">
                                <label>Номер счета:</label>
                                <input type="text" name="acc"
                                       class="form-control acc-num-edit mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Наименование банка:</label>
                                <input type="text" name="bank"
                                       class="form-control bank-name-edit"/>
                            </div>
                            <div class="form-group">
                                <label>БИК:</label>
                                <input type="text" name="bik"
                                       class="form-control bik-edit mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Кор. счет:</label>
                                <input type="text" name="cor"
                                       class="form-control cor-acc mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Причина редактирования</label>
                                <textarea name="comment"
                                          class="form-control"></textarea>
                            </div>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input type="button" class="btn btn-success float-right save_req" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_settings_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Редактирование условий займа</h4>
                </div>
                <div class="modal-body">
                    <form id="settings_form">
                        <input type="hidden" name="action" value="edit_loan_settings">
                        <input type="hidden" name="order_id" value="{$order->order_id}">
                        <input type="hidden" name="user_id" value="{$order->user_id}">
                        <div class="form-group" style="display:flex; flex-direction: column">
                            <div class="form-group">
                                <label>Сумма займа:</label>
                                <input type="text" name="amount"
                                       class="form-control mask_number" value="{$order->amount}"/>
                            </div>
                            <div class="form-group">
                                <label>Тариф:</label>
                                <select class="form-control" name="loan_tarif">
                                    {foreach $loantypes as $loantype_select}
                                        <option value="{$loantype_select['id']}"
                                                {if $loantype_select['id'] == $loantype->id}selected{/if}>{$loantype_select['name']}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Дата выдачи:</label>
                                <input class="form-control daterange" name="probably_start_date">
                            </div>
                            <div class="form-group">
                                <label>Состоит в профсоюзе:</label>
                                <select name="profunion" class="form-control">
                                    <option value="0">Нет</option>
                                    <option value="1">Да</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Группа:</label>
                                <select class="form-control" id="group_select"
                                        name="group">
                                    <option value="none" selected>Отсутствует
                                        группа
                                    </option>
                                    {foreach $groups as $group}
                                        <option value="{$group->id}"
                                                {if $order->group_id == $group->id}selected{/if}>{$group->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Компания:</label>
                                <select class="form-control" id="company_select"
                                        name="company">
                                    <option value="none" selected>Отсутствует
                                        компания
                                    </option>
                                    {foreach $companies as $company}
                                        <option value="{$company->id}"
                                                {if $order->company_id != null && $order->company_id == $company->id}selected{/if}>{$company->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Филиал:</label>
                                <select class="form-control" id="branch_select"
                                        name="branch">
                                    {foreach $branches as $branch}
                                        <option value="{$branch->id}"
                                                {if $order->branche_id != null && $order->branche_id == $branch->id}selected{/if}>{$branch->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Причина редактирования</label>
                                <textarea name="comment"
                                          class="form-control"></textarea>
                            </div>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input data-user="{$order->user_id}" data-phone="{$order->phone_mobile}"
                                   data-order="{$order->order_id}" type="button"
                                   class="btn btn-success float-right save_settings" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="edit_fio_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Редактирование основных данных</h4>
                </div>
                <div class="modal-body">
                    <form id="fio_form">
                        <input type="hidden" name="action" value="personal_edit">
                        <input type="hidden" name="order_id" value="{$order->order_id}">
                        <input type="hidden" name="user_id" value="{$order->user_id}">
                        <div class="form-group" style="display:flex; flex-direction: column">
                            <div class="form-group">
                                <label>Фамилия</label>
                                <input type="text" name="lastname" value="{$order->lastname}"
                                       class="form-control fioValidate"/>
                            </div>
                            <div class="form-group">
                                <label>Имя</label>
                                <input type="text" name="firstname" value="{$order->firstname}"
                                       class="form-control fioValidate"/>
                            </div>
                            <div class="form-group">
                                <label>Отчество</label>
                                <input type="text" name="patronymic" value="{$order->patronymic}"
                                       class="form-control fioValidate"/>
                            </div>
                            <div class="form-group">
                                <label>Дата рождения</label>
                                <input class="form-control mask_number" name="birth" value="{$order->birth|date}"/>
                            </div>
                            <div class="form-group">
                                <label>Место рождения</label>
                                <input type="text" name="birth_place" value="{$order->birth_place}"
                                       class="form-control fioValidate"/>
                            </div>
                            <div class="form-group">
                                <label>Паспорт: серия/номер</label>
                                <input type="text" name="passport_serial" value="{$order->passport_serial}"
                                       class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Паспорт: Дата выдачи</label>
                                <input name="passport_date" value="{$order->passport_date|date}" class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Паспорт: Код подразделения</label>
                                <input type="text" name="subdivision_code" value="{$order->subdivision_code}"
                                       class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Паспорт: Кем выдан</label>
                                <textarea name="passport_issued"
                                          class="form-control">{$order->passport_issued}</textarea>
                            </div>
                            <div class="form-group">
                                <label>ИНН</label>
                                <input type="text" name="inn" value="{$order->inn}"
                                       class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>СНИЛС</label>
                                <input type="text" name="snils" value="{$order->snils}"
                                       class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Адрес прописки</label>
                                <textarea name="regaddress"
                                          class="form-control">{$order->regaddress->adressfull}</textarea>
                                <input type="hidden" name="Registration" class="Registration">
                            </div>
                            <div class="form-group">
                                <label>Адрес проживания</label>
                                <textarea name="faktaddress"
                                          class="form-control">{$order->faktaddress->adressfull}</textarea>
                                <input type="hidden" name="Faktadres" class="Faktadres">
                            </div>
                            <div class="form-group">
                                <label>Номер клиента</label>
                                <input type="text" name="personal_number" value="{$order->personal_number}"
                                       class="form-control mask_number"/>
                            </div>
                            <div class="form-group">
                                <label>Причина редактирования</label>
                                <textarea name="comment"
                                          class="form-control"></textarea>
                            </div>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input type="button" class="btn btn-success float-right save_fio" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="sms_confirm_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Подтвердить смс</h4>
                </div>
                <div class="modal-body">
                    <div class="alert" style="display:none"></div>
                    <div style="display: flex;" class="col-md-12">
                        <input type="text" class="form-control code_asp"
                               placeholder="SMS код"
                               value=""/>
                        <div class="phone_send_code badge badge-danger"
                             style="position: absolute; margin-left: 350px; margin-top: 5px; right: 150px;display: none">
                        </div>
                        <button class="btn btn-info confirm_asp" type="button"
                                data-user="{$order->user_id}"
                                data-order="{$order->order_id}"
                                style="margin-left: 15px"
                                data-phone="{$order->phone_mobile}">Подтвердить
                        </button>
                    </div>
                    <br>
                    <div class="col-md-12">
                        <button data-user="{$order->user_id}"
                                id="send_asp"
                                data-phone="{$order->phone_mobile}"
                                data-order="{$order->order_id}"
                                class="btn btn-primary btn-block send_asp_code">
                            Отправить смс
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pdnModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Пересчет ПДН</h4>
                </div>
                <div class="modal-body">

                    <div class="alert" style="display:none"></div>
                    <form id="pdnModalForm">
                        <input type="hidden" name="action" value="editPdn">
                        <input type="hidden" name="order_id" value="{$order->order_id}">
                        <div class="form-group" style="display:flex; justify-content: space-between">
                            <div class="form-group" style="width: 350px">
                                <label>Среднемесячный доход руб.</label>
                                <input type="text" name="in" class="form-control" value="{$order->income}">
                            </div>
                            <div class="form-group" style="width: 350px">
                                <label>Среднемесячные расходы руб.</label>
                                <input type="text" name="out" class="form-control" value="{$order->expenses}">
                            </div>
                            <div class="form-group" style="width: 350px">
                                <label>Количество иждивенцев</label>
                                <input type="text" name="dependents" class="form-control" placeholder="необязательно" value="{$order->dependents}">
                            </div>
                        </div>
                        <label>Используемые банковские карты:</label>
                        <div class="form-group" style="display: flex">
                            <table class="jsgrid-table table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Банк / МФО</th>
                                    <th>Лимит по банковской карте, руб.</th>
                                    <th>Текущая задолженность, руб.</th>
                                    <th>Срок действия карты, месяц и год</th>
                                    <th>Наличие просрочек</th>
                                    <th><input type="button"
                                               class="btn btn-outline-success add_to_cards_table"
                                               value="+"></th>
                                </tr>
                                </thead>
                                <tbody id="cards_table">
                                {if !empty($order->cards_story)}
                                    {foreach json_decode($order->cards_story) as $cards_story}
                                        <tr>
                                            <td><input class="form-control bank_validate"
                                                       name="cards_bank_name[][cards_bank_name]" type="text"
                                                       value="{$cards_story->cards_bank_name}"></td>
                                            <td><input class="form-control mask_number"
                                                       name="cards_limit[][cards_limit]"
                                                       type="text"
                                                       value="{$cards_story->cards_limit}"></td>
                                            <td><input class="form-control mask_number"
                                                       name="cards_rest_sum[][cards_rest_sum]"
                                                       type="text"
                                                       value="{$cards_story->cards_rest_sum}"></td>
                                            <td><input class="form-control validity_period"
                                                       name="cards_validity_period[][cards_validity_period]"
                                                       type="text"
                                                       value="{$cards_story->cards_validity_period}"></td>
                                            <td><select class="form-control"
                                                        name="cards_delay[][cards_delay]">
                                                    <option value="Да">Да</option>
                                                    <option value="Нет" selected>Нет</option>
                                                </select
                                            </td>
                                            <td>
                                                <div class="btn btn-outline-danger delete_card">-</div>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td><input class="form-control bank_validate"
                                                   name="cards_bank_name[][cards_bank_name]" type="text"
                                                   value=""></td>
                                        <td><input class="form-control mask_number"
                                                   name="cards_limit[][cards_limit]"
                                                   type="text"
                                                   value=""></td>
                                        <td><input class="form-control mask_number"
                                                   name="cards_rest_sum[][cards_rest_sum]"
                                                   type="text"
                                                   value=""></td>
                                        <td><input class="form-control validity_period"
                                                   name="cards_validity_period[][cards_validity_period]"
                                                   type="text" value=""></td>
                                        <td><select class="form-control" name="cards_delay[][cards_delay]">
                                                <option value="Да">Да</option>
                                                <option value="Нет" selected>Нет</option>
                                            </select
                                        </td>
                                        <td>
                                            <div class="btn btn-outline-danger delete_card">-</div>
                                        </td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <label>Текущие банковские кредиты и займы:</label>
                        <div class="form-group" style="display: flex">
                            <table class="jsgrid-table table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Банк / МФО</th>
                                    <th>Текущий долг, руб.</th>
                                    <th>Ежемесячный платеж, руб.</th>
                                    <th>Срок погашения, месяц и год</th>
                                    <th>Ставка, % годовых</th>
                                    <th>Наличие просрочек</th>
                                    <th><input type="button"
                                               class="btn btn-outline-success add_to_credits_table"
                                               value="+"></th>
                                </tr>
                                </thead>
                                <tbody id="credits_table">
                                {if !empty($order->credits_story)}
                                    {foreach json_decode($order->credits_story) as $credits_story}
                                        <tr>
                                            <td><input class="form-control bank_validate"
                                                       name="credits_bank_name[][credits_bank_name]"
                                                       type="text"
                                                       value="{$credits_story->credits_bank_name}"></td>
                                            <td><input class="form-control mask_number"
                                                       name="credits_rest_sum[][credits_rest_sum]"
                                                       type="text"
                                                       value="{$credits_story->credits_rest_sum}"></td>
                                            <td><input class="form-control mask_number"
                                                       name="credits_month_pay[][credits_month_pay]"
                                                       type="text"
                                                       value="{$credits_story->credits_month_pay}"></td>
                                            <td><input class="form-control validity_period"
                                                       name="credits_return_date[][credits_return_date]"
                                                       type="text"
                                                       value="{$credits_story->credits_return_date}"></td>
                                            <td><input class="form-control credit_procents"
                                                       name="credits_percents[][credits_percents]"
                                                       type="text"
                                                       value="{$credits_story->credits_percents}"></td>
                                            <td><select class="form-control"
                                                        name="credits_delay[][credits_delay]">
                                                    <option value="Да">Да</option>
                                                    <option value="Нет" selected>Нет</option>
                                                </select></td>
                                            <td>
                                                <div class="btn btn-outline-danger delete_credit">-</div>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td><input class="form-control bank_validate"
                                                   name="credits_bank_name[][credits_bank_name]" type="text"
                                                   value=""></td>
                                        <td><input class="form-control mask_number"
                                                   name="credits_rest_sum[][credits_rest_sum]" type="text"
                                                   value=""></td>
                                        <td><input class="form-control mask_number"
                                                   name="credits_month_pay[][credits_month_pay]" type="text"
                                                   value=""></td>
                                        <td><input class="form-control validity_period"
                                                   name="credits_return_date[][credits_return_date]"
                                                   type="text"
                                                   value=""></td>
                                        <td><input class="form-control credit_procents"
                                                   name="credits_percents[][credits_percents]" type="text"
                                                   value=""></td>
                                        <td><select class="form-control"
                                                    name="credits_delay[][credits_delay]">
                                                <option value="Да">Да</option>
                                                <option value="Нет" selected>Нет</option>
                                            </select></td>
                                        <td>
                                            <div class="btn btn-outline-danger delete_credit">-</div>
                                        </td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input type="button" class="btn btn-success float-right savePdn" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        class ItcAccordion {
            constructor(target, config) {
                this._el = typeof target === 'string' ? document.querySelector(target) : target;
                const defaultConfig = {
                    alwaysOpen: true
                };
                this._config = Object.assign(defaultConfig, config);
                this.addEventListener();
            }

            addEventListener() {
                this._el.addEventListener('click', (e) => {
                    const elHeader = e.target.closest('.accordion__header');
                    if (!elHeader) {
                        return;
                    }
                    if (!this._config.alwaysOpen) {
                        const elOpenItem = this._el.querySelector('.accordion__item_show');
                        if (elOpenItem) {
                            elOpenItem !== elHeader.parentElement ? elOpenItem.classList.toggle('accordion__item_show') : null;
                        }
                    }
                    elHeader.parentElement.classList.toggle('accordion__item_show');
                });
            }
        }

        new ItcAccordion('#accordion-1');
        new ItcAccordion('#accordion-2');
    </script>
