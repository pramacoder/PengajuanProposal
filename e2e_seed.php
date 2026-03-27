<?php
/**
 * Full E2E Seed — semua data yang dibutuhkan untuk testing alur lengkap PKM
 * Status flow: submitted → valid → review_administratif → review_substantif →
 *              revisi → validasi_2_valid → review_substantif_seleksi →
 *              pimpinan_pt → revisi_akhir → validasi_akhir_dosen_univ →
 *              pimpinan_pt → lolos/tidak_lolos
 */
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\RuangKontrol;
use App\Models\FormPenilaian;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== Seeding Full E2E Data ===\n\n";

// ============================================================
// 1. USERS
// ============================================================
$userData = [
    ['role'=>'operator',    'name'=>'Budi Operator',       'email'=>'operator@test.com',   'identifier'=>'OP001'],
    ['role'=>'mahasiswa',   'name'=>'Andi Mahasiswa',       'email'=>'mahasiswa@test.com',  'identifier'=>'2500000001'],
    ['role'=>'dosen',       'name'=>'Prof. Dosen Wibowo',   'email'=>'dosen@test.com',      'identifier'=>'DSN001'],
    ['role'=>'reviewer',    'name'=>'Dr. Reviewer Satu',    'email'=>'reviewer1@test.com',  'identifier'=>'RV001'],
    ['role'=>'reviewer',    'name'=>'Dr. Reviewer Dua',     'email'=>'reviewer2@test.com',  'identifier'=>'RV002'],
    ['role'=>'reviewer',    'name'=>'Dr. Reviewer Tiga',    'email'=>'reviewer3@test.com',  'identifier'=>'RV003'],
    ['role'=>'reviewer',    'name'=>'Dr. Reviewer Empat',   'email'=>'reviewer4@test.com',  'identifier'=>'RV004'],
    ['role'=>'pimpinan_pt', 'name'=>'Dr. Pimpinan PT',      'email'=>'pimpinan@test.com',   'identifier'=>'PP001'],
];

$users = [];
foreach ($userData as $d) {
    $u = User::updateOrCreate(['email' => $d['email']], [
        'name'       => $d['name'],
        'email'      => $d['email'],
        'identifier' => $d['identifier'],
        'role'       => $d['role'],
        'password'   => Hash::make('password'),
        'phone'      => '08123456789',
        'is_active'  => true,
        'metadata'   => ($d['role'] === 'mahasiswa') ? [
            'prodi_id'    => 1,
            'prodi_name'  => 'Teknik Informatika',
            'fakultas_id' => 1,
            'fakultas_name'=> 'Fakultas Teknik',
        ] : [],
    ]);
    $users[$d['role'] . '_' . ($d['identifier'])] = $u;
    echo "  [USER] {$d['role']}: {$d['email']}\n";
}

$operator  = $users['operator_OP001'];
$mahasiswa = $users['mahasiswa_2500000001'];
$dosen     = $users['dosen_DSN001'];
$reviewer1 = $users['reviewer_RV001'];
$reviewer2 = $users['reviewer_RV002'];
$reviewer3 = $users['reviewer_RV003'];
$reviewer4 = $users['reviewer_RV004'];
$pimpinan  = $users['pimpinan_pt_PP001'];

// ============================================================
// 2. RUANG KONTROL — buka semua fase untuk testing (operator bisa switch manual)
// ============================================================
$rk = RuangKontrol::updateOrCreate(['tahun_ajaran' => '2025/2026'], [
    'tahun_ajaran'                   => '2025/2026',
    'is_active'                      => true,
    'status_pendaftaran'             => 'terbuka',
    'status_review'                  => 'tertutup',
    'status_perbaikan'               => 'tertutup',
    'status_penilaian_akhir'         => 'tertutup',
    'tanggal_pendaftaran_mulai'      => now()->subDay(),
    'tanggal_pendaftaran_selesai'    => now()->addMonths(2),
    'tanggal_review_mulai'           => now()->addMonths(2),
    'tanggal_review_selesai'         => now()->addMonths(3),
    'tanggal_perbaikan_mulai'        => now()->addMonths(3),
    'tanggal_perbaikan_selesai'      => now()->addMonths(4),
    'tanggal_penilaian_akhir_mulai'  => now()->addMonths(4),
    'tanggal_penilaian_akhir_selesai'=> now()->addMonths(5),
    'tanggal_review_pertama_mulai'   => null,
    'tanggal_review_pertama_selesai' => null,
    'nama_history'                   => 'Testing Alur Lengkap 2025/2026',
    'dana_min_operator'              => 5000000,
    'dana_max_operator'              => 30000000,
    'dana_min_belmawa'               => 5000000,
    'dana_max_belmawa'               => 70000000,
    'id_pt'                          => $operator->id,
]);
echo "\n  [RUANG KONTROL] 2025/2026 — Phase: pendaftaran=TERBUKA\n";

// ============================================================
// 3. FORM PENILAIAN — Administratif & Substantif
// ============================================================
$formAdmin = FormPenilaian::updateOrCreate(
    ['nama_form' => 'Form Review Administratif'],
    [
        'nama_form'   => 'Form Review Administratif',
        'jenis_form'  => 'administratif',
        'skim'        => null,
        'is_active'   => true,
        'tahun_ajaran'=> '2025/2026',
        'created_by'  => $operator->id,
        'config'      => [
            'criteria' => [
                ['label' => 'Kelengkapan Dokumen', 'type' => 'integer_scale', 'required' => true],
                ['label' => 'Format Penulisan',    'type' => 'integer_scale', 'required' => true],
                ['label' => 'Catatan Reviewer',    'type' => 'textarea',      'required' => false],
            ]
        ],
    ]
);
echo "  [FORM] Administratif: #{$formAdmin->id}\n";

$formSubstantif = FormPenilaian::updateOrCreate(
    ['nama_form' => 'Form Review Substantif'],
    [
        'nama_form'   => 'Form Review Substantif',
        'jenis_form'  => 'substantif',
        'skim'        => null,
        'is_active'   => true,
        'tahun_ajaran'=> '2025/2026',
        'created_by'  => $operator->id,
        'config'      => [
            'criteria' => [
                ['label' => 'Originalitas Ide', 'type' => 'integer_scale', 'required' => true],
                ['label' => 'Metodologi',       'type' => 'integer_scale', 'required' => true],
                ['label' => 'Kelayakan Biaya',  'type' => 'integer_scale', 'required' => true],
                ['label' => 'Komentar',         'type' => 'textarea',      'required' => false],
            ]
        ],
    ]
);
echo "  [FORM] Substantif: #{$formSubstantif->id}\n";

// ============================================================
// 4. DATA PENDUKUNG (Fakultas & Prodi untuk form mahasiswa)
// ============================================================
if (DB::table('fakultas')->count() === 0) {
    $fakId = DB::table('fakultas')->insertGetId([
        'nama_fakultas' => 'Fakultas Teknik',
        'kode_fakultas' => 'FT',
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
    DB::table('prodis')->insert([
        'nama_prodi'   => 'Teknik Informatika',
        'kode_prodi'   => 'TI',
        'id_fakultas'  => $fakId,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);
    echo "  [DB] Fakultas & Prodi ditambahkan\n";
}

// ============================================================
// SUMMARY
// ============================================================
echo "\n=== SEED SELESAI ===\n";
echo "\nAkun Testing:\n";
echo "  operator@test.com       / password  (Operator)\n";
echo "  mahasiswa@test.com      / password  (Mahasiswa)\n";
echo "  dosen@test.com          / password  (Dosen Pendamping)\n";
echo "  reviewer1@test.com      / password  (Reviewer Administratif)\n";
echo "  reviewer2@test.com      / password  (Reviewer Substantif 1)\n";
echo "  reviewer3@test.com      / password  (Reviewer Substantif 2)\n";
echo "  reviewer4@test.com      / password  (Reviewer Seleksi)\n";
echo "  pimpinan@test.com       / password  (Pimpinan PT)\n";
echo "\nAlur yang akan ditest:\n";
echo "  submitted → valid → review_adm → review_sub → revisi\n";
echo "  → validasi_2 → review_sub_seleksi → pimpinan_pt\n";
echo "  → revisi_akhir → validasi_akhir_dosen → pimpinan_pt → lolos/tidak\n";
