Changelog
=========

## 4.1.0 ##

- Adds WebP / AVIF support.
- Fixes an issue where the excerpt would not be truncated as expected.
- Fixes an issue where the fallback thumbnail may revert to the stock one under certain conditions.
- Minor fixes and improvements.

[Release notes](https://cabrerahector.com/wordpress/recently-4-1-webp-avif-support-plus-some-minor-improvements/).

## 4.0.3 ##

**If you're using a page caching plugin it's highly recommended to clear its cache after upgrading to this version.**
**If you're using a JS minifying plugin you may need to add recently.min.js to its exclusion list (see [FAQ](https://github.com/cabrerahector/recently/wiki/3.-FAQ#is-recently-compatible-with-plugins-that-minify--bundle-javascript-code)).**

- Min. required WordPress version is now 5.7.
- Improves PHP 8 support.
- Improves compatibility with XHTML WordPress themes.
- Query data caching feature has been removed as it's now built-in into WordPress.
- Other minor enhancements.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/#4.0.3).

## 4.0.2 ##

- Fixes an issue where the excerpt would not be truncated at the expected length (props to dimalifragis!)
- Fixes a PHP warning related to the excerpt generator function (props to dimalifragis!)
- Updates dependencies.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/#4.0.2).

## 4.0.1 ##

- Fixes an issue where the excerpt may output broken HTML under some circumstances.
- Updated dependencies.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/#4.0.1).

## 4.0.0 ##

This release includes a couple of major changes so please review before updating.

- Minimum required PHP version is now 7.2.
- Minimum required WordPress version is now 5.3.
- Introduces Recently's own block!
- Admin: only users with `edit_others_posts` capability (usually Editors and Administrators) will be able to access certain areas of Recently's dashboard.
- Widgets: Users will no longer be able to add the "classic" widget to the block-based Widgets screen, only the Recently block.
- Fixes an issue where widget themes stored in child theme's folder would not be recognized by the plugin.
- Security enhancements.
- Minor improvements and fixes.

[Release notes](https://cabrerahector.com/wordpress/recently-4-0-new-recently-block-php-5-support-dropped-minimum-supported-wordpress-version-changed/)

## 3.0.5 ##

- Fixes a potential XSS vulnerability (props to Yu Iwama of Secure Sky Technology Inc. and the JPCERT/CC Vulnerability Coordination Group).
- Fixes a potential code injection vulnerability (props to Jerome & NinTechNet).
- Fixes a fatal error that can occur when using stock thumbnail sizes (props to @rianovost).
- Fixes a srcset bug that affects specific PHP locales (props to @fredel).
- Fixes a srcset not loading images due to improper SSL/HTTPS configuration (props to @aj4h).
- Fixes a views/comments translation issue that affects some languages.

If you're using a caching plugin, flushing its cache after installing/upgrading to this version is highly recommended.

[Release notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/#minor-updates-and-hotfixes)

## 3.0.4 ##

* Fixes an issue where when using multiple widgets on the same page posts would be duplicated (thanks rianovost!)
* Enables the Ajaxify Widget option by default (affects new installs only) for better out-of-the-box compat with caching plugins.

[Release notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/#minor-updates-and-hotfixes)

## 3.0.3 ##

* Fixes a fatal PHP error when using an image source other than Featured Image (thanks rianovost!)

[Release notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/#minor-updates-and-hotfixes)

## 3.0.2 ##

* Fixes thumbnails not displaying under certain conditions.
* Fixes an issue where the Tiny theme would change the widget title to "Trending."
* Updates plugin screenshots to showcase the new themes.

[Release notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/#minor-updates-and-hotfixes)

## 3.0.1 ##

* Fixes a bug where the user couldn't disable the display of thumbnails.
* Improves compatibility with WordPress Popular Posts and Polylang.
* Minor code enhancements.

[Release notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/#minor-updates-and-hotfixes)

## 3.0.0 ##
**If you're using a caching plugin, flushing its cache after installing/upgrading to this version is highly recommended.**

* Code has been refactored to make maintenance easier in the future.
* Widget Themes support!
* Retina displays support!
* New Content Tags added: total_items, item_position, and title_attr.
* Minor usability improvements.
* Improves compatibility with Polylang/WPML.
* Improved CSP compatibility. Note that this may cause issues on some set-ups, please check the Release Notes for more details.

See the [Release Notes](https://cabrerahector.com/wordpress/recently-3-0-has-been-released/) for more details.
[Full Changelog](https://github.com/cabrerahector/recently/blob/master/changelog.md).

## 2.1.0 ##
*If you're using a caching plugin, flushing its cache after installing/upgrading to this version is highly recommended.*

* Recently will fetch its ajaxified widgets via the REST API now for faster performance.
* Adds filter hook to customize query args per widget instance: `recently_pre_get_posts` ([documentation](https://github.com/cabrerahector/recently/wiki/1.-Filter-Hooks#recently_pre_get_posts))
* Adds filter hook to parse custom Content Tags: `recently_parse_custom_content_tags` ([documentation](https://github.com/cabrerahector/recently/wiki/1.-Filter-Hooks#recently_parse_custom_content_tags)).
* Improves compatibility with Cloudflare's Rocket Loader.
* Drops included language files in favor for language packs downloaded automatically by WordPress.
* Minor bug fixes and improvements.

See the [Release Notes](https://cabrerahector.com/wordpress/recently-2-1-rest-api-support-new-filter-hooks/) for more details.

## 2.0.2 ##
*If you're using a caching plugin, flushing its cache after upgrading to this version is highly recommended.*

* Fixes Content Tag `{author}` returning a bad URL.
* Data Caching enabled by default (new installs only).
* Minor copy adjustments.

## 2.0.1 ##
* Fixes missing helper method messing up the Customizer.
* Updates default widget stylesheet.
* Other minor fixes and improvements.

## 2.0.0 ##
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

## 1.0.2 ##
* Minor bug fixes and improvements

## 1.0.1 ##
* Adds the recently_no_data filter hook
* Minor bug fixes and improvements

## 1.0.0 ##
* Public release