<?php
$srcDir = __DIR__ . '/resources/views/operator';
$dstDir = __DIR__ . '/resources/views/pimpinan_pt';
$files = [
    'dashboard.blade.php',
    'ruang_kontrol.blade.php',
    'pilih_reviewer.blade.php',
    'hasil_semi_final.blade.php',
    'detail_hasil_semi_final.blade.php',
    'hasil_final.blade.php',
    'detail_hasil_final.blade.php',
    'pilih_reviewer_seleksi.blade.php',
    'proposal_detail.blade.php',
    'manajemen_akun.blade.php',
    'profile.blade.php'
];

foreach ($files as $file) {
    if (!file_exists("$srcDir/$file")) {
        echo "Skip $file\n";
        continue;
    }
    $content = file_get_contents("$srcDir/$file");
    
    // Convert routes and titles
    $content = str_replace("route('operator.", "route('pimpinan_pt.", $content);
    $content = str_replace("- Operator", "- Pimpinan PT", $content);
    $content = str_replace("Dashboard Operator PKM", "Dashboard Pimpinan PT", $content);
    $content = str_replace("Operator PKM", "Pimpinan PT", $content);

    // Disable inputs and forms specifically in Pimpinan PT
    // For ruang kontrol: remove the form submit button and active schedule editor block
    if ($file === 'ruang_kontrol.blade.php') {
        // Simple regex to disable inputs just in case, but let's hide the submit button
        $content = str_replace('<button type="button" class="btn btn-primary" id="btnSimpanJadwal"', '<button type="button" class="btn btn-primary d-none" id="btnSimpanJadwal"', $content);
        $content = str_replace('<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal"', '<button class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal"', $content);
        $content = preg_replace('/<input ([^>]+)>/', '<input $1 disabled>', $content);
        $content = preg_replace('/<select ([^>]+)>/', '<select $1 disabled>', $content);
        // Except for the year selector, we need it to work!
        $content = str_replace('id="tahunSelector" disabled', 'id="tahunSelector"', $content);
    }
    
    // For pilih_reviewer: remove the assignment select
    if ($file === 'pilih_reviewer.blade.php' || $file === 'pilih_reviewer_seleksi.blade.php') {
        $content = preg_replace('/<select.*?class="form-select reviewer-select".*?>.*?<\/select>/s', '<span class="badge bg-secondary">Mode Read-Only</span>', $content);
    }

    file_put_contents("$dstDir/$file", $content);
    echo "Copied $file\n";
}

// For form_penilaian and laporan_simbelmawa, the operator has folders.
// Let's copy their index files if they exist.
if (file_exists("$srcDir/form_penilaian/index.blade.php")) {
    $content = file_get_contents("$srcDir/form_penilaian/index.blade.php");
    $content = str_replace("route('operator.", "route('pimpinan_pt.", $content);
    $content = str_replace("- Operator", "- Pimpinan PT", $content);
    file_put_contents("$dstDir/form_penilaian.blade.php", $content);
    echo "Copied form_penilaian\n";
}

if (file_exists("$srcDir/laporan_simbelmawa/index.blade.php")) {
    $content = file_get_contents("$srcDir/laporan_simbelmawa/index.blade.php");
    $content = str_replace("route('operator.", "route('pimpinan_pt.", $content);
    $content = str_replace("- Operator", "- Pimpinan PT", $content);
    file_put_contents("$dstDir/laporan_simbelmawa.blade.php", $content);
    echo "Copied laporan_simbelmawa\n";
}
