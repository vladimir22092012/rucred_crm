$(function () {

    let token_dadata = "25c845f063f9f3161487619f630663b2d1e4dcd7";

    let choose_tarif = 0;

    moment.locale('ru');

    $('.daterange').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD.MM.YYYY'
        },
    });

    $('.birth_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: moment().subtract(18, 'years'),
        locale: {
            format: 'DD.MM.YYYY'
        },
    });

    $('.to_form_loan').on('click', function (e) {

        let sum = $('#order_sum').val();
        let loan_id = Number($(this).attr('data-loan'));
        let period = Number($(this).attr('data-loan-period'));
        let max_amount = Number($(this).attr('data-max-amount'));
        let min_amount = Number($(this).attr('data-min-amount'));

        sum = sum.replace(/[^0-9]/g, "");
        sum = Number(sum);


        if (sum.length < 0 || sum == 0) {

            $('.alert-danger').fadeIn();
            $('.alert-danger').html('<span>Вы не ввели сумму займа</span>');
            $('.alert-danger').fadeOut(3000);
        }
        else {

            if (parseInt(sum) >= parseInt(min_amount) && parseInt(sum) <= parseInt(max_amount)) {
                $('.alert-danger').hide();

                $('.buttons_append').show();


                let percents = $('.price_basic[id="' + loan_id + '"]').find('.percents:visible').find('input').val();
                let date_from = $('#start_date').val();
                let date_to = $('#end_date').val();
                let company_id = $('.my_company').val();
                let branch_id = $('.branches').val();
                let amount = $('#order_sum').val();
                let profunion = $('#profunion1').prop('checked') === true ? 1 : 0;

                $.ajax({
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'sum_to_pay',
                        loan_id: loan_id,
                        date_from: date_from,
                        date_to: date_to,
                        branch_id: branch_id,
                        amount: amount,
                        percents: percents,
                        company_id: company_id,
                        profunion: profunion,
                    },
                    success: function (sum) {
                        sum['annouitet'] = new Intl.NumberFormat("ru").format(sum['annouitet']);
                        $('#final_sum').val(sum['annouitet']);
                        $('.graphic').html(sum['schedule']);
                    }
                });
            }
            else {
                $('.alert-danger').fadeIn();
                $('.alert-danger').html('<span>Проверьте правильность суммы</span>');
                $('.alert-danger').fadeOut(3000);
            }
        }
    });

    $('#start_date').on('change', function () {
        let start_date = $(this).val();
        let loan_id = $('.to_form_loan').attr('data-loan');
        let company_id = $('select[class="form-control groups"]').val();

        $.ajax({
            dataType: 'JSON',
            data: {
                start_date: start_date,
                loan_id: loan_id,
                company_id: company_id
            },
            success: function (suc) {
                $('#end_date').val(suc['date'])
            }
        });
    });

    $(document).on('click', '.price_basic', function (e) {
        e.preventDefault();

        $('.loantype_settings').show();

        $('.price_basic').css('background', 'white');
        $(this).css('background', '#b3eeff');

        choose_tarif = 1;

        let percents = $(this).children().find('.loantype-percents').text();
        let loan_id = $(this).data('loan');
        let period = $(this).data('loan-period');
        let min_amount = $(this).data('min-amount');
        let max_amount = $(this).data('max-amount');
        let branche_id = $('.branches').val();
        let company_id = $('.my_company').val();

        $('.to_form_loan').attr('data-loan', loan_id);
        $('.to_form_loan').attr('data-loan-period', period);
        $('.to_form_loan').attr('data-max-amount', max_amount);
        $('.to_form_loan').attr('data-min-amount', min_amount);
        $('.loan_type_to_submit').attr('value', loan_id);

        if (loan_id != 1) {
            $('#return_sum').hide();
            $('#month_sum').show();
        }
        else {
            $('#month_sum').hide();
            $('#return_sum').show();
        }

        let start_date = $('#start_date').val();

        $.ajax({
            dataType: 'JSON',
            data: {
                start_date: start_date,
                loan_id: loan_id,
                branche_id: branche_id,
                company_id: company_id
            },
            success: function (suc) {
                $('#end_date').val(suc['date'])
            }
        });

        $('#loan_percents').val(percents);
    });

    $('#change_fio1').on('click', function () {
        $('#change_fio_toggle').hide();
        $('.prev_fio').removeAttr('required');
        $('.fio_change_date').removeAttr('required');
    });

    $('#change_fio2').on('click', function () {
        $('#change_fio_toggle').show();
        $('.prev_fio').attr('required', true);
        $('.fio_change_date').attr('required', true);
    });

    $('#sex1').on('click', function () {
        $('#sex_toggle').show();
        $('.fio_spouse').removeAttr('required');
        $('.phone_spouse').removeAttr('required');
    });

    $('#sex2').on('click', function () {
        $('#sex_toggle').hide();
        $('.fio_public_spouse').attr('required', true);
    });

    $('#foreign_husb_wife1').on('click', function () {
        $('#foreign_husb_wife_toggle').hide();
        $('.fio_public_spouse').removeAttr('required');
    });

    $('#foreign_husb_wife2').on('click', function () {
        $('#foreign_husb_wife_toggle').show();
        $('.fio_spouse').attr('required', true);
    });

    $('#foreign_relative1').on('click', function () {
        $('#foreign_relative_toggle').hide();
        $('.fio_relative').removeAttr('required');
    });

    $('#foreign_relative2').on('click', function () {
        $('#foreign_relative_toggle').show();
        $('.fio_relative').attr('required', true);
    });

    $('#profunion1').on('click', function () {
        $('.want_profunion').prop('checked', false);
        $('#profunion_toggle').hide();
        $('.in_profunion').show();
        $('.out_profunion').hide();
    });

    $('#profunion2').on('click', function () {
        $('#profunion_toggle').show();
        $('.in_profunion').hide();
        $('.out_profunion').show();
    });

    $('.want_profunion').on('click', function (e) {
        if ($(this).is(':checked')) {
            $('.in_profunion').show();
            $('.out_profunion').hide();
        }
        else {
            $('.in_profunion').hide();
            $('.out_profunion').show();
        }
    });

    $('input[name="actual_address"]').on('click', function (e) {
        $('#actual_address_toggle').toggle();
    });

    $('.my_company').on('change', function () {

        let value = $(this).val();

        if (value == 1) {
            $('.my_company_toggle').show();
        }
        else {
            $('.my_company_toggle').hide();
        }
    });

    $('form').on('submit', function (e) {

        if (choose_tarif == 0) {
            let error = '<br><br><div style="width: 300px" class="alert alert-danger">Выберите тариф</div>';

            $('#buttons_append').after(error);

            $('.alert-danger').delay(3000).slideUp(300);
        }
    });

    $('.subdivision_code').suggestions({
        token: token_dadata,
        type: "fms_unit",
        minChars: 3,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function (suggestion) {
            $('.passport_issued').empty();
            $(this).empty();
            $('.passport_issued').val(suggestion.value);
            $(this).val(suggestion.data.code);
        }
    });

    $('.Regadress').suggestions({
        token: token_dadata,
        type: "ADDRESS",
        minChars: 3,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function (suggestion) {
            $(this).val(suggestion.value);
            $('.Registration').val(JSON.stringify(suggestion));
        }
    });

    $('.Faktaddress').suggestions({
        token: token_dadata,
        type: "ADDRESS",
        minChars: 3,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function (suggestion) {
            $(this).val(suggestion.value);
            $('.Fakt_adress').val(JSON.stringify(suggestion));
        }
    });

    $('.bik').suggestions({
        token: token_dadata,
        type: "bank",
        minChars: 3,
        /* Вызывается, когда пользователь выбирает одну из подсказок */
        onSelect: function (suggestion) {
            $(this).val(suggestion.data.bic);
            $('.bank_name').val(suggestion.value);
            $('.cor').val(suggestion.data.correspondent_account);
        }
    });

    $.fn.setCursorPosition = function (pos) {
        if ($(this).get(0).setSelectionRange) {
            $(this).get(0).setSelectionRange(pos, pos);
        } else if ($(this).get(0).createTextRange) {
            var range = $(this).get(0).createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    };

    $('.phone_num').click(function () {
        $(this).setCursorPosition(3);
    }).mask('+7(999)999-99-99');

    $('.passport_serial').click(function () {
        $(this).setCursorPosition(0);
    }).mask('9999');

    $('.passport_number').click(function () {
        $(this).setCursorPosition(0);
    }).mask('999999');

    $('.snils').click(function () {
        $(this).setCursorPosition(0);
    }).mask('999-999-999 99');

    $('.inn').click(function () {
        $(this).setCursorPosition(0);
    }).mask('999999999999');

    $('.account_number').click(function () {
        $(this).setCursorPosition(8);
    }).mask('40817810999999999999');

    $('.card_num').click(function () {
        $(this).setCursorPosition(0);
    }).mask('9999-9999-9999-9999');

    $('.card_month', '.card_year').click(function () {
        $(this).setCursorPosition(0);
    }).mask('99');

    $('.validity_period').click(function () {
        $(this).setCursorPosition(0);
    }).mask('99/99');

    $(document).on('change', '.js-lastname-input , .js-firstname-input , .js-patronymic-input', function () {

        let lastname = $('.js-lastname-input').val().replace(/ /g, '');
        let firstname = $('.js-firstname-input').val().replace(/ /g, '');
        let patronymic = $('.js-patronymic-input').val().replace(/ /g, '');

        if ($(this).hasClass('js-lastname-input'))
            $('input[name="requisite[holder][lastname]"]').val(lastname);

        if ($(this).hasClass('js-firstname-input'))
            $('input[name="requisite[holder][firstname]"]').val(firstname);

        if ($(this).hasClass('js-patronymic-input'))
            $('input[name="requisite[holder][patronymic]"]').val(patronymic);
    });

    $('.mask_number').each(function () {
        new Cleave(this, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            delimiter: ' ',
        });
    });

    $('.permission').on('change', function () {
        let permission = $(this).val();

        if (permission != 'none') {
            $('.groups').fadeIn();
            $('.groups').empty();
            $('.groups').append('<option value="none">Выберите из списка</option>');

            $.ajax({
                method: 'POST',
                data: {
                    action: 'get_groups',
                    permission: permission
                },
                success: function (resp) {
                    $('.groups').html(resp);
                }
            });
        } else {
            $('.groups').fadeOut();
            $('.groups').empty();
        }
    });

    $('.groups').on('change', function (e) {
        e.preventDefault();

        $('.my_company').empty();
        $('.my_company').append('<option value="none">Выберите из списка</option>');
        $('.branches').empty();
        $('.branches').append('<option value="none">Выберите из списка</option>');

        let group_id = $(this).val();
        let permission = $('.permission').val();

        if (group_id != 'none') {
            $.ajax({
                dataType: 'JSON',
                data: {
                    action: 'get_companies',
                    group_id: group_id,
                    permission: permission
                },
                success: function (resp) {
                    $('.my_company').show();

                    if ($('#pricelist').hasClass('slick-initialized')) {
                        $('.price').slick('unslick');
                    }

                    $('#pricelist').empty();

                    for (let key in resp['companies']) {

                        let blockedCard = '';

                        if (resp['companies'][key]['blocked'] == 1)
                            blockedCard = "class='badge-danger'";

                        $('.my_company').append('<option ' + blockedCard + 'value="' + resp['companies'][key]['id'] + '">' + resp['companies'][key]['name'] + '</option>')
                    }

                    for (let key in resp['loantypes']) {

                        let blockedCard = '';

                        if (resp['loantypes'][key]['online_flag'] == 0)
                            blockedCard = "style='background-color: indianred!important'";

                        $('#pricelist').append(
                            '<div class="price_container">' +
                            '<div ' + blockedCard + ' class="price_basic" data-loan-period="' + resp['loantypes'][key]['max_period'] + '"' +
                            ' data-loan="' + resp['loantypes'][key]['id'] + '"' +
                            ' data-min-amount="' + resp['loantypes'][key]['min_amount'] + '"' +
                            ' data-max-amount="' + resp['loantypes'][key]['max_amount'] + '" data-loan-percents=""' +
                            ' id="' + resp['loantypes'][key]['id'] + '"><br>' +
                            '<div class="height">' +
                            '<h4>' + resp['loantypes'][key]['name'] + '</h4>' +
                            '<h5>от <span' +
                            'class="sum">' + new Intl.NumberFormat().format(resp['loantypes'][key]['min_amount']) + '</span>' +
                            'Р до ' +
                            '<span class="sum">' + new Intl.NumberFormat().format(resp['loantypes'][key]['max_amount']) + '</span>' +
                            'Р</h5>' +
                            '</div>' +
                            '<hr style="width: 80%; size: 5px">' +
                            '<div class="out_profunion percents">' +
                            '<h6>' +
                            '<span class="loantype-percents">' + new Intl.NumberFormat().format(resp['loantypes'][key]['standart_percents']) + '</span>%' +
                            '<input type="hidden" class="percents"' +
                            ' value="' + resp['loantypes'][key]['standart_percents'] + '">' +
                            '</h6>' +
                            '<span>За каждый день использования микрозаймом</span>' +
                            '</div>' +
                            '<div class="in_profunion percents" style="display: none">' +
                            '<h6>' +
                            '<span class="loantype-percents-profunion">' + new Intl.NumberFormat().format(resp['loantypes'][key]['preferential_percents']) + '</span>%' +
                            '<input type="hidden" class="percents"' +
                            ' value="' + resp['loantypes'][key]['preferential_percents'] + '">' +
                            '</h6>' +
                            '<span>За каждый день использования микрозаймом</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        )
                    }

                    $('.price').slick({
                        infinite: false,
                        speed: 300,
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        dots: true,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3,
                                    infinite: true,
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                }
            });
        }
        else {
            $('.my_company').hide();
            $('.branches').hide();
            $('#pricelist').empty();
            slider.slick('reinit');
        }
    });

    $('.my_company').on('change', function (e) {
        e.preventDefault();

        $('.branches').empty();
        $('.branches').append('<option value="none">Выберите из списка</option>');

        let company_id = $(this).val();

        if (company_id != 'none') {
            $.ajax({
                dataType: 'JSON',
                data: {
                    action: 'get_branches',
                    company_id: company_id
                },
                success: function (resp) {
                    $('.branches').show();

                    for (let key in resp) {
                        $('.branches').append('<option value="' + resp[key]['id'] + '">' + resp[key]['name'] + '</option>')
                    }
                }
            });
        }
        else {
            $('.branches').hide();
        }
    });

    $('.add_to_credits_table').on('click', function (e) {
        e.preventDefault();

        let html = $(
            '<tr>' +
            '<td><input class="form-control" name="credits_bank_name[][credits_bank_name]" type="text" value=""></td>' +
            '<td><input class="form-control" name="credits_rest_sum[][credits_rest_sum]" type="text" value=""></td>' +
            '<td><input class="form-control" name="credits_month_pay[][credits_month_pay]" type="text" value=""></td>' +
            '<td><input class="form-control validity_period" name="credits_return_date[][credits_return_date]" type="text" value=""></td>' +
            '<td><input class="form-control" name="credits_percents[][credits_percents]" type="text" value=""></td>' +
            '<td><select class="form-control" name="credits_delay[][credits_delay]"><option value="Да">Да</option>' +
            '<option value="Нет" selected>Нет</option></select></td>' +
            '<td></td>' +
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
            '<td><input class="form-control" name="cards_bank_name[][cards_bank_name]" type="text" value=""></td>' +
            '<td><input class="form-control" name="cards_limit[][cards_limit]" type="text" value=""></td>' +
            '<td><input class="form-control" name="cards_rest_sum[][cards_rest_sum]" type="text" value=""></td>' +
            '<td><input class="form-control validity_period" name="cards_validity_period[][cards_validity_period]" type="text" value=""></td>' +
            '<td><select class="form-control" name="cards_delay[][cards_delay]"><option value="Да">Да</option>' +
            '<option value="Нет" selected>Нет</option></select></td>' +
            '<td></td>' +
            '</tr>');

        $('.validity_period').click(function () {
            $(this).setCursorPosition(0);
        }).mask('99/99');
    });

    $('.add_to_attestation_table').on('click', function () {

        let html =
            $('<tr>' +
                '<td><input class="form-control daterange" name="date[][date]" type="text" value=""></td>' +
                '<td><input class="form-control" name="comment[][comment]" type="text" value=""></td>' +
                '<td><div type="button" class="btn btn-outline-danger remove_from_attestation_table">-</div></td>' +
                '</tr>');

        $('.remove_from_attestation_table', html).on('click', function () {
            $(this).closest('tr').remove();
        });

        $('#attestation_table').append(html);

        moment.locale('ru');

        $('.daterange').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD.MM.YYYY'
            },
        });
    });

    $('.remove_from_attestation_table').on('click', function () {
        $(this).closest('tr').remove();
    });

    $('.phone_num').on('paste', function (e) {

        let phone_number = e.originalEvent.clipboardData.getData('text');

        phone_number = phone_number.match(/\d+/g).join([]);

        if (phone_number.length > 10) {
            final_number = phone_number.slice(-10);
            $(this).val(final_number);
        }
        else {
            phone_number = Number(phone_number);
            $(this).val(phone_number);
        }
        $(this).mask("+7(999)999-9999");
    });

    $('#no_attestation').on('click', function () {
        $('.attestation_table').toggle();
    });

    $('.check_users').on('click', function (e) {
        e.preventDefault();

        let lastname = $('input[name="lastname"]').val();
        let firstname = $('input[name="firstname"]').val();
        let patronymic = $('input[name="patronymic"]').val();
        let birth = $('input[name="birth"]').val();

        $.ajax({
            dataType: 'JSON',
            data: {
                action: 'check_same_users',
                lastname: lastname,
                firstname: firstname,
                patronymic: patronymic,
                birth: birth
            },
            success: function (users) {

                $('#users_same').empty();
                $('#users_same').fadeIn();

                if (users['empty']) {
                    let html =
                        '<label class="control-label" style="color: #880000">Совпадений не найдено</label>';

                    $('#users_same').append(html);

                    setTimeout(function () {
                        $('#users_same').fadeOut();
                        $('#users_same').empty();
                    }, 2000);
                } else {
                    for (let user in users) {
                        let html =
                            $('<label class="control-label">' + users[user]['personal_number'] + ' ' + users[user]['lastname'] + ' ' + users[user]['firstname'] + ' ' + users[user]['patronymic'] + ' ' + users[user]['birth'] + '</label>' +
                                '<input style="margin-left: 25px" type="button" class="btn btn-outline-warning choose_user" data-user="' + users[user]['id'] + '" value="Выбрать"><br><br>');

                        $('#users_same').append(html);
                    }
                }

                $('.choose_user').on('click', function () {
                    $('input[name="user_id"]').attr('value', $(this).attr('data-user'));

                    $('#users_same').empty();

                    let html =
                        '<label class="control-label" style="color: #00a300">Пользователь выбран</label>';

                    $('#users_same').append(html);

                    setTimeout(function () {
                        $('#users_same').fadeOut();
                        $('#users_same').empty();
                    }, 2000);
                });
            }
        });
        $('input[name="check_same_users"]').val('1');
    });

    $(document).on('click', '.download', function () {
        let sum = $('#order_sum').val();
        let loan_id = Number($('.to_form_loan').attr('data-loan'));
        let max_amount = Number($('.to_form_loan').attr('data-max-amount'));
        let min_amount = Number($('.to_form_loan').attr('data-min-amount'));

        sum = sum.replace(/[^0-9]/g, "");
        sum = Number(sum);

        if (parseInt(sum) >= parseInt(min_amount) && parseInt(sum) <= parseInt(max_amount)) {
            $('.alert-danger').hide();

            $('.buttons_append').show();


            let percents = $('.price_basic[id="' + loan_id + '"]').find('.percents:visible').find('input').val();
            let date_from = $('#start_date').val();
            let date_to = $('#end_date').val();
            let company_id = $('.my_company').val();
            let branch_id = $('.branches').val();
            let amount = $('#order_sum').val();

            $.ajax({
                method: 'POST',
                data: {
                    action: 'download_excell',
                    loan_id: loan_id,
                    date_from: date_from,
                    date_to: date_to,
                    branch_id: branch_id,
                    amount: amount,
                    percents: percents,
                    company_id: company_id
                },
                success: function (link) {
                    $(document).remove('.download');
                    $('.link-button').html(link);
                }
            });
        }
    })

});
