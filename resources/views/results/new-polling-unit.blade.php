@extends('layouts.app')

@section('title', 'Add New Polling Unit')

@section('content')
    <p class="crumb"><a href="{{ url('/') }}">&larr; Home</a></p>

    <h1>Add New Polling Unit</h1>
    <p class="lede">Register a polling unit under an existing ward and record its score for all nine parties.</p>

    @if (session('success'))
        <div class="notice notice-ok">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice notice-bad">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('results.new-polling-unit.store') }}">
        @csrf

        <div class="card">
            <p class="section-title">Where is this polling unit?</p>
            <p class="section-note">Choose the ward the new unit belongs to.</p>

            <div class="field">
                <label for="state">State</label>
                <select id="state">
                    <option value="">-- Select State --</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->state_id }}">{{ $state->state_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="lga">Local Government</label>
                <select id="lga" disabled>
                    <option value="">-- Select LGA --</option>
                </select>
                <input type="hidden" name="lga_id" id="lga_id_hidden" value="{{ old('lga_id') }}">
            </div>

            <div class="field">
                <label for="ward">Ward</label>
                <select id="ward" name="ward_uniqueid" disabled required>
                    <option value="">-- Select Ward --</option>
                </select>
            </div>
        </div>

        <div class="card">
            <p class="section-title">Polling unit details</p>
            <p class="section-note">The name is how the unit will appear in the lookup page.</p>

            <div class="field">
                <label for="polling_unit_name">Polling Unit Name</label>
                <input type="text" id="polling_unit_name" name="polling_unit_name"
                       value="{{ old('polling_unit_name') }}" required maxlength="50"
                       placeholder="e.g. Ward 5 Primary School">
            </div>

            <div class="field">
                <label for="polling_unit_number">
                    Polling Unit Number <span class="hint">(optional)</span>
                </label>
                <input type="text" id="polling_unit_number" name="polling_unit_number"
                       value="{{ old('polling_unit_number') }}" maxlength="50"
                       placeholder="e.g. 8888">
            </div>
        </div>

        <div class="card">
            <p class="section-title">Party scores</p>
            <p class="section-note">
                Enter the votes recorded for each party. Leave a party at 0 if it scored nothing.
            </p>

            <div class="party-grid">
                @foreach ($parties as $party)
                    <div>
                        <label for="score_{{ $party }}">{{ $party }}</label>
                        <input type="number" min="0" id="score_{{ $party }}"
                               name="scores[{{ $party }}]"
                               class="score-input"
                               value="{{ old('scores.'.$party, 0) }}" required>
                    </div>
                @endforeach
            </div>

            <p class="section-note" style="margin: 16px 0 0;">
                Total votes entered: <strong id="scoreTotal">0</strong>
            </p>
        </div>

        <button type="submit" id="submitBtn" disabled>Save Polling Unit</button>
        <span class="hint" id="submitHint" style="margin-left:10px;">Select a ward to continue.</span>
    </form>
@endsection

@section('scripts')
<script>
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const lgaHidden = document.getElementById('lga_id_hidden');
    const wardSelect = document.getElementById('ward');
    const submitBtn = document.getElementById('submitBtn');
    const submitHint = document.getElementById('submitHint');
    const scoreTotal = document.getElementById('scoreTotal');

    function updateSubmitState() {
        const ready = Boolean(wardSelect.value);
        submitBtn.disabled = !ready;
        submitHint.textContent = ready ? '' : 'Select a ward to continue.';
    }

    function updateScoreTotal() {
        const sum = [...document.querySelectorAll('.score-input')]
            .reduce((total, input) => total + (Number(input.value) || 0), 0);

        scoreTotal.textContent = num(sum);
    }

    stateSelect.addEventListener('change', async function () {
        resetSelect(lgaSelect, '-- Select LGA --');
        resetSelect(wardSelect, '-- Select Ward --');
        lgaHidden.value = '';
        updateSubmitState();

        if (!this.value) return;

        const lgas = await getJson(`/api/lgas/${this.value}`);
        fillSelect(lgaSelect, lgas, l => l.lga_id, l => l.lga_name);
    });

    lgaSelect.addEventListener('change', async function () {
        resetSelect(wardSelect, '-- Select Ward --');
        lgaHidden.value = this.value;
        updateSubmitState();

        if (!this.value) return;

        const wards = await getJson(`/api/wards/${this.value}`);
        fillSelect(wardSelect, wards, w => w.uniqueid, w => w.ward_name);
    });

    wardSelect.addEventListener('change', updateSubmitState);

    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('input', updateScoreTotal);
    });

    updateScoreTotal();
    autoSelectIfOnlyOption(stateSelect);
</script>
@endsection
