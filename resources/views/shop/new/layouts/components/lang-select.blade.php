
<li value="en" class="@if($lang == 'en') active @else hide @endif" >
    <a href="{{ route('index') }}">en</a>
</li>
<li value="ru" class="@if($lang == 'ru') active @else hide @endif" >
    <a href="{{ route('index', ['lang' => 'ru']) }}">ru</a>
</li>
<li value="he" class="@if($lang == 'he') active @else hide @endif" >
    <a href="{{ route('index', ['lang' => 'he']) }}">he</a>
</li>
