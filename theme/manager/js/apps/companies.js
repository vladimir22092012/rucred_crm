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
                console.log(branch);

                $('input[class="edit_branch_form"][name="branch_id"]').val(branch['id']);
                $('input[class="form-control edit_branch_form"][name="name"]').val(branch['name']);
                $('select[class="form-control edit_branch_form"] option[value="'+branch['payday']+'"]').prop('selected', true);
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
    })
});