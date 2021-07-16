$(".admin-edit-user-button").click(function () {
    $('#edit_user_modal').modal('show');
    $.ajax({
        type: 'get',
        url: '/admin/user/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            let url2 = '/admin/user/save/' + response._id;
            let edit_form = $('#edit_user_form');
            edit_form.attr('action', url2);
            $('#edit_user_name').val(response.name);
            $('#edit_user_email').val(response.email);
            if (response.gender === "Nam") {
                $('#edit_user_gender_male').attr('checked', 'checked');
            } else if (response.gender === "Nữ") {
                $('#edit_user_gender_female').attr('checked', 'checked');
            } else if (response.gender === "Khác"){
                $('#edit_user_gender_other').attr('checked', 'checked');
            }
            $('#edit_user_phone').val(response.phone);
            if (response.avatar) {
                let a = $('#edit_user_avatar2');
                a.show();
                a.val('File hiện tại: ' + response.avatar);
            } else {
                $('#edit_user_avatar2').hide();
            }

            $('#edit_user_super_admin').select2("val", response.super_admin);
            $('#edit_user_role_customer').select2("val", response.role_id.customer);
            $('#edit_user_role_order').select2("val", response.role_id.order);
            $('#edit_user_role_company').select2("val", response.role_id.company);
            $('#edit_user_role_product').select2("val", response.role_id.product);

            edit_form.data('changed', 0);
            $("form :input").change(function () {
                $(this).closest('form').data('changed', 1);
            });

        }
    })
});

$('.show-role-modal-button').click(function () {
    let data = $(this).data('role');

    if (data.name) {
        $('#show_role_modal > .modal-dialog > .modal-content > .modal-header').html('<h5>Vai trò của người dùng '
            + data.name + '</h5>');
    } else {
        $('#show_role_modal > .modal-header').html('Vai trò của người dùng');
    }

    if(data.customer_role && data.customer_role.name) {
        $('#show_role_modal_customer').html(data.customer_role.name);
    } else {
        $('#show_role_modal_customer').html('');
    }

    if (data.company_role && data.company_role.name) {
        $('#show_role_modal_company').html(data.company_role.name);
    } else {
        $('#show_role_modal_company').html('');
    }

    if (data.order_role && data.order_role.name) {
        $('#show_role_modal_order').html(data.order_role.name);
    } else {
        $('#show_role_modal_order').html('');
    }

    if (data.product_role && data.product_role.name) {
        $('#show_role_modal_product').html(data.product_role.name);
    } else {
        $('#show_role_modal_product').html('');
    }
})

$('.user-table').dataTable({
    "scrollX": true,
    "scrollY": "calc(100vh - 325px)",
    "scrollCollapse": true,
    paging: false,
    info: false,
    "oLanguage": {
        "sEmptyTable": "Không có dữ liệu."
    },
    "searching": false,
    "order": []
});
