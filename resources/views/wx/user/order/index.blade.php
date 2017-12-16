@extends('wx.layout.app')
@section('title')
    修修咻，瞬间就好！
@endsection
@section('content')
    <div class="page__bd">
        @foreach($data as $k=>$v)
            <div class="weui-cells__title">{{$v['name']}}</div>
            <div class="weui-cells">
                @foreach($v->brands()->get() as $vv)
                    <a class="weui-cell weui-cell_access" href="{{url('wx/user/order/select/product')."?cat_id={$v['id']}&cat_name={$v['name']}&brand_id={$vv['id']}&brand_name={$vv['name']}"}}">
                        <div class="weui-cell__bd">
                            <p>{{$vv['name']}}</p>
                        </div>
                        <div class="weui-cell__ft"></div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
@section('myjs')

    <script>
        var window_height = $(window).height();
        var document_height =$(document).height();
        console.log('window_height',window_height);
        console.log('document_height',document_height);

        if(document_height > window_height  ){
            $('#weui_footer').removeClass('weui-footer_fixed-bottom');
        }
    </script>
@endsection