{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Admin Dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <p class="mb-6">
        Welcome, <strong>{{ auth()->user()->name ?: auth()->user()->username }}</strong>! You’re viewing the admin dashboard.
    </p>

    {{-- Grid of summary cards (you can style these with Tailwind if you wish) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total Users --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="text-sm font-semibold text-blue-700">Total Users</div>
            <div class="mt-2 text-2xl font-bold text-blue-900">{{ number_format($totalUsers) }}</div>
        </div>

        {{-- Active Users --}}
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="text-sm font-semibold text-green-700">Active Users</div>
            <div class="mt-2 text-2xl font-bold text-green-900">{{ number_format($activeUsers) }}</div>
        </div>

        {{-- Total Tracks --}}
        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
            <div class="text-sm font-semibold text-purple-700">Total Tracks</div>
            <div class="mt-2 text-2xl font-bold text-purple-900">{{ number_format($totalTracks) }}</div>
        </div>

        {{-- Total Reports --}}
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="text-sm font-semibold text-red-700">Total Reports</div>
            <div class="mt-2 text-2xl font-bold text-red-900">{{ number_format($totalReports) }}</div>
        </div>

        {{-- Reports by Type --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded col-span-1 sm:col-span-2 lg:col-span-3">
            <div class="text-sm font-semibold text-yellow-700 mb-2">Reports by Type</div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($reportCountsByType as $type => $count)
                <div class="bg-yellow-100 p-2 rounded">
                    <div class="text-xs text-yellow-800 uppercase">{{ $type }}</div>
                    <div class="mt-1 text-xl font-bold text-yellow-900">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection