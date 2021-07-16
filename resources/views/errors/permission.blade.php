@extends('layout')
@section('content')
    @php
        use Illuminate\Support\Facades\Session;
        $message = Session::get('message');
        $errorMessage = Session::get('errorMessage');
        $currentUser = Session::get('currentUser');
    @endphp

    <div class="body text-center">
        @if(isset($errorMessage))<h1 class="text-bold">{{$errorMessage}}</h1>@endif
        <a href="{{url('home')}}" class="btn btn-default">Trang chủ</a>
        <a href="javascript:void(0)" onclick="window.history.back()" class="btn btn-default">Quay lại</a>
    </div>
@endsection
