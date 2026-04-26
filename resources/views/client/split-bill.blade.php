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
        <p class="text-gray-400 text-xs">Phòng #{{ $room['code'] ?? '' }} · {{ count($bills ?? []) }} người</p>
      </div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-3">
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-[#FFD23F] font-black text-lg">{{ number_format($grandTotal ?? 0) }}đ</div>
        <div class="text-white/60 text-[10px]">Tổng đơn</div>
      </div>
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-green-400 font-black text-lg" id="paid-count">0/{{ count($bills ?? []) }}</div>
        <div class="text-white/60 text-[10px]">Đã trả</div>
      </div>
      <div class="bg-white/10 rounded-xl p-3 text-center">
        <div class="text-[#FF6B35] font-black text-lg" id="remaining-total">{{ number_format($grandTotal ?? 0) }}đ</div>
        <div class="text-white/60 text-[10px]">Còn lại</div>
      </div>
    </div>

    {{-- Progress --}}
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

    @foreach($bills ?? [] as $bill)
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden" id="bill-{{ $bill['participantId'] }}">
      <div class="p-4">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-lg flex-shrink-0" id="bill-emoji-{{ $bill['participantId'] }}">
            {{ $bill['emoji'] }}
          </div>
          <div class="flex-1">
            <div class="font-black text-[#1C1C1C] flex items-center gap-2">
              {{ $bill['participantName'] }}
              <span class="text-green-500 text-xs hidden" id="paid-label-{{ $bill['participantId'] }}">(Đã trả)</span>
            </div>
            <div class="text-xs text-gray-400">{{ count($bill['items']) }} món</div>
          </div>
          <div class="font-black text-[#FF6B35] text-lg">{{ number_format($bill['total']) }}đ</div>
        </div>

        {{-- Toggle items --}}
        <button onclick="toggleItems('{{ $bill['participantId'] }}')" class="w-full flex items-center justify-between text-xs text-gray-500 bg-gray-50 rounded-xl px-3 py-2 mb-3 border border-gray-200">
          <span>Chi tiết đơn hàng</span><span>▾</span>
        </button>
        <div id="items-{{ $bill['participantId'] }}" class="space-y-1.5 mb-3 bg-gray-50 rounded-xl p-3 hidden">
          @foreach($bill['items'] as $it)
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-700">{{ $it['name'] }} <span class="text-gray-400">x{{ $it['quantity'] }}</span></span>
            <span class="font-bold text-[#1C1C1C]">{{ number_format($it['subtotal']) }}đ</span>
          </div>
          @endforeach
          <div class="border-t border-gray-200 pt-1.5 mt-1.5 flex justify-between text-xs font-black">
            <span>Tổng</span><span class="text-[#FF6B35]">{{ number_format($bill['total']) }}đ</span>
          </div>
        </div>

        {{-- MoMo info --}}
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 mb-3 text-xs momo-info">
          <div class="font-bold text-purple-700 mb-1">Chuyển MoMo cho host:</div>
          <div class="text-gray-600">SĐT: <span class="font-bold text-[#1C1C1C]">0901 234 567</span></div>
          <div class="text-gray-600">Nội dung: <span class="font-bold text-[#1C1C1C]">{{ $bill['participantName'] }} {{ $room['code'] ?? '' }}</span></div>
          <div class="text-gray-600">Số tiền: <span class="font-bold text-purple-600 text-sm">{{ number_format($bill['total']) }}đ</span></div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
          <button onclick="copyBill('{{ $bill['participantId'] }}', '{{ $bill['participantName'] }}', {{ $bill['total'] }})"
            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] bg-[#FFD23F] text-[#1C1C1C] text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all" id="copy-btn-{{ $bill['participantId'] }}">
            📋 Copy bill
          </button>
          <button onclick="markPaid('{{ $bill['participantId'] }}', {{ $bill['total'] }})"
            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] bg-green-500 text-white text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all" id="paid-btn-{{ $bill['participantId'] }}">
            ✓ Đã trả
          </button>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Share --}}
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
const totalBills = {{ count($bills ?? []) }};
const grandTotal = {{ $grandTotal ?? 0 }};
let paidSet = new Set();

function toggleItems(id) {
  document.getElementById('items-' + id).classList.toggle('hidden');
}

function markPaid(id, amount) {
  const isPaid = paidSet.has(id);
  if (isPaid) { paidSet.delete(id); } else { paidSet.add(id); }

  document.getElementById('bill-emoji-' + id).textContent = isPaid ? '{{ collect($bills ?? [])->pluck("emoji", "participantId") }}' : '✅';
  document.getElementById('paid-label-' + id).classList.toggle('hidden', isPaid);
  document.getElementById('bill-' + id).style.opacity = isPaid ? '1' : '0.7';

  const btn = document.getElementById('paid-btn-' + id);
  btn.textContent = isPaid ? '✓ Đã trả' : '↩ Bỏ xác nhận';
  btn.className = btn.className.replace(isPaid ? 'bg-gray-200 text-gray-600' : 'bg-green-500 text-white', isPaid ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600');

  const paidCount = paidSet.size;
  const remaining = [...Array(totalBills).keys()].reduce((s, _, i) => s, grandTotal);
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
  navigator.clipboard.writeText(`🧾 Bill của ${name}\n💰 Tổng: ${total.toLocaleString('vi-VN')}đ\nChuyển MoMo: 0901234567`);
  const btn = document.getElementById('copy-btn-' + id);
  btn.textContent = '✅ Đã copy!';
  setTimeout(() => btn.textContent = '📋 Copy bill', 2000);
}

function selectPM(id) {
  ['momo','bank','cod','zalopay'].forEach(p => {
    const el = document.getElementById('pm-' + p);
    if (p === id) el.className = el.className.replace('bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]', 'bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]');
    else el.className = el.className.replace('bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]', 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]');
  });
  document.querySelectorAll('.momo-info').forEach(el => el.style.display = id === 'momo' ? '' : 'none');
}
</script>
@endpush
