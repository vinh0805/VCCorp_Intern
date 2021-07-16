@extends('login-layout')
@section('content')
    <div class="login-box text-center">
        <div class="panel">
            <div class="panel-heading text-center">
                <h3>Đăng ký</h3>
                <?php
                use Illuminate\Support\Facades\Session;
                $message = Session::get('message');
                if ($message) {
                    echo '<div id="loginError" class="text-danger-800"><strong>' . $message . '</strong></div>';
                    Session::put('message', null);
                }
                ?>
            </div>

            <div class="panel-body">
                <form action="{{url('signup-submit')}}" method="post" class="validate-form-2">
                    {{csrf_field()}}
                    <div class="form-group row signup-row">
                        <label class="control-label col-sm-3 text-left"><strong>Tên của bạn:</strong></label>
                        <input type="text" class="col-sm-9 border" name="name">
                    </div>
                    <div class="form-group row signup-row">
                        <label class="control-label col-sm-3 text-left"><strong>Email:</strong></label>
                        <input type="email" class="col-sm-9 border" name="email">
                    </div>
                    <div class="form-group row signup-row">
                        <label class="control-label col-sm-3 text-left"><strong>Mật khẩu:</strong></label>
                        <input type="password" class="col-sm-9" name="password" id="password">
                    </div>
                    <div class="form-group row signup-row">
                        <label class="control-label col-sm-3 text-left"><strong>Nhập lại mật khẩu:</strong></label>
                        <input type="password" class="col-sm-9" name="confirm_password" id="confirm_password"
                              >
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary">Đăng ký <i
                                class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </form>
            </div>

            <div class="panel-footer">
                <div>Bạn đã có tài khoản? <a href="{{url('/login')}}">Đăng nhập</a></div>
            </div>
        </div>
    </div>

@endsection
