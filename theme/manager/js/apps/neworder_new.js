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

    $('.to_form_loan').on('click', function (e) {

        let sum = $('#order_sum').val();
        let loan_id = $(this).attr('data-loan');
        let period = $(this).attr('data-loan-period');
        let max_amount = $(this).attr('data-max-amount');
        let min_amount = $(this).attr('data-min-amount');

        sum = sum.replace(/[^0-9]/g, "");


        if (sum.length < 0 || sum == 0) {

            $('.alert-danger').fadeIn();
            $('.alert-danger').html('<span>Вы не ввели сумму займа</span>');
            $('.alert-danger').fadeOut(3000);
        }
        else {

            if (parseInt(sum) >= parseInt(min_amount) && parseInt(sum) <= parseInt(max_amount)) {
                $('.alert-danger').hide();


                let percents = $('.price_basic[id="' + loan_id + '"]').find('.percents:visible').find('input').val();

                let percent_per_month = ((percents / 100) * 365) / 12;

                let sum_to_pay = sum * (percent_per_month / (1 - Math.pow((1 + percent_per_month), -period)));

                if (parseInt(loan_id) == 1) {
                    let date_from = new Date($('#start_date').val());
                    let date_to = new Date($('#end_date').val());
                    date_to.setDate(10);

                    if (date_from.getDate() >= 1 && date_from.getDate() < date_to.getDate()) {
                        let percent_sum = (10 - date_from.getDate()) * (percents / 100) * sum;
                        sum_to_pay += percent_sum;
                    }
                }

                sum_to_pay = sum_to_pay.toFixed(2);
                sum_to_pay = new Intl.NumberFormat("ru").format(sum_to_pay);

                $('#final_sum').val(sum_to_pay);
            }
            else {
                $('.alert-danger').fadeIn();
                $('.alert-danger').html('<span>Проверьте правильность суммы</span>');
                $('.alert-danger').fadeOut(3000);
            }
        }
    });

    $('.price_basic').on('click', function (e) {
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
                loan_id: loan_id
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
        $('.want_profunion').removeAttr('required');
    });

    $('#profunion2').on('click', function () {
        $('#profunion_toggle').show();
        $('.in_profunion').hide();
        $('.out_profunion').show();
        $('.want_profunion').attr('required', true);
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

    $('.bik').click(function () {
        $(this).setCursorPosition(0);
    }).mask('999999999');

    $('.account_number').click(function () {
        $(this).setCursorPosition(8);
    }).mask('40817810999999999999');

    $('.card_num').click(function () {
        $(this).setCursorPosition(0);
    }).mask('9999-9999-9999-9999');

    $('.card_month', '.card_year').click(function () {
        $(this).setCursorPosition(0);
    }).mask('99');

    $('.js-lastname-input , .js-firstname-input , .js-patronymic-input').on('change', function () {

        let lastname = $('.js-lastname-input').val();
        let firstname = $('.js-firstname-input').val();
        let patronymic = $('.js-patronymic-input').val();
        lastname = (lastname) ? lastname + ' ' : '';
        firstname = (firstname) ? firstname + ' ' : '';
        patronymic = (patronymic) ? patronymic + ' ' : '';

        let fio = lastname + firstname + patronymic;

        $('input[name="fio_acc_holder"]').val(fio);
    });

    $('.mask_number').each(function () {
        new Cleave(this, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            delimiter: ' ',
        });
    });
});