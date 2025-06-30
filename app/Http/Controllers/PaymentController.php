<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    protected $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function checkout($sert_id, $asesi_id, Request $request)
    {
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
        
        $result = $this->checkoutService->processCheckout($data);

        // Jika service mengembalikan null, berarti pembayaran sudah lunas.
        if (is_null($result)) {
            return redirect()->route('asesi.applied.show', ['sert_id' => $sert_id, 'asesi_id' => $asesi_id])
                             ->with('error', 'Pembayaran untuk sertifikasi ini sudah lunas.');
        }

        return view('asesi.sertifikasi.bayar.payment-page', [
            'asesi' => $asesi,
            'sertification' => $sertification,
            'snap_token' => $result['snap_token'],
            'transaction' => $result['transaction'],
        ]);
    }

    public function handleWebhook(Request $request)
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;

        $transaction = Transaction::find($orderId);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Logika update status berdasarkan notifikasi
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                // Transaksi berhasil dan aman
                $transaction->status = 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            // Transaksi berhasil
            $transaction->status = 'paid';
        } else if ($transactionStatus == 'pending') {
            // Menunggu pembayaran
            $transaction->status = 'pending';
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            // Transaksi gagal atau dibatalkan
            $transaction->status = 'failed';
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
