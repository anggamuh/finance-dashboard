# ReconcileAI PDF pada shared hosting

Perbaikan ini mengganti pemanggilan `pdftotext` dan `proc_open` dengan `smalot/pdfparser` (pure PHP).

## Instalasi dependency

**Jalankan di mesin pengembangan lokal**, karena shared hosting dapat memblokir `proc_open` yang juga digunakan Composer.

```bash
composer require "smalot/pdfparser:^2.13"
php artisan test --filter=BankStatementPdfParserTest
```

Commit hasil perubahan **composer.lock** dari komputer lokal sebelum deploy:

```bash
git add composer.json composer.lock
git commit -m "build: lock pure PHP pdf parser dependency"
```

Di hosting, jalankan `composer install --no-dev --optimize-autoloader` **hanya kalau Composer berfungsi**. Jika tidak, deploy folder `vendor` hasil instalasi di lingkungan PHP yang kompatibel dan pastikan file autoloader tersinkronisasi.

## Perilaku

- PDF tekstual tanpa password: teks diekstrak langsung lewat PHP.
- PDF hasil scan: tidak didukung sampai ada OCR.
- Format transaksi PDF selain `tanggal keterangan nominal DB/CR`: **tidak dipaksakan terbaca**. Untuk rekening koran BCA/Mandiri aktual, parser baris perlu disesuaikan berdasarkan PDF sampel yang sudah disensor.
- Pengguna tetap perlu mengonfirmasi preview. Jangan menyetujui rekonsiliasi sebelum mengecek total dan arah dana.
- Bila muncul `Library PDF belum terpasang`, periksa apakah package terinstal di vendor hosting.

### Keterbatasan PR ini

`composer.json` telah diubah tetapi `composer.lock` **belum** diperbarui, karena eksekusi Composer dengan akses Packagist tidak tersedia di lingkungan pembuat PR. Merge PR dan deploy tanpa memperbarui lock/vendor belum cukup untuk mengaktifkan parser.
