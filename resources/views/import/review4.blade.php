<div>
    <div class="row text-center">
        <h2><b>Nhập dữ liệu hoàn tất!</b></h2>
    </div>
    <div class="row text-center">
        <table class="table">
            <tbody>
            <tr class="danger row">
                <td class="col-sm-8 text-left">
                    <p class="text-danger-800"><b>Số bản ghi lỗi: {{$countWrongRecords}}</b></p>
                </td>
                <td class="col-sm-4 text-right">
                    @if($countWrongRecords > 0)
                        <a href="{{$wrongPath}}" onclick="return confirm('Bạn muốn tải file chứa các bản ghi lỗi?')">
                            <i class="icon-download"></i>
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="alert('Không có bản ghi lỗi!')">
                            <i class="icon-download"></i>
                        </a>
                    @endif
                </td>
            </tr>
            <tr class="warning row">
                <td class="col-sm-8 text-left">
                    <p class="text-warning-800"><b>Số bản ghi trùng: {{$countDuplicatedRecords}}</b></p>
                </td>
                <td class="col-sm-4 text-right">
                    @if($countDuplicatedRecords > 0)
                        <a href="{{$duplicatedPath}}" onclick="return confirm('Bạn muốn tải file chứa các bản ghi trùng?')">
                            <i class="icon-download"></i>
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="alert('Không có bản ghi trùng!')">
                            <i class="icon-download"></i>
                        </a>
                    @endif
                </td>
            </tr>
            <tr class="success row">
                <td class="col-sm-8 text-left">
                    <p class="text-success-800"><b>Số bản ghi mới: {{$countNewRecords}}</b></p>
                </td>
                <td class="col-sm-4 text-right">
                    @if($countNewRecords > 0)
                        <a href="{{$newPath}}" onclick="return confirm('Bạn muốn tải file chứa các bản ghi mới?')">
                            <i class="icon-download"></i>
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="alert('Không có bản ghi mới!')">
                            <i class="icon-download"></i>
                        </a>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
