<p>
    @foreach ($breadcrumbs as $key => $crumb)
        @if ($key > 0) > @endif
        @if($crumb[1])<a href="{{ $crumb[1] }}">@endif{{ $crumb[0] }}@if($crumb[1])</a>@endif
    @endforeach
</p>