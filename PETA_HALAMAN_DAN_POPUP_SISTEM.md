# Peta Komprehensif Halaman, Alur Fungsi, dan Pop-Up / Modal Sistem Pengajuan Proposal PKM

> **Dokumen Panduan Arsitektur UI/UX Sistem**  
> Target: Standarisasi Pengalaman Pengguna (Design System & UX Consistency) dari Mahasiswa hingga Pimpinan Perguruan Tinggi.

---

## 1. Matriks Pengguna & Hak Akses (Role Matrix)

Sistem Pengajuan Proposal PKM memiliki 5 tingkat otorisasi peran utama:

| Peran (Role) | Guard / Middleware | Lingkup Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **Guest / Publik** | Tidak Terautentikasi | Login sistem, permohonan akun mahasiswa, reset kata sandi |
| **Mahasiswa** | `auth`, `role:mahasiswa` | Pembuatan tim, pengajuan draft proposal, upload dokumen, revisi berkas, pantau kelulusan |
| **Dosen** | `auth`, `role:dosen` | Terbagi 3 portal peran: Dosen Pembimbing, Dosen Pendamping, dan Dosen Universitas |
| **Reviewer** | `auth`, `role:reviewer` | Penilaian seleksi administratif, penilaian rubrik substantif, dan substantif seleksi |
| **Operator** | `auth`, `role:operator` | Manajemen ruang kontrol jadwal/fase, alokasi reviewer, olah nilai semi final/final, manajemen form & akun |
| **Pimpinan PT** | `auth`, `role:pimpinan_pt` | Tinjauan eksekutif, monitoring jadwal/reviewer, penetapan putusan akhir kelulusan & pelaporan SIMBELMAWA |

---

## 2. Komponen Global & Sistem Dialog Sistem

Sistem memiliki komponen UI global yang dimuat pada layout utama (`resources/views/mainlayout/app.blade.php`):

### 2.1. Global Modal Review (`#reviewModal`)
* **ID Element**: `div#reviewModal` (Bootstrap Modal `modal-lg`)
* **Pengguna**: Reviewer, Operator, Pimpinan PT, Dosen
* **Pemicu (Trigger)**: Klik tombol *Lihat Catatan*, *Detail Review*, atau badge hasil review.
* **Konten**: Menampilkan rubrik skor reviewer dan catatan evaluasi secara AJAX tanpa reload halaman.
* **Tombol Aksi**: Tombol *Tutup* (`btn-close` & `btn-secondary`).

### 2.2. Global Toast Notification Container (`#toastContainer`)
* **ID Element**: `div#toastContainer` (Fixed position kanan-atas `z-index: 9999`)
* **Tipe Notifikasi**:
  * `success` (Hijau): Berhasil menyimpan, submit berkas, penugasan berhasil.
  * `error` (Merah): Validasi gagal, file melebihi kapasitas, kesalahan server.
  * `warning` (Kuning): Peringatan catatan minimal 50 karakter, batas waktu mendekati akhir.
  * `info` (Biru): Notifikasi informasi pembaruan data atau loading.

### 2.3. Navbar Dropdown Notifikasi & Profil
* **Notification Dropdown (`#notificationDropdown`)**:
  * Menampilkan badge lonceng merah jika ada proposal baru, perubahan jadwal fase ruang kontrol, atau feedback review.
  * Tombol aksi: *Tandai Semua Dibaca* (`#markAllRead`) & *Lihat Semua Notifikasi*.
* **User Profile Dropdown (`x-user-profile-dropdown`)**:
  * Menampilkan nama pengguna, role, email, avatar, tautan ke *Profil Saya*, dan tombol *Logout* dengan konfirmasi.

---

## 3. Peta Halaman: Publik & Autentikasi

### 3.1. Halaman Login
* **URL**: `/login` (GET, POST)
* **Route Name**: `login`
* **File View**: [auth/login.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/auth/login.blade.php)
* **Fungsi Halaman**:
  Pintu masuk utama seluruh pengguna (Mahasiswa, Dosen, Reviewer, Operator, Pimpinan PT) menggunakan NIM/NIP/Username dan Password.
* **Pop-Up & Notifikasi**:
  1. **Alert Flash Message**: Pesan error kredensial salah, akun dinonaktifkan, atau sesi habis.
  2. **Validasi Form Inline**: Peringatan field kosong jika submit form belum terisi lengkap.

### 3.2. Halaman Permohonan Akun Mahasiswa (Request Credentials)
* **URL**: `/request-credentials` atau `/register` (GET, POST)
* **Route Name**: `register`
* **File View**: [auth/register_mahasiswa.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/auth/register_mahasiswa.blade.php)
* **Fungsi Halaman**:
  Mahasiswa mengajukan permohonan akun baru dengan memasukkan identitas diri (NIM, Nama, Email Kampus, Fakultas, Program Studi, No HP) dan unggahan bukti KTM.
* **Pop-Up & Notifikasi**:
  1. **Toast/Alert Upload File**: Validasi format file KTM (format PDF/JPG/PNG max 2MB).
  2. **Flash Alert Sukses**: Memberitahukan bahwa permohonan berhasil terkirim dan menunggu verifikasi operator.

### 3.3. Halaman Lupa & Reset Password
* **URL**: `/forgot-password` dan `/reset-password` (GET, POST)
* **Route Name**: `password.forgot`, `password.reset.form`, `password.reset`
* **File View**: [auth/forgot-password.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/auth/forgot-password.blade.php) & [auth/reset_password.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/auth/reset_password.blade.php)
* **Fungsi Halaman**:
  Mengirimkan link/token pemulihan kata sandi ke email pengguna terdaftar dan form pembuatan kata sandi baru.
* **Pop-Up & Notifikasi**:
  1. **Alert Status Email**: Memberitahukan link reset telah dikirimkan ke email.
  2. **Alert Token Expired**: Notifikasi jika token reset sudah kadaluarsa/tidak valid.

---

## 4. Peta Halaman: Portal Mahasiswa

### 4.1. Dashboard Mahasiswa
* **URL**: `/mahasiswa/dashboard`
* **Route Name**: `mahasiswa.dashboard`
* **File View**: [mahasiswa/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/dashboard.blade.php)
* **Fungsi Halaman**:
  Pusat informasi mahasiswa: menampilkan status proposal aktif, ringkasan timeline fase PKM yang sedang berjalan di Ruang Kontrol (Pendaftaran, Review, Revisi, Penilaian Akhir), pengumuman panduan, serta tombol aksi cepat.
* **Pop-Up & Notifikasi**:
  1. **Banner Peringatan Fase**: Muncul jika fase pendaftaran proposal belum dibuka atau sudah ditutup oleh operator.
  2. **Toast Sambutan / Sesi**: Notifikasi status proposal terupdate.

### 4.2. Profil Mahasiswa
* **URL**: `/mahasiswa/profile`
* **Route Name**: `mahasiswa.profile` (GET, PUT)
* **File View**: [mahasiswa/profile.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/profile.blade.php)
* **Fungsi Halaman**:
  Melihat identitas mahasiswa (NIM, Prodi, Fakultas), mengubah nomor HP/kontak, serta mengganti kata sandi akun.
* **Pop-Up & Notifikasi**:
  1. **Flash Alert Sukses**: Notifikasi berhasil memperbarui profil / password.
  2. **Form Error Alert**: Peringatan jika konfirmasi password baru tidak cocok.

### 4.3. Form Pengajuan Proposal PKM (Fase 1)
* **URL**: `/mahasiswa/proposal/create` dan `/mahasiswa/proposal/{id}/edit`
* **Route Name**: `mahasiswa.proposal.create`, `mahasiswa.proposal.store`, `mahasiswa.proposal.edit`, `mahasiswa.proposal.update`
* **File View**: [mahasiswa/ajukanproposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/ajukanproposal.blade.php)
* **Fungsi Halaman**:
  Formulir lengkap pendaftaran proposal: pemilihan skim PKM (RE, RSH, KC, PM, PI, K, KI, VGK, AI, GFT), judul, bidang ilmu, penambahan anggota tim berdasarkan NIM, pemilihan dosen pendamping & pembimbing, anggaran biaya, serta upload dokumen PDF.
* **Pop-Up & Notifikasi**:
  1. **Dialog AJAX Check Mahasiswa**: Memunculkan peringatan pop-up jika NIM anggota yang dimasukkan sudah terdaftar pada proposal lain di tahun berjalan.
  2. **Toast Validasi File**: Muncul jika file proposal PDF yang diunggah tidak valid atau melebihi kuota 5MB.
  3. **Modal/Dialog Konfirmasi Submit**: Konfirmasi kepastian data sebelum finalisasi draft proposal.

### 4.4. Daftar Proposal Mahasiswa
* **URL**: `/mahasiswa/proposal`
* **Route Name**: `mahasiswa.proposal.index`
* **File View**: [mahasiswa/lihat_proposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/lihat_proposal.blade.php)
* **Fungsi Halaman**:
  Tabel riwayat pengajuan proposal mahasiswa, lengkap dengan badge status alur pengajuan (*Draft, Menunggu Validasi Dosen, Valid, Review Administratif, Review Substantif, Revisi, Semi Final, Final*).
* **Pop-Up & Notifikasi**:
  1. **Modal Overlay Catatan Review (`#modalOverlay` / `showReviewModal`)**:
     * **Judul**: "Hasil Review Administratif / Substantif / Final"
     * **Pemicu**: Tombol klik *Lihat Catatan Reviewer*.
     * **Isi**: Catatan perbaikan dari reviewer dan dosen tanpa meninggalkan halaman.
     * **Tombol Aksi**: Tombol *Tutup Modal*.

### 4.5. Detail Proposal Mahasiswa
* **URL**: `/mahasiswa/proposal/{id}`
* **Route Name**: `mahasiswa.proposal.show`
* **File View**: [mahasiswa/detail_proposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/detail_proposal.blade.php)
* **Fungsi Halaman**:
  Tinjauan menyeluruh proposal: struktur tim, identitas dosen, timeline status proses, preview dokumen proposal terintegrasi PDF.js, serta riwayat revisi.
* **Pop-Up & Notifikasi**:
  1. **Dialog Konfirmasi Hapus**: `confirm('Apakah Anda yakin ingin menghapus proposal ini?')` jika proposal masih berstatus draft/belum divalidasi.
  2. **Toast Feedback Download**: Peringatan saat mengunduh lembar pengesahan atau lampiran proposal.

### 4.6. Form Revisi Proposal (Fase 3: Revisi & Seleksi)
* **URL**: `/mahasiswa/proposal/{id}/revisi` dan `/mahasiswa/revisi`
* **Route Name**: `mahasiswa.proposal.revisi`, `mahasiswa.proposal.revisi.submit`
* **File View**: [mahasiswa/revisi_proposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/revisi_proposal.blade.php)
* **Fungsi Halaman**:
  Pengunggahan dokumen proposal yang telah diperbaiki sesuai catatan reviewer substantif pada Fase 2, pengisian formulir poin perbaikan (matriks respon revisi), dan bukti perubahan.
* **Pop-Up & Notifikasi**:
  1. **Modal Konfirmasi Hapus Berkas Revisi (`#deleteModal`)**:
     * **Pemicu**: Klik tombol *Hapus* pada file revisi yang sudah diupload.
     * **Tombol**: Batal (`btn-secondary`) dan Hapus Permanen (`btn-danger`).
  2. **Toast Peringatan**: Validasi wajib melampirkan lembar pengesahan revisi dan deskripsi respon revisi.

### 4.7. Form Revisi Akhir Proposal (Fase 4: Penilaian Akhir)
* **URL**: `/mahasiswa/proposal/{id}/revisi-akhir`
* **Route Name**: `mahasiswa.proposal.revisi.akhir`, `mahasiswa.proposal.revisi.akhir.submit`
* **File View**: [mahasiswa/revisi_akhir_proposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/mahasiswa/revisi_akhir_proposal.blade.php)
* **Fungsi Halaman**:
  Finalisasi dokumen proposal bagi tim yang dinyatakan lolos universitas/semi-final sebelum diajukan ke portal nasional SIMBELMAWA.
* **Pop-Up & Notifikasi**:
  1. **Alert Konfirmasi Finalisasi**: Peringatan bahwa dokumen revisi akhir ini akan dijadikan berkas resmi universitas dan tidak dapat diubah kembali.

---

## 5. Peta Halaman: Portal Dosen

Akun Dosen memiliki 3 sub-portal fungsional: **Dosen Pembimbing**, **Dosen Pendamping**, dan **Dosen Universitas**.

### 5.1. Profil Dosen
* **URL**: `/dosen/profile`
* **Route Name**: `dosen.profile`
* **File View**: [dosen/profile.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/profile.blade.php)
* **Fungsi Halaman**: Pengelolaan data NIDN/NIP, bidang keahlian, nomor telepon, dan ubah kata sandi.

### 5.2. Sub-Portal Dosen Pembimbing
1. **Dashboard Pembimbing**:
   * **URL**: `/dosen/pembimbing/dashboard` (`dosen.pembimbing.dashboard`)
   * **File View**: [dosen/pembimbing/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pembimbing/dashboard.blade.php)
   * **Fungsi**: Memantau seluruh proposal tim mahasiswa yang berada di bawah bimbingannya.
2. **Mahasiswa Bimbingan**:
   * **URL**: `/dosen/pembimbing/mahasiswa-bimbingan` (`dosen.pembimbing.mahasiswa.bimbingan`)
   * **File View**: [dosen/pembimbing/mahasiswa_bimbingan.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pembimbing/mahasiswa_bimbingan.blade.php)
   * **Fungsi**: Daftar ketua dan anggota tim mahasiswa yang dibimbing.
3. **Validasi Tahap 2 & Detail Validasi**:
   * **URL**: `/dosen/pembimbing/validasi-2` & `/dosen/pembimbing/proposal/{id}/validasi-2-detail`
   * **File View**: [dosen/pembimbing/validasi_2.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pembimbing/validasi_2.blade.php) & [dosen/pembimbing/validasi_2_detail.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pembimbing/validasi_2_detail.blade.php)
   * **Fungsi**: Memeriksa kembali berkas proposal yang telah direvisi mahasiswa pasca-review untuk disetujui melaju ke seleksi semi-final.
   * **Pop-Up / Dialog**: Dialog konfirmasi persetujuan revisi valid/perlu perbaikan lagi + catatan dosen.

### 5.3. Sub-Portal Dosen Pendamping
1. **Dashboard Pendamping**:
   * **URL**: `/dosen/pendamping/dashboard` (`dosen.pendamping.dashboard`)
   * **File View**: [dosen/pendamping/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/dashboard.blade.php)
   * **Fungsi**: Tinjauan proposal mahasiswa yang didampingi di tingkat jurusan/program studi.
2. **Validasi Proposal (Fase 1)**:
   * **URL**: `/dosen/pendamping/proposal-validasi` & `/dosen/pendamping/proposal/{id}/detail`
   * **Route Name**: `dosen.pendamping.proposal.validasi`, `dosen.pendamping.proposal.detail`
   * **File View**: [dosen/pendamping/proposal_validasi.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/proposal_validasi.blade.php) & [dosen/pendamping/detail_proposal.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/detail_proposal.blade.php)
   * **Fungsi**: Menilai kelayakan awal proposal baru mahasiswa (Setujui / Tolak / Perbaikan) agar dapat masuk ke antrean pembagian reviewer.
   * **Pop-Up & Modals**:
     * Modal Form Validasi: Pilihan status (Valid / Tidak Valid), isian catatan koreksi.
     * Alert konfirmasi submit validasi proposal.
3. **Hasil Review (Fase 2) & Detail**:
   * **URL**: `/dosen/pendamping/hasil-review` & `/dosen/hasil-review/{id}/detail`
   * **File View**: [dosen/pendamping/hasil_review.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/hasil_review.blade.php) & [dosen/pendamping/detail_hasil_review.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/detail_hasil_review.blade.php)
   * **Fungsi**: Melihat hasil evaluasi reviewer administratif dan substantif tim bimbingan.
   * **Pop-Up**: **Modal Hasil Review (`#reviewModal`)** untuk memuat skor dan catatan review.
4. **Hasil Final (Fase 4) & Detail**:
   * **URL**: `/dosen/pendamping/hasil-final` & `/dosen/hasil-final/{id}/detail`
   * **File View**: [dosen/pendamping/hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/hasil_final.blade.php) & [dosen/pendamping/detail_hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/pendamping/detail_hasil_final.blade.php)
   * **Fungsi**: Melihat hasil akhir kelulusan tingkat universitas.

### 5.4. Sub-Portal Dosen Universitas (Pendamping Universitas)
1. **Dashboard Universitas**:
   * **URL**: `/dosen/universitas/dashboard` (`dosen.universitas.dashboard`)
   * **File View**: [dosen/universitas/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/universitas/dashboard.blade.php)
   * **Fungsi**: Memantau rekapitulasi proposal tingkat universitas yang telah lolos semi final.
2. **Validasi Akhir Proposal & Detail**:
   * **URL**: `/dosen/universitas/validasi-akhir` & `/dosen/universitas/validasi-akhir/{id}/detail`
   * **Route Name**: `dosen.universitas.validasi.akhir`, `dosen.universitas.validasi.akhir.detail`
   * **File View**: [dosen/universitas/validasi_akhir.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/universitas/validasi_akhir.blade.php) & [dosen/universitas/detail_validasi_akhir.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/dosen/universitas/detail_validasi_akhir.blade.php)
   * **Fungsi**: Melakukan verifikasi dan pengesahan akhir tingkat universitas sebelum proposal diajukan ke Pimpinan PT.
   * **Pop-Up & Dialog**: Dialog konfirmasi `confirm('Apakah Anda yakin ingin [validasi/tolak] proposal ini?')` dan Toast feedback status AJAX.

---

## 6. Peta Halaman: Portal Reviewer

### 6.1. Dashboard Reviewer
* **URL**: `/reviewer/dashboard`
* **Route Name**: `reviewer.dashboard`
* **File View**: [reviewer/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/dashboard.blade.php)
* **Fungsi Halaman**:
  Pusat kerja reviewer: Menampilkan rekap antrean tugas review (Administratif yang belum/selesai dinilai, Substantif yang belum/selesai dinilai, dan Substantif Seleksi).
* **Pop-Up & Notifikasi**: Toast informasi beban tugas review aktif.

### 6.2. Profil Reviewer
* **URL**: `/reviewer/profile`
* **Route Name**: `reviewer.profile`
* **File View**: [reviewer/profile.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/profile.blade.php)
* **Fungsi Halaman**: Pengaturan profil reviewer, bidang kepakaran, dan kata sandi.

### 6.3. Daftar Tugas Review Administratif
* **URL**: `/reviewer/review-administratif`
* **Route Name**: `reviewer.review.administratif`
* **File View**: [reviewer/review_administratif.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/review_administratif.blade.php)
* **Fungsi Halaman**:
  Tabel daftar proposal yang dialokasikan operator untuk dievaluasi kelengkapan format, administrasi, dan keabsahan berkasnya.

### 6.4. Form & Workspace Review Administratif
* **URL**: `/reviewer/proposal/{id}/detail`
* **Route Name**: `reviewer.detail.proposal`, POST `reviewer.submit.review.administratif`
* **File View**: [reviewer/detail_proposal_administratif.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/detail_proposal_administratif.blade.php)
* **Fungsi Halaman**:
  Ruang kerja penilaian split-view: Sisi kiri menampilkan dokumen proposal via PDF.js terintegrasi, sisi kanan menampilkan checklist kriteria administrasi (format halaman, daftar pustaka, surat pengesahan), input catatan perbaikan, dan penetapan status kelulusan (Lolos / Tidak Lolos).
* **Pop-Up & Notifikasi**:
  1. **Toast Validasi**: "Catatan review harus diisi." jika reviewer belum menulis catatan evaluasi.
  2. **Toast Feedback Error/Success**: Notifikasi hasil penyimpanan review via AJAX.

### 6.5. Daftar Tugas Review Substantif
* **URL**: `/reviewer/review-substantif`
* **Route Name**: `reviewer.review.substantif`
* **File View**: [reviewer/review_substantif.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/review_substantif.blade.php)
* **Fungsi Halaman**:
  Daftar proposal yang lolos verifikasi administrasi untuk dinilai kualitas gagasan, metodologi, dan nilai kebaruannya oleh Reviewer 1 dan Reviewer 2.

### 6.6. Form & Workspace Review Substantif
* **URL**: `/reviewer/proposal/{id}/detail-substantif`
* **Route Name**: `reviewer.detail.proposal.substantif`, POST `reviewer.submit.review.substantif`
* **File View**: [reviewer/detail_proposal_substantif.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/detail_proposal_substantif.blade.php)
* **Fungsi Halaman**:
  Ruang kerja penilaian substansi: PDF.js preview dokumen, tabel rubrik kriteria dinamis dengan bobot persentase, input skor nilai (skala 1-7 atau 1-100), kalkulasi total nilai otomatis di sisi klien, dan textarea catatan perbaikan komprehensif (minimal 50 karakter).
* **Pop-Up & Notifikasi**:
  1. **Toast Error Validasi**: Peringatan jika ada skor kriteria yang belum terisi atau panjang catatan evaluasi kurang dari 50 karakter.
  2. **Toast Sukses**: `✅ Data penilaian substantif berhasil disimpan!`.

### 6.7. Review Substantif Seleksi (Fase 3)
* **URL**: `/reviewer/review-substantif-seleksi`
* **Route Name**: `reviewer.review.substantif.seleksi`, POST `reviewer.submit.review.substantif.seleksi`
* **File View**: [reviewer/review_substantif_seleksi.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/reviewer/review_substantif_seleksi.blade.php)
* **Fungsi Halaman**:
  Penilaian terhadap proposal tim mahasiswa yang telah mengunggah berkas revisi untuk menentukan ranking kelayakan semi-final universitas.
* **Pop-Up & Notifikasi**: Toast konfirmasi penilaian seleksi berhasil terkirim.

---

## 7. Peta Halaman: Portal Operator PKM

### 7.1. Dashboard Operator
* **URL**: `/operator/dashboard`
* **Route Name**: `operator.dashboard`
* **File View**: [operator/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/dashboard.blade.php)
* **Fungsi Halaman**:
  Analitik utama institusi: statistik proposal masuk, rasio per skim, status validasi dosen, grafik per fakultas, status fase aktif ruang kontrol, dan jalan pintas ke fungsi krusial.

### 7.2. Profil Operator
* **URL**: `/operator/profile`
* **Route Name**: `operator.profile`
* **File View**: [operator/profile.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/profile.blade.php)

### 7.3. Ruang Kontrol (Jadwal & Fase Sistem)
* **URL**: `/operator/ruang-kontrol`
* **Route Name**: `operator.ruang.kontrol`
* **File View**: [operator/ruang_kontrol.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/ruang_kontrol.blade.php)
* **Fungsi Halaman**:
  Pusat kendali tahapan PKM: memilih tahun ajaran aktif, membuka/menutup fase secara manual (Fase 1: Pendaftaran, Fase 2: Review, Fase 3: Perbaikan/Seleksi, Fase 4: Penilaian Akhir), serta membuat dan mengedit jadwal gelombang kegiatan.
* **Pop-Up & Modals**:
  1. **Modal Buat Jadwal Baru (`#modalBuatJadwal`)**:
     * **Pemicu**: Klik tombol *Buat Jadwal Baru*.
     * **Form**: Nama gelombang, Tahun ajaran, Tanggal mulai & selesai untuk Fase 1 s.d. 4.
     * **Aksi**: Batal (`btn-secondary`) dan Simpan Jadwal (`btn-primary`).
  2. **Modal Edit Jadwal (`#modalEditJadwal`)**:
     * **Pemicu**: Klik ikon pensil *Edit* pada baris riwayat jadwal.
     * **Form**: Mengubah tanggal dan status buka/tutup fase jadwal terpilih.
     * **Aksi**: Batal dan Update Jadwal.
  3. **Dialog Konfirmasi Aktifkan**: `confirm('Aktifkan jadwal ini? Jadwal lain untuk tahun ajaran yang sama akan dinonaktifkan.')`.
  4. **Dialog Konfirmasi Hapus**: `confirm('Hapus jadwal ini? Tindakan ini tidak dapat dibatalkan.')`.

### 7.4. Pilih Reviewer (Fase 2: Review)
* **URL**: `/operator/pilih-reviewer`
* **Route Name**: `operator.pilih.reviewer`, POST `operator.assign.reviewer`
* **File View**: [operator/pilih_reviewer.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/pilih_reviewer.blade.php)
* **Fungsi Halaman**:
  Menugaskan 1 Reviewer Administratif, 1 Reviewer Substantif 1, dan 1 Reviewer Substantif 2 untuk setiap proposal yang divalidasi, disertai filter tahun ajaran dan skim.
* **Pop-Up & Toasts**:
  1. **Toast Peringatan**: "Reviewer substantif 1 dan 2 tidak boleh sama".
  2. **Toast Peringatan**: "Mohon pilih semua reviewer terlebih dahulu".
  3. **Toast Sukses**: "Reviewer berhasil ditugaskan!".

### 7.5. Pilih Reviewer Seleksi (Fase 3: Revisi & Seleksi)
* **URL**: `/operator/pilih-reviewer-seleksi`
* **Route Name**: `operator.pilih.reviewer.seleksi`, POST `operator.assign.reviewer.seleksi`
* **File View**: [operator/pilih_reviewer_seleksi.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/pilih_reviewer_seleksi.blade.php)
* **Fungsi Halaman**:
  Menugaskan Reviewer Seleksi 1 & 2 untuk menilai hasil revisi proposal mahasiswa.
* **Pop-Up & Toasts**: Toast validasi reviewer ganda & feedback penugasan sukses.

### 7.6. Hasil Semi Final & Detail
* **URL**: `/operator/hasil-semi-final` dan `/operator/detail-hasil-semi-final/{id}`
* **Route Name**: `operator.hasil.semi.final`, `operator.detail.hasil.semi.final`, POST `operator.update.hasil.semi.final`
* **File View**: [operator/hasil_semi_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/hasil_semi_final.blade.php) & [operator/detail_hasil_semi_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/detail_hasil_semi_final.blade.php)
* **Fungsi Halaman**:
  Rekapitulasi nilai Reviewer 1 dan Reviewer 2, normalisasi skor per skim, serta halaman edit detail nilai semi final dan penetapan status lolos/tidak lolos ke final universitas.
* **Pop-Up & Toasts**:
  1. **Toast Validasi**: Peringatan jika nilai di luar skala atau catatan evaluasi kurang dari 50 karakter.
  2. **Toast Sukses**: "Hasil semi final berhasil diperbarui!".

### 7.7. Hasil Final & Detail
* **URL**: `/operator/hasil-final` dan `/operator/detail-hasil-final/{id}`
* **Route Name**: `operator.hasil.final`, `operator.detail.hasil.final`
* **File View**: [operator/hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/hasil_final.blade.php) & [operator/detail_hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/detail_hasil_final.blade.php)
* **Fungsi Halaman**:
  Menampilkan hasil rekapitulasi penilaian akhir universitas dan status rekomendasi pendanaan SIMBELMAWA.

### 7.8. Proposal Detail Umum
* **URL**: `/operator/proposal/{id}/detail`
* **Route Name**: `operator.proposal.detail`
* **File View**: [operator/proposal_detail.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/proposal_detail.blade.php)
* **Fungsi Halaman**:
  Pemeriksaan detail proposal lengkap dengan dokumen terlampir, status reviewer, dan log aktivitas.
* **Pop-Up & Modals**:
  1. **Modal Penetapan Hasil Final (`#hasilFinalModal`)**: Form modal penetapan status putusan final dan catatan operator/institusi.

### 7.9. Manajemen Form Penilaian Dinamis (CRUD)
* **URL**: `/operator/form-penilaian`, `/operator/form-penilaian/create`, `/operator/form-penilaian/{id}/edit`
* **Route Name**: `operator.form.penilaian.index`, `create`, `store`, `edit`, `update`, `destroy`
* **File View**: Folder [operator/form_penilaian/](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/form_penilaian)
* **Fungsi Halaman**:
  Membuat dan mengelola form kriteria rubrik penilaian fleksibel untuk review administratif, substantif, semi-final, dan final per skim PKM.
* **Pop-Up & Dialog**:
  1. **Dialog Konfirmasi Hapus**: `confirm('Yakin ingin menghapus form [Nama Form]?')`.
  2. **Toast Validasi Kriteria**: Validasi total persentase bobot kriteria harus pas 100%.

### 7.10. Manajemen Laporan SIMBELMAWA (CRUD)
* **URL**: `/operator/laporan-simbelmawa`, `/operator/laporan-simbelmawa/create`, `/operator/laporan-simbelmawa/{id}/edit`
* **Route Name**: `operator.laporan.simbelmawa.index`, `create`, `store`, `edit`, `update`, `destroy`
* **File View**: Folder [operator/laporan_simbelmawa/](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/laporan_simbelmawa)
* **Fungsi Halaman**:
  Pencatatan rekapitulasi pelaporan resmi universitas ke kementerian (kuota pengajuan, jumlah lolos, jumlah didanai, file laporan rekapitulasi).
* **Pop-Up & Dialog**: Dialog konfirmasi hapus laporan `confirm('Yakin ingin menghapus laporan ini?')`.

### 7.11. Manajemen Akun Sistem (CRUD User)
* **URL**: `/operator/akun`
* **Route Name**: `operator.manage.accounts`, `operator.accounts.store`, `operator.accounts.update`, `operator.accounts.delete`, `operator.accounts.mahasiswa.bulk-delete`
* **File View**: [operator/manajemen_akun.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/operator/manajemen_akun.blade.php)
* **Fungsi Halaman**:
  Pusat pengelolaan seluruh user (Tab Mahasiswa, Tab Dosen, Tab Reviewer, Tab Operator): tambah pengguna baru, ubah data/reset password, filter per fakultas/prodi, pencarian nama/NIM/NIP, hapus per akun, dan penghapusan massal (*bulk delete*) akun mahasiswa dengan checkbox.
* **Pop-Up & Modals**:
  1. **Modal Edit Mahasiswa (`#editMahasiswaModal`)**: Dialog form ubah nama, NIM, email, fakultas, prodi, no telp, status akun.
  2. **Modal Edit Dosen (`#editDosenModal`)**: Dialog form ubah NIP/NIDN, nama dosen, email, fakultas, prodi.
  3. **Modal Edit Reviewer (`#editReviewerModal`)**: Dialog form ubah NIP/NIDN, nama reviewer, email, kepakaran, status aktif.
  4. **Modal Edit Operator (`#editOperatorModal`)**: Dialog form ubah nama operator, email, hak akses.
  5. **Dialog Konfirmasi Hapus Per Akun**: `confirm('Hapus akun ini?')`.
  6. **Dialog Konfirmasi Bulk Delete**: `confirm('Yakin ingin menghapus akun yang dipilih?')`.

---

## 8. Peta Halaman: Portal Pimpinan Perguruan Tinggi

Portal eksekutif bagi Pimpinan PT (Wakil Rektor / Direktur Kemahasiswaan). Fitur penugasan dan konfigurasi bersifat *Read-Only* (monitoring), sedangkan fitur Penilaian Final dan Putusan Kelulusan merupakan wewenang penuh Pimpinan PT.

### 8.1. Dashboard Pimpinan PT
* **URL**: `/pimpinan-pt/dashboard`
* **Route Name**: `pimpinan_pt.dashboard`
* **File View**: [pimpinan_pt/dashboard.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/dashboard.blade.php)
* **Fungsi Halaman**:
  Executive Summary: Tinjauan performa PKM universitas, statistik proposal per skim, per fakultas, progres tahapan dari ruang kontrol, dan daftar proposal dengan skor tertinggi.
* **Pop-Up & Notifikasi**: Toast update status analitik.

### 8.2. Profil Pimpinan PT
* **URL**: `/pimpinan-pt/profile`
* **Route Name**: `pimpinan_pt.profile`
* **File View**: [pimpinan_pt/profile.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/profile.blade.php)

### 8.3. Ruang Kontrol (Monitoring Jadwal)
* **URL**: `/pimpinan-pt/ruang-kontrol`
* **Route Name**: `pimpinan_pt.ruang.kontrol`
* **File View**: [pimpinan_pt/ruang_kontrol.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/ruang_kontrol.blade.php)
* **Fungsi Halaman**:
  Memantau jadwal aktif dan tahapan fase PKM tahun berjalan secara *read-only* tanpa opsi mengubah jadwal.

### 8.4. Pilih Reviewer & Reviewer Seleksi (Monitoring)
* **URL**: `/pimpinan-pt/pilih-reviewer` dan `/pimpinan-pt/pilih-reviewer-seleksi`
* **Route Name**: `pimpinan_pt.pilih.reviewer`, `pimpinan_pt.pilih.reviewer.seleksi`
* **File View**: [pimpinan_pt/pilih_reviewer.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/pilih_reviewer.blade.php) & [pimpinan_pt/pilih_reviewer_seleksi.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/pilih_reviewer_seleksi.blade.php)
* **Fungsi Halaman**:
  Memantau persebaran penugasan reviewer administratif dan reviewer substantif ke masing-masing proposal secara transparan (*read-only* dengan badge monitoring).

### 8.5. Hasil Semi Final & Detail (Monitoring)
* **URL**: `/pimpinan-pt/hasil-semi-final` dan `/pimpinan-pt/detail-hasil-semi-final/{id}`
* **Route Name**: `pimpinan_pt.hasil.semi.final`, `pimpinan_pt.detail.hasil.semi.final`
* **File View**: [pimpinan_pt/hasil_semi_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/hasil_semi_final.blade.php) & [pimpinan_pt/detail_hasil_semi_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/detail_hasil_semi_final.blade.php)
* **Fungsi Halaman**:
  Memantau perolehan skor semi-final, nilai normalisasi, dan perbandingan penilaian Reviewer 1 vs Reviewer 2.

### 8.6. Penilaian Final / Hasil Final (Wewenang Pimpinan PT)
* **URL**: `/pimpinan-pt/penilaian-final` dan `/pimpinan-pt/hasil-final`
* **Route Name**: `pimpinan_pt.penilaian.final`, `pimpinan_pt.hasil.final`
* **File View**: [pimpinan_pt/hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/hasil_final.blade.php)
* **Fungsi Halaman**:
  Modul penentu kelulusan universitas: Pimpinan PT menetapkan proposal yang lolos seleksi akhir universitas untuk diajukan ke SIMBELMAWA nasional.

### 8.7. Detail Penilaian Final (Form Scoring & Putusan)
* **URL**: `/pimpinan-pt/detail-hasil-final/{id}`
* **Route Name**: `pimpinan_pt.detail.hasil.final`, POST `pimpinan_pt.update.hasil.final`
* **File View**: [pimpinan_pt/detail_hasil_final.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/detail_hasil_final.blade.php)
* **Fungsi Halaman**:
  Halaman penilaian final dan putusan resmi: menampilkan PDF preview revisi akhir mahasiswa, penilaian skor institusi, input catatan pimpinan PT (minimal 50 karakter), tombol putusan *Lolos Universitas* atau *Tidak Lolos*, dan penguncian hasil.
* **Pop-Up & Toasts**:
  1. **Toast Validasi**: "Catatan final minimal 50 karakter." jika pengisian catatan terlalu pendek.
  2. **Toast Validasi**: "Semua skor penilaian wajib diisi (skala 1-7)".
  3. **Toast Sukses**: `✅ Hasil final dan keputusan berhasil disimpan!`.

### 8.8. Form Penilaian & Laporan SIMBELMAWA (Monitoring)
* **URL**: `/pimpinan-pt/form-penilaian` dan `/pimpinan-pt/laporan-simbelmawa`
* **Route Name**: `pimpinan_pt.form.penilaian`, `pimpinan_pt.laporan.simbelmawa`
* **File View**: [pimpinan_pt/form_penilaian.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/form_penilaian.blade.php) & [pimpinan_pt/laporan_simbelmawa.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/laporan_simbelmawa.blade.php)
* **Fungsi Halaman**:
  Tinjauan instrumen rubrik penilaian aktif dan arsip laporan kuota/pendanaan SIMBELMAWA secara *read-only*.

### 8.9. Manajemen Akun Pimpinan PT
* **URL**: `/pimpinan-pt/akun`
* **Route Name**: `pimpinan_pt.manage.accounts`
* **File View**: [pimpinan_pt/manajemen_akun.blade.php](file:///c:/Users/USER/Downloads/PengajuanProposal/resources/views/pimpinan_pt/manajemen_akun.blade.php)
* **Fungsi Halaman**:
  Akses penuh pimpinan untuk mengelola atau mengawasi seluruh akun mahasiswa, dosen, reviewer, dan staf operator.
* **Pop-Up & Modals**:
  * Memiliki 4 modal edit yang sama (`#editMahasiswaModal`, `#editDosenModal`, `#editReviewerModal`, `#editOperatorModal`).
  * Dialog konfirmasi hapus satuan dan hapus massal (*bulk delete*).

---

## 9. Rekomendasi Titik Sentral Perbaikan UI/UX

Untuk mempersiapkan panduan/skill UI/UX yang akan Anda berikan, berikut adalah area inkonsistensi yang perlu distandarisasi antar peran:

1. **Standardisasi Modal & Dialog**:
   * Saat ini sebagian aksi masih menggunakan native browser `confirm('...')`. Perlu digantikan dengan modal konfirmasi seragam (misal: modal konfirmasi Bootstrap modern atau modal komponen seragam).
2. **Standardisasi Toast Notification**:
   * Terdapat variasi implementasi fungsi `showToast(...)` lokal di beberapa blade view. Perlu disatukan ke dalam satu API global tunggal (misal `window.AppToast.show(message, type)`).
3. **Penyelarasan Komponen PDF Previewer**:
   * Halaman split-view PDF pada Mahasiswa, Reviewer, dan Pimpinan PT memiliki variasi styling control bar (zoom, next/prev page, rotate). Perlu komponen PDF viewer yang seragam.
4. **Desain Status Badge & Stepper Alur**:
   * Timeline fase di Dashboard Mahasiswa, Ruang Kontrol Operator, dan Detail Proposal Pimpinan PT perlu diselaraskan dengan stepper visual yang modern, konsisten, dan responsif di mobile.
