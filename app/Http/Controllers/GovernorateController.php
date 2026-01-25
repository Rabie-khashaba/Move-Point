<?php
namespace App\Http\Controllers;

use App\Services\GovernorateService;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class GovernorateController extends Controller
{
    protected $service;

    public function __construct(GovernorateService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_governorates');

        $query = Governorate::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $governorates = $query->paginate(20)->withQueryString();

        return view('governorates.index', compact('governorates'));
    }

    public function create()
    {
        $this->authorize('create_governorates');
        return view('governorates.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_governorates');
        $validated = $request->validate([
            //'name' => 'required|string|max:255',
            'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('governorates')->where(function ($query) {
                return $query; // ممكن تضيف شروط إضافية لو احتجت
            }),
        ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        // Ensure unchecked checkbox becomes false
        $validated['is_active'] = $request->has('is_active');
        $this->service->create($validated);
        return redirect()->route('governorates.index')->with('success', 'تم إنشاء المحافظة بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_governorates');
        $governorate = $this->service->find($id);
        return view('governorates.show', compact('governorate'));
    }

    public function edit($id)
    {
        $this->authorize('edit_governorates');
        $governorate = $this->service->find($id);
        return view('governorates.edit', compact('governorate'));
    }

    public function update(Request $request, $id)
    {
        //return $request;
        $this->authorize('edit_governorates');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        // Ensure unchecked checkbox becomes false
        $validated['is_active'] = $request->has('is_active');
        
        // إذا تم تغيير الحالة إلى غير نشط، قم بتحديث inactive_date
        $governorate = $this->service->find($id);
        if (!$validated['is_active'] && $governorate->is_active) {
            // تم تغيير الحالة من نشط إلى غير نشط
            $validated['inactive_date'] = now()->toDateString();
        } elseif ($validated['is_active']) {
            // إذا أصبحت نشطة، احذف التاريخ
            $validated['inactive_date'] = null;
        }
        
        $this->service->update($id, $validated);
        return redirect()->route('governorates.index')->with('success', 'تم تحديث المحافظة بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_governorates');
        $this->service->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المحافظة بنجاح'
            ]);
        }

        return redirect()->route('governorates.index')->with('success', 'تم حذف المحافظة بنجاح!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_governorates');
        $governorate = $this->service->find($id);
        $governorate->update(['is_active' => !$governorate->is_active]);

        $status = $governorate->is_active ? 'نشطة' : 'غير نشطة';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم تغيير حالة المحافظة إلى {$status} بنجاح",
                'governorate' => $governorate
            ]);
        }

        return redirect()->route('governorates.index')->with('success', "تم تغيير حالة المحافظة إلى {$status} بنجاح");
    }
}
