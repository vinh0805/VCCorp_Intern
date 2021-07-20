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


// $('.product-table').DataTable({
//     processing: true,
//     serverSide: true,
//     ajax: {
//         url: 'products/get-data',
//         dataType: 'json',
//         type: 'GET'
//     },
//     columns:[
//         {
//             data: 'id',
//         },
//         {
//             data: 'name',
//             orderable: false
//         },
//         {
//             data: 'code',
//             orderable: false
//         },
//         {
//             data: 'price',
//             render: function (data, type, full, meta) {
//                 return new Intl.NumberFormat().format(data);
//             }
//         },
//         {
//             data: 'remain'
//         },
//         {
//             data: 'image',
//             orderable: false,
//             render: function (data, type, full, meta) {
//                 if (data) {
//                     let url = $('#url').val();
//                     return  "<img src='" + url + "/storage/images/" + data + "' class='product-image'/>";
//                 } else return null;
//             }
//         },
//         {
//             data: 'user',
//             orderable: false
//         },
//         {
//             data: 'status',
//             orderable: false,
//             render: function (data, type, full, meta) {
//                 if (data && data === "Có sẵn") {
//                     return '<span class="label label-success">' + data + '</span>';
//                 } else if (data && data === "Không có sẵn") {
//                     return '<span class="label label-default">' + data + '</span>';
//                 } else {
//                     return null;
//                 }
//             }
//         },
//         {
//             data: 'option',
//             orderable: false,
//             render: function (data, type, full, meta) {
//                 if (!full.edit_permission && !full.delete_permission && !full.set_permission) {
//                     return null;
//                 }
//
//                 let url = $('#url').val();
//                 let html = `<td class="text-center">
//                                 <ul class="icons-list">
//                                     <li class="dropdown">
//                                         <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
//                                             <i class="icon-menu9"></i>
//                                         </a>
//
//                                         <ul class="dropdown-menu dropdown-menu-right">`;
//
//                 if (full.edit_permission) {
//                     html +=            `<li>
//                                             <a href="javascript:void(0)" class="edit-product-button" data-id="` + data + `">
//                                                 <i class="icon-pencil"></i> Sửa thông tin
//                                             </a>
//                                         </li>`;
//                 }
//
//                 if (full.delete_permission) {
//                     html +=            `<li>
//                                             <a href="` + url + `/user/product/delete/` + data + `"
//                                             onclick="return confirm(\'Bạn có muốn xóa sản phẩm này không?\')">
//                                                 <i class="icon-folder-remove"></i> Xóa
//                                             </a>
//                                         </li>`;
//                 }
//
//                 if (full.set_permission) {
//                     html +=            `<li>
//                                             <a href="#" class="set_permission_button" data-toggle="modal"
//                                                data-target="#set_permission_modal" data-id="` + data + `">
//                                                 <i class="icon-cog2"></i> Phân quyền
//                                             </a>
//                                         </li>`;
//                 }
//
//                 html +=             `</ul>
//                                 </li>
//                             </ul>
//                         </td>`;
//
//                 return html;
//             }
//         }
//     ]
// });
