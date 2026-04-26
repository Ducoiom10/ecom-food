<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GroupRoom;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupOrderController extends Controller
{
    public function index()
    {
        $roomCode = strtoupper(Str::random(6)) . rand(100, 999);
        return view('client.group-order', compact('roomCode'));
    }

    public function create(Request $request)
    {
        $request->validate(['branch_id' => 'required|exists:branches,id']);

        $room = GroupRoom::create([
            'branch_id' => $request->branch_id,
            'room_code' => strtoupper(Str::random(6)),
            'status'    => 'active',
        ]);

        $participant = Participant::create([
            'room_id'      => $room->id,
            'user_id'      => auth()->id(),
            'display_name' => auth()->user()->name,
            'is_host'      => true,
        ]);

        $room->update(['host_id' => $participant->id]);

        return redirect()->route('client.group-order.room', $room->room_code);
    }

    public function join(Request $request)
    {
        return redirect()->route('client.group-order.room', strtoupper($request->code));
    }

    public function room(string $code)
    {
        $room = GroupRoom::where('room_code', $code)
            ->with('participants.orders.items.product')
            ->firstOrFail();

        return view('client.group-order-room', [
            'room'        => $room,
            'menuItems'   => [],
            'myItems'     => [],
            'myItemCount' => 0,
            'myTotal'     => 0,
            'grandTotal'  => 0,
            'isHost'      => $room->host?->user_id === auth()->id(),
            'menuPrices'  => [],
            'menuNames'   => [],
        ]);
    }

    public function addItem(Request $request, string $code)
    {
        // TODO: Sprint 2
        return back();
    }

    public function lock(string $code)
    {
        $room = GroupRoom::where('room_code', $code)->firstOrFail();
        $room->update(['is_locked' => true]);

        return redirect()->route('client.split-bill', $code);
    }

    public function splitBill(string $code)
    {
        $room = GroupRoom::where('room_code', $code)
            ->with('participants.orders.items')
            ->firstOrFail();

        $bills      = $room->participants;
        $grandTotal = $bills->sum(fn($p) => $p->orders->sum('grand_total'));

        return view('client.split-bill', compact('room', 'bills', 'grandTotal'));
    }
}
