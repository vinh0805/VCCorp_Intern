let body = $('body');
$('.modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
    $('.stepy-navigator').show();
});

// Left Menu
let menuStateKey = "menu.toggle_nav.state";
$(document).ready(function () {
    let setInitialMenuState = function () {
        // Note: This retrieves a String, not a Boolean.
        let state = localStorage.getItem(menuStateKey) === 'true';
        setNavDisplayState(state);
    };

    let toggleNav = function () {
        let state = $(body).attr('class') === 'pace-done sidebar-xs';
        localStorage.setItem(menuStateKey, state);
        setNavDisplayState(state);
    };

    let setNavDisplayState = function (state) {
        if (state) {
            $(body).attr('class', 'pace-done sidebar-xs')
        } else {
            $(body).attr('class', 'pace-done')
        }
    };

    $(".sidebar-main-toggle").click(toggleNav);

    setInitialMenuState();
});

// Avoid double click form submit
$(document).ready(function () {
    $('button[type=submit]:not(.delete-button)').one('click', function (e) {
        e.preventDefault();
        $(this).prop("disabled", true);
        if ($(this).parent().parent().parent().parent().is('form')) {   // Submit change info user
            $(this).parent().parent().parent().parent().submit();
        } else if ($(this).parent().parent().is('form')) {
            $(this).parent().parent().submit();
        } else if ($(this).parent().is('form')) {
            $(this).parent().submit();
        }

        setTimeout(() => {
            $(this).prop("disabled", false);
        }, 2000)
    });
});

// Show modal
body.on('click', '.company-modal', function () {
    let $company = $(this).data('company');
    $('.company-modal-title').text($company['name']);
    $('#company_id_modal').text($company['id']);
    $('#company_name_modal').text($company['name']);
    $('#company_code_modal').text($company['code']);
    $('#company_field_modal').text($company['field']);
    $('#company_address_modal').text($company['address']);
    $('#company_email_modal').text($company['email']);
    $('#company_phone_modal').text($company['phone']);
    $('#company_status_modal').text($company['status']);
})

body.on('click', '.products-modal', function () {
    $('#products_modal').modal('show');

    $.ajax({
        type: 'get',
        url: '/order/products/' + $(this).data('id'),
        dataType: 'json',

        success: function (response) {
            let products_modal_content = $('#products_modal_content');
            products_modal_content.html("");
            let $content = '';
            for (let i = 0; i < response.length; i++) {
                $content += `
                    <tr>
                        <td>` + response[i].name + `</td>
                        <td>` + response[i].code + `</td>
                        <td>` + new Intl.NumberFormat().format(response[i].price) + `</td>`;

                if (response[i].image) {
                    $content += `<td><img alt="" class="product-image" src="` + base_path + `/storage/images/` +
                        response[i].image + `"></td>`;
                } else {
                    $content += `<td><img alt="" class="product-image" src="` + base_path + `/storage/images/default.png"></td>`;
                }

                $content += `
                        <td>` + response[i].remain + `</td>
                        <td>` + response[i].number + `</td>
                    </tr>
                `;
            }
            products_modal_content.append($content);
        }
    })
})

body.on('click', '.customer-modal', function () {
    let $customer = $(this).data('customer');
    $('.customer-modal-title').text($customer['name']);
    $('#customer_id_modal').text($customer['id']);
    $('#customer_name_modal').text($customer['name']);
    $('#customer_birth_modal').text($customer['birth']);
    $('#customer_gender_modal').text($customer['gender']);
    $('#customer_job_modal').text($customer['job']);
    $('#customer_address_modal').text($customer['address']);
    $('#customer_email_modal').text($customer['email']);
    $('#customer_phone_modal').text($customer['phone']);
    $('#customer_company_modal').text($customer['company_name']);
    $('#customer_status_modal').text($customer['status']);
})

// Confirm when close add/edit modal
$('.check-form-change').click(function () {
    let form = $($(this).data('target')).find('form');
    old_state_form = form.serialize();

    form.change(function () {
        new_state_form = $(this).closest('form').serialize();
    });
    new_state_form = null;
})
$('.close-modal').click(function () {
    if (old_state_form !== new_state_form && new_state_form != null) {
        if (confirm('Lưu ý: dữ liệu bạn nhập sẽ không được lưu.\nBạn có chắc chắn muốn đóng không?')) {
            $('button.close-modal2').click();
            new_state_form = null;
        }
    } else {
        $('button.close-modal2').click();
        new_state_form = null;
    }
})

// Select2
$("div.select-search").select2();

$(document).ready(function () {
    $('select.select-search').select2();
});

// Debugbar
// body.on('xhr.dt', function (e, settings, data, xhr) {
//     if (typeof phpdebugbar != "undefined") {
//         phpdebugbar.ajaxHandler.handle(xhr);
//     }
// });

// Create
$(".add-form").submit(function (e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this);
    let url = form.attr('action');

    // check if the input is valid using a 'valid' property
    if (form.valid()) {
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData: false,        // To send DOMDocument or non processed data file it is set to false
            success: function (data) {
                if (data.confirm) {
                    if (confirm(data.message)) {
                        form.children('input.check-confirm').val('1');
                        form.submit();
                    }
                    return;
                }
                alert(data.message); // show response from the php script.
                if (data.success) {
                    location.reload();
                }
            }
        });
    }
});

// Search
$(".search-form > button[type=button]").click(function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this).parent('form');

    let search_value = $('.search-value').val();
    if (!search_value) {
        alert('Chưa nhập dữ liệu để tìm kiếm!');
        return false;
    } else {
        form.submit();
    }
});

// Update
$(".edit-form").submit(function (e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this);
    let url = form.attr('action');

    if (!$(this).valid()) {
        return false;
    }

    $.ajax({
        type: "POST",
        url: url,
        data: new FormData(this),
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData: false,        // To send DOMDocument or non processed data file it is set to false
        success: function (data) {
            if (data.confirm) {
                if (confirm(data.message)) {
                    form.children('input.check-confirm').val('1');
                    form.submit();
                }
                return;
            }
            alert(data.message); // show response from the php script.
            if (data.success) {
                location.reload();
            }
        }
    });
});

$("#info_form").submit(function (e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this);
    let url = form.attr('action');

    if (!form.valid()) {
        return false;
    }

    $.ajax({
        type: "POST",
        url: url,
        data: new FormData(this),
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData: false,        // To send DOMDocument or non processed data file it is set to false
        success: function (data) {
            alert(data.message); // show response from the php script.
            if (data.success) {
                location.reload();
            }
        }
    });
});


// Delete
body.on('click', '.delete-button', function () {
    let collection = $(this).data('collection');
    let _id = $(this).data('id');
    if (confirm('Bạn có muốn xóa bản ghi này không?')) {
        $.ajax({
            type: 'post',
            url: '/' + collection + '/delete/' + _id,
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


// Delete all
body.on('click', '.delete-all-button', function () {
    let collection = $(this).data('collection');
    if (selectedList.length < 1) {
        return alert('Chưa chọn bản ghi nào!');
    }
    if (confirm('Bạn có muốn xóa ' + selectedList.length + ' bản ghi đã chọn không?')) {
        $.ajax({
            type: 'post',
            url: '/' + collection + '/delete-all',
            data: {'idList': selectedList},
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


// Delete on import
body.on('click', '.remove-review-data', function (e) {
    let _id = $(this).parent().parent('tr').data('id');
    if (confirm('Bạn có muốn xóa bản ghi này không?')) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: '/review-data/delete/' + _id,
            dataType: 'json',

            success: function (response) {
                alert(response.message);
                if (response.success) {
                    table2
                        .row($('tr#tmp_' + _id))
                        .remove()
                        .draw();
                }
            }
        })
    }
})


// Import =============================//

// Hidden
body.on('hidden.bs.modal', function () {
    document.getElementsByClassName('button-back')[0].click();
    document.getElementsByClassName('button-back')[3].click();
    $('#import_form_datatable2').html('');
    $('#import_form_datatable3').html('');
    $('.reload-page-button').hide();
    // Single picker
    $('.daterange-single').daterangepicker({
        singleDatePicker: true,
    }).val('');
    validate_form.resetForm();
});

$(document).ready(function () {
    $('.daterange-single').daterangepicker({
        singleDatePicker: true,
    }).val('');

})

// Loading...
$(document).on({
    ajaxStart: function () {
        body.addClass("loading");
    },
    ajaxStop: function () {
        body.removeClass("loading");
    }
});

// Validate type of file xlsx
let validate = false;
$(document).ready(function () {
    $("#import_file").change(function () {
        let fileExtension = ['xlsx'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) === -1) {
            alert("Chỉ chấp nhận đuôi file : " + fileExtension.join(', '));
            validate = false;
        } else {
            $('.button-next').prop('disabled', false);
            validate = true;
        }
    });

    $('#import_form > fieldset > div.stepy-navigator > a.button-next').click(function () {
        let import_file = $("#import_file");
        let fileExtension = ['xlsx'];

        if (!import_file.val()) {
            alert("Chưa chọn file!");
            validate = false;
        } else if ($.inArray(import_file.val().split('.').pop().toLowerCase(), fileExtension) === -1) {
            alert("Chỉ chấp nhận đuôi file : " + fileExtension.join(', '));
            validate = false;
        }
    })
})

// Set stepy import form
let import_form = $("#import_form").stepy({
    validate: true,
    block: true,
    labels:
        {
            next: "Tiếp",
            back: "Trước",
            finish: "Nhập",
        },
    saveState: false,
    next: function (index) {
        if (!validate) {
            return false
        }
        $('#step_import').val(index);
        if (index === 2) {
            $('#import_form_datatable2').html('');

            $('#import_form').trigger("submit");

        } else if (index === 3) {
            $('#import_form_datatable3').html('');

            $('#import_form').trigger("submit");

        } else if (index === 4) {
            $('#default_fields').val(default_fields);
            $('#old_fields').val(old_fields);
            $('#new_fields').val(new_fields);

            $('#import_form').trigger("submit");
        }

    },
    back: function (index) {
        if (!validate) {
            return false
        }
        $('#step_import').val(index);
    },
    select: function (index) {

    }
})
$('#import_form').submit(function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.

    let form = $(this);
    let url = form.attr('action');
    let html = '';

    $.ajax({
        type: "POST",
        url: url,
        data: new FormData(this),
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData: false,       // To send DOMDocument or non processed data file it is set to false
        success: function (data) {
            if (data.message) {
                $('button.submit-import-form').prop('disabled', true);
                alert(data.message); // show response from the php script.
                if (data.success) {
                    location.reload();
                } else {
                    document.getElementsByClassName('button-back')[data.step - 2].click()
                }
            } else {
                if (data[1] === '2') {
                    html = data[0];

                    $('#import_form_datatable2').html(html);
                    table = $('.import-table').DataTable({
                        "scrollX": true,
                        // "scrollY": "295px",
                        "scrollY": "calc(100vh - 350px)",
                        "scrollCollapse": true,
                        "pageLength": 10,
                        "lengthChange": false,
                        "searching": false,
                        "ordering": false,
                        "paging": false,
                        "info": false,
                        "oLanguage": {
                            "sEmptyTable": "Không có dữ liệu."
                        }
                    });
                    $('select.select-search').select2();
                    table.columns.adjust();
                    $('#import_form_random_key').val(data[2]);

                } else if (data[1] === '3') {
                    html = data[0];

                    $('#import_form_datatable3').html(html);
                    table2 = $('.import-table2').DataTable({
                        "scrollX": true,
                        // "scrollY": "250px",
                        "scrollY": "calc(100vh - 397px)",
                        "scrollCollapse": true,
                        "pageLength": 10,
                        "lengthChange": false,
                        "searching": false,
                        "ordering": false,
                        "paging": false,
                        "info": false,
                        "oLanguage": {
                            "sEmptyTable": "Không có dữ liệu."
                        }
                    });
                    $('select.select-search').select2();

                    $('#import_collection').val(data[2]);
                    $('#import_form_random_key').val(data[3]);
                    $('#fields_in_db_input').val(data[4]);

                } else if (data[1] === '4') {
                    $('#import_form_datatable4').html(data[0]);
                    $('.stepy-navigator').hide();
                    $('.reload-page-button').show();
                }
            }
        }
    });
})

// Pagination of import view
$('#import_form_datatable3').on('click', 'a.page-link', function (event) {
    event.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    fetch_data(page);
});

function fetch_data(page) {
    let step_import = $('#step_import').val();
    let import_form_random_key = $('#import_form_random_key').val();
    let collection = $('#import_collection').val();
    let fields_in_db = $("#fields_in_db_input").val();
    let default_fields = $('#default_fields').val();
    let old_fields = $('#old_fields').val();
    let new_fields = $('#new_fields').val();

    $.ajax({
        url: "/import2?page=" + page,
        type: 'get',
        data: {
            step_import: step_import,
            import_form_random_key: import_form_random_key,
            collection: collection,
            fields_in_db: fields_in_db,
            default_fields: default_fields,
            old_fields: old_fields,
            new_fields: new_fields
        },
        success: function (data) {
            let html = data[0];

            $('#import_form_datatable3').html(html);
            table2 = $('.import-table2').DataTable({
                "scrollX": true,
                // "scrollY": "250px",
                "scrollY": "calc(100vh - 397px)",
                "scrollCollapse": true,
                "pageLength": 10,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "paging": false,
                "info": false,
                "oLanguage": {
                    "sEmptyTable": "Không có dữ liệu."
                }
            });
            $('select.select-search').select2();
        }
    });
}

// Set data type for data in import view
body.on('click', '.set-data-type', function () {
    let field = $(this).data('field');
    let type = $(this).data('type');
    $('a.set-data-type2[data-field=' + field + '][data-type=' + type + ']').each(function () {
        $(this).click();
    });
    if (type === 'default') {
        default_fields.push(field);
        old_fields = old_fields.filter(item => item !== field);
        new_fields = new_fields.filter(item => item !== field);
    } else if (type === 'new') {
        new_fields.push(field);
        old_fields = old_fields.filter(item => item !== field);
        default_fields = default_fields.filter(item => item !== field);
    } else if (type === 'old') {
        old_fields.push(field);
        default_fields = default_fields.filter(item => item !== field);
        new_fields = new_fields.filter(item => item !== field);
    }
    default_fields = removeDuplicates(default_fields);
    new_fields = removeDuplicates(new_fields);
    old_fields = removeDuplicates(old_fields);

    $('#default_fields').val(default_fields);
    $('#old_fields').val(old_fields);
    $('#new_fields').val(new_fields);
})

function removeDuplicates(array) {
    return array.filter((a, b) => array.indexOf(a) === b)
}

body.on('click', '.set-data-type2', function () {
    $(this).parent().parent().siblings('a').html($(this).data('value') + ` <span class="caret"></span>`);
    $(this).parent().parent().parent().siblings('input').val($(this).data('type'));
})

$('#submitBtn').click(function () {
    let stepInfo = $('#import_form').formwizard('state');
    for (let i = 0; i < stepInfo.activatedSteps.length; i++) {
        stepInfo.steps.filter("#" + stepInfo.activatedSteps[i]).find(":input").not(".wizard-ignore").removeAttr("disabled");
    }
});
// End Import ==========================//

// Validate
$().ready(function () {
    validate_form = $('.validate-form').validate({
        ignore: [],
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            email: {
                maxlength: 50,
                email: true
            },
            phone: {
                minlength: 8,
                maxlength: 14,
                regex: /^([0]|[8][4]|[+][8][4])[1-9 ][0-9 ]*$/
            },
            address: {
                minlength: 3,
                maxlength: 100,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ \-,./\s]+)$/i
            },
            field: {
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            job: {
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            // birth: {
            //     number: true,
            //     min: 1900,
            //     max: 2021
            // },
            tax: {
                number: true,
                min: 0,
                max: 1000
            },
            product: {
                required: true
            },
            number: {
                number: true,
                min: 1
            },
            price: {
                required: true,
                number: true,
                min: 1000
            },
            remain: {
                required: true,
                number: true,
                min: 1
            }
        }
    })
    $('.validate-form2').validate({
        ignore: [],
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            email: {
                maxlength: 50,
                email: true,
                required: true
            },
            phone: {
                minlength: 8,
                maxlength: 14,
                regex: /^([0]|[8][4]|[+][8][4])[1-9 ][0-9 ]*$/
            },
            address: {
                minlength: 3,
                maxlength: 100,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ \-,./\s]+)$/i
            },
            field: {
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            job: {
                minlength: 3,
                maxlength: 50,
                regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
            },
            // birth: {
            //     number: true,
            //     min: 1900,
            //     max: 2021
            // },
            tax: {
                number: true,
                min: 0,
                max: 1000
            },
            product: {
                required: true
            },
            number: {
                number: true,
                min: 1
            },
            price: {
                required: true,
                number: true,
                min: 1000
            },
            remain: {
                required: true,
                number: true,
                min: 1
            }
        }
    })
    // Prevent submit form by enter
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });
})

jQuery.extend(jQuery.validator.messages, {
    required: "Bạn cần phải nhập trường này!",
    email: "Hãy nhập đúng định dạng email!",
    date: "Hãy nhập đúng định dạng ngày!",
    number: "Hãy nhập đúng định dạng số!",
    digits: "Hãy nhập đúng định dạng số!",
    maxlength: jQuery.validator.format("Tối đa {0} ký tự."),
    minlength: jQuery.validator.format("Ít nhất {0} ký tự."),
    max: jQuery.validator.format("Nhỏ hơn hoặc bằng {0}."),
    min: jQuery.validator.format("Lớn hơn hoặc bằng {0}.")
});

$.validator.addMethod(
    "regex",
    function (value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    },
    "Sai định dạng dữ liệu!"
);


// Disable other select
$("select").each(function () {
    $(this).data('stored-value', $(this).val());
});

body.on('change', 'select.field-in-db', function () {
    let cSelected = $(this).val();
    let cPrevious = $(this).data('stored-value');
    $(this).data('stored-value', cSelected);

    let otherSelects = $("select.field-in-db").not(this);

    otherSelects.find('option[value=' + cPrevious + ']').removeAttr('disabled');
    otherSelects.find('option[value=' + cSelected + ']').attr('disabled', 'disabled');
});

// Check box -> filter
$('.check-all').click(function () {
    if ($(this).data('direct') === 1 || $(this).data('direct') === '1') {
        if ($(this).prop('checked') === false) {
            $('.check-one:checked').each(function () {
                $(this).click()
            })
        } else {
            $('.check-one:not(:checked)').each(function () {
                $(this).click()
            })
        }
    } else {
        $(this).data('direct', 1);
    }
})
$('.check-all-export').click(function () {
    if ($(this).data('direct') === 1 || $(this).data('direct') === '1') {
        if ($(this).prop('checked') === false) {
            $('.check-one-export:checked').each(function () {
                $(this).click()
            })
        } else {
            $('.check-one-export:not(:checked)').each(function () {
                $(this).click()
            })
        }
    } else {
        $(this).data('direct', 1);
    }
})

// Export
let selectedList = [];
let total_records = $('.total-records').val();
let count_record_checked = $('#count_record_checked');
let total_records_export = $('.total_records_export');
let num_row_get_filter = $('#num_row_get_filter');
let number_row = $('#number_row');

body.on('click', '.export-button', function () {
    let collection = $(this).data('collection');
    let checked_list_input = $('#checked_list');
    count_record_checked.html(selectedList.length);
    count_record_checked.val(selectedList.length);
    checked_list_input.val(selectedList)
    total_records_export.html(total_records + ' bản ghi');
    number_row.val(total_records);
    num_row_get_filter.prop('max', total_records);
    $('#export_form').prop('action', '/' + collection + '/export');
    $('.export-modal').show();
})

let export_form = $("#export_form").stepy({
    validate: true,
    block: true,
    saveState: false,
    next: function (index) {
        $('#step_import').val(index);
        if (index === 2) {
            let option = $('input[name=export_option]:checked');
            if (option.val() === undefined) {
                alert("Chưa chọn bản ghi để xuất dữ liệu!");
                return false;
            }

            if (option.val() === 'choose' && count_record_checked.val() < 1) {
                alert('Không tồn tại dữ liệu đã chọn để xuất!');
                return false;
            }
            if (option.val() === 'all' && number_row.val() < 1) {
                alert('Không tồn tại dữ liệu để xuất!');
                return false;
            }
            if (option.val() === 'input' && num_row_get_filter.val() < 1) {
                alert('Không tồn tại dữ liệu để xuất!');
                return false;
            }
        }
    },
    back: function (index) {
        $('#step_import').val(index);
        $('.submit-export-form-div').html(`
            <button class="btn btn-primary stepy-finish submit-export-form">
                Xác nhận <i class="icon-check position-right"></i>
            </button>
        `)
    }
})

$('#export_form').submit(function (e) {
    e.preventDefault();

    let form = $(this);
    let url = form.attr('action');
    let nameRegex = new RegExp(/^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i);
    let name = $('#export_file_name').val();
    let fields = $('input.check-one-export:checked');
    // check if the input is valid using a 'valid' property

    if (!name) {
        return false;
    } else if (!nameRegex.test(name)) {
        alert('Sai định dạng dữ liệu trường tên file!\nTên file không được chứa các ký tự đặc biệt.');
        return false;
    } else if (name.length > 50) {
        alert('Độ dài tên file tối đa 50 ký tự!');
        return false;
    }

    if (fields.length <= 0) {
        alert('Chưa chọn trường để xuất dữ liệu!');
        return false;
    }

    if (form.valid()) {
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData: false,        // To send DOMDocument or non processed data file it is set to false
            success: function (data) {
                if (data.success) {
                    $('.submit-export-form-div').html(`
                        <div class="btn btn-info">` + data.number + `/` + data.number + ` bản ghi</div>
                        <a href="` + data.path + `" class="btn btn-success download-button">
                            Lưu file <i class="icon-download"></i>
                        </a>
                    `);
                } else {
                    alert(data.message)
                }
            }
        });
    } else {
        alert('Sai định dạng dữ liệu!');
    }
})

body.on('click', '.check-one', function () {
    if ($(this).prop('checked') === false) {
        let check_all = $('.check-all');
        if (check_all.prop('checked') === true) {
            check_all.data('direct', 0);
            check_all.click();
        }
    }
    let id = $(this).data('id');
    if (selectedList.includes(id)) {
        selectedList = selectedList.filter(item => item !== id);
    } else {
        selectedList.push(id);
        selectedList = removeDuplicates(selectedList);
    }
})
body.on('click', '.check-one-export', function () {
    if ($(this).prop('checked') === false) {
        let check_all = $('.check-all-export');
        if (check_all.prop('checked') === true) {
            check_all.data('direct', 0);
            check_all.click();
        }
    }
})
