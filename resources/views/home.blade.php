@extends('layout')
@section('content')

    @php
        $currentUser = \Illuminate\Support\Facades\Session::get('currentUser');
        try {
            $currentUser['email'] = \Illuminate\Support\Facades\Crypt::decrypt($currentUser['email']);
        } catch (Exception $e) {

        }
    @endphp

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Thông
                        tin cá nhân</h4>
                </div>
            </div>
        </div>
        <div class="page-content display-block">
            <!-- Basic layout-->
            <form action="{{url('/save-info')}}" method="post" enctype="multipart/form-data" id="info_form">
                @csrf
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">Thông tin cá nhân</h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label>ID:</label>
                            <input type="text" class="form-control" value="{{$currentUser->_id}}" disabled
                                   readonly="readonly">
                        </div>
                        <div class="form-group">
                            <label>Tên:</label>
                            <input type="text" class="form-control" value="{{$currentUser->name}}" name="name">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="text" class="form-control" value="{{$currentUser->email}}" name="email"
                                   disabled readonly="readonly">
                        </div>
                        <div class="form-group">
                            <label class="display-block">Giới tính:</label>
                            <label class="radio-inline">
                                <input type="radio" class="styled" name="gender" value="Nam"
                                       @if($currentUser->gender === "Nam") checked @endif>
                                Nam
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="styled" name="gender" value="Nữ"
                                       @if($currentUser->gender === "Nữ") checked @endif>
                                Nữ
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="styled" name="gender" value="Khác"
                                       @if($currentUser->gender === "Khác") checked @endif>
                                Khác
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại:</label>
                            <input type="text" class="form-control" value="{{$currentUser->phone}}" name="phone">
                        </div>
                        <div class="form-group">
                            <label>Ảnh đại diện:</label>
                            <input type="file" class="form-control" id="exampleInputFile" name="avatar"
                                   accept="image/x-png,image/gif,image/jpeg">
                            @if(isset($currentUser['avatar']))
                                <input type="text" class="form-control current-avatar" name="image2" value="Avatar hiện tại: {{$currentUser['avatar']}}"
                                       accept="image/x-png,image/gif,image/jpeg" readonly disabled>
                            @endif
                            <span class="help-block">Loại file: gif, png, jpg. Tối đa 2Mb</span>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                Lưu thông tin <i class="icon-arrow-right14 position-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /basic layout -->
        </div>
    </div>
@endsection
