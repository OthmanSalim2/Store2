@props([
    'type' => 'text',
    'name',
    'value' => '',
    'label' => false,
])

@if ($label)
    <label for="">{{ $label }}</label>
@endif

<input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
    {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
{{--  other way --}}
{{-- @class(['form-control', 'is-invalid' => $errors->has($name)])> --}}

<x-form.validation-feedback :name="$name" />
