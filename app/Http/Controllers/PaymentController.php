<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Sertification;
use App\Models\Asesi;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
class PaymentController extends Controller
{
    protected $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }
    private function storeFileWithUniqueName(UploadedFile $file, string $baseDirectory): array
    {
        // id unik berdasarkan timestamp
        $uniqueId = uniqid() . '-' . now()->timestamp;
        // nama file asli tanpa extension dijadiin slug + unik id + ekstensinya tadi
        $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();
        // Simpan file dengan nama baru
        $path = $file->storeAs($baseDirectory, $newFilename, 'public');
        return ['path' => $path];
    }
    // untuk nampilin halaman checkout, jadi setelah asesi tekan bayar, nanti diarahin ke halaman invoice, disitu, asesi dapat bayar dgn midtrans
    public function checkout($sert_id, $asesi_id, Request $request)
    {
        $sertification = Sertification::with('asesor', 'skema')->find($sert_id);
        $asesi = Asesi::with('student.user')->find($asesi_id);
        $data = [
            'asesi_id' => $asesi->id,
            'sertification_id' => $sertification->id,
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

    // untuk nerima webhook dari midtrans, (yg diterima itu status pembayaran)
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
    // untuk upload bukti pembayaran dari sisi asesi
    public function buktipembayaran($sert_id, $asesi_id, Request $request)
    {
        dd($request);
        // $sertification = Sertification::with('asesor', 'skema')->find($sert_id);
        // $asesi = Asesi::with('student.user')->find($asesi_id);
        $transaction = Transaction::where('asesi_id', $asesi_id)->find()->latest();
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);
        if ($request->hasFile('bukti_pembayaran')) {
            if ($transaction->$bukti_pembayaran && Storage::disk('public')->exists($transaction->$bukti_pembayaran)) {
                Storage::disk('public')->delete($transaction->$bukti_pembayaran);
            }
            $fileData = $this->storeFileWithUniqueName($request->file('bukti_pembayaran'), 'bukti_pembayaran');
            $transaction->$fileField = $fileData['path'];
        }
        $transaction['tipe'] = 'manual';
        $transaction['status'] = 'pending';
        $transaction->save();
        return redirect(route('asesi.applied.payment.create',[$sert_id, $asesi_id]))->with('success', 'Berhasil update bukti pembayaran, admin akan memverifikasi bukti pembayaran Anda.');
    }
    // untuk memverifikasi bukti pembayaran yg sudah diinput asesi, yg verifikasi tuh admin
    public function updateStatusBayar($id, Request $request)
    {
        $asesi = Asesi::find($id);
        $transaction = $asesi->transaction;
        // Memperbarui status sesuai dengan yang diterima dari form
        $transaction->status = $request->status;
        $transaction->save();
        return redirect()->back()->with('success', 'Status pembayaran asesi berhasil diperbarui');
    }
}
