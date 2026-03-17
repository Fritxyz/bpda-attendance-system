<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


class DashboardController extends Controller
{
    //
    // Sa iyong DashboardController.php
    public function index() {
        return view('admin.dashboard', [
            'totalEmployees' => 145,
            'presentToday' => 122,
            'lateToday' => 8,
            'onLeave' => 5,
            'pendingLeaves' => 3,
            // Chart Labels (Last 7 Days)
            'labels' => ['Mar 11', 'Mar 12', 'Mar 13', 'Mar 14', 'Mar 15', 'Mar 16', 'Today'],
            // Chart Data
            'attendanceData' => [110, 115, 108, 0, 0, 120, 122], // 0 for weekends
            'onTimeData' => [95, 100, 90, 0, 0, 105, 110]
        ]);
    }
}
