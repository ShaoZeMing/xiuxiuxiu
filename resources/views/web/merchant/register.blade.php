<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('merchant.title')}} | {{ trans('merchant.register') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/font-awesome/css/font-awesome.min.css") }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/dist/css/AdminLTE.min.css") }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/plugins/iCheck/square/blue.css") }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="{{ merchant_base_path('/') }}"><b>注册商家</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">{{ trans('merchant.register') }}</p>

    <form action="{{ merchant_base_path('auth/register') }}" method="post">
      <div class="form-group has-feedback {!! !$errors->has('mobile') ?: 'has-error' !!}">
        @if($errors->has('mobile'))
          @foreach($errors->get('mobile') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label></br>
          @endforeach
        @endif
        <input type="text" class="form-control" placeholder="{{ trans('merchant.mobile') }}" name="mobile" value="{{ old('mobile') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">
        @if($errors->has('password'))
          @foreach($errors->get('password') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label></br>
          @endforeach
        @endif
        <input type="password" class="form-control" placeholder="{{ trans('merchant.password') }}" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>


      <div class="form-group has-feedback {!! !$errors->has('name') ?: 'has-error' !!}">
        @if($errors->has('name'))
          @foreach($errors->get('name') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label></br>
          @endforeach
        @endif
        <input type="text" class="form-control" placeholder="公司名称" name="name" value="{{ old('name') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>


      <div class="form-group has-feedback {!! !$errors->has('code') ?: 'has-error' !!}">
        @if($errors->has('code'))
          @foreach($errors->get('code') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label></br>
          @endforeach
        @endif
        <input type="text" class="form-control" placeholder="验证码" name="code" value="{{ old('code') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>



      <div class="row">
        <div class="col-xs-12 " >
          <span  class="desc pull-left"> <a  href="{{ merchant_base_path('auth/login') }}">返回登录</a></span>
        </div>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12 ">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{ trans('merchant.register') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}} "></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
