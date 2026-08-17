@extends('layouts.app')

@section('title', 'LGA Result Summary')

@section('content')
    <p class="crumb"><a href="{{ url('/') }}">&larr; Home</a></p>

    <h1>LGA Result Summary</h1>
    <p class="lede">
        Party totals for a local government, summed from every polling unit result recorded within it.
    </p>

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
    </div>

    <div id="resultsContainer"></div>
@endsection

@section('scripts')
<script>
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const resultsContainer = document.getElementById('resultsContainer');

    stateSelect.addEventListener('change', async function () {
        resetSelect(lgaSelect, '-- Select LGA --');
        resultsContainer.innerHTML = '';

        if (!this.value) return;

        try {
            const lgas = await getJson(`/api/lgas/${this.value}`);
            fillSelect(lgaSelect, lgas, l => l.lga_id, l => l.lga_name);
        } catch (e) {
            showError(resultsContainer, e);
        }
    });

    lgaSelect.addEventListener('change', async function () {
        resultsContainer.innerHTML = '';

        if (!this.value) return;

        const lgaName = this.options[this.selectedIndex].textContent;

        showLoading(resultsContainer, 'Adding up polling unit results...');

        let data;

        try {
            data = await getJson(`/api/lga-summary/${this.value}`);
        } catch (e) {
            showError(resultsContainer, e);
            return;
        }

        if (data.parties.length === 0) {
            resultsContainer.innerHTML = `
                <div class="card">
                    <p class="section-title">${lgaName} LGA</p>
                    <p class="empty">No polling unit results have been recorded in this LGA yet.</p>
                </div>`;
            return;
        }

        const total = data.parties.reduce((sum, p) => sum + p.summed, 0);
        const top = Math.max(...data.parties.map(p => p.summed));
        const hasAnnounced = data.parties.some(p => p.announced !== null);

        const rows = data.parties.map(p => {
            const share = total ? (p.summed / total * 100) : 0;
            const width = top ? (p.summed / top * 100) : 0;

            return `
                <tr class="${p.summed === top ? 'leader' : ''}">
                    <td>${p.party}</td>
                    <td class="num">${num(p.summed)}</td>
                    <td class="num">${share.toFixed(1)}%</td>
                    <td class="bar-cell"><div class="bar" style="width:${width}%"></div></td>
                </tr>`;
        }).join('');

        const unitCount = data.polling_units;

        let html = `
            <div class="card">
                <p class="section-title">${lgaName} LGA &mdash; summed polling unit results</p>
                <p class="section-note">
                    Totalled from ${unitCount} polling unit${unitCount === 1 ? '' : 's'} with recorded results.
                </p>
                <table>
                    <thead>
                        <tr>
                            <th>Party</th>
                            <th class="num">Total score</th>
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

        if (hasAnnounced) {
            const comparisonRows = data.parties.map(p => {
                if (p.announced === null) {
                    return `
                        <tr>
                            <td>${p.party}</td>
                            <td class="num">${num(p.summed)}</td>
                            <td class="num muted">not announced</td>
                            <td class="num muted">&mdash;</td>
                        </tr>`;
                }

                const diff = p.summed - p.announced;
                const sign = diff > 0 ? '+' : '';

                return `
                    <tr>
                        <td>${p.party}</td>
                        <td class="num">${num(p.summed)}</td>
                        <td class="num">${num(p.announced)}</td>
                        <td class="num" style="color: ${diff === 0 ? 'var(--ok)' : 'var(--muted)'}">${sign}${num(diff)}</td>
                    </tr>`;
            }).join('');

            html += `
                <div class="card">
                    <p class="section-title">Cross-check against the announced LGA figures</p>
                    <p class="section-note">
                        For comparison only &mdash; the summed total above is calculated from the polling unit
                        results, never from this table.
                    </p>
                    <table>
                        <thead>
                            <tr>
                                <th>Party</th>
                                <th class="num">Summed from PUs</th>
                                <th class="num">Announced at LGA</th>
                                <th class="num">Difference</th>
                            </tr>
                        </thead>
                        <tbody>${comparisonRows}</tbody>
                    </table>
                </div>`;
        }

        resultsContainer.innerHTML = html;
    });

    autoSelectIfOnlyOption(stateSelect);
</script>
@endsection
