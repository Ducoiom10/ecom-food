@extends('layouts.client')
@section('title', 'Thông báo')
@section('page_heading', 'Thông báo')

@section('content')
    <div class="p-4 lg:p-8 max-w-2xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="font-black text-[#1C1C1C] text-lg">📬 Thông báo</h1>
            @if ($notifications->where('is_read', false)->count() > 0)
                <button onclick="markAllRead()" class="text-xs font-bold text-[#FF6B35] hover:underline">
                    Đánh dấu tất cả đã đọc
                </button>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl p-4 shadow-[3px_3px_0px_#1C1C1C] transition-all {{ $notification->is_read ? 'opacity-70' : '' }}"
                    id="notif-{{ $notification->id }}">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-10 h-10 rounded-full {{ $notification->is_read ? 'bg-gray-100' : 'bg-[#FF6B35]' }} flex items-center justify-center text-lg flex-shrink-0">
                            {{ $notification->is_read ? '✉️' : '🔔' }}
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-[#1C1C1C] text-sm">{{ $notification->title }}</p>
                            <p class="text-gray-500 text-sm mt-0.5">{{ $notification->body }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if (!$notification->is_read)
                            <button onclick="markRead({{ $notification->id }})"
                                class="text-xs bg-[#FFD23F] text-[#1C1C1C] font-black px-2 py-1 rounded-lg border border-[#1C1C1C]">
                                Đọc
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-4xl mb-2">📭</div>
                    <p class="text-gray-400 text-sm">Chưa có thông báo nào</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>

    <script>
        async function markRead(id) {
            try {
                const res = await fetch(`/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function markAllRead() {
            try {
                const res = await fetch('{{ route('client.notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.ok) {
                    location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }
    </script>
@endsection
