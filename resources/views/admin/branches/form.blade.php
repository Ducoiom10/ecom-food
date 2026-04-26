@extends('layouts.admin')
@section('title', isset($branch) ? 'Sửa chi nhánh' : 'Thêm chi nhánh')
@section('page_title', isset($branch) ? 'Sửa chi nhánh' : 'Thêm chi nhánh')

@section('content')
<div class="h-full overflow-y-auto bg-[#0F0F0F] p-4 lg:p-6">
  <div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
      <a href="{{ route('admin.branch') }}" class="w-9 h-9 bg-[#1A1A1A] border border-[#444] rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-colors font-bold">←</a>
      <h2 class="text-white font-black text-xl">{{ isset($branch) ? 'Sửa: '.$branch->name : 'Thêm chi nhánh mới' }}</h2>
    </div>

    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-6">
      <form action="{{ isset($branch) ? route('admin.branch.update', $branch->id) : route('admin.branch.store') }}" method="POST" class="space-y-5">
        @csrf
        @if(isset($branch)) @method('PUT') @endif

        @if($errors->any())
        <div class="bg-red-900/30 border border-red-500/50 rounded-xl px-4 py-3 text-red-400 text-sm">
          ⚠️ {{ $errors->first() }}
        </div>
        @endif

        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-wide mb-1.5">Tên chi nhánh <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="{{ old('name', $branch->name ?? '') }}" required
            class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
        </div>

        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-wide mb-1.5">Địa chỉ <span class="text-red-500">*</span></label>
          <input type="text" name="address" value="{{ old('address', $branch->address ?? '') }}" required
            class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-wide mb-1.5">Latitude</label>
            <input type="number" step="any" name="lat" value="{{ old('lat', $branch->lat ?? '') }}"
              class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-wide mb-1.5">Longitude</label>
            <input type="number" step="any" name="lng" value="{{ old('lng', $branch->lng ?? '') }}"
              class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-wide mb-1.5">Trạng thái</label>
          <select name="status" class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all">
            <option value="open"   {{ (old('status', $branch->status ?? 'open') === 'open')   ? 'selected' : '' }}>🟢 Đang mở</option>
            <option value="closed" {{ (old('status', $branch->status ?? '') === 'closed') ? 'selected' : '' }}>🔴 Đóng cửa</option>
          </select>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" class="flex-1 bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#FF6B35] hover:bg-[#e55a25] transition-colors">
            💾 {{ isset($branch) ? 'Cập nhật' : 'Tạo chi nhánh' }}
          </button>
          <a href="{{ route('admin.branch') }}" class="px-6 py-3 border-2 border-[#444] rounded-xl font-bold text-sm text-gray-400 hover:text-white hover:border-[#666] transition-all flex items-center">
            Huỷ
          </a>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
