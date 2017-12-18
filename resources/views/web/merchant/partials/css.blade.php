@foreach($css as $c)
    <link rel="stylesheet" href="{{ merchant_asset("$c") }}">
@endforeach