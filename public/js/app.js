/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/01_variable.js":
/*!*************************************!*\
  !*** ./resources/js/01_variable.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var table = null;
var table2 = null;
var submitted = false;
var default_fields = [];
var new_fields = [];
var old_fields = [];
var base_path = $("#url").val();
var validate_form = null;
var old_state_form = null;
var new_state_form = null;

/***/ }),

/***/ "./resources/js/admin.js":
/*!*******************************!*\
  !*** ./resources/js/admin.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(".admin-edit-user-button").click(function () {
  $('#edit_user_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/admin/user/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      var url2 = '/admin/user/save/' + response._id;
      var edit_form = $('#edit_user_form');
      edit_form.attr('action', url2);
      $('#edit_user_name').val(response.name);
      $('#edit_user_email').val(response.email);

      if (response.gender === "Nam") {
        $('#edit_user_gender_male').attr('checked', 'checked');
      } else if (response.gender === "Nữ") {
        $('#edit_user_gender_female').attr('checked', 'checked');
      } else if (response.gender === "Khác") {
        $('#edit_user_gender_other').attr('checked', 'checked');
      }

      $('#edit_user_phone').val(response.phone);

      if (response.avatar) {
        var a = $('#edit_user_avatar2');
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
      old_state_form = new_state_form = edit_form.serialize();
      edit_form.change(function () {
        new_state_form = $(this).closest('form').serialize();
      });
    }
  });
});
$('.show-role-modal-button').click(function () {
  var data = $(this).data('role');

  if (data.name) {
    $('#show_role_modal > .modal-dialog > .modal-content > .modal-header').html('<h5>Vai trò của người dùng ' + data.name + '</h5>');
  } else {
    $('#show_role_modal > .modal-header').html('Vai trò của người dùng');
  }

  if (data.customer_role && data.customer_role.name) {
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
});
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

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! ./01_variable */ "./resources/js/01_variable.js");

__webpack_require__(/*! ./layout */ "./resources/js/layout.js");

__webpack_require__(/*! ./customer */ "./resources/js/customer.js");

__webpack_require__(/*! ./company */ "./resources/js/company.js");

__webpack_require__(/*! ./order */ "./resources/js/order.js");

__webpack_require__(/*! ./product */ "./resources/js/product.js");

__webpack_require__(/*! ./admin */ "./resources/js/admin.js");

__webpack_require__(/*! ./role */ "./resources/js/role.js");

/***/ }),

/***/ "./resources/js/company.js":
/*!*********************************!*\
  !*** ./resources/js/company.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$("body").on('click', '.edit-company-button', function () {
  $('#edit_company_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/company/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      var url2 = '/company/save/' + response._id;
      var edit_form = $('#edit_company_form');
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
      old_state_form = new_state_form = edit_form.serialize();
      edit_form.change(function () {
        new_state_form = $(this).closest('form').serialize();
      });
    }
  });
});
$(".import-company-button").click(function () {
  $('#import_collection').val('company');
  $('#import_form').prop('action', '/company/import');
});
$('.company-table').dataTable({
  "scrollX": true,
  "scrollY": "calc(100vh - 325px)",
  "scrollCollapse": true,
  "paging": false,
  "info": false,
  "oLanguage": {
    "sEmptyTable": "Không có dữ liệu."
  },
  columnDefs: [{
    orderable: false,
    targets: 0
  }, {
    orderable: false,
    targets: 9
  }, {
    orderable: false,
    targets: 10
  }],
  searching: false
});
$('body').on('click', '.set_permission_button', function () {
  var url = $('#url').val() + '/set-permission/company/' + $(this).data('id');
  $('#set_permission_form').attr('action', url);
});

/***/ }),

/***/ "./resources/js/customer.js":
/*!**********************************!*\
  !*** ./resources/js/customer.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$("body").on('click', '.edit-customer-button', function () {
  $('#edit_customer_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/customer/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      if (response.message) {
        alert(response.message);
      } else {
        var url2 = '/customer/save/' + response._id;
        var edit_form = $('#edit_customer_form');
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
        } else if (response.gender === "Khác") {
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
  });
});
$(".import-customer-button").click(function () {
  $("#import_form").attr('action', '/customer/import');
});
var a = $('.customer-table').DataTable({
  "scrollX": true,
  "scrollY": "calc(100vh - 325px)",
  "scrollCollapse": true,
  paging: false,
  info: false,
  searching: false,
  "oLanguage": {
    "sEmptyTable": "Không có dữ liệu."
  }
});

/***/ }),

/***/ "./resources/js/layout.js":
/*!********************************!*\
  !*** ./resources/js/layout.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var body = $('body');
base_path = $('#url').val();
$('.modal').on('hidden.bs.modal', function () {
  $(this).find('form').trigger('reset');
  $('.stepy-navigator').show();
}); // Left Menu

var menuStateKey = "menu.toggle_nav.state";
$(document).ready(function () {
  var setInitialMenuState = function setInitialMenuState() {
    // Note: This retrieves a String, not a Boolean.
    var state = localStorage.getItem(menuStateKey) === 'true';
    setNavDisplayState(state);
  };

  var toggleNav = function toggleNav() {
    var state = $(body).attr('class') === 'pace-done sidebar-xs';
    localStorage.setItem(menuStateKey, state);
    setNavDisplayState(state);
  };

  var setNavDisplayState = function setNavDisplayState(state) {
    if (state) {
      $(body).attr('class', 'pace-done sidebar-xs');
    } else {
      $(body).attr('class', 'pace-done');
    }
  };

  $(".sidebar-main-toggle").click(toggleNav);
  setInitialMenuState();
}); // Avoid double click form submit

$(document).ready(function () {
  $('button[type=submit]:not(.delete-button)').one('click', function (e) {
    var _this = this;

    e.preventDefault();
    $(this).prop("disabled", true);

    if ($(this).parent().parent().parent().parent().is('form')) {
      // Submit change info user
      $(this).parent().parent().parent().parent().submit();
    } else if ($(this).parent().parent().is('form')) {
      $(this).parent().parent().submit();
    } else if ($(this).parent().is('form')) {
      $(this).parent().submit();
    }

    setTimeout(function () {
      $(_this).prop("disabled", false);
    }, 2000);
  });
}); // Show modal

body.on('click', '.company-modal', function () {
  var $company = $(this).data('company');
  $('.company-modal-title').text($company['name']);
  $('#company_id_modal').text($company['id']);
  $('#company_name_modal').text($company['name']);
  $('#company_code_modal').text($company['code']);
  $('#company_field_modal').text($company['field']);
  $('#company_address_modal').text($company['address']);
  $('#company_email_modal').text($company['email']);
  $('#company_phone_modal').text($company['phone']);
  $('#company_status_modal').text($company['status']);
});
body.on('click', '.products-modal', function () {
  $('#products_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/order/products/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      var products_modal_content = $('#products_modal_content');
      products_modal_content.html("");
      var $content = '';

      for (var i = 0; i < response.length; i++) {
        $content += "\n                    <tr>\n                        <td>" + response[i].name + "</td>\n                        <td>" + response[i].code + "</td>\n                        <td>" + new Intl.NumberFormat().format(response[i].price) + "</td>";

        if (response[i].image) {
          $content += "<td><img alt=\"\" class=\"product-image\" src=\"" + base_path + "/storage/images/" + response[i].image + "\"></td>";
        } else {
          $content += "<td><img alt=\"\" class=\"product-image\" src=\"" + base_path + "/storage/images/default.png\"></td>";
        }

        $content += "\n                        <td>" + response[i].remain + "</td>\n                        <td>" + response[i].number + "</td>\n                    </tr>\n                ";
      }

      products_modal_content.append($content);
    }
  });
});
body.on('click', '.customer-modal', function () {
  var $customer = $(this).data('customer');
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
}); // Confirm when close add/edit modal

$('.check-form-change').click(function () {
  var form = $($(this).data('target')).find('form');
  old_state_form = form.serialize();
  form.change(function () {
    new_state_form = $(this).closest('form').serialize();
  });
  new_state_form = null;
});
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
}); // Select2

$("div.select-search").select2();
$(document).ready(function () {
  $('select.select-search').select2();
}); // Debugbar
// body.on('xhr.dt', function (e, settings, data, xhr) {
//     if (typeof phpdebugbar != "undefined") {
//         phpdebugbar.ajaxHandler.handle(xhr);
//     }
// });
// Create

$(".add-form").submit(function (e) {
  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $(this);
  var url = form.attr('action'); // check if the input is valid using a 'valid' property

  if (form.valid()) {
    $.ajax({
      type: "POST",
      url: url,
      data: new FormData(this),
      contentType: false,
      // The content type used when sending data to the server.
      cache: false,
      // To unable request pages to be cached
      processData: false,
      // To send DOMDocument or non processed data file it is set to false
      success: function success(data) {
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
}); // Search

$(".search-form > button[type=button]").click(function (e) {
  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $(this).parent('form');
  var search_value = $('.search-value').val();

  if (!search_value) {
    alert('Chưa nhập dữ liệu để tìm kiếm!');
    return false;
  } else {
    form.submit();
  }
}); // Update

$(".edit-form").submit(function (e) {
  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $(this);
  var url = form.attr('action');

  if (!$(this).valid()) {
    return false;
  }

  $.ajax({
    type: "POST",
    url: url,
    data: new FormData(this),
    contentType: false,
    // The content type used when sending data to the server.
    cache: false,
    // To unable request pages to be cached
    processData: false,
    // To send DOMDocument or non processed data file it is set to false
    success: function success(data) {
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

  var form = $(this);
  var url = form.attr('action');

  if (!form.valid()) {
    return false;
  }

  $.ajax({
    type: "POST",
    url: url,
    data: new FormData(this),
    contentType: false,
    // The content type used when sending data to the server.
    cache: false,
    // To unable request pages to be cached
    processData: false,
    // To send DOMDocument or non processed data file it is set to false
    success: function success(data) {
      alert(data.message); // show response from the php script.

      if (data.success) {
        location.reload();
      }
    }
  });
}); // Delete

body.on('click', '.delete-button', function () {
  var collection = $(this).data('collection');

  var _id = $(this).data('id');

  if (confirm('Bạn có muốn xóa bản ghi này không?')) {
    $.ajax({
      type: 'post',
      url: '/' + collection + '/delete/' + _id,
      dataType: 'json',
      success: function success(response) {
        alert(response.message);

        if (response.success) {
          location.reload();
        }
      }
    });
  }
}); // Delete all

body.on('click', '.delete-all-button', function () {
  var collection = $(this).data('collection');

  if (selectedList.length < 1) {
    return alert('Chưa chọn bản ghi nào!');
  }

  if (confirm('Bạn có muốn xóa ' + selectedList.length + ' bản ghi đã chọn không?')) {
    $.ajax({
      type: 'post',
      url: '/' + collection + '/delete-all',
      data: {
        'idList': selectedList
      },
      dataType: 'json',
      success: function success(response) {
        alert(response.message);

        if (response.success) {
          location.reload();
        }
      }
    });
  }
}); // Delete on import

body.on('click', '.remove-review-data', function (e) {
  var _id = $(this).parent().parent('tr').data('id');

  if (confirm('Bạn có muốn xóa bản ghi này không?')) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      url: '/review-data/delete/' + _id,
      dataType: 'json',
      success: function success(response) {
        alert(response.message);

        if (response.success) {
          table2.row($('tr#tmp_' + _id)).remove().draw();
        }
      }
    });
  }
}); // Import =============================//
// Hidden

body.on('hidden.bs.modal', function () {
  document.getElementsByClassName('button-back')[0].click();
  document.getElementsByClassName('button-back')[3].click();
  $('#import_form_datatable2').html('');
  $('#import_form_datatable3').html('');
  $('.reload-page-button').hide(); // Single picker

  $('.daterange-single').daterangepicker({
    singleDatePicker: true,
    maxDate: new Date()
  }).val('');
  validate_form.resetForm();
});
$(document).ready(function () {
  $('.daterange-single').daterangepicker({
    singleDatePicker: true,
    maxDate: new Date()
  }).val('');
}); // Loading...

$(document).on({
  ajaxStart: function ajaxStart() {
    body.addClass("loading");
  },
  ajaxStop: function ajaxStop() {
    body.removeClass("loading");
  }
}); // Validate type of file xlsx

var validate = false;
$(document).ready(function () {
  $("#import_file").change(function () {
    var fileExtension = ['xlsx'];

    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) === -1) {
      alert("Chỉ chấp nhận đuôi file : " + fileExtension.join(', '));
      validate = false;
    } else {
      $('.button-next').prop('disabled', false);
      validate = true;
    }
  });
  $('#import_form > fieldset > div.stepy-navigator > a.button-next').click(function () {
    var import_file = $("#import_file");
    var fileExtension = ['xlsx'];

    if (!import_file.val()) {
      alert("Chưa chọn file!");
      validate = false;
    } else if ($.inArray(import_file.val().split('.').pop().toLowerCase(), fileExtension) === -1) {
      alert("Chỉ chấp nhận đuôi file : " + fileExtension.join(', '));
      validate = false;
    }
  });
}); // Set stepy import form

var import_form = $("#import_form").stepy({
  validate: true,
  block: true,
  labels: {
    next: "Tiếp",
    back: "Trước",
    finish: "Nhập"
  },
  saveState: false,
  next: function next(index) {
    if (!validate) {
      return false;
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
  back: function back(index) {
    if (!validate) {
      return false;
    }

    $('#step_import').val(index);
  },
  select: function select(index) {}
});
$('#import_form').submit(function (e) {
  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $(this);
  var url = form.attr('action');
  var html = '';
  $.ajax({
    type: "POST",
    url: url,
    data: new FormData(this),
    contentType: false,
    // The content type used when sending data to the server.
    cache: false,
    // To unable request pages to be cached
    processData: false,
    // To send DOMDocument or non processed data file it is set to false
    success: function success(data) {
      if (data.message) {
        $('button.submit-import-form').prop('disabled', true);
        alert(data.message); // show response from the php script.

        if (data.success) {
          location.reload();
        } else {
          document.getElementsByClassName('button-back')[data.step - 2].click();
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
}); // Pagination of import view

$('#import_form_datatable3').on('click', 'a.page-link', function (event) {
  event.preventDefault();
  var page = $(this).attr('href').split('page=')[1];
  fetch_data(page);
});

function fetch_data(page) {
  var step_import = $('#step_import').val();
  var import_form_random_key = $('#import_form_random_key').val();
  var collection = $('#import_collection').val();
  var fields_in_db = $("#fields_in_db_input").val();
  var default_fields = $('#default_fields').val();
  var old_fields = $('#old_fields').val();
  var new_fields = $('#new_fields').val();
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
    success: function success(data) {
      var html = data[0];
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
} // Set data type for data in import view


body.on('click', '.set-data-type', function () {
  var field = $(this).data('field');
  var type = $(this).data('type');
  $('a.set-data-type2[data-field=' + field + '][data-type=' + type + ']').each(function () {
    $(this).click();
  });

  if (type === 'default') {
    default_fields.push(field);
    old_fields = old_fields.filter(function (item) {
      return item !== field;
    });
    new_fields = new_fields.filter(function (item) {
      return item !== field;
    });
  } else if (type === 'new') {
    new_fields.push(field);
    old_fields = old_fields.filter(function (item) {
      return item !== field;
    });
    default_fields = default_fields.filter(function (item) {
      return item !== field;
    });
  } else if (type === 'old') {
    old_fields.push(field);
    default_fields = default_fields.filter(function (item) {
      return item !== field;
    });
    new_fields = new_fields.filter(function (item) {
      return item !== field;
    });
  }

  default_fields = removeDuplicates(default_fields);
  new_fields = removeDuplicates(new_fields);
  old_fields = removeDuplicates(old_fields);
  $('#default_fields').val(default_fields);
  $('#old_fields').val(old_fields);
  $('#new_fields').val(new_fields);
});

function removeDuplicates(array) {
  return array.filter(function (a, b) {
    return array.indexOf(a) === b;
  });
}

body.on('click', '.set-data-type2', function () {
  $(this).parent().parent().siblings('a').html($(this).data('value') + " <span class=\"caret\"></span>");
  $(this).parent().parent().parent().siblings('input').val($(this).data('type'));
});
$('#submitBtn').click(function () {
  var stepInfo = $('#import_form').formwizard('state');

  for (var i = 0; i < stepInfo.activatedSteps.length; i++) {
    stepInfo.steps.filter("#" + stepInfo.activatedSteps[i]).find(":input").not(".wizard-ignore").removeAttr("disabled");
  }
}); // End Import ==========================//
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
  });
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
  }); // Prevent submit form by enter

  $(window).keydown(function (event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      return false;
    }
  });
});
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
$.validator.addMethod("regex", function (value, element, regexp) {
  return this.optional(element) || regexp.test(value);
}, "Sai định dạng dữ liệu!"); // Disable other select

$("select").each(function () {
  $(this).data('stored-value', $(this).val());
});
body.on('change', 'select.field-in-db', function () {
  var cSelected = $(this).val();
  var cPrevious = $(this).data('stored-value');
  $(this).data('stored-value', cSelected);
  var otherSelects = $("select.field-in-db").not(this);
  otherSelects.find('option[value=' + cPrevious + ']').removeAttr('disabled');
  otherSelects.find('option[value=' + cSelected + ']').attr('disabled', 'disabled');
}); // Check box -> filter

$('.check-all').click(function () {
  if ($(this).data('direct') === 1 || $(this).data('direct') === '1') {
    if ($(this).prop('checked') === false) {
      $('.check-one:checked').each(function () {
        $(this).click();
      });
    } else {
      $('.check-one:not(:checked)').each(function () {
        $(this).click();
      });
    }
  } else {
    $(this).data('direct', 1);
  }
});
$('.check-all-export').click(function () {
  if ($(this).data('direct') === 1 || $(this).data('direct') === '1') {
    if ($(this).prop('checked') === false) {
      $('.check-one-export:checked').each(function () {
        $(this).click();
      });
    } else {
      $('.check-one-export:not(:checked)').each(function () {
        $(this).click();
      });
    }
  } else {
    $(this).data('direct', 1);
  }
}); // Export

var selectedList = [];
var total_records = $('.total-records').val();
var count_record_checked = $('#count_record_checked');
var total_records_export = $('.total_records_export');
var num_row_get_filter = $('#num_row_get_filter');
var number_row = $('#number_row');
body.on('click', '.export-button', function () {
  var collection = $(this).data('collection');
  var checked_list_input = $('#checked_list');
  count_record_checked.html(selectedList.length);
  count_record_checked.val(selectedList.length);
  checked_list_input.val(selectedList);
  total_records_export.html(total_records + ' bản ghi');
  number_row.val(total_records);
  num_row_get_filter.prop('max', total_records);
  $('#export_form').prop('action', '/' + collection + '/export');
  $('.export-modal').show();
});
var export_form = $("#export_form").stepy({
  validate: true,
  block: true,
  saveState: false,
  next: function next(index) {
    $('#step_import').val(index);

    if (index === 2) {
      var option = $('input[name=export_option]:checked');

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
  back: function back(index) {
    $('#step_import').val(index);
    $('.submit-export-form-div').html("\n            <button class=\"btn btn-primary stepy-finish submit-export-form\">\n                X\xE1c nh\u1EADn <i class=\"icon-check position-right\"></i>\n            </button>\n        ");
  }
});
$('#export_form').submit(function (e) {
  e.preventDefault();
  var form = $(this);
  var url = form.attr('action');
  var nameRegex = new RegExp(/^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i);
  var name = $('#export_file_name').val();
  var fields = $('input.check-one-export:checked'); // check if the input is valid using a 'valid' property

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
      contentType: false,
      // The content type used when sending data to the server.
      cache: false,
      // To unable request pages to be cached
      processData: false,
      // To send DOMDocument or non processed data file it is set to false
      success: function success(data) {
        if (data.success) {
          $('.submit-export-form-div').html("\n                        <div class=\"btn btn-info\">" + data.number + "/" + data.number + " b\u1EA3n ghi</div>\n                        <a href=\"" + data.path + "\" class=\"btn btn-success download-button\">\n                            L\u01B0u file <i class=\"icon-download\"></i>\n                        </a>\n                    ");
        } else {
          alert(data.message);
        }
      }
    });
  } else {
    alert('Sai định dạng dữ liệu!');
  }
});
body.on('click', '.check-one', function () {
  if ($(this).prop('checked') === false) {
    var check_all = $('.check-all');

    if (check_all.prop('checked') === true) {
      check_all.data('direct', 0);
      check_all.click();
    }
  }

  var id = $(this).data('id');

  if (selectedList.includes(id)) {
    selectedList = selectedList.filter(function (item) {
      return item !== id;
    });
  } else {
    selectedList.push(id);
    selectedList = removeDuplicates(selectedList);
  }
});
body.on('click', '.check-one-export', function () {
  if ($(this).prop('checked') === false) {
    var check_all = $('.check-all-export');

    if (check_all.prop('checked') === true) {
      check_all.data('direct', 0);
      check_all.click();
    }
  }
});

/***/ }),

/***/ "./resources/js/order.js":
/*!*******************************!*\
  !*** ./resources/js/order.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$("body").on('click', '.edit-order-button', function () {
  $('#edit_order_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/order/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      var url2 = '/order/save/' + response._id;
      var edit_form = $('#edit_order_form');
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
      var $productList = $(".add-product-order-button").data('products');

      if (response.products.length > 0) {
        $('#edit_product_list').data('id', response.products.length);
        $('#edit_product_01').select2("val", response.products[0].product);
        $('#edit_product_number_01').val(response.products[0].number);
      }

      if (response.products.length > 1) {
        for (var $i = 2; $i < response.products.length + 1; $i++) {
          var $appendContent = "\n                        <div class=\"form-group add-product-list\" data-id=\"" + $i + "\" id=\"edit_product" + $i + "\">\n                            <div class=\"col-sm-6\">\n                                <label class=\"control-label col-sm-4 product-label\">S\u1EA3n ph\u1EA9m:</label>\n                                <div class=\"col-sm-8 select-product-order\">\n                                    <select class=\"select-search\" data-placeholder=\"Ch\u1ECDn s\u1EA3n ph\u1EA9m...\"\n                                            name=\"product[]\" id=\"edit_product_" + $i + "\">\n                                        <option></option>";

          for (var $i2 = 0; $i2 < $productList.length; $i2++) {
            if (response.products[$i - 1].product === $productList[$i2]._id) {
              $appendContent += "\n                                        <option value=\"" + $productList[$i2]._id + "\" selected>" + $productList[$i2].name + "</option>";
            } else {
              $appendContent += "\n                                        <option value=\"" + $productList[$i2]._id + "\">" + $productList[$i2].name + "</option>";
            }
          }

          $appendContent += "\n                                    </select>\n                                </div>\n                            </div>\n                            <div class=\"col-md-6\">\n                                <label class=\"control-label col-sm-3\">S\u1ED1 l\u01B0\u1EE3ng</label>\n                                <div class=\"col-sm-7\">\n                                    <input type='number' class=\"form-control\" name=\"number[]\" value=\"" + response.products[$i - 1].number + "\" min=\"1\">\n                                </div>\n                                <div class=\"col-sm-2 text-right\">\n                                    <button type=\"button\" onclick=\"document.getElementById('edit_product" + $i + "').remove()\"\n                                            class=\"btn btn-danger sub-product-order-button\">\n                                        <i class=\"icon-subtract\"></i>\n                                    </button>\n                                </div>\n                            </div>\n                        </div>";
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
  });
});
$(".add-product-order-button").click(function () {
  var $id = $(this).data('id') + 1;
  $(this).data('id', $id);
  var $productList = $(this).data('products');
  var $appendContent = "\n        <div class=\"form-group add-product-list\" data-id=\"" + $id + "\" id=\"product" + $id + "\">\n            <div class=\"col-sm-6\">\n                <label class=\"control-label col-sm-4 product-label\">S\u1EA3n ph\u1EA9m:</label>\n                <div class=\"col-sm-8 select-product-order\">\n                    <select class=\"select-search\" data-placeholder=\"Ch\u1ECDn s\u1EA3n ph\u1EA9m...\" name=\"product[]\">\n                        <option></option>";

  for (var $i = 0; $i < $productList.length; $i++) {
    $appendContent += "\n                    <option value=\"" + $productList[$i]._id + "\">" + $productList[$i].name + "</option>";
  }

  $appendContent += "\n                    </select>\n                </div>\n            </div>\n            <div class=\"col-md-6\">\n                <label class=\"control-label col-sm-3\">S\u1ED1 l\u01B0\u1EE3ng</label>\n                <div class=\"col-sm-7\">\n                    <input type='number' class=\"form-control\" name=\"number[]\" min=\"1\" value=\"1\">\n                </div>\n                <div class=\"col-sm-2 text-right\">\n                    <button type=\"button\" onclick=\"document.getElementById('product" + $id + "').remove()\"\n                            class=\"btn btn-danger sub-product-order-button\">\n                        <i class=\"icon-subtract\"></i>\n                    </button>\n                </div>\n            </div>\n        </div>";
  $("#add_product_list2").append($appendContent);
  $("select.select-search").select2();
});
$(".add-product-order-button2").click(function () {
  var $id = $(this).data('id') + 1;
  $(this).data('id', $id);
  var $productList = $(this).data('products');
  var $appendContent = "\n        <div class=\"form-group add-product-list\" data-id=\"" + $id + "\" id=\"product" + $id + "\">\n            <div class=\"col-sm-6\">\n                <label class=\"control-label col-sm-4 product-label\">S\u1EA3n ph\u1EA9m:</label>\n                <div class=\"col-sm-8 select-product-order\">\n                    <select class=\"select-search\" data-placeholder=\"Ch\u1ECDn s\u1EA3n ph\u1EA9m...\" name=\"product[]\">\n                        <option></option>";

  for (var $i = 0; $i < $productList.length; $i++) {
    $appendContent += "\n                    <option value=\"" + $productList[$i]._id + "\">" + $productList[$i].name + "</option>";
  }

  $appendContent += "\n                    </select>\n                </div>\n            </div>\n            <div class=\"col-md-6\">\n                <label class=\"control-label col-sm-3\">S\u1ED1 l\u01B0\u1EE3ng</label>\n                <div class=\"col-sm-7\">\n                    <input type='number' class=\"form-control\" name=\"number[]\" min=\"1\" value=\"1\">\n                </div>\n                <div class=\"col-sm-2 text-right\">\n                    <button type=\"button\" onclick=\"document.getElementById('product" + $id + "').remove()\"\n                            class=\"btn btn-danger sub-product-order-button\">\n                        <i class=\"icon-subtract\"></i>\n                    </button>\n                </div>\n            </div>\n        </div>";
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

/***/ }),

/***/ "./resources/js/product.js":
/*!*********************************!*\
  !*** ./resources/js/product.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$("body").on('click', ".edit-product-button", function () {
  $('#edit_product_modal').modal('show');
  $.ajax({
    type: 'get',
    url: '/product/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      var url2 = '/product/save/' + response._id;
      var edit_form = $('#edit_product_form');
      edit_form.attr('action', url2);
      $('#edit_product_name').val(response.name);
      $('#edit_product_code').val(response.code);
      $('#edit_product_price').val(response.price);
      $('#edit_product_remain').val(response.remain);

      if (response.image) {
        var a = $('#edit_product_image2');
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
  });
});
$(".import-product-button").click(function () {
  $("#import_form").attr('action', '/product/import');
});
var product_table = $('.product-table').DataTable({
  "scrollX": true,
  "scrollY": "calc(100vh - 325px)",
  "scrollCollapse": true,
  "paging": false,
  "info": false,
  "oLanguage": {
    "sEmptyTable": "Không có dữ liệu."
  },
  columnDefs: [{
    orderable: false,
    targets: 0
  }, {
    orderable: false,
    targets: 6
  }, {
    orderable: false,
    targets: 9
  }],
  searching: false
});
product_table.columns.adjust();
$(".sidebar-main-toggle").click(function () {
  product_table.columns.adjust();
});

/***/ }),

/***/ "./resources/js/role.js":
/*!******************************!*\
  !*** ./resources/js/role.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

var body = $('body'); // Delete role

body.on('click', '.delete-role', function () {
  var _id = $(this).data('id');

  if (confirm('Bạn có muốn xóa nhóm quyền này không?')) {
    $.ajax({
      type: 'post',
      url: 'role/delete/' + _id,
      dataType: 'json',
      success: function success(response) {
        alert(response.message);

        if (response.success) {
          location.reload();
        }
      }
    });
  }
});
$("body").on('click', '.edit-role-button', function () {
  $('#edit_role_modal').modal('show');
  $.ajax({
    type: 'get',
    url: 'role/edit/' + $(this).data('id'),
    dataType: 'json',
    success: function success(response) {
      if (response.message) {
        alert(response.message);
      } else {
        var url = 'role/save/' + response._id;
        var edit_form = $('#edit_role_form');
        edit_form.attr('action', url);
        edit_form.val(response.name);
        $('.edit-permission').each(function () {
          if (response.permission_list.includes($(this).data('role'))) {
            $(this).prop('checked', true);
          }

          $(".styled").uniform({
            radioClass: 'choice'
          });
        });
        old_state_form = new_state_form = edit_form.serialize();
        edit_form.change(function () {
          new_state_form = $(this).closest('form').serialize();
        });
      }
    }
  });
});

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\xampp\htdocs\VCCorp\salesManagement\resources\js\app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! C:\xampp\htdocs\VCCorp\salesManagement\resources\sass\app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });