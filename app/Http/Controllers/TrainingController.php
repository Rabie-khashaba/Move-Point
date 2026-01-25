<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RepresentativeTraining;

class TrainingController extends Controller
{
    public function index()
    {
        $this->authorize('view_trainings');
        $trainings = RepresentativeTraining::with('representative')->latest()->paginate(20);
        return view('trainings.index', compact('trainings'));
    }

    public function update(Request $request, RepresentativeTraining $training)
    {
        $this->authorize('edit_trainings');
        $data = $request->validate([
            'is_completed' => 'required|boolean',
        ]);
        $training->update([
            'is_completed' => $data['is_completed'],
            'completed_at' => $data['is_completed'] ? now() : null,
        ]);
        return back()->with('success', 'تم تحديث حالة التدريب');
    }
}


