<?php
$content = file_get_contents('README.md');

// 1. Update intro descriptions
$content = preg_replace('/\*\*Sistem menggunakan arsitektur Polyglot Persistence\*\*.*?\n/s', "**Sistem menggunakan Relational Database Architecture** berbasis MySQL/PostgreSQL dengan implementasi form request terpusat untuk validasi data, serta file storage dengan mekanisme *smart fallback* antara Cloud Storage dan Local Storage.\n", $content);

$content = str_replace('**Database:** Supabase PostgreSQL + Firebase Firestore (Polyglot Persistence)', '**Database:** Supabase PostgreSQL / MySQL', $content);
$content = str_replace('**File Storage:** Supabase Storage (S3-compatible)', '**File Storage:** Supabase Storage (S3-compatible) & Local Storage Fallback', $content);

// 2. Update Daftar Isi
$content = preg_replace('/5\. \[Polyglot Persistence \(MongoDB \+ MySQL\)\]\(#polyglot-persistence-mongodb--mysql\)\n/', '', $content);

// 3. Update Teknologi yang Digunakan
$tech_replacement = "- **Backend:** Laravel 12, PHP 8.3+\n- **Frontend:** Blade Templates, Vanilla CSS (Crimson & Slate Theme), Vanilla JS\n- **Database:** Supabase PostgreSQL / MySQL (Relational)\n- **Authentication:** Unified single-guard (`web`) with role-based middleware\n- **File Storage:** Supabase Storage dengan Fallback otomatis ke Public Local Disk via `StorageHelper`\n- **Form Validation:** Laravel FormRequests untuk data integrity";

$content = preg_replace('/- \*\*Backend:\*\*.*?- \*\*Real-time:\*\*.*?\n/s', $tech_replacement . "\n", $content);

// 4. Remove Polyglot Persistence sections completely
// First section around line 838
$content = preg_replace('/## 🔄 Polyglot Persistence \(MongoDB \+ MySQL\).*?### Benefits of Polyglot Persistence.*?(?=---)/s', '', $content);

// Second section around line 2034
$content = preg_replace('/## 💾 Polyglot Persistence \(MongoDB \+ MySQL\).*?(?=---)/s', '', $content);

// Remove residual mentions of MongoDB and Firebase
$content = str_replace('MongoDB reference', 'Database reference', $content);
$content = str_replace('MongoDB', 'Database', $content);
$content = str_replace('Firebase Firestore', 'Database Relasional', $content);
$content = str_replace('Polyglot', 'Relational', $content);

file_put_contents('README.md', $content);
echo "README updated successfully.";
