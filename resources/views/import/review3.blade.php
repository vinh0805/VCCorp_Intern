<table class="table datatable-basic import-table2"
       id="import_table">
    <thead>
    <tr>
        <th></th>
        <th>Kết quả xử lý</th>
        @foreach($fields_in_db as $field)
            @if(isset($field) && $field != "")
                <th>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="javascript:void(0)" type="button" id="dropdownMenu1"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            {{$field}}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li>
                                <a class="dropdown-item set-data-type" data-field="{{$field}}"
                                   data-type="default" href="javascript:void(0)">Lấy tất cả mặc định</a>
                            </li>
                            <li>
                                <a class="dropdown-item set-data-type" data-field="{{$field}}"
                                   data-type="new" href="javascript:void(0)">Lấy tất cả giá trị mới</a>
                            </li>
                            <li>
                                <a class="dropdown-item set-data-type" data-field="{{$field}}"
                                   data-type="old" href="javascript:void(0)">Lấy tất cả giá trị cũ</a>
                            </li>
                        </ul>
                    </div>
                </th>
            @endif
        @endforeach
    </tr>
    </thead>

    <tbody>

    @foreach($allTmpData as $tmpData)
        <tr id="tmp_{{$tmpData->_id}}" data-id="{{$tmpData->_id}}">
            <td>
                <i class="icon-folder-remove remove-review-data"></i>
            </td>
            <td>
                @if(isset($tmpData->wrong_format) && count($tmpData->wrong_format) > 0)
                    <label class="label label-danger">Sai dữ liệu:</label>
                    <label class="label label-danger">{{implode(', ', $tmpData->wrong_format)}}</label>
                @elseif(isset($tmpData->required_fields) && count($tmpData->required_fields) > 0)
                    <label class="label label-danger">Thiếu dữ liệu:</label>
                    <label class="label label-danger">{{implode(', ', $tmpData->required_fields)}}</label>
                @elseif(isset($tmpData->duplicated_fields) && count($tmpData->duplicated_fields) > 0)
                    <label class="label label-warning">Trùng:</label>
                    <label class="label label-warning">{{implode(', ', $tmpData->duplicated_fields)}}</label>
                @else
                    <label class="label label-success">Thêm mới</label>
                @endif
            </td>

            @foreach($fields_in_db as $key => $field)
                @if(isset($field) && $field != "")
                    @if(isset($tmpData->duplicated_fields) && count($tmpData->duplicated_fields) > 0)
                        <td>
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="javascript:void(0)" type="button" id="dropdownMenu1"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    @if(isset($default_fields) && in_array($field, $default_fields))
                                        {{$defaultData[$field]}}
                                    @elseif(isset($old_fields) && in_array($field, $old_fields))
                                        {{$tmpData['duplicated_record'][$field]}}
                                    @elseif(isset($new_fields) && in_array($field, $new_fields))
                                        {{$tmpData[$field]}}
                                    @else
                                        {{$tmpData[$field]}}
                                    @endif
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li>
                                        <a class="dropdown-item set-data-type2" data-field="{{$field}}"
                                           data-value="{{$defaultData[$field]}}" data-key="{{$tmpData['_id']}}"
                                           data-type="default" href="javascript:void(0)">
                                            Lấy giá trị mặc định ({{$defaultData[$field]}})</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item set-data-type2" data-field="{{$field}}"
                                           data-value="{{$tmpData[$field]}}" data-key="{{$tmpData['_id']}}"
                                           data-type="new" href="javascript:void(0)">
                                            Lấy giá trị mới ({{$tmpData[$field]}})</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item set-data-type2" data-field="{{$field}}"
                                           data-value="{{@$tmpData['duplicated_record'][$field]}}"
                                           data-key="{{$tmpData['_id']}}" data-type="old" href="javascript:void(0)">
                                            Lấy giá trị cũ ({{@$tmpData['duplicated_record'][$field]}})</a>
                                    </li>
                                </ul>
                            </div>
                            <input name="records[{{$tmpData->_id}}][{{$field}}]" value="" readonly="readonly" hidden>
                        </td>
                    @else
                        <td>{{$tmpData[$field]}}</td>
                    @endif

                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

<div class="text-center">
    {{$allTmpData->links()}}
</div>


