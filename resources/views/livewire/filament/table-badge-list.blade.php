@if($getState())
    @foreach ($getState() as $item)
        <span
            class="inline-flex items-center rounded-md bg-blue-50 me-1 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
        {{ $item }}
        </span>
    @endforeach
@endif

