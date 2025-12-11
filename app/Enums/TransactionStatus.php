<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case BUKTI_PEMBAYARAN_DITOLAK = 'bukti_pembayaran_ditolak';
    case BUKTI_PEMBAYARAN_TERVERIFIKASI = 'bukti_pembayaran_terverifikasi';
    case PERLU_PERBAIKAN_BUKTI_BAYAR = 'perlu_perbaikan_bukti_bayar';
}
