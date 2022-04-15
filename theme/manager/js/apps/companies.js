$(function () {

    $('.add-company-modal').on('click', function () {

        $('#modal_add_item').modal();
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

    $('.add_branche').on('click', function (e) {
        e.preventDefault();

        let form = $('#add_branche_form').serialize();

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

        if(group_number != 'none')
        {
            $('tr[class="companies"]').not('#'+group_number+'').hide();
            $('tr[class="companies"][id="'+group_number+'"]').show();
        }
        else
            $('tr[class="companies"]').show();

    });
});