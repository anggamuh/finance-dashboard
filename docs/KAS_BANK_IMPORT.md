# Modul Kas & Bank

Modul ini berdiri sendiri dari transaksi Accurate, Buku Kas, omzet dan biaya operasional lama. Import **tidak** menambah baris ke tabel `transactions`.

## Cara pakai
1. Deploy branch melalui PR dan jalankan `php artisan migrate --force`.
2. Buka menu **Kas & Bank**, pilih rekening dan periode.
3. Isi saldo awal sesuai pembukuan periode yang dipilih, lalu simpan.
4. Upload `KAS-BANK-GABUNGAN 2026.xlsx` (atau file serupa) dan klik **Preview**.
5. Periksa jumlah baris per sheet lalu klik **Konfirmasi import**.
6. Gunakan formulir transaksi untuk menambah/edit transaksi harian; Export CSV untuk laporan.

## Aturan data
- Sheet sumber: BCA, MANDIRI, KAS. Sheet GABUNGAN tidak diimport agar rekap tidak menggandakan transaksi.
- Header BCA dan KAS pada baris 2; MANDIRI baris 1; transaksi mulai baris 3/2.
- Serial tanggal Excel dikonversi menjadi tanggal kalender.
- Debit adalah pemasukan dan kredit adalah pengeluaran (mengikuti buku Kas-Bank).
- Fingerprint berisi rekening, tanggal, no. transaksi, akun, invoice, uraian dan nilai debit/kredit. Nomor yang sama pada bulan/tanggal lain tetap boleh. Baris identik pada tanggal sama dilewati.
- Nilai SALDO pada workbook disimpan sebagai referensi `source_balance`, sedangkan saldo yang ditampilkan dihitung ulang dari saldo awal + debit - kredit.
- Saldo awal diset untuk setiap rekening + bulan + tahun. Antarbulan tidak otomatis dibawa agar tidak salah hitung jika workbook mengandung saldo pembuka sebagai transaksi.
- Transfer antar rekening saat ini dicatat sebagai dua baris (kredit rekening sumber dan debit rekening tujuan), tidak menjadi otomatis pada fitur input ini.
- Ekspor yang tersedia adalah CSV, bisa dibuka di Excel; belum ada cetak sesuai format bon/lembar kerja asli.
- DBACC dan nama akun disimpan sesuai file sumber, belum otomatis dicocokkan dengan Master Akun Accurate karena daftar kode akunnya berbeda.

## Checklist setelah deploy
- `php artisan test --filter=BankLedgerTest`
- Uji import di staging lebih dulu, cek jumlah baris dan saldo terhadap workbook asli.
- Cek saldo periode dan setiap rekening, khususnya baris saldo awal dan mutasi pindah dana.
- Verifikasi tidak ada baris ganda setelah import ulang file yang sama.
