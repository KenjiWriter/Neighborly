<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationOptionsController extends Controller
{
    public function communities()
    {
        return response()->json(
            \App\Models\Community::select('id', 'name')->orderBy('name')->get()
        );
    }

    public function buildings(Request $request)
    {
        $request->validate(['community_id' => 'required|exists:communities,id']);

        return response()->json(
            \App\Models\Building::where('community_id', $request->community_id)
                ->select('id', 'name', 'community_id')
                ->orderBy('name')
                ->get()
        );
    }

    public function units(Request $request)
    {
        $request->validate(['building_id' => 'required|exists:buildings,id']);

        return response()->json(
            \App\Models\Unit::where('building_id', $request->building_id)
                ->select('id', 'label', 'building_id', 'community_id')
                ->orderBy('label')
                ->get()
        );
    }
}
