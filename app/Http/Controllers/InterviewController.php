<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\InterviewNote;
use App\Models\Lead;
use App\Models\Message;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exports\InterviewsExport;
use App\Models\Supervisor;
use Maatwebsite\Excel\Facades\Excel;

class InterviewController extends Controller
{
    protected $whatsappService  ,$service;

    public function __construct(WhatsAppService $whatsappService , LeadService $service)
    {
        $this->whatsappService = $whatsappService;
        $this->service = $service;
    }


    public function index(Request $request)
    {


        // Apply search filter if there's a search term
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');

        // Fetch the interviews with pagination, including filters
        $interviews = Interview::with(['lead.source','lead.governorate', 'message.government', 'message.location', 'notes' => function($query) {
                $query->latest()->limit(1); // Get only the latest note
            }])
            ->when($search, function ($query, $search) {
                return $query->whereHas('lead', function ($q) use ($search) {
                    $q->where(function($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                    });
                });
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('date_interview', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                return $query->whereDate('date_interview', '<=', $dateTo);
            })->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                return $query->where('assigned_to', $request->employee_id); // 👈 فلتر بالموظف
            })
            ->when($request->filled('governorate_id'), function ($query) use ($request) {
                $query->whereHas('message.government', function ($q) use ($request) {
                    $q->where('government_id', $request->governorate_id);
                });
            })

            ->latest('date_interview')
            ->paginate(15)->appends($request->query()); // 👈 يحافظ على الفلاتر أثناء التنقل بين الصفحات


        // إجمالي المقابلات
        $totalInterviews = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->count();

        $undefinedCount = \App\Models\Interview::query()
        ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
        ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
        ->whereNull('status')
        ->count();

// غير مهتم
        $notInterestedCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'غير مهتم')
            ->count();

// موافق
        $acceptedCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'موافق على العمل')
            ->count();

// هيفكر
        $thinkingCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'هيفكر')
            ->count();


        $noResponseCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'لم يرد')
            ->count();


        $absentCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'لم يحضر')
            ->count();


        $followUpNextTimeCount = \App\Models\Interview::query()
            ->when(request('date_from'), fn($q) => $q->whereDate('date_interview', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date_interview', '<=', request('date_to')))
            ->where('status', 'المتابعة مرة أخرى')
            ->count();



        $employees = \App\Models\User::where('type', 'employee')
        ->whereHas('employee', function ($query) {
            $query->where('department_id', 7);
        })
        ->get();

        $governorates = \App\Models\Governorate::get();







        // Pass the interviews to the view
        return view('interviews.index', compact('interviews',
            'search' ,'totalInterviews','notInterestedCount','acceptedCount','thinkingCount',
            'undefinedCount','absentCount','noResponseCount','followUpNextTimeCount','employees','governorates'));
    }

    public function show($id)
    {
        try {
            $interview = Interview::with(['lead.source', 'message.government', 'message.location', 'notes.createdBy:id,name'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'interview' => $interview
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'المقابلة غير موجودة',
                'error' => 'ModelNotFoundException: Interview with ID ' . $id . ' not found',
                'debug_info' => [
                    'id' => $id,
                    'total_interviews' => Interview::count(),
                    'available_ids' => Interview::pluck('id')->toArray()
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المقابلة',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'id' => $id,
                    'exception_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function saveNote(Request $request, $id)
    {

        try {

            $validated = $request->validate([
                'note' => 'required|string|max:5000',
                'status' => 'required',
            ]);

            $interview = Interview::findOrFail($id);
            // تحديث الحالة في جدول المقابلة
            if (!empty($validated['status'])) {
                $interview->status = $validated['status'];
                $interview->save();
            }


            $note = InterviewNote::create([
                'interview_id' => $interview->id,
                'note' => $validated['note'],
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ]);

            $notes = $interview->notes()->with('createdBy:id,name')->get(['id','note','created_at','created_by']);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الملاحظة بنجاح',
                'notes' => $notes,
                'status' => $interview->status,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر حفظ الملاحظة',
            ], 500);
        }
    }


    public function updateDate(Request $request, $id){
        $request->validate([
            'date_interview' => 'required|date',
            'message_id' => 'required',
            'supervisor_id' => 'nullable',
        ]);


        $interview = Interview::findOrFail($id);
        $oldDate = $interview->date_interview;
        $newDate = $request->date_interview;
        $interview->date_interview = $newDate;
        $interview->supervisor_id = $request->supervisor_id;
        $interview->save();

        // تحديد الحالة بناءً على الفرق في الأيام
        $diffDays = \Carbon\Carbon::parse($oldDate)->diffInDays($newDate, false);

        if ($diffDays > 0) {
            $status = "تم تأجيل المقابلة";
        } elseif ($diffDays < 0) {
            $status = "تم تقديم المقابلة";
        } else {
            $status = "تغيير بسيط في الوقت";
        }

        $interview->notes()->create([
            'note' => 'تم تعديل تاريخ المقابلة من '
                . \Carbon\Carbon::parse($oldDate)->format('Y-m-d H:i')
                . ' إلى '
                . \Carbon\Carbon::parse($newDate)->format('Y-m-d H:i'),
            'status' => $status,
            'created_by' => auth()->id(),
        ]);

        // Update lead status to "حالة مقابلة"
        $lead = Lead::find($request->lead_id);
        $lead->update(['status' => 'مقابلة']);

        // Get the message for WhatsApp
        $message = Message::find($request->message_id);

        // Send WhatsApp message with Google Maps URL
        $whatsappResult = $this->whatsappService->send($lead->phone, $message->description, $request->date_interview, $message->google_map_url);

//        return response()->json([
//            'success' => true,
//            'message' => 'تم جدولة المقابلة بنجاح' . ($whatsappResult ? ' وتم إرسال رسالة واتساب' : ' ولكن فشل في إرسال رسالة واتساب'),
//            'interview' => $interview,
//            'whatsapp_sent' => $whatsappResult
//        ]);

        return back();
    }

    public function destroy($id)
    {
        try {
            $interview = Interview::findOrFail($id);
            $interview->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المقابلة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المقابلة'
            ], 500);
        }
    }

    public function resendWhatsApp($id)
    {
        try {
            $interview = Interview::with(['lead', 'message'])->findOrFail($id);

            // Send WhatsApp message with Google Maps URL
            $result = $this->whatsappService->send($interview->lead->phone, $interview->message->description, $interview->date_interview, $interview->message->google_map_url);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال رسالة واتساب بنجاح'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في إرسال رسالة واتساب'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        //return $request;
        try {
            $validated = $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'message_id' => 'required|exists:messages,id',
                'supervisor_id' => 'required|exists:supervisors,id',
                'date_interview' => 'required|date|after:now',
            ]);

            $lead = \App\Models\Lead::findOrFail($request->lead_id);

            // Create the interview بنفس الموظف المعين للـ lead
            $interview = Interview::create([
                'lead_id'        => $validated['lead_id'],
                'message_id'     => $validated['message_id'],
                'supervisor_id'     => $validated['supervisor_id'],
                'date_interview' => $validated['date_interview'],
                'assigned_to'    => $lead->assigned_to, // 👈 نفس assign بتاع lead
            ]);

            // Update lead status to "حالة مقابلة"
            $lead = Lead::find($validated['lead_id']);
            $lead->update(['status' => 'مقابلة']);

            // Get the message for WhatsApp
            $message = Message::find($validated['message_id']);

            // Send WhatsApp message with Google Maps URL
            $whatsappResult = $this->whatsappService->send($lead->phone, $message->description, $validated['date_interview'], $message->google_map_url);

            return response()->json([
                'success' => true,
                'message' => 'تم جدولة المقابلة بنجاح' . ($whatsappResult ? ' وتم إرسال رسالة واتساب' : ' ولكن فشل في إرسال رسالة واتساب'),
                'interview' => $interview,
                'whatsapp_sent' => $whatsappResult
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating interview: ' . $e->getMessage(), [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جدولة المقابلة: ' . $e->getMessage()
            ], 500);
        }
    }



    public function bulkAssign(Request $request)
    {
    $this->authorize('edit_interviews'); // بدل التصريح حسب الـ policy عندك

    $request->validate([
        'interviews'   => 'required|array',
        'interviews.*' => 'exists:interviews,id',
        'employee_id'  => 'nullable|exists:users,id'
    ]);

    try {
        $employeeId = $request->employee_id;

        // لو مفيش موظف محدد → وزع Round-Robin أو حسب أقل عدد مقابلات
        if (empty($employeeId)) {
            $assignments = $this->service->distributeInterviewsRoundRobin($request->interviews);

            if (empty($assignments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد موظفين متاحين للتعيين'
                ], 400);
            }

            foreach ($assignments as $interviewId => $assigneeId) {
               $interview = \App\Models\Interview::find($interviewId);

                if ($interview) {
                    // تحديث المقابلة
                    $interview->update([
                        'assigned_to' => $assigneeId
                    ]);

                    // تحديث الـ Lead المرتبط
                    if ($interview->lead_id) {
                        \App\Models\Lead::where('id', $interview->lead_id)
                            ->update(['assigned_to' => $assigneeId]);
                    }
                }

            }


            $employeeId = null; // distributed individually
        }

        // لو موظف محدد → تحديث بالجملة
        if ($employeeId) {

           $leadIds = \App\Models\Interview::whereIn('id', $request->interviews)
                ->pluck('lead_id')
                ->filter() // يستبعد null
                ->unique(); // يمنع التكرار

            // تحديث كل الـ interviews
            \App\Models\Interview::whereIn('id', $request->interviews)
                ->update(['assigned_to' => $employeeId]);

            // تحديث كل الـ leads المرتبطة
            if ($leadIds->isNotEmpty()) {
                \App\Models\Lead::whereIn('id', $leadIds)
                    ->update(['assigned_to' => $employeeId]);
            }
        }

        // جلب اسم الموظف للرسالة
        $employeeName = $employeeId
            ? (User::with('employee')->find($employeeId)?->employee?->name
                ?? User::find($employeeId)?->name
                ?? 'غير محدد')
            : 'تم توزيع المقابلات على عدة موظفين';

        return response()->json([
            'success' => true,
            'message' => "تمت إعادة توزيع المقابلات بنجاح إلى: {$employeeName}",
            'redirect' => route('interviews.index')
        ]);
    } catch (\Throwable $e) {
        Log::error('خطأ أثناء إعادة توزيع المقابلات: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'تعذر إعادة توزيع المقابلات'
        ], 500);
    }
}


public function export(Request $request)
{
    return Excel::download(new InterviewsExport($request), 'interviews.xlsx');
}


// InterviewController.php
public function getSupervisors(Request $request)
{
    $governmentId = $request->government_id;
    $locationId = $request->location_id;

    if (!$governmentId || !$locationId) {
        return response()->json([]);
    }

    // جلب المشرفين النشطين المرتبطين بالمحافظة والمنطقة
    $supervisors = Supervisor::active()
        ->where('governorate_id', $governmentId)
        ->where('location_id', $locationId)
        ->get(['id', 'name']);

    return response()->json($supervisors);
}



}
