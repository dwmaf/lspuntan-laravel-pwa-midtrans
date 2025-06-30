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
                'biaya' => $data['biaya'],
                'tipe' => $data['tipe'],
                'status' => 'pending',
            ]);
        } else if ($transaction->status === 'paid') {
            // Jika sudah lunas, kembalikan null untuk menandakan tidak perlu bayar lagi.
            return null;
        }
        // Jika statusnya 'pending', kita akan gunakan ulang transaksi yang sama.

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->id, // Gunakan ID transaksi yang ada atau yang baru dibuat
                'gross_amount' => (int) $data['biaya']
            ],
            'customer_details'=> [
                'first_name' => $data['name'],
                'email'=> $data['email'],
                'phone' => $data['no_tlp_hp']
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return [
            'transaction' => $transaction,
            'snap_token' => $snapToken
        ];
    }
}
