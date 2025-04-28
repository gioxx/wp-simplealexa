=== Simple Alexa News Briefing ===
Contributors: gioxx
GitHub Plugin URI: https://github.com/gioxx/wp-simplealexa
Tags: alexa, flash-briefing, rest-api, news, briefing
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.0
License: MIT
License URI: https://opensource.org/licenses/MIT

A modern, simple REST API–based WordPress plugin for creating Alexa flash briefing skills directly from your latest posts.

== Description ==
Simple Alexa News Briefing lets you expose your latest WordPress posts as an Alexa Flash Briefing feed via a REST endpoint. Customize the number of items, translated strings, and more.

Features:
* Uses WP REST API (`/wp-json/simplealexa/v1/briefing`)
* Administrator setting for default items count
* Optional `?count=<number>` query parameter
* Internationalization ready (load your own `.po`/`.mo` in `/languages`)
* Git Updater compatible for seamless updates from GitHub

== Installation ==
1. Upload the `wp-simplealexa` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings → Alexa Briefing** and set the default number of items.
4. Access your feed at:

   `https://your-site.com/wp-json/simplealexa/v1/briefing`

5. (Optional) Use the `count` query parameter to override default: `...?count=3`.

== Frequently Asked Questions ==
= How can I translate the plugin texts? =
Upload the `.po` and `.mo` files to the `languages/` folder within the plugin. The text domain is `simplealexa`..

= How do I force a different number of entries? =
You can use the setting in Dashboard **Settings → Alexa Briefing** or pass `?count=` in the REST API call.

== Screenshots ==
1. Plugin settings in the Dashboard.

== Changelog ==
= 1.2.0 =
* Added textdomain loading function.
* Detailed readme.txt.
* Updated Git Updater compatibility.

= 1.1.0 =
* Moving to REST API.
* Setting number of items via Dashboard.

= 1.0.0 =
* Initial version with `the_posts` filter for Alexa Flash Briefing feed.

== Upgrade Notice ==
= 1.2.0 =
Be sure to create the `languages/` folder if it does not exist to load translations.

= 1.1.0 =
Breaking: REST API endpoint now `/wp-json/simplealexa/v1/briefing` (no more filtering on `the_posts`).

