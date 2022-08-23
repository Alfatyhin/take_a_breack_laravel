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
