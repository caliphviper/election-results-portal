@extends('layouts.app')

@section('title', 'Polling Unit Lookup')

@section('content')
    <p class="crumb"><a href="{{ url('/') }}">&larr; Home</a></p>

    <h1>Polling Unit Lookup</h1>
    <p class="lede">Work down from State to Polling Unit to see the party scores announced at that unit.</p>

    <div class="card">
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
        </div>

        <div class="field">
            <label for="ward">Ward</label>
            <select id="ward" disabled>
                <option value="">-- Select Ward --</option>
            </select>
        </div>

        <div class="field">
            <label for="pollingUnit">Polling Unit</label>
            <select id="pollingUnit" disabled>
                <option value="">-- Select Polling Unit --</option>
            </select>
        </div>
    </div>

    <div id="resultsContainer"></div>
@endsection

@section('scripts')
<script>
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const wardSelect = document.getElementById('ward');
    const puSelect = document.getElementById('pollingUnit');
    const resultsContainer = document.getElementById('resultsContainer');

    function clearFrom(...selects) {
        selects.forEach(([select, placeholder]) => resetSelect(select, placeholder));
        resultsContainer.innerHTML = '';
    }

    stateSelect.addEventListener('change', async function () {
        clearFrom([lgaSelect, '-- Select LGA --'], [wardSelect, '-- Select Ward --'], [puSelect, '-- Select Polling Unit --']);

        if (!this.value) return;

        try {
            const lgas = await getJson(`/api/lgas/${this.value}`);
            fillSelect(lgaSelect, lgas, l => l.lga_id, l => l.lga_name);
        } catch (e) {
            showError(resultsContainer, e);
        }
    });

    lgaSelect.addEventListener('change', async function () {
        clearFrom([wardSelect, '-- Select Ward --'], [puSelect, '-- Select Polling Unit --']);

        if (!this.value) return;

        try {
            const wards = await getJson(`/api/wards/${this.value}`);
            fillSelect(wardSelect, wards, w => w.uniqueid, w => w.ward_name);
        } catch (e) {
            showError(resultsContainer, e);
        }
    });

    wardSelect.addEventListener('change', async function () {
        clearFrom([puSelect, '-- Select Polling Unit --']);

        if (!this.value) return;

        try {
            const units = await getJson(`/api/polling-units/${this.value}`);
            fillSelect(
                puSelect,
                units,
                u => u.uniqueid,
                u => u.polling_unit_name || u.polling_unit_number || `Polling Unit ${u.polling_unit_id}`
            );
        } catch (e) {
            showError(resultsContainer, e);
        }
    });

    puSelect.addEventListener('change', async function () {
        resultsContainer.innerHTML = '';

        if (!this.value) return;

        const unitName = this.options[this.selectedIndex].textContent;
        const wardName = wardSelect.options[wardSelect.selectedIndex].textContent;
        const lgaName = lgaSelect.options[lgaSelect.selectedIndex].textContent;

        showLoading(resultsContainer, 'Loading results...');

        let results;

        try {
            results = await getJson(`/api/polling-unit-results/${this.value}`);
        } catch (e) {
            showError(resultsContainer, e);
            return;
        }

        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="card">
                    <p class="section-title">${unitName}</p>
                    <p class="empty">No results have been announced for this polling unit yet.</p>
                </div>`;
            return;
        }

        const total = results.reduce((sum, r) => sum + Number(r.party_score), 0);
        const top = Math.max(...results.map(r => Number(r.party_score)));

        const rows = results
            .slice()
            .sort((a, b) => b.party_score - a.party_score)
            .map(r => {
                const score = Number(r.party_score);
                const share = total ? (score / total * 100) : 0;
                const width = top ? (score / top * 100) : 0;

                return `
                    <tr class="${score === top ? 'leader' : ''}">
                        <td>${r.party_abbreviation}</td>
                        <td class="num">${num(score)}</td>
                        <td class="num">${share.toFixed(1)}%</td>
                        <td class="bar-cell"><div class="bar" style="width:${width}%"></div></td>
                    </tr>`;
            })
            .join('');

        resultsContainer.innerHTML = `
            <div class="card">
                <p class="section-title">${unitName}</p>
                <p class="section-note">${wardName} Ward &middot; ${lgaName} LGA</p>
                <table>
                    <thead>
                        <tr>
                            <th>Party</th>
                            <th class="num">Score</th>
                            <th class="num">Share</th>
                            <th class="bar-cell"></th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                    <tfoot>
                        <tr>
                            <td>Total votes</td>
                            <td class="num">${num(total)}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>`;
    });

    autoSelectIfOnlyOption(stateSelect);
</script>
@endsection
