# Changelog

All notable changes to Habeas CLE.

The format follows [Keep a Changelog](https://keepachangelog.com/) and the project uses semantic versioning.

## [0.1.0] — 2026-06-23

First functional release. Implements every feature from the brief.

### Added

- **Custom Post Types** (7): Program, Week, Module, Practice Scenario, Template, Schedule Event, Case Update. Grouped into 2 capability groups (`content`, `case_update`).
- **Roles and capabilities:** CLE Student, CLE Instructor, and an extended Administrator. Custom caps `view_cle_content`, `reveal_model_answers`, `view_participant_progress`.
- **Per-program access control:** login gate + enrollment check (`hcle_can_access_post`). REST API protection. Search filtering.
- **Model answers** protected server-side via `[hcle_model_answer]`.
- **Hierarchical relationships** via post meta (Program → Week → Module → Scenario/Template; Event per week), with a select meta box, a "Parent" admin column, and a query API.
- **Progress tracking** (user meta) with per-week and per-program computation, REST endpoint `habeas-cle/v1/progress`, a live-updating frontend button, and per-week progress bars on the program page.
- **Per-program enrollment** (user meta) with a "Participants & Enrollment" management screen.
- **Session dates** on Schedule Events (datetime meta box + validation that rejects overflow).
- **Block theme** (child of Twenty Twenty-Five) with 7 `single-*` templates.
- **Dynamic blocks:** curriculum-children, progress-bar, complete-button, event-datetime, breadcrumbs, my-programs.
- **Front door** "My Training" (page + menu link) and navigation **breadcrumbs**.
- **Scripts:** `seed-demo.php` (idempotent sample data) and `setup-front-door.php`.

### Fixed

- The CPTs were registered with `public => false`, which left `publicly_queryable => false` and made the single views return 404 (the access gate never ran). Fixed with `publicly_queryable => true` + `exclude_from_search`.
