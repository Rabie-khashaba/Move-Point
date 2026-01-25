<?php
namespace App\Http\Controllers;

use App\Services\SourceService;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    protected $service;

    public function __construct(SourceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_sources');

        $query = Source::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sources = $query->paginate(20)->withQueryString();

        return view('sources.index', compact('sources'));
    }

    public function create()
    {
        $this->authorize('create_sources');
        return view('sources.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_sources');
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'name.string'   => 'الاسم يجب أن يكون نصاً',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرفاً',
            'description.max' => 'الوصف يجب ألا يتجاوز 500 حرفاً',
        ]);

        $source = $this->service->create($validated);
        return redirect()->route('sources.index')->with('success', '✅ تم إضافة المصدر بنجاح');
    }

    public function show($id)
    {
        $this->authorize('view_sources');
        $source = $this->service->find($id);
        return view('sources.show', compact('source'));
    }

    public function edit($id)
    {
        $this->authorize('edit_sources');
        $source = $this->service->find($id);
        return view('sources.edit', compact('source'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_sources');
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'name.string'   => 'الاسم يجب أن يكون نصاً',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرفاً',
            'description.max' => 'الوصف يجب ألا يتجاوز 500 حرفاً',
        ]);

        $source = $this->service->update($id, $validated);
        return redirect()->route('sources.index')->with('success', '✅ تم تحديث المصدر بنجاح');
    }

    public function destroy($id)
    {
        $this->authorize('delete_sources');
        $this->service->delete($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ تم حذف المصدر بنجاح'
            ]);
        }
        
        return redirect()->route('sources.index')->with('success', '🗑️ تم حذف المصدر بنجاح');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_sources');
        $source = $this->service->find($id);
        $source->update(['is_active' => !$source->is_active]);
        
        $status = $source->is_active ? '✅ تم تفعيل المصدر' : '⛔ تم تعطيل المصدر';
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $status,
                'source'  => $source
            ]);
        }
        
        return redirect()->route('sources.index')->with('success', $status);
    }
}
