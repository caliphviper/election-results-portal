@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Election Results Portal</h1>
    <p class="lede">
        View, summarise and record polling unit results from the 2011 Delta State dataset.
    </p>

    <a class="card" href="{{ route('results.lookup') }}" style="display:block; text-decoration:none; color:inherit;">
        <p class="section-title" style="color: var(--accent);">1 &middot; Polling Unit Lookup</p>
        <p class="section-note" style="margin:0;">
            Drill down through State, LGA, Ward and Polling Unit to see the individual party scores announced at that unit.
        </p>
    </a>

    <a class="card" href="{{ route('results.lga-summary') }}" style="display:block; text-decoration:none; color:inherit;">
        <p class="section-title" style="color: var(--accent);">2 &middot; LGA Result Summary</p>
        <p class="section-note" style="margin:0;">
            Total up every polling unit result within a local government, with the officially announced figures shown alongside for comparison.
        </p>
    </a>

    <a class="card" href="{{ route('results.new-polling-unit') }}" style="display:block; text-decoration:none; color:inherit;">
        <p class="section-title" style="color: var(--accent);">3 &middot; Add New Polling Unit</p>
        <p class="section-note" style="margin:0;">
            Register a polling unit under an existing ward and record its score for all nine parties in one submission.
        </p>
    </a>

    <p class="muted" style="font-size:13px; margin-top:28px;">
        Built with Laravel. The dataset covers Delta State only, so it is the sole state offered in the selectors.
    </p>
@endsection
