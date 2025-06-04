<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a paginated list of reports.
     */
    public function index(Request $request)
    {
        // 1. Start a query builder for Report
        $query = Report::with('reporter', 'reportable');

        // 2. Optionally allow filtering by status or type via query string
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // 3. Order by newest first and paginate (20 per page)
        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        // 4. Pass the paginated collection to the view
        return view('admin.reports.index', compact('reports'));
    }


    /**
     * Display a single report’s details.
     */
    public function show(Report $report)
    {
        // Eager‐load the reporter and the reported item
        $report->load(['reporter', 'reportable']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Mark a report as resolved.
     */
    public function resolve(Request $request, Report $report)
    {
        // Determine the new status (default to 'resolved')
        $newStatus = $request->input('status', 'resolved');

        // Ensure the status is one of the allowed values
        if (! in_array($newStatus, ['in_review', 'resolved'])) {
            return redirect()
                ->route('admin.reports.show', $report)
                ->with('error', 'Invalid status provided.');
        }

        // Update and save
        $report->status = $newStatus;
        $report->save();

        // Redirect back to the report detail with a success message
        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Report status updated to "' . str_replace('_', ' ', $newStatus) . '".');
    }
}
