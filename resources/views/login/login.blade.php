@extends('login-layout')
@section('content')
    <div class="login-box text-center">
        <div class="panel">
            <div class="panel-heading text-center">
                <h3>Đăng nhập</h3>
                <?php
                use Illuminate\Support\Facades\Session;
                $message = Session::get('message');
                if($message) {
                    echo '<div id="loginError" class="text-danger-800"><strong>' . $message . '</strong></div>';
                    Session::put('message', null);
                }
                ?>
            </div>

            <div class="panel-body">
                <form action="{{url('login-confirm')}}" class="validate-form-sign-in" method="post">
                    @csrf
                    <div class="form-group row">
                        <label class="control-label col-sm-3 text-left"><strong>Email:</strong></label>
                        <input type="email" class="col-sm-6 border" name="email">
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-sm-3 text-left"><strong>Mật khẩu:</strong></label>
                        <input type="password" class="col-sm-6" name="password">
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary">Đăng nhập <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </form>
            </div>

            <div class="panel-footer">
                <div>Bạn chưa có tài khoản? <a href="{{url('/signup')}}">Đăng ký</a></div>
            </div>
        </div>
    </div>

@endsection
