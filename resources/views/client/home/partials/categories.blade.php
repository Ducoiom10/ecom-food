{{-- Categories --}}
<div class="flex gap-2 flex-wrap">
  @foreach([
    ['id'=>'all',    'label'=>'Tất cả', 'emoji'=>'🍽️'],
    ['id'=>'noodles','label'=>'Mì & Phở','emoji'=>'🍜'],
    ['id'=>'rice',   'label'=>'Cơm',    'emoji'=>'🍚'],
    ['id'=>'snacks', 'label'=>'Ăn vặt', 'emoji'=>'🍗'],
    ['id'=>'drinks', 'label'=>'Đồ uống','emoji'=>'🧋'],
  ] as $cat)
  <a href="{{ route('client.menu', ['category' => $cat['id']]) }}"
    class="flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs lg:text-sm font-bold transition-all bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none hover:bg-orange-50">
    {{ $cat['emoji'] }} {{ $cat['label'] }}
  </a>
  @endforeach
</div>
