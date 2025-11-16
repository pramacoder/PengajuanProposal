<?php

/**
 * Konfigurasi Kriteria Penilaian Substantif PKM
 * 
 * File ini berisi mapping lengkap antara skim proposal dengan kriteria penilaian substantif
 * yang akan ditampilkan pada form review substantif.
 * 
 * Struktur:
 * - Setiap skim memiliki array kriteria
 * - Setiap kriteria memiliki: nama, bobot (dalam %)
 * - Skor: 0-10 (input oleh reviewer)
 * - Nilai: Bobot × Skor
 * - Total maksimal: 1000 (jika semua skor = 10)
 * - Konversi ke decimal: Total / 10 (contoh: 789 → 78.9)
 * 
 * @author Pramajaya
 * @version 1.0.0
 */

return [
    // ============================================
    // PKM-RE (Riset Eksak)
    // Sesuai dengan Lampiran 8. Formulir Penilaian Proposal
    // ============================================
    'RE' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Gagasan (orisinalitas, unik dan bermanfaat)',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Penyajian rumusan masalah (data lengkap, fokus dan atraktif)',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Perbandingan dengan riset terdahulu (kebaruan)',
                    'bobot' => 10,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesesuaian dan Kemutakhiran Metode Riset',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kontribusi Perkembangan Ilmu dan Teknologi',
                    'bobot' => 10,
                ],
                [
                    'kriteria' => 'Sintesis Telaah Literatur, Potensi dan Prediksi Hasil Riset',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Kemanfaatan',
                    'bobot' => 10,
                ],
            ],
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-RSH (Riset Sosial Humaniora)
    // Sesuai dengan Panduan PKM 2025 - Formulir Penilaian Proposal
    // Struktur: Kriteria utama dengan sub-kriteria
    // ============================================
    'RSH' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Gagasan (orisinalitas, unik dan bermanfaat)',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Penyajian rumusan masalah (data lengkap, fokus dan atraktif)',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Perbandingan dengan riset terdahulu (state of the art)',
                    'bobot' => 10,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesesuaian dan Kemutakhiran Metode Riset',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kontribusi Perkembangan Ilmu dan Teknologi',
                    'bobot' => 10,
                ],
                [
                    'kriteria' => 'Sintesis Telaah Literatur, Potensi dan Prediksi Hasil Riset',
                    'bobot' => 15,
                ],
            ],
        ],
        [
            'kriteria' => 'Kemanfaatan',
            'bobot' => 10,
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-PM (Pengabdian Masyarakat)
    // Sesuai dengan Lampiran 9. Formulir Penilaian Proposal
    // ============================================
    'PM' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Perumusan Masalah',
                    'bobot' => 10,
                ],
                [
                    'kriteria' => 'Ketepatan Solusi (fokus dan atraktif)',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Ketepatan Masyarakat Mitra dan Kondisi Existing Mitra',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Potensi Nilai Tambah untuk Mitra Program',
                    'bobot' => 25,
                ],
                [
                    'kriteria' => 'Potensi Keberlanjutan Program',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-PI (Penerapan IPTEKS)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'PI' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Identifikasi Permasalahan atau Kebutuhan Mitra',
                    'bobot' => 10,
                ],
                [
                    'kriteria' => 'Ketepatan Solusi yang Ditawarkan',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Ketepatan Mitra Program',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Potensi Nilai Tambah untuk Mitra Program',
                    'bobot' => 25,
                ],
                [
                    'kriteria' => 'Potensi Keberlanjutan Program',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-KC (Karsa Cipta)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'KC' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Gagasan (orisinalitas, unik dan manfaat masa depan)',
                    'bobot' => 20,
                ],
                [
                    'kriteria' => 'Kemutakhiran ipteks yang diadopsi',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesesuaian Tahap Pelaksanaan',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kontribusi produk luaran terhadap solusi permasalahan dan perkembangan IPTEKS',
                    'bobot' => 25,
                ],
                [
                    'kriteria' => 'Potensi Publikasi Artikel Ilmiah/Kekayaan Intelektual',
                    'bobot' => 10,
                ],
            ],
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-K (Kewirausahaan)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'K' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Gagasan Usaha (analisis peluang pasar, dukungan sumber data yang berkualitas)',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Keunggulan Produk (berbasis iptek, unik, dan bermanfaat)',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Rancangan Usaha',
            'bobot' => 20,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Potensi Pelaksanaan dan Perolehan Profit',
                    'bobot' => 20,
                ],
                [
                    'kriteria' => 'Potensi Keberlanjutan Usaha',
                    'bobot' => 15,
                ],
            ],
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-KI (Karya Inovatif)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'KI' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Urgensi Permasalahan, Cakupan Pengguna',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Kreativitas Gagasan Solusi (orisinalitas, problem based, specific, measurable)',
                    'bobot' => 25,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesesuaian Tahap Pelaksanaan',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Produk (dampak ekonomi nasional)',
            'bobot' => 10,
        ],
        [
            'kriteria' => 'Ketepatan Iptek, Standar, Regulasi dan Metode yang Digunakan',
            'bobot' => 25,
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-VGK (Video Gagasan Konstruktif)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'VGK' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kreativitas Gagasan (ketepatan solusi, komprehensif, unik, originalitas, dan konstruktif)',
                    'bobot' => 20,
                ],
                [
                    'kriteria' => 'Kreativitas Komunikasi pada Skenario Konten Video (informatif, kejelasan alur, unik, objektif, & originalitas)',
                    'bobot' => 20,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesesuaian Tahap Pelaksanaan',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kontribusi Gagasan Terhadap Isu Keprihatinan Bangsa',
                    'bobot' => 15,
                ],
                [
                    'kriteria' => 'Potensi Efektivitas Informasi Pada Skenario Video',
                    'bobot' => 15,
                ],
            ],
        ],
        [
            'kriteria' => 'Sumber Informasi',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-AI (Artikel Ilmiah)
    // ============================================
    'AI' => [
        [
            'kriteria' => 'JUDUL: Kesesuaian isi dan judul artikel',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'ABSTRAK/ABSTRACT: Latar belakang, Tujuan, Metode, Hasil, Kesimpulan, Kata kunci',
            'bobot' => 10,
        ],
        [
            'kriteria' => 'PENDAHULUAN: Persoalan yang mendasari pelaksanaan dan uraian dasar keilmuan yang mendukung kemutakhiran substansi kajian',
            'bobot' => 15,
        ],
        [
            'kriteria' => 'METODE: Kesesuaian dengan persoalan yang telah diselasaikan, Pengembangan metode baru, Penggunaan metode yang sudah ada',
            'bobot' => 25,
        ],
        [
            'kriteria' => 'HASIL DAN PEMBAHASAN: Kumpulan dan kejelasan penampilan data, Proses/teknik pengolahan data, Ketajaman analisis dan sintesis data, Perbandingan hasil dengan hipotesis atau hasil sejenis sebelumnya',
            'bobot' => 30,
        ],
        [
            'kriteria' => 'KESIMPULAN: Tingkat ketersesuaian hasil dengan tujuan',
            'bobot' => 10,
        ],
        [
            'kriteria' => 'DAFTAR PUSTAKA: Ditulis dengan sistem Harvard (nama, tahun). Sesuai dengan uraian sitasi, Kemutakhiran Pustaka',
            'bobot' => 5,
        ],
    ],

    // ============================================
    // PKM-GFT (Gagasan Futuristik Tertulis)
    // Sesuai dengan Formulir Penilaian Proposal
    // ============================================
    'GFT' => [
        [
            'kriteria' => 'Format Makalah',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Tata tulis (ukuran kertas, tipografi, kerapihan ketik, tata letak, jumlah halaman)',
                    'bobot' => 3.33,
                ],
                [
                    'kriteria' => 'Penggunaan Bahasa Indonesia yang baik dan benar',
                    'bobot' => 3.33,
                ],
                [
                    'kriteria' => 'Kesesuaian dengan format penulisan yang tercantum di Panduan',
                    'bobot' => 3.34,
                ],
            ],
        ],
        [
            'kriteria' => 'Gagasan',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kreativitas gagasan (visioner/futuristik, unik, manfaat dan dampak sistemik)',
                    'bobot' => 11.67,
                ],
                [
                    'kriteria' => 'Kelayakan realisasi gagasan',
                    'bobot' => 11.67,
                ],
                [
                    'kriteria' => 'Ruang lingkup/skala permasalahan yang ditangani',
                    'bobot' => 11.66,
                ],
            ],
        ],
        [
            'kriteria' => 'Tahapan solusi yang ditawarkan dan prediksi keberhasilan',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Ketepatan solusi',
                    'bobot' => 7.5,
                ],
                [
                    'kriteria' => 'Pemanfaatan iptek',
                    'bobot' => 7.5,
                ],
                [
                    'kriteria' => 'Keterlibatan pihak terkait',
                    'bobot' => 7.5,
                ],
                [
                    'kriteria' => 'Jangka waktu realisasi gagasan',
                    'bobot' => 7.5,
                ],
            ],
        ],
        [
            'kriteria' => 'Sumber informasi',
            'bobot' => 0, // Total bobot dari sub-kriteria
            'sub_kriteria' => [
                [
                    'kriteria' => 'Kesesuaian sumber informasi dengan gagasan yang ditawarkan',
                    'bobot' => 7.5,
                ],
                [
                    'kriteria' => 'Akurasi dan kemutakhiran sumber informasi',
                    'bobot' => 7.5,
                ],
            ],
        ],
        [
            'kriteria' => 'Kesimpulan: Prediksi dampak terealisasikannya gagasan',
            'bobot' => 10,
        ],
    ],

    // ============================================
    // Default (Fallback)
    // ============================================
    'default' => [
        [
            'kriteria' => 'Kreativitas',
            'bobot' => 30,
        ],
        [
            'kriteria' => 'Metodologi',
            'bobot' => 25,
        ],
        [
            'kriteria' => 'Potensi Program',
            'bobot' => 30,
        ],
        [
            'kriteria' => 'Penjadwalan',
            'bobot' => 5,
        ],
        [
            'kriteria' => 'Anggaran',
            'bobot' => 10,
        ],
    ],
];

