# Simple Alexa News Briefing

*[README disponibile anche in italiano](README.it.md)*

A modern, simple REST API–based WordPress plugin for creating Alexa flash briefing skills directly from your latest posts. Based on the original source code by Francesco Napoletano.

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher

## Installation

1. Copy the whole plugin folder to `wp-content/plugins/wp-simplealexa/` on your WordPress site.
2. Go to **Plugins** in the WordPress dashboard and activate "Simple Alexa News Briefing".
3. Go to **Settings → Alexa Briefing** and set the default number of items.

## Usage

The plugin exposes your latest published posts as a JSON feed at:

```
https://your-site.com/wp-json/simplealexa/v1/briefing
```

Each item includes a `uid`, `updateDate`, `titleText`, and `mainText` (title + excerpt), matching Alexa Flash Briefing skill requirements.

Use the `count` query parameter to override the configured default for a single request, e.g. `...?count=3`.

## Configuration

Go to **Settings → Alexa Briefing** and set the default number of items returned when `count` is not passed in the request.

## Language

The plugin interface is in English by default; upload your own `.po`/`.mo` translation files to the `languages/` folder to localize it (text domain: `simplealexa`).

## Updates

The plugin is compatible with [Git Updater](https://git-updater.com/), so it can be kept up to date directly from the [GitHub repository](https://github.com/gioxx/wp-simplealexa) without going through WordPress.org.
