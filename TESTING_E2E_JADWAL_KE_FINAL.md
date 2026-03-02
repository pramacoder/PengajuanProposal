# Panduan Testing E2E: Setting Jadwal sampai Hasil Final

Dokumen ini dipakai untuk uji **end-to-end** alur PKM dari pengaturan jadwal oleh operator sampai penetapan hasil final oleh pimpinan PT.

## 1) Tujuan

- Memastikan kontrol fase (`Ruang Kontrol`) berjalan sesuai tanggal/status.
- Memastikan transisi status proposal berjalan benar dari awal hingga akhir.
- Memastikan hak akses tiap role sesuai (menu, aksi, dan data yang tampil).
- Memastikan output akhir (hasil semi final, validasi akhir, hasil final) konsisten.

## 2) Prasyarat

- Aplikasi sudah bisa login untuk role:
  - Operator
  - Mahasiswa
  - Dosen Pendamping
  - Dosen Pendamping Universitas
  - Reviewer
  - Pimpinan PT
- Data master tersedia:
  - Fakultas/Prodi
  - Form Penilaian (minimal untuk `substantif_seleksi`, `semi_final`, `final`)
- Tahun ajaran aktif sudah ditentukan (contoh: `2025/2026`).
- Tersedia minimal 1 proposal dummy dengan file PDF valid.

## 3) Data Uji yang Direkomendasikan

- **Skim proposal:** pilih salah satu yang paling sering dipakai (contoh `RE`).
- **Nilai dana diajukan:** gunakan format Indonesia, contoh `12.500.000`.
- **Reviewer:** siapkan minimal 3 reviewer untuk fase review awal + 2 reviewer seleksi.
- **Nama skenario:** gunakan kode sederhana, misalnya:
  - `E2E-HAPPY-01` (alur lolos sampai final)
  - `E2E-REJECT-01` (gagal di semi/final)

## 4) Alur Testing Utama (Happy Path)

## A. Operator set jadwal di Ruang Kontrol

1. Login sebagai **Operator**.
2. Buka menu `Ruang Kontrol` (`/operator/ruang-kontrol`).
3. Buat/aktifkan jadwal tahun ajaran target.
4. Isi rentang tanggal untuk semua fase:
   - Pengajuan
   - Review
   - Perbaikan/Seleksi
   - Penilaian Akhir
5. Pastikan urutan tanggal valid (mulai <= selesai) dan kronologis antar fase benar.
6. Aktifkan fase **Pengajuan**.

**Expected Result**
- Jadwal tersimpan.
- Hanya fase yang diaktifkan yang terbuka.
- Tidak muncul error validasi tanggal.

## B. Mahasiswa ajukan proposal

1. Login sebagai **Mahasiswa**.
2. Buka menu ajukan proposal.
3. Isi form proposal lengkap (judul, skim, dana, tim).
4. Upload PDF proposal.
5. Submit.

**Expected Result**
- Proposal berhasil tersimpan.
- Status awal proposal sesuai alur (submitted/pending validasi).
- Nominal tampil dengan format Indonesia (`.` sebagai pemisah ribuan).

## C. Dosen pendamping validasi awal

1. Login sebagai **Dosen Pendamping**.
2. Buka menu validasi proposal.
3. Buka detail proposal mahasiswa.
4. Lakukan validasi **valid** (dengan/ tanpa catatan).

**Expected Result**
- Status proposal berubah ke valid (siap masuk fase review).
- Mahasiswa menerima notifikasi/status update.

## D. Operator assign reviewer fase review

1. Login sebagai **Operator**.
2. Nonaktifkan fase pengajuan, aktifkan fase **Review**.
3. Buka menu `Pilih Reviewer`.
4. Assign:
   - 1 reviewer administratif
   - 2 reviewer substantif
5. Submit assignment.

**Expected Result**
- Assignment tersimpan sukses.
- Proposal masuk antrian reviewer.
- Reviewer terkait dapat melihat proposal yang ditugaskan.

## E. Reviewer administratif + substantif pertama

1. Login sebagai **Reviewer** (akun yang ditugaskan administratif).
2. Buka `Review Administratif`, isi checklist + catatan, submit.
3. Login sebagai **Reviewer Substantif** (akun reviewer 1/2).
4. Buka `Review Substantif`, isi catatan substantif, submit.
5. Ulangi untuk reviewer substantif kedua jika flow mensyaratkan keduanya.

**Expected Result**
- Nilai/catatan tersimpan.
- Status proposal berpindah ke tahap revisi setelah syarat review terpenuhi.

## F. Mahasiswa revisi + dosen validasi lanjutan

1. Login sebagai **Operator**, aktifkan fase **Perbaikan/Seleksi**.
2. Login sebagai **Mahasiswa**.
3. Buka menu revisi proposal, upload file revisi.
4. Login sebagai **Dosen Pendamping**.
5. Lakukan validasi lanjutan pada revisi (valid).

**Expected Result**
- File revisi tersimpan.
- Proposal lolos ke tahap seleksi reviewer.

## G. Operator assign reviewer seleksi + reviewer seleksi menilai

1. Login sebagai **Operator**.
2. Buka menu `Pilih Reviewer Seleksi`.
3. Assign 2 reviewer seleksi, submit.
4. Login sebagai **Reviewer Seleksi**.
5. Buka `Review Substantif Seleksi`.
6. Isi penilaian sesuai form penilaian aktif, submit.

**Expected Result**
- Penilaian seleksi tersimpan.
- Data siap dihitung/ditampilkan di hasil semi final.

## H. Operator tetapkan hasil semi final

1. Login sebagai **Operator**.
2. Buka menu `Hasil Semi Final`.
3. Buka detail proposal, isi keputusan + catatan + nilai (jika dibutuhkan).
4. Simpan.

**Expected Result**
- Hasil semi final tersimpan.
- Proposal yang lolos masuk alur revisi akhir.

## I. Mahasiswa revisi akhir + dosen universitas validasi akhir

1. Login sebagai **Mahasiswa**.
2. Buka menu revisi akhir, upload file revisi akhir.
3. Login sebagai **Dosen Pendamping Universitas**.
4. Buka `Validasi Akhir`, lakukan validasi final (valid).

**Expected Result**
- File revisi akhir tersimpan.
- Proposal siap ditetapkan hasil final oleh pimpinan PT.

## J. Pimpinan PT tetapkan hasil final

1. Login sebagai **Operator** dengan role **Pimpinan PT**.
2. Buka dashboard/halaman hasil final pimpinan PT.
3. Buka detail proposal.
4. Tetapkan keputusan final (lolos/tidak lolos), isi catatan bila perlu.
5. Simpan.

**Expected Result**
- Hasil final tersimpan permanen.
- Mahasiswa dapat melihat status hasil final di sisi mahasiswa.
- Rekap/operator dan pimpinan PT konsisten.

## 5) Skenario Negatif Wajib

## A. Validasi jadwal

- Isi tanggal mulai > tanggal selesai pada fase yang sama.
- Set dua fase aktif bersamaan (jika aturan tidak mengizinkan).
- Coba akses halaman aksi fase saat fase tertutup.

**Expected Result**
- Ditolak dengan pesan validasi yang jelas.
- Akses aksi fase tertutup diblokir middleware (`check.phase`).

## B. Validasi assignment reviewer

- Submit tanpa reviewer lengkap.
- Pilih reviewer yang sama untuk slot yang harus berbeda.

**Expected Result**
- Submit gagal, muncul error validasi.

## C. Validasi dokumen

- Upload file non-PDF.
- Upload ukuran file melebihi batas.

**Expected Result**
- Upload ditolak dengan pesan error per field.

## D. Validasi penilaian

- Submit form penilaian dengan nilai kosong/tidak valid.
- Submit JSON config form penilaian tidak valid (operator CRUD form).

**Expected Result**
- Submit ditolak, error tampil jelas, data lama tidak hilang.

## 6) Checklist Sign-off UAT

Centang seluruh item sebelum menyatakan lulus:

- [ ] Operator dapat membuat dan mengaktifkan jadwal tanpa error.
- [ ] Akses menu/aksi mengikuti fase aktif.
- [ ] Mahasiswa dapat submit proposal dan revisi sesuai fase.
- [ ] Dosen pendamping dapat validasi awal dan lanjutan.
- [ ] Reviewer administratif/substantif/seleksi dapat submit review.
- [ ] Operator dapat menetapkan hasil semi final.
- [ ] Dosen universitas dapat validasi akhir.
- [ ] Pimpinan PT dapat menetapkan hasil final.
- [ ] Status proposal konsisten di semua role.
- [ ] Notifikasi/feedback tampil sesuai aksi penting.
- [ ] Format angka Indonesia konsisten di input/output.

## 7) Catatan Eksekusi Testing

- Jalankan minimal 2 siklus:
  - 1 siklus **lolos** sampai hasil final.
  - 1 siklus **tidak lolos** di semi/final.
- Simpan bukti uji:
  - Screenshot per fase
  - Nama akun penguji
  - Timestamp pengujian
  - Status pass/fail + catatan bug

