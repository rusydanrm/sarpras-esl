@props(['value', 'required' => false])

@php
    $isRequired = $required || $attributes->has('required');
@endphp

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
    @if ($isRequired)
        <span class="text-[#EF4444] font-bold">*</span>
    @endif
</label>
