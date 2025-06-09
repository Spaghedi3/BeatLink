@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name ?: auth()->user()->username }}</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-blue-700 p-4 rounded shadow">
        <div class="text-sm font-medium">Total Users</div>
        <div class="text-3xl font-bold mt-2">{{ number_format($totalUsers) }}</div>
    </div>

    <div class="bg-green-700 p-4 rounded shadow">
        <div class="text-sm font-medium">Active Users</div>
        <div class="text-3xl font-bold mt-2">{{ number_format($activeUsers) }}</div>
    </div>

    <div class="bg-purple-700 p-4 rounded shadow">
        <div class="text-sm font-medium">Total Tracks</div>
        <div class="text-3xl font-bold mt-2">{{ number_format($totalTracks) }}</div>
    </div>

    <div class="bg-red-700 p-4 rounded shadow">
        <div class="text-sm font-medium">Total Reports</div>
        <div class="text-3xl font-bold mt-2">{{ number_format($totalReports) }}</div>
    </div>

    <div class="bg-yellow-600 p-4 rounded shadow col-span-1 sm:col-span-2 lg:col-span-3">
        <div class="text-sm font-medium mb-4">Reports by Type</div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($reportCountsByType as $type => $count)
            <div class="bg-yellow-500 text-gray-900 p-2 rounded text-center">
                <div class="uppercase text-xs">{{ $type }}</div>
                <div class="text-xl font-bold">{{ $count }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection