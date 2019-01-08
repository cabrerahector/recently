=== Recently ===
Contributors: hcabrera
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PASXEM2E7JUVC
Tags: recent, posts, widget, recently
Requires at least: 4.7
Tested up to: 4.9.7
Requires PHP: 5.2
Stable tag: 2.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A highly customizable, feature-packed Recent Posts widget!

== Description ==

Let's face it: WordPress' default Recent Posts widget does the job as promised but it's not very flexible. Things like excluding certain entries from the list or displaying recent posts by category can't be done with the stock Recent Posts widget. Therefore, let me introduce Recently.

Recently is a WordPress widget that displays your site's most recent posts. And it doesn't stop there:

= Main Features =
* **Thumbnails support!** (*see the [FAQ section](https://github.com/cabrerahector/recently/wiki/2.-FAQ) for technical requirements*).
* **[Use your own layout](https://github.com/cabrerahector/recently/wiki/2.-FAQ#how-can-i-use-my-own-html-markup-with-your-plugin)**! Control how your recent posts are shown on your theme.
* **Custom Post-type support**. Wanna show other stuff than just posts and pages?
* **Categories, tags, and custom taxonomies support!**
* **[WordPress Popular Posts](https://wordpress.org/extend/plugins/wordpress-popular-posts/)** / **[WP-PostViews](https://wordpress.org/extend/plugins/wp-postratings/)** / **[Top 10](https://wordpress.org/plugins/top-10/)** **support**: display the number of page views of your posts!
* **[Polylang](https://wordpress.org/plugins/polylang/)/[WPML](https://wpml.org/) support**!
* **[WP-PostRatings](http://wordpress.org/extend/plugins/wp-postratings/) support**. Show your visitors how your readers are rating your posts!
* **WordPress Multisite support**!

== Installation ==

Please make sure your site meets the [minimum requirements](https://github.com/cabrerahector/recently#requirements) before proceeding.

= Automatic installation =

1. Log in into your WordPress dashboard.
2. Go to Plugins > Add New.
3. In the "Search Plugins" field, type in **Recently** and hit Enter.
4. Find the plugin in the search results list and click on the "Install Now" button.

= Manual installation =

1. Download the plugin and extract its contents.
2. Upload the `recently` folder to the `/wp-content/plugins/` directory.
3. Activate the **Recently** plugin through the "Plugins" menu in WordPress.

= Done! What's next? =

1. Go to *Appearance > Widgets*, drag the *Recently* widget onto your sidebar, configure it, and when you're done click on *Save*.
2. If you have a caching plugin installed on your site, flush its cache now. Then, go to *Settings > Recently* and enable the *Ajaxify widget* feature.

That's it!

== Frequently Asked Questions ==

The FAQ section has been moved [here](https://github.com/cabrerahector/recently/wiki/2.-FAQ).

== Screenshots ==

1. Widgets Control Panel.
2. Recently Widget.
3. Recently Widget with custom HTML.

== Changelog ==

= 2.0.2 =
*If you're using a caching plugin, flushing its cache after upgrading to this version is highly recommended.*

* Fixes Content Tag `{author}` returning a bad URL.
* Data Caching enabled by default (new installs only).
* Minor copy adjustments.

= 2.0.1 =
* Fixes missing helper method messing up the Customizer.
* Updates default widget stylesheet.
* Other minor fixes and improvements.

= 2.0.0 =
*If you're using a caching plugin, flushing its cache after upgrading to this version is highly recommended.*

* Plugin code refactored!
* Improves PHP7+ compatibility.
* Improves compatibility with WordPress' Customizer.
* Drops jQuery dependency: your site will load a tab bit faster now.
* Adds Relative Date Format.
* Adds ability to select first attached image as thumbnail source.
* New filters: `recently_post_class` & `recently_post_exclude_terms`.
* Improves compatibility with WP-SpamShield, Polylang and WPML.
* Tons of minor bug fixes and improvements.

See: [Release Notes](https://cabrerahector.com/wordpress/recently-2-0-is-out/).

== Upgrade Notice ==
= 2.0.2 =
If you're using a caching plugin, flushing its cache after upgrading to this version is highly recommended.
