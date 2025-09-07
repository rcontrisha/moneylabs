<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::instance('cart')->content();
        Log::info($cartItems);
        return view('cart',compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        Cart::instance('cart')->add([
            'id'      => $request->id,
            'name'    => $request->name,
            'qty'     => $request->quantity,
            'price'   => $request->price,
            'options' => [
                'size'      => $request->size,
                'condition' => $request->condition,
            ]
        ])->associate('App\Models\Product');

        return response()->json([
            'status'  => 200,
            'message' => 'Success ! Item Successfully added to your cart.'
        ]);
    }

    public function increase_item_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function reduce_item_quantity($rowId){
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function remove_item_from_cart($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {        
        $coupon_code = $request->coupon_code;
        if(isset($coupon_code))
        {
            $coupon = Coupon::where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())->where('cart_value','<=',Cart::instance('cart')->subtotal())->first();
            if(!$coupon)
            {
                return back()->with('error','Invalid coupon code!');
            }
            session()->put('coupon',[
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);
            $this->calculateDiscounts();
            return back()->with('status','Coupon code has been applied!');
        }
        else{
            return back()->with('error','Invalid coupon code!');
        }        
    }

    public function calculateDiscounts()
    {
        $discount = 0;
        if(session()->has('coupon'))
        {
            if(session()->get('coupon')['type'] == 'fixed')
            {
                $discount = session()->get('coupon')['value'];
            }
            else
            {
                $discount = (Cart::instance('cart')->subtotal() * session()->get('coupon')['value'])/100;
            }
            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount; 
            session()->put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval(Cart::instance('cart')->subtotal() - $discount),2,'.',''),
                'tax' => number_format(floatval((($subtotalAfterDiscount * config('cart.tax'))/100)),2,'.',''),
                'total' => number_format(floatval($subtotalAfterDiscount + $taxAfterDiscount),2,'.','')
            ]);            
        }
    }

    public function remove_coupon_code()
    {
        session()->forget('coupon');
        session()->forget('discounts');
        return back()->with('status','Coupon has been removed!');
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route("login");
        }
        $address = Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();              
        return view('checkout',compact("address"));
    }

    public function place_order(Request $request)
    {
        $user_id = Auth::id();
        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
        $mode = $request->mode;

        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric',
                'zip' => 'required|numeric',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required'
            ]);

            $address = new Address();
            $address->user_id = $user_id;
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->zip = $request->zip;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->address = $request->address;
            $address->locality = $request->locality;
            $address->landmark = $request->landmark;
            $address->country = 'Indonesia';
            $address->isdefault = true;
            $address->save();
        }

        $this->setAmountForCheckout();

        $order = new Order();
        $order_code = $order->id . '-' . uniqid();
        $order->user_id = $user_id;
        $order->order_code = $order_code;
        $order->subtotal = floatval(str_replace(',', '', session()->get('checkout')['subtotal']));
        $order->discount = floatval(str_replace(',', '', session()->get('checkout')['discount']));
        $order->tax = floatval(str_replace(',', '', session()->get('checkout')['tax']));
        $order->total = floatval(str_replace(',', '', session()->get('checkout')['total']));
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderitem = new OrderItem();
            $orderitem->product_id = $item->id;
            $orderitem->order_id = $order->id;
            $orderitem->price = $item->price;
            $orderitem->quantity = $item->qty;
            $orderitem->save();       
        }

        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->order_code = $order_code;
        $transaction->order_id = (string) $order->id;
        $transaction->mode = $mode;
        $transaction->status = "pending";
        $transaction->save();

        $snapToken = $this->generateSnapToken($order, $order_code);

        if ($mode == 'cod') {
            return redirect()->route('cart.confirmation');
        }

        return response()->json([
            'snap_token' => $snapToken,
            'message' => 'Proceed to payment',
            'status' => 200,
        ]);
    }

    public function save_address(Request $request)
    {
        $user_id = Auth::id();

        $request->validate([
            'name'     => 'required',
            'phone'    => 'required',
            'zip'      => 'required',
            'state'    => 'required',
            'city'     => 'required',
            'address'  => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        // cek address existing
        $address = Address::where('user_id', $user_id)
            ->where('isdefault', true)
            ->first();

        if (!$address) {
            $address = new Address();
            $address->user_id   = $user_id;
            $address->isdefault = true;
        }

        // isi manual (bypass fillable)
        $address->name     = $request->name;
        $address->phone    = $request->phone;
        $address->zip      = $request->zip;
        $address->state    = $request->state;
        $address->city     = $request->city;
        $address->address  = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country  = 'Indonesia';
        $address->save();

        return response()->json([
            'message' => 'Address saved successfully!',
            'address' => $address
        ]);
    }

    private function generateSnapToken($order, $order_code)
    {
        // Config Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $items = [];
        $total_item_price = 0;

        // Ambil produk dari cart
        foreach (Cart::instance('cart')->content() as $item) {
            $items[] = [
                'id' => $item->id,
                'price' => (int)$item->price,
                'quantity' => (int)$item->qty,
                'name' => $item->name,
            ];
            $total_item_price += $item->price * $item->qty;
        }

        // Ambil discount, tax dari order 
        $discount = (int) ($order->discount ?? 0);
        $tax = (int) ($order->tax ?? 0);

        // Masukkan discount sebagai item negatif jika ada
        if ($discount > 0) {
            $items[] = [
                'id' => 'discount',
                'price' => -$discount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
            $total_item_price -= $discount;
        }

        // Masukkan tax sebagai item tersendiri jika ada
        if ($tax > 0) {
            $items[] = [
                'id' => 'tax',
                'price' => $tax,
                'quantity' => 1,
                'name' => 'Tax',
            ];
            $total_item_price += $tax;
        }

        // Kalau ada biaya shipping, bisa ditambahkan di sini juga
        // Contoh:
        // $shipping = (int) ($order->shipping ?? 0);
        // if ($shipping > 0) {
        //     $items[] = [
        //         'id' => 'shipping',
        //         'price' => $shipping,
        //         'quantity' => 1,
        //         'name' => 'Shipping Fee',
        //     ];
        //     $total_item_price += $shipping;
        // }

        $payload = [
            'transaction_details' => [
                'order_id' => $order_code,
                'gross_amount' => (int) ceil($order->total),
            ],
            'customer_details' => [
                'first_name' => $order->name,
                'last_name' => '',
                'email' => Auth::user()->email ?? 'guest@example.com',
                'phone' => $order->phone,
            ],
            'shipping_address' => [
                'first_name' => $order->name,
                'last_name' => '',
                'phone' => $order->phone,
                'address' => $order->address,
                'city' => $order->city,
                'postal_code' => $order->zip,
                'country_code' => 'IDN',
            ],
            'item_details' => $items,
        ];

        // Log payload buat debug
        Log::debug('Midtrans Payload:', $payload);
        $payload['notification_url'] = route('cart.payment.callback');

        try {
            $snapToken = Snap::getSnapToken($payload);

            // Kirim balik token dan payload supaya bisa dicek di frontend
            // return $snapToken;
            return response()->json([
                'token' => $snapToken,
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        // Log full payload untuk debugging awal
        Log::info('[Midtrans] Notification received', $request->all());

        try {
            // Set konfigurasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Buat instance notifikasi
            $notification = new \Midtrans\Notification();

            // Log detail notifikasi dari Midtrans
            Log::info('[Midtrans] Parsed Notification:', [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'payment_type' => $notification->payment_type,
                'fraud_status' => $notification->fraud_status,
                'gross_amount' => $notification->gross_amount,
            ]);

            // Ambil data transaksi berdasarkan order_id
            $orderId = $notification->order_id;
            $transaction = Transaction::where('order_code', $orderId)->first();

            if (!$transaction) {
                Log::warning("[Midtrans] Transaction not found for order_id: {$orderId}");
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Update status transaksi berdasarkan status dari Midtrans
            switch ($notification->transaction_status) {
                case 'capture':
                case 'settlement':
                    $transaction->status = 'settlement';
                    Log::info("[Midtrans] Transaction settled for order_id: {$orderId}");
                    break;

                case 'pending':
                    $transaction->status = 'pending';
                    Log::info("[Midtrans] Transaction pending for order_id: {$orderId}");
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                    $transaction->status = 'failed';
                    Log::info("[Midtrans] Transaction failed (denied/cancelled/expired) for order_id: {$orderId}");
                    break;

                default:
                    Log::warning("[Midtrans] Unhandled transaction status: {$notification->transaction_status} for order_id: {$orderId}");
                    break;
            }

            $transaction->save();
            Log::info("[Midtrans] Transaction updated successfully for order_id: {$orderId}");

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('[Midtrans] Notification handler error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function setAmountForCheckout()
    { 
        if(!Cart::instance('cart')->count() > 0)
        {
            session()->forget('checkout');
            return;
        }    
        if(session()->has('coupon'))
        {
            session()->put('checkout',[
                'discount' => session()->get('discounts')['discount'],
                'subtotal' =>  session()->get('discounts')['subtotal'],
                'tax' =>  session()->get('discounts')['tax'],
                'total' =>  session()->get('discounts')['total']
            ]);
        }
        else
        {
            session()->put('checkout',[
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total()
            ]);
        }
    }

    public function confirmation(Request $request)
    {
        $orderCode = $request->query('order_id');

        $order = Order::with('orderItems.product')
            ->where('order_code', $orderCode)
            ->firstOrFail();

        return view('order-confirmation', compact('order'));
    }
}
