<?php
namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    protected $service;

    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_companies');

        $query = Company::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $companies = $query->paginate(20)->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        $this->authorize('create_companies');
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_companies');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $this->service->create($validated);
        return redirect()->route('companies.index')->with('success', 'تم إنشاء الشركة بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_companies');
        $company = $this->service->find($id);
        
        // Process logo to include proper URL
        if ($company->logo) {
            $company->logo_url = asset('storage/app/public/' . $company->logo);
            $company->logo_src = asset('storage/app/public/' . $company->logo);
        }
        
        return view('companies.show', compact('company'));
    }

    public function edit($id)
    {
        $this->authorize('edit_companies');
        $company = $this->service->find($id);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_companies');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $this->service->update($id, $validated);
        return redirect()->route('companies.index')->with('success', 'تم تحديث الشركة بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_companies');
        $this->service->delete($id);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الشركة بنجاح'
            ]);
        }
        
        return redirect()->route('companies.index')->with('success', 'تم حذف الشركة بنجاح!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_companies');
        $company = $this->service->find($id);
        $company->update(['is_active' => !$company->is_active]);
        
        $status = $company->is_active ? 'مفعلة' : 'معطلة';
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "تم {$status} الشركة بنجاح!",
                'company' => $company
            ]);
        }
        
        return redirect()->route('companies.index')->with('success', "تم {$status} الشركة بنجاح!");
    }
}
