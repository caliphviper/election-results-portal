<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LGA Result Summary</title>
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
    <h1>LGA Result Summary</h1>
    <p><a href="{{ route('results.lookup') }}">&larr; Back to Polling Unit Lookup</a></p>

    <label for="state">State</label>
    <select id="state">
        <option value="">-- Select State --</option>
        @foreach ($states as $state)
            <option value="{{ $state->state_id }}">{{ $state->state_name }}</option>
        @endforeach
    </select>

    <label for="lga">Local Government</label>
    <select id="lga" disabled>
        <option value="">-- Select LGA --</option>
    </select>

    <div id="resultsContainer"></div>

    <script>
        const lgaSelect = document.getElementById('lga');
        const resultsContainer = document.getElementById('resultsContainer');

        function resetSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        document.getElementById('state').addEventListener('change', async function () {
            resetSelect(lgaSelect, '-- Select LGA --');
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
            resultsContainer.innerHTML = '';

            if (!this.value) return;

            let summary;

            try {
                const res = await fetch(`/api/lga-summary/${this.value}`);

                if (!res.ok) {
                    throw new Error(`server returned ${res.status}`);
                }

                summary = await res.json();
            } catch (e) {
                resultsContainer.innerHTML = `<p style="color:#b00">Could not load the summary (${e.message}). Please try again.</p>`;
                return;
            }

            if (summary.length === 0) {
                resultsContainer.innerHTML = '<p>No results found for this LGA.</p>';
                return;
            }

            let html = '<table><thead><tr><th>Party</th><th>Total Score</th></tr></thead><tbody>';
            summary.forEach(r => {
                html += `<tr><td>${r.party_abbreviation}</td><td>${r.total_score}</td></tr>`;
            });
            html += '</tbody></table>';
            resultsContainer.innerHTML = html;
        });
    </script>
</body>
</html>