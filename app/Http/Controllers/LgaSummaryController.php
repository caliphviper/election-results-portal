<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\AnnouncedPuResult;

class LgaSummaryController extends Controller
{
    public function index()
    {
        $states = State::orderBy('state_name')->get();

        return view('results.lga-summary', compact('states'));
    }

    public function summaryByLga($lga)
    {
        $summary = AnnouncedPuResult::join('polling_unit', 'polling_unit.uniqueid', '=', 'announced_pu_results.polling_unit_uniqueid')
            ->where('polling_unit.lga_id', $lga)
            ->selectRaw('announced_pu_results.party_abbreviation, SUM(announced_pu_results.party_score) as total_score')
            ->groupBy('announced_pu_results.party_abbreviation')
            ->orderBy('announced_pu_results.party_abbreviation')
            ->get();

        return response()->json($summary);
    }
}