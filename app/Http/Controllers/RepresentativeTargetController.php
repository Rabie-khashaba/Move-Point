<?php

namespace App\Http\Controllers;

use App\Models\RepresentativeTarget;
use App\Models\Employee;
use App\Models\LeadTarget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RepresentativeTargetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_representative_targets');

       // المندوبين
        $repYear = $request->rep_year ?? now()->year;
        $repMonth = $request->rep_month ?? now()->month;

        $targets = \App\Models\RepresentativeTarget::where('year', $repYear)
            ->where('month', $repMonth)
            ->get();

        // التسويق (Leads)
        $leadYear = $request->lead_year ?? now()->year;
        $leadMonth = $request->lead_month ?? now()->month;

        $leadTargets = \App\Models\LeadTarget::where('year', $leadYear)
            ->where('month', $leadMonth)
            ->get();

        return view('representative-targets.index', compact(
            'targets',
            'leadTargets',
            'repYear',
            'repMonth',
            'leadYear',
            'leadMonth'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create_representative_targets');

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'representatives_count' => 'required|integer|min:0',
            'bonus_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        // Allow multiple targets per month/year

        // Create new target
        $target = RepresentativeTarget::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'representatives_count' => $validated['representatives_count'],
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

        return view('representative-targets.create', compact('currentYear', 'currentMonth'));
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('edit_representative_targets');

        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*.id' => 'nullable|exists:representative_targets,id',
            'targets.*.representatives_count' => 'required|integer|min:0',
            'targets.*.bonus_amount' => 'required|numeric|min:0',
            'targets.*.notes' => 'nullable|string|max:500'
        ]);

        $updatedCount = 0;
        $currentYear = now()->year;
        $currentMonth = now()->month;

        foreach ($request->input('targets') as $targetData) {
            if (isset($targetData['id'])) {
                // Update existing target
                $target = RepresentativeTarget::find($targetData['id']);
                if ($target) {
                    $target->update([
                        'representatives_count' => $targetData['representatives_count'],
                        'bonus_amount' => $targetData['bonus_amount'],
                        'notes' => $targetData['notes']
                    ]);
                    $updatedCount++;
                }
            } else {
                // Create new target
                RepresentativeTarget::create([
                    'representatives_count' => $targetData['representatives_count'],
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

        $target = RepresentativeTarget::findOrFail($id);
        $target->delete();

        return back()->with('success', 'تم حذف الهدف بنجاح!');
    }

    public function processBonuses(Request $request)
    {
        $this->authorize('edit_representative_targets');

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $processedCount = RepresentativeTarget::processAllTargets($year, $month);

        return back()->with('success', "تم معالجة {$processedCount} هدف وإضافة المكافآت للموظفين المؤهلين!");
    }
}
