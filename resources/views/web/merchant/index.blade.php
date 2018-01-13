<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Merchant::title() }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/font-awesome/css/font-awesome.min.css") }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/dist/css/skins/" . config('merchant.skin') .".min.css") }}">

    {!! Merchant::css() !!}
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/laravel-merchant/laravel-merchant.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/nprogress/nprogress.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/sweetalert/dist/sweetalert.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/nestable/nestable.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/toastr/build/toastr.min.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/bootstrap3-editable/css/bootstrap-editable.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/google-fonts/fonts.css") }}">
    <link rel="stylesheet" href="{{ merchant_asset("/vendor/laravel-merchant/AdminLTE/dist/css/AdminLTE.min.css") }}">

    <!-- REQUIRED JS SCRIPTS -->
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/AdminLTE/bootstrap/js/bootstrap.min.js") }}"></script>
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/AdminLTE/dist/js/app.min.js") }}"></script>
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/jquery-pjax/jquery.pjax.js") }}"></script>
    <script src="{{ merchant_asset ("/vendor/laravel-merchant/nprogress/nprogress.js") }}"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition {{config('merchant.skin')}} {{join(' ', config('merchant.layout'))}}">
<div class="wrapper">

    @include('merchant::partials.header')

    @include('merchant::partials.sidebar')

    <div class="content-wrapper" id="pjax-container">
        @yield('content')
        {!! Merchant::script() !!}
    </div>

    @include('merchant::partials.footer')

</div>

<!-- ./wrapper -->

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";
</script>

<!-- REQUIRED JS SCRIPTS -->
{{--<script src="{{ merchant_asset ("/vendor/laravel-merchant/nestable/jquery.nestable.js") }}"></script>--}}
<script src="{{ merchant_asset ("/vendor/laravel-merchant/toastr/build/toastr.min.js") }}"></script>
<script src="{{ merchant_asset ("/vendor/laravel-merchant/bootstrap3-editable/js/bootstrap-editable.min.js") }}"></script>
<script src="{{ merchant_asset ("/vendor/laravel-merchant/sweetalert/dist/sweetalert.min.js") }}"></script>
{!! Merchant::js() !!}
<script src="{{ merchant_asset ("/vendor/laravel-merchant/laravel-merchant/laravel-merchant.js") }}"></script>

</body>
</html>
