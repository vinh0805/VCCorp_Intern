$("body").on('click', '.edit-customer-button', function () {
    $('#edit_customer_modal').modal('show');

    $.ajax({
        type: 'get',
        url: '/customer/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            if (response.message) {
                alert(response.message)
            } else {
                let url2 = '/customer/save/' + response._id;
                let edit_form = $('#edit_customer_form');
                edit_form.attr('action', url2);
                $('#edit_customer_name').val(response.name);
                $('#edit_customer_birth').daterangepicker({
                    singleDatePicker: true,
                    maxDate: new Date()
                }).val(response.birth);
                if (response.gender === "Nam") {
                    $("#edit_customer_gender_male").prop('checked', true);
                } else if (response.gender === "Nữ") {
                    $('#edit_customer_gender_female').prop('checked', 'checked');
                } else if (response.gender === "Khác"){
                    $('#edit_customer_gender_other').prop('checked', 'checked');
                }
                $('#edit_customer_job').val(response.job);
                $('#edit_customer_address').val(response.address);
                $('#edit_customer_email').val(response.email);
                $('#edit_customer_phone').val(response.phone);
                $('#edit_customer_company').select2("val", response.company_id);
                $('#edit_customer_user').select2("val", response.user_id);
                $('#edit_customer_status').select2("val", response.status);

                old_state_form = new_state_form = edit_form.serialize();

                edit_form.change(function () {
                    new_state_form = $(this).closest('form').serialize();
                });
            }
        }
    })
});

$(".import-customer-button").click(function () {
    $("#import_form").attr('action', '/customer/import');
})

let a = $('.customer-table').DataTable({
    "scrollX": true,
    "scrollY": "calc(100vh - 325px)",
    "scrollCollapse": true,
    paging: false,
    info: false,
    searching: false,
    "oLanguage": {
        "sEmptyTable": "Không có dữ liệu."
    },
});
