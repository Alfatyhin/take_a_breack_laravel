
@if(env('APP_NAME') == 'Take a Break Server')
    <hr>
    <p>server info</p>
    <h3>Order {{ $order_number }}</h3>
    <hr>
@endif

@if ($errors->any())
<div class="alert alert-danger" style="color: brown;">
    @foreach ($errors->all() as $error)
        <p class="errors">{{ $error }}</p>
    @endforeach
</div>
@endif
