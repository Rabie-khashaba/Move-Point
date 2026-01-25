<?php

namespace App\Http\Controllers;

use App\Services\RepresentativeNoService;
use App\Services\WhatsAppWorkService;
use App\Exports\RepresentativesResigneExport;
use App\Models\Company;
use App\Models\Governorate;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class RepresentativeResignContorller extends Controller
{

    protected $service;
    protected $whatsappService;

    public function __construct(RepresentativeNoService $service , WhatsAppWorkService $whatsappService)
    {
        $this->service = $service;
        $this->whatsappService = $whatsappService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

         //return $request;
        $this->authorize('view_representatives_no');

        $query = \App\Models\Representative::where('is_active',0)->with(['company', 'user', 'supervisors', 'location', 'governorate'])->withCount('deliveryDeposits');



        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Company filter
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // docs
        if ($request->filled('docs')) {
            $requiredCount = count(\App\Models\Representative::requiredDocs());

            if ($request->docs === 'completed') {
                // عنده أوراق ناقصة (missingDocs > 0)
                $query->whereRaw("JSON_LENGTH(attachments) < ?", [$requiredCount]);
            }

            if ($request->docs === 'NotCompleted') {
                // مكتمل (missingDocs = 0)
                $query->whereRaw("JSON_LENGTH(attachments) = ?", [$requiredCount]);
            }
        }

        // Training filter
        if ($request->filled('training')) {
            if ($request->training === 'attended') {
                $query->where('is_training', 1);
            }

            if ($request->training === 'not_attended') {
                $query->where('is_training', 0);
            }
        }

        // Ready filter
//        if ($request->filled('ready')) {
//            if ($request->ready === 'ready') {
//                $query->having('delivery_deposits_count', '=', 7);
//            }
//
//            if ($request->ready === 'not_ready') {
//                $query->having('delivery_deposits_count', '<', 7);
//            }
//        }



        if ($request->filled('ready')) {
            if ($request->ready === 'ready') {
                $query->where('is_training', 1) // لازم يكون حضر التدريب
                ->having('delivery_deposits_count', '=', 7); // 7 إيداعات
            }

            if ($request->ready === 'not_ready') {
                $query->where(function ($q) {
                    $q->where('is_training', 0) // لسه ما حضرش
                    ->orHaving('delivery_deposits_count', '<', 7); // أقل من 7 إيداعات
                });
            }
        }





        $representatives = $query->paginate(20)->appends(request()->query());
//        dd($representatives);


       $dateFrom = $request->filled('date_from') ? $request->date_from : null;
        $dateTo   = $request->filled('date_to') ? $request->date_to : null;

        $totalRepresentatives = \App\Models\Representative::where('is_active', 0)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $NoonRepresentatives = \App\Models\Representative::where('is_active', 0)
            ->where('company_id', 9)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $boostaRepresentatives = \App\Models\Representative::where('is_active', 0)
            ->where('company_id', 10)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $governorates = Governorate::get();
        $companies = Company::get();




        return view('representativesResignation.index', compact('representatives','totalRepresentatives','NoonRepresentatives','boostaRepresentatives','governorates','companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function toggleStatus(Request $request, $id)
    {

        //return $request->all();
        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($id);

        $representative->update([
            'is_active' => !$representative->is_active,
            'unresign_date' => now(),            // تاريخ التفعيل
            'unresign_by' => auth()->id(),       // المستخدم الذي فعل
            'resign_date' => null ,
            'company_id' => $request->company_id,
            'governorate_id' => $request->governorate_id,
            'location_id' => $request->location_id,
        ]);




        // Sync user table status
        try {
            if ($representative->user) {
                $representative->user->update(['is_active' => $representative->is_active]);
            }
        } catch (\Throwable $e) { /* ignore */ }

        $status = $representative->is_active ? 'نشط' : 'غير نشط';
        return redirect()->route('resignation-representatives.index')->with('success', "تم تغيير حالة المندوب إلى: {$status}");
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export(Request $request)
    {
        //$filters = $request->all();

        return Excel::download(
            new RepresentativesResigneExport($request),
            'representatives.xlsx'
        );
    }
}
