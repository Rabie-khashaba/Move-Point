<?php

namespace App\Http\Controllers;

use App\Models\WorkStart;
use App\Models\Representative;
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
            ->orderBy('date', 'desc');

        // Pagination
        $workStarts = $workStartQuery->paginate(20)->appends(request()->query());

        // IDs المندوبين الموجودين في الجلسات بعد الفلترة
        $representativeIds = (clone $workStartQuery)
            ->pluck('representative_id')
            ->unique();

        // الإحصائيات (تعمل بناءً على الفلترة)
        $totalRepresentatives = $representativeIds->count();

        $NoonRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('company_id', 9)
            ->count();

        $BoostaRepresentatives = \App\Models\Representative::whereIn('id', $representativeIds)
            ->where('company_id', 10)
            ->count();


        return view('work_starts.index', compact('workStarts', 'totalRepresentatives', 'NoonRepresentatives', 'BoostaRepresentatives'));
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


}
