<?php
require_once '../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NilaiAdministratif;
use App\Models\Proposal;

echo "<h1>Debug Checklist Data</h1>";

// Get all administrative reviews
$reviews = NilaiAdministratif::with('proposal')->get();

echo "<h2>Total Reviews: " . $reviews->count() . "</h2>";

foreach ($reviews as $review) {
    echo "<hr>";
    echo "<h3>Review ID: " . $review->id . "</h3>";
    echo "<p><strong>Proposal ID:</strong> " . $review->id_proposal . "</p>";
    echo "<p><strong>Proposal Title:</strong> " . ($review->proposal ? $review->proposal->judul_proposal : 'N/A') . "</p>";
    
    echo "<h4>Raw Checklist Data:</h4>";
    echo "<pre>" . json_encode($review->checklist, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h4>Checklist Type:</h4>";
    echo "<p>" . gettype($review->checklist) . "</p>";
    
    if (is_array($review->checklist)) {
        echo "<h4>Checklist Analysis:</h4>";
        echo "<ul>";
        foreach ($review->checklist as $key => $value) {
            $status = $value ? "✅ TRUE" : "❌ FALSE";
            echo "<li><strong>$key:</strong> $value ($status)</li>";
        }
        echo "</ul>";
        
        // Count true values
        $trueCount = count(array_filter($review->checklist, function($value) {
            return $value === true;
        }));
        echo "<p><strong>True Values Count:</strong> $trueCount</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> Checklist is not an array!</p>";
    }
}

echo "<hr>";
echo "<h2>Test Processing Logic</h2>";

// Test the processing logic
foreach ($reviews as $review) {
    echo "<h3>Processing Review ID: " . $review->id . "</h3>";
    
    if ($review->checklist && is_array($review->checklist)) {
        $formattedChecklist = [];
        $criteriaNames = [
            'format_dokumen' => 'Format Dokumen',
            'kelengkapan_data' => 'Kelengkapan Data',
            'struktur_proposal' => 'Struktur Proposal',
            'penulisan' => 'Penulisan',
            'margin_dan_spasi' => 'Margin dan Spasi',
            'font_dan_ukuran' => 'Font dan Ukuran',
            'nomor_halaman' => 'Nomor Halaman',
            'daftar_pustaka' => 'Daftar Pustaka',
            'cover_proposal' => 'Cover Proposal',
            'lembar_pengesahan' => 'Lembar Pengesahan',
            'abstrak' => 'Abstrak',
            'kata_pengantar' => 'Kata Pengantar',
            'daftar_isi' => 'Daftar Isi',
            'daftar_gambar' => 'Daftar Gambar',
            'daftar_tabel' => 'Daftar Tabel',
            'lampiran' => 'Lampiran'
        ];
        
        foreach ($review->checklist as $key => $value) {
            if ($value === true) {
                $displayName = $criteriaNames[$key] ?? str_replace('_', ' ', ucwords($key));
                $formattedChecklist[$displayName] = true;
            }
        }
        
        echo "<h4>Formatted Checklist:</h4>";
        echo "<pre>" . json_encode($formattedChecklist, JSON_PRETTY_PRINT) . "</pre>";
        echo "<p><strong>Formatted Items Count:</strong> " . count($formattedChecklist) . "</p>";
    } else {
        echo "<p style='color: red;'><strong>ERROR:</strong> Cannot process checklist!</p>";
    }
}
?>
