$("body").on('click', '.edit-company-button', function () {
    $('#edit_company_modal').modal('show');
    $.ajax({
        type: 'get',
        url: '/company/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            let url2 = '/company/save/' + response._id;
            let edit_form = $('#edit_company_form');
            edit_form.attr('action', url2);
            $('#edit_company_name').val(response.name);
            $('#edit_company_code').val(response.code);
            $('#edit_company_field').val(response.field);
            $('#edit_company_address').val(response.address);
            $('#edit_company_email').val(response.email);
            $('#edit_company_phone').val(response.phone);
            if (response.user_id) {
                $('#edit_company_user').select2("val", response.user_id);
            }
            $('#edit_company_status').select2("val", response.status);

            edit_form.data('changed', 0);
            $("form :input").change(function () {
                $(this).closest('form').data('changed', 1);
            });
        }
    })
});

$(".import-company-button").click(function () {
    $('#import_collection').val('company');
    $('#import_form').prop('action', '/company/import');

})

$('.company-table').dataTable({
    "scrollX": true,
    "scrollY": "calc(100vh - 325px)",
    "scrollCollapse": true,
    "paging": false,
    "info": false,
    "oLanguage": {
        "sEmptyTable": "Không có dữ liệu."
    },
    columnDefs: [
        { orderable: false, targets: 0 },
        { orderable: false, targets: 9 },
        { orderable: false, targets: 10 }
    ],
    searching: false
});

$('body').on('click', '.set_permission_button', function () {
    let url = $('#url').val() + '/set-permission/company/' + $(this).data('id');
    $('#set_permission_form').attr('action', url);
})


