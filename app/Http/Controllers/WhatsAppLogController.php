<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WhatsAppLogController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display WhatsApp logs dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('view_whatsapp_logs');
        
        $query = WhatsAppLog::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }
        
        // Get logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get pending messages for quick actions
        $pendingMessages = WhatsAppLog::pending()->limit(10)->get();
        
        return view('whatsapp.logs.index', compact('logs', 'stats', 'pendingMessages'));
    }

    /**
     * Show detailed log view
     */
    public function show(WhatsAppLog $log)
    {
        $this->authorize('view_whatsapp_logs');
        
        return view('whatsapp.logs.show', compact('log'));
    }

    /**
     * Resend a specific message
     */
    public function resend(Request $request, WhatsAppLog $log)
    {
        $this->authorize('resend_whatsapp_messages');
        
        if (!$log->canResend()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إعادة إرسال هذه الرسالة'
            ], 400);
        }
        
        try {
            // Increment attempts
            $log->incrementAttempts();
            
            // Attempt to resend
            $result = $this->whatsappService->send($log->phone, $log->message);
            
            if ($result) {
                $log->markAsSent('Message resent successfully');
                
                return response()->json([
                    'success' => true,
                    'message' => 'تم إعادة إرسال الرسالة بنجاح',
                    'log' => $log->fresh()
                ]);
            } else {
                $log->markAsFailed('Failed to resend message');
                
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في إعادة إرسال الرسالة'
                ]);
            }
        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend all pending messages
     */
    public function resendAll(Request $request)
    {
        $this->authorize('resend_whatsapp_messages');
        
        $pendingLogs = WhatsAppLog::pending()->where('attempts', '<', 5)->get();
        
        if ($pendingLogs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد رسائل معلقة لإعادة الإرسال'
            ]);
        }
        
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($pendingLogs as $log) {
            try {
                $log->incrementAttempts();
                $result = $this->whatsappService->send($log->phone, $log->message);
                
                if ($result) {
                    $log->markAsSent('Bulk resend successful');
                    $successCount++;
                } else {
                    $log->markAsFailed('Bulk resend failed');
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $log->markAsFailed($e->getMessage());
                $failedCount++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "تم إعادة إرسال {$successCount} رسالة بنجاح، فشل {$failedCount} رسالة",
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ]);
    }

    /**
     * Delete a log entry
     */
    public function destroy(WhatsAppLog $log)
    {
        $this->authorize('manage_whatsapp_logs');
        
        $log->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف السجل بنجاح'
        ]);
    }

    /**
     * Bulk delete logs
     */
    public function bulkDelete(Request $request)
    {
        $this->authorize('manage_whatsapp_logs');
        
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تحديد أي سجلات للحذف'
            ], 400);
        }
        
        $deletedCount = WhatsAppLog::whereIn('id', $ids)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deletedCount} سجل بنجاح",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Clear old logs
     */
    public function clearOld(Request $request)
    {
        $this->authorize('manage_whatsapp_logs');
        
        $days = $request->input('days', 30);
        $cutoffDate = Carbon::now()->subDays($days);
        
        $deletedCount = WhatsAppLog::where('created_at', '<', $cutoffDate)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deletedCount} سجل أقدم من {$days} يوم",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics(Request $request)
    {
        $this->authorize('view_whatsapp_logs');
        
        $stats = $this->getStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Export logs
     */
    public function export(Request $request)
    {
        $this->authorize('view_whatsapp_logs');
        
        $query = WhatsAppLog::query();
        
        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'whatsapp_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'الهاتف', 'الرسالة', 'الحالة', 'الخدمة', 'المحاولات', 
                'تاريخ الإنشاء', 'تاريخ الإرسال', 'تاريخ الفشل', 'الخطأ'
            ]);
            
            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->phone,
                    $log->message,
                    $log->status_text,
                    $log->service,
                    $log->attempts,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '',
                    $log->failed_at ? $log->failed_at->format('Y-m-d H:i:s') : '',
                    $log->error
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get comprehensive statistics
     */
    private function getStatistics()
    {
        $total = WhatsAppLog::count();
        $sent = WhatsAppLog::sent()->count();
        $failed = WhatsAppLog::failed()->count();
        $pending = WhatsAppLog::pending()->count();
        
        $today = WhatsAppLog::today()->count();
        $thisWeek = WhatsAppLog::thisWeek()->count();
        $thisMonth = WhatsAppLog::thisMonth()->count();
        
        $successRate = $total > 0 ? round(($sent / $total) * 100, 2) : 0;
        
        // Service breakdown
        $serviceStats = WhatsAppLog::selectRaw('service, COUNT(*) as count')
            ->groupBy('service')
            ->pluck('count', 'service')
            ->toArray();
        
        // Daily stats for last 7 days
        $dailyStats = WhatsAppLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $successRate,
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'service_stats' => $serviceStats,
            'daily_stats' => $dailyStats
        ];
    }
}