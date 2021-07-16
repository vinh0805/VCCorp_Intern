@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách tài khoản người dùng</strong></h3>
                    <p class="total-record">Tìm thấy {{$userList->total()}} bản ghi</p>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li>
                                <a href="#" data-toggle="modal" data-target="#add_user_modal" class="check-form-change">
                                    <i class="icon-add" title="Tạo người dùng mới"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="delete-all-button" data-collection="admin/user">
                                    <i class="icon-folder-remove" title="Xóa các bản ghi đã chọn"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="search">
                    <form action="{{url('/admin/user/search')}}"
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
                            <a href="{{url('/admin/users')}}">
                                <div class="btn btn-default">Xóa bộ lọc</div>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="datatable-container">
                    <table class="table datatable-basic user-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox" class="styled check-all" data-direct="1"></th>
                            <th>STT</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Giới tính</th>
                            <th>Số ĐT</th>
                            <th>Ảnh đại diện</th>
                            <th>Vai trò</th>
                            <th>Phân quyền</th>
                            <th class="text-center">Tùy chọn</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($userList as $key => $user)
                            <tr>
                                <td><input type="checkbox" class="styled check-one" data-id="{{$user->_id}}"></td>
                                <td>{{ ($userList->currentPage() - 1)*10 + $key + 1}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->gender}}</td>
                                <td>{{$user->phone}}</td>
                                <td>
                                    @if(isset($user->avatar))
                                        <img class="product-image" src="{{url('storage/images/' . $user->avatar)}}">
                                    @else
                                        <img class="product-image" src="{{url('storage/images/default.png')}}">
                                    @endif
                                </td>

                                <td>
                                    @if(isset($user->super_admin) && $user->super_admin)
                                        Super Admin
                                    @elseif(isset($user->super_admin) && !$user->super_admin)
                                        User
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#show_role_modal"
                                       data-role="{{$user}}" class="show-role-modal-button">
                                        Chi tiết
                                    </a>
                                </td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a href="#"
                                                       class="admin-edit-user-button" data-id="{{$user->_id}}">
                                                        <i class="icon-pencil"></i> Sửa thông tin
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="submit" class="delete-button text-left"
                                                            data-id="{{$user->_id}}" data-collection="user">
                                                        <i class="icon-folder-remove"></i> Xóa
                                                    </button>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- /basic datatable -->
            <div class="text-center">
                @if(isset($search_value))
                    {{ $userList->appends(['search_field' => isset($search_field) ? $search_field : '', 'search_value' => $search_value])->links() }}
                @else
                    {{ $userList->links() }}
                @endif
            </div>

        </div>
    </div>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp


    <!-- Customer modal -->
    <div id="show_role_modal" class="modal fade show-info-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  bg-info">
                    <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title customer-modal-title"></h5>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">Module Khách hàng:</div>
                        <div class="col-sm-8" id="show_role_modal_customer"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">Module Đơn hàng:</div>
                        <div class="col-sm-8" id="show_role_modal_order"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">Module Công ty:</div>
                        <div class="col-sm-8" id="show_role_modal_company"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">Module Sản phẩm:</div>
                        <div class="col-sm-8" id="show_role_modal_product"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Customer modal -->


    <!-- Add user form modal -->
    <div id="add_user_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm người dùng mới</h5>
                </div>

                <form action="{{url('/admin/user/create')}}" class="form-horizontal validate-form add-form" method="post"
                      enctype="multipart/form-data" data-changed="0">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên người dùng <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email"
                                       required="required">
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
                            <label class="control-label col-sm-3">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" required="required">
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
                            <label class="control-label col-sm-3">Ảnh đại diện</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Super Admin</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" data-placeholder="Admin/User"
                                        name="super_admin">
                                    <option></option>
                                    <option value="true">Admin</option>
                                    <option value="false">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Vai trò</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module khách hàng:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" multiple="multiple"
                                                data-placeholder="Admin/User/..."
                                                name="role_customer">
                                            <option></option>
                                            @foreach($allRoleCustomers as $roleCustomer)
                                                <option value="{{$roleCustomer->_id}}">{{$roleCustomer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module đơn hàng:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" multiple="multiple"
                                                data-placeholder="Admin/User/..."
                                                name="role_order">
                                            <option></option>
                                            @foreach($allRoleOrders as $roleOrder)
                                                <option value="{{$roleOrder->_id}}">{{$roleOrder->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module công ty:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" multiple="multiple"
                                                data-placeholder="Admin/User/..."
                                                name="company_product">
                                            <option></option>
                                            @foreach($allRoleCompanies as $roleCompany)
                                                <option value="{{$roleCompany->_id}}">{{$roleCompany->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module sản phẩm:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" multiple="multiple"
                                                data-placeholder="Admin/User/..."
                                                name="role_product">
                                            <option></option>
                                            @foreach($allRoleProducts as $roleProduct)
                                                <option value="{{$roleProduct->_id}}">{{$roleProduct->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
    <!-- / Add user form modal -->

    <!-- Edit user form modal -->
    <div id="edit_user_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Sửa thông tin người dùng</h5>
                </div>

                <form action="{{url('/admin/user/edit')}}" class="form-horizontal validate-form2 edit-form" method="post"
                      enctype="multipart/form-data" id="edit_user_form" data-changed="0">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên người dùng <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Eugene" class="form-control" name="name"
                                       id="edit_user_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email"
                                       id="edit_user_email" disabled readonly="readonly">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Giới tính:</label>
                            <div class="col-sm-9">
                                <input type="radio" id="edit_user_gender_male" name="gender" value="Nam">
                                <label class="gender-label" for="edit_user_gender_male">Nam</label>
                                <input type="radio" id="edit_user_gender_female" name="gender" value="Nữ">
                                <label class="gender-label" for="edit_user_gender_female">Nữ</label>
                                <input type="radio" id="edit_user_gender_other" name="gender" value="Khác">
                                <label class="gender-label" for="edit_user_gender_other">Khác</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Mật khẩu
                                <span class="text-danger"
                                      title="Nếu bạn bỏ trống trường này, mật khẩu của tài khoản sẽ được giữ nguyên">(*)</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password"
                                       id="edit_user_password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="099 123 9999"
                                       class="form-control" name="phone" id="edit_user_phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Ảnh đại diện</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="avatar" id="edit_user_avatar"
                                       accept="image/x-png,image/gif,image/jpeg">
                                <input type="text" class="form-control" name="image2" id="edit_user_avatar2"
                                       accept="image/x-png,image/gif,image/jpeg" readonly>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Super Admin</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" data-placeholder="Admin/User"
                                        name="super_admin" id="edit_user_super_admin">
                                    <option></option>
                                    <option value="1">Admin</option>
                                    <option value="0">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Vai trò</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module khách hàng:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" data-placeholder="Admin/User/..."
                                                name="role_customer" id="edit_user_role_customer">
                                            <option></option>
                                            @foreach($allRoleCustomers as $roleCustomer)
                                                <option value="{{$roleCustomer->_id}}">{{$roleCustomer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module đơn hàng:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" data-placeholder="Admin/User/..."
                                                name="role_order" id="edit_user_role_order">
                                            <option></option>
                                            @foreach($allRoleOrders as $roleOrder)
                                                <option value="{{$roleOrder->_id}}">{{$roleOrder->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module công ty:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" data-placeholder="Admin/User/..."
                                                name="role_company" id="edit_user_role_company">
                                            <option></option>
                                            @foreach($allRoleCompanies as $roleCompany)
                                                <option value="{{$roleCompany->_id}}">{{$roleCompany->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 role-label">Module sản phẩm:</div>
                                    <div class="col-sm-8">
                                        <select class="select-search" data-placeholder="Admin/User/..."
                                                name="role_product" id="edit_user_role_product">
                                            <option></option>
                                            @foreach($allRoleProducts as $roleProduct)
                                                <option value="{{$roleProduct->_id}}">{{$roleProduct->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Sửa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Edit user form modal -->

@endsection
