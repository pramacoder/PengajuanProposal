@extends('dosen.layout')

@section('page_title', 'Validasi Akhir - Dosen Pendamping Universitas')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard Dosen', 'url' => route('dosen.universitas.dashboard')],
            ['label' => 'Validasi Akhir', 'active' => true],
        ]" />

        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-check-double me-2"></i>
                    Validasi Akhir Proposal
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Proposal yang perlu divalidasi akhir setelah mahasiswa melakukan revisi akhir.
                </p>

                @if($proposals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Judul Proposal</th>
                                    <th>Skim</th>
                                    <th>Mahasiswa</th>
                                    <th>Status</th>
                                    <th>Hasil Semi Final</th>
                                    <th>Tanggal Revisi Akhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposals as $index => $proposal)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ Str::limit($proposal->judul, 50) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                        </td>
                                        <td>
                                            {{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}
                                            <br><small class="text-muted">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Perlu Validasi Akhir</span>
                                        </td>
                                        <td>
                                            @if($proposal->hasilSemiFinal)
                                                <span class="badge bg-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}">
                                                    {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos' : 'Tidak Lolos' }}
                                                </span>
                                                <br><small class="text-muted">Nilai: @formatId($proposal->hasilSemiFinal->nilai, 2)</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $revisiAkhir = $proposal->proposalRevisi->where('path_file', 'like', '%revisi_akhir%')->first();
                                            @endphp
                                            @if($revisiAkhir)
                                                <small>{{ \Carbon\Carbon::parse($revisiAkhir->tanggal_submit)->format('d M Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-check-double me-1"></i>Validasi Akhir
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-muted">Tidak Ada Proposal yang Perlu Divalidasi</h5>
                        <p class="text-muted">Semua proposal yang perlu divalidasi akhir sudah selesai.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

