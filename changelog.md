Changelog
=========
#### 2.1.0 ####
*If you're using a caching plugin, flushing its cache after installing/upgrading to this version is highly recommended.*

* Recently will fetch its ajaxified widgets via the REST API now for faster performance.
* Adds filter hook to customize query args per widget instance: `recently_pre_get_posts` ([documentation](https://github.com/cabrerahector/recently/wiki/1.-Filter-Hooks#recently_pre_get_posts))
* Adds filter hook to parse custom Content Tags: `recently_parse_custom_content_tags` ([documentation](https://github.com/cabrerahector/recently/wiki/1.-Filter-Hooks#recently_parse_custom_content_tags)).
* Improves compatibility with Cloudflare's Rocket Loader.
* Drops included language files in favor for language packs downloaded automatically by WordPress.
* Minor bug fixes and improvements.

See the [Release Notes](https://cabrerahector.com/wordpress/recently-2-1-rest-api-support-new-filter-hooks/) for more details.

#### 2.0.2 ####
*If you're using a caching plugin, flushing its cache after upgrading to this version is highly recommended.*

* Fixes Content Tag `{author}` returning a bad URL.
* Data Caching enabled by default (new installs only).
* Minor copy adjustments.

#### 2.0.1 ####
* Fixes missing helper method messing up the Customizer.
* Updates default widget stylesheet.
* Other minor fixes and improvements.

#### 2.0.0 ####
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

#### 1.0.2 ####
* Minor bug fixes and improvements

#### 1.0.1 ####
* Adds the recently_no_data filter hook
* Minor bug fixes and improvements

#### 1.0.0 ####
* Public release