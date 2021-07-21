@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading role-heading">
                    @if($collection && $collection == 'company')
                        <h3 class="display-inline-block">Công ty - </h3>
                    @elseif($collection && $collection == 'customer')
                        <h3 class="display-inline-block">Khách hàng - </h3>
                    @elseif($collection && $collection == 'order')
                        <h3 class="display-inline-block">Đơn hàng - </h3>
                    @elseif($collection && $collection == 'product')
                        <h3 class="display-inline-block">Sản phẩm - </h3>
                    @endif

                    <h3 class="panel-title display-inline-block"><strong>Danh sách các nhóm quyền</strong></h3>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li>
                                <a href="#" data-toggle="modal" data-target="#add_role_modal" class="check-form-change">
                                    Thêm nhóm quyền
                                    <i class="icon-add"></i>
                                </a>
                            </li>
                            <li><a data-action="collapse"></a></li>
                        </ul>
                    </div>
                </div>

                <form action="{{url('edit-roles-list/' . $collection)}}" method="post" class="edit-form">
                    @csrf
                    <table class="table role-table">
                        <thead>
                        <tr>
                            <th>Quyền</th>
                            @foreach($roles as $role)
                                <th class="text-center">{{$role->name}}
                                    @if(!$role['default'])
                                        <i class="fa fa-remove delete-role" data-id="{{$role->_id}}"></i>
                                        <a href="#" class="edit-role-button"
                                           data-id="{{$role->_id}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                        </thead>

                        <tbody class="tbody-role-table">
                        <tr>
                            <td>Quyền tạo dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][create]"
                                           @if(in_array('create', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền đọc dữ liệu liên quan đến mình</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][read]"
                                           @if(in_array('read', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền sửa dữ liệu liên quan đến mình</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][update]"
                                           @if(in_array('update', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền xóa dữ liệu liên quan đến mình</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][delete]"
                                           @if(in_array('delete', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền nhập (import) dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][import]"
                                           @if(in_array('import', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền xuất (export) dữ liệu liên quan đến mình</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][export]"
                                           @if(in_array('export', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền đọc toàn bộ dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][read_all]"
                                           @if(in_array('read_all', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền sửa toàn bộ dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][update_all]"
                                           @if(in_array('update_all', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền xóa toàn bộ dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][delete_all]"
                                           @if(in_array('delete_all', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Quyền xuất (export) toàn bộ dữ liệu</td>
                            @foreach($roles as $role)
                                <td class="text-center @if($role['default']) default-role @endif">
                                    <input type="checkbox" class="styled" name="role[{{$role->_id}}][export_all]"
                                           @if(in_array('export_all', $role->permission_list)) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                    <input readonly="readonly" name="collection" value="{{$collection}}" hidden>
                    <!-- /basic datatable -->
                    <div class="bottom-submit-button text-center">
                        <button type="submit" class="btn btn-primary">
                            Cập nhật <i class="icon-check position-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

    <!-- Add role form modal -->
    <div id="add_role_modal" class="modal fade" data-backdrop="static" data-changed="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm nhóm quyền mới</h5>
                </div>

                <form action="{{url('role/create')}}"
                      class="form-horizontal form-validate-jquery add-form" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên nhóm quyền <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="admin" class="form-control" name="name"
                                       required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Collection</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="collection" value="{{$collection}}"
                                       readonly="readonly">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Chọn các quyền</label>
                            <div class="col-sm-9">
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền tạo dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[create]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền đọc dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled" name="permission[read]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền sửa dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled" name="permission[update]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xóa dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled" name="permission[delete]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền nhập (import) dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[import]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xuất (export) dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled" name="permission[export]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền đọc toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[read_all]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền sửa toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[update_all]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xóa toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[delete_all]">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xuất (export) toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled" name="permission[export_all]">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                                                <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>

                        <button type="submit" class="btn btn-primary">Thêm nhóm quyền mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Add role form modal -->

    <!-- Edit role form modal -->
    <div id="edit_role_modal" class="modal fade" data-backdrop="static" data-changed="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Sửa thông tin nhóm quyền</h5>
                </div>

                <form action="" class="form-horizontal form-validate-jquery add-form" method="post"
                      enctype="multipart/form-data" id="edit_role_form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên nhóm quyền <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="admin" class="form-control" name="name"
                                       required="required" id="edit_role_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Collection</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="collection" value="{{$collection}}"
                                       readonly="readonly">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Chọn các quyền</label>
                            <div class="col-sm-9">
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền tạo dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[create]"
                                           id="permission_create" data-role="create">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền đọc dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[read]"
                                           id="permission_read" data-role="read">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền sửa dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[update]"
                                           id="permission_update" data-role="update">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xóa dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[delete]"
                                           id="permission_delete" data-role="delete">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền nhập (import) dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[import]"
                                           id="permission_import" data-role="import">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xuất (export) dữ liệu liên quan đến mình</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[export]"
                                           id="permission_export" data-role="export">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền đọc toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[read_all]"
                                           id="permission_read_all" data-role="read_all">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền sửa toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[update_all]"
                                           id="permission_update_all" data-role="update_all">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xóa toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[delete_all]"
                                           id="permission_delete_all" data-role="delete_all">
                                </div>
                                <div class="row permission-row">
                                    <div class="col-sm-6">Quyền xuất (export) toàn bộ dữ liệu</div>
                                    <input type="checkbox" class="styled edit-permission" name="permission[export_all]"
                                           id="permission_export_all" data-role="export_all">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                                                <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>

                        <button type="submit" class="btn btn-primary">Sửa thông tin nhóm quyền</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Edit role form modal -->

    {{--        <!-- Edit product form modal -->--}}
    {{--        <div id="edit_product_modal" class="modal fade" data-backdrop="static" data-changed="0">--}}
    {{--            <div class="modal-dialog modal-lg">--}}
    {{--                <div class="modal-content">--}}
    {{--                    <div class="modal-header">--}}
    {{--                        <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>--}}
    {{--                        <h5 class="modal-title">Thêm sản phẩm mới</h5>--}}
    {{--                    </div>--}}

    {{--                    <form action="" class="form-horizontal form-validate-jquery" method="post" enctype="multipart/form-data"--}}
    {{--                          id="edit_product_form">--}}
    {{--                        @csrf--}}
    {{--                        <div class="modal-body">--}}
    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Tên sản phẩm <span--}}
    {{--                                        class="text-danger">*</span></label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <input type="text" placeholder="Iphone X" class="form-control" name="name"--}}
    {{--                                           id="edit_product_name" required="required">--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Mã sản phẩm</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <input type='text' class="form-control" name="code" id="edit_product_code">--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Giá</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <input type="number" class="form-control" name="price" id="edit_product_price">--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Hình ảnh</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <input type="file" class="form-control" name="image" id="edit_product_image"--}}
    {{--                                           accept="image/x-png,image/gif,image/jpeg">--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Số lượng</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <input type="number" class="form-control" name="remain" id="edit_product_remain"--}}
    {{--                                           min="1">--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Người dùng</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <select class="select-search" multiple="multiple"--}}
    {{--                                            data-placeholder="Chọn người dùng..."--}}
    {{--                                            name="user_id" id="edit_product_user">--}}
    {{--                                        <option></option>--}}
    {{--                                        @foreach($allUsers as $user)--}}
    {{--                                            <option value="{{$user->_id}}">{{$user->name}}</option>--}}
    {{--                                        @endforeach--}}
    {{--                                    </select>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

    {{--                            <div class="form-group">--}}
    {{--                                <label class="control-label col-sm-3">Trạng thái</label>--}}
    {{--                                <div class="col-sm-9">--}}
    {{--                                    <select class="select-search" multiple="multiple" data-placeholder="Chọn trạng thái..."--}}
    {{--                                            name="status" id="edit_product_status">--}}
    {{--                                        <option></option>--}}
    {{--                                        <option value="Có sẵn">Có sẵn</option>--}}
    {{--                                        <option value="Không có sẵn">Không có sẵn</option>--}}
    {{--                                    </select>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}

    {{--                        <div class="modal-footer">--}}
    {{--                            <button type="button" class="btn btn-link" data-dismiss="modal">Đóng</button>--}}
    {{--                            <button type="submit" class="btn btn-primary">Cập nhật</button>--}}
    {{--                        </div>--}}
    {{--                    </form>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    <!-- / Add product form modal -->

@endsection
