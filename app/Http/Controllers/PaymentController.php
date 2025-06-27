<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function checkout($sert_id, $asesi_id, Request $request)
    {
        // dd($request);
        $sertification = Sertification::with('asesor','skema')->find($sert_id);
        $asesi = Asesi::with('student.user')->find($asesi_id);
        $data = [
            'asesi_id' => $asesi->id,
            'biaya' => $sertification->harga,
            'name' => $asesi->student->name,
            'email' => $asesi->student->user->email,
            'no_tlp_hp' => $asesi->student->no_tlp_hp,
            'tipe' => 'midtrans',
        ];
        dd($data);
        $result = $this->checkoutService->processCheckout($data);

        return view('asesi.sertifikasi.bayar.payment-page', [
            'snap_token' => $result['snap_token'],
            'transaction' => $result['transaction'],
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

        return response()->json(['message' => 'Webhook processed successfully']);
    }

    public function buktipembayaran($sert_id, $asesi_id, Request $request)
    {
        dd($request);
        $sertification = Sertification::with('asesor','skema')->find($sert_id);
        $asesi = Asesi::with('student.user')->find($asesi_id);
        $validatedData=$request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        Transaction::create($validatedData);
    }
}
