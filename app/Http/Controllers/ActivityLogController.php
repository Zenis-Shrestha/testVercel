<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(100)          // latest 100 logs
            ->get()
            ->map(function ($log) {
                return [
                    'id'          => $log->id,
                    'description' => $log->description,
                    'action'      => $log->action,
                    'model'       => $log->model,
                    'ip_address'  => $log->ip_address,
                    'user_name'   => $log->user?->name ?? 'System',
                    'created_at'  => $log->created_at->diffForHumans(), // "2 minutes ago"
                ];
            });

        return response()->json($logs);
    }
}
