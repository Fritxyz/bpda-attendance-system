@extends('layouts.admin.top-and-side-bar')

@section('header', 'Dashboard')

@section('content')

 {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-1">
                    <i class="bi bi-house-door text-xs"></i>
                    Dashboard
                </a>
            </li>
        </ol>
    </nav>

<script src="{{ asset('js/admin/employee-filter.js') }}"></script>
@endsection