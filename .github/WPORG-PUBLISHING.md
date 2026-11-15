# Publishing on WordPress.org: open points

Read this before submitting Simple Alexa News Briefing to the WordPress.org plugin directory.
Written on 2026-11-15, after Reloaded Social Icons went live; check each point against the
current code before acting on it.

## 1. Pick one slug and use it everywhere

The plugin currently has three different identifiers:

| Where | Value | Problem |
|---|---|---|
| `.github/workflows/deploy.yml` (`SLUG`) | `wp-simplealexa` | WordPress.org rejects slugs starting with `wp-` |
| Text domain (`wp-simplealexa.php` header + 22 `'simplealexa'` calls) | `simplealexa` | must equal the directory slug |
| Gallery page on wordpress.gxware.org | `simple-alexa-news-briefing` | derived from the plugin name |

Proposed slug: **`simple-alexa-news-briefing`**. WordPress.org generates slugs from the plugin
name, and the gallery page already uses it. The reviewer has the final say: the approval email
states the assigned slug. Use that one.

Once the slug is confirmed:

- [ ] Text domain: change the `Text Domain:` header and every `'simplealexa'` string in PHP to the slug.
- [ ] `deploy.yml`: set `SLUG` to the slug.
- [ ] Keep the plugin folder and main file name (`wp-simplealexa/wp-simplealexa.php`) as they are,
      so installs updated through Git Updater keep working. The GitHub release zip keeps its
      current name too.
- [ ] Gallery site (repo `wordpress.gioxx.org`, `src/data/plugins.ts`): if the assigned slug is not
      `simple-alexa-news-briefing`, rename the page and add a redirect route with `statusCode: 301`
      from the old URL. Then set the `wporg` field so the card gets the WordPress.org badge and
      shows up under the `?source=wporg` filter.

## 2. Bring the workflows in line with the other published plugins

Use `wp-tag-loom` (Tag Loom) and `wp-lsi-reloaded` (Reloaded Social Icons) as references.

- [ ] **deploy.yml** currently listens on `release: released`. That event never fires here: the
      release is created by `release.yml` with `GITHUB_TOKEN`, and events authored by that token
      don't trigger other workflows. Switch to `workflow_run` on `Release` plus
      `workflow_dispatch`. Read the version from the plugin header and deploy a clean staging
      directory through `BUILD_DIR`, excluding `.github/`, `.wordpress-org/`, `README.it.md` and
      the other dev-only files.
- [ ] **Deploy workflow is `disabled_manually`** on GitHub. Re-enable it
      (`gh workflow enable deploy.yml`) only after approval. If a release ran while it was off,
      dispatch it by hand with `-f tag=vX.Y.Z`.
- [ ] **assets.yml** is missing. Copy it from `wp-lsi-reloaded`, including the guard that publishes
      only when the readme's `Stable tag` already exists under `tags/` in SVN. Without the guard, a
      release commit can push the new readme before the code. Also give both workflows the shared
      `concurrency: wporg-svn` group.
- [ ] **release.yml**: exclude `.wordpress-org/` from the release zip.
- [ ] **Secrets**: the repository has **no** `SVN_USERNAME` / `SVN_PASSWORD` (checked 2026-11-15:
      0 secrets). Add them before the first deploy. The password is the SVN password generated in
      profile.wordpress.org → Account & Security, not the login password.

## 3. Directory assets

- [ ] Create `.wordpress-org/` with `banner-1544x500.png`, `banner-772x250.png`, `icon-128x128.png`,
      `icon-256x256.png`, optional `icon.svg`, and the screenshots listed in `readme.txt`.
      `assets/images/plugin-icon.svg` can be the starting point for the icon.
- [ ] If you add `icon.svg`, check it in a real browser: WordPress.org prefers it over the PNGs, and
      macOS Quick Look can render SVGs wrongly.
- [ ] Screenshots can be taken from a WordPress Playground instance (`@wp-playground/cli` with the
      repo mounted as a plugin) driven by Playwright.

## 4. Before submitting

- [ ] Run Plugin Check again: the last pass was at 1.2.4 (commit `6586609`), and the slug and
      text domain change will add new findings until every string is updated.
- [ ] License is MIT, which is GPL-compatible. The plugin is based on original code by Francesco
      Napoletano: confirm that code's license is GPL-compatible as well, and keep the attribution.
- [ ] `readme.txt`: `Tested up to` is `7.0`; bump it to the current WordPress release.
