@php
    $isActive = $route === url()->current();

    $class = $isActive ?
        "bg-gray-50 text-indigo-600 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold" :
        "text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold";
@endphp

<li>
    <!-- Current: "bg-gray-50 text-indigo-600", Default: "text-gray-700 hover:text-indigo-600 hover:bg-gray-50" -->
    <a href="{{ $route }}" class="{{ $class }} items-center">
        {!! $icon ?? '' !!}
        {{ $label }}
    </a>
</li>
