let body = $('body')

// Delete role
body.on('click', '.delete-role', function () {
    let _id = $(this).data('id');
    if (confirm('Bạn có muốn xóa nhóm quyền này không?')) {
        $.ajax({
            type: 'post',
            url: 'role/delete/' + _id,
            dataType: 'json',

            success: function (response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            }
        })
    }
})

$("body").on('click', '.edit-role-button', function () {
    $('#edit_role_modal').modal('show');

    $.ajax({
        type: 'get',
        url: 'role/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            if (response.message) {
                alert(response.message)
            } else {
                let url = 'role/save/' + response._id;
                let edit_form = $('#edit_role_form');
                edit_form.attr('action', url);
                $('#edit_role_name').val(response.name);

                $('.edit-permission').each(function () {
                    if (response.permission_list.includes($(this).data('role'))) {
                        $(this).prop('checked', true);
                    }
                    $(".styled").uniform({
                        radioClass: 'choice'
                    });
                })

                old_state_form = new_state_form = edit_form.serialize();

                edit_form.change(function () {
                    new_state_form = $(this).closest('form').serialize();
                });
            }
        }
    })
});
