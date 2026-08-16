<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Polling Unit Result Lookup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; }
        select { width: 100%; padding: 8px; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Polling Unit Result Lookup</h1>

    <label for="state">State</label>
    <select id="state">
        <option value="">-- Select State --</option>
        @foreach ($states as $state)
            <option value="{{ $state->state_id }}">{{ $state->state_name }}</option>
        @endforeach
    </select>

    <label for="lga">LGA</label>
    <select id="lga" disabled>
        <option value="">-- Select LGA --</option>
    </select>

    <label for="ward">Ward</label>
    <select id="ward" disabled>
        <option value="">-- Select Ward --</option>
    </select>

    <label for="pollingUnit">Polling Unit</label>
    <select id="pollingUnit" disabled>
        <option value="">-- Select Polling Unit --</option>
    </select>

    <div id="resultsContainer"></div>

    <script>
        const lgaSelect = document.getElementById('lga');
        const wardSelect = document.getElementById('ward');
        const puSelect = document.getElementById('pollingUnit');
        const resultsContainer = document.getElementById('resultsContainer');

        function resetSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        document.getElementById('state').addEventListener('change', async function () {
            resetSelect(lgaSelect, '-- Select LGA --');
            resetSelect(wardSelect, '-- Select Ward --');
            resetSelect(puSelect, '-- Select Polling Unit --');
            resultsContainer.innerHTML = '';

            if (!this.value) return;

            const res = await fetch(`/api/lgas/${this.value}`);
            const lgas = await res.json();

            lgas.forEach(lga => {
                const opt = document.createElement('option');
                opt.value = lga.lga_id;
                opt.textContent = lga.lga_name;
                lgaSelect.appendChild(opt);
            });
            lgaSelect.disabled = false;
        });

        lgaSelect.addEventListener('change', async function () {
            resetSelect(wardSelect, '-- Select Ward --');
            resetSelect(puSelect, '-- Select Polling Unit --');
            resultsContainer.innerHTML = '';

            if (!this.value) return;

            const res = await fetch(`/api/wards/${this.value}`);
            const wards = await res.json();

            wards.forEach(ward => {
                const opt = document.createElement('option');
                opt.value = ward.uniqueid;
                opt.textContent = ward.ward_name;
                wardSelect.appendChild(opt);
            });
            wardSelect.disabled = false;
        });

        wardSelect.addEventListener('change', async function () {
            resetSelect(puSelect, '-- Select Polling Unit --');
            resultsContainer.innerHTML = '';

            if (!this.value) return;

            const res = await fetch(`/api/polling-units/${this.value}`);
            const units = await res.json();

            units.forEach(pu => {
                const opt = document.createElement('option');
                opt.value = pu.uniqueid;
                opt.textContent = pu.polling_unit_name ?? pu.polling_unit_number ?? `PU #${pu.polling_unit_id}`;
                puSelect.appendChild(opt);
            });
            puSelect.disabled = false;
        });

        puSelect.addEventListener('change', async function () {
            resultsContainer.innerHTML = '';

            if (!this.value) return;

            const res = await fetch(`/api/polling-unit-results/${this.value}`);
            const results = await res.json();

            if (results.length === 0) {
                resultsContainer.innerHTML = '<p>No results found for this polling unit.</p>';
                return;
            }

            let html = '<table><thead><tr><th>Party</th><th>Score</th></tr></thead><tbody>';
            results.forEach(r => {
                html += `<tr><td>${r.party_abbreviation}</td><td>${r.party_score}</td></tr>`;
            });
            html += '</tbody></table>';
            resultsContainer.innerHTML = html;
        });
    </script>
</body>
</html>