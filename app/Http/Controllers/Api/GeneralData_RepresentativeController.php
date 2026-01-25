<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Location;
use App\Models\Governorate;

class GeneralData_RepresentativeController extends Controller
{
    public function companies()
    {
        return response()->json([
            'success' => true,
            'data'    => Company::all()
        ]);
    }

    public function locations()
    {
        return response()->json([
            'success' => true,
            'data'    => Location::all()
        ]);
    }

    public function governorates()
    {
        return response()->json([
            'success' => true,
            'data'    => Governorate::all()
        ]);
    }
}
