=== Recently ===
Contributors: hcabrera
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PASXEM2E7JUVC
Tags: recent, posts, widget, recently
Requires at least: 5.3
Tested up to: 6.0.2
Requires PHP: 7.2
Stable tag: 4.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A highly customizable, feature-packed Recent Posts widget!

== Description ==

Let's face it: WordPress' default Recent Posts widget does the job as promised but it's not very flexible. Things like excluding certain entries from the list or displaying recent posts by category can't be done with the stock Recent Posts widget. Therefore, let me introduce Recently.

Recently is a WordPress widget that displays your site's most recent posts. And it doesn't stop there:

= Main Features =
* **Multi-widget capable**. That is, you can have several Recently widgets on your blog - each with its own settings!
* **[Polylang](https://wordpress.org/plugins/polylang/)/[WPML 3.2+](https://wpml.org/) support** - Show the translated version of your recent posts!
* **WordPress Multisite support**.
* **[WordPress Popular Posts](https://wordpress.org/extend/plugins/wordpress-popular-posts/)** / **[WP-PostViews](https://wordpress.org/extend/plugins/wp-postratings/)** / **[Top 10](https://wordpress.org/plugins/top-10/)** **support**: - Display the views count of your recent posts!
* **Custom Post-type support**. - Want to show other stuff than just posts and pages, eg. Recent *Products*? [You can](https://github.com/cabrerahector/recently/wiki/3.-FAQ#i-want-to-have-a-recent-list-of-my-custom-post-type-how-can-i-do-that)!
* List recent posts filtered by categories, tags, or custom taxonomies!
* Display a **thumbnail** of your recent posts! (see the [FAQ section](https://github.com/cabrerahector/recently/wiki/3.-FAQ#how-does-recently-pick-my-posts-thumbnails) for more details.)
* Use **your own layout**! Recently is flexible enough to let you customize the look and feel of your recent posts list! (see [customizing Recently's HTML markup](https://github.com/cabrerahector/recently/wiki/3.-FAQ#how-can-i-use-my-own-html-markup-with-your-plugin) and [How to style Recently](https://github.com/cabrerahector/recently/wiki/4.-Styling-the-list) for more.)
* **Localizable** to your own language (See [translating Recently into your language](https://github.com/cabrerahector/recently/wiki/3.-FAQ#i-want-to-translate-your-plugin-into-my-language--help-you-update-a-translation-what-do-i-need-to-do) for more info).
* **[WP-PostRatings](http://wordpress.org/extend/plugins/wp-postratings/) support**. Show your visitors how your readers are rating your posts!

= PSA: do not use the classic Recently widget with the new Widgets screen! =

The classic Recently widget doesn't work very well / at all with the new Widgets screen introduced with WordPress 5.8.

This new Widgets screen expects WordPress blocks instead of regular WordPress widgets. If you're using the Recently widget on your block-based Widgets screen please replace it with the [Recently block](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/).

= Support the Project! =

If you'd like to support my work and efforts to creating and maintaining more open source projects your donations and messages of support mean a lot!

[Ko-fi](https://ko-fi.com/cabrerahector) | [Buy me a coffee](https://www.buymeacoffee.com/cabrerahector) | [PayPal Me](https://paypal.me/cabrerahector)

**Recently** is now also on [GitHub](https://github.com/cabrerahector/recently)!

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
2. If you have a caching plugin installed on your site you may need to adjust some settings to make sure Recently can work: [Is Recently compatible with caching plugins?](https://github.com/cabrerahector/recently/wiki/3.-FAQ#can-recently-work-with-caching-plugins-such-as-wp-super-cache)

Make sure to stop by the **[Wiki](https://github.com/cabrerahector/recently/wiki)** as well, you'll find even more info there!

That's it!

== Frequently Asked Questions ==

The FAQ section has been moved [here](https://github.com/cabrerahector/recently/wiki/3.-FAQ).

== Screenshots ==

1. Widgets Control Panel.
2. Recently Widget.
3. Recently Widget with Cards theme.
4. Recently Widget with Cardview theme.
5. Recently Widget with Midnight theme.
6. Recently Widget with Tiles theme.
7. Recently Widget with Tiny theme.

== Changelog ==

= 4.0.2 =

- Fixes an issue where the excerpt would not be truncated at the expected length (props to dimalifragis!)
- Fixes a PHP warning related to the excerpt generator function (props to dimalifragis!)
- Updates dependencies.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/#4.0.2).

= 4.0.1 =

- Fixes an issue where the excerpt may output broken HTML under some circumstances.
- Updated dependencies.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/#4.0.1).

= 4.0.0 =

This release includes a couple of major changes so please review before updating.

- Minimum required PHP version is now 7.2.
- Minimum required WordPress version is now 5.3.
- Introduces Recently's own block!
- Admin: only users with `edit_others_posts` capability (usually Editors and Administrators) will be able to access certain areas of Recently's dashboard.
- Widgets: Users will no longer be able to add the "classic" widget to the block-based Widgets screen, only the Recently block.
- Fixes an issue where widget themes stored in child theme's folder would not be recognized by the plugin.
- Security enhancements.
- Minor improvements and fixes.

See the [Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/) for more details.

[Full Changelog](https://github.com/cabrerahector/recently/blob/master/changelog.md).

== Upgrade Notice ==
= 4.0.0 =
If you're using a caching plugin flushing its cache after installing/upgrading to this version is highly recommended.
