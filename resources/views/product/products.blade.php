@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách sản phẩm</strong></h3>
                    <p class="total-record">Tìm thấy {{$allProducts->total()}} bản ghi</p>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            @if(isset($permissionList) && in_array('create', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#add_product_modal" class="check-form-change">
                                        <i class="icon-add" title="Thêm sản phẩm mới"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && in_array('import', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#import_modal"
                                       class="import-product-button check-form-change">
                                        <i class="icon-import" title="Nhập dữ liệu sản phẩm"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('export', $permissionList) || in_array('export_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="export-button check-form-change" data-collection="product"
                                       data-toggle="modal" data-target="#export_modal">
                                        <i class="icon-database-export" title="Xuất dữ liệu sản phẩm"></i>
                                    </a>
                                </li>
                            @endif
                            @if(isset($permissionList) && (in_array('delete', $permissionList) || in_array('delete_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="delete-all-button" data-collection="product">
                                        <i class="icon-folder-remove" title="Xóa các bản ghi đã chọn"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="search">
                    <form action="{{url('product/search')}}"
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
                            <a href="{{url('/products')}}">
                                <div class="btn btn-default">Xóa bộ lọc</div>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="datatable-container">
                    <table class="table datatable-basic product-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox" class="styled check-all" data-direct="1"></th>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã sản phẩm</th>
                            <th>Giá</th>
                            <th>Còn lại</th>
                            <th>Ảnh</th>
                            <th>Người phụ trách</th>
                            <th>Trạng thái</th>
                            <th>Thời điểm tạo</th>
                            <th>Thời điểm chỉnh sửa</th>
                            <th class="text-center">Tùy chọn</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($allProducts as $key => $product)
                            <tr>
                                <td><input type="checkbox" class="styled check-one" data-id="{{$product->_id}}"></td>
                                <td>{{ ($allProducts->currentPage() - 1)*10 + $key + 1}}</td>
                                <td>{{$product->name}}</td>
                                <td>{{$product->code}}</td>
                                <td>{{number_format($product->price)}}</td>
                                <td>{{$product->remain}}</td>
                                @if($product->image)
                                    <td><img class="product-image" src="{{url('storage/images/' . $product->image)}}">
                                    </td>
                                @else
                                    <td><img class="product-image" src="{{url('storage/images/default.png')}}"></td>
                                @endif
                                <td class="user-list">
                                    <p @if(strlen($product['userName']) > 50) class="hide-overflow"
                                       title="{{$product['userName']}}" @endif>
                                        {{$product->userName}}
                                    </p>
                                </td>
                                <td>
                                    @if($product->status == 'Có sẵn')
                                        <span class="label label-success">{{$product->status}}</span>
                                    @else
                                        <span class="label label-default">{{$product->status}}</span>
                                    @endif
                                </td>
                                <td title="{{$product->created_at->format('Y-m-d h:i:s.u')}}">
                                    {{$product->created_at->format('Y-m-d h:i:s')}}
                                </td>
                                <td title="{{$product->updated_at->format('Y-m-d h:i:s.u')}}">
                                    {{$product->updated_at->format('Y-m-d h:i:s')}}
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
                                                            <a href="#" class="edit-product-button"
                                                               data-id="{{$product->_id}}">
                                                                <i class="icon-pencil"></i> Sửa thông tin
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if(in_array('delete', $permissionList))
                                                        <li>
                                                            <button type="submit" class="delete-button text-left"
                                                                    data-id="{{$product->_id}}"
                                                                    data-collection="product">
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
                        {{ $allProducts->appends(['search_field' => isset($search_field) ? $search_field : '', 'search_value' => $search_value])->links() }}
                    @else
                        {{ $allProducts->links() }}
                    @endif
                </div>
            </div>

            <!-- /basic datatable -->

        </div>
    </div>
    <label>
        <input type="text" class="total-records" hidden value="{{$allProducts->total()}}" readonly="readonly">
    </label>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

    <!-- Add product form modal -->
    <div id="add_product_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                </div>

                <form action="{{url('product/create')}}" data-changed="0"
                      class="form-horizontal validate-form add-form" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên sản phẩm
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Iphone X" class="form-control" name="name"
                                       required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Mã sản phẩm</label>
                            <div class="col-sm-9">
                                <input type='text' class="form-control" name="code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Giá (VND)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="price">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Hình ảnh</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="image" id="exampleInputFile"
                                       accept="image/x-png,image/gif,image/jpeg">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số lượng
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="remain" min="1" value="1">
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
                                <select class="select-search" data-placeholder="Chọn trạng thái..." name="status">
                                    <option></option>
                                    <option value="Có sẵn">Có sẵn</option>
                                    <option value="Không có sẵn">Không có sẵn</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                                                <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>

                        <button type="submit" class="btn btn-primary">Thêm sản phẩm mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Add product form modal -->

    <!-- Edit product form modal -->
    <div id="edit_product_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                </div>

                <form action="" class="form-horizontal validate-form2 edit-form" method="post"
                      enctype="multipart/form-data" id="edit_product_form" data-changed="0">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">Tên sản phẩm
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Iphone X" class="form-control" name="name"
                                       id="edit_product_name" required="required">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Mã sản phẩm</label>
                            <div class="col-sm-9">
                                <input type='text' class="form-control" name="code" id="edit_product_code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Giá (VND)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="price" id="edit_product_price">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Hình ảnh</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="image" id="edit_product_image"
                                       accept="image/x-png,image/gif,image/jpeg">
                                <input type="text" class="form-control" name="image2" id="edit_product_image2"
                                       accept="image/x-png,image/gif,image/jpeg" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Số lượng
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="remain" id="edit_product_remain"
                                       min="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-3">Người phụ trách</label>
                            <div class="col-sm-9">
                                <select class="select-search" multiple="multiple" data-placeholder="Chọn người dùng..."
                                        name="users[]" id="edit_product_user">
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
                                <select class="select-search" data-placeholder="Chọn trạng thái..." name="status"
                                        id="edit_product_status">
                                    <option></option>
                                    <option value="Có sẵn">Có sẵn</option>
                                    <option value="Không có sẵn">Không có sẵn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                                                <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Edit product form modal -->

@endsection
