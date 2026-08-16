<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Polling Unit</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; }
        select, input { width: 100%; padding: 8px; margin-bottom: 15px; box-sizing: border-box; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        .party-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .party-row label { width: 80px; margin: 0; }
        .party-row input { margin: 0; }
        button { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:disabled { background: #999; cursor: not-allowed; }
        .success { background: #dcfce7; border: 1px solid #16a34a; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #fee2e2; border: 1px solid #dc2626; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        fieldset { border: 1px solid #ccc; border-radius: 4px; margin-top: 20px; padding: 15px; }
    </style>
</head>
<body>
    <h1>Add New Polling Unit</h1>
    <p><a href="{{ route('results.lookup') }}">&larr; Back to Polling Unit Lookup</a></p>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('results.new-polling-unit.store') }}">
        @csrf

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
        <input type="hidden" name="lga_id" id="lga_id_hidden">

        <label for="ward">Ward</label>
        <select id="ward" name="ward_uniqueid" disabled required>
            <option value="">-- Select Ward --</option>
        </select>

        <label for="polling_unit_name">Polling Unit Name</label>
        <input type="text" id="polling_unit_name" name="polling_unit_name" required maxlength="50">

        <label for="polling_unit_number">Polling Unit Number (optional)</label>
        <input type="text" id="polling_unit_number" name="polling_unit_number" maxlength="50">

        <fieldset>
            <legend><strong>Party Scores</strong></legend>
            @foreach ($parties as $party)
                <div class="party-row">
                    <label for="score_{{ $party }}">{{ $party }}</label>
                    <input type="number" min="0" id="score_{{ $party }}" name="scores[{{ $party }}]" value="0" required>
                </div>
            @endforeach
        </fieldset>

        <button type="submit" id="submitBtn" disabled>Save Polling Unit</button>
    </form>

    <script>
        const lgaSelect = document.getElementById('lga');
        const lgaHidden = document.getElementById('lga_id_hidden');
        const wardSelect = document.getElementById('ward');
        const submitBtn = document.getElementById('submitBtn');

        function resetSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        function updateSubmitState() {
            submitBtn.disabled = !wardSelect.value;
        }

        document.getElementById('state').addEventListener('change', async function () {
            resetSelect(lgaSelect, '-- Select LGA --');
            resetSelect(wardSelect, '-- Select Ward --');
            lgaHidden.value = '';
            updateSubmitState();

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
            lgaHidden.value = this.value;
            updateSubmitState();

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

        wardSelect.addEventListener('change', updateSubmitState);
    </script>
</body>
</html>
