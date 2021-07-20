$("body").on('click', '.edit-order-button', function () {
    $('#edit_order_modal').modal('show');

    $.ajax({
        type: 'get',
        url: '/order/edit/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            let url2 = '/order/save/' + response._id;
            let edit_form = $('#edit_order_form');
            edit_form.attr('action', url2);
            if (response.customer_id) {
                $('#edit_order_customer').select2("val", response.customer_id);
            }
            if (response.company_id) {
                $('#edit_order_company').select2("val", response.company_id);
            }
            $('#edit_order_time').val(response.time);
            $('#edit_order_tax').val(response.tax);
            $('#edit_order_address').val(response.address);
            if (response.user_id) {
                $('#edit_order_user').select2("val", response.user_id);
            }
            $('#edit_order_status').select2("val", response.status);

            let $productList = $(".add-product-order-button").data('products');

            if (response.products.length > 0) {
                $('#edit_product_list').data('id', response.products.length);
                $('#edit_product_01').select2("val", response.products[0].product);
                $('#edit_product_number_01').val(response.products[0].number);
            }

            if (response.products.length > 1) {
                for (let $i = 2; $i < response.products.length + 1; $i++) {
                    let $appendContent = `
                        <div class="form-group add-product-list" data-id="` + $i + `" id="edit_product` + $i + `">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4 product-label">Sản phẩm:</label>
                                <div class="col-sm-8 select-product-order">
                                    <select class="select-search" data-placeholder="Chọn sản phẩm..."
                                            name="product[]" id="edit_product_` + $i + `">
                                        <option></option>`;

                    for (let $i2 = 0; $i2 < $productList.length; $i2++) {
                        if (response.products[$i - 1].product === $productList[$i2]._id) {
                            $appendContent += `
                                        <option value="` + $productList[$i2]._id + `" selected>` +
                                $productList[$i2].name + `</option>`;
                        } else {
                            $appendContent += `
                                        <option value="` + $productList[$i2]._id + `">` + $productList[$i2].name +
                                `</option>`;
                        }
                    }

                    $appendContent += `
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label col-sm-3">Số lượng</label>
                                <div class="col-sm-7">
                                    <input type='number' class="form-control" name="number[]" value="` +
                        response.products[$i - 1].number + `" min="1">
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button type="button" onclick="document.getElementById('edit_product` + $i + `').remove()"
                                            class="btn btn-danger sub-product-order-button">
                                        <i class="icon-subtract"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;

                    $("#edit_product_list2").append($appendContent);
                }
                $("select.select-search").select2();

            } else {
                $("#edit_product_list2").html("");
            }

            old_state_form = new_state_form = edit_form.serialize();

            edit_form.change(function () {
                new_state_form = $(this).closest('form').serialize();
            });

        }
    })
});

$(".add-product-order-button").click(function () {
    let $id = $(this).data('id') + 1;
    $(this).data('id', $id);

    let $productList = $(this).data('products');

    let $appendContent = `
        <div class="form-group add-product-list" data-id="` + $id + `" id="product` + $id + `">
            <div class="col-sm-6">
                <label class="control-label col-sm-4 product-label">Sản phẩm:</label>
                <div class="col-sm-8 select-product-order">
                    <select class="select-search" data-placeholder="Chọn sản phẩm..." name="product[]">
                        <option></option>`;

    for (let $i = 0; $i < $productList.length; $i++) {
        $appendContent += `
                    <option value="` + $productList[$i]._id + `">` + $productList[$i].name + `</option>`;
    }

    $appendContent += `
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label class="control-label col-sm-3">Số lượng</label>
                <div class="col-sm-7">
                    <input type='number' class="form-control" name="number[]" min="1" value="1">
                </div>
                <div class="col-sm-2 text-right">
                    <button type="button" onclick="document.getElementById('product` + $id + `').remove()"
                            class="btn btn-danger sub-product-order-button">
                        <i class="icon-subtract"></i>
                    </button>
                </div>
            </div>
        </div>`;

    $("#add_product_list2").append($appendContent);

    $("select.select-search").select2();
});

$(".add-product-order-button2").click(function () {
    let $id = $(this).data('id') + 1;
    $(this).data('id', $id);

    let $productList = $(this).data('products');

    let $appendContent = `
        <div class="form-group add-product-list" data-id="` + $id + `" id="product` + $id + `">
            <div class="col-sm-6">
                <label class="control-label col-sm-4 product-label">Sản phẩm:</label>
                <div class="col-sm-8 select-product-order">
                    <select class="select-search" data-placeholder="Chọn sản phẩm..." name="product[]">
                        <option></option>`;

    for (let $i = 0; $i < $productList.length; $i++) {
        $appendContent += `
                    <option value="` + $productList[$i]._id + `">` + $productList[$i].name + `</option>`;
    }

    $appendContent += `
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label class="control-label col-sm-3">Số lượng</label>
                <div class="col-sm-7">
                    <input type='number' class="form-control" name="number[]" min="1" value="1">
                </div>
                <div class="col-sm-2 text-right">
                    <button type="button" onclick="document.getElementById('product` + $id + `').remove()"
                            class="btn btn-danger sub-product-order-button">
                        <i class="icon-subtract"></i>
                    </button>
                </div>
            </div>
        </div>`;

    $("#edit_product_list2").append($appendContent);

    $("select.select-search").select2();
});

// $(".import-order-button").click(function () {
//     $("#import_form").attr('action', 'order/import');
//
//     $("#import_form_fields_list").html(`
//         <div class="col-sm-1">
//             <h5>ID</h5>
//             <input type="checkbox" id="field_id" name="field_id">
//             <label for="field_id">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Customer_id</h5>
//             <input type="checkbox" id="field_customer_id" name="field_customer_id">
//             <label for="field_customer_id">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Company_id</h5>
//             <input type="checkbox" id="field_company_id" name="field_company_id">
//             <label for="field_company_id">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Sản phẩm</h5>
//             <input type="checkbox" id="field_products" name="field_products">
//             <label for="field_products">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Giá</h5>
//             <input type="checkbox" id="field_price" name="field_price">
//             <label for="field_price">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Thuế</h5>
//             <input type="checkbox" id="field_tax" name="field_tax">
//             <label for="field_tax">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Tổng giá</h5>
//             <input type="checkbox" id="field_total_price" name="field_total_price">
//             <label for="field_total_price">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Thời gian</h5>
//             <input type="checkbox" id="field_time" name="field_time">
//             <label for="field_time">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Địa chỉ</h5>
//             <input type="checkbox" id="field_address" name="field_address">
//             <label for="field_address">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>User</h5>
//             <input type="checkbox" id="field_user_id" name="field_user_id">
//             <label for="field_user_id">Check trùng</label>
//         </div>
//         <div class="col-sm-1">
//             <h5>Status</h5>
//             <input type="checkbox" id="field_status" name="field_status">
//             <label for="field_status">Check trùng</label>
//         </div>
//     `)
// })

$('.order-table').dataTable({
    "scrollX": true,
    "scrollY": "calc(100vh - 325px)",
    "scrollCollapse": true,
    "paging": false,
    "info": false,
    "oLanguage": {
        "sEmptyTable": "Không có dữ liệu."
    },
    searching: false,
    "order": []
});
// $('.order-table').DataTable({
//     processing: true,
//     serverSide: true,
//     ajax: {
//         url: 'orders/get-data',
//         dataType: 'json',
//         type: 'GET'
//     },
//     columns:[
//         {
//             data: 'id',
//         },
//         {
//             data: 'customer',
//             render: function (data, type, full, meta) {
//                 if (data && data.name) {
//                     return `<a href="javascript:void(0)" class="customer-modal" data-customer='` + JSON.stringify(data) + `'
//                                data-toggle="modal"
//                                data-target="#customer_modal" data-title="` + data.name + `">` + data.name +
//                         `</a>`;
//                 }
//                 return null;
//             }
//
//         },
//         {
//             data: 'company',
//             render: function (data, type, full, meta) {
//                 if (data && data.name) {
//                     return `<a href="javascript:void(0)" class="company-modal" data-company='` + JSON.stringify(data) + `'
//                                data-toggle="modal"
//                                data-target="#company_modal" data-title="` + data.name + `">` + data.name +
//                         `</a>`;
//                 }
//                 return null;
//             }
//         },
//         {
//             data: 'products',
//             render: function (data, type, full, meta) {
//                 if (data) {
//                     return `<a href="javascript:void(0)" class="products-modal" data-id="` + full._id + `">` + data.length +
//                         ` sản phẩm</a>`;
//                 }
//                 return null;
//             }
//         },
//         {
//             data: 'price',
//             render: function (data, type, full, meta) {
//                 return new Intl.NumberFormat().format(data);
//             }
//         },
//         {
//             data: 'tax'
//         },
//         {
//             data: 'total_price',
//             render: function (data, type, full, meta) {
//                 return new Intl.NumberFormat().format(data);
//             }
//         },
//         {
//             data: 'time'
//         },
//         {
//             data: 'address'
//         },
//         {
//             data: 'user',
//             orderable: false
//         },
//         {
//             data: 'status',
//             orderable: false,
//             render: function (data, type, full, meta) {
//                 if (data && data === "Đã hoàn thành") {
//                     return '<span class="label label-success">' + data + '</span>';
//                 } else if (data && data === "Chưa hoàn thành") {
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
//                                             <a href="javascript:void(0)" class="edit-order-button" data-id="` + data + `">
//                                                 <i class="icon-pencil"></i> Sửa thông tin
//                                             </a>
//                                         </li>`;
//                 }
//
//                 if (full.delete_permission) {
//                     html +=            `<li>
//                                             <a href="` + url + `/user/order/delete/` + data + `"
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
