@extends('layouts.client')
@section('title', 'Chia Bill')

@section('content')
<div class="min-h-screen bg-[#FAFAF8] pb-8">

  {{-- Header --}}
  <div class="bg-[#1C1C1C] px-4 pt-8 pb-6 border-b-2 border-[#1C1C1C]">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-[#FFD23F] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center text-2xl">👥</div>
      <div>
        <h1 class="text-white font-black text-xl">Chia Bill Nhóm</h1>
        <p class="text-gray-400 text-xs">Phòng #{{ $room->room_code }} · {{ $bills->count() }} người</p>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-[#FFD23F] font-black text-lg">{{ number_format($grandTotal) }}đ</div>
        <div class="text-white/60 text-[10px]">Tổng đơn</div>
      </div>
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-green-400 font-black text-lg" id="paid-count">0/{{ $bills->count() }}</div>
        <div class="text-white/60 text-[10px]">Đã trả</div>
      </div>
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-[#FF6B35] font-black text-lg" id="remaining-total">{{ number_format($grandTotal) }}đ</div>
        <div class="text-white/60 text-[10px]">Còn lại</div>
      </div>
    </div>

    <div class="mt-4">
      <div class="flex justify-between text-xs text-gray-400 mb-1">
        <span>Tiến độ thanh toán</span>
        <span id="progress-pct">0%</span>
      </div>
      <div class="h-3 bg-white/20 rounded-full overflow-hidden border border-white/30">
        <div class="h-full bg-gradient-to-r from-[#FFD23F] to-[#FF6B35] rounded-full transition-all duration-500" id="progress-bar" style="width: 0%"></div>
      </div>
    </div>
  </div>

  {{-- Payment method --}}
  <div class="px-4 mt-4">
    <p class="text-xs font-black text-[#1C1C1C] mb-2 uppercase tracking-wide">Phương thức thanh toán</p>
    <div class="flex gap-2">
      @foreach([['id'=>'momo','label'=>'MoMo','emoji'=>'💜'],['id'=>'bank','label'=>'Chuyển khoản','emoji'=>'🏦'],['id'=>'cod','label'=>'Tiền mặt','emoji'=>'💵'],['id'=>'zalopay','label'=>'ZaloPay','emoji'=>'🔵']] as $pm)
      <button onclick="selectPM('{{ $pm['id'] }}')" id="pm-{{ $pm['id'] }}"
        class="flex-1 flex flex-col items-center py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold transition-all {{ $pm['id'] === 'momo' ? 'bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]' }}">
        <span class="text-lg">{{ $pm['emoji'] }}</span>
        <span class="text-[9px]">{{ $pm['label'] }}</span>
      </button>
      @endforeach
    </div>
  </div>

  {{-- Individual bills --}}
  <div class="px-4 mt-4 space-y-3">
    <h2 class="font-black text-[#1C1C1C] flex items-center gap-2">🧾 Bill từng người</h2>

    @foreach($bills as $participant)
    @php
      $order      = $participant->orders->first();
      $items      = $order?->items ?? collect();
      $total      = $order?->grand_total ?? 0;
    @endphp
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden" id="bill-{{ $participant->id }}">
      <div class="p-4">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-lg flex-shrink-0" id="bill-emoji-{{ $participant->id }}">
            {{ $participant->emoji ?? '👤' }}
          </div>
          <div class="flex-1">
            <div class="font-black text-[#1C1C1C] flex items-center gap-2">
              {{ $participant->display_name }}
              @if($participant->is_host)<span class="bg-[#FFD23F] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full border border-[#1C1C1C]">Host</span>@endif
              <span class="text-green-500 text-xs hidden" id="paid-label-{{ $participant->id }}">(Đã trả)</span>
            </div>
            <div class="text-xs text-gray-400">{{ $items->count() }} món</div>
          </div>
          <div class="font-black text-[#FF6B35] text-lg">{{ number_format($total) }}đ</div>
        </div>

        <button onclick="toggleItems({{ $participant->id }})" class="w-full flex items-center justify-between text-xs text-gray-500 bg-gray-50 rounded-xl px-3 py-2 mb-3 border border-gray-200">
          <span>Chi tiết đơn hàng</span><span>▾</span>
        </button>
        <div id="items-{{ $participant->id }}" class="space-y-1.5 mb-3 bg-gray-50 rounded-xl p-3 hidden">
          @forelse($items as $it)
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-700">{{ $it->product?->name ?? 'Sản phẩm' }} <span class="text-gray-400">x{{ $it->quantity }}</span></span>
            <span class="font-bold text-[#1C1C1C]">{{ number_format($it->price * $it->quantity) }}đ</span>
          </div>
          @empty
          <p class="text-xs text-gray-400 italic">Chưa chọn món nào</p>
          @endforelse
          @if($items->count() > 0)
          <div class="border-t border-gray-200 pt-1.5 mt-1.5 flex justify-between text-xs font-black">
            <span>Tổng</span><span class="text-[#FF6B35]">{{ number_format($total) }}đ</span>
          </div>
          @endif
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 mb-3 text-xs momo-info">
          <div class="font-bold text-purple-700 mb-1">Chuyển MoMo cho host:</div>
          <div class="text-gray-600">Nội dung: <span class="font-bold text-[#1C1C1C]">{{ $participant->display_name }} {{ $room->room_code }}</span></div>
          <div class="text-gray-600">Số tiền: <span class="font-bold text-purple-600 text-sm">{{ number_format($total) }}đ</span></div>
        </div>

        <div class="flex gap-2">
          <button onclick="copyBill({{ $participant->id }}, '{{ $participant->display_name }}', {{ $total }})"
            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] bg-[#FFD23F] text-[#1C1C1C] text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all" id="copy-btn-{{ $participant->id }}">
            📋 Copy bill
          </button>
          <button onclick="markPaid({{ $participant->id }}, {{ $total }})"
            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] bg-green-500 text-white text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all" id="paid-btn-{{ $participant->id }}">
            ✓ Đã trả
          </button>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="px-4 mt-4">
    <button onclick="navigator.clipboard.writeText(window.location.href)"
      class="w-full flex items-center justify-center gap-2 py-3.5 bg-[#1C1C1C] text-white font-black rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#FF6B35] hover:shadow-[2px_2px_0px_#FF6B35] transition-all">
      📤 Chia sẻ bill cho cả nhóm
    </button>
    <a href="{{ route('client.home') }}" class="w-full mt-2 text-center text-sm text-[#FF6B35] font-bold py-2 block">Đặt đơn mới →</a>
  </div>

</div>
@endsection

@push('scripts')
<script>
const totalBills = {{ $bills->count() }};
const grandTotal = {{ $grandTotal }};
let paidSet = new Set();

function toggleItems(id) {
  document.getElementById('items-' + id).classList.toggle('hidden');
}

function markPaid(id, amount) {
  const isPaid = paidSet.has(id);
  isPaid ? paidSet.delete(id) : paidSet.add(id);

  document.getElementById('paid-label-' + id).classList.toggle('hidden', isPaid);
  document.getElementById('bill-' + id).style.opacity = isPaid ? '1' : '0.7';

  const btn = document.getElementById('paid-btn-' + id);
  if (!isPaid) {
    btn.classList.replace('bg-green-500', 'bg-gray-200');
    btn.classList.replace('text-white', 'text-gray-600');
    btn.textContent = '↩ Bỏ xác nhận';
  } else {
    btn.classList.replace('bg-gray-200', 'bg-green-500');
    btn.classList.replace('text-gray-600', 'text-white');
    btn.textContent = '✓ Đã trả';
  }

  const paidCount = paidSet.size;
  document.getElementById('paid-count').textContent = paidCount + '/' + totalBills;
  document.getElementById('progress-pct').textContent = Math.round((paidCount / totalBills) * 100) + '%';
  document.getElementById('progress-bar').style.width = ((paidCount / totalBills) * 100) + '%';

  if (paidCount === totalBills) {
    document.body.insertAdjacentHTML('beforeend', `
      <div class="fixed inset-0 pointer-events-none z-50 flex items-center justify-center">
        <div class="text-center animate-bounce">
          <div class="text-6xl mb-2">🎉</div>
          <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] px-6 py-3">
            <p class="font-black text-[#1C1C1C] text-lg">Tất cả đã thanh toán!</p>
            <p class="text-sm text-gray-600">Enjoy bữa ăn! 😋</p>
          </div>
        </div>
      </div>`);
  }
}

function copyBill(id, name, total) {
  navigator.clipboard.writeText(`🧾 Bill của ${name}\n💰 Tổng: ${total.toLocaleString('vi-VN')}đ\nPhòng: {{ $room->room_code }}`);
  const btn = document.getElementById('copy-btn-' + id);
  btn.textContent = '✅ Đã copy!';
  setTimeout(() => btn.textContent = '📋 Copy bill', 2000);
}

function selectPM(id) {
  ['momo','bank','cod','zalopay'].forEach(p => {
    const el = document.getElementById('pm-' + p);
    if (p === id) {
      el.classList.replace('bg-white', 'bg-[#1C1C1C]');
      el.classList.replace('text-[#1C1C1C]', 'text-white');
    } else {
      el.classList.replace('bg-[#1C1C1C]', 'bg-white');
      el.classList.replace('text-white', 'text-[#1C1C1C]');
    }
  });
  document.querySelectorAll('.momo-info').forEach(el => el.style.display = id === 'momo' ? '' : 'none');
}
</script>
@endpush
