$("body").on('click', ".edit-product-button", function () {
    $('#edit_product_modal').modal('show');
    $.ajax({
        type: 'get',
        url: '/product/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            let url2 = '/product/save/' + response._id;
            let edit_form = $('#edit_product_form');
            edit_form.attr('action', url2);
            $('#edit_product_name').val(response.name);
            $('#edit_product_code').val(response.code);
            $('#edit_product_price').val(response.price);
            $('#edit_product_remain').val(response.remain);
            if (response.image) {
                let a = $('#edit_product_image2');
                a.show();
                a.val('File hiện tại: ' + response.image);
            } else {
                $('#edit_product_image2').hide();
            }
            $('#edit_product_image').prop('placeholder', response.image);
            $('#edit_product_user').select2("val", response.user_id);
            $('#edit_product_status').select2("val", response.status);

            old_state_form = new_state_form = edit_form.serialize();

            edit_form.change(function () {
                new_state_form = $(this).closest('form').serialize();
            });

        }
    })
});

$(".import-product-button").click(function () {
    $("#import_form").attr('action', '/product/import');

})

let product_table = $('.product-table').DataTable({
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
        { orderable: false, targets: 6 },
        { orderable: false, targets: 9 }
    ],
    searching: false
});
product_table.columns.adjust();


$(".sidebar-main-toggle").click(function (){
    product_table.columns.adjust();
});
