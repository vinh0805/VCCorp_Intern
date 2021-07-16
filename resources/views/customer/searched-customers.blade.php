<div class="table">
    <table class="table datatable-basic customer-table">
        <thead>
        <tr>
            <th><input type="checkbox" class="styled check-all"></th>
            <th>ID</th>
            <th>Tên</th>
            <th>Tuổi</th>
            <th>Giới tính</th>
            <th>Công việc</th>
            <th>Địa chỉ</th>
            <th>Email</th>
            <th>Số ĐT</th>
            <th>Công ty</th>
            <th>Người phụ trách</th>
            <th>Trạng thái</th>
            <th class="text-center">Tùy chọn</th>
        </tr>
        </thead>

        <tbody>
        @foreach($allCustomers as $customer)
            <tr>
                <td><input type="checkbox" class="styled check-one" data-id="{{$customer->_id}}"></td>
                <td>{{$customer->id}}</td>
                <td>{{$customer->name}}</td>
                <td>{{$customer->age}}</td>
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
                <td>@foreach($customer->userName as $key => $userName)
                        @if($key == array_key_last($customer->userName))
                            {{$userName}}
                        @else
                            {{$userName}},
                        @endif
                    @endforeach
                </td>
                <td>
                    @if($customer->status == 'Đang hoạt động')
                        <span class="label label-success">{{$customer->status}}</span>
                    @else
                        <span class="label label-default">{{$customer->status}}</span>
                    @endif
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
                                               class="edit-customer-button" data-id="{{$customer->_id}}">
                                                <i class="icon-pencil"></i> Sửa thông tin
                                            </a>
                                        </li>
                                    @endif
                                    @if(in_array('delete', $permissionList))
                                        <li>
                                            <button class="delete-button text-left"
                                                    data-id="{{$customer->_id}}" data-collection="customer">
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
    <div class="text-center">
        {{ $allCustomers->links() }}
    </div>

</div>
