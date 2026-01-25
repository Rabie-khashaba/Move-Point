<?php
namespace App\Http\Controllers;
use App\Services\RepresentativeNoService;
use App\Models\Company;
use App\Models\Governorate;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RepresentativesExport;


class RepresentativeController extends Controller
{
    protected $service;

    public function __construct(RepresentativeNoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        //return $request;
        $this->authorize('view_representatives');

        $query = \App\Models\Representative::where('status', 1)
            ->where('is_active', 1)
            ->with(['company', 'user', 'supervisors', 'location', 'governorate'])->withCount('deliveryDeposits');

        // بحث بالكلمات
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // الشركة
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Documents received filter
       if ($request->filled('document_received')) {
            $query->where('documents_received', $request->document_received);
        }

        $representatives = $query->paginate(20);



        //statistics
        /* $totalRepresentatives = \App\Models\Representative::where('status', 1)->where('is_active',1)->count();
        $NoonRepresentatives = \App\Models\Representative::where('status', 1)->where('is_active',1)->where('company_id',9)->count();
        $boostaRepresentatives = \App\Models\Representative::where('status', 1)->where('is_active',1)->where('company_id',10)->count(); */

        $dateFrom = $request->filled('date_from') ? $request->date_from : null;
        $dateTo = $request->filled('date_to') ? $request->date_to : null;

        $totalRepresentatives = \App\Models\Representative::where('status', 1)
            ->where('is_active', 1)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $NoonRepresentatives = \App\Models\Representative::where('status', 1)
            ->where('is_active', 1)
            ->where('company_id', 9)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();

        $boostaRepresentatives = \App\Models\Representative::where('status', 1)
            ->where('is_active', 1)
            ->where('company_id', 10)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->count();


        return view('representatives.index', compact('representatives', 'totalRepresentatives', 'NoonRepresentatives', 'boostaRepresentatives'));
    }

    public function create(Request $request)
    {
        $this->authorize('create_representatives');
        $companies = Company::where('is_active', true)->get();
        $governorates = Governorate::all();
        $locations = Location::all();

        // Get lead data if lead_id is provided
        $lead = null;
        if ($request->has('lead_id')) {
            $lead = \App\Models\Lead::find($request->lead_id);
        }

        return view('representatives.create', compact('companies', 'governorates', 'locations', 'lead'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_representatives');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'address' => 'required|string|max:500',
            'contact' => 'required|string|max:255',
            'national_id' => 'required|digits:14|unique:representatives,national_id',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'bank_account' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_checkbox' => 'boolean',
            'inquiry_data' => 'nullable|string|max:1000',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'home_location' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        $representative = $this->service->create($validated);

        // Check if this was created from a lead
        if ($request->has('lead_id')) {
            return redirect()->route('representatives.index')->with('success', 'تم إنشاء المندوب بنجاح من العميل المحتمل!');
        }

        return redirect()->route('representatives.index')->with('success', 'تم إنشاء المندوب بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_representatives');
        $representative = $this->service->find($id);

        // Process attachments to include proper URLs
        if ($representative->attachments) {
            // Check if attachments is already an array or needs to be decoded
            if (is_string($representative->attachments)) {
                $attachments = json_decode($representative->attachments, true);
            } else {
                $attachments = $representative->attachments;
            }

            if (is_array($attachments)) {
                $representative->attachments_with_urls = array_map(function ($path) {
                    // Check if path is already a full URL
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        return [
                            'path' => $path,
                            'url' => $path,
                            'src' => $path
                        ];
                    } else {
                        return [
                            'path' => $path,
                            'url' => asset('storage/app/public/' . $path),
                            'src' => asset('storage/app/public/' . $path)
                        ];
                    }
                }, $attachments);
            }
        }

        return view('representatives.show', compact('representative'));
    }

    public function downloadAttachment($id, $index)
    {
        try {
            $this->authorize('view_representatives');
            $representative = $this->service->find($id);

            $attachments = null;
            if ($representative->attachments) {
                if (is_string($representative->attachments)) {
                    $attachments = json_decode($representative->attachments, true);
                } elseif (is_array($representative->attachments)) {
                    $attachments = $representative->attachments;
                }
            }

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

            $attachment = $attachments[$index];

            // Security: Ensure the attachment path is within the storage directory
            $fixedAttachment = str_replace(['\\', '//'], ['/', '/'], $attachment);
            $filePath = storage_path('app/public/' . $fixedAttachment);

            // Additional security check to prevent directory traversal
            $realPath = realpath($filePath);
            $storagePath = realpath(storage_path('app/public'));

            if (!$realPath || strpos($realPath, $storagePath) !== 0) {
                abort(404, 'الملف غير موجود على الخادم');
            }

            if (!file_exists($filePath)) {
                abort(404, 'الملف غير موجود على الخادم');
            }

            $fileName = basename($fixedAttachment);
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $contentType = 'application/octet-stream';
            switch ($extension) {
                case 'pdf':
                    $contentType = 'application/pdf';
                    break;
                case 'jpg':
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
            }

            return response()->download($filePath, $fileName, [
                'Content-Type' => $contentType
            ]);
        } catch (\Exception $e) {
            \Log::error('Error downloading attachment: ' . $e->getMessage(), [
                'representative_id' => $id,
                'attachment_index' => $index,
                'error' => $e->getMessage()
            ]);
            abort(500, 'حدث خطأ أثناء تحميل الملف');
        }
    }

    public function viewAttachment($id, $index)
    {
        try {
            $this->authorize('view_representatives');
            $representative = $this->service->find($id);

            $attachments = null;
            if ($representative->attachments) {
                if (is_string($representative->attachments)) {
                    $attachments = json_decode($representative->attachments, true);
                } elseif (is_array($representative->attachments)) {
                    $attachments = $representative->attachments;
                }
            }

            if (!$attachments || !isset($attachments[$index])) {
                abort(404, 'الملف غير موجود');
            }

            $attachment = $attachments[$index];

            // Check if attachment is already a full URL
            if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                // It's already a full URL, redirect directly
                return redirect($attachment);
            }

            // Security: Ensure the attachment path is within the storage directory
            $fixedAttachment = str_replace(['\\', '//'], ['/', '/'], $attachment);

            // Basic security check to prevent directory traversal
            if (strpos($fixedAttachment, '..') !== false || strpos($fixedAttachment, '/') === 0) {
                abort(404, 'الملف غير موجود على الخادم');
            }

            // Check if file exists in public storage (where files are served from)
            $publicPath = public_path('storage/' . $fixedAttachment);

            if (!file_exists($publicPath)) {
                // Try to find the file in different possible locations
                $possiblePaths = [
                    $fixedAttachment,
                    'representatives/attachments/' . basename($fixedAttachment),
                    'attachments/' . basename($fixedAttachment),
                    basename($fixedAttachment)
                ];

                $foundPath = null;
                foreach ($possiblePaths as $possiblePath) {
                    $testPath = public_path('storage/' . $possiblePath);
                    if (file_exists($testPath)) {
                        $foundPath = $possiblePath;
                        break;
                    }
                }

                if ($foundPath) {
                    $storageUrl = asset('storage/app/public/' . $foundPath);
                    return redirect($storageUrl);
                } else {
                    abort(404, 'الملف غير موجود على الخادم');
                }
            }

            // Return a redirect to the proper storage URL
            $storageUrl = asset('storage/app/public/' . $fixedAttachment);
            return redirect($storageUrl);
        } catch (\Exception $e) {
            \Log::error('Error viewing attachment: ' . $e->getMessage(), [
                'representative_id' => $id,
                'attachment_index' => $index,
                'error' => $e->getMessage()
            ]);
            abort(500, 'حدث خطأ أثناء عرض الملف');
        }
    }

    public function edit($id)
    {
        $this->authorize('edit_representatives');
        $representative = $this->service->find($id);
        $companies = Company::where('is_active', true)->get();
        $governorates = Governorate::all();
        $locations = Location::all();
        $users = User::where('type', 'employee')->whereHas('employee', function ($query) {
            $query->where('department_id', 7)->where('is_active', true);
        })->get();
        return view('representatives.edit', compact('representative', 'companies', 'governorates', 'locations', 'users'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_representatives');
        $representative = $this->service->find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'digits:11', Rule::unique('users')->ignore($representative->user_id)],
            'address' => 'required|string|max:500',
            'contact' => 'required|string|max:255',
            'national_id' => ['required', 'digits:14', Rule::unique('representatives')->ignore($id)],
            'salary' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'bank_account' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'inquiry_checkbox' => 'boolean',
            'inquiry_data' => 'nullable|string|max:1000',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'home_location' => 'required|url|max:500',
            'is_active' => 'boolean',
            'employee_id' => 'nullable|exists:users,id',
        ]);

        $representative = $this->service->update($id, $validated);
        return redirect()->route('representatives.index')->with('success', 'تم تحديث المندوب بنجاح!');
    }

    public function changePassword(Request $request, $id)
    {
        $this->authorize('edit_representatives');
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $this->service->changePassword($id, $validated['password']);
        return redirect()->route('representatives.index')->with('success', 'تم تحديث كلمة المرور بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_representatives');
        $this->service->delete($id);
        return redirect()->route('representatives.index')->with('success', 'تم حذف المندوب بنجاح!');
    }

    public function getLocationsByGovernorate($governorateId)
    {
        $locations = Location::where('governorate_id', $governorateId)->get();
        return response()->json($locations);
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_representatives');
        $representative = $this->service->find($id);

        $representative->update([
            'is_active' => !$representative->is_active
        ]);

        // Sync user table status
        try {
            if ($representative->user) {
                $representative->user->update(['is_active' => $representative->is_active]);
            }
        } catch (\Throwable $e) { /* ignore */
        }

        $status = $representative->is_active ? 'نشط' : 'غير نشط';
        return redirect()->route('representatives.index')->with('success', "تم تغيير حالة المندوب إلى: {$status}");
    }


    public function export(Request $request)
    {
        return Excel::download(new RepresentativesExport($request), 'representatives.xlsx');
    }




}
