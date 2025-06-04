{{-- resources/views/admin/reports/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Report #' . $report->id)
@section('page-heading', 'Report Details')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-4">Report #{{ $report->id }}</h2>

    {{-- Reporter and Date --}}
    <div class="mb-4 space-y-1">
        <div>
            <p><strong>Reported By:</strong>
                {{ $report->reporter->username ?? 'Unknown' }}
                (ID: {{ $report->user_id ?? 'N/A' }})
            </p>
        </div>
        <div>
            <strong>When:</strong> {{ $report->created_at->toDayDateTimeString() }}
        </div>
    </div>

    {{-- Type & Reported Item --}}
    <div class="mb-4 space-y-1">
        <div>
            <strong>Type:</strong> {{ ucfirst($report->type) }}
        </div>
        <div>
            <strong>Item:</strong>
            @if ($report->reportable)
            @php $item = $report->reportable; @endphp

            @if ($report->type === 'track')
            <a href="{{ route('admin.tracks.show', $item->id) }}" class="text-blue-600 hover:underline">
                {{ $item->title ?? 'Untitled Track' }}
            </a>
            @elseif ($report->type === 'user')
            <a href="{{ route('admin.users.show', $item->id) }}" class="text-blue-600 hover:underline">
                {{ $item->username ?? $item->name ?? 'Unknown User' }}
            </a>
            @else
            {{ ucfirst($report->type) }}
            @endif
            @else
            <span class="text-gray-500 italic">Deleted</span>
            @endif
        </div>
    </div>

    {{-- Reason --}}
    <div class="mb-4">
        <strong>Reason:</strong>
        <p class="mt-1">{{ $report->reason ?? 'No reason provided.' }}</p>
    </div>

    {{-- Description --}}
    @if($report->description)
    <div class="mb-4">
        <strong>Description:</strong>
        <p class="mt-1 text-gray-800">{{ $report->description }}</p>
    </div>
    @endif

    {{-- Current Status --}}
    <div class="mb-6">
        <strong>Status:</strong>
        <span class="capitalize">{{ str_replace('_', ' ', $report->status) }}</span>
    </div>

    {{-- Action Buttons --}}
    <div class="space-x-4">
        @if ($report->type === 'user')
        <a href="{{ route('admin.user.conversations', [
        'reported' => $report->reportable_id,
        'reporter' => $report->user_id
    ]) }}" class="text-blue-600 hover:underline">
            View Chat Messages
        </a>
        @endif

        @if ($report->status !== 'resolved')
        <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="status" value="resolved">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Mark as Resolved
            </button>
        </form>
        @else
        <span class="text-gray-500 italic">This report is already resolved.</span>
        @endif

        <a href="{{ route('admin.reports.index') }}"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
            Back to Reports
        </a>
    </div>
</div>
@endsection