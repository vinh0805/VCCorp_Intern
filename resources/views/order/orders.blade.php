@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-content display-block">
            <!-- Basic datatable -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Danh sách đơn hàng</strong></h3>
                    <p class="total-record">Tìm thấy {{$allOrders->total()}} bản ghi</p>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            @if(isset($permissionList) && in_array('create', $permissionList))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#add_order_modal" class="check-form-change">
                                        <i class="icon-add" title="Thêm đơn hàng mới"></i>
                                    </a>
                                </li>
                            @endif
                            {{--                            @if(isset($permissionList) && in_array('import', $permissionList))--}}
                            {{--                                <li>--}}
                            {{--                                    <a href="#" data-toggle="modal" data-target="#import_modal"--}}
                            {{--                                       class="import-order-button">--}}
                            {{--                                        <i class="icon-import"></i>--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}
                            {{--                            @if(isset($permissionList) && (in_array('export', $permissionList) || in_array('export_all', $permissionList)))--}}
                            {{--                                <li>--}}
                            {{--                                    <a href="javascript:void(0)" class="export-button" data-collection="order">--}}
                            {{--                                        <i class="icon-database-export"></i>--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}
                            @if(isset($permissionList) && (in_array('delete', $permissionList) || in_array('delete_all', $permissionList)))
                                <li>
                                    <a href="javascript:void(0)" class="delete-all-button" data-collection="order">
                                        <i class="icon-folder-remove" title="Xóa các bản ghi đã chọn"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="search">
                    <form action="{{url('order/search')}}"
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
                            <a href="{{url('/orders')}}">
                                <div class="btn btn-default">Xóa bộ lọc</div>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="datatable-container">
                    <table class="table datatable-basic order-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox" class="styled check-all" data-direct="1"></th>
                            <th>STT</th>
                            <th>Khách hàng</th>
                            <th>Công ty</th>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Thuế (%)</th>
                            <th>Tổng giá</th>
                            <th>Ngày đặt hàng</th>
                            <th>Địa chỉ</th>
                            <th>Người phụ trách</th>
                            <th>Trạng thái</th>
                            <th>Thời điểm tạo</th>
                            <th>Thời điểm chỉnh sửa</th>
                            <th class="text-center">Tùy chọn</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($allOrders as $key => $order)
                            <tr>
                                <td><input type="checkbox" class="styled check-one" data-id="{{$order->_id}}"></td>
                                <td>{{ ($allOrders->currentPage() - 1)*10 + $key + 1}}</td>
                                @if(isset($order->customer['name']) && isset($order->customer))
                                    <td>
                                        <a class="customer-modal" data-customer="{{ json_encode($order['customer']) }}"
                                           data-toggle="modal"
                                           data-target="#customer_modal" href="#">{{$order->customer['name']}}</a>
                                @else
                                    <td></td>
                                @endif
                                <td>
                                    <a href="#" class="company-modal" data-company="{{ json_encode($order->company) }}"
                                       data-toggle="modal"
                                       data-target="#company_modal" data-title="{{$order->name}}">
                                        @if(isset($order->company['name'])) {{$order->company['name']}} @endif
                                    </a>
                                </td>
                                <td>
                                    @if(isset($order->products) && is_array($order->products))
                                        <a href="#" class="products-modal" data-id="{{$order->_id}}">
                                            {{count($order->products)}} sản phẩm
                                        </a>
                                    @endif
                                </td>
                                <td>{{number_format($order->price)}}</td>
                                <td>{{$order->tax}}</td>
                                <td>{{number_format($order->total_price)}}</td>
                                <td>{{$order->time}}</td>
                                <td>{{$order->address}}</td>
                                <td class="user-list">
                                    <p @if(strlen($order['userName']) > 50) class="hide-overflow"
                                       title="{{$order['userName']}}" @endif>
                                        {{$order->userName}}
                                    </p>
                                </td>
                                <td>
                                    @if($order->status == 'Đã hoàn thành')
                                        <span class="label label-success">{{$order->status}}</span>
                                    @else
                                        <span class="label label-default">{{$order->status}}</span>
                                    @endif
                                </td>
                                <td title="{{$order->created_at->format('Y-m-d h:i:s.u')}}">
                                    {{$order->created_at->format('Y-m-d h:i:s')}}</td>
                                <td title="{{$order->updated_at->format('Y-m-d h:i:s.u')}}">
                                    {{$order->updated_at->format('Y-m-d h:i:s')}}
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
                                                            <a href="javascript:void(0)"
                                                               class="edit-order-button" data-id="{{$order->_id}}">
                                                                <i class="icon-pencil"></i> Sửa thông tin
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if(in_array('delete', $permissionList))
                                                        <li>
                                                            <button type="submit" class="delete-button text-left"
                                                                    data-id="{{$order->_id}}" data-collection="order">
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
                        {{ $allOrders->appends(['search_field' => isset($search_field) ? $search_field : '', 'search_value' => $search_value])->links() }}
                    @else
                        {{ $allOrders->links() }}
                    @endif
                </div>
            </div>
            <!-- /basic datatable -->
        </div>
    </div>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

    <!-- Add order form modal -->
    <div id="add_order_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Thêm đơn hàng mới</h5>
                </div>

                <form action="{{url('order/create')}}" data-changed="0"
                      class="form-horizontal validate-form add-form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Khách hàng:
                                <span class="text-danger" title="Cần phải nhập 1 trong 2 trường sản phẩm hoặc công ty">(*)</span>
                            </label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn khách hàng..." name="customer">
                                    <option></option>
                                    @foreach($allCustomers as $customer)
                                        <option value="{{$customer->_id}}">{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Công ty:
                                <span class="text-danger" title="Cần phải nhập 1 trong 2 trường sản phẩm hoặc công ty">(*)</span>
                            </label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn công ty..." name="company">
                                    <option></option>
                                    @foreach($allCompanies as $company)
                                        <option value="{{$company->_id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Ngày đặt hàng</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control daterange-single" value="03/18/2021"
                                           name="time">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="add_product_list" data-id="1">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4 product-label">Sản phẩm:
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-8 select-product-order">
                                    <select class="select-search" data-placeholder="Chọn sản phẩm..."
                                            name="product[]" required="required">
                                        <option></option>
                                        @foreach($allProducts as $product)
                                            <option value="{{$product->_id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label col-sm-3">Số lượng</label>
                                <div class="col-sm-7">
                                    <input type='number' class="form-control" name="number[]" value="1" min="1">
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button type="button" data-id="1" class="btn btn-success add-product-order-button"
                                            data-products="{{json_encode($allProducts)}}">
                                        <i class="icon-add"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="add_product_list2">

                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Thuế (%):</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="tax" value="0" min="0" max="99">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Địa chỉ</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Người phụ trách</label>
                            <div class="col-sm-10">
                                <select class="select-search" multiple="multiple"
                                        data-placeholder="Chọn người dùng..."
                                        name="users[]">
                                    <option></option>
                                    @foreach($allUsers as $user)
                                        <option value="{{$user->_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Trạng thái</label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn trạng thái..." name="status">
                                    <option></option>
                                    <option value="Đã hoàn thành">Đã hoàn thành</option>
                                    <option value="Chưa hoàn thành">Chưa hoàn thành</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Add order form modal -->


    <!-- Edit order form modal -->
    <div id="edit_order_modal" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-modal"><i class="fa fa-close"></i></button>
                    <h5 class="modal-title">Sửa thông tin đơn hàng</h5>
                </div>

                <form action="{{url('order/edit')}}" data-changed="0"
                      id="edit_order_form" class="form-horizontal validate-form edit-form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Khách hàng:
                                <span class="text-danger" title="Cần phải nhập 1 trong 2">(*)</span>
                            </label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn khách hàng..." name="customer"
                                        id="edit_order_customer">
                                    <option></option>
                                    @foreach($allCustomers as $customer)
                                        <option value="{{$customer->_id}}">{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Công ty:
                                <span class="text-danger" title="Cần phải nhập 1 trong 2">(*)</span>
                            </label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn công ty..." name="company"
                                        id="edit_order_company">
                                    <option></option>
                                    @foreach($allCompanies as $company)
                                        <option value="{{$company->_id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Ngày đặt hàng</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control daterange-single" value="03/18/2021"
                                           name="time" id="edit_order_time">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="edit_product_list" data-id="1">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-4 product-label">Sản phẩm:
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-8 select-product-order">
                                    <select class="select-search" data-placeholder="Chọn sản phẩm..."
                                            name="product[]" id="edit_product_01" required="required">
                                        <option></option>
                                        @foreach($allProducts as $product)
                                            <option value="{{$product->_id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label col-sm-3">Số lượng</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control" name="number[]" min="1"
                                           id="edit_product_number_01">
                                </div>

                                <div class="col-sm-2 text-right">
                                    <button type="button" data-id="1" class="btn btn-success add-product-order-button2"
                                            data-products="{{json_encode($allProducts)}}">
                                        <i class="icon-add"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="edit_product_list2">

                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Thuế (%):</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="tax" value="0" min="0" max="99"
                                       id="edit_order_tax">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Địa chỉ</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="Hà Nội" class="form-control" name="address"
                                       id="edit_order_address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Người phụ trách</label>
                            <div class="col-sm-10">
                                <select class="select-search" multiple="multiple" data-placeholder="Chọn người dùng..."
                                        name="users[]" id="edit_order_user">
                                    <option></option>
                                    @foreach($allUsers as $user)
                                        <option value="{{$user->_id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2">Trạng thái</label>
                            <div class="col-sm-10">
                                <select class="select-search" data-placeholder="Chọn trạng thái..." name="status"
                                        id="edit_order_status">
                                    <option></option>
                                    <option value="Đã hoàn thành">Đã hoàn thành</option>
                                    <option value="Chưa hoàn thành">Chưa hoàn thành</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link close-modal2" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-link close-modal">Đóng</button>

                        <button type="submit" class="btn btn-primary">Lưu đơn hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Add order form modal -->

@endsection
