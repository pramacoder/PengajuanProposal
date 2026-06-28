<?php

/**
 * Konfigurasi Checklist Review Administratif PKM
 * 
 * File ini berisi mapping lengkap antara skim proposal dengan checklist items
 * yang akan ditampilkan pada form review administratif.
 * 
 * Struktur:
 * - Setiap skim memiliki array kategori
 * - Setiap kategori memiliki array items
 * - Item disimpan sebagai string yang akan digunakan sebagai value checkbox
 * 
 * @author Pramajaya
 * @version 1.0.0
 */

return [
    // ============================================
    // PKM-AI (Artikel Ilmiah)
    // ============================================
    'AI' => [
        'A. Kelengkapan Administratif' => [
            'Jumlah anggota termasuk ketua tim 3-5 orang',
            'Terdiri dari minimal 2 angkatan yang berbeda',
            'Nama pengusul tidak boleh disingkat',
            'Naskah Proposal tidak boleh berisikan halaman Sampul, halaman Pengesahan, daftar Isi, Usulan Pendanaan',
            'Jumlah halaman mulai "Judul" hingga "Daftar Pustaka adalah 8-15 halaman',
            'Tanggal, bulan dan Tahun harus sesuai dengan tahun pengusulan (mulai dari diterbitkannya buku pedoman sampai batas pengumpulan proposal di tahun tersebut) pada lampiran (1 Februari-1 Maret)',
            'Surat pernyataan ketua pengusul menggunakan Format terbaru, bermaterai dan ditandatangani',
            'Harus terdapat lampiran hasil pengecekan plagiasi',
            'Naskah utama menggunakan bahasa Indonesia, kecuali ABSTRACT bahasa inggris',
            'Artikel tidak merupakan narrative review atau sejenisnya',
            'Tidak boleh berisi Rekapitulasi Anggaran',
            'Tanda tangan pada lampiran tidak boleh hasil cropping lokal',
            'Urutan dan jumlah lampiran wajib harus sesuai',
        ],
        'B. Format Penulisan' => [
            'Ukuran kertas A4',
            'Margin kiri 4 cm, kanan, bawah, atas 3 cm',
            'Halaman inti dari Judul hingga Lampiran diberi angka arab (1,2,3,....) ditulis di kanan atas',
            'Konten ditulis dengan rata kanan kiri (Justify)',
        ],
        'C. Sistematika Penulisan Judul, Nama Penulis, Alamat Institusi, Abstrak dan Abstract' => [
            'Judul Artikel, Nama Penulis, Alamat Institusi, Abstrak dan Abstract ditulis dalam satu halaman. Teks menggunakan jarak baris 1,0 spasi',
            'Judul Artikel PKM-AI ditulis menggunakan tipe huruf Times New Roman ukuran 12, tidak boleh disingkat/menggunakan akronim, dicetak tebal dan tegak kecuali istilah asing, maksimum 20 kata, huruf kapital',
            'Penulisan Nama penulis dan alamat institusinya serta penulis korespondensi (correspondence author) ditulis dengan tipe huruf Times New Roman ukuran 10 cetak normal. Nama-nama penulis beserta alamat institusi dituliskan tepat di bawah judul, Penulis korespondensi (correspondence author) ditulis di bawah nama penulis, ditulis alamat e-mailnya',
            'Abstrak/Abstract dan kata-kata kunci (keywords) ditulis dengan tipe huruf menggunakan Times New Roman ukuran 11. Abstrak Bahasa Indonesia dicetak tegak dan Abstract Bahasa Inggris dicetak miring disusun dalam format satu paragraf, perataan teks menggunakan rata kiri dan kanan, dan masing-masing memuat tidak lebih dari 250 kata',
            'Kata kunci pada abstrak terdiri dari 3-5 kata/frasa',
        ],
        'D. Sistematika Penulisan Pendahuluan sampai dengan Daftar Pustaka' => [
            'Menggunakan huruf Times New Roman ukuran 12',
            'Konten/teks paragraf ditulis menggunakan line spacing 1,15',
            'Format paragraf menggunakan satu kolom',
            'Daftar pustaka ditulis dengan jarak spasi 1,15, rata kanan kiri',
            'Keterangan/judul gambar diletakkan di bawah gambar dengan huruf Times New Roman 11 dan ditulis dalam satu spasi',
            'Tabel dibuat dengan format standar (tanpa garis menyilang dan membujur di tengah-tengah)',
            'Keterangan/judul tabel diletakkan di atas tabel dengan huruf Times New Roman 11 dan ditulis dalam satu spasi, Rata tengah',
            'Sitasi menggunakan sistem Harvard (contoh: ...........(Harold et al., 2021))',
            'Daftar pustaka menggunakan sistem Harvard, diurutkan nama belakang penulis berdasarkan abjad',
        ],
        'E. Kelengkapan Sistematika' => [
            'JUDUL',
            'Penulis dan korespondensi',
            'ABSTRAK',
            'ABSTRACT',
            'PENDAHULUAN',
            'METODE',
            'HASIL DAN PEMBAHASAN',
            'KESIMPULAN',
            'UCAPAN TERIMAKASIH',
            'KONTRIBUSI PENULIS',
            'DAFTAR PUSTAKA',
            'LAMPIRAN-LAMPIRAN',
            'Lampiran 1. Biodata Ketua dan Anggota, serta Dosen Pendamping (yang ditandatangani dengan tanda tangan basah bukan scan)',
            'Lampiran 2. Kontribusi ketua, anggota, dan dosen pendamping (Gunakan format terbaru)',
            'Lampiran 3. Surat Pernyataan Ketua Tim Penyusun (ditandatangani diatas materai 10 ribu dengan tandatangan basah bukan hasil scan. Gunakan format terbaru)',
            'Lampiran 4. Surat Pernyataan Sumber Tulisan (Gunakan format terbaru)',
            'Lampiran 5. Hasil Pengecekan Plagiasi dengan Indeks Similaritas Maksimum 25%',
        ],
    ],

    // ============================================
    // PKM-GFT (Gagasan Futuristik Tertulis)
    // ============================================
    'GFT' => [
        'A. Kelengkapan Administratif' => [
            'Jumlah anggota termasuk ketua tim 3-5 orang',
            'Terdiri dari minimal 2 angkatan yang berbeda',
            'Nama pengusul tidak boleh disingkat',
            'Naskah Proposal tidak berisikan Halaman Sampul dan Halaman Pengesahan',
            'Judul tidak boleh disingkat/menggunakan akronim, maksimal 20 kata',
            'Jumlah halaman inti (bab 1 Pendahuluan - daftar pustaka) adalah 8-15 halaman',
            'Tanggal, bulan dan Tahun harus sesuai dengan tahun pengusulan (mulai dari diterbitkannya buku pedoman sampai batas pengumpulan proposal di tahun tersebut) pada lampiran (1 Februari-1 Maret)',
            'Surat pernyataan ketua pengusul menggunakan Format terbaru, bermaterai dan ditandatangani',
            'Tanda tangan pada lampiran tidak boleh hasil cropping lokal',
            'Topik harus sesuai dengan ciri khusus PKM-GFT, instan dampak terbatas lokal',
        ],
        'B. Format Penulisan' => [
            'Ukuran kertas A4',
            'Font Times New Roman ukuran 12',
            'Margin kiri 4 cm, kanan, bawah, atas 3 cm',
            'Daftar isi menggunakan huruf (i,ii,iii,....), dan diletakan pada sudut kanan bawah',
            'Bab 1 - Lampiran angka arab (1,2,3,....) ditulis di kanan atas',
            'Konten ditulis menggunakan line spacing 1,15',
            'Konten ditulis dengan rata kanan kiri (Justify)',
            'Penulisan judul bab menggunakan HURUF BESAR dan rata kanan kiri',
            'Daftar pustaka ditulis dengan jarak spasi 1,15, rata kanan kiri',
            'Keterangan gambar diketik di bawah gambar, dan rata tengah, ukuran font 12 pt',
            'Keterangan tabel di buat diatas tabel, dan rata tengah, ukuran font 12 pt',
            'Sitasi menggunakan sistem Harvard (contoh: ...........(Harold et al., 2021))',
            'Daftar pustaka menggunakan sistem Harvard, diurutkan nama belakang penulis berdasarkan abjad',
        ],
        'C. Kelengkapan BAB' => [
            'DAFTAR ISI',
            'BAB 1. PENDAHULUAN',
            'BAB 2. GAGASAN',
            'BAB 3. KESIMPULAN',
            'DAFTAR PUSTAKA',
            'LAMPIRAN-LAMPIRAN',
            'Lampiran 1. Biodata Ketua dan Anggota, serta Dosen Pendamping (yang ditandatangani dengan tanda tangan basah bukan scan)',
            'Lampiran 2. Susunan Tim Pengusul dan Pembagian Tugas (menggunakan format terbaru)',
            'Lampiran 3. Surat Pernyataan Ketua Tim Pengusul (menggunakan format terbaru, ditandatangani diatas materai 10 ribu dengan tandatangan basah bukan hasil scan.)',
            'Lampiran 4. Hasil Uji Periksa Similaritas Proposal (Turtitin, iThenticate atau yang lainnya) dengan indeks similaritas maksimum 25%',
        ],
    ],

    // ============================================
    // PKM Pendanaan (8 Bidang)
    // RE, RSH, K, KI, KC, VGK, PM, PI
    // ============================================
    'RE' => [
        'A. Kelengkapan Administratif' => [
            'Jumlah anggota termasuk ketua tim 3-5 orang',
            'Terdiri dari minimal 2 angkatan yang berbeda',
            'Nama pengusul tidak boleh disingkat',
            'Naskah Proposal tidak boleh berisikan Halaman Sampul dan Halaman Pengesahan',
            'Naskah Proposal tidak boleh berisikan Abstrak dan Ringkasan',
            'Jumlah halaman inti (bab 1 - daftar pustaka) adalah 10 halaman',
            'Besaran dana yang diajukan ke BELMAWA sesuai ketentuan',
            'Wajib ada dana yang diajukan ke Perguruan Tinggi (Wajib ada dan maksimal Rp. 2.000.000)',
            'Besaran dana pendamping dari sponsor dan mitra lainnya (Tidak wajib dan maksimal Rp. 1.000.000)',
            'Tanggal, bulan dan Tahun harus sesuai dengan tahun pengusulan (mulai dari diterbitkannya buku pedoman sampai batas pengumpulan proposal di tahun tersebut) pada lampiran (1 Februari-1 Maret)',
            'Surat pernyataan ketua pengusul menggunakan Format terbaru, bermaterai dan ditandatangani',
            'Tanda tangan pada lampiran bukan hasil cropping lokal',
        ],
        'B. Format Penulisan' => [
            'Ukuran kertas A4',
            'Font Times New Roman ukuran 12',
            'Margin kiri 4 cm, kanan, bawah, atas 3 cm',
            'Daftar isi menggunakan huruf (i,ii,iii,....), dan diletakan pada sudut kanan bawah',
            'Bab 1 - Lampiran angka arab (1,2,3,....) ditulis di kanan atas',
            'Konten ditulis menggunakan line spacing 1,15',
            'Konten ditulis dengan rata kanan kiri (Justify)',
            'Penulisan judul bab menggunakan HURUF BESAR dan rata kanan kiri',
            'Format jadwal kegiatan pada bab 4 sesuai panduan',
            'Format rekapitulasi anggaran biaya pada bab 4 sesuai panduan',
        ],
        'C. Format Isi Utama' => [
            'Judul tidak boleh disingkat/menggunakan akronim, maksimal 20 kata',
            'Luaran wajib sesuai panduan dan tertulis di proposal (1. Laporan kemajuan; 2. Laporan akhir; 3. Artikel ilmiah; 4. Akun media sosial)',
            'Waktu pelaksanaan 3-4 bulan',
            'Keterangan gambar diketik di bawah gambar, dan rata tengah, ukuran 12 pt',
            'Keterangan tabel di buat diatas tabel, dan rata kanan kiri, ukuran 12 pt',
            'Sitasi menggunakan sistem Harvard (contoh: ...........(Harold et al., 2021))',
            'Daftar pustaka ditulis dengan jarak spasi 1,15, rata kanan kiri',
            'Daftar pustaka menggunakan sistem Harvard, diurutkan nama belakang penulis berdasarkan abjad',
            'Sumber pustaka yang digunakan berasal dari jurnal ilmiah atau hasil riset terbaru (5 sampai 10 tahun terakhir)',
        ],
        'D. Kelengkapan BAB' => [
            'Proposal dimulai dari DAFTAR ISI',
            'Bunyi judul dan jumlah BAB sesuai ketentuan SKIM',
            'Lampiran berurutan sesuai panduan',
            'Jumlah lampiran wajib lengkap',
            'Lampiran Biodata Ketua dan Anggota, serta Dosen Pendamping (yang ditandatangani dengan tanda tangan basah bukan scan)',
            'Lampiran Justifikasi Anggaran Kegiatan sesuai format panduan',
            'Lampiran Susunan Tim Pengusul dan Pembagian Tugas menggunakan Format terbaru',
            'Lampiran Surat Pernyataan Ketua Tim Pengusul (menggunakan format terbaru, ditandatangani diatas materai 10 ribu dengan tandatangan basah bukan hasil scan.)',
            'Lampiran Hasil Uji turnitin sesuai ketentuan maksimal 25%',
        ],
    ],

    // Mapping untuk skim pendanaan lainnya menggunakan checklist yang sama dengan RE
    'RSH' => 'RE',
    'K' => 'RE',
    'KI' => 'RE',
    'KC' => 'RE',
    'VGK' => 'RE',
    'PM' => 'RE',
    'PI' => 'RE',

    // ============================================
    // Checklist Umum (Fallback)
    // Digunakan jika skim tidak dikenali
    // ============================================
    'default' => [
        'Kesalahan Umum' => [
            'Dokumen tidak lengkap',
            'Format dokumen tidak sesuai',
            'Data mahasiswa tidak lengkap',
            'Data dosen tidak lengkap',
            'Skim tidak sesuai',
            'Tanda tangan tidak lengkap',
            'Stempel/legitimasi tidak ada',
            'Salah penulisan identitas (nama/NIM/NIP)',
            'Tanggal dokumen tidak ada',
            'Jumlah halaman tidak sesuai ketentuan',
            'File tidak terbaca atau rusak',
            'Dokumen tidak sesuai template',
            'Lainnya',
        ],
    ],
];

