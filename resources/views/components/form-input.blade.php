@props([
  'label'       => '',
  'name'        => '',
  'type'        => 'text',
  'placeholder' => '',
  'value'       => '',
  'required'    => false,
  'disabled'    => false,
  'hint'        => '',
  'icon'        => '',
])

@php
$hasError = $errors->has($name);
$inputClass = 'w-full border-2 rounded-xl px-4 py-3 text-sm outline-none transition-all '
  . ($icon ? 'pl-10 ' : '')
  . ($hasError
      ? 'border-red-400 shadow-[2px_2px_0px_#dc2626] focus:border-red-500'
      : 'border-[#1C1C1C] focus:border-[#FF6B35] focus:shadow-[2px_2px_0px_#FF6B35]')
  . ($disabled ? ' bg-gray-50 text-gray-400 cursor-not-allowed' : ' bg-white');
@endphp

<div class="space-y-1.5">
  @if($label)
  <label for="{{ $name }}" class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide">
    {{ $label }}
    @if($required)<span class="text-red-500">*</span>@endif
  </label>
  @endif

  <div class="relative">
    @if($icon)
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">{{ $icon }}</span>
    @endif

    @if($type === 'textarea')
    <textarea
      id="{{ $name }}"
      name="{{ $name }}"
      placeholder="{{ $placeholder }}"
      {{ $required ? 'required' : '' }}
      {{ $disabled ? 'disabled' : '' }}
      {{ $attributes->merge(['class' => $inputClass . ' resize-none']) }}
      rows="3">{{ old($name, $value) }}</textarea>
    @else
    <input
      type="{{ $type }}"
      id="{{ $name }}"
      name="{{ $name }}"
      value="{{ old($name, $value) }}"
      placeholder="{{ $placeholder }}"
      {{ $required ? 'required' : '' }}
      {{ $disabled ? 'disabled' : '' }}
      {{ $attributes->merge(['class' => $inputClass]) }} />
    @endif
  </div>

  @error($name)
  <p class="text-red-600 text-xs font-medium flex items-center gap-1">
    <span>❌</span> {{ $message }}
  </p>
  @enderror

  @if($hint && !$errors->has($name))
  <p class="text-gray-400 text-xs">{{ $hint }}</p>
  @endif
</div>
