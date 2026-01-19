<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller {
    public function index() {
        $activityLogs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.activity-logs', compact('activityLogs'));
    }
}