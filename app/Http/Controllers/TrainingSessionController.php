<?php

namespace App\Http\Controllers;

use App\Models\MessageWorking;
use App\Models\ResignationRequest;
use App\Models\TrainingSession;
use App\Models\Representative;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\Message;
use App\Models\WorkStart;
use App\Models\WaitingRepresentative;
use App\Models\TrainingSessionPostpone;
use App\Services\WhatsAppWorkService;

use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{

    protected $whatsappService;

    public function __construct(WhatsAppWorkService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        // جلب الجلسات مع الفلترة حسب التاريخ والبحث
        // $sessionsQuery = TrainingSession::with(['representative', 'governorate', 'location', 'message'])
        //     ->when(request('date_from'), fn($q) => $q->whereDate('date', '>=', request('date_from')))
        //     ->when(request('date_to'), fn($q) => $q->whereDate('date', '<=', request('date_to')))
        //     ->when(request('search'), fn($q) => $q->whereHas('representative', function ($qq) {
        //         $search = request('search');
        //         $qq->where('name', 'like', "%{$search}%");
        //     }))
        //     ->orderBy('date', 'desc');

        // // Pagination
        // $sessions = $sessionsQuery->paginate(20);

        // // IDs المندوبين الموجودين في الجلسات بعد الفلترة
        // $representativeIds = $sessionsQuery->pluck('representative_id')->unique();

        // // الإحصائيات حسب المندوبين الموجودين في الجلسات بعد الفلترة
        // $totalRepresentatives = $representativeIds->count();
        // $attendedRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
        //     ->where('is_training', 1)
        //     ->where('status', 0)
        //     ->count();
        // $notAttendedRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
        //     ->where('is_training', 0)
        //     ->where('status', 0)
        //     ->count();


        // جلب الجلسات مع الفلترة حسب التاريخ والبحث والشركة
        $sessionsQuery = TrainingSession::with(['representative', 'governorate', 'location', 'message'])
            ->when(request('date_from'), fn($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date', '<=', request('date_to')))
            ->when(
                request('search'),
                fn($q) =>
                $q->whereHas('representative', function ($rep) {
                    $search = request('search');
                    $rep->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                })
            )
            ->when(request('governorate_id'), function ($q) {
                $q->where('governorate_id', request('governorate_id'));
            })
            ->when(request('location_id'), function ($q) {
                $q->where('location_id', request('location_id'));
            })
            ->when(
                request('company_id'),
                fn($q) =>
                $q->whereHas('representative', function ($rep) {
                    $rep->where('company_id', request('company_id'));
                })
            )
            ->when(request()->filled('is_training'), function($q) {
                $isTraining = request('is_training');
                // استخدم شرط whereHas مع orWhere لمنع حجب الجلسات الأخرى
                $q->whereHas('representative', fn($rep) => $rep->where('is_training', $isTraining));
            })
            ->orderBy('date', 'desc');

        // Pagination
        $statsQuery = clone $sessionsQuery;
        $sessions = $sessionsQuery->paginate(20)->appends(request()->query());

        $sessionIds = $sessions->pluck('id');
        $latestPostponeReasons = TrainingSessionPostpone::whereIn('training_session_id', $sessionIds)
            ->orderBy('created_at', 'desc')
            ->get(['training_session_id', 'reason'])
            ->groupBy('training_session_id')
            ->map(fn($items) => $items->first()?->reason)
            ->toArray();

        // IDs المندوبين الموجودين في الجلسات بعد الفلترة
        $representativeIds = (clone $statsQuery)
            ->pluck('representative_id')
            ->unique();

        // الإحصائيات (تعمل بناءً على الفلترة)
        $totalRepresentatives = $representativeIds->count();

        $attendedRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('is_training', 1)
            ->where('status', 0)
            ->count();

        $notAttendedRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('is_training', 0)
            ->where('status', 0)
            ->count();

        $inactiveRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('is_active', 0)
            ->count();



        return view('training_sessions.index', compact(
            'sessions',
            'totalRepresentatives',
            'attendedRepresentatives',
            'notAttendedRepresentatives',
            'inactiveRepresentatives',
            'latestPostponeReasons'
        ));
    }

    public function create()
    {
        $representatives = Representative::where('is_active', 1)->get();
        $governorates = Governorate::all();
        $locations = Location::all();
        $messages = Message::all();

        return view('training_sessions.create', compact('representatives', 'governorates', 'locations', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'required|exists:locations,id',
            'message_id' => 'required|exists:messages,id',
            'date' => 'required|date',
            'type' => 'required|in:online,offline',
        ]);

        TrainingSession::create($request->all());

        return redirect()->route('training_sessions.index')->with('success', 'تم إضافة جلسة التدريب بنجاح.');
    }

    public function edit(TrainingSession $trainingSession)
    {
        $representatives = Representative::where('is_active', 1)->get();
        $governorates = Governorate::all();
        $locations = Location::all();
        $messages = Message::all();

        return view('training_sessions.edit', compact('trainingSession', 'representatives', 'governorates', 'locations', 'messages'));
    }

    public function update(Request $request, TrainingSession $trainingSession)
    {
        $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'required|exists:locations,id',
            'message_id' => 'required|exists:messages,id',
            'date' => 'required|date',
            'type' => 'required|in:online,offline',
        ]);

        $trainingSession->update($request->all());

        return redirect()->route('training_sessions.index')->with('success', 'تم تحديث جلسة التدريب بنجاح.');
    }

    public function destroy(TrainingSession $trainingSession)
    {
        $trainingSession->delete();
        return redirect()->route('training_sessions.index')->with('success', 'تم حذف جلسة التدريب بنجاح.');
    }


    public function toggleStatus(Request $request, $id)
    {


        $representative = Representative::findOrFail($id);



        if ($representative->is_active ) {

            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            // التحقق من وجود طلب استقالة بحالة pending
            $existingPendingRequest = ResignationRequest::where('representative_id', $representative->id)
                ->where('status', 'pending')
                ->first();

            if ($existingPendingRequest) {
                return redirect()->back()
                    ->with('error', 'يوجد طلب استقالة موجود بالفعل بحالة "في الانتظار" ولم يتم اتخاذ أي إجراء عليه. يرجى مراجعة الطلب الحالي أولاً.');
            }

            ResignationRequest::create([    // الشرط
                'representative_id' => $representative->id,
                'resignation_date' => now(),
                'last_working_day' => now(), // لو عندك قيمة مختلفة عدّلها
                'reason' => $request->reason,
                'status' => 'pending',
                'source' => 'training_session',
            ]);
        }



        return redirect()->route(route: 'training_sessions.index')->with('success', "تم تحويل  الشخص الي طلبات الاستقاله");
    }


    public function StartRealRepresentative(Request $request, $id)
    {
        //return $request;
        $request->validate([
            'date' => 'required|date',
            'message_id' => 'required',
        ]);

        $representative = Representative::find($id);


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
       // $whatsappResult = $this->whatsappService->send($representative->phone, $message->description, $request->date, $message->google_map_url, null);

       $employee = auth()->user()?->employee;
            $deviceToken = $employee?->device?->device_token;

            $whatsapp = app(\App\Services\WhatsAppServicebyair::class);
            $result = $whatsapp->send2($representative->phone, $message->description, $request->date, $message->google_map_url,null, $deviceToken);

        return redirect()->route(route: 'training_sessions.index')->with('success', "تم بدء الجلسة بنجاح");

    }

    public function activeResigne(Request $request, $id){
        //return  $id;
        $representative = Representative::findOrFail($id);
        $representative->update([
            'is_active' => true,                 // تفعيل
            'unresign_date' => now(),            // تاريخ التفعيل
            'unresign_by' => auth()->id(),       // المستخدم الذي فعل
            'resign_date' => null ,
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()->with('success', 'تم تفعيل المندوب بنجاح');
    }

    public function noLocation(Request $request, $id)
    {
        //return $id;
         WaitingRepresentative::updateOrCreate(
            ['representative_id' => $id], // الشرط
            [
                'date' => now(),
                'status' => 0,
            ]
        );


        return redirect()->route(route: 'training_sessions.index')->with('success', "تم إضافة المندوب إلى قائمة الانتظار بنجاح");

    }

    public function postpone(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|in:مرضي,الـ zone مقفول,اخرى',
            'follow_up_date' => 'nullable|date',
            'note' => 'required|string',
        ]);

        $session = TrainingSession::with('representative')->findOrFail($id);

        TrainingSessionPostpone::create([
            'training_session_id' => $session->id,
            'follow_up_date' => $request->follow_up_date,
            'reason' => $request->reason,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        WaitingRepresentative::updateOrCreate(
            ['representative_id' => $session->representative_id],
            [
                'date' => $request->follow_up_date ?? now(),
                'status' => 0,
            ]
        );

        return redirect()->route('training_sessions.index')->with('success', 'تم تأجيل الجلسة وإضافة المندوب لقائمة المنتظرين.');
    }

    public function postponeHistory($id)
    {
        $session = TrainingSession::findOrFail($id);

        $items = TrainingSessionPostpone::where('training_session_id', $session->id)
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
