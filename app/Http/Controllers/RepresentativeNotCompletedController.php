<?php

namespace App\Http\Controllers;
use App\Models\MessageTraining;
use App\Models\MessageWorking;
use App\Models\Representative;
use App\Models\RepresentativeNote;
use App\Services\RepresentativeNoService;
use App\Models\Company;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\User;
use App\Models\ResignationRequest;
use App\Models\TrainingSession;
use App\Services\WhatsAppWorkService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Exports\RepresentativesNotExport;
use Maatwebsite\Excel\Facades\Excel;


class RepresentativeNotCompletedController extends Controller
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
        $this->authorize('view_representatives_no');



        // Base Query (بدون علاقات – فقط الفلاتر الأساسية)
            $baseQuery = Representative::where('status', 0)
                //->where('is_active', 1)
                ->when($request->filled('date_from'), fn($q) =>
                    $q->whereDate('start_date', '>=', $request->date_from)
                )
                ->when($request->filled('date_to'), fn($q) =>
                    $q->whereDate('start_date', '<=', $request->date_to)
                )
                ->when($request->filled('company_id'), fn($q) =>
                    $q->where('company_id', $request->company_id)
                )
                ->when($request->filled('governorate_id'), fn($q) =>
                    $q->where('governorate_id', $request->governorate_id)
                )
                ->when($request->filled('location_id'), fn($q) =>
                    $q->where('location_id', $request->location_id)
                )
                ->when($request->filled('employee_id'), fn($q) =>
                    $q->where('employee_id', $request->employee_id)
                )
                ->when($request->filled('code_status'), function ($q) use ($request) {
                    if ($request->code_status === 'with') {
                        $q->whereNotNull('code')->where('code', '!=', '');
                    } elseif ($request->code_status === 'without') {
                        $q->where(function ($qq) {
                            $qq->whereNull('code')->orWhere('code', '');
                        });
                    }
                })
                ->when($request->filled('search'), function($q) use ($request){
                    $q->where(function($qq) use ($request){
                        $qq->where('name', 'like', "%{$request->search}%")
                           ->orWhere('phone', 'like', "%{$request->search}%")
                           ->orWhere('code', 'like', "%{$request->search}%")
                           ->orWhere('national_id', 'like', "%{$request->search}%");
                    });
                });

            // --------------------------
            //     DOCS Filter (JSON)
            // --------------------------
            if ($request->filled('docs')) {
                $requiredCount = count(Representative::requiredDocs());

                if ($request->docs === 'completed') {
                    $baseQuery->whereRaw("JSON_LENGTH(attachments) = ?", [$requiredCount]);
                }

                if ($request->docs === 'NotCompleted') {
                    $baseQuery->whereRaw("JSON_LENGTH(attachments) < ?", [$requiredCount]);
                }
            }

            // --------------------------
            //     Training Filter
            // --------------------------
            if ($request->filled('training')) {
                if ($request->training === 'attended') {
                    $baseQuery->where('is_training', 1);
                }

                if ($request->training === 'not_attended') {
                    $baseQuery->where('is_training', 0);
                }
            }

            // --------------------------
            //     Ready Filter
            // --------------------------
            if ($request->filled('ready')) {
                $baseQuery->withCount('deliveryDeposits');

                if ($request->ready === 'ready') {
                    $baseQuery->where('is_training', 1)
                              ->having('delivery_deposits_count', 7);
                }

                if ($request->ready === 'not_ready') {
                    $baseQuery->where(function ($q) {
                        $q->where('is_training', 0)
                          ->orHaving('delivery_deposits_count', '<', 7);
                    });
                }
            }


             // --------------------------
            //     Inquiry Filter
            // --------------------------
            if ($request->filled('inquiry_status')) {
                if ($request->inquiry_status === 'none') {
                    $baseQuery->where(function ($q) {
                        $q->whereDoesntHave('inquiry')
                          ->orWhereHas('inquiry', function ($iq) {
                              $iq->whereNull('inquiry_type');
                          });
                    });
                }

                if ($request->inquiry_status === 'good') {
                    $baseQuery->whereHas('inquiry', function ($iq) {
                        $iq->where(function ($qq) {
                            $qq->where('inquiry_field_result', 'good')
                               ->orWhere('inquiry_security_result', 'no_judgments');
                        });
                    });
                }

                if ($request->inquiry_status === 'bad') {
                    $baseQuery->whereHas('inquiry', function ($iq) {
                        $iq->where(function ($qq) {
                            $qq->where('inquiry_field_result', 'bad')
                               ->orWhere('inquiry_security_result', 'has_judgments');
                        });
                    });
                }
            }

                            // Documents received filter
            if ($request->filled('document_received')) {
                $baseQuery->where('documents_received', $request->document_received);
            }

            // ----------------------------------
            //        Missing One Doc Filter
            // ----------------------------------
            $collection = (clone $baseQuery)->get();

            if ($request->filled('missing_doc')) {
                $doc = $request->missing_doc;

                $collection = $collection->filter(fn($rep) =>
                    in_array($doc, $rep->missingDocs())
                );
            }

            // ----------------------------------
            //       Manual Pagination
            // ----------------------------------
            $page = $request->get('page', 1);
            $perPage = 20;
            $total = $collection->count();

            $representatives = new \Illuminate\Pagination\LengthAwarePaginator(
                $collection->forPage($page, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // ----------------------------------
            //            STATISTICS
            // ----------------------------------
            $requiredDocs = count(Representative::requiredDocs());

            $totalNotCompleted = (clone $baseQuery)->count();

            $missingDocsCount = (clone $baseQuery)->get()
                ->filter(fn($rep) => count($rep->missingDocs()) > 0)
                ->count();

            $notTrainedCount = (clone $baseQuery)->where('is_training', 0)->count();

            $trainedCount = (clone $baseQuery)->where('is_training', 1)->count();

            $readyToWorkCount = (clone $baseQuery)
                ->withCount('deliveryDeposits')
                ->get()
                ->filter(fn($rep) =>
                    $rep->is_training == 1 &&
                    $rep->delivery_deposits_count == 7 &&
                    count($rep->missingDocs()) == 0
                )
                ->count();



        return view('representativesNotCompleted.index', compact('representatives','totalNotCompleted',
        'missingDocsCount',
        'notTrainedCount',
        'trainedCount',
        'readyToWorkCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create_representatives_no');
        $companies = Company::where('is_active', true)->get();
        $governorates = Governorate::all();
        $locations = Location::all();

        $users = User::where('type','employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7)->where('is_active', true);
            })->get();

        // Get lead data if lead_id is provided
        $lead = null;
        if ($request->has('lead_id')) {
            $lead = \App\Models\Lead::find($request->lead_id);
        }

        return view('representativesNotCompleted.create', compact('companies', 'governorates', 'locations', 'lead','users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_representatives_no');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'address' => 'required|string|max:500',
            'address_in_card' => 'required|string|max:500',
            'contact' => 'nullable|string|max:255',
            'national_id' => 'required|digits:14|unique:representatives,national_id',
            'salary' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'bank_account' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_checkbox' => 'boolean',
            'inquiry_data' => 'nullable|string|max:1000',

            'inquiry_type' => 'nullable|in:field,security',
            'inquiry_field_result' => 'required_if:inquiry_type,field|in:good,bad',
            'inquiry_field_notes' => 'nullable|string|max:1000',
            'inquiry_field_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_security_result' => 'required_if:inquiry_type,security|in:has_judgments,no_judgments',
            'inquiry_security_notes' => 'nullable|string|max:1000',
            'inquiry_security_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'required|exists:locations,id',
            'home_location' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'employee_id' => 'required|exists:users,id',
            'is_supervisor' => 'boolean', // ← أضفناها
        ]);



        $validated['inquiry_checkbox'] = !empty($validated['inquiry_type']);

        if (($validated['inquiry_type'] ?? null) === 'field') {
            $validated['inquiry_security_result'] = null;
            $validated['inquiry_security_notes'] = null;
            $validated['inquiry_security_attachments'] = [];
        } elseif (($validated['inquiry_type'] ?? null) === 'security') {
            $validated['inquiry_field_result'] = null;
            $validated['inquiry_field_notes'] = null;
            $validated['inquiry_field_attachments'] = [];
        } else {
            $validated['inquiry_field_result'] = null;
            $validated['inquiry_field_notes'] = null;
            $validated['inquiry_field_attachments'] = [];
            $validated['inquiry_security_result'] = null;
            $validated['inquiry_security_notes'] = null;
            $validated['inquiry_security_attachments'] = [];
        }

        if (($validated['inquiry_type'] ?? null) === 'security' && ($validated['inquiry_security_result'] ?? null) === 'has_judgments') {
            $validated['is_active'] = false;
            $validated['security_inactive_reason'] = 'لأسباب أمنية';
        }

        $representative = $this->service->create($validated);

        // Check if this was created from a lead
        if ($request->has('lead_id')) {
            return redirect()->route('representatives-not-completed.index')->with('success', 'تم إنشاء المندوب بنجاح من العميل المحتمل!');
        }

        return redirect()->route('representatives-not-completed.index')->with('success', 'تم إنشاء المندوب بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('view_representatives_no');

        $representative = $this->service->find($id);
        $representative->load(['training', 'inquiry']);


         $workStartDate = \App\Models\WorkStart::where('representative_id', $representative->id)
            ->latest('date')
            ->value('date');

        $trainingDate = \App\Models\TrainingSession::where('representative_id', $representative->id)->first();


        if ($representative->attachments) {
            // Decode إذا كان String
            $attachments = is_string($representative->attachments)
                ? json_decode($representative->attachments, true)
                : $representative->attachments;

            if (is_array($attachments)) {
                $representative->attachments_with_urls = array_map(function($attachment) {
                    $path = $attachment['path'] ?? null;
                    $type = $attachment['type'] ?? 'مرفق غير معروف';

                    if (!$path) {
                        return null;
                    }

                    // Generate URL
                    $url = filter_var($path, FILTER_VALIDATE_URL)
                        ? $path
                        : asset('storage/app/public/' . $path);

                    return [
                        'type' => $type,
                        'path' => $path,
                        'url'  => $url,
                        'src'  => $url,
                    ];
                }, $attachments);

                // Remove nulls لو فيه مرفق بايظ
                $representative->attachments_with_urls = array_filter($representative->attachments_with_urls);
            }
        }


        return view('representativesNotCompleted.show', compact('representative','workStartDate','trainingDate'));
    }

    public function downloadAttachment($id, $index)
    {
        try {
            $this->authorize('view_representatives_no');
            $representative = $this->service->find($id);

            $attachments = is_string($representative->attachments)
                ? json_decode($representative->attachments, true)
                : $representative->attachments;

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

            $attachment = $attachments[$index];
            $path = $attachment['path'] ?? null;

            if (!$path || !Storage::disk('public')->exists($path)) {
                abort(404, 'الملف غير موجود على الخادم');
            }

            $fileName = basename($path);
            return Storage::disk('public')->download($path, $fileName);

        } catch (\Exception $e) {
            \Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'representative_id' => $id,
                'attachment_index' => $index,
            ]);
            abort(500, 'حدث خطأ أثناء تحميل الملف');
        }
    }



public function viewAttachment($id, $index)
{
    try {
        $this->authorize('view_representatives_no');
        $representative = $this->service->find($id);

        $attachments = is_string($representative->attachments)
            ? json_decode($representative->attachments, true)
            : $representative->attachments;

        if (!$attachments || !isset($attachments[$index])) {
            abort(404, 'الملف غير موجود');
        }

        $attachment = $attachments[$index]['path'] ?? null;

        if (!$attachment) {
            abort(404, 'الملف غير موجود');
        }

        // توليد الرابط المباشر بدون أي تعديل
        $url = url('storage/app/public/' . $attachment);

        return redirect($url);

    } catch (\Exception $e) {
        \Log::error('Error viewing attachment: ' . $e->getMessage(), [
            'representative_id' => $id,
            'attachment_index' => $index,
            'error' => $e->getMessage()
        ]);
        abort(500, 'حدث خطأ أثناء عرض الملف');
    }
}


    public function viewInquiryAttachment($id, $type, $index)
    {
        try {
           // $this->authorize('view_representatives_no');
            $representative = $this->service->find($id);
            $inquiry = $representative->inquiry;

            if (!in_array($type, ['field', 'security'], true)) {
                abort(404, 'الملف غير موجود');
            }

            $attachments = $type === 'field'
                ? ($inquiry->inquiry_field_attachments ?? $representative->inquiry_field_attachments ?? [])
                : ($inquiry->inquiry_security_attachments ?? $representative->inquiry_security_attachments ?? []);

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

            $item = $attachments[$index];
            $path = is_array($item) ? ($item['path'] ?? null) : $item;

            if (!$path || !Storage::disk('public')->exists($path)) {
                abort(404, 'الملف غير موجود');
            }

           // return Storage::disk('public')->response($path);
        return redirect(asset('storage/app/public/' . $path));

        } catch (\Exception $e) {
            \Log::error('Error viewing inquiry attachment: ' . $e->getMessage(), [
                'representative_id' => $id,
                'type' => $type,
                'attachment_index' => $index,
            ]);
            abort(500, 'حدث خطأ أثناء عرض الملف');
        }
    }


    public function edit($id)
    {
        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($id);
        $companies = Company::where('is_active', true)->get();
        $governorates = Governorate::all();
        $locations = Location::all();

        $users = User::where('type','employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7)->where('is_active', true);
            })->get();

        return view('representativesNotCompleted.edit', compact('representative', 'companies', 'governorates', 'locations' , 'users'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'phone' => ['required', 'digits:11', Rule::unique('users')->ignore($representative->user_id)],
            'phone' => 'required|digits:11',
            'address' => 'required|string|max:500',
            'address_in_card' => 'required|string|max:500',
            'contact' => 'nullable|string|max:255',
            'national_id' => ['required', 'digits:14', Rule::unique('representatives')->ignore($id)],
            'salary' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'bank_account' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_checkbox' => 'boolean',
            'inquiry_data' => 'nullable|string|max:1000',
            'inquiry_type' => 'nullable|in:field,security',
            'inquiry_field_result' => 'required_if:inquiry_type,field|in:good,bad',
            'inquiry_field_notes' => 'nullable|string|max:1000',
            'inquiry_field_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_security_result' => 'required_if:inquiry_type,security|in:has_judgments,no_judgments',
            'inquiry_security_notes' => 'nullable|string|max:1000',
            'inquiry_security_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'required|exists:locations,id',
            'home_location' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'employee_id' => 'required|exists:users,id',
            'is_supervisor' => 'boolean', // ← أضفناها
        ]);


        $validated['inquiry_checkbox'] = !empty($validated['inquiry_type']);

        if (($validated['inquiry_type'] ?? null) === 'field') {
            $validated['inquiry_security_result'] = null;
            $validated['inquiry_security_notes'] = null;
            $validated['inquiry_security_attachments'] = [];
        } elseif (($validated['inquiry_type'] ?? null) === 'security') {
            $validated['inquiry_field_result'] = null;
            $validated['inquiry_field_notes'] = null;
            $validated['inquiry_field_attachments'] = [];
        } else {
            $validated['inquiry_field_result'] = null;
            $validated['inquiry_field_notes'] = null;
            $validated['inquiry_field_attachments'] = [];
            $validated['inquiry_security_result'] = null;
            $validated['inquiry_security_notes'] = null;
            $validated['inquiry_security_attachments'] = [];
        }

        if (($validated['inquiry_type'] ?? null) === 'security' && ($validated['inquiry_security_result'] ?? null) === 'has_judgments') {
            $validated['is_active'] = false;
            $validated['security_inactive_reason'] = 'لأسباب أمنية';
        }


        $representative = $this->service->update($id, $validated);
        //return redirect()->route('representatives-not-completed.index')->with('success', 'تم تحديث المندوب بنجاح!');
        return redirect()->route('representatives-not-completed.show', $representative->id)->with('success', 'تم تحديث المندوب بنجاح!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function toggleStatus($id)
    {
        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($id);

        $representative->update([
            'is_active' => !$representative->is_active
        ]);

        // Sync user table status
        try {
            if ($representative->user) {
                $representative->user->update(['is_active' => $representative->is_active]);
            }
        } catch (\Throwable $e) { /* ignore */ }

        $status = $representative->is_active ? 'نشط' : 'غير نشط';
        return redirect()->route('representatives-not-completed.index')->with('success', "تم تغيير حالة المندوب إلى: {$status}");
    }

    public function StartRealRepresentative(Request $request,$id)
    {

        //return $request;

        $request->validate([
            'date' => 'required|date',
            'message_id' => 'required',
        ]);



        $representative = Representative::find($id);
        $representative->status = 1; // جعلها 1 دائماً
        $representative->converted_to_active_date = now()->toDateString(); // إضافة تاريخ التحويل
        $representative->converted_active_by = auth()->id();
        $representative->save();

        // Get the message for WhatsApp
        $message = MessageWorking::find($request->message_id);

        // Send WhatsApp message with Google Maps URL
        //$whatsappResult = $this->whatsappService->send($representative->phone, $message->description, $request->date, $message->google_map_url , null);


        $employee = auth()->user()?->employee;
            $deviceToken = $employee?->device?->device_token;

            $whatsapp = app(\App\Services\WhatsAppServicebyair::class);
            $result = $whatsapp->send2($representative->phone, $message->description, $request->date, $message->google_map_url,null , $deviceToken);

        return back();

    }

    public function representative(Request $request,$id)
    {

        //return $request;
        $request->validate([
            'date' => 'required|date',
            //'message_id' => 'required',
        ]);

        $representative = Representative::find($request->representative_id);
       // return $representative;
        $representative->status = 1; // جعلها 1 دائماً
        //$representative->converted_to_active_date = now()->toDateString(); // إضافة تاريخ التحويل
        $representative->converted_to_active_date = $request->date; // إضافة تاريخ التحويل
        $representative->converted_to_notcompleted_date = null;
        $representative->converted_active_by = auth()->id();



        $representative->save();



        // Get the message for WhatsApp
        // $message = MessageWorking::find($request->message_id);

        // // Send WhatsApp message with Google Maps URL
        // $whatsappResult = $this->whatsappService->send($representative->phone, $message->description, $request->date, $message->google_map_url);

        return back();
    }


    public function send_message_training(Request $request ,$id)
    {
        $request->validate([
            'date' => 'required|date',
            'message_id' => 'required',
            'type'=>'required',
            'company_id' => 'required',

        ]);

        $representative = Representative::find($request->representative_id);


        // Get the message for WhatsApp
        $message = MessageTraining::find($request->message_id);


        if (!$message) {
            return back()->with('error', 'الرسالة غير موجودة');
        }

        TrainingSession::updateOrCreate(
            ['representative_id' => $request->representative_id], // الشرط
            [
                'governorate_id' => $request->government_id,
                'location_id' => $request->location_id,
                'message_id' => $message->id,
                'date' => $request->date,
                'type' => $request->type,

            ]
        );

        $description = '';
        $link = null;

        if ($message->type === 'أونلاين') {
            $description = $message->description_training;
            $link = $message->link_training;
        } elseif ($message->type === 'في المقر') {
            $description = $message->description_location;
            $link = $message->google_map_url;
        }

        $representative->update([
            'company_id'=>$request->company_id,
            ]);
        // إرسال الواتساب بالوصف والرابط المناسب
        // $whatsappResult = $this->whatsappService->send(
        //     $representative->phone,
        //     $description,
        //     null,
        //     $link,
        //     $request->date,

        // );



        $employee = auth()->user()?->employee;
            $deviceToken = $employee?->device?->device_token;

            $whatsapp = app(\App\Services\WhatsAppServicebyair::class);
            $result = $whatsapp->send2(
                  $representative->phone,
                  $description,
                  null,
                  $link,
                  $request->date,
                  $deviceToken
            );
        return back();
    }

    public function toggleTraining(Request $request,$id)
    {

        //return $request;
        $representative = Representative::findOrFail($id);

        /* $representative->is_training = $representative->is_training ? 0 : 1;
        $representative->save(); */

        if ($request->is_training == 0) {

            // هنا الحالة: لم يحضر → مستخدم فتح المودال
            $representative->is_training =  0;
            $representative->save();


            TrainingSession::where('representative_id', $id)->update([
                'note'   => $request->note,
                'status' => $request->status,
            ]);

        } else {
                $representative->is_training =  1;
                $representative->save();
        }


       return back();
    }



    public function toggleDocumentsStatus($id)
    {
        $this->authorize('edit_trainings');

        $representative = Representative::findOrFail($id);

        $representative->documents_received =
            $representative->documents_received === 'received'
            ? 'pending'
            : 'received';

        $representative->save();

        return back()->with(
            'success',
            'تم تحديث حالة استلام الورق بنجاح'
        );
    }



    public function saveNote(Request $request, $id)
    {


        $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'note'              => 'required',
        ]);

        $representative = Representative::findOrFail($request->representative_id);

        RepresentativeNote::create([
            'representative_id' => $request->representative_id,
            'note'              => $request->note,
            'created_by'        => auth()->id(), // خلي بالك: لازم () مش id بس
        ]);

        return back()->with('success', 'تم حفظ الملاحظة بنجاح');

    }


     public function representative2(Request $request,$id)
    {
        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($id);

        $representative->update([
            'status' => !$representative->status
        ]);

        // Sync user table status
        try {
            if ($representative->user) {
                $representative->user->update(['status' => $representative->status]);
            }
        } catch (\Throwable $e) { /* ignore */ }

        $status = $representative->status ? 'مندوب غير فعلي' : 'مندوب فعلي';
        return redirect()->route('representatives-not-completed.index')->with('success', "تم تغيير حالة المندوب إلى: {$status}");
    }


     public function resignation(Request $request, $id)
    {
        //return $request;
        $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'reason'  => 'required|string|max:1000',
            'date'  => 'required|date',
        ]);

        $this->authorize('edit_representatives_no');
        $representative = $this->service->find($request->representative_id);


        $representative->update([
            'is_active' => 0 // حالة استقالة
        ]);


        ResignationRequest::create([
            'employee_id' => null, // مش موظف
            'representative_id' => $request->representative_id,
            'supervisor_id' => null, // مش موظف
            'resignation_date' => $request->date,
            'reason' => $request->reason,
            'status' => 'approved', // معتمدة فوراً
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('representatives-not-completed.index')->with('success', "تم تغيير حالة المندوب إلى: استقالة");
    }

    public function export(Request $request)
    {
        $filters = $request->all();

        return Excel::download(
            new RepresentativesNotExport($filters),
            'representatives.xlsx'
        );
    }




}
