<?php

namespace App\Http\Controllers;

use App\Models\MessageWorking;
use App\Models\Representative;
use App\Models\WaitingRepresentative;
use App\Models\WorkStart;
use App\Models\WaitingRepresentativeFollowup;
use App\Models\TrainingSessionPostpone;
use Illuminate\Http\Request;
use App\Services\WhatsAppWorkService;

class WaitingRepresentativeController extends Controller
{

    protected $whatsappService;

    public function __construct(WhatsAppWorkService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }
    public function index(Request $request)
    {
        // $waitings = WaitingRepresentative::with('representative')->paginate(10);


        $waitingRepresentativesQuery = WaitingRepresentative::where('status', 0)->with(['representative'])
            ->when(request('date_from'), fn($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request('date_to'), fn($q) => $q->whereDate('date', '<=', request('date_to')))
            ->when(request('search'), function ($q) {
                $search = request('search');

                $q->whereHas('representative', function ($rep) use ($search) {
                    $rep->where(function ($cond) use ($search) {

                        $cond->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")

                            // Governorate ID
                            //->orWhere('governorate_id', $search)
                            // Location ID
                            //->orWhere('location_id', $search)

                            // Governorate name
                            ->orWhereHas('governorate', function ($gov) use ($search) {
                                $gov->where('name', 'like', "%{$search}%");
                            })

                            // Location name
                            ->orWhereHas('location', function ($loc) use ($search) {
                                $loc->where('name', 'like', "%{$search}%");
                            });
                    });
                });
            })
            ->when(
                request('company_id'),
                fn($q) =>
                $q->whereHas('representative', function ($rep) {
                    $rep->where('company_id', request('company_id'));
                })
            )
            ->when(request('governorate_id'), function ($q) {
                $q->whereHas('representative', function ($rep) {
                    $rep->where('governorate_id', request('governorate_id'));
                });
            })
            ->when(request('location_id'), function ($q) {
                $q->whereHas('representative', function ($rep) {
                    $rep->where('location_id', request('location_id'));
                });
            })
            ->when(request('postpone_reason'), function ($q) {
                $reason = request('postpone_reason');

                $repIds = TrainingSessionPostpone::join('training_sessions', 'training_sessions.id', '=', 'training_session_postpones.training_session_id')
                    ->where('training_session_postpones.reason', $reason)
                    ->pluck('training_sessions.representative_id');

                $q->whereIn('representative_id', $repIds);
            })
            ->when(request('followup_status'), function ($q) {
                $status = request('followup_status');

                $latestPerWaiting = WaitingRepresentativeFollowup::selectRaw('waiting_representative_id, MAX(created_at) as max_created')
                    ->groupBy('waiting_representative_id');

                $waitingIds = WaitingRepresentativeFollowup::joinSub($latestPerWaiting, 'latest', function ($join) {
                    $join->on('waiting_representative_followups.waiting_representative_id', '=', 'latest.waiting_representative_id')
                        ->on('waiting_representative_followups.created_at', '=', 'latest.max_created');
                })
                    ->where('waiting_representative_followups.status', $status)
                    ->pluck('waiting_representative_followups.waiting_representative_id');

                $q->whereIn('id', $waitingIds);
            })
            ->orderBy('date', 'desc');

        // Pagination
        $waitings = $waitingRepresentativesQuery->paginate(20)->appends(request()->query());

        // IDs المندوبين الموجودين في الجلسات بعد الفلترة
        $representativeIds = (clone $waitingRepresentativesQuery)
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

        $postponeReasonCounts = TrainingSessionPostpone::join('training_sessions', 'training_sessions.id', '=', 'training_session_postpones.training_session_id')
            ->whereIn('training_sessions.representative_id', $representativeIds)
            ->whereIn('training_session_postpones.reason', ['مرضي', 'الـ zone مقفول', 'اخرى'])
            ->selectRaw('training_session_postpones.reason, COUNT(DISTINCT training_sessions.representative_id) as total')
            ->groupBy('training_session_postpones.reason')
            ->pluck('total', 'reason');

        $postponeReasonSick = (int) ($postponeReasonCounts['مرضي'] ?? 0);
        $postponeReasonZoneClosed = (int) ($postponeReasonCounts['الـ zone مقفول'] ?? 0);
        $postponeReasonOther = (int) ($postponeReasonCounts['اخرى'] ?? 0);

        $latestPostponeReasons = TrainingSessionPostpone::join('training_sessions', 'training_sessions.id', '=', 'training_session_postpones.training_session_id')
            ->whereIn('training_sessions.representative_id', $representativeIds)
            ->get(['training_sessions.representative_id as representative_id', 'training_session_postpones.reason'])
            ->keyBy('representative_id')
            ->map(fn($item) => $item?->reason)
            ->toArray();

        $latestFollowupStatuses = WaitingRepresentativeFollowup::whereIn('waiting_representative_id', $waitings->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get(['waiting_representative_id', 'status'])
            ->groupBy('waiting_representative_id')
            ->map(fn($items) => $items->first()?->status)
            ->toArray();

        return view('waiting-representatives.index', compact(
            'waitings',
            'totalRepresentatives',
            'NoonRepresentatives',
            'BoostaRepresentatives',
            'postponeReasonSick',
            'postponeReasonZoneClosed',
            'postponeReasonOther',
            'latestPostponeReasons',
            'latestFollowupStatuses'
        ));
    }

    public function followupStore(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:لم يرد,متابعة مره اخري,تغيير الشركه',
            'follow_up_date' => 'nullable|date',
            'note' => 'required|string',
        ]);

        $waiting = WaitingRepresentative::findOrFail($id);

        WaitingRepresentativeFollowup::create([
            'waiting_representative_id' => $waiting->id,
            'status' => $request->status,
            'follow_up_date' => $request->follow_up_date,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('waiting-representatives.index')->with('success', 'تم حفظ المتابعة بنجاح.');
    }

    public function followupHistory($id)
    {
        $waiting = WaitingRepresentative::findOrFail($id);

        $items = WaitingRepresentativeFollowup::where('waiting_representative_id', $waiting->id)
            ->orderBy('created_at', 'desc')
            ->with('createdBy:id,name')
            ->get(['id', 'status', 'follow_up_date', 'note', 'created_at', 'created_by'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
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



    public function StartRealRepresentative(Request $request, $id)
    {
        //return $id;
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

        WaitingRepresentative::where('representative_id', $representative->id)->update([
            'status' => 1,
        ]);

        // Send WhatsApp message with Google Maps URL
        $whatsappResult = $this->whatsappService->send($representative->phone, $message->description, $request->date, $message->google_map_url, null);

        return redirect()->route(route: 'waiting-representatives.index')->with('success', "تم ارسال بيانات بدء العمل بنجاح ");

    }

    public function changeLocation(Request $request, $id)
    {
        //return $id;
        $request->validate([
            'government_id' => 'required',
            'location_id' => 'required',
        ]);

        $representative = Representative::find($id);

        $representative->location_id = $request->location_id;
        $representative->governorate_id = $request->government_id;
        $representative->save();


        WaitingRepresentative::where('representative_id', $representative->id)->update([
            'status' => 1,
        ]);

        return redirect()->route(route: 'waiting-representatives.index')->with('success', "تم تغير المنطقه بنجاح ");
    }

    public function resign(Request $request, $id)
    {
        $request->validate([
            'pickup_location' => 'required|string|max:255',
            'appointment_date' => 'required|date',
        ]);

        $representative = Representative::findOrFail($id);

        $representative->update([
            'is_active' => false,
            'resign_date' => now(),
            'unresign_date' => null,
            'unresign_by' => null,
        ]);

        $message = "يرجى التوجه إلى {$request->pickup_location} لاستلام اوراق الاستقالة بتاريخ {$request->appointment_date}.";

        $this->whatsappService->send($representative->phone, $message);

        return redirect()->route('waiting-representatives.index')->with('success', 'تم إرسال رسالة الاستقالة بنجاح.');
    }

}
