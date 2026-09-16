<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = Cart::with(['items.product' => function ($q) {
            $q->with('images');
        }])->where('owner_id', Auth::id())->first();

        $cartItems = $cart ? $cart->items : collect();

        return view('owner.cart.index', compact('cart', 'cartItems'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'Selected product does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
        ]);

        $product = Product::find($validated['product_id']);

        if ($product->status !== 'active') {
            return back()->with('error', 'This product is no longer available.');
        }

        if ($product->stock_quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock available. Only ' . $product->stock_quantity . ' items left.');
        }

        $cart = Cart::firstOrCreate(['owner_id' => Auth::id()]);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $validated['quantity'];
            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Cannot add more items. Stock limit reached.');
            }
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
        }

        return redirect()->route('owner.cart.index')
            ->with('success', 'Product added to cart.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        if ($item->cart->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to modify this cart.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
        ]);

        $product = Product::find($item->product_id);

        if ($validated['quantity'] > $product->stock_quantity) {
            return back()->with('error', 'Insufficient stock available. Only ' . $product->stock_quantity . ' items left.');
        }

        $item->update(['quantity' => $validated['quantity']]);

        return redirect()->route('owner.cart.index')
            ->with('success', 'Cart updated successfully.');
    }

    public function remove(CartItem $item): RedirectResponse
    {
        if ($item->cart->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to modify this cart.');
        }

        $item->delete();

        return redirect()->route('owner.cart.index')
            ->with('success', 'Item removed from cart.');
    }

    public function checkout(): RedirectResponse
    {
        $cart = Cart::with('items.product')->where('owner_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        foreach ($cart->items as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return back()->with('error', 'Insufficient stock for ' . $item->product->name . '.');
            }
        }

        $order = DB::transaction(function () use ($cart) {
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->product->effective_price * $item->quantity;
            });

            $order = Order::create([
                'owner_id' => Auth::id(),
                'order_number' => 'ORD-' . time(),
                'total_amount' => $totalAmount,
                'status' => 'placed',
                'shipping_address' => Auth::user()->address,
                'order_date' => now(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_each' => $item->product->effective_price,
                ]);

                Product::where('id', $item->product_id)
                    ->decrement('stock_quantity', $item->quantity);
            }

            $cart->items()->delete();
            $cart->delete();

            return $order;
        });

        return redirect()->route('owner.orders.show', $order)
            ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);
    }
}
