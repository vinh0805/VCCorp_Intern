@extends('layout')
@section('content')
    @php
        $message = \Illuminate\Support\Facades\Session::get('message');
        $errorMessage = \Illuminate\Support\Facades\Session::get('errorMessage');
    @endphp

    <!-- Main content -->
    <div class="content-wrapper">
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách công ty</strong></h3>
                    <p class="total-record">Tìm thấy {{$allCompanies->total()}} bản ghi</p>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            @if(isset($permissionList) && in_array('create', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#add_company_modal" class="check-form-change">
                                        <i class="icon-add" title="Thêm công ty mới"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && in_array('import', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#import_modal"
                                       class="import-company-button check-form-change">
                                        <i class="icon-import" title="Nhập dữ liệu công ty"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('export', $permissionList) || in_array('export_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="export-button check-form-change" data-collection="company"
                                       data-toggle="modal" data-target="#export_modal">
                                        <i class="icon-database-export" title="Xuất dữ liệu công ty"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('delete', $permissionList) || in_array('delete_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="delete-all-button" data-collection="company">
                                        <i class="icon-folder-remove" title="Xóa các bản ghi đã chọn"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="search">
                    <form action="{{url('company/search')}}"
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
                            <a href="{{url('/companies')}}">
                                <div class="btn btn-default">Xóa bộ lọc</div>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="datatable-container">
                    <table class="table datatable-basic company-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox" class="styled check-all" data-direct="1"></th>
                            <th>STT</th>
                            <th>Tên công ty</th>
                            <th>Mã công ty</th>
                            <th>Lĩnh vực</th>
                            <th>Địa chỉ</th>
                            <th>Email</th>
                            <th>Số ĐT</th>
                            <th>Người phụ trách</th>
                            <th>Trạng thái</th>
                            <th>Thời điểm tạo</th>
                            <th>Thời điểm chỉnh sửa</th>
                            <th class="text-center">Tùy chọn</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($allCompanies as $key => $company)
                            <tr>
                                <td><input type="checkbox" class="styled check-one" data-id="{{$company->_id}}"></td>
                                <td>{{ ($allCompanies->currentPage() - 1)*10 + $key + 1}}</td>
                                <td>
                                    <a href="javascript:void(0)" class="edit-company-button"
                                       data-id="{{$company->_id}}">
                                        {{$company->name}}
                                    </a>
                                </td>
                                <td>{{$company->code}}</td>
                                <td>{{$company->field}}</td>
                                <td>{{$company->address}}</td>
                                <td>{{$company->email}}</td>
                                <td>{{$company->phone}}</td>
                                <td class="user-list">
                                    <p @if(strlen($company['userName']) > 50) class="hide-overflow"
                                       title="{{$company['userName']}}" @endif>
                                        {{$company->userName}}
                                    </p>
                                </td>
                                <td>
                                    @if ($company->status == "Đang hoạt động")
                                        <span class="label label-success">{{$company->status}}</span>
                                    @elseif ($company->status == "Không hoạt động")
                                        <span class="label label-default">{{$company->status}}</span>
                                    @endif
                                </td>
                                <td title="{{$company->created_at->format('Y-m-d h:i:s.u')}}">
                                    {{$company->created_at->format('Y-m-d h:i:s')}}
                                </td>
                                <td title="{{$company->updated_at->format('Y-m-d h:i:s.u')}}">
                                    {{$company->updated_at->format('Y-m-d h:i:s')}}
                                </td>
                                <td class="text-center">
                                    @if(isset($permissionList) &&
                                        (in_array('update', $permissionList) || in_array('delete', $permissionList)))
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="javascript:void(0)" class="dropdown-toggle"
                                                   data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @if(in_array('update', $permissionList))
                                                        <li>
                                                            <a href="javascript:void(0)" class="edit-company-button"
                                                               data-id="{{$company->_id}}">
                                                                <i class="icon-pencil"></i> Sửa thông tin
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if(in_array('delete', $permissionList))
                                                        <li>
                                                            <button class="delete-button text-left"
                                                                    data-id="{{$company->_id}}"
                                                                    data-collection="company">
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
                <div class="text-center">
                    @if(isset($search_value))
                        {{ $allCompanies->appends(['search_field' => isset($search_field) ? $search_field : '', 'search_value' => $search_value])->links() }}
                    @else
                        {{ $allCompanies->links() }}
                    @endif
                </div>
            </div>
            <!-- /basic datatable -->
        </div>
    </div>

    <label>
        <input type="text" class="total-records" hidden value="{{$allCompanies->total()}}" readonly="readonly">
    </label>
    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

    <!-- Add company form modal -->
    <div id="add_company_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm công ty mới</h5>
                </div>

                <form action="{{url('/company/create')}}" class="form-horizontal validate-form add-form" method="post"
                      id="add_company_form" data-changed="0">
                    @csrf

                    <input type="text" class="form-control check-confirm" name="confirm" value="0">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên công ty <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Mã công ty</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="MBB, MWG,..." class="form-control" name="code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Lĩnh vực</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Bất động sản, bán lẻ,..."
                                       class="form-control" name="field">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Địa chỉ</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="099 999 9999" data-mask="099 999 9999"
                                       class="form-control" name="phone">
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
    <!-- / Add company form modal -->

    <!-- Edit company form modal -->
    <div id="edit_company_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Sửa thông tin công ty</h5>
                </div>

                <form action="{{url('/company/edit')}}" data-changed="0"
                      id="edit_company_form" class="form-horizontal validate-form edit-form" method="post">
                    @csrf

                    <input type="text" class="form-control check-confirm" name="confirm" value="0">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên công ty <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name"
                                       id="edit_company_name" required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Mã công ty</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="MBB, MWG,..." class="form-control" name="code"
                                       id="edit_company_code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Lĩnh vực</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Bất động sản, bán lẻ,..."
                                       class="form-control" name="field" id="edit_company_field">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Địa chỉ</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address"
                                       id="edit_company_address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Email</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="name@domain.com" class="form-control" name="email"
                                       id="edit_company_email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="099 999 9999" data-mask="099 999 9999"
                                       class="form-control" name="phone" id="edit_company_phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Người phụ trách</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" id="edit_company_user"
                                        data-placeholder="Chọn người dùng..." name="users[]">
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
                                        name="status" id="edit_company_status">
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
    <!-- / Edit company form modal -->
@endsection
