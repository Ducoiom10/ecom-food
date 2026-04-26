@extends('layouts.admin')
@section('title', 'Super Admin')
@section('page_title', 'Super Admin')

@section('content')
<div class="h-full flex flex-col bg-[#0F0F0F]">

  {{-- Tabs --}}
  <div class="flex border-b-2 border-[#333] bg-[#1A1A1A] flex-shrink-0 overflow-x-auto scrollbar-hide">
    @foreach([['id'=>'analytics','label'=>'Analytics','icon'=>'📊'],['id'=>'campaigns','label'=>'Campaigns','icon'=>'🏷️'],['id'=>'roles','label'=>'RBAC','icon'=>'🔒'],['id'=>'audit','label'=>'Audit','icon'=>'📋']] as $tab)
    <a href="{{ route('admin.super', ['tab' => $tab['id']]) }}"
      class="flex items-center gap-1.5 px-4 lg:px-5 py-3 text-xs lg:text-sm font-black border-r border-[#333] transition-all whitespace-nowrap flex-shrink-0
             {{ ($activeTab??'analytics') === $tab['id'] ? 'bg-[#FF6B35] text-white' : 'text-gray-400 hover:text-white' }}">
      {{ $tab['icon'] }} {{ $tab['label'] }}
    </a>
    @endforeach
  </div>

  <div class="flex-1 overflow-y-auto p-4 lg:p-6">

    {{-- ANALYTICS --}}
    @if(($activeTab??'analytics') === 'analytics')
    <div class="space-y-4 lg:space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        @foreach([
          ['label'=>'Tổng doanh thu tuần','value'=>number_format($totalRevenue??0).'đ','icon'=>'💰','color'=>'text-green-400','bg'=>'border-green-700/30'],
          ['label'=>'Đơn hàng hôm nay',   'value'=>'1,247',                            'icon'=>'⚡','color'=>'text-orange-400','bg'=>'border-orange-700/30'],
          ['label'=>'Người dùng active',  'value'=>'3,841',                            'icon'=>'👥','color'=>'text-blue-400',  'bg'=>'border-blue-700/30'],
          ['label'=>'Tăng trưởng tuần',   'value'=>'+23%',                             'icon'=>'📈','color'=>'text-purple-400','bg'=>'border-purple-700/30'],
        ] as $kpi)
        <div class="bg-[#1A1A1A] border-2 {{ $kpi['bg'] }} rounded-2xl p-3 lg:p-4">
          <div class="text-xl lg:text-2xl mb-2">{{ $kpi['icon'] }}</div>
          <div class="text-white font-black text-lg lg:text-2xl">{{ $kpi['value'] }}</div>
          <div class="text-gray-500 text-xs mt-0.5">{{ $kpi['label'] }}</div>
        </div>
        @endforeach
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        {{-- Revenue chart --}}
        <div class="lg:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
          <h3 class="text-white font-black mb-4 text-sm lg:text-base">Doanh thu 7 ngày qua</h3>
          <div class="h-40 lg:h-52 flex items-end gap-2 px-2">
            @foreach($revenueData ?? [] as $d)
            @php $h = min(100, ($d['revenue'] / 25000000) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
              <div class="w-full bg-gradient-to-t from-[#FF6B35] to-[#FFD23F] rounded-t-sm hover:opacity-80 transition-opacity cursor-pointer" style="height: {{ $h }}%" title="{{ $d['day'] }}: {{ number_format($d['revenue']) }}đ"></div>
              <span class="text-[10px] text-gray-500">{{ $d['day'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Branch comparison --}}
        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
          <h3 class="text-white font-black mb-4 text-sm">So sánh chi nhánh</h3>
          <div class="space-y-3">
            @foreach(collect($branches ?? [])->where('status','open') as $branch)
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-gray-300 text-xs">{{ str_replace('Chi nhánh ','',$branch['name']) }}</span>
                <span class="text-white font-black text-xs">{{ number_format($branch['revenue']/1000000,1) }}M</span>
              </div>
              <div class="h-3 bg-[#333] rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full" style="width: {{ min(100,($branch['revenue']/16000000)*100) }}%"></div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- CAMPAIGNS --}}
    @if(($activeTab??'') === 'campaigns')
    <div class="space-y-4 lg:space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-white font-black text-lg lg:text-xl">Quản lý Campaigns</h2>
        <button class="bg-[#FF6B35] text-white text-xs lg:text-sm font-bold px-3 lg:px-4 py-2 rounded-xl border-2 border-[#FF6B35]">🏷️ Tạo voucher</button>
      </div>

      {{-- Vouchers table --}}
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[600px]">
            <thead class="bg-[#222] border-b border-[#333]">
              <tr>
                @foreach(['Mã','Giảm giá','Loại','Đã dùng','Hạn sử dụng','Trạng thái'] as $h)
                <th class="text-left text-gray-400 text-xs font-black px-4 py-3 uppercase tracking-wide">{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($vouchers ?? [] as $v)
              <tr class="border-b border-[#222] hover:bg-[#1D1D1D]">
                <td class="px-4 py-3 text-white font-black text-sm">{{ $v['code'] }}</td>
                <td class="px-4 py-3 text-[#FFD23F] font-black">{{ $v['discount'] }}</td>
                <td class="px-4 py-3 text-gray-400 text-sm">{{ $v['type']==='flat'?'Cố định':($v['type']==='percent'?'Phần trăm':'Free ship') }}</td>
                <td class="px-4 py-3">
                  <div class="text-white text-sm font-bold">{{ $v['used'] }}/{{ $v['max'] }}</div>
                  <div class="h-1.5 bg-[#333] rounded-full mt-1 w-20">
                    <div class="h-full bg-[#FF6B35] rounded-full" style="width: {{ ($v['used']/max(1,$v['max']))*100 }}%"></div>
                  </div>
                </td>
                <td class="px-4 py-3 text-gray-400 text-sm">{{ $v['expires'] }}</td>
                <td class="px-4 py-3">
                  <span class="text-xs font-black px-2 py-1 rounded-full {{ $v['status']==='active'?'bg-green-500/20 text-green-400 border border-green-500/30':'bg-gray-700 text-gray-500' }}">
                    {{ $v['status']==='active'?'Hoạt động':'Hết hạn' }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- Push notification --}}
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 lg:p-5">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-[#FFD23F]">🔔</span>
          <h3 class="text-white font-black text-sm lg:text-base">Gửi Push Notification</h3>
        </div>
        <form action="{{ route('admin.super.push') }}" method="POST" class="space-y-3">
          @csrf
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-gray-400 text-xs mb-1 block">Tiêu đề</label>
              <input name="title" value="🔥 Flash Sale giờ trưa!" class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]" />
            </div>
            <div>
              <label class="text-gray-400 text-xs mb-1 block">Target segment</label>
              <select name="segment" class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none">
                <option>Tất cả người dùng</option>
                <option>Giỏ hàng bị bỏ quên</option>
                <option>Chưa đặt trong 7 ngày</option>
                <option>Khách VIP</option>
              </select>
            </div>
          </div>
          <textarea name="body" class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35] h-20 resize-none">Đặt ngay Mì trộn + Trà sữa combo chỉ 65k!</textarea>
          <button type="submit" class="bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#FF6B35] flex items-center gap-2 text-sm">🔔 Gửi ngay</button>
        </form>
      </div>
    </div>
    @endif

    {{-- ROLES --}}
    @if(($activeTab??'') === 'roles')
    <div class="space-y-4">
      <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">🔒 RBAC Permission Matrix</h2>
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px]">
            <thead>
              <tr class="bg-[#222] border-b border-[#333]">
                <th class="text-left text-gray-400 text-xs px-4 py-3 font-black uppercase tracking-wide">Quyền hạn</th>
                @foreach($roles ?? [] as $role)
                <th class="text-center text-gray-300 text-xs px-3 py-3 font-black">{{ $role }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($permissions ?? [] as $perm)
              <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D]">
                <td class="px-4 py-3 text-gray-300 text-sm">{{ $perm['label'] }}</td>
                @foreach($roles ?? [] as $role)
                <td class="text-center px-3 py-3">
                  @if(in_array($perm['key'], $rolePerms[$role] ?? []))
                  <form action="{{ route('admin.super.perm') }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="{{ $role }}" />
                    <input type="hidden" name="perm" value="{{ $perm['key'] }}" />
                    <input type="hidden" name="action" value="remove" />
                    <button type="submit" class="{{ $role==='Super Admin'?'cursor-not-allowed':'cursor-pointer hover:scale-110' }} transition-all text-lg" {{ $role==='Super Admin'?'disabled':'' }}>✅</button>
                  </form>
                  @else
                  <form action="{{ route('admin.super.perm') }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="{{ $role }}" />
                    <input type="hidden" name="perm" value="{{ $perm['key'] }}" />
                    <input type="hidden" name="action" value="add" />
                    <button type="submit" class="cursor-pointer hover:scale-110 transition-all text-lg">⬜</button>
                  </form>
                  @endif
                </td>
                @endforeach
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <p class="text-gray-600 text-xs">* Super Admin có toàn quyền, không thể chỉnh sửa.</p>
    </div>
    @endif

    {{-- AUDIT --}}
    @if(($activeTab??'') === 'audit')
    <div class="space-y-4">
      <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">📋 Audit Trail</h2>
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
        <div class="px-4 py-3 border-b border-[#333] flex flex-col sm:flex-row gap-3">
          <form action="{{ route('admin.super', ['tab'=>'audit']) }}" method="GET" class="flex gap-2 flex-1">
            <input type="hidden" name="tab" value="audit" />
            <input name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}"
              class="flex-1 bg-[#222] border border-[#444] text-white text-sm rounded-xl px-3 py-1.5 outline-none" />
            <select name="action_filter" class="bg-[#222] border border-[#444] text-gray-400 text-xs rounded-xl px-3 py-1.5 outline-none">
              <option value="">Tất cả</option>
              <option value="CREATE" {{ request('action_filter')==='CREATE'?'selected':'' }}>CREATE</option>
              <option value="UPDATE" {{ request('action_filter')==='UPDATE'?'selected':'' }}>UPDATE</option>
              <option value="DELETE" {{ request('action_filter')==='DELETE'?'selected':'' }}>DELETE</option>
            </select>
            <button type="submit" class="bg-[#FF6B35] text-white text-xs font-bold px-3 py-1.5 rounded-xl">Lọc</button>
          </form>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[700px]">
            <thead>
              <tr class="bg-[#222] border-b border-[#333]">
                @foreach(['Thời gian','Người dùng','Hành động','Đối tượng','Chi tiết','IP'] as $h)
                <th class="text-left text-gray-500 text-[10px] px-4 py-2.5 font-black uppercase tracking-wide">{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($auditLogs ?? [] as $log)
              <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D] font-mono text-xs">
                <td class="px-4 py-3 text-gray-500">{{ $log['time'] }}</td>
                <td class="px-4 py-3 text-blue-400">{{ $log['user'] }}</td>
                <td class="px-4 py-3">
                  <span class="font-black px-2 py-0.5 rounded text-[10px] {{ $log['action']==='CREATE'?'bg-green-900/50 text-green-400':($log['action']==='UPDATE'?'bg-yellow-900/50 text-yellow-400':'bg-red-900/50 text-red-400') }}">{{ $log['action'] }}</span>
                </td>
                <td class="px-4 py-3 text-gray-300">{{ $log['target'] }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $log['detail'] }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $log['ip'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection
