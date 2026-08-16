# Election Results Portal

A Laravel-based web application for viewing, summarizing, and recording polling unit election results — built around a dataset of 2011 dummy election results for Delta State, Nigeria (polling units, wards, and LGAs).

## Overview

Nigeria's electoral results structure is hierarchical: **Polling Units** sit under **Wards**, which sit under **Local Government Areas (LGAs)**, which sit under **States**. This project provides three core tools for working with that data:

1. **Polling Unit Lookup** — Select a State, LGA, Ward, and Polling Unit (via chained dropdowns) to view that polling unit's individual party results.
2. **LGA Result Summary** — Select a State and LGA to view a summed total of all party scores across every polling unit in that LGA, computed live from polling-unit-level data (not from a pre-announced results table).
3. **Add New Polling Unit** — Register a new polling unit under a chosen Ward, and record its results for all parties in one submission.

## Tech Stack

- **Backend:** Laravel 12 (PHP)
- **Database:** SQLite (local development)
- **Frontend:** Blade templates, vanilla JavaScript (chained AJAX dropdowns)

## Data Model

The database schema follows the original dataset's structure:

| Table | Description |
|---|---|
| `states` | List of states |
| `lga` | Local Government Areas, linked to a state |
| `ward` | Wards, linked to an LGA |
| `polling_unit` | Polling units, linked to a ward (via `uniquewardid`) and LGA |
| `announced_pu_results` | Individual party scores per polling unit (9 rows per unit) |
| `announced_lga_results` | Pre-announced LGA-level results, used only as a separate cross-check dataset |

**Note:** `polling_unit.uniquewardid` — not `ward_id` — is the reliable foreign key linking a polling unit to its ward, since `ward_id` values are largely unset (`0`) in the source dataset.

## Setup

```bash
git clone https://github.com/caliphviper/election-results-portal.git
cd election-results-portal
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

In `.env`, set:



## PDB_CONNECTION=SQLITE

Then run migrations and seed the database:
```bash
php artisan migrate --seed
php artisan serve
```

Visit `http://localhost:8000` to view the homepage and navigate to each feature.

## Project Structure

- `app/Models/` — Eloquent models mapped to the existing (non-standard) table/column naming
- `app/Http/Controllers/` — one controller per feature (lookup, summary, new entry)
- `resources/views/results/` — Blade views for each feature page
- `database/seeders/` — seed data derived from the original dataset

## License

Built as a technical assessment project.
