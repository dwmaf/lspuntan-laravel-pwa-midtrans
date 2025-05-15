<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function checkout(Request $request)
    {
        $data = [
            'asesi_id' => $request->input('asesi_id'),
            'biaya' => $request->input('biaya'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'no_tlp_hp' => $request->input('no_tlp_hp'),
        ];

        $result = $this->checkoutService->processCheckout($data);

        return response()->json([
            'snap_token' => $result['snap_token'],
            'transaction' => $result['transaction']
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $serverKey = config('midtrans.server_key');

        // validasi signature key dari midtrans
        $signatureKey = hash(
            "sha512",
            $request->order_id . $request->staus_code . $request->gross_amount . $serverKey
        );

        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid isgnature key'], 403);
        }

        $transaction = Transaction::find($request->order_id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        } elseif ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
            $transaction->status = 'paid';
        } elseif ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
            $transaction->status = 'failed';
        } elseif ($request->transaction_status == 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();

        return response()->json(['message'=>'Webhook processed successfully']);
    }
}
