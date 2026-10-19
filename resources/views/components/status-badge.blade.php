@props([
    'status' => null,
    'label' => null,
    'class' => '',
    'showIcon' => true,
    'icon' => null,
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
        'validasi_2_valid'                   => 'Validasi 2 Selesai',
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
        'validasi_2_valid'                   => 'badge-status-validated',
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

    $iconMap = [
        'pending'                            => 'fa-clock',
        'valid'                              => 'fa-check-circle',
        'tidak_valid'                        => 'fa-times-circle',
        'draft'                              => 'fa-file-alt',
        'submitted'                          => 'fa-paper-plane',
        'validated'                          => 'fa-check-circle',
        'review_administratif'               => 'fa-clipboard-check',
        'review_substantif'                  => 'fa-search',
        'review_substantif_seleksi'          => 'fa-award',
        'review_completed'                   => 'fa-check-double',
        'revisi'                             => 'fa-edit',
        'validasi_2_valid'                   => 'fa-check-circle',
        'revisi_akhir'                       => 'fa-file-signature',
        'hasil_semi_final'                   => 'fa-list-ol',
        'validasi_akhir_dosen_univ'          => 'fa-user-check',
        'pimpinan_pt'                        => 'fa-stamp',
        'lolos_tingkat_universitas'          => 'fa-trophy',
        'tidak_lolos_tingkat_universitas'    => 'fa-times-circle',
        'lolos_pimnas_pendanaan'             => 'fa-medal',
        'lolos'                              => 'fa-check-circle',
        'tidak_lolos'                        => 'fa-times-circle',
        'terbuka'                            => 'fa-door-open',
        'tertutup'                           => 'fa-door-closed',
        'diterima'                           => 'fa-check-circle',
        'ditolak'                            => 'fa-times-circle',
    ];

    $resolvedLabel = $label ?? ($labelMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey)));
    $resolvedClass = $classMap[$statusKey] ?? 'badge-status-draft';
    $resolvedIcon = $icon ?? ($iconMap[$statusKey] ?? null);
@endphp

<span {{ $attributes->merge(['class' => "badge {$resolvedClass} {$class} d-inline-flex align-items-center gap-1"]) }}>
    @if($showIcon && $resolvedIcon)
        <i class="fas {{ $resolvedIcon }} fa-xs"></i>
    @endif
    <span>{{ $resolvedLabel }}</span>
</span>
