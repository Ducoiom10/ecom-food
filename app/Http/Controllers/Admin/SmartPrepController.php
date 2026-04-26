<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartPrepLog;

class SmartPrepController extends Controller
{
    public function index()
    {
        $recommendations = SmartPrepLog::with('inventoryItem')
            ->whereNull('acknowledged_at')
            ->orderByRaw("CASE urgency WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->get();

        return view('admin.smart-prep', [
            'recommendations' => $recommendations,
            'weather'         => ['icon' => '🌧️', 'temp' => 24, 'label' => 'Mưa lớn', 'impact' => 'Mưa lớn → Đơn ship tăng 85%!', 'deliveryBoost' => 85],
            'mealPeriod'      => ['name' => 'Bữa trưa', 'emoji' => '☀️', 'peak' => true],
            'currentWeather'  => 'rainy',
            'criticalCount'   => $recommendations->where('urgency', 'critical')->count(),
            'highCount'       => $recommendations->where('urgency', 'high')->count(),
            'pendingRecs'     => $recommendations->count(),
            'acknowledgedRecs'=> SmartPrepLog::whereNotNull('acknowledged_at')->count(),
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
