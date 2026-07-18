# Changelog

All notable changes to this plugin are documented in this file.

## [1.2.4] - 2026-07-18

- Addressed WordPress Plugin Check findings ahead of directory submission: removed the deprecated `load_plugin_textdomain()` call (WordPress auto-loads translations for hosted plugins since 4.6), updated the readme "Tested up to" header to 7.0, and excluded `.gitattributes` and `README.it.md` from the release zip built by the GitHub Actions workflow.

## [1.2.3] - 2026-07-18

- Renamed the main plugin file from `index.php` to `wp-simplealexa.php`.
- Added `README.md` / `README.it.md` for GitHub, this `CHANGELOG.md`, a GitHub Actions release workflow (auto-builds a zip and cuts a GitHub release when the plugin version bumps), and a Dependabot config for GitHub Actions updates.

## [1.2.2] - 2025-04-28

- Reverted the pretty-permalink rewrite rule added in 1.2.1.
- `GitHub Plugin URI` header changed from the full URL to the `owner/repo` shorthand form.

## [1.2.1] - 2025-04-28

- Added a pretty-permalink rewrite rule so the REST endpoint is also reachable at `/simplealexa`.

## [1.2.0]

- Added textdomain loading function.
- Detailed readme.txt.
- Updated Git Updater compatibility.

## [1.1.0]

- Moved to REST API.
- Setting number of items via Dashboard.

## [1.0.0]

- Initial version with `the_posts` filter for the Alexa Flash Briefing feed.
