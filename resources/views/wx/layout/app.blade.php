<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0"/>
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('/plugins/weui/style/weui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/plugins/weui/style/style.css')}}"/>
    <link rel="stylesheet" href="{{asset('/plugins/weui/style/jquery-weui.min.css')}}"/>
    <style>
        .text-center {
            text-align: center;
        }
        .ming-navbar{
            top:0;
            width:100%;
            color: #000000;
            font-size:20px;
            background-color:#fff;
            height: 45px;
            text-align: center
        }

        .ming-navbar a{
            position:relative;
            z-index: 100;
            float: left;
            padding-left: 1em;
            color:#586C94;
            padding-top:0.25em;
        }

        .ming-navbar p{
            position: relative;
            left: -2em;
            padding-top:0.25em;
        }
        .ming-navbar_fixed-top{
            position:fixed;
            top:0;
            width:100%;
        }

        .ming-icon_msg{
            font-size:53px;
        }
        .ming-textarea{
            display:block;
            border:0;
            resize:none;
            width:100%;
            color:#999;
            font-size:1em;
            line-height:inherit;
            outline:0;
        }
        .error{
            color:red;
        }
    </style>
</head>
<body ontouchstart>
{{--<div class="weui-toptips weui-toptips_warn js_tooltips">错误提示</div>--}}
{{--<div class="container" id="container">--}}
@section('header')
    <div class="page__hd ">
        <h1 class="page__title text-center">
            <img  src="{{asset('images/lsd_logo.png')}}" alt="螺丝刀售后"/>
        </h1>
        <p class="page__desc text-center">以人为本，让生活更美好。</p>
    </div>
@show
@yield('content')
@section('footer')
    <div class="page__ft ">
        <div class="weui-footer weui-footer_fixed-bottom" id="weui_footer">
            <p class="weui-footer__links">
                <a href="javascript:;" class="weui-footer__link">客服电话：{{config('saas.floor_telephone')}}</a>
            </p>
            <p class="weui-footer__text">Copyright &copy; 2016-{{date('Y')}} 杭州螺丝刀网络科技有限公司</p>
        </div>
    </div>
@show
</body>
<script src="{{asset('/plugins/weui/src/jquery.min.js')}}"></script>
@yield('myjs')
</html>
