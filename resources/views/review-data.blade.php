@extends('layout')
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
    @php
        $allTmpData = \Illuminate\Support\Facades\Session::get('allTmpData');
        $adminMode = \Illuminate\Support\Facades\Session::get('adminMode');
        $random_key = \Illuminate\Support\Facades\Session::get('random_key');
    @endphp
    <!-- Page header -->
        @if(isset($collection))
            <div class="page-header">
                <div class="page-header-content">
                    <div class="page-title">
                        <h4>Review dữ liệu khách từ file excel
                            <div class="display-inline-block right-submit-button">
                                <a href="{{url('review-data/submit/' . $collection . '/' . $random_key)}}"
                                   class="btn btn-primary">
                                    Xác nhận <i class="icon-check position-right"></i>
                                </a>
                            </div>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="page-content display-block">
                <!-- Basic datatable -->
                @if($collection == 'customer')
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Danh sách khách hàng</strong></h3>
                        </div>

                        <table class="table datatable-basic datatable-scroll-x overflow-auto customer-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Năm sinh</th>
                                <th>Giới tính</th>
                                <th>Công việc</th>
                                <th>Địa chỉ</th>
                                <th>Email</th>
                                <th>Số ĐT</th>
                                <th>Công ty</th>
                                <th>Người dùng</th>
                                <th>Trạng thái</th>
                                <th>Kiểm tra trùng</th>
                                <th>Tùy chọn</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($allTmpData as $customer)
                                <tr>
                                    <td>{{$customer->id}}</td>
                                    <td>{{$customer->name}}</td>
                                    <td>{{$customer->birth}}</td>
                                    <td>{{$customer->gender}}</td>
                                    <td>{{$customer->job}}</td>
                                    <td>{{$customer->address}}</td>
                                    <td>{{$customer->email}}</td>
                                    <td>{{$customer->phone}}</td>
                                    <td>{{$customer->company}}</td>
                                    <td>{{$customer->user}}</td>
                                    <td>
                                        @if($customer->status == 'Đang hoạt động')
                                            <span class="label label-success">{{$customer->status}}</span>
                                        @else
                                            <span class="label label-default">{{$customer->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($customer->duplicate)
                                            <span class="label label-warning">Trùng {{$customer->duplicate_key}}</span>
                                        @else
                                            <span class="label label-default">Không trùng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <button type="submit" class="delete-button text-left"
                                                            data-id="{{$customer->_id}}" data-collection="review-data">
                                                        <i class="icon-folder-remove"></i> Xóa
                                                    </button>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /basic datatable -->
                @elseif ($collection == 'order')
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Danh sách đơn hàng</strong></h3>
                        </div>

                        <table class="table datatable-basic datatable-scroll-x overflow-auto order-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Khách hàng</th>
                                <th>ID Công ty</th>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Thuế</th>
                                <th>Tổng giá</th>
                                <th>Thời gian</th>
                                <th>Địa chỉ</th>
                                <th>Người dùng</th>
                                <th>Trạng thái</th>
                                <th>Kiểm tra trùng</th>
                                <th>Tùy chọn</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($allTmpData as $order)
                                <tr>
                                    <td>{{$order->id}}</td>
                                    <td>{{$order->customer}}</td>
                                    <td>{{$order->company}}</td>
                                    <td>{{$order->products}}</td>
                                    <td>{{$order->price}}</td>
                                    <td>{{$order->tax}}</td>
                                    <td>{{$order->total_price}}</td>
                                    <td>{{$order->time}}</td>
                                    <td>{{$order->address}}</td>
                                    <td>{{$order->user}}</td>
                                    <td>
                                        @if($order->status == 'Đã hoàn thành')
                                            <span class="label label-success">{{$order->status}}</span>
                                        @else
                                            <span class="label label-default">{{$order->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->duplicate)
                                            <span class="label label-warning">Trùng {{$customer->duplicate_key}}</span>
                                        @else
                                            <span class="label label-default">Không trùng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <button type="submit" class="delete-button text-left"
                                                            data-id="{{$order->_id}}" data-collection="review-data">
                                                        <i class="icon-folder-remove"></i> Xóa
                                                    </button>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /basic datatable -->
                @elseif ($collection == 'company')
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Danh sách công ty</strong></h3>
                        </div>

                        <table class="table datatable-basic datatable-scroll-x overflow-auto company-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên Công ty</th>
                                <th>Mã Công ty</th>
                                <th>Lĩnh vực</th>
                                <th>Địa chỉ</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Người dùng</th>
                                <th>Trạng thái</th>
                                <th>Kiểm tra trùng</th>
                                <th>Tùy chọn</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($allTmpData as $company)
                                <tr>
                                    <td>{{$company->id}}</td>
                                    <td>{{$company->name}}</td>
                                    <td>{{$company->code}}</td>
                                    <td>{{$company->field}}</td>
                                    <td>{{$company->address}}</td>
                                    <td>{{$company->email}}</td>
                                    <td>{{$company->phone}}</td>
                                    <td>{{$company->user}}</td>
                                    <td>
                                        @if($company->status == 'Đang hoạt động')
                                            <span class="label label-success">{{$company->status}}</span>
                                        @else
                                            <span class="label label-default">{{$company->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->duplicate)
                                            <span class="label label-warning">Trùng {{$company->duplicate_key}}</span>
                                        @else
                                            <span class="label label-default">Không trùng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <button type="submit" class="delete-button text-left"
                                                            data-id="{{$company->_id}}" data-collection="review-data">
                                                        <i class="icon-folder-remove"></i> Xóa
                                                    </button>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /basic datatable -->
                @elseif ($collection == 'product')
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Danh sách sản phẩm</strong></h3>
                        </div>

                        <table class="table datatable-basic datatable-scroll-x overflow-auto product-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Mã sản phẩm</th>
                                <th>Giá</th>
                                <th>Hình ảnh</th>
                                <th>Còn lại</th>
                                <th>Người dùng</th>
                                <th>Trạng thái</th>
                                <th>Check trùng</th>
                                <th>Tùy chọn</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($allTmpData as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$product->name}}</td>
                                    <td>{{$product->code}}</td>
                                    <td>{{$product->price}}</td>
                                    <td>{{$product->image}}</td>
                                    <td>{{$product->remain}}</td>
                                    <td>{{$product->user}}</td>
                                    <td>
                                        @if($product->status == 'Có sẵn')
                                            <span class="label label-success">{{$product->status}}</span>
                                        @else
                                            <span class="label label-default">{{$product->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->duplicate)
                                            <span class="label label-warning">Trùng {{$product->duplicate_key}}</span>
                                        @else
                                            <span class="label label-default">Không trùng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <button type="submit" class="delete-button text-left"
                                                            data-id="{{$product->_id}}" data-collection="review-data">
                                                        <i class="icon-folder-remove"></i> Xóa
                                                    </button>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /basic datatable -->
                @endif

                <div class="bottom-submit-button text-center">
                    <a href="javascript:history.back()" class="btn btn-default">
                        <i class="icon-previous2"></i> Quay lại
                    </a>
                    <a href="{{url('review-data/submit/' . $collection . '/' . $random_key)}}"
                       class="btn btn-primary">
                        Xác nhận <i class="icon-check position-right"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
    @endphp

@endsection
