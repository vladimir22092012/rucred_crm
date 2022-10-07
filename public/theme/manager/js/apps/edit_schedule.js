$(function () {
    $('.restructure_od').on('change', function () {

        let loan_od = 0;
        let period = $('.rest_sum').length;

        $('.restructure_od').each(function () {
            let val = $(this).val();
            val = val.replace(' ', '');
            val = val.replace(' ', '');
            val = val.replace(',', '.');

            loan_od = loan_od + parseFloat(val);
        });

        let sum = loan_od.toFixed(2);

        let loan_amount = $('#loan_amount').val();

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
});