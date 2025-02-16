@props(['permission'])

@if(auth()->user()->checkResourcePermission($permission['type'], $permission['value'], $permission['action']))
    {{ $slot }}
@endif
