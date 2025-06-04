{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Reports')
@section('page-heading', 'All Reports')

@section('content')
{{-- Filters bar --}}
<div class="mb-4 flex space-x-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="flex space-x-2">
        <select name="status" class="border rounded px-2 py-1">
            <option value="">All Statuses</option>
            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>
        <select name="type" class="border rounded px-2 py-1">
            <option value="">All Types</option>
            <option value="track" {{ request('type') == 'track' ? 'selected' : '' }}>Track</option>
            <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
            <option value="app" {{ request('type') == 'app' ? 'selected' : '' }}>App</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white rounded px-3 py-1">Filter</button>
    </form>
</div>

{{-- Reports table --}}
<div class="bg-white shadow rounded-lg overflow-x-auto">
    <table class="min-w-full">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2">ID</th>
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
            @forelse($reports as $report)
            <tr class="{{ $report->status === 'open' ? 'bg-yellow-50' : '' }}">
                <td class="border-t px-4 py-2">{{ $report->id }}</td>
                <td class="border-t px-4 py-2 capitalize">{{ $report->type }}</td>
                <td class="border-t px-4 py-2">
                    @if($report->reportable)
                    @php $item = $report->reportable; @endphp

                    @if($report->type === 'track' && isset($item->title))
                    <a href="{{ route('admin.tracks.show', $item->id) }}" class="text-blue-600 hover:underline">
                        {{ Str::limit($item->title, 30) }}
                    </a>
                    @elseif($report->type === 'user' && isset($item->name))
                    <a href="{{ route('admin.users.show', $item->id) }}" class="text-blue-600 hover:underline">
                        {{ $item->name }}
                    </a>
                    @else
                    {{ ucfirst($report->type) }}
                    @endif
                    @else
                    <span class="text-gray-500 italic">Deleted</span>
                    @endif
                </td>

                <td class="border-t px-4 py-2">
                    {{ $report->reporter?->username ?? 'Unknown' }}
                </td>



                <td class="border-t px-4 py-2">{{ Str::limit($report->reason, 40) }}</td>
                <td class="border-t px-4 py-2 capitalize">{{ str_replace('_',' ',$report->status) }}</td>
                <td class="border-t px-4 py-2">{{ $report->created_at->diffForHumans() }}</td>
                <td class="border-t px-4 py-2">
                    <a href="{{ route('admin.reports.show', $report) }}"
                        class="text-indigo-600 hover:underline mr-2">
                        View
                    </a>
                    @if($report->status !== 'resolved')
                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline">
                            Resolve
                        </button>
                    </form>
                    @else
                    <span class="text-gray-500 italic">Resolved</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                    No reports found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination controls --}}
    <div class="p-4">
        {{ $reports->withQueryString()->links() }}
    </div>
</div>
@endsection