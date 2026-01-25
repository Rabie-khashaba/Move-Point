<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class Supervisor_InterviewController extends Controller
{
    public function supervisorInterviews($supervisorId)
    {
        $interviews = Interview::with('lead')
            ->where('supervisor_id', $supervisorId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $interviews
        ]);
    }


    public function updateInterviewStatus(Request $request, $interviewId)
    {
        $request->validate([
            'status' => 'required|string|in:مقابلة,نجاح,فشل,مؤجل' // حسب الحالات اللي عندك
        ]);

        $interview = Interview::findOrFail($interviewId);

        $interview->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المقابلة بنجاح',
            'interview' => $interview
        ]);
    }


}
