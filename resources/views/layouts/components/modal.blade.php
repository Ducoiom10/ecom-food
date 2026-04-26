@props(['id', 'title' => '', 'size' => 'md'])

@php
$sizes = ['sm'=>'max-w-sm', 'md'=>'max-w-md', 'lg'=>'max-w-lg', 'xl'=>'max-w-xl', 'full'=>'max-w-3xl'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 bg-black/70 z-50 items-center justify-center p-4 hidden"
  onclick="if(event.target===this) closeModal('{{ $id }}')">
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[6px_6px_0px_#1C1C1C] w-full {{ $sizeClass }} max-h-[90vh] overflow-y-auto">
    @if($title)
    <div class="flex items-center justify-between px-5 py-4 border-b-2 border-[#1C1C1C]">
      <h3 class="font-black text-[#1C1C1C] text-lg">{{ $title }}</h3>
      <button onclick="closeModal('{{ $id }}')" class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-gray-200 text-gray-400 hover:border-[#1C1C1C] hover:text-[#1C1C1C] transition-all font-bold">✕</button>
    </div>
    @endif
    <div class="p-5">{{ $slot }}</div>
  </div>
</div>
