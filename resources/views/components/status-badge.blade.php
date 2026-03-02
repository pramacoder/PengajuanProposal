@props([
    'status' => null,
    'label' => null,
    'class' => '',
])

@php
    $statusKey = (string) ($status ?? '');
    $labelMap = [
        'pending' => 'Menunggu',
        'valid' => 'Valid',
        'tidak_valid' => 'Tidak Valid',
        'submitted' => 'Telah Diajukan',
        'review_administratif' => 'Review Administratif',
        'review_substantif' => 'Review Substantif',
        'revisi' => 'Revisi',
        'review_substantif_seleksi' => 'Review Substantif Seleksi',
        'hasil_semi_final' => 'Hasil Semi Final',
        'revisi_akhir' => 'Revisi Akhir',
        'validasi_akhir_dosen_univ' => 'Validasi Akhir Dosen Universitas',
        'pimpinan_pt' => 'Penilaian Pimpinan PT',
        'lolos_tingkat_universitas' => 'Lolos Tingkat Universitas',
        'tidak_lolos_tingkat_universitas' => 'Tidak Lolos Tingkat Universitas',
        'lolos_pimnas_pendanaan' => 'Lolos PIMNAS + Pendanaan',
        'lolos_pimnas_tidak_pendanaan' => 'Lolos PIMNAS, Tidak Lolos Pendanaan',
        'tidak_lolos_pimnas_lolos_pendanaan' => 'Tidak Lolos PIMNAS, Lolos Pendanaan',
        'lolos' => 'Lolos',
        'tidak_lolos' => 'Tidak Lolos',
        'terbuka' => 'Terbuka',
        'tertutup' => 'Tertutup',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $colorMap = [
        'pending' => 'warning',
        'valid' => 'success',
        'tidak_valid' => 'danger',
        'submitted' => 'info',
        'review_administratif' => 'warning',
        'review_substantif' => 'info',
        'revisi' => 'warning',
        'review_substantif_seleksi' => 'warning',
        'hasil_semi_final' => 'primary',
        'revisi_akhir' => 'warning',
        'validasi_akhir_dosen_univ' => 'primary',
        'pimpinan_pt' => 'dark',
        'lolos_tingkat_universitas' => 'success',
        'tidak_lolos_tingkat_universitas' => 'danger',
        'lolos_pimnas_pendanaan' => 'success',
        'lolos_pimnas_tidak_pendanaan' => 'warning',
        'tidak_lolos_pimnas_lolos_pendanaan' => 'info',
        'lolos' => 'success',
        'tidak_lolos' => 'danger',
        'terbuka' => 'success',
        'tertutup' => 'secondary',
        'diterima' => 'success',
        'ditolak' => 'danger',
    ];
    $resolvedLabel = $label ?? ($labelMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey)));
    $resolvedColor = $colorMap[$statusKey] ?? 'secondary';
@endphp

<span {{ $attributes->merge(['class' => "badge bg-{$resolvedColor} {$class}"]) }}>
    {{ $resolvedLabel }}
</span>
