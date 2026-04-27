{{-- Stats card tái sử dụng cho admin --}}
{{-- Nhận: $label, $value, $change, $icon, $color (text-*), $bg (border-*) --}}
<div class="bg-[#1A1A1A] border-2 {{ $bg ?? 'border-[#333]' }} rounded-2xl p-3 lg:p-4">
  <div class="flex items-center justify-between mb-2">
    <span class="text-xl lg:text-2xl">{{ $icon ?? '📊' }}</span>
    @if(isset($change))
    <span class="text-xs font-bold {{ $color ?? 'text-gray-400' }}">{{ $change }}</span>
    @endif
  </div>
  <div class="text-white font-black text-lg lg:text-xl">{{ $value ?? '—' }}</div>
  <div class="text-gray-500 text-xs mt-0.5">{{ $label ?? '' }}</div>
</div>
