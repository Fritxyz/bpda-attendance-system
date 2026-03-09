<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DTRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kunin ang kasalukuyang petsa sa Manila
        $todayInManila = now()->timezone('Asia/Manila')->toDateString();
        
        // Kunin ang records at i-compute ang stats para sa bawat isa
        $today_records = Attendance::with('employee')
            ->where('attendance_date', $todayInManila)
            ->get()
            ->map(function ($record) {
                // I-parse ang mga oras gamit ang Carbon
                $amIn = $record->am_in ? Carbon::parse($record->am_in) : null;
                $amOut = $record->am_out ? Carbon::parse($record->am_out) : null;
                $pmIn = $record->pm_in ? Carbon::parse($record->pm_in) : null;
                $pmOut = $record->pm_out ? Carbon::parse($record->pm_out) : null;

                $totalMinutes = 0;

                // AM Duration
                if ($amIn && $amOut) {
                    $totalMinutes += $amIn->diffInMinutes($amOut);
                }

                // PM Duration
                if ($pmIn && $pmOut) {
                    $totalMinutes += $pmIn->diffInMinutes($pmOut);
                }

                // --- COMPUTATION PARA SA H AND MINUTES ---
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                
                // Format: "8h 15m"
                $record->computed_total_hours = "{$hours}h {$minutes}m";

                // Status Logic (8 hours = 480 minutes)
                $requiredMinutes = 8 * 60;
                $diffMinutes = abs($totalMinutes - $requiredMinutes);
                $diffH = floor($diffMinutes / 60);
                $diffM = $diffMinutes % 60;
                
                // I-attach ang readable difference (e.g., "0h 45m")
                $record->diff_hours = "{$diffH}h {$diffM}m";
                
                if ($totalMinutes > $requiredMinutes) {
                    $record->attendance_status = 'OVERTIME';
                    $record->status_color = 'blue';
                } elseif ($totalMinutes < $requiredMinutes && $totalMinutes > 0) {
                    $record->attendance_status = 'UNDERTIME';
                    $record->status_color = 'rose';
                } else {
                    $record->attendance_status = 'REGULAR';
                    $record->status_color = 'emerald';
                    $record->diff_hours = "0h 0m";
                }

                return $record;
            });

        return view('admin.timekeeping.view-dtr', compact('today_records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
