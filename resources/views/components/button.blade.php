@props([
  'variant' => 'primary',
  'size'    => 'md',
  'type'    => 'button',
  'href'    => null,
  'disabled'=> false,
])

@php
$variants = [
  'primary'   => 'bg-[#FF6B35] text-white border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]',
  'secondary' => 'bg-[#FFD23F] text-[#1C1C1C] border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]',
  'dark'      => 'bg-[#1C1C1C] text-white border-[#1C1C1C] shadow-[4px_4px_0px_#FF6B35] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]',
  'outline'   => 'bg-white text-[#1C1C1C] border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none hover:bg-orange-50',
  'danger'    => 'bg-red-500 text-white border-red-600 shadow-[3px_3px_0px_#dc2626] hover:shadow-none',
  'ghost'     => 'bg-transparent text-[#FF6B35] border-transparent hover:bg-orange-50',
];
$sizes = [
  'sm' => 'px-3 py-1.5 text-xs',
  'md' => 'px-4 py-2.5 text-sm',
  'lg' => 'px-6 py-3.5 text-base',
  'xl' => 'px-8 py-4 text-lg',
];
$base = 'inline-flex items-center justify-center gap-2 font-black border-2 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0 disabled:shadow-none';
$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
