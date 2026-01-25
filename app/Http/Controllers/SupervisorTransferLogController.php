<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTransferLog;
use Illuminate\Http\Request;

class SupervisorTransferLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_supervisors');
        
        $query = SupervisorTransferLog::with([
            'representative', 
            'oldSupervisor', 
            'newSupervisor', 
            'transferredBy'
        ])->orderBy('created_at', 'desc');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('representative', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Representative filter
        if ($request->filled('representative_id')) {
            $query->where('representative_id', $request->representative_id);
        }
        
        // Old supervisor filter
        if ($request->filled('old_supervisor_id')) {
            $query->where('old_supervisor_id', $request->old_supervisor_id);
        }
        
        // New supervisor filter
        if ($request->filled('new_supervisor_id')) {
            $query->where('new_supervisor_id', $request->new_supervisor_id);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transferLogs = $query->paginate(20);
        
        // Get data for filters
        $representatives = \App\Models\Representative::where('is_active', true)->get();
        $supervisors = \App\Models\Supervisor::where('is_active', true)->get();
        
        return view('supervisor-transfer-logs.index', compact('transferLogs', 'representatives', 'supervisors'));
    }
}
