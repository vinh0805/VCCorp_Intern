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
