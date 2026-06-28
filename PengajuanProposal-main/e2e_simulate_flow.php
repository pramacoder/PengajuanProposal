<?php
/**
 * Script simulasi alur penuh PKM — field disesuaikan dengan fillable model
 */
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Proposal;
use App\Models\Dokumen;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use Illuminate\Support\Facades\DB;

$mahasiswa = User::where('email','mahasiswa@test.com')->firstOrFail();
$dosen     = User::where('email','dosen@test.com')->firstOrFail();
$operator  = User::where('email','operator@test.com')->firstOrFail();
$reviewer1 = User::where('email','reviewer1@test.com')->firstOrFail();
$reviewer2 = User::where('email','reviewer2@test.com')->firstOrFail();
$reviewer3 = User::where('email','reviewer3@test.com')->firstOrFail();
$reviewer4 = User::where('email','reviewer4@test.com')->firstOrFail();
$pimpinan  = User::where('email','pimpinan@test.com')->firstOrFail();

// Bersihkan data sebelumnya
Proposal::query()->delete();
echo "=== SIMULASI ALUR PENUH PROPOSAL PKM ===\n\n";

// ─── 1. submitted ──────────────────────────────────────────────────────────
echo "[1/12] Mahasiswa submit proposal → submitted...\n";
$proposal = Proposal::create([
    'id_mahasiswa'      => $mahasiswa->id,
    'id_dosen'          => $dosen->id,
    'judul_proposal'    => 'Pengembangan Sistem Monitoring Lingkungan Berbasis IoT',
    'skim'              => 'KC',
    'dana_diajukan'     => 15000000,
    'status'            => 'submitted',
    'status_validasi'   => 'pending',
    'tahun_ajaran'      => '2025/2026',
    'tanggal_pengajuan' => now(),
    'ketua_nama'        => 'Andi Mahasiswa',
    'ketua_nim'         => '2500000001',
    'ketua_prodi'       => 'Teknik Informatika',
    'ketua_fakultas'    => 'Fakultas Teknik',
    'ketua_email'       => 'mahasiswa@test.com',
    'ketua_no_hp'       => '08123456789',
]);
Dokumen::create([
    'id_proposal' => $proposal->id_proposal,
    'skim'        => 'KC',
    'path_file'   => 'proposals/proposal_iot.pdf',
    'tgl_upload'  => now(),
]);
echo "   → ID: {$proposal->id_proposal} | Status: submitted\n";

// ─── 2. Dosen validasi → review_administratif ──────────────────────────────
echo "[2/12] Dosen validasi → review_administratif...\n";
$proposal->update([
    'status_validasi'  => 'valid',
    'status'           => 'review_administratif',
    'catatan'          => 'Disetujui. Layak untuk direview.',
    'tanggal_validasi' => now(),
]);

// ─── 3. Operator assign reviewer admin ─────────────────────────────────────
echo "[3/12] Operator assign reviewer administratif...\n";
$proposal->update(['id_reviewer_administratif' => $reviewer1->id]);

// ─── 4. Review administratif → review_substantif ───────────────────────────
echo "[4/12] Reviewer admin → review_substantif...\n";
NilaiAdministratif::create([
    'id_proposal'          => $proposal->id_proposal,
    'id_reviewer'          => $reviewer1->id,
    'checklist'            => ['kelengkapan_dokumen' => true, 'format_penulisan' => true],
    'note_administratif'   => 'Dokumen lengkap dan format penulisan sangat baik.',
]);
$proposal->update(['status' => 'review_substantif']);

// ─── 5. Operator assign 2 reviewer substantif ──────────────────────────────
echo "[5/12] Operator assign reviewer substantif (2 orang)...\n";
$proposal->update([
    'id_reviewer_substantif_1' => $reviewer2->id,
    'id_reviewer_substantif_2' => $reviewer3->id,
]);

// ─── 6. 2x review substantif → revisi ──────────────────────────────────────
echo "[6/12] Review substantif × 2 → revisi...\n";
NilaiSubstantif::create([
    'id_proposal'     => $proposal->id_proposal,
    'id_reviewer'     => $reviewer2->id,
    'note_substantif' => 'Ide orisinal, metodologi perlu diperkuat.',
    'jenis_review'    => 'substantif',
    'total_nilai'     => 72,
    'nilai_akhir'     => 72,
]);
NilaiSubstantif::create([
    'id_proposal'     => $proposal->id_proposal,
    'id_reviewer'     => $reviewer3->id,
    'note_substantif' => 'Potensi baik, perbaiki detail metodologi.',
    'jenis_review'    => 'substantif',
    'total_nilai'     => 70,
    'nilai_akhir'     => 70,
]);
$proposal->update(['status' => 'revisi']);

// ─── 7. Mahasiswa revisi → validasi_2_valid ────────────────────────────────
echo "[7/12] Mahasiswa upload revisi → validasi_2_valid...\n";
$proposal->dokumen?->update([
    'path_file'  => 'proposals/proposal_iot_revised.pdf',
    'tgl_upload' => now(),
]);
$proposal->update(['status' => 'validasi_2_valid', 'status_validasi' => 'pending']);

// ─── 8. Dosen validasi ke-2 → review_substantif_seleksi ───────────────────
echo "[8/12] Dosen validasi ke-2 → review_substantif_seleksi...\n";
$proposal->update([
    'status_validasi_2' => 'valid',
    'catatan'           => 'Revisi diterima, lanjut seleksi.',
    'status'            => 'review_substantif_seleksi',
]);

// ─── 9. Reviewer seleksi + review → pimpinan_pt ───────────────────────────
echo "[9/12] Reviewer seleksi → pimpinan_pt...\n";
$proposal->update([
    'id_reviewer_substantif_seleksi_1' => $reviewer4->id,
]);
NilaiSubstantif::create([
    'id_proposal'     => $proposal->id_proposal,
    'id_reviewer'     => $reviewer4->id,
    'note_substantif' => 'Revisi sangat baik, layak didanai dan maju PIMNAS.',
    'jenis_review'    => 'seleksi',
    'total_nilai'     => 83,
    'nilai_akhir'     => 83,
]);
$proposal->update(['status' => 'pimpinan_pt']);

// ─── 10. Pimpinan PT semi final → revisi_akhir ────────────────────────────
echo "[10/12] Pimpinan PT → lolos semi final → revisi_akhir...\n";
HasilSemiFinal::create([
    'id_proposal'     => $proposal->id_proposal,
    'id_pt'           => $pimpinan->id,
    'status_final'    => 'lolos',
    'catatan_final'   => 'Lolos semi final. Lakukan revisi akhir.',
    'nilai'           => 78.5,
]);
$proposal->update(['status' => 'revisi_akhir']);

// ─── 11. Mahasiswa revisi akhir → validasi_akhir_dosen_univ ───────────────
echo "[11/12] Mahasiswa upload revisi akhir → validasi_akhir_dosen_univ...\n";
$proposal->dokumen?->update([
    'path_file'  => 'proposals/proposal_iot_final.pdf',
    'tgl_upload' => now(),
]);
$proposal->update(['status' => 'validasi_akhir_dosen_univ']);

// ─── 12. Pimpinan PT → lolos_pimnas_pendanaan ─────────────────────────────
echo "[12/12] Pimpinan PT → lolos_pimnas_pendanaan...\n";
$proposal->update(['status' => 'pimpinan_pt']);
HasilFinal::create([
    'id_proposal'          => $proposal->id_proposal,
    'id_pimpinan_pt'       => $pimpinan->id,
    'status_pimnas'        => 'lolos',
    'status_pendanaan'     => 'lolos',
    'dana_yang_didapatkan' => 15000000,
    'status_final'         => 'lolos',
    'catatan_final'        => 'LOLOS PIMNAS dan mendapat PENDANAAN penuh.',
]);
$proposal->update(['status' => 'lolos_pimnas_pendanaan']);

// ─── Ringkasan ──────────────────────────────────────────────────────────────
$p = Proposal::with(['dokumen','nilaiAdministratif','nilaiSubstantif','hasilFinal','hasilSemiFinal'])->find($proposal->id_proposal);
echo "\n=== RINGKASAN ===\n";
printf("%-25s: %s\n", 'ID',              $p->id_proposal);
printf("%-25s: %s\n", 'Judul',           $p->judul_proposal);
printf("%-25s: %s\n", 'Status Akhir',    $p->status);
printf("%-25s: %d record\n", 'Review Admin',    $p->nilaiAdministratif->count());
printf("%-25s: %d record\n", 'Review Sub',      $p->nilaiSubstantif->count());
printf("%-25s: %s\n", 'Hasil Semi Final', $p->hasilSemiFinal?->status_final ?? '-');
printf("%-25s: %s\n", 'Hasil Final',      $p->hasilFinal?->status_pimnas ?? '-');
echo "\n✅ Simulasi 12 state transition BERHASIL!\n";
echo "URL untuk testing browser: http://127.0.0.1:8000\n";
echo "Proposal ID: {$p->id_proposal}\n";
