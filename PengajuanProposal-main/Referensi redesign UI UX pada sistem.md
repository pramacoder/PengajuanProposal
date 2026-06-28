1) Target gaya yang ingin dicapai

Kesan yang dicari: modern, bersih, rapi, mudah dipindai (scan), berbasis card dan whitespace, dengan sidebar permanen.
Aksen warna: ganti ke maroon sebagai identitas sistem PengajuanProposal.

2) Struktur UI yang disarankan (IA & Layout)
A. Sidebar (kiri) — navigasi utama

Menu yang umum untuk sistem pengajuan:

Dashboard

Ajukan Proposal (CTA / menu utama)

Draft Saya

Pengajuan Saya

Review/Verifikasi (jika ada role admin/dosen)

Riwayat

Pengumuman

Profil

Pengaturan

Logout

UX penting: menu aktif pakai background maroon gelap, item lain netral.

B. Top bar (atas konten)

Judul halaman + subjudul singkat (seperti referensi)

Tombol aksi utama di kanan atas (mis. Ajukan Proposal / Submit)

C. Konten utama berbasis card

Gunakan pola 3 card utama seperti referensi:

Card Ringkasan Proposal
Menampilkan “Proposal X” + status + info cepat (kategori, tanggal, pembimbing, tahap).

Card Detail Pengajuan
Daftar field penting dalam format dua kolom (label kiri, value kanan).

Card Dokumen & Lampiran
File yang diunggah + status validasi + tombol upload/replace.

3) UX Flow yang lebih baik (alur pengguna)
Flow yang paling enak untuk sistem pengajuan proposal:

Buat Draft (autosave)

Isi data proposal (step-by-step)

Upload berkas

Preview ringkasan

Submit

Tracking status + notifikasi revisi

Agar terasa seperti referensi, kamu bisa bikin halaman “My Proposal” mirip “My Unit”:

Header: nama proposal + badge status

Mini-cards: kategori, pembimbing, tanggal submit, nomor pengajuan

Section: Detail, Lampiran, Riwayat status

4) Komponen UI yang wajib kamu standarkan (biar “satu gaya”)
Card

Rounded 12–16px

Shadow tipis

Header card ada ikon + judul section

Divider halus antar row

Mini-stat card (seperti Size/Bedrooms)

Untuk info cepat:

“Kategori”, “Pembimbing”, “Tahap”, “Deadline”, “Skema”, dsb.

Badge Status (ini penting untuk UX pengajuan)

Contoh status:

Draft (abu)

Submitted (biru)

In Review (ungu)

Revision Required (oranye)

Approved (hijau)

Rejected (merah)

Walau brand kamu maroon, warna status sebaiknya tetap “standar” agar cepat dipahami.

Primary Button (maroon)

Dipakai hanya untuk aksi utama: Submit / Ajukan / Simpan perubahan.

5) Mapping warna: gaya sama, identitas beda (Maroon)

Gunakan maroon untuk:

Sidebar item aktif

CTA button

Link/ikon highlight

Badge “brand” (mis. “PengajuanProposal” tag)

Dan tetap gunakan netral untuk:

background halaman

body card

garis pemisah

teks sekunder

6) Rekomendasi halaman inti yang sebaiknya kamu redesign dulu

Kalau UI sekarang “kurang baik”, biasanya titik paling terasa ada di:

Dashboard pengguna (ringkasan proposal & status)

Form pengajuan (dibuat wizard/stepper + autosave)

Halaman detail pengajuan (tracking status & dokumen)

Halaman review admin/dosen (ceklist validasi + komentar revisi)

7) Contoh struktur halaman “Detail Pengajuan” (mirip referensi)

Header card:

Judul: “Proposal: Sistem X”

Badge status (Revision Required)

Tombol kanan: “Review / Submit / Edit”

Mini-cards:

Kategori | Pembimbing | Tanggal submit | Tahap

Section cards:

Proposal Details (judul, latar belakang, tujuan, lokasi, anggaran)

Documents (file + status + tombol upload)

Review Notes (komentar reviewer + timeline)