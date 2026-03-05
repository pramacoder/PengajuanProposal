@props([
    'status' => null,
    'label' => null,
    'class' => '',
])

@php
    $statusKey = (string) ($status ?? '');
    $labelMap = [
        'pending'                            => 'Menunggu',
        'valid'                              => 'Valid',
        'tidak_valid'                        => 'Tidak Valid',
        'submitted'                          => 'Telah Diajukan',
        'review_administratif'               => 'Review Administratif',
        'review_substantif'                  => 'Review Substantif',
        'revisi'                             => 'Revisi',
        'review_substantif_seleksi'          => 'Review Seleksi',
        'hasil_semi_final'                   => 'Semi Final',
        'revisi_akhir'                       => 'Revisi Akhir',
        'validasi_akhir_dosen_univ'          => 'Validasi Akhir',
        'pimpinan_pt'                        => 'Penilaian Pimpinan',
        'lolos_tingkat_universitas'          => 'Lolos Universitas',
        'tidak_lolos_tingkat_universitas'    => 'Tidak Lolos Univ.',
        'lolos_pimnas_pendanaan'             => 'Lolos PIMNAS + Dana',
        'lolos_pimnas_tidak_pendanaan'       => 'Lolos PIMNAS',
        'tidak_lolos_pimnas_lolos_pendanaan' => 'PIMNAS – Dana Lolos',
        'lolos'                              => 'Lolos',
        'tidak_lolos'                        => 'Tidak Lolos',
        'terbuka'                            => 'Terbuka',
        'tertutup'                           => 'Tertutup',
        'diterima'                           => 'Diterima',
        'ditolak'                            => 'Ditolak',
        'draft'                              => 'Draft',
        'finalized'                          => 'Finalized',
        'review_completed'                   => 'Review Selesai',
        'validated'                          => 'Tervalidasi',
    ];

    /* Map status → custom badge class */
    $classMap = [
        'pending'                            => 'badge-status-review',
        'valid'                              => 'badge-status-final',
        'tidak_valid'                        => 'badge-status-rejected',
        'draft'                              => 'badge-status-draft',
        'submitted'                          => 'badge-status-submitted',
        'validated'                          => 'badge-status-validated',
        'review_administratif'               => 'badge-status-review',
        'review_substantif'                  => 'badge-status-review',
        'review_substantif_seleksi'          => 'badge-status-review',
        'review_completed'                   => 'badge-status-review',
        'revisi'                             => 'badge-status-revisi',
        'revisi_akhir'                       => 'badge-status-revisi',
        'hasil_semi_final'                   => 'badge-status-submitted',
        'validasi_akhir_dosen_univ'          => 'badge-status-submitted',
        'pimpinan_pt'                        => 'badge-status-submitted',
        'lolos_tingkat_universitas'          => 'badge-status-final',
        'tidak_lolos_tingkat_universitas'    => 'badge-status-rejected',
        'lolos_pimnas_pendanaan'             => 'badge-status-final',
        'lolos_pimnas_tidak_pendanaan'       => 'badge-status-revisi',
        'tidak_lolos_pimnas_lolos_pendanaan' => 'badge-status-submitted',
        'lolos'                              => 'badge-status-final',
        'tidak_lolos'                        => 'badge-status-rejected',
        'terbuka'                            => 'badge-status-final',
        'tertutup'                           => 'badge-status-draft',
        'diterima'                           => 'badge-status-final',
        'ditolak'                            => 'badge-status-rejected',
        'finalized'                          => 'badge-status-final',
    ];

    $resolvedLabel = $label ?? ($labelMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey)));
    $resolvedClass = $classMap[$statusKey] ?? 'badge-status-draft';
@endphp

<span {{ $attributes->merge(['class' => "badge {$resolvedClass} {$class}"]) }}>
    {{ $resolvedLabel }}
</span>
