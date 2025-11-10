<?php

namespace App\Enums;

enum AsesiStatus: string
{
    case MENUNGGU_VERIFIKASI_BERKAS = 'menunggu_verifikasi_berkas';
    case PERLU_PERBAIKAN_BERKAS = 'perlu_perbaikan_berkas';
    case DITOLAK = 'ditolak';
    case DILANJUTKAN_ASESMEN = 'dilanjutkan_asesmen';
    case LULUS_SERTIFIKASI = 'lulus_sertifikasi';
}
