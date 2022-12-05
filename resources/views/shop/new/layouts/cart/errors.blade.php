@if ($errors->any())
<div class="alert alert-danger" style="color: brown;">
    @foreach ($errors->all() as $error)
        <p class="errors">{{ $error }}</p>
    @endforeach
</div>
@endif
