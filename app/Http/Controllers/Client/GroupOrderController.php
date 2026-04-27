<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Product;
use App\Models\Group\GroupRoom;
use App\Models\Group\Participant;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupOrderController extends Controller
{
    public function index()
    {
        $branches = \App\Models\System\Branch::where('status', 'open')->get();
        return view('client.group-orders.index', compact('branches'));
    }

    public function create(Request $request)
    {
        $request->validate(['branch_id' => 'required|exists:branches,id']);

        $room = GroupRoom::create([
            'branch_id' => $request->branch_id,
            'room_code' => strtoupper(Str::random(6)),
            'status'    => 'active',
            'is_locked' => false,
        ]);

        $participant = Participant::create([
            'room_id'      => $room->id,
            'user_id'      => auth()->id(),
            'display_name' => auth()->user()->name,
            'emoji'        => '👑',
            'is_host'      => true,
        ]);

        $room->update(['host_id' => $participant->id]);

        return redirect()->route('client.group-order.room', $room->room_code);
    }

    public function join(Request $request)
    {
        $code = strtoupper(trim($request->code));
        $room = GroupRoom::where('room_code', $code)->firstOrFail();

        $existing = $room->participants()->where('user_id', auth()->id())->first();
        if (!$existing) {
            Participant::create([
                'room_id'      => $room->id,
                'user_id'      => auth()->id(),
                'display_name' => auth()->user()->name,
                'emoji'        => collect(['🍜','🍗','🧋','🍚','🥗','🍱'])->random(),
                'is_host'      => false,
            ]);
        }

        return redirect()->route('client.group-order.room', $code);
    }

    public function room(string $code)
    {
        $room = GroupRoom::where('room_code', $code)
            ->with('participants.user', 'participants.orders.items.product')
            ->firstOrFail();

        $myParticipant = $room->participants->firstWhere('user_id', auth()->id());
        $myOrder       = $myParticipant?->orders->first();
        $myItems       = $myOrder?->items ?? collect();
        $products      = Product::with('category')->where('is_active', true)->get();
        $grandTotal    = $room->participants->sum(fn($p) => $p->orders->sum('grand_total'));

        return view('client.group-orders.room', [
            'room'          => $room,
            'products'      => $products,
            'myItems'       => $myItems,
            'myItemCount'   => $myItems->sum('quantity'),
            'myTotal'       => $myItems->sum(fn($i) => $i->price * $i->quantity),
            'grandTotal'    => $grandTotal,
            'isHost'        => $myParticipant?->is_host ?? false,
            'myParticipant' => $myParticipant,
        ]);
    }

    public function addItem(Request $request, string $code)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'action' => 'required|in:add,remove']);

        $room        = GroupRoom::where('room_code', $code)->where('is_locked', false)->firstOrFail();
        $participant = $room->participants()->where('user_id', auth()->id())->firstOrFail();
        $product     = Product::findOrFail($request->product_id);

        $order = $participant->orders()->firstOrCreate(
            ['group_room_id' => $room->id],
            [
                'order_number'   => Order::generateOrderNumber('GRP'),
                'user_id'        => auth()->id(),
                'branch_id'      => $room->branch_id,
                'status'         => 'pending',
                'delivery_mode'  => 'pickup',
                'payment_method' => 'cash',
                'subtotal'       => 0,
                'grand_total'    => 0,
            ]
        );

        $item = $order->items()->where('product_id', $product->id)->first();

        if ($request->action === 'add') {
            $item ? $item->increment('quantity')
                  : $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => $product->base_price]);
        } elseif ($request->action === 'remove' && $item) {
            $item->quantity > 1 ? $item->decrement('quantity') : $item->delete();
        }

        $order->update(['grand_total' => $order->items()->sum(DB::raw('price * quantity'))]);

        return back();
    }

    public function lock(string $code)
    {
        GroupRoom::where('room_code', $code)->firstOrFail()->update(['is_locked' => true, 'status' => 'completed']);
        return redirect()->route('client.split-bill', $code);
    }

    public function splitBill(string $code)
    {
        $room = GroupRoom::where('room_code', $code)
            ->with('participants.orders.items.product')
            ->firstOrFail();

        $bills      = $room->participants;
        $grandTotal = $bills->sum(fn($p) => $p->orders->sum('grand_total'));

        return view('client.group-orders.split-bill', compact('room', 'bills', 'grandTotal'));
    }
}
