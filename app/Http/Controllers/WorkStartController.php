<?php

namespace App\Http\Controllers;

use App\Models\WorkStart;
use App\Models\TrainingSession;
use App\Models\TrainingSessionPostpone;
use App\Models\WaitingRepresentative;
use App\Models\Representative;
use App\Models\Company;
use App\Models\Message;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\MessageWorking;
use App\Models\ResignationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppWorkService;





class WorkStartController extends Controller
{
    /**
     * Display a listing of work start records
     */

    protected $whatsappService;

    public function __construct(WhatsAppWorkService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {

        $workStartQuery = WorkStart::with(['representative', 'governorate', 'location', 'message'])
            ->when(request('date_from'), fn($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date', '<=', request('date_to')))
            ->when(
                request('search'),
                fn($q) =>
                $q->whereHas('representative', function ($rep) {
                    $search = request('search');
                    $rep->where('name', 'like', "%{$search}%");
                })
            )
            ->when(
                request('company_id'),
                fn($q) =>
                $q->whereHas('representative', function ($rep) {
                    $rep->where('company_id', request('company_id'));
                })
            )
            ->when(
                request('status'),
                fn($q) => $q->where('status', request('status'))
            )
            ->when(
                request('postpone_reason'),
                function ($q) {
                    $reason = request('postpone_reason');
                    $q->whereExists(function ($sub) use ($reason) {
                        $sub->select(DB::raw(1))
                            ->from('training_session_postpones')
                            ->whereColumn('training_session_postpones.work_start_id', 'work_starts.id')
                            ->where('training_session_postpones.reason', $reason);
                    });
                }
            )
            ->when(
                request('resigned_status'),
                function ($q) {
                    $status = request('resigned_status');
                    if ($status === 'resigned') {
                        $q->whereHas('representative', function ($rep) {
                            $rep->where('is_active', 0);
                        });
                    } elseif ($status === 'not_resigned') {
                        $q->whereHas('representative', function ($rep) {
                            $rep->where('is_active', 1);
                        });
                    }
                }
            )
            ->orderBy('date', 'desc');

        // Pagination
        $statsQuery = clone $workStartQuery;
        $workStarts = $workStartQuery->paginate(20)->appends(request()->query());

        // IDs المندوبين الموجودين في الجلسات بعد الفلترة
        $representativeIds = (clone $statsQuery)
            ->pluck('representative_id')
            ->unique();

        $workStartIds = (clone $statsQuery)
            ->pluck('id')
            ->unique();

        // الإحصائيات (تعمل بناءً على الفلترة)
        $totalRepresentatives = $representativeIds->count();

        $NoonRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('company_id', 9)
            ->count();

        $BoostaRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('company_id', 10)
            ->count();

        $resignedCount = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('is_active', 0)
            ->count();

        $companies = Company::orderBy('name')->get();
        $companyCounts = Representative::whereIn('id', $representativeIds)
            ->select('company_id', DB::raw('count(*) as total'))
            ->groupBy('company_id')
            ->pluck('total', 'company_id')
            ->toArray();

        $postponeReasonCounts = TrainingSessionPostpone::whereIn('work_start_id', $workStartIds)
            ->select('reason', DB::raw('count(*) as total'))
            ->groupBy('reason')
            ->pluck('total', 'reason');

        $latestPostponeReasonsByWorkStart = TrainingSessionPostpone::whereIn('work_start_id', $workStartIds)
            ->orderBy('id', 'desc')
            ->get(['id', 'work_start_id', 'reason'])
            ->unique('work_start_id')
            ->pluck('reason', 'work_start_id');

        return view('work_starts.index', compact(
            'workStarts',
            'totalRepresentatives',
            'NoonRepresentatives',
            'BoostaRepresentatives',
            'resignedCount',
            'companies',
            'companyCounts',
            'postponeReasonCounts',
            'latestPostponeReasonsByWorkStart'
        ));
    }




     public function toggleStatus(Request $request, $id)
    {
        //return $request;

        // $representative = Representative::find($id);

        // $representative->update([
        //     'is_active' => !$representative->is_active
        // ]);

        $representative = Representative::findOrFail($id);

        // الحالة الجديدة
        //$newStatus = !$representative->is_active;

        // لو تغيير الحالة من نشط → غير نشط (يعني استقالة)
        if ($representative->is_active ) {

            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            ResignationRequest::create([
                'representative_id' => $representative->id,
                'resignation_date' => now(),
                'last_working_day' => now(), // لو عندك قيمة مختلفة عدّلها
                'reason' => $request->reason,
                'status' => 'pending',
                'source' => 'work_start',
            ]);
        }

        // تحديث حالة المندوب
        /* $representative->update([
            'is_active' => $newStatus
        ]); */

        // Sync user table status
        try {
            if ($representative->user) {
                $representative->user->update(['is_active' => $representative->is_active]);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $status = $representative->is_active ? 'نشط' : 'غير نشط';
        return redirect()->route('work_starts.index')->with('success', "تم تغيير حالة المندوب إلى: {$status}");
    }


    public function StartRealRepresentative(Request $request, $id)
    {
        //return $id;
        $request->validate([
            'date' => 'required|date',
            'message_id' => 'required',
        ]);

        $representative = Representative::find($id);

        if ($representative && $representative->is_active == false) {

            // تفعيل المندوب
            $representative->update([
                'is_active' => true
            ]);

            // مزامنة حالة المستخدم المرتبطة (لو موجود)
            if ($representative->user) {
                $representative->user->update(['is_active' => true]);
            }

        }
        // Get the message for WhatsApp
        $message = MessageWorking::find($request->message_id);


        WorkStart::updateOrCreate(
            ['representative_id' => $representative->id], // الشرط
            [
                'governorate_id' => $request->government_id,
                'location_id' => $request->location_id,
                'message_id' => $message->id,
                'date' => $request->date,

            ]
        );

        // Send WhatsApp message with Google Maps URL
        $whatsappResult = $this->whatsappService->send($representative->phone, $message->description, $request->date, $message->google_map_url, null);

        return redirect()->route(route: 'training_sessions.index')->with('success', "تم بدء الجلسة بنجاح");

    }


    public function followup(Request $request , $id){
        //return $request;
        try {
            // جلب سجل بداية العمل للمندوب
            $workStart = WorkStart::where('representative_id', $id)->first();

            if (!$workStart) {
                return redirect()->back()->with('error', 'لم يتم العثور على بيانات بداية العمل.');
            }

            // تحديث الحالة
            $workStart->update([
                'status' => $request->status,     // تم بدء العمل / لم يرد
            ]);

            return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الحالة.');
        }


    }

    public function postpone(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|in:مرضي,الـ zone مقفول,اخرى',
            'follow_up_date' => 'required|date',
            'note' => 'required|string',
        ]);

        $workStart = WorkStart::where('representative_id', $id)->latest('id')->first();
        if (!$workStart) {
            return redirect()->back()->with('error', 'لا توجد بيانات بدء عمل مرتبطة بهذا المندوب.');
        }

        TrainingSessionPostpone::updateOrCreate(
            ['work_start_id' => $workStart->id],
            [
                'training_session_id' => null,
                'follow_up_date' => $request->follow_up_date,
                'reason' => $request->reason,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]
        );

        WaitingRepresentative::updateOrCreate(
            ['representative_id' => $workStart->representative_id],
            [
                'date' => $request->follow_up_date ?? now(),
                'status' => 0,
                'source' => 'work_start',
            ]
        );

        return redirect()->route('work_starts.index')->with('success', 'تم تأجيل الجلسة وإضافة المندوب لقائمة المنتظرين.');
    }

    public function postponeHistory($id)
    {
        $workStart = WorkStart::where('representative_id', $id)->latest('id')->first();
        if (!$workStart) {
            return response()->json(['success' => false, 'items' => []]);
        }

        $items = TrainingSessionPostpone::where('work_start_id', $workStart->id)
            ->orderBy('created_at', 'desc')
            ->with('createdBy:id,name')
            ->get(['id', 'reason', 'follow_up_date', 'note', 'created_at', 'created_by'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reason' => $item->reason,
                    'follow_up_date' => $item->follow_up_date,
                    'note' => $item->note,
                    'created_at' => $item->created_at,
                    'created_by_name' => optional($item->createdBy)->name,
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }


}
