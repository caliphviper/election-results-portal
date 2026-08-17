<?php

namespace App\Http\Controllers;

use App\Models\AnnouncedLgaResult;
use App\Models\AnnouncedPuResult;
use App\Models\Lga;
use App\Models\State;

class LgaSummaryController extends Controller
{
    public function index()
    {
        return view('results.lga-summary', [
            'states' => State::withLgaData()->get(),
        ]);
    }

    public function summaryByLga($lga)
    {
        // Question 2 asks for the total summed from the individual polling unit
        // results, so this deliberately builds up from announced_pu_results
        // rather than reading announced_lga_results.
        $summary = AnnouncedPuResult::join('polling_unit', 'polling_unit.uniqueid', '=', 'announced_pu_results.polling_unit_uniqueid')
            ->where('polling_unit.lga_id', $lga)
            ->selectRaw('announced_pu_results.party_abbreviation, SUM(announced_pu_results.party_score) as total_score')
            ->groupBy('announced_pu_results.party_abbreviation')
            ->orderByDesc('total_score')
            ->get();

        // The announced figures are only ever used as a cross-check, shown
        // beside the summed total and never in place of it.
        $announced = AnnouncedLgaResult::where('lga_name', $lga)
            ->pluck('party_score', 'party_abbreviation');

        $pollingUnits = Lga::pollingUnitCount($lga);

        return response()->json([
            'polling_units' => $pollingUnits,
            'parties' => $summary->map(fn ($row) => [
                'party' => $row->party_abbreviation,
                'summed' => (int) $row->total_score,
                'announced' => isset($announced[$row->party_abbreviation])
                    ? (int) $announced[$row->party_abbreviation]
                    : null,
            ]),
        ]);
    }
}
