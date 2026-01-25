<?php
namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Models\Governorate;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class LocationController extends Controller
{
    protected $service;

    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_locations');

        $query = Location::query();

        // البحث بالاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // الفلترة بالمحافظة
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        $locations = $query->paginate(20)->withQueryString();

        // تحميل المحافظات لقائمة الفلترة
        $governorates = \App\Models\Governorate::all();

        return view('locations.index', compact('locations', 'governorates'));
    }

    public function create()
    {
        $this->authorize('create_locations');
        $governorates = Governorate::get();
        return view('locations.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_locations');
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            //'name' => 'required|string|max:255',
            'name' => [
            'required',
            'string',
            'max:255',
            // تحقق من عدم وجود نفس الاسم داخل نفس المحافظة
            Rule::unique('locations')->where(function ($query) use ($request) {
                return $query->where('governorate_id', $request->governorate_id);
            }),
        ],
            'is_active' => 'boolean',
        ]);


        // Ensure unchecked checkbox becomes false
        $validated['is_active'] = $request->has('is_active');
        $this->service->create($validated);
        return redirect()->route('locations.index')->with('success', 'تم إنشاء الموقع بنجاح');
    }

    public function show($id)
    {
        $this->authorize('view_locations');
        $location = $this->service->find($id);
        return view('locations.show', compact('location'));
    }

    public function edit($id)
    {
        $this->authorize('edit_locations');
        $location = $this->service->find($id);
        $governorates = Governorate::get();
        return view('locations.edit', compact('location', 'governorates'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_locations');
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);
        // Ensure unchecked checkbox becomes false
        $validated['is_active'] = $request->has('is_active');
        $this->service->update($id, $validated);
        return redirect()->route('locations.index')->with('success', 'تم تحديث الموقع بنجاح');
    }

    public function destroy($id)
    {
        $this->authorize('delete_locations');
        $this->service->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموقع بنجاح'
            ]);
        }

        return redirect()->route('locations.index')->with('success', 'تم حذف الموقع بنجاح');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_locations');
        $location = $this->service->find($id);
        $location->update(['is_active' => !$location->is_active]);

        $status = $location->is_active ? 'تم التفعيل' : 'تم الإيقاف';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "الموقع {$status} بنجاح",
                'location' => $location
            ]);
        }

        return redirect()->route('locations.index')->with('success', "الموقع {$status} بنجاح");
    }
}
