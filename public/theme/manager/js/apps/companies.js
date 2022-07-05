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

    $('.add-company-modal').on('click', function () {
        $('#add-company-modal').modal();
    });

    $('.add_settlement').on('click', function () {
        $('#add_settlement').modal();
    });

    $('.add_document').on('click', function () {
        $('#add_document').modal();
    });

    $('.action-block-company').on('change', function () {
        let val = $(this).val();
        let value = (val == 1) ? 0 : 1;
        let company_id = $(this).attr('data-company-id');
        let that = $(this);
        $.ajax({
            method: 'POST',
            data: {
                action: 'change_blocked_flag',
                company_id: company_id,
                value: value
            },
            success: function () {
                that.val(value);
            }
        });
    });

    $('.action-edit-company').on('click', function () {

        $('#modal_edit_company').modal();
    });

    $('.save').on('click', function (e) {
        e.preventDefault();

        let form = $('#edit_company_form').serialize();

        $.ajax({
            method: 'POST',
            data: form,
            success: function () {
                location.reload();
            }
        })

    });

    $('.action-delete-company').on('click', function (e) {
        e.preventDefault();

        let company_id = $(this).attr('data-company-id');

        $.ajax({
            method: 'POST',
            data: {
                action: 'delete_company',
                company_id: company_id
            },
            success: function (resp) {
                location.replace('/companies');
            }
        });
    });

    $('#add_branche_form').on('submit', function (e) {
        e.preventDefault();

        let form = $(this).serialize();
        $.ajax({
            method: 'POST',
            data: form,
            success: function () {
                location.reload();
            }
        })
    });

    $('.delete_branch').on('click', function (e) {
        e.preventDefault();

        let branches_id = $(this).attr('data-branch-id');

        $.ajax({
            method: 'POST',
            data: {
                action: 'delete_branche',
                branches_id: branches_id
            },
            success: function (resp) {
                location.reload();
            }
        });
    });

    $('#group_filter').on('change', function (e) {
        e.preventDefault();

        let group_number = $(this).val();

        if (group_number != 'none') {
            $('tr[class="companies"]').not('#' + group_number + '').hide();
            $('tr[class="companies"][id="' + group_number + '"]').show();
        }
        else
            $('tr[class="companies"]').show();

    });

    $('.edit_branch').on('click', function (e) {
        e.preventDefault();

        let branch_id = $(this).attr('data-branch-id');

        $.ajax({
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'get_branch',
                branch_id: branch_id
            },
            success: function (branch) {
                $('input[class="edit_branch_form"][name="branch_id"]').val(branch['id']);
                $('input[class="form-control edit_branch_form"][name="name"]').val(branch['name']);
                $('select[class="form-control edit_branch_form"] option[value="' + branch['payday'] + '"]').prop('selected', true);
                $('input[class="form-control edit_branch_form"][name="fio"]').val(branch['fio']);
                $('input[class="form-control edit_branch_form"][name="phone"]').val(branch['phone']);
            }
        });

        $('#edit_branch').modal();
    });

    $('.action_edit_branch').on('click', function (e) {
        e.preventDefault();

        let form = $('#edit_branch_form').serialize();

        $.ajax({
            method: 'POST',
            data: form,
            success: function () {
                location.reload();
            }
        });
    });

    $('.action_add_settlement').on('click', function (e) {
        e.preventDefault();

        let form = $('#add_settlement_form').serialize();

        $.ajax({
            method: 'POST',
            data: form,
            success: function () {
                location.reload();
            }
        });
    });

    $('.std_flag').on('change', function () {

        let settlement_id = $(this).val();

        $.ajax({
            method: 'POST',
            data: {
                action: 'change_std_flag',
                settlement_id: settlement_id
            }
        });
    });

    $('.update_settlement').on('click', function () {

        let settlement_id = $(this).attr('data-settlement');

        $.ajax({
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'get_settlement',
                settlement_id: settlement_id
            },
            success: function (settlement) {
                $('input[class="update_settlement_form"][name="settlement_id"]').val(settlement['id']);
                $('input[class="form-control update_settlement_form"][name="name"]').val(settlement['name']);
                $('input[class="form-control update_settlement_form"][name="payment"]').val(settlement['payment']);
                $('input[class="form-control update_settlement_form"][name="cors"]').val(settlement['cors']);
                $('input[class="form-control update_settlement_form"][name="bik"]').val(settlement['bik']);
            }
        });

        $('#update_settlement').modal();

        $('.action_update_settlement').on('click', function () {

            let form = $('#update_settlement_form').serialize();

            $.ajax({
                method: 'POST',
                data: form,
                success: function () {
                    location.reload();
                }
            });
        });
    });

    $('.delete_settlement').on('click', function () {

        let settlement_id = $(this).attr('data-settlement');

        $.ajax({
            method: 'POST',
            data: {
                action: 'delete_settlement',
                settlement_id: settlement_id
            },
            success: function () {
                location.reload();
            }
        });
    });

    $('#name_settlement').suggestions({
        token: token_dadata,
        type: "BANK",
        minChars: 3,
        onSelect: function (suggestion) {
            console.log(suggestion);
            $(this).val(suggestion.value);
            $('#correspondent_account').val(suggestion.data.correspondent_account);
            $('#bik_settlement').val(suggestion.data.bic);
        }
    });

    $('.action_add_doc').on('click', function () {

        let file = $('#doc')[0].files;
        let form_data = new FormData($('#add_document_form')[0]);
        form_data.append('file', file);

        $.ajax({
            method: 'POST',
            data: form_data,
            processData: false,
            contentType: false,
            success: function (resp) {
                location.reload();
            }
        })
    });

    $(document).on('click', '.wrong_info', function (e) {
        e.preventDefault();

        let company_id = $(this).attr('data-company');
        let group_id = $(this).attr('data-group');

        $.ajax({
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'wrong_info',
                company_id: company_id,
                group_id: group_id
            },
            success: function (resp) {
                if (resp['error']) {
                    Swal.fire({
                        title: resp['text'],
                        confirmButtonText: 'ОК'
                    });
                } else {
                    Swal.fire({
                        title: 'Тикет создан успешно',
                        confirmButtonText: 'ОК'
                    });
                }
            }
        });
    })
});