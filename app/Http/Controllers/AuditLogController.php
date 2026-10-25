<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter by menu (shortcut filter)
        if ($request->filled('menu_filter')) {
            $menuFilter = $request->menu_filter;

            switch ($menuFilter) {
                case 'karyawan':
                    $query->where('model_type', 'App\\Models\\DataKaryawan');
                    break;
                case 'formasi':
                    $query->where('model_type', 'App\\Models\\Formasi');
                    break;
                case 'realisasi':
                    $query->where('model_type', 'App\\Models\\Realisasi');
                    break;
                case 'data-pgs':
                    $query->where('model_type', 'App\\Models\\DataPgs');
                    break;
                case 'user':
                    $query->where('model_type', 'App\\Models\\User');
                    break;
                case 'version':
                    $query->where('model_type', 'App\\Models\\Version');
                    break;
            }
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by model identifier
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('model_identifier', 'like', '%' . $request->search . '%')
                    ->orWhere('user_name', 'like', '%' . $request->search . '%')
                    ->orWhere('user_email', 'like', '%' . $request->search . '%');
            });
        }

        $auditLogs = $query->paginate(50)->withQueryString();

        // Get unique model types untuk filter
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->mapWithKeys(function ($type) {
                $name = match ($type) {
                    'App\\Models\\DataKaryawan' => 'Data Karyawan',
                    'App\\Models\\Formasi' => 'Data Formasi',
                    'App\\Models\\Realisasi' => 'Data Realisasi',
                    'App\\Models\\DataPgs' => 'Data PGS',
                    'App\\Models\\User' => 'User',
                    'App\\Models\\Version' => 'Version Snapshot',
                    default => class_basename($type),
                };
                return [$type => $name];
            });

        // Get users untuk filter
        $users = User::orderBy('name')->get();

        return view('audit-logs.index', compact('auditLogs', 'modelTypes', 'users'));
    }

    /**
     * Display audit log details
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality jika diperlukan
        return back()->with('info', 'Export feature coming soon');
    }
}
