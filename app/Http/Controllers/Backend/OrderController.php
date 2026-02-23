<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request)
{
    $query = Order::with(['items.product'])->latest();

    if ($request->from && $request->to) {
        $query->whereBetween('order_date', [$request->from, $request->to]);
    }

    $orders = $query->get();

    $products = Product::select('id', 'name', 'sell_price', 'purchase_price')->get();

    // Existing totals
    $grand = $orders->sum('grand_total');
    $paid  = $orders->sum('paid_amount');
    $due   = $orders->sum('due_amount');

    // NEW: Purchase Cost from order items
    $purchaseCost = $orders->sum(function ($order) {
        return $order->items->sum(function ($item) {
            return $item->purchase_price * $item->quantity;
        });
    });

    // NEW: Profit / Benefit
    $profit = $grand - $purchaseCost;

    $totals = [
        'grand'    => $grand,
        'paid'     => $paid,
        'due'      => $due,
        'purchase' => $purchaseCost,
        'profit'   => $profit,
    ];

    return view('orders.index', compact('orders', 'products', 'totals'));
}

    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'products'   => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            $subtotal = 0;

            foreach ($request->products as $item) {

                $product = Product::findOrFail($item['product_id']);
                $qty = $item['qty'];

                $subtotal += $qty * $product->sell_price;
            }

            $discount   = $request->discount ?? 0;
            $vatPercent = $request->vat_percent ?? 0;

            $afterDiscount = $subtotal - $discount;
            $vatAmount     = ($afterDiscount * $vatPercent) / 100;
            $grandTotal    = $afterDiscount + $vatAmount;

            $paid = $request->paid_amount ?? 0;
            $due  = $grandTotal - $paid;

            $order = Order::create([
                'order_date'   => $request->order_date,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'vat_percent'  => $vatPercent,
                'vat_amount'   => $vatAmount,
                'grand_total'  => $grandTotal,
                'paid_amount'  => $paid,
                'due_amount'   => $due,
            ]);

            foreach ($request->products as $item) {

                $product = Product::findOrFail($item['product_id']);
                $qty = $item['qty'];

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $product->id,
                    'quantity'        => $qty,
                    'purchase_price'  => $product->purchase_price,
                    'sell_price'      => $product->sell_price,
                    'total'           => $qty * $product->sell_price,
                ]);

                // reduce stock
                $product->decrement('quantity', $qty);
            }

            DB::commit();
            return back()->with('success', 'Order created');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_date' => 'required|date',
            'products'   => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            // restore previous stock
            foreach ($order->items as $oldItem) {
                Product::where('id', $oldItem->product_id)
                    ->increment('quantity', $oldItem->quantity);
            }

            $order->items()->delete();

            $subtotal = 0;

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = $item['qty'];
                $subtotal += $qty * $product->sell_price;
            }

            $discount   = $request->discount ?? 0;
            $vatPercent = $request->vat_percent ?? 0;

            $afterDiscount = $subtotal - $discount;
            $vatAmount     = ($afterDiscount * $vatPercent) / 100;
            $grandTotal    = $afterDiscount + $vatAmount;

            $paid = $request->paid_amount ?? 0;
            $due  = $grandTotal - $paid;

            $order->update([
                'order_date'   => $request->order_date,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'vat_percent'  => $vatPercent,
                'vat_amount'   => $vatAmount,
                'grand_total'  => $grandTotal,
                'paid_amount'  => $paid,
                'due_amount'   => $due,
            ]);

            foreach ($request->products as $item) {

                $product = Product::findOrFail($item['product_id']);
                $qty = $item['qty'];

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $product->id,
                    'quantity'        => $qty,
                    'purchase_price'  => $product->purchase_price,
                    'sell_price'      => $product->sell_price,
                    'total'           => $qty * $product->sell_price,
                ]);

                // reduce stock again
                $product->decrement('quantity', $qty);
            }

            DB::commit();
            return back()->with('success', 'Order updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function destroy(Order $order)
    {
        DB::beginTransaction();

        try {

            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('quantity', $item->quantity);
            }

            $order->delete();

            DB::commit();
            return back()->with('success', 'Order deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
