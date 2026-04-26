<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartPrepLog;

class SmartPrepController extends Controller
{
    public function index()
    {
        $weather = request('weather', 'rainy');

        $weatherConfig = [
            'sunny'  => ['icon' => '☀️',  'temp' => 35, 'label' => 'Nắng nóng',  'impact' => 'Nắng nóng → Đơn đồ uống tăng 40%!',    'deliveryBoost' => 10],
            'cloudy' => ['icon' => '☁️',  'temp' => 28, 'label' => 'Nhiều mây',  'impact' => 'Thời tiết bình thường, dự báo ổn định.', 'deliveryBoost' => 0],
            'rainy'  => ['icon' => '🌧️', 'temp' => 24, 'label' => 'Mưa lớn',    'impact' => 'Mưa lớn → Đơn ship tăng 85%!',          'deliveryBoost' => 85],
            'stormy' => ['icon' => '⛈️', 'temp' => 20, 'label' => 'Bão',         'impact' => 'Bão → Đơn ship tăng 120%! Chuẩn bị gấp!','deliveryBoost' => 120],
        ];

        $hour = now()->hour;
        $mealPeriod = match(true) {
            $hour >= 6  && $hour < 10 => ['name' => 'Bữa sáng',  'emoji' => '🌅', 'peak' => false],
            $hour >= 10 && $hour < 14 => ['name' => 'Bữa trưa',  'emoji' => '☀️', 'peak' => true],
            $hour >= 14 && $hour < 17 => ['name' => 'Xế chiều',  'emoji' => '🌤️', 'peak' => false],
            $hour >= 17 && $hour < 21 => ['name' => 'Bữa tối',   'emoji' => '🌆', 'peak' => true],
            default                   => ['name' => 'Đêm khuya', 'emoji' => '🌙', 'peak' => false],
        };

        $recommendations = SmartPrepLog::with('inventoryItem')
            ->whereNull('acknowledged_at')
            ->orderByRaw("CASE urgency WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->get();

        return view('admin.smart-prep', [
            'recommendations'  => $recommendations,
            'weather'          => $weatherConfig[$weather] ?? $weatherConfig['rainy'],
            'currentWeather'   => $weather,
            'mealPeriod'       => $mealPeriod,
            'criticalCount'    => $recommendations->where('urgency', 'critical')->count(),
            'highCount'        => $recommendations->where('urgency', 'high')->count(),
            'pendingRecs'      => $recommendations->count(),
            'acknowledgedRecs' => SmartPrepLog::whereNotNull('acknowledged_at')->count(),
        ]);
    }

    public function acknowledge(int $id)
    {
        SmartPrepLog::findOrFail($id)->update([
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
        ]);

        return back();
    }
}
