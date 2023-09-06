@props([
    'id' => '',
])

{{-- here the slot variable it represent the content inside component tags in view file --}}
<label for="{{ $id }}">{{ $slot }}</label>
