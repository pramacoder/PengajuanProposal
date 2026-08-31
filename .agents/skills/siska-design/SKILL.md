---
name: siska-design
description: Panduan desain UI/UX untuk Sistem Informasi Akademik (SISKA) menggunakan Tailwind CSS dan komponen React dengan tema Navy Blue.
---

# SISKA Design System

Skill ini memberikan panduan desain komponen visual untuk Sistem Informasi Akademik (SISKA). Warna dominan yang digunakan adalah Navy Blue dengan status semantik standar.

Setiap kali Anda diminta untuk membuat atau memperbaiki antarmuka pengguna (UI) untuk SISKA, ikuti panduan dan gunakan referensi kode di bawah ini.

## CSS & Tailwind Configuration

Sistem ini menggunakan Tailwind CSS dengan kustomisasi tema warna Navy Blue.

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
@import 'tailwindcss';

@theme {
  --font-sans: 'Inter', system-ui, sans-serif;

  --color-navy-950: #0a0f1e;
  --color-navy-900: #0d1b3e;
  --color-navy-800: #1a2f5a;
  --color-navy-700: #1e3a6e;
  --color-navy-600: #2348a0;
  --color-navy-500: #2b59c3;
  --color-navy-400: #4a74d4;
  --color-navy-300: #7599e0;
  --color-navy-200: #afc3ef;
  --color-navy-100: #dce6f9;
  --color-navy-50: #f0f4fd;

  --color-status-green: #16a34a;
  --color-status-green-bg: #dcfce7;
  --color-status-green-text: #14532d;
  --color-status-red: #dc2626;
  --color-status-red-bg: #fee2e2;
  --color-status-red-text: #7f1d1d;
  --color-status-yellow: #ca8a04;
  --color-status-yellow-bg: #fef9c3;
  --color-status-yellow-text: #713f12;
}

html, body, #root {
  height: 100%;
  font-family: 'Inter', system-ui, sans-serif;
  -webkit-font-smoothing: antialiased;
}

* {
  box-sizing: border-box;
}

::-webkit-scrollbar {
  width: 4px;
  height: 4px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
```

## Komponen React (Contoh Implementasi)

Gunakan struktur komponen di bawah ini sebagai referensi untuk layout halaman SISKA:

```tsx
import { useState } from "react";
import { Button, Badge, Input, Select, FileUpload, Card } from "../components/ui";
import StatCard from "../components/StatCard";
import { FileText, Users, CheckCircle, AlertCircle, Download, Plus, Trash2 } from "lucide-react";

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="space-y-4">
      <div className="flex items-center gap-3">
        <h3 className="text-sm font-700 text-slate-800 uppercase tracking-wider">{title}</h3>
        <div className="flex-1 h-px bg-slate-200" />
      </div>
      {children}
    </section>
  );
}

export default function DesignSystemPage() {
  const [inputVal, setInputVal] = useState("");
  const [selectVal, setSelectVal] = useState("");
  const [inputError, setInputError] = useState("");

  return (
    <div className="max-w-4xl mx-auto space-y-10 pb-12">
      {/* Header */}
      <div className="bg-[#0d1b3e] rounded-2xl p-8 text-white">
        <p className="text-xs font-600 text-blue-300 uppercase tracking-widest mb-2">SISKA Design System</p>
        <h1 className="text-3xl font-800 leading-tight">Komponen UI Akademik</h1>
        <p className="text-sm text-white/60 mt-2 max-w-lg">
          Panduan komponen visual untuk Sistem Informasi Akademik (SISKA). Warna dominan Navy Blue dengan status semantik standar.
        </p>

        {/* Color palette */}
        <div className="flex flex-wrap gap-2 mt-6">
          {[
            { label: "Navy 900", bg: "#0d1b3e" },
            { label: "Navy 700", bg: "#1e3a6e" },
            { label: "Navy 500", bg: "#2b59c3" },
            { label: "Navy 200", bg: "#afc3ef" },
            { label: "Navy 50", bg: "#f0f4fd" },
            { label: "Lolos", bg: "#16a34a" },
            { label: "Revisi", bg: "#ca8a04" },
            { label: "Ditolak", bg: "#dc2626" },
          ].map((c) => (
            <div key={c.label} className="flex items-center gap-1.5">
              <span
                className="w-5 h-5 rounded-md border border-white/20 flex-shrink-0"
                style={{ backgroundColor: c.bg }}
              />
              <span className="text-[10px] text-white/50">{c.label}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Buttons */}
      <Section title="Tombol Aksi">
        <Card className="p-6">
          <div className="space-y-4">
            <div>
              <p className="text-xs text-slate-500 mb-3 font-500">Variant</p>
              <div className="flex flex-wrap gap-3">
                <Button variant="primary">
                  <Plus size={14} /> Pengajuan Baru
                </Button>
                <Button variant="secondary">
                  <Download size={14} /> Unduh PDF
                </Button>
                <Button variant="danger">
                  <Trash2 size={14} /> Hapus Data
                </Button>
                <Button variant="ghost">Batal</Button>
                <Button variant="primary" disabled>Nonaktif</Button>
              </div>
            </div>
            <div>
              <p className="text-xs text-slate-500 mb-3 font-500">Ukuran</p>
              <div className="flex flex-wrap items-center gap-3">
                <Button size="sm">Kecil</Button>
                <Button size="md">Sedang</Button>
                <Button size="lg">Besar</Button>
              </div>
            </div>
          </div>
        </Card>
      </Section>

      {/* Badges */}
      <Section title="Badge Status">
        <Card className="p-6">
          <div className="flex flex-wrap gap-3 items-center">
            <Badge variant="valid">Lolos</Badge>
            <Badge variant="rejected">Ditolak</Badge>
            <Badge variant="revision">Revisi</Badge>
            <Badge variant="pending">Menunggu</Badge>
            <Badge variant="info">Informasi</Badge>
            <Badge variant="default">Draft</Badge>
          </div>
          <div className="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-4 text-sm">
            <div className="flex items-start gap-2">
              <Badge variant="valid">Lolos</Badge>
              <p className="text-xs text-slate-500">Dokumen telah diverifikasi dan disetujui</p>
            </div>
            <div className="flex items-start gap-2">
              <Badge variant="revision">Revisi</Badge>
              <p className="text-xs text-slate-500">Diperlukan perbaikan sebelum disetujui</p>
            </div>
            <div className="flex items-start gap-2">
              <Badge variant="rejected">Ditolak</Badge>
              <p className="text-xs text-slate-500">Tidak memenuhi persyaratan akademik</p>
            </div>
          </div>
        </Card>
      </Section>

      {/* Stat Cards */}
      <Section title="Kartu Statistik">
        <div className="grid grid-cols-2 gap-4">
          <StatCard title="Mahasiswa Aktif" value="1.284" subtitle="Semester genap 2023/2024" icon={<Users size={18} />} trend={5} accent="blue" />
          <StatCard title="Pengajuan Disetujui" value="342" subtitle="Dari 489 total pengajuan" icon={<CheckCircle size={18} />} trend={12} accent="green" />
          <StatCard title="Perlu Revisi" value="87" subtitle="Menunggu tindak lanjut" icon={<AlertCircle size={18} />} trend={-3} accent="yellow" />
          <StatCard title="Total Berkas" value="2.041" subtitle="Berkas terunggah tahun ini" icon={<FileText size={18} />} trend={0} accent="blue" />
        </div>
      </Section>

      {/* Form Components */}
      <Section title="Komponen Form">
        <Card className="p-6 space-y-5">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            <Input
              label="Nama Lengkap"
              placeholder="Masukkan nama lengkap"
              required
              value={inputVal}
              onChange={(e) => setInputVal(e.target.value)}
              hint="Sesuai KTP/identitas resmi"
            />
            <Input
              label="NIM"
              placeholder="Contoh: 220411001"
              required
            />
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            <Input
              label="Email Institusi"
              type="email"
              placeholder="nim@student.univ.ac.id"
              error={inputError}
              onBlur={(e) => {
                if (e.target.value && !e.target.value.includes("@")) {
                  setInputError("Format email tidak valid");
                } else {
                  setInputError("");
                }
              }}
            />
            <Select
              label="Program Studi"
              placeholder="Pilih program studi"
              required
              value={selectVal}
              onChange={(e) => setSelectVal(e.target.value)}
              options={[
                { value: "ti", label: "Teknik Informatika" },
                { value: "si", label: "Sistem Informasi" },
                { value: "ik", label: "Ilmu Komputer" },
                { value: "tk", label: "Teknik Komputer" },
              ]}
            />
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            <Select
              label="Jenis Pengajuan"
              placeholder="Pilih jenis pengajuan"
              options={[
                { value: "skripsi", label: "Skripsi" },
                { value: "pkl", label: "Praktek Kerja Lapangan" },
                { value: "kp", label: "Kerja Praktek" },
              ]}
            />
            <Select
              label="Semester"
              options={[
                { value: "1", label: "Semester 1" },
                { value: "2", label: "Semester 2" },
                { value: "3", label: "Semester 3" },
                { value: "4", label: "Semester 4" },
                { value: "5", label: "Semester 5" },
                { value: "6", label: "Semester 6" },
                { value: "7", label: "Semester 7" },
                { value: "8", label: "Semester 8" },
              ]}
            />
          </div>
          <Input
            label="Judul Pengajuan"
            placeholder="Masukkan judul skripsi / laporan PKL"
            required
            hint="Minimal 10 karakter, sesuai proposal yang telah disetujui"
          />
          <FileUpload
            label="Unggah Dokumen"
            accept=".pdf,.doc,.docx"
            hint="PDF, DOC, atau DOCX — Maks. 10MB"
          />
          <div className="flex justify-end gap-3 pt-2">
            <Button variant="ghost">Reset</Button>
            <Button variant="secondary">Simpan Draft</Button>
            <Button variant="primary" type="submit">Kirim Pengajuan</Button>
          </div>
        </Card>
      </Section>

      {/* Typography */}
      <Section title="Tipografi">
        <Card className="p-6 space-y-3">
          <p className="text-3xl font-800 text-slate-900">Heading Utama — Inter 800</p>
          <p className="text-xl font-700 text-slate-800">Sub-heading Halaman — Inter 700</p>
          <p className="text-base font-600 text-slate-700">Label Seksi — Inter 600</p>
          <p className="text-sm font-400 text-slate-600 leading-relaxed max-w-prose">
            Teks body standar untuk deskripsi, konten artikel, dan instruksi form. Font Inter dipilih karena
            keterbacaan tinggi di layar digital, cocok untuk antarmuka akademik yang membutuhkan
            kejelasan informasi dan kesan profesional.
          </p>
          <p className="text-xs font-500 text-slate-400 uppercase tracking-widest">Label Kecil — Inter 500</p>
          <code className="text-xs font-mono bg-slate-100 px-2 py-1 rounded text-slate-700">
            220411001 — Monospace untuk NIM, kode referensi
          </code>
        </Card>
      </Section>
    </div>
  );
}
```
