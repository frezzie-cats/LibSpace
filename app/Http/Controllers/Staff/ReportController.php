<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Generates and displays aggregated reports for staff analysis, filtered by period.
     * This corresponds to the 'staff.reports.index' route.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Determine the reporting period based on request, defaulting to 'monthly'
        $period = $request->input('period', 'monthly');
        $endDate = Carbon::now()->endOfDay();
        $startDate = null;
        $reportPeriodText = '';

        switch ($period) {
            case 'weekly':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                $reportPeriodText = $startDate->format('M j') . ' - ' . $endDate->format('M j') . ' (Last 7 Days)';
                break;
            case 'quarterly':
                $startDate = Carbon::now()->subDays(90)->startOfDay();
                $reportPeriodText = $startDate->format('M j, Y') . ' - ' . $endDate->format('M j, Y') . ' (Last 90 Days)';
                break;
            case 'all':
                // No start date needed for 'All Time'
                $reportPeriodText = 'All Time';
                break;
            case 'monthly':
            default:
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                $reportPeriodText = $startDate->format('M j') . ' - ' . $endDate->format('M j') . ' (Last 30 Days)';
                break;
        }

        // --- 2. Facility Usage Report (Bookings) ---
        $usageQuery = Booking::select('facility_id', DB::raw('count(*) as total_bookings'))
            ->groupBy('facility_id')
            ->orderByDesc('total_bookings')
            ->limit(5)
            ->with('facility');
        
        // Apply date filter if a start date is set
        if ($startDate) {
            $usageQuery->where('booking_date', '>=', $startDate);
        }
        $usageReport = $usageQuery->get();
        
        // --- 3. Feedback Analysis (Base Query for reuse) ---
        $feedbackBaseQuery = Feedback::query();
        if ($startDate) {
            // Filter feedback based on creation date
            $feedbackBaseQuery->where('created_at', '>=', $startDate);
        }

        $feedbackAnalysis = [
            // Use clone to prevent modifying the base query state for subsequent counts
            'total_feedback' => (clone $feedbackBaseQuery)->count(),
            'average_rating' => round((clone $feedbackBaseQuery)->whereNotNull('rating')->avg('rating') ?? 0, 2),
            // Counts feedback items by their status (new, reviewed, resolved, ignored)
            'status_counts' => (clone $feedbackBaseQuery)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];
        
        // --- 4. Facility Performance with Average Ratings ---
        $ratingQuery = Feedback::select('subject', DB::raw('avg(rating) as average_rating'), DB::raw('count(*) as total_feedback'))
            ->whereNotNull('rating')
            ->groupBy('subject')
            ->orderByDesc('average_rating');
        
        // Apply the same date filter
        if ($startDate) {
            $ratingQuery->where('created_at', '>=', $startDate);
        }

        $facilityRatings = $ratingQuery->get();

        // Pass all aggregated data to the reporting view
        return view('staff.reports.index', [
            'usageReport' => $usageReport,
            'feedbackAnalysis' => $feedbackAnalysis,
            'facilityRatings' => $facilityRatings,
            'reportPeriod' => $reportPeriodText,
        ]);
    }
}