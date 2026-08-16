<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Election Results Portal</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 60px auto; line-height: 1.6; color: #222; }
        h1 { margin-bottom: 5px; }
        .subtitle { color: #666; margin-top: 0; margin-bottom: 30px; }
        .card { display: block; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 16px; text-decoration: none; color: #222; transition: border-color 0.15s; }
        .card:hover { border-color: #2563eb; }
        .card h2 { margin: 0 0 6px 0; font-size: 18px; color: #2563eb; }
        .card p { margin: 0; color: #555; font-size: 14px; }
        footer { margin-top: 40px; color: #999; font-size: 13px; }
    </style>
</head>
<body>
    <h1>Election Results Portal</h1>
    <p class="subtitle">A demo built to view, summarize, and record polling unit results for Delta State (2011 dummy election data).</p>

    <a class="card" href="{{ route('results.lookup') }}">
        <h2>1. Polling Unit Lookup</h2>
        <p>Select a State, LGA, Ward, and Polling Unit to view its individual party results.</p>
    </a>

    <a class="card" href="{{ route('results.lga-summary') }}">
        <h2>2. LGA Result Summary</h2>
        <p>Select a State and LGA to view the summed total results across all polling units in that LGA.</p>
    </a>

    <a class="card" href="{{ route('results.new-polling-unit') }}">
        <h2>3. Add New Polling Unit</h2>
        <p>Register a new polling unit under a Ward and record its party results.</p>
    </a>

    <footer>Built with Laravel &middot; Data sourced from Bincom's 2011 dummy election dataset.</footer>
</body>
</html>