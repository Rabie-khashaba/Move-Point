<?php

namespace App\Http\Controllers;

use App\Models\LeadTarget;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeadsTargetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_representative_targets');

        $currentYear = request('year', now()->year);
        $currentMonth = request('month', now()->month);

        // Get existing targets for this month/year
        $leadTargets = LeadTarget::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->orderBy('created_at', 'desc')
                ->get();

        return view('representative-targets.index', compact('leadTargets', 'currentYear', 'currentMonth'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_representative_targets');

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'leads_count' => 'required|integer|min:0',
            'bonus_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        // Allow multiple targets per month/year

        // Create new target
        $target = LeadTarget::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'leads_count' => $validated['leads_count'],
            'bonus_amount' => $validated['bonus_amount'],
            'notes' => $validated['notes']
        ]);

        // Process bonuses for qualified employees
        $target->saveWithBonusProcessing();

        return back()->with('success', 'تم حفظ الهدف ومعالجة المكافآت بنجاح!');
    }

    public function create()
    {
        $this->authorize('create_representative_targets');

        $currentYear = now()->year;
        $currentMonth = now()->month;

        return view('representative-targets.leadTargetCreate', compact('currentYear', 'currentMonth'));
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('edit_representative_targets');

        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*.id' => 'nullable|exists:lead_targets,id',
            'targets.*.leads_count' => 'required|integer|min:0',
            'targets.*.bonus_amount' => 'required|numeric|min:0',
            'targets.*.notes' => 'nullable|string|max:500'
        ]);

        $updatedCount = 0;
        $currentYear = now()->year;
        $currentMonth = now()->month;

        foreach ($request->input('targets') as $targetData) {
            if (isset($targetData['id'])) {
                // Update existing target
                $target = LeadTarget::find($targetData['id']);
                if ($target) {
                    $target->update([
                        'leads_count' => $targetData['leads_count'],
                        'bonus_amount' => $targetData['bonus_amount'],
                        'notes' => $targetData['notes']
                    ]);
                    $updatedCount++;
                }
            } else {
                // Create new target
                LeadTarget::create([
                    'leads_count' => $targetData['leads_count'],
                    'bonus_amount' => $targetData['bonus_amount'],
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'notes' => $targetData['notes']
                ]);
                $updatedCount++;
            }
        }

        return back()->with('success', "تم تحديث {$updatedCount} هدف بنجاح!");
    }

    public function destroy($id)
    {
        $this->authorize('delete_representative_targets');

        $target = LeadTarget::findOrFail($id);
        $target->delete();

        return back()->with('success', 'تم حذف الهدف بنجاح!');
    }

    public function processBonuses(Request $request)
    {
        $this->authorize('edit_representative_targets');

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $processedCount = LeadTarget::processAllTargets($year, $month);

        return back()->with('success', "تم معالجة {$processedCount} هدف وإضافة المكافآت للموظفين المؤهلين!");
    }
}
