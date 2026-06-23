<?php
/**
 * Script untuk membuat proposal test secara langsung ke database
 * dan melanjutkan alur blackbox testing tanpa browser
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Proposal;
use App\Models\RuangKontrol;
use Illuminate\Support\Facades\DB;

// Verifikasi user mahasiswa ada
$mahasiswa = User::where('email', 'mahasiswa-test@test.com')->first();
if (!$mahasiswa) {
    die("ERROR: Mahasiswa tidak ditemukan!\n");
}
echo "Mahasiswa: {$mahasiswa->name} (ID: {$mahasiswa->id})\n";

// Verifikasi dosen ada
$dosen = User::where('email', 'dosen-test@test.com')->first();
if (!$dosen) {
    die("ERROR: Dosen tidak ditemukan!\n");
}
echo "Dosen: {$dosen->name} (ID: {$dosen->id})\n";

// Verifikasi ruang kontrol aktif
$ruangKontrol = RuangKontrol::where('is_active', true)->first();
if (!$ruangKontrol) {
    die("ERROR: Ruang Kontrol aktif tidak ditemukan!\n");
}
echo "Ruang Kontrol: {$ruangKontrol->tahun_ajaran} (Status Pendaftaran: {$ruangKontrol->status_pendaftaran})\n";

// Cek apakah sudah ada proposal
$existing = Proposal::where('id_mahasiswa', $mahasiswa->id)->first();
if ($existing) {
    echo "Proposal sudah ada: ID {$existing->id_proposal}, Status: {$existing->status}\n";
    echo "Menggunakan proposal yang sudah ada...\n";
} else {
    // Buat proposal baru
    try {
        DB::beginTransaction();

        $proposal = Proposal::create([
            'judul_proposal' => 'Pengembangan Sistem IoT untuk Monitoring Lingkungan Kampus',
            'judul'          => 'Pengembangan Sistem IoT untuk Monitoring Lingkungan Kampus',
            'tanggal_pengajuan' => now(),
            'skim'           => 'KC',
            'dosen_pembimbing' => $dosen->name,
            'dana_diajukan'  => 10000000,
            'dana_diajukan_operator' => 5000000,
            'dana_diajukan_belmawa' => 5000000,
            'tahun_ajaran'   => $ruangKontrol->tahun_ajaran,
            'status_validasi' => 'pending',
            'status_final'   => 'submitted',
            'status'         => 'submitted',
            'id_mahasiswa'   => $mahasiswa->id,
            'id_dosen'       => $dosen->id,
            'id_dosen_pendamping_universitas' => $dosen->id,

            // Data Ketua Tim
            'ketua_nama'     => 'Andi Mahasiswa Test',
            'ketua_nim'      => '2500000099',
            'ketua_prodi'    => 'Teknik Informatika',
            'ketua_fakultas' => 'Fakultas Teknik',
            'ketua_email'    => 'mahasiswa-test@test.com',
            'ketua_no_hp'    => '081234567890',

            // Data Anggota 1 (wajib)
            'anggota1_nama'  => 'Budi Anggota Satu',
            'anggota1_nim'   => '2500000001',
            'anggota1_prodi' => 'Teknik Informatika',
            'anggota1_fakultas' => 'Fakultas Teknik',
            'anggota1_email' => 'anggota1@test.com',
            'anggota1_no_hp' => '081234567891',

            // Data Anggota 2 (wajib)
            'anggota2_nama'  => 'Citra Anggota Dua',
            'anggota2_nim'   => '2500000002',
            'anggota2_prodi' => 'Teknik Informatika',
            'anggota2_fakultas' => 'Fakultas Teknik',
            'anggota2_email' => 'anggota2@test.com',
            'anggota2_no_hp' => '081234567892',
        ]);

        DB::commit();
        echo "\n[SUKSES] Proposal berhasil dibuat!\n";
        echo "  ID Proposal : {$proposal->id_proposal}\n";
        echo "  Judul       : {$proposal->judul}\n";
        echo "  Status      : {$proposal->status}\n";
        $existing = $proposal;
    } catch (Exception $e) {
        DB::rollBack();
        die("ERROR membuat proposal: " . $e->getMessage() . "\n");
    }
}

echo "\n=== Status Saat Ini ===\n";
$p = Proposal::find($existing->id_proposal ?? $existing->id_proposal);
echo "Proposal ID : {$p->id_proposal}\n";
echo "Judul       : {$p->judul}\n";
echo "Status      : {$p->status}\n";
echo "Status Valid: {$p->status_validasi}\n";
echo "Mahasiswa ID: {$p->id_mahasiswa}\n";
echo "Dosen ID    : {$p->id_dosen}\n";
