<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Generate an invoice for a given order.
     *
     * @param Order $order
     * @return string The path to the generated invoice.
     */
    public function generateInvoice(Order $order)
    {
        // Create invoices directory in public storage if it doesn't exist
        if (!Storage::disk('public')->exists('invoices')) {
            Storage::disk('public')->makeDirectory('invoices');
        }

        $data = [
            'order' => $order,
            'orderItems' => $order->orderItems
        ];

        $pdf = PDF::loadView('invoice.template', $data);

        // Generate file name using order code
        $fileName = 'invoice--' . ltrim($order->order_code, '-') . '.pdf';
        $path = 'invoices/' . $fileName;
        
        // Store file in public storage
        Storage::disk('public')->put($path, $pdf->output());

        // Update order with public path
        $order->invoice_path = $path;
        $order->save();

        return $path;
    }

    /**
     * Download the invoice for a given order.
     *
     * @param string $order_code
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoice($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $fileName = 'invoice--' . ltrim($order_code, '-') . '.pdf';
        $path = 'invoices/' . $fileName;

        if (!Storage::disk('public')->exists($path)) {
            // Generate if doesn't exist
            $this->generateInvoice($order);
        }

        if (Storage::disk('public')->exists($path)) {
            return response()->download(storage_path('app/public/' . $path));
        }

        abort(404, 'Invoice not found.');
    }
}