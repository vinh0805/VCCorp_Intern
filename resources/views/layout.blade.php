<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@if(isset($title)){{$title}}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Global stylesheets -->
    {{--    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"--}}
    {{--          type="text/css">--}}
    <link href="{{asset('assets/css/icons/icomoon/styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/icons/fontawesome/styles.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/core.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <link href="{{asset('css/stylesheet.css')}}" rel="stylesheet">

    <!-- Core JS files -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/pace.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery_ui/full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/loaders/blockui.min.js')}}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/wizards/stepy.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery_ui/interactions.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/selects/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/notifications/pnotify.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/components_notifications_pnotify.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/notifications/bootbox.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/notifications/sweet_alert.min.js')}}"></script>
    {{--    <script type="text/javascript" src="{{asset('assets/js/plugins/uploaders/dropzone.min.js')}}"></script>--}}
    <script type="text/javascript" src="{{asset('assets/js/plugins/forms/validation/validate.min.js')}}"></script>
    {{--Add--}}
    <script type="text/javascript"
            src="{{asset('assets/js/plugins/uploaders/plupload/plupload.full.min.js')}}"></script>
    <script type="text/javascript"
            src="{{asset('assets/js/plugins/uploaders/plupload/plupload.queue.min.js')}}"></script>


    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery_ui/datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jquery_ui/effects.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/core/libraries/jasny_bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/extensions/cookie.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/notifications/jgrowl.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/ui/moment/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/daterangepicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/anytime.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/pickadate/picker.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/pickadate/picker.date.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/pickadate/picker.time.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/plugins/pickers/pickadate/legacy.js')}}"></script>

    <script type="text/javascript" src="{{asset('assets/js/core/app.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/form_inputs.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/datatables_basic.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/form_select2.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/picker_date.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/components_modals.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/wizard_stepy.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/pages/form_checkboxes_radios.js')}}"></script>
{{--    <script type="text/javascript" src="{{asset('assets/js/pages/uploader_dropzone.js')}}"></script>--}}
{{--    <script type="text/javascript" src="{{asset('assets/js/pages/uploader_plupload.js')}}"></script>--}}
{{--    <script type="text/javascript" src="{{asset('assets/js/pages/form_validation.js')}}"></script>--}}

{{--    <script type="text/javascript" src="{{asset('js/jquery.validate.js')}}"></script>--}}

<!-- /theme JS files -->

</head>

<body>

<!-- Main navbar -->
<div class="navbar navbar-inverse">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{url('/home')}}"><img src="{{url('assets/images/logo_light.png')}}" alt=""></a>

        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav left-menu-nav">
            <li title="Điều khiển menu">
                <a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a>
            </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    @if(isset($currentUser->avatar))
                        <img class="navbar-avatar" src="{{url('storage/images/'. $currentUser->avatar)}}">
                    @else
                        <img class="navbar-avatar" src="{{url('assets/images/placeholder.jpg')}}" alt="">
                    @endif
                    <span>{{$currentUser->name}}</span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{url('home')}}"><i class="icon-user-plus"></i> Thông tin cá nhân </a></li>
                    {{--                    <li>--}}
                    {{--                        <a href="javascript:void(0)" data-toggle="modal" data-target="#change_password_modal">--}}
                    {{--                            <i class="icon-lock2"></i> Đổi mật khẩu--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
                    <li><a href="{{url('logout')}}"><i class="icon-switch2"></i> Đăng xuất</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main sidebar -->
        <div class="sidebar sidebar-main">
            <div class="sidebar-content">
                <!-- Main navigation -->
                <div class="sidebar-category sidebar-category-visible">
                    <div class="category-content no-padding">
                        <ul class="navigation navigation-main navigation-accordion">

                            <!-- Main -->
                            <li>
                                <a href="{{url('customers')}}"><i class="icon-user"></i><span>Khách hàng</span></a>
                            </li>
                            <li>
                                <a href="{{url('orders')}}"><i class="icon-copy"></i> <span>Đơn hàng</span></a>
                            </li>
                            <li>
                                <a href="{{url('companies')}}"><i class="icon-office"></i> <span>Công ty</span></a>
                            </li>
                            <li>
                                <a href="{{url('products')}}"><i class="icon-design"></i> <span>Sản phẩm</span></a>
                            </li>
                            {{--                            @if($currentUser->super_admin)--}}
                            <li>
                                <a href="javascript:void(0)"><i class="icon-user-block"></i> <span>Admin</span></a>
                                <ul>
                                    <li><a href="{{url('admin/users')}}">Danh sách người dùng</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="icon-users4"></i>
                                    <span>Phân quyền</span>
                                </a>
                                <ul>
                                    <li><a href="{{url('set-permission/company')}}">Công ty</a></li>
                                    <li><a href="{{url('set-permission/customer')}}">Khách hàng</a></li>
                                    <li><a href="{{url('set-permission/order')}}">Đơn hàng</a></li>
                                    <li><a href="{{url('set-permission/product')}}">Sản phẩm</a></li>
                                </ul>
                            </li>
                            {{--                            @endif--}}
                            <li>
                                <a href="#"><i class="icon-droplet2"></i> <span>Chức năng khác...</span></a>
                                <ul>
                                    <li><a href="#">Chức năng 1</a></li>
                                    <li><a href="">Chức năng 2</a></li>
                                    <li><a href="#">Chức năng 3</a></li>
                                </ul>
                            </li>
                            <!-- /page kits -->

                        </ul>
                    </div>
                </div>
                <!-- /main navigation -->

            </div>
        </div>
        <!-- /main sidebar -->

        @yield('content')

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->


<!-- Set permission form modal -->
<div id="set_permission_modal" class="modal fade" data-backdrop="static" data-changed="0">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                <h5 class="modal-title">Phân quyền cho người dùng</h5>
            </div>

            <form action="{{url('/set-permission/')}}" class="form-horizontal form-validate-jquery" method="post"
                  id="set_permission_form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3">Chọn quyền <span class="text-danger">*</span></label>
                        <div class="row col-sm-9">
                            @if(isset($allPermissions))
                                @foreach($allPermissions as $permission)
                                    <div class="col-sm-4">
                                        <input type="checkbox" name="permission[]" value="{{ $permission->name }}">
                                        <label> {{$permission->name}}</label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Email người dùng</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Phân quyền</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Set permission form modal -->


<!-- Customer modal -->
<div id="customer_modal" class="modal fade show-info-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header  bg-info">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
                <h5 class="modal-title customer-modal-title"></h5>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">ID khách hàng:</div>
                    <div class="col-sm-8" id="customer_id_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Tên khách hàng:</div>
                    <div class="col-sm-8" id="customer_name_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Ngày sinh:</div>
                    <div class="col-sm-8" id="customer_birth_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Giới tính:</div>
                    <div class="col-sm-8" id="customer_gender_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Công việc:</div>
                    <div class="col-sm-8" id="customer_job_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Địa chỉ:</div>
                    <div class="col-sm-8" id="customer_address_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Email:</div>
                    <div class="col-sm-8" id="customer_email_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Số điện thoại:</div>
                    <div class="col-sm-8" id="customer_phone_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Công ty:</div>
                    <div class="col-sm-8" id="customer_company_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Trạng thái:</div>
                    <div class="col-sm-8" id="customer_status_modal"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<!-- / Customer modal -->

<!-- Company modal -->
<div id="company_modal" class="modal fade show-info-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
                <h5 class="modal-title company-modal-title"></h5>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">ID công ty:</div>
                    <div class="col-sm-8" id="company_id_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Tên công ty:</div>
                    <div class="col-sm-8" id="company_name_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Mã công ty:</div>
                    <div class="col-sm-8" id="company_code_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Lĩnh vực:</div>
                    <div class="col-sm-8" id="company_field_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Địa chỉ:</div>
                    <div class="col-sm-8" id="company_address_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Email:</div>
                    <div class="col-sm-8" id="company_email_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Số điện thoại:</div>
                    <div class="col-sm-8" id="company_phone_modal"></div>
                </div>
                <div class="row">
                    <div class="col-sm-4">Trạng thái:</div>
                    <div class="col-sm-8" id="company_status_modal"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<!-- / Company modal -->

<!-- Products modal -->
<div id="products_modal" class="modal fade show-info-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
                <h5 class="modal-title">Danh sách sản phẩm</h5>
            </div>

            <div class="modal-body">
                <table id="example2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Mã sản phẩm</th>
                        <th>Giá</th>
                        <th>Hình ảnh</th>
                        <th>Còn lại</th>
                        <th>Số lượng</th>
                    </tr>
                    </thead>
                    <tbody id="products_modal_content">
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<!-- / Company modal -->

<!-- Import modal -->
<div id="import_modal" class="modal fade" data-backdrop="static" data-changed="0">
    <div class="modal-dialog modal-full">
        <div class="modal-content import-step-wizard">
            <div class="modal-header">
                <button type="button" class="close close-modal">
                    <i class="fa fa-close"></i>
                </button>
                <button type="button" class="close close-modal2" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h5 class="modal-title">Nhập dữ liệu từ excel</h5>
            </div>
            <form action="" enctype="multipart/form-data" method="post" id="import_form">
                @csrf
                <fieldset>
                    <legend class="text-semibold">Chọn file</legend>
                    <input id="step_import" name="step_import" value="1" hidden readonly="readonly">
                    <input id="import_collection" name="collection" value="1" hidden readonly="readonly">
                    <input id="fields_in_db_input" name="fields_in_db_input" value="1" hidden readonly="readonly">
                    <div class="row import-step-1-row">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Chọn file</label>
                            <div class="col-sm-9">
                                {{--<div class="file-uploader"  name="import_file" id="import_file">--}}
                                <input type="file" class="form-control required" name="import_file" id="import_file"
                                       accept=".xlsx">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-semibold">Chọn các trường</legend>
                    <div class="row" id="import_form_datatable2"></div>
                    <div class="waiting-modal text-center">
                        <p>Đang xử lý dữ liệu, vui lòng đợi...</p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-semibold">Kiểm tra dữ liệu</legend>
                    <div class="row" id="import_form_datatable3"></div>
                    <input id="import_form_random_key" name="import_form_random_key" hidden readonly="readonly"
                           value="">
                    <div class="waiting-modal text-center">
                        <p>Đang xử lý dữ liệu, vui lòng đợi...</p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-semibold">Nhập dữ liệu</legend>
                    <input id="default_fields" name="default_fields" readonly="readonly" hidden value="1">
                    <input id="old_fields" name="old_fields" readonly="readonly" hidden value="1">
                    <input id="new_fields" name="new_fields" readonly="readonly" hidden value="1">
                    <div class="row" id="import_form_datatable4"></div>
                    <div class="waiting-modal text-center">
                        <p>Đang xử lý dữ liệu, vui lòng đợi...</p>
                    </div>
                    <div class="btn btn-default reload-page-button" onclick="location.reload()">
                        Tải lại trang <i class="icon-reload-alt"></i>
                    </div>
                </fieldset>

                <button class="btn btn-primary stepy-finish submit-import-form">
                    Xác nhận <i class="icon-check position-right"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<!-- Import modal -->

<!-- Export modal -->
<div id="export_modal" class="modal fade export-modal">
    <div class="modal-dialog modal-sm export-modal-dialog">
        <div class="modal-content import-step-wizard">
            <div class="modal-header">
                <button type="button" class="close close-modal">
                    <i class="fa fa-close"></i>
                </button>
                <button type="button" class="close close-modal2" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>

                <h5 class="modal-title">Nhập dữ liệu từ excel</h5>
            </div>
            <form action="" enctype="multipart/form-data" method="post" class="validate-form"
                  id="export_form">
                @csrf
                <fieldset>
                    <legend class="text-semibold">Chọn bản ghi</legend>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="radio">
                                <label>
                                    <div class="choice">
                                        <input type="radio" name="export_option" value="choose" class="styled">
                                    </div>
                                    Lấy theo danh sách đã chọn
                                    <span class="badge bg-teal">
                                        <span id="count_record_checked">0</span> bản ghi
                                    </span>
                                </label>
                                <input name="checked_list" id="checked_list" type="hidden" readonly="readonly">
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="radio">
                                <label>
                                    <div class="choice">
                                        <input type="radio" name="export_option" value="all" class="styled">
                                    </div>
                                    Lấy theo điều kiện lọc
                                    <span class="badge bg-teal total_records_export"> bản ghi</span>
                                </label>
                                <input type="hidden" id="number_row" name="number_row" value="1">
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="radio">
                                <label>
                                    <div class="choice">
                                        <input type="radio" name="export_option" value="input" class="styled">
                                    </div>
                                    <div class="pull-left m-1">Lấy</div>
                                    <input type="number" id="num_row_get_filter" name="option_number"
                                           class="form-control pull-left input-sm" value="1" min="1">
                                    <div class="m-1 pull-left"> bản ghi theo điều kiện lọc</div>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" id="export_type" value="all">
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-semibold">Chọn trường lưu dữ liệu</legend>
                    <div id="select_fields_export">
                        <input class="form-control" id="export_file_name" name="name" placeholder="Tên file..."
                               required="required">
                        <table class="table choose-fields-table">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="styled check-all-export" data-direct="1"></th>
                                <th>Chọn tất cả</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($allFieldLabels))
                                @forelse($allFieldLabels as $key => $label)
                                    <tr>
                                        <td><input type="checkbox" class="styled check-one-export" name="fields[]"
                                                   value="{{$key}}"></td>
                                        <td>{{$label}}</td>
                                    </tr>
                                @empty
                                @endforelse
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="waiting-modal text-center">
                        <p>Đang xử lý dữ liệu, vui lòng đợi...</p>
                    </div>
                </fieldset>

                <div class="submit-export-form-div stepy-finish display-inline-block">
                    <button class="btn btn-primary stepy-finish submit-export-form">
                        Xác nhận <i class="icon-check position-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Export modal -->

<!-- Change password form modal -->
<div id="change_password_modal" class="modal fade" data-backdrop="static" data-changed="0">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
                <h5 class="modal-title">Thay đổi mật khẩu</h5>
            </div>

            <form action="{{url('change-password')}}" id="change_password_form  form-validate-jquery"
                  class="form-horizontal form-validate-jquery" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Mật khẩu cũ <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="password_old" id="password_old"
                                   required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Mật khẩu mới <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" name="password" id="password"
                                   required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Nhập lại mật khẩu <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" name="repeat_password" class="form-control" required="required">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Change password form modal -->


<input type="hidden" value="{{url('/')}}" id="url" name="url">

<script type="text/javascript" src="{{url('js/app.js')}}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>
