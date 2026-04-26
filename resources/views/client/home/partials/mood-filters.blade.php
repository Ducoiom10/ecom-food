{{-- Mood filters --}}
<div>
  <h3 class="font-black text-[#1C1C1C] mb-3 flex items-center gap-2 text-base lg:text-lg">⚡ Hôm nay muốn ăn gì?</h3>
  <div class="flex gap-2 flex-wrap">
    @foreach([
      ['label'=>'Cần ngọt','emoji'=>'🧁','color'=>'bg-pink-100 border-pink-300 text-pink-700'],
      ['label'=>'Cay xè',  'emoji'=>'🌶️','color'=>'bg-red-100 border-red-300 text-red-700'],
      ['label'=>'Healthy', 'emoji'=>'🥗','color'=>'bg-green-100 border-green-300 text-green-700'],
      ['label'=>'No bụng', 'emoji'=>'💪','color'=>'bg-blue-100 border-blue-300 text-blue-700'],
      ['label'=>'Mau nào', 'emoji'=>'⚡','color'=>'bg-yellow-100 border-yellow-300 text-yellow-700'],
    ] as $mood)
    <a href="{{ route('client.menu', ['mood' => $mood['label']]) }}"
      class="flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 text-xs lg:text-sm font-bold {{ $mood['color'] }} hover:scale-105 transition-transform">
      {{ $mood['emoji'] }} {{ $mood['label'] }}
    </a>
    @endforeach
  </div>
</div>
