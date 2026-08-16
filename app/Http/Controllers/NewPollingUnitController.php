<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResult;
use Illuminate\Support\Facades\DB;

class NewPollingUnitController extends Controller
{
    protected array $parties = ['PDP', 'DPP', 'ACN', 'PPA', 'CDC', 'JP', 'ANPP', 'LABO', 'CPP'];

    public function create()
    {
        $states = State::orderBy('state_name')->get();

        return view('results.new-polling-unit', [
            'states' => $states,
            'parties' => $this->parties,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ward_uniqueid' => 'required|integer|exists:ward,uniqueid',
            'lga_id' => 'required|integer',
            'polling_unit_name' => 'required|string|max:50',
            'polling_unit_number' => 'nullable|string|max:50',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            // work out the next polling_unit_id within this ward
            $nextId = PollingUnit::where('uniquewardid', $validated['ward_uniqueid'])
                ->max('polling_unit_id');
            $nextId = $nextId ? $nextId + 1 : 1;

            $pollingUnit = PollingUnit::create([
                'polling_unit_id' => $nextId,
                'ward_id' => 0, // matches existing dump convention -- uniquewardid is the real link
                'lga_id' => $validated['lga_id'],
                'uniquewardid' => $validated['ward_uniqueid'],
                'polling_unit_number' => $validated['polling_unit_number'],
                'polling_unit_name' => $validated['polling_unit_name'],
            ]);

            foreach ($validated['scores'] as $party => $score) {
                AnnouncedPuResult::create([
                    'polling_unit_uniqueid' => $pollingUnit->uniqueid,
                    'party_abbreviation' => $party,
                    'party_score' => $score,
                    'entered_by_user' => 'web_form',
                    'date_entered' => now(),
                    'user_ip_address' => request()->ip(),
                ]);
            }
        });

        return redirect()->route('results.new-polling-unit')->with('success', 'Polling unit and results saved successfully.');
    }
}