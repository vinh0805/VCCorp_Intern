@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        {{--        <div class="page-header">--}}
        {{--            <div class="page-header-content">--}}
        {{--                <div class="page-title">--}}
        {{--                    <h4><b>Danh sách khách hàng</b></h4>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách khách hàng</strong></h3>
                    <p class="total-record">Tìm thấy {{$allCustomers->total()}} bản ghi</p>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            @if(isset($permissionList) && in_array('create', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" class="check-form-change"
                                       data-target="#add_customer_modal">
                                        <i class="icon-add" title="Thêm khách hàng mới"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && in_array('import', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#import_modal"
                                       class="import-customer-button check-form-change">
                                        <i class="icon-import" title="Nhập dữ liệu khách hàng"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('export', $permissionList) || in_array('export_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="export-button check-form-change"
                                       data-collection="customer"
                                       data-toggle="modal" data-target="#export_modal">
                                        <i class="icon-database-export" title="Xuất dữ liệu khách hàng"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('delete', $permissionList) || in_array('delete_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="delete-all-button" data-collection="customer">
                                        <i class="icon-folder-remove" title="Xóa các bản ghi đã chọn"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="search">
                    <form action="{{url('/customer/search')}}"
                          class="search-form" method="get" id="search_form">
                        <select class="select-search search-field" data-placeholder="Chọn trường tìm kiếm"
                                name="search_field">
                            <option></option>
                            @foreach($allFieldLabels as $key => $field)
                                @if(isset($search_field) && $search_field == $key)
                                    <option value="{{$key}}" selected>{{$field}}</option>
                                @else
                                    <option value="{{$key}}">{{$field}}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="text" class="form-control display-inline-block search-value"
                               placeholder="Nhập giá trị tìm kiếm..." name="search_value"
                               @if(isset($search_value)) value="{{$search_value}}" @endif>
                        <button type="button" class="btn btn-info">Tìm</button>
                        @if(isset($search_value))
                            <a href="{{url('customers')}}">
                                <div class="btn btn-default">Xóa bộ lọc</div>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="datatable-container">
                    <div class="table">
                        <table class="table datatable-basic customer-table">
                            <thead>
                            <tr>
                                <th><input type="checkbox" class="styled check-all" data-direct="1"></th>
                                <th>STT</th>
                                <th>Tên</th>
                                <th>Ngày sinh</th>
                                <th>Giới tính</th>
                                <th>Công việc</th>
                                <th>Địa chỉ</th>
                                <th>Email</th>
                                <th>Số ĐT</th>
                                <th>Công ty</th>
                                <th>Người phụ trách</th>
                                <th>Trạng thái</th>
                                <th>Thời điểm tạo</th>
                                <th>Thời điểm chỉnh sửa</th>
                                <th class="text-center">Tùy chọn</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($allCustomers as $key => $customer)
                                <tr>
                                    <td><input type="checkbox" class="styled check-one" data-id="{{$customer->_id}}">
                                    </td>
                                    <td>{{ ($allCustomers->currentPage() - 1)*10 + $key + 1}}</td>
                                    <td>{{$customer->name}}</td>
                                    <td>{{$customer->birth}}</td>
                                    <td>{{$customer->gender}}</td>
                                    <td>{{$customer->job}}</td>
                                    <td>{{$customer->address}}</td>
                                    <td>{{$customer->email}}</td>
                                    <td>{{$customer->phone}}</td>
                                    <td>
                                        <a href="#" class="company-modal" data-company="{{$customer->company}}"
                                           data-toggle="modal"
                                           data-target="#company_modal" data-title="{{$customer->name}}">
                                            @if(isset($customer->company->name)) {{$customer->company->name}} @endif
                                        </a>
                                    </td>
                                    <td class="user-list">
                                        <p @if(strlen($customer['userName']) > 50) class="hide-overflow"
                                           title="{{$customer['userName']}}" @endif>
                                            {{$customer->userName}}
                                        </p>
                                    </td>
                                    <td>
                                        @if($customer->status == 'Đang hoạt động')
                                            <span class="label label-success">{{$customer->status}}</span>
                                        @else
                                            <span class="label label-default">{{$customer->status}}</span>
                                        @endif
                                    </td>
                                    <td title="{{$customer->created_at->format('Y-m-d h:i:s.u')}}">
                                        {{$customer->created_at->format('Y-m-d h:i:s')}}
                                    </td>
                                    <td title="{{$customer->updated_at->format('Y-m-d h:i:s.u')}}">
                                        {{$customer->updated_at->format('Y-m-d h:i:s')}}
                                    </td>

                                    <td class="text-center">
                                        @if(isset($permissionList) &&
                                            (in_array('update', $permissionList) || in_array('delete', $permissionList)))
                                            <ul class="icons-list">
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>

                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @if(in_array('update', $permissionList))
                                                            <li>
                                                                <a href="#"
                                                                   class="edit-customer-button check-form-change"
                                                                   data-id="{{$customer->_id}}"
                                                                   data-target="#edit_customer_modal">
                                                                    <i class="icon-pencil"></i> Sửa thông tin
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if(in_array('delete', $permissionList))
                                                            <li>
                                                                <button class="delete-button text-left"
                                                                        data-id="{{$customer->_id}}"
                                                                        data-collection="customer">
                                                                    <i class="icon-folder-remove"></i> Xóa
                                                                </button>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </li>
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center">
                    @if(isset($search_value))
                        {{ $allCustomers->appends(['search_field' => isset($search_field) ? $search_field : '', 'search_value' => $search_value])->links() }}
                    @else
                        {{ $allCustomers->links() }}
                    @endif
                </div>
            </div>
            <!-- /basic datatable -->
        </div>
    </div>
    <label>
        <input type="text" class="total-records" hidden value="{{$allCustomers->total()}}" readonly="readonly">
    </label>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

    <!-- Add customer form modal -->
    <div id="add_customer_modal" class="modal fade" data-backdrop="static" data-changed="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm khách hàng mới</h5>
                </div>

                <form action="{{url('/customer/create')}}"
                      class="form-horizontal add-form validate-form" method="post" id="add_customer_form">
                    @csrf

                    <input type="text" class="form-control check-confirm" name="confirm" value="0">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên khách hàng <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Khách hàng 01" class="form-control" name="name"
                                       required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Ngày sinh</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control daterange-single" value=""
                                           name="birth">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giới tính:</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" class="styled" name="gender" value="Nam">
                                    Nam
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" class="styled" name="gender" value="Nữ">
                                    Nữ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" class="styled" name="gender" value="Khác">
                                    Khác
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Công việc</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Nhân viên ngân hàng, nhân viên kinh doanh,..."
                                       class="form-control" name="job">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Địa chỉ</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email"
                                       required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="099 123 9999"
                                       class="form-control" name="phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Công ty</label>
                            <div class="col-sm-9">
                                <select class="select-search" data-placeholder="Chọn công ty..."
                                        name="company">
                                    <option></option>
                                    @foreach($allCompanies as $company)
                                        <option value="{{$company->_id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Người phụ trách</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" data-placeholder="Chọn người dùng..."
                                        name="users[]">
                                    <option></option>
                                    @foreach($allUsers as $user)
                                        <option value="{{$user->_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Trạng thái</label>
                            <div class="col-sm-9">
                                <select class="select-search" data-placeholder="Chọn trạng thái..."
                                        name="status">
                                    <option></option>
                                    <option value="Đang hoạt động">Đang hoạt động</option>
                                    <option value="Không hoạt động">Không hoạt động</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Add customer form modal -->

    <!-- Edit customer form modal -->
    <div id="edit_customer_modal" class="modal fade" data-backdrop="static" data-changed="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Sửa thông tin khách hàng</h5>
                </div>

                <form action="" id="edit_customer_form" class="form-horizontal validate-form2 edit-form"
                      method="post">
                    @csrf

                    <input type="text" class="form-control check-confirm" name="confirm" value="0">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên khách hàng <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Khách hàng 01" class="form-control" name="name"
                                       id="edit_customer_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Ngày sinh</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control daterange-single" value="01/01/1999"
                                           name="birth" id="edit_customer_birth">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giới tính:</label>
                            <div class="col-sm-9">
                                <input type="radio" id="edit_customer_gender_male" name="gender" value="Nam">
                                <label class="gender-label" for="edit_customer_gender_male">Nam</label>
                                <input type="radio" id="edit_customer_gender_female" name="gender" value="Nữ">
                                <label class="gender-label" for="edit_customer_gender_female">Nữ</label>
                                <input type="radio" id="edit_customer_gender_other" name="gender" value="Khác">
                                <label class="gender-label" for="edit_customer_gender_other">Khác</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Công việc</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Nhân viên ngân hàng, nhân viên kinh doanh,..."
                                       class="form-control" name="job" id="edit_customer_job">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Địa chỉ</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address"
                                       id="edit_customer_address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email"
                                       id="edit_customer_email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="099 123 9999"
                                       class="form-control" name="phone" id="edit_customer_phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Công ty</label>
                            <div class="col-sm-9">
                                <select class="select-search" data-placeholder="Chọn công ty..."
                                        name="company_id" id="edit_customer_company">
                                    <option></option>
                                    @foreach($allCompanies as $company)
                                        <option value="{{$company->_id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Người phụ trách</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" data-placeholder="Chọn người dùng..."
                                        name="users[]" id="edit_customer_user">
                                    <option></option>
                                    @foreach($allUsers as $user)
                                        <option value="{{$user->_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Trạng thái</label>
                            <div class="col-sm-9">
                                <select class="select-search" data-placeholder="Chọn trạng thái..."
                                        name="status" id="edit_customer_status">
                                    <option></option>
                                    <option value="Đang hoạt động">Đang hoạt động</option>
                                    <option value="Không hoạt động">Không hoạt động</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Edit customer form modal -->

@endsection
