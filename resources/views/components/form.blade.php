@props([
    'method' => 'POST',
    'action',
    'onsubmit' => ''
])

<form
    method="{{ in_array(strtoupper($method), ['GET', 'POST']) ? $method : 'POST' }}"
    action="{{ $action }}"
    {!! $onsubmit ? "onsubmit=\"{$onsubmit}\"" : '' !!}
    {{ $attributes }}
>
    @if(!in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif

    @if(strtoupper($method) !== 'GET')
        @csrf
    @endif

    {{ $slot }}
</form>
