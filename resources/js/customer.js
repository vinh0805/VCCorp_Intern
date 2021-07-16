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

                edit_form.data('changed', 0);
                $("form :input").change(function () {
                    $(this).closest('form').data('changed', 1);
                });

                // $('.validate-form2').validate({
                //     ignore: [],
                //     rules: {
                //         name: {
                //             required: true,
                //             minlength: 3,
                //             maxlength: 50,
                //             regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
                //         },
                //         email: {
                //             maxlength: 50,
                //             email: true,
                //             required: true
                //         },
                //         phone: {
                //             regex: /^([0][1-9]{2} [0-9]{3} [0-9]{4}||'')$/
                //         },
                //         address: {
                //             minlength: 3,
                //             maxlength: 100,
                //             regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ \-,\s]+)$/i
                //         },
                //         field: {
                //             minlength: 3,
                //             maxlength: 50,
                //             regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
                //         },
                //         job: {
                //             minlength: 3,
                //             maxlength: 50,
                //             regex: /^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i
                //         },
                //         birth: {
                //             number: true,
                //             min: 1900,
                //             max: 2021
                //         },
                //         tax: {
                //             number: true,
                //             min: 0,
                //             max: 1000
                //         },
                //         product: {
                //             required: true
                //         },
                //         number: {
                //             number: true,
                //             min: 1
                //         },
                //         price: {
                //             required: true,
                //             number: true,
                //             min: 1000
                //         },
                //         remain: {
                //             required: true,
                //             number: true,
                //             min: 1
                //         }
                //     }
                // })
            }
        }
    })
});

$(".import-customer-button").click(function () {
    $("#import_form").attr('action', '/customer/import');

    // let base_path = $("#url").val();
    // $(".file-uploader").pluploadQueue({
    //     runtimes: 'html5, html4, Flash, Silverlight',
    //     url: base_path + '/customer/import',
    //     chunk_size: '5Mb',
    //     unique_names: true,
    //     multi_selection: false,
    //     max_file_count: 1,
    //     filters: {
    //         max_file_size: '5Mb',
    //         mime_types: [{
    //             title: "Excel files",
    //             extensions: "xlsx"
    //         }]
    //     },
    //     resize: {
    //         width: 320,
    //         height: 240,
    //         quality: 90
    //     },
    //     init: {
    //         QueueChanged: function (uploader) {
    //             if (uploader.files.length > 1) {
    //                 uploader.files.splice(1, uploader.files.length);
    //
    //                 alert('You can not add more than one file!', {});
    //             }
    //         }// Initialize validation
    //     }
    // })
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

// $('.customer-table').DataTable();
// $('.customer-table').DataTable({
//     processing: true,
//     serverSide: true,
//     ajax: {
//         url: 'customers/get-data',
//         dataType: 'json',
//         type: 'GET'
//     },
//     columns:[
//         {
//             data: 'id',
//         },
//         {
//             data: 'name'
//         },
//         {
//             data: 'age'
//         },
//         {
//             data: 'gender',
//             orderable: false
//         },
//         {
//             data: 'job'
//         },
//         {
//             data: 'address'
//         },
//         {
//             data: 'email'
//         },
//         {
//             data: 'phone'
//         },
//         {
//             data: 'company',
//             render: function (data, type, full, meta) {
//                 if (data && data.name) {
//                     return `<a href="javascript:void(0)" class="company-modal" data-company='` + JSON.stringify(data) + `'
//                                data-toggle="modal"
//                                data-target="#company_modal" data-title="` + data.name + `">` + data.name +
//                             `</a>`;
//                 }
//                 return null;
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
//                 if (data && data === "Đang hoạt động") {
//                     return '<span class="label label-success">' + data + '</span>';
//                 } else if (data && data === "Không hoạt động") {
//                     return '<span class="label label-default">' + data + '</span>';
//                 } else {
//                     return null;
//                 }
//             }
//         },
//         {
//             data: 'option',
//             orderable: false,
//
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
//                                             <a href="javascript:void(0)" class="edit-customer-button" data-id="` + data + `">
//                                                 <i class="icon-pencil"></i> Sửa thông tin
//                                             </a>
//                                         </li>`;
//                 }
//
//                 if (full.delete_permission) {
//                     html +=            `<li>
//                                             <a href="` + url + `/user/customer/delete/` + data + `"
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
//
//         }
//     ]
// });
