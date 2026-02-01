<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:audit.read');
    }

    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->leftJoin('users as u', 'u.id', '=', 'activity_logs.user_id')
            ->orderByDesc('activity_logs.created_at')
            ->get([
                'activity_logs.id',
                'activity_logs.method',
                'activity_logs.url',
                'activity_logs.status_code',
                'activity_logs.created_at',
                'u.name as user_name',
                'u.email as user_email',
            ]);

        return view('activity-logs.index', compact('logs'));
    }

    public function show(string $id)
    {
        $log = ActivityLog::query()
            ->leftJoin('users as u', 'u.id', '=', 'activity_logs.user_id')
            ->where('activity_logs.id', $id)
            ->firstOrFail([
                'activity_logs.id',
                'activity_logs.method',
                'activity_logs.url',
                'activity_logs.status_code',
                'activity_logs.ip_address',
                'activity_logs.user_agent',
                'activity_logs.request_payload',
                'activity_logs.response_payload',
                'activity_logs.created_at',
                'u.name as user_name',
                'u.email as user_email',
            ]);

        return response()->json([
            'id' => $log->id,
            'user' => [
                'name' => $log->user_name,
                'email' => $log->user_email,
            ],
            'method' => $log->method,
            'url' => $log->url,
            'status_code' => $log->status_code,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'request_payload' => $log->request_payload,
            'response_payload' => $log->response_payload,
            'created_at' => $log->created_at,
        ]);
    }
}
