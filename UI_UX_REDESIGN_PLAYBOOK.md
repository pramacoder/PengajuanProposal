# UI/UX Redesign Playbook — Sistem Pengajuan Proposal PKM

> Tujuan: menjadi acuan utama AI Agent saat mengaudit, merancang ulang, mengimplementasikan, dan memverifikasi UI/UX Sistem Pengajuan Proposal PKM.
>
> Prinsip utama: **konsisten secara visual, jelas secara fungsional, aman terhadap alur bisnis, dan teruji di seluruh role.**

## 1. Konteks produk

Sistem memiliki role dan kebutuhan berbeda:
- **Guest / Publik:** login, permohonan akun mahasiswa, lupa/reset password.
- **Mahasiswa:** dashboard, profil, pengajuan proposal, daftar/detail proposal, revisi, revisi akhir.
- **Dosen:** portal pembimbing, pendamping, dan universitas; validasi, pemantauan, serta hasil review.
- **Reviewer:** antrean review administratif/substantif dan workspace penilaian.
- **Operator:** ruang kontrol fase/jadwal, penugasan reviewer, hasil seleksi, formulir penilaian, laporan, dan manajemen akun.
- **Pimpinan PT:** dashboard eksekutif, monitoring, penilaian final, dan keputusan akhir.

Fungsi tiap role tidak boleh disamaratakan hanya demi keseragaman visual. Gunakan pola visual yang sama, tetapi pertahankan informasi, izin, dan tindakan yang memang khusus untuk role tersebut.

## 2. Aturan kerja wajib untuk Agent

Sebelum mengubah kode:
1. Periksa struktur proyek, layout utama, stylesheet, JavaScript, komponen Blade, route, middleware/role, dan pola UI yang sudah ada.
2. Buat inventaris halaman per role dan kelompokkan menjadi pola: dashboard, tabel/listing, detail, form, workspace review, profil, dan halaman autentikasi.
3. Audit inkonsistensi yang nyata: warna, tipografi, spacing, lebar konten, header, sidebar/navbar, tombol, input, tabel, badge status, modal, toast, responsivitas, serta state loading/error/empty.
4. Catat baseline dan tangkapan layar halaman utama sebelum perubahan jika tooling tersedia.
5. Buat rancangan design system dan rencana perubahan bertahap sebelum melakukan refactor lintas halaman.

Jangan langsung menulis ulang seluruh aplikasi. Terapkan perubahan bertahap, validasi setiap kelompok halaman, dan pertahankan perubahan yang sudah benar.

## 3. Design system sebagai satu sumber kebenaran

Buat atau rapikan satu sumber token desain terpusat. Jangan menyebarkan nilai warna, radius, shadow, spacing, dan ukuran kontrol secara acak ke banyak file.

Tetapkan dan dokumentasikan:
- **Warna:** primary, secondary, accent, background, surface, border, text, muted, success, warning, danger, info.
- **Tipografi:** keluarga font, skala heading/body/caption, bobot, line-height.
- **Spacing:** skala jarak konsisten untuk section, card, form, tabel, dan layout.
- **Layout:** lebar konten, grid, gutter, sidebar, topbar, dan breakpoint.
- **Shape/elevation:** radius, border, shadow, divider.
- **Komponen:** button, input, select, textarea, checkbox, radio, tabs, card, table, badge, alert, toast, modal/dialog, pagination, breadcrumb, dropdown, tooltip, skeleton.
- **Status:** warna dan label yang konsisten untuk Draft, Menunggu Validasi, Valid, Review Administratif, Review Substantif, Revisi, Semi Final, dan Final.

Jangan menentukan palet atau gaya visual hanya berdasarkan tren. Audit identitas produk yang sudah ada, pilih arah visual yang sesuai untuk aplikasi administrasi akademik, lalu gunakan secara konsisten. Jika belum ada identitas visual yang jelas, ajukan satu usulan desain yang koheren sebelum mengimplementasikannya.

## 4. Konsistensi layout lintas halaman

- Gunakan layout utama bersama untuk area yang memiliki pola navigasi sama.
- Samakan tinggi topbar, lebar sidebar, padding konten, posisi judul halaman, breadcrumb, jarak antarseksi, dan perilaku navigasi.
- Gunakan pola page header yang konsisten: judul, deskripsi singkat bila diperlukan, breadcrumb bila membantu, dan aksi utama yang relevan.
- Dashboard tidak harus identik; prioritaskan tugas utama dari masing-masing role.
- Jangan menambahkan card hanya untuk menghias halaman. Gunakan card bila memang mengelompokkan informasi.
- Hindari gradient, shadow, border, ikon, animasi, atau warna dekoratif yang tidak punya tujuan fungsional.
- Gunakan ikon dari satu keluarga yang konsisten. Jangan mencampur ikon dengan gaya visual yang berbeda.

## 5. Aturan komponen dan interaksi

### Tombol
- Tentukan hierarki primary, secondary, outline/tertiary, dan destructive.
- Gunakan label tindakan yang jelas, misalnya “Simpan Draft”, “Kirim Proposal”, “Setujui”, atau “Minta Perbaikan”.
- Satu area biasanya memiliki satu aksi utama yang paling menonjol.
- Tombol harus memiliki state default, hover, focus, active, disabled, dan loading jika relevan.

### Formulir
- Label selalu jelas dan tetap terlihat; jangan mengandalkan placeholder sebagai satu-satunya label.
- Kelompokkan field terkait dan gunakan urutan yang sesuai dengan alur kerja.
- Tampilkan required/optional secara konsisten.
- Validasi harus dekat dengan field terkait dan menjelaskan cara memperbaiki kesalahan.
- Pertahankan nilai input ketika validasi gagal jika aman dan sesuai.
- Unggahan file harus menjelaskan format, batas ukuran, status unggahan, dan cara mengganti/menghapus file.
- Untuk tindakan final yang berdampak besar, jelaskan konsekuensinya sebelum pengguna mengonfirmasi.

### Tabel dan daftar
- Samakan gaya header, tinggi baris, padding, alignment, pagination, pencarian, filter, dan empty state.
- Kolom yang paling penting harus mudah dipindai; hindari kolom berlebihan.
- Aksi per baris harus konsisten dan tidak memenuhi layar.
- Pastikan tabel tetap dapat digunakan pada layar sempit; gunakan adaptasi layout yang sesuai, bukan sekadar mengecilkan semua elemen.
- Jangan mengubah arti, urutan proses, atau data yang ditampilkan tanpa alasan fungsional.

### Badge dan status proses
- Satu status harus memiliki label, warna semantik, dan arti yang sama di semua portal.
- Jangan mengandalkan warna saja untuk menyampaikan status.
- Pertahankan istilah proses yang digunakan sistem kecuali ada persetujuan eksplisit untuk mengubahnya.

### Modal, dialog, dan toast
- Gunakan pola ukuran, judul, tombol, spacing, dan penempatan yang konsisten.
- Modal untuk informasi/detail; dialog konfirmasi untuk tindakan yang berisiko atau sulit dibatalkan.
- Toast untuk feedback singkat, bukan sebagai satu-satunya tempat menampilkan kesalahan form penting.
- Pesan harus menyebut apa yang terjadi dan, jika perlu, langkah berikutnya.
- Pastikan fokus keyboard, tombol tutup, Escape, dan perilaku fokus setelah modal ditutup ditangani dengan benar.

## 6. State yang harus dirancang

Untuk setiap halaman atau komponen data, periksa state yang relevan:
- Initial/loading.
- Data tersedia.
- Empty state dengan penjelasan dan langkah berikutnya.
- Error dari validasi, jaringan, server, atau izin akses.
- Success setelah aksi.
- Disabled/loading saat submit untuk mencegah pengiriman berulang.
- Permission denied atau data yang tidak tersedia untuk role terkait.
- Data panjang, teks panjang, nama panjang, dan tabel tanpa data.
- Konfirmasi tindakan destruktif/finalisasi.

Jangan membuat state palsu yang tidak sesuai dengan backend. Jika implementasi state belum didukung, dokumentasikan kebutuhan tersebut daripada berpura-pura bahwa fitur sudah bekerja.

## 7. Aksesibilitas dan responsivitas

- Semua tindakan penting dapat diakses dengan keyboard.
- Focus state terlihat jelas.
- Kontras teks dan kontrol harus memadai.
- Gunakan heading berurutan, label form yang terhubung, dan nama aksesibel untuk tombol ikon.
- Jangan menggunakan warna sebagai satu-satunya pembeda.
- Periksa desktop, tablet, dan mobile.
- Hindari horizontal overflow, elemen bertumpuk, tombol terlalu kecil, serta modal yang tidak dapat digulir.
- Hormati preferensi reduced motion bila animasi digunakan.
- Animasi harus membantu memahami perubahan state, bukan sekadar dekorasi.

## 8. Batasan keamanan dan fungsional

Redesign adalah perubahan presentasi dan pengalaman pengguna, bukan perubahan aturan bisnis.

**Dilarang tanpa permintaan dan persetujuan eksplisit:**
- Mengubah route, nama route, HTTP method, middleware, role guard, permission, atau aturan otorisasi.
- Mengubah validasi, aturan fase PKM, rumus/bobot penilaian, syarat catatan review, batas unggahan, atau status proses.
- Menghapus fitur, field, tombol, informasi, modal, atau feedback yang masih digunakan.
- Mengubah nama field form, atribut `name`, ID/selector yang dipakai JavaScript, kontrak AJAX/API, atau format payload.
- Mengubah query, model, database schema, atau alur backend hanya demi menyesuaikan UI.
- Menampilkan tindakan kepada role yang tidak berhak menjalankannya.
- Mengganti interaksi nyata dengan tombol dekoratif atau data dummy.

Sebelum mengubah markup, cari pemakaian ID, class, atribut, dan selector pada JavaScript/CSS. Pertahankan kontrak yang dibutuhkan atau perbarui semua pemakai terkait dengan pengujian.

## 9. Strategi implementasi bertahap

1. **Audit:** inventaris halaman dan catat masalah berdasarkan dampak pada konsistensi, usability, aksesibilitas, dan risiko fungsional.
2. **Fondasi:** rapikan token, layout utama, typography, spacing, button, form control, badge, alert, modal, dan toast.
3. **Halaman acuan:** pilih satu halaman representatif untuk menetapkan standar, misalnya dashboard dan satu halaman tabel.
4. **Pola berulang:** perbaiki listing/table, form, detail, profile, dan workspace review sebagai kelompok.
5. **Rollout per role:** terapkan komponen yang sama pada Mahasiswa, Dosen, Reviewer, Operator, dan Pimpinan PT.
6. **Regression check:** pastikan route, permission, submit form, upload, filter, pagination, modal, AJAX, dan status masih bekerja.
7. **Visual QA:** bandingkan halaman sebelum/sesudah pada viewport yang sama dan perbaiki penyimpangan.
8. **Dokumentasi:** perbarui panduan desain jika ada keputusan atau komponen baru.

Urutan prioritas: masalah yang menghambat tugas pengguna atau berisiko pada submit/penilaian lebih dahulu; inkonsistensi kosmetik setelahnya.

## 10. Checklist penerimaan

Redesign baru dianggap selesai jika:
- [ ] Semua halaman menggunakan token dan pola komponen yang disepakati.
- [ ] Header, navigasi, jarak, typography, tombol, form, tabel, badge, modal, dan toast konsisten.
- [ ] Setiap role tetap melihat fungsi dan data sesuai kewenangannya.
- [ ] Semua tombol dan interaksi yang terlihat berfungsi.
- [ ] Loading, empty, error, success, disabled, dan konfirmasi tersedia sesuai kebutuhan.
- [ ] Tidak ada perubahan tidak sengaja pada route, validasi, permission, kontrak backend, atau alur bisnis.
- [ ] Tidak ada error JavaScript baru atau error console yang diketahui.
- [ ] Layout diuji di desktop, tablet, dan mobile.
- [ ] Fokus keyboard, label form, kontras, dan state aksesibel telah diperiksa.
- [ ] Halaman utama dibandingkan secara visual dan penyimpangan antarhalaman telah diperbaiki.
- [ ] Ringkasan perubahan, file yang disentuh, hasil pengujian, dan pekerjaan tersisa dilaporkan.

## 11. Template prompt untuk Agent

Gunakan instruksi berikut saat memulai redesign:

> Baca dan patuhi `UI_UX_REDESIGN_PLAYBOOK.md` sebelum mengubah UI.
>
> Mulai dengan mengaudit struktur proyek, layout utama, CSS/JS, komponen yang digunakan bersama, dan halaman dari setiap role. Jangan langsung mengubah semua halaman.
>
> Buat laporan singkat yang berisi:
> 1. Pola UI yang sekarang digunakan dan inkonsistensinya.
> 2. Komponen yang perlu dijadikan shared component.
> 3. Usulan design tokens dan arah visual yang sesuai untuk aplikasi administrasi akademik.
> 4. Risiko terhadap selector JavaScript, form, route, permission, AJAX, dan alur bisnis.
> 5. Urutan implementasi berdasarkan dampak dan risiko.
>
> Setelah audit, implementasikan secara bertahap. Pertahankan framework, struktur backend, route, hak akses, validasi, nama field, ID/selector yang dipakai, kontrak API/AJAX, serta semua fungsi yang sudah ada. Jangan gunakan data dummy untuk menggantikan fungsi nyata.
>
> Setelah setiap kelompok halaman selesai, jalankan pemeriksaan yang tersedia dan laporkan hasilnya. Verifikasi konsistensi visual, responsivitas, aksesibilitas, state interaksi, dan regresi fungsi. Jangan menyatakan pengujian lulus jika belum benar-benar dijalankan.

## 12. Catatan penggunaan

Dokumen peta halaman proyek menjadi referensi untuk cakupan halaman dan fungsi yang perlu dipertahankan. Dokumen ini adalah pedoman redesign; ia bukan pengganti pemeriksaan source code aktual. Sebelum mengedit, Agent harus memastikan bahwa nama file, route, selector, dan perilaku yang tercantum masih sesuai dengan repository saat ini.
