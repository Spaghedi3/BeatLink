{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Reports')
@section('page-heading', 'All Reports')

@section('content')
{{-- Filters bar --}}
<div class="mb-4 flex space-x-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex space-x-2">
        <select name="status" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2">
            <option value="">All Statuses</option>
            <option value="open">Open</option>
            <option value="in_review">In Review</option>
            <option value="resolved">Resolved</option>
        </select>

        <select name="type" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2 ml-2">
            <option value="">All Types</option>
            <option value="user">User</option>
            <option value="track">Track</option>
        </select>

        <button type="submit" class="ml-2 bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded">
            Filter
        </button>
    </form>
</div>

{{-- Reports table --}}
<div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full text-sm text-left text-white bg-gray-900">
        <thead class="bg-gray-700 text-white">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Item</th>
                <th class="px-4 py-2">Reporter</th>
                <th class="px-4 py-2">Reason</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr class="{{ $report->status === 'resolved' ? 'bg-gray-800 hover:bg-gray-700 text-gray-400 italic' : 'bg-gray-800 hover:bg-gray-700' }}">
                <td class="px-4 py-2">{{ $report->id }}</td>
                <td class="px-4 py-2">{{ ucfirst($report->type) }}</td>
                <td class="px-4 py-2">
                    @if ($report->reportable)
                    {{ $report->reportable->name ?? $report->reportable->title ?? 'N/A' }}
                    @else
                    <span class="italic text-gray-400">Deleted</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $report->reporter->username ?? 'Unknown' }}</td>
                <td class="px-4 py-2">{{ Str::limit($report->reason, 40) }}</td>
                <td class="px-4 py-2">{{ ucfirst($report->status) }}</td>
                <td class="px-4 py-2">{{ $report->created_at->diffForHumans() }}</td>
                <td class="px-4 py-2 space-x-2">
                    <a href="{{ route('admin.reports.show', $report) }}" class="text-blue-400 hover:underline">View</a>
                    @if($report->status !== 'resolved')
                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-400 hover:underline bg-transparent border-none p-0">
                            Resolve
                        </button>
                    </form>

                    @else
                    <span class="text-gray-400 italic">Resolved</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- Pagination controls --}}
    <div class="bg-gray-900 p-4 flex justify-center">
        {{ $reports->withQueryString()->links() }}
    </div>

</div>
@endsection