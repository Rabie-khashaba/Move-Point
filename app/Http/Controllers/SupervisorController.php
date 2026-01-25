<?php
namespace App\Http\Controllers;
use App\Services\SupervisorService;
use App\Models\Location;
use App\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupervisorController extends Controller
{
    protected $service;

    public function __construct(SupervisorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_supervisors');

        $query = \App\Models\Supervisor::with(['representatives', 'location', 'governorate']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // Governorate filter
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        // Location filter
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $supervisors = $query->paginate(20);
        return view('supervisors.index', compact('supervisors'));
    }

    public function create()
    {
        $this->authorize('create_supervisors');
        $governorates = \App\Models\Governorate::all();
        $locations = Location::where('is_active', true)->get();
        $representatives = Representative::where('is_active', true)
            ->with(['company', 'governorate', 'location', 'supervisors'])
            ->get()
            ->filter(function($representative) {
                return $representative->supervisors->isEmpty();
            });
        return view('supervisors.create', compact('governorates', 'locations', 'representatives'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_supervisors');
        $validated = $request->validate([
            'name' => 'required|string|max:255',                    // الاسم ( نص)
            'phone' => 'required|digits:11|unique:users,phone',     // رقم التليفون ( رقم مقيد بعدد 11 رقم )
            'contact' => 'required|digits:11',                      // التواصل ( رقم )
            'governorate_id' => 'required|exists:governorates,id',  // المحافظة
            'location_id' => 'nullable|exists:locations,id',        // المقر ( المقر المسؤول عنه المشرف )
            'location_name' => 'nullable|string|max:255',           // المقر المسؤول عنه المشرف
            'national_id' => 'required|digits:14|unique:supervisors,national_id', // رقم البطاقة (رقم مقيد ب 14 رقم)
            'salary' => 'required|numeric|min:0',                   // المرتب ( رقم)
            'start_date' => 'required|date',                        // تاريخ بداية العمل (تاريخ)
            'representative_ids' => 'array',                        // Multiple representatives
            'representative_ids.*' => 'exists:representatives,id',
            'is_active' => 'boolean',
        ]);

        $supervisor = $this->service->create($validated);
        return redirect()->route('supervisors.index')->with('success', 'تم إنشاء المشرف بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_supervisors');
        $supervisor = $this->service->find($id);
        return view('supervisors.show', compact('supervisor'));
    }

    public function edit($id)
    {
        $this->authorize('edit_supervisors');
        $supervisor = $this->service->find($id);
        $governorates = \App\Models\Governorate::all();
        $locations = Location::where('is_active', true)->get();
        $representatives = Representative::where('is_active', true)
            ->with(['company', 'governorate', 'location', 'supervisors'])
            ->get()
            ->filter(function($representative) {
                return $representative->supervisors->isEmpty();
            });
        return view('supervisors.edit', compact('supervisor', 'governorates', 'locations', 'representatives'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_supervisors');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'digits:11', Rule::unique('users')->ignore($this->service->find($id)->user_id)],
            'contact' => 'required|digits:11',
            'governorate_id' => 'required|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'location_name' => 'nullable|string|max:255',
            'national_id' => ['required', 'digits:14', Rule::unique('supervisors')->ignore($id)],
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'representative_ids' => 'array',
            'representative_ids.*' => 'exists:representatives,id',
            'is_active' => 'boolean',
        ]);

        $supervisor = $this->service->update($id, $validated);
        return redirect()->route('supervisors.index')->with('success', 'تم تحديث المشرف بنجاح!');
    }

    public function changePassword(Request $request, $id)
    {
        $this->authorize('edit_supervisors');
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->service->changePassword($id, $validated['password']);
        return redirect()->route('supervisors.index')->with('success', 'تم تغيير كلمة المرور بنجاح!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_supervisors');

        try {
            $supervisor = $this->service->find($id);
            $newStatus = !$supervisor->is_active;

            $supervisor->update(['is_active' => $newStatus]);

            // Sync user table status
            try {
                if ($supervisor->user) {
                    $supervisor->user->update(['is_active' => $newStatus]);
                }
            } catch (\Throwable $e) { /* ignore */ }

            $statusText = $newStatus ? 'تفعيل' : 'إيقاف';
            return redirect()->route('supervisors.index')->with('success', "تم {$statusText} المشرف بنجاح!");

        } catch (\Exception $e) {
            return redirect()->route('supervisors.index')->with('error', 'حدث خطأ أثناء تغيير حالة المشرف');
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete_supervisors');
        $this->service->delete($id);
        return redirect()->route('supervisors.index')->with('success', 'تم حذف المشرف بنجاح!');
    }

    // Transfer representative to different supervisor
    public function transferRepresentative(Request $request)
    {
        $this->authorize('edit_supervisors');

        $validated = $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'new_supervisor_id' => 'required|exists:supervisors,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $result = $this->service->transferRepresentative(
            $validated['representative_id'],
            $validated['new_supervisor_id'],
            $validated['reason'] ?? null
        );

        if ($result) {
            return response()->json(['success' => true, 'message' => 'تم نقل الممثل بنجاح!']);
        }

        return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء نقل الممثل'], 400);
    }

    // Get representatives for a supervisor
    public function getRepresentatives($id)
    {
        $this->authorize('view_supervisors');
        $supervisor = $this->service->find($id);
        $representatives = $supervisor->representatives()->with(['company', 'governorate', 'location'])->get();

        return response()->json($representatives);
    }

    // Get representatives by governorate
    public function getRepresentativesByGovernorate($governorateId)
    {
        $this->authorize('view_supervisors');
        $representatives = Representative::where('is_active', true)
            ->where('governorate_id', $governorateId)
            ->with(['company', 'governorate', 'location', 'supervisors'])
            ->get()
            ->filter(function($representative) {
                return $representative->supervisors->isEmpty();
            });

        return response()->json($representatives);
    }

    // Get representatives by location
    public function getRepresentativesByLocation($locationId)
    {
        $this->authorize('view_supervisors');
        $representatives = Representative::where('is_active', true)
            ->where('location_id', $locationId)
            ->with(['company', 'governorate', 'location', 'supervisors'])
            ->get()
            ->filter(function($representative) {
                return $representative->supervisors->isEmpty();
            });

        return response()->json($representatives);
    }

    // Search representatives with filters (for AJAX Select2)
    public function searchRepresentatives(Request $request)
    {
        $this->authorize('view_supervisors');

        $query = Representative::where('is_active', true)
            ->with(['company', 'governorate', 'location', 'supervisors']);

        // Filter by governorate if provided
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        // Filter by location if provided
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

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

        $representatives = $query->get()
            ->filter(function($representative) {
                return $representative->supervisors->isEmpty();
            })
            ->map(function($representative) {
                return [
                    'id' => $representative->id,
                    'text' => $representative->name . ' - ' . ($representative->code ?? '') . ' (' . ($representative->phone ?? '') . ')',
                    'name' => $representative->name,
                    'code' => $representative->code,
                    'phone' => $representative->phone,
                ];
            })
            ->values();

        return response()->json($representatives);
    }

    // Get supervisors by location
    public function getSupervisorsByLocation($locationId)
    {
        $this->authorize('view_supervisors');
        $supervisors = \App\Models\Supervisor::where('is_active', true)
            ->where('location_id', $locationId)
            ->with(['governorate'])
            ->get();

        return response()->json($supervisors);
    }

    // Get supervisors by governorate
    public function getSupervisorsByGovernorate($governorateId)
    {
        $this->authorize('view_supervisors');
        $supervisors = \App\Models\Supervisor::where('is_active', true)
            ->where('governorate_id', $governorateId)
            ->with(['governorate'])
            ->get();

        return response()->json($supervisors);
    }
}
