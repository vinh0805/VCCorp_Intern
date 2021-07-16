<table class="table datatable-basic import-table" id="import_table">
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{$header}}</th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    <tr>
        @foreach($headers as $key => $header)
            <td>
                <select class="select-search field-in-db" data-placeholder="Chọn trường tương ứng"
                        name="fields_in_db[]">
                    <option></option>
                    <option value="">Chọn trường tương ứng</option>
                    @foreach($allFields as $field)
                        <option value="{{$field}}">{{$allLabels['labels'][$field]}}</option>
                    @endforeach
                </select>
            </td>
        @endforeach
    </tr>
    <tr>
        @foreach($headers as $key => $header)
            <td>
                <label>
                    <input type="checkbox" class="styled" name="check_field_duplicate[{{$key}}]">
                    Check trùng
                </label>
            </td>
        @endforeach
    </tr>
    <tr>
        @foreach($headers as $key => $header)
            <td>
                <label>
                    <input type="checkbox" class="styled" name="check_field_require[{{$key}}]">
                    Bắt buộc
                </label>
            </td>
        @endforeach
    </tr>

    @foreach($dataToShow as $data)
        <tr>
            @foreach($data as $value)
                <td>{{$value}}</td>
            @endforeach
        </tr>
    @endforeach

</table>
