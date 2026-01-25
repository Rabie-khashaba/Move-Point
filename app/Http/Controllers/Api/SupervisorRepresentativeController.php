<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorRepresentativeController extends Controller
{
    public function active($supervisorId)
    {
        // IDs المندوبين المرتبطين
        $assigned = \App\Models\SupervisorRepresentative::where('supervisor_id', $supervisorId)
            ->pluck('representative_id');

        // المندوبين الفعليين فقط
        $representatives = \App\Models\Representative::whereIn('id', $assigned)
            ->where('status', 1)
            ->get();

        return response()->json([
            'success' => true,
            'type' => 'active_representatives',
            'data' => $representatives
        ]);
    }

    public function incomplete($supervisorId)
    {
        // IDs المندوبين المرتبطين
        $assigned = \App\Models\SupervisorRepresentative::where('supervisor_id', $supervisorId)
            ->pluck('representative_id');

        // المندوبين غير المكتملين فقط
        $representatives = \App\Models\Representative::whereIn('id', $assigned)
            ->where('status', 0)
            ->get();

        return response()->json([
            'success' => true,
            'type' => 'incomplete_representatives',
            'data' => $representatives
        ]);
    }


}
