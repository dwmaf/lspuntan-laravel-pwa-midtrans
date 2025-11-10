<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case BUKTI_PEMBAYARAN_DITOLAK = 'bukti_pembayaran_ditolak';
    case BUKTI_PEMBAYARAN_TERVERIFIKASI = 'bukti_pembayaran_terverifikasi';
}
