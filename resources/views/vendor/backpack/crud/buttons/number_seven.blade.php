@if ($crud->hasAccess('update'))
    <a href="{{ route('number_seven', ['id' => $entry->getKey()]) }}" class="btn btn-xs @if(!$entry->number_seven) btn-default @else btn-success @endif">Number7</a>
@endif
