{{-- Table wrapper tái sử dụng cho admin --}}
{{-- Nhận: $headers (array), $slot --}}
<div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full {{ $minWidth ?? 'min-w-[600px]' }}">
      @if(isset($headers))
      <thead class="bg-[#222] border-b border-[#333]">
        <tr>
          @foreach($headers as $h)
          <th class="text-left text-gray-400 text-xs font-black px-4 py-3 uppercase tracking-wide">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      @endif
      <tbody>
        {{ $slot }}
      </tbody>
    </table>
  </div>
</div>
