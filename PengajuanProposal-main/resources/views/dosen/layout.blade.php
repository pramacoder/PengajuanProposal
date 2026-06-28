@extends('mainlayout.app')

@section('title', 'Dashboard Dosen - Sistem Proposal PKM')

@section('styles')
<style>
    .stat-card-icon.orange { background: rgba(245,158,11,0.1); color: #D97706; }
    .stat-card-icon.red    { background: rgba(220,38,38,0.1);  color: #DC2626; }
</style>
@yield('dosen_styles')
@endsection

@section('content')
<div class="container-fluid">
    @yield('dosen_content')
</div>
@endsection

@section('scripts')
@yield('dosen_scripts')
@endsection
