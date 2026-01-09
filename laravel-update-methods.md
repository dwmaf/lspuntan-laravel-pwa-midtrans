# Panduan Cara Update Data di Laravel (Eloquent)

Ada tiga cara utama untuk memperbarui data di Laravel. Masing-masing memiliki kegunaan dan karakteristik yang berbeda.

---

## 1. Properti Assignment + `save()`
Cara ini dilakukan dengan mengubah nilai properti model satu per satu secara manual.

### Contoh:
```php
$asesi = Asesi::find(1);
$asesi->status_berkas = 'sudah_lengkap';
$asesi->catatan_perbaikan = null;
$asesi->save();
```

### Karakteristik:
- **Eksplisit**: Sangat jelas kolom apa saja yang diubah.
- **Aman**: Tidak bergantung pada `$fillable` atau `$guarded`.
- **Fleksibel**: Cocok jika data butuh diolah dulu (misal: enkripsi password atau manipulasi string) sebelum disimpan.
- **Boros Baris**: Jika kolom yang diupdate banyak, kode jadi sangat panjang.

---

## 2. Mass Assignment + `update()`
Cara ini dilakukan dengan mengirimkan array berisi pasangan *key-value* data yang ingin diubah.

### Contoh:
```php
$asesi = Asesi::find(1);
$asesi->update([
    'status_berkas' => 'sudah_lengkap',
    'catatan_perbaikan' => null,
]);
```

### Karakteristik:
- **Ringkas**: Sangat bersih dan mudah dibaca, terutama jika mengupdate banyak kolom.
- **Otomatis**: Langsung melakukan perubahan di memori dan ke database sekaligus.
- **Dependency**: Membutuhkan pengaturan `$fillable` atau `$guarded` di Model untuk mencegah *Mass Assignment Vulnerability*.
- **Paling Populer**: Cara yang paling sering digunakan dalam pengembangan aplikasi Laravel modern.

---

## 3. Query Builder (Direct Update)
Cara ini dilakukan langsung melalui query SQL tanpa harus mengambil data (*fetch*) ke memori PHP terlebih dahulu.

### Contoh:
```php
Asesi::where('id', 1)->update(['status_berkas' => 'sudah_lengkap']);
```

### Karakteristik:
- **Performa Tinggi**: Sangat cepat untuk update data dalam jumlah besar (ribuan baris sekaligus).
- **Tanpa Eager Load**: Tidak perlu memuat object Model ke memori.
- **Kekurangan Vital**: **TIDAK** menjalankan *Model Events* (seperti `updating`, `saved`) atau *Observers*. Log aktivitas otomatis biasanya tidak akan tercatat jika pakai cara ini.

---

## Tabel Perbandingan

| Fitur | `save()` | `update()` | Query Builder |
| :--- | :---: | :---: | :---: |
| **Ringkas** | ❌ Tidak | ✅ Ya | ✅ Ya |
| **Butuh Fillable** | ❌ Tidak | ✅ Ya | ❌ Tidak |
| **Trigger Events** | ✅ Ya | ✅ Ya | ❌ Tidak |
| **Update Bulk** | ❌ Satu-satu | ❌ Satu-satu | ✅ Ya (Cepat) |

---

## Mana yang Harus Digunakan?

1. **Gunakan `save()`** jika Anda hanya mengubah satu kolom atau ingin melakukan logika PHP tambahan di tengah-tengah proses assignment.
2. **Gunakan `update()`** sebagai standar utama dalam Controller Anda karena kodenya paling bersih dan aman (didukung Model Events).
3. **Gunakan Query Builder** hanya jika Anda perlu melakukan perubahan "sapu jagat" pada banyak baris data sekaligus demi efisiensi database.
