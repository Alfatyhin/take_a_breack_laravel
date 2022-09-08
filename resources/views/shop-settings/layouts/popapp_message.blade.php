@isset ($message)
    @if($message)
        <div class="pop-ap message">
            <div class="body">
                <span class="close"></span>
                @foreach($message as $mess)
                    <p>
                        {{ $mess }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif
@endisset
@if ($errors->any())
    <div class="pop-ap message alert alert-danger">
        <div class="body">
            <span class="close"></span>

            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif