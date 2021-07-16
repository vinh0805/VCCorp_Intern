<div class="table">
    <table class="table datatable-basic company-table">
        <thead>
        <tr>
            <th><input type="checkbox" class="styled check-all"></th>
            <th>ID</th>
            <th>Tên công ty</th>
            <th>Mã công ty</th>
            <th>Lĩnh vực</th>
            <th>Địa chỉ</th>
            <th>Email</th>
            <th>Số ĐT</th>
            <th>Người phụ trách</th>
            <th>Trạng thái</th>
            <th class="text-center">Tùy chọn</th>
        </tr>
        </thead>

        <tbody>
        @foreach($allCompanies as $company)
            <tr>
                <td><input type="checkbox" class="styled check-one" data-id="{{$company->_id}}"></td>
                <td>{{$company->id}}</td>
                <td>{{$company->name}}</td>
                <td>{{$company->code}}</td>
                <td>{{$company->field}}</td>
                <td>{{$company->address}}</td>
                <td>{{$company->email}}</td>
                <td>{{$company->phone}}</td>
                <td>@foreach($company->userName as $key => $userName)
                        @if($key == array_key_last($company->userName))
                            {{$userName}}
                        @else
                            {{$userName}},
                        @endif
                    @endforeach
                </td>
                <td>
                    @if ($company->status == "Đang hoạt động")
                        <span class="label label-success">{{$company->status}}</span>
                    @elseif ($company->status == "Không hoạt động")
                        <span class="label label-default">{{$company->status}}</span>
                    @endif
                </td>
                <td class="text-center">
                    @if(isset($permissionList) &&
                        (in_array('update', $permissionList) || in_array('delete', $permissionList)))
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
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
                                                    data-id="{{$company->_id}}" data-collection="company">
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
        {{ $allCompanies->links() }}
    </div>

</div>
