@if ($date)
    <time datetime="{{ $date->toW3cString() }}" title="{{ $date->format('r') }}">
        {{ $date->diffForHumans() }}
    </time>
@else
    <span class="text-muted">
        null
    </span>
@endif
