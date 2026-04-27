@props(['type' => 'info', 'message' => ''])

@php
$config = [
  'success' => ['bg'=>'bg-green-50',  'border'=>'border-green-400',  'text'=>'text-green-700',  'icon'=>'✅'],
  'error'   => ['bg'=>'bg-red-50',    'border'=>'border-red-400',    'text'=>'text-red-700',    'icon'=>'⚠️'],
  'warning' => ['bg'=>'bg-yellow-50', 'border'=>'border-yellow-400', 'text'=>'text-yellow-700', 'icon'=>'⚠️'],
  'info'    => ['bg'=>'bg-blue-50',   'border'=>'border-blue-400',   'text'=>'text-blue-700',   'icon'=>'ℹ️'],
];
$c = $config[$type] ?? $config['info'];
@endphp

@if($message || $slot->isNotEmpty())
<div class="{{ $c['bg'] }} border-2 {{ $c['border'] }} rounded-xl px-4 py-3 flex items-center gap-2 {{ $c['text'] }} text-sm font-bold">
  <span>{{ $c['icon'] }}</span>
  <span>{{ $message ?: $slot }}</span>
</div>
@endif
