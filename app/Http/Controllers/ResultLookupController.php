<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Lga;
use App\Models\Ward;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResult;

class ResultLookupController extends Controller
{
    public function index()
    {
        $states = State::orderBy('state_name')->get();

        return view('results.lookup', compact('states'));
    }

    public function lgasByState($state)
    {
        $lgas = Lga::where('state_id', $state)
            ->orderBy('lga_name')
            ->get(['uniqueid', 'lga_id', 'lga_name']);

        return response()->json($lgas);
    }

    public function wardsByLga($lga)
    {
        $wards = Ward::where('lga_id', $lga)
            ->orderBy('ward_name')
            ->get(['uniqueid', 'ward_id', 'ward_name']);

        return response()->json($wards);
    }

    public function pollingUnitsByWard($ward)
{
    $pollingUnits = PollingUnit::where('uniquewardid', $ward)
        ->orderBy('polling_unit_name')
        ->get(['uniqueid', 'polling_unit_id', 'polling_unit_name', 'polling_unit_number']);

    return response()->json($pollingUnits);
}

    public function resultsByPollingUnit($pollingUnit)
    {
        $results = AnnouncedPuResult::where('polling_unit_uniqueid', $pollingUnit)
            ->orderBy('party_abbreviation')
            ->get(['party_abbreviation', 'party_score']);

        return response()->json($results);
    }
}