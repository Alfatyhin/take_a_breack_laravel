@isset ($message)
    @if($message)
        <div class="pop-ap message">
            <div class="body">
                <span class="close"></span>
                @foreach($message as $k => $mess)
                    <p>
                       {{ $k }} - {{ $mess }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif
@endisset