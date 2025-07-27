<?php
namespace App\Services;

use App\Repositories\TransactionRepositoryInterface;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction; // Pastikan untuk mengimpor model Transaction

class CheckoutService
{
    protected $transactionRepository;
    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_ENV') === 'production';
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function processCheckout($data)
    {
        // Cari transaksi yang sudah ada untuk asesi ini
        $transaction = Transaction::where('asesi_id', $data['asesi_id'])->latest()->first();

        // Jika tidak ada transaksi, atau transaksi terakhir gagal/kadaluarsa, buat yang baru.
        if (!$transaction || in_array($transaction->status, ['failed', 'expire', 'cancel'])) {
            $transaction = $this->transactionRepository->createTransaction([
                'asesi_id' => $data['asesi_id'],
                'sertification_id' => $data['sertification_id'],
                'biaya' => $data['biaya'],
                'tipe' => $data['tipe'],
                'status' => 'pending',
            ]);

            // SOLUSI: Segarkan model untuk mendapatkan invoice_number yang baru dibuat oleh event.
            $transaction->refresh();
        } else if ($transaction->status === 'paid') {
            // Jika sudah lunas, kembalikan null untuk menandakan tidak perlu bayar lagi.
            return null;
        }
        // Jika statusnya 'pending', kita akan gunakan ulang transaksi yang sama.

        // --- LOGIKA BARU UNTUK MENCEGAH ERROR ---

        // 1. Jika transaksi ini sudah punya snap_token, langsung gunakan token itu.
        if ($transaction->snap_token) {
            return [
                'transaction' => $transaction,
                'snap_token' => $transaction->snap_token
            ];
        }
        // dd($transaction);
        // 2. Jika belum ada snap_token, buat yang baru.
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->id, // Sekarang ini dijamin tidak akan null
                // 'gross_amount' => (int) $data['biaya']
                'gross_amount' => $data['biaya']
            ],
            'customer_details'=> [
                'first_name' => $data['name'],
                'email'=> $data['email'],
                'phone' => $data['no_tlp_hp']
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // 3. Simpan snap_token yang baru dibuat ke database untuk digunakan lagi nanti.
        $transaction->snap_token = $snapToken;
        $transaction->save();

        return [
            'transaction' => $transaction,
            'snap_token' => $snapToken
        ];
    }
}
