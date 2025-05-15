<?php
namespace App\Services;

use App\Repositories\TransactionRepositoryInterface;
use Midtrans\Snap;
use Midtrans\Config;

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
        // Simpan transaksi ke database menggunakan repository
        $transaction = $this->transactionRepository->createTransaction([
            'asesi_id' => $data['asesi_id'],
            'biaya' => $data['biaya'],
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->id,
                'gross_amount' => $transaction->biaya
            ],
            'customer_details'=> [
                'first_name' => $data['name'],
                'email'=> $data['email'],
                'phoen' => $data['no_tlp_hp']
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return [
            'transaction' => $transaction,
            'snap_token' => $snapToken
        ];
    }
}
