# Recently

A highly customizable Recent Posts widget for WordPress.

----
## Table of contents
 
* [Description](https://github.com/cabrerahector/recently#description)
* [Features](https://github.com/cabrerahector/recently#features)
* [Requirements](https://github.com/cabrerahector/recently#requirements)
* [Installation](https://github.com/cabrerahector/recently#installation)
* [Usage](https://github.com/cabrerahector/recently#usage)
* [Support](https://github.com/cabrerahector/recently#support)
* [Contributing](https://github.com/cabrerahector/recently#contributing)
* [Changelog](https://github.com/cabrerahector/recently/blob/master/changelog.md)
* [License](https://github.com/cabrerahector/recently#license)


## Description

Recently is a highly customizable [widget](https://wordpress.org/plugins/recently/) to showcase the most recent entries from your [WordPress](https://wordpress.org/) powered site.


## Features

* **Multi-widget capable**. That is, you can have several Recently widgets on your blog - each with its own settings!
* **[Polylang](https://wordpress.org/plugins/polylang/)/[WPML 3.2+](https://wpml.org/) support** - Show the translated version of your recent posts!
* **WordPress Multisite support**.
* **WordPress Popular Posts support** - Display the views count of your recent posts!
* **Custom Post-type support**. - Want to show other stuff than just posts and pages, eg. Recent *Products*? [You can](https://github.com/cabrerahector/recently/wiki/3.-FAQ#i-want-to-have-a-recent-list-of-my-custom-post-type-how-can-i-do-that)!
* List recent posts filtered by categories, tags, or custom taxonomies!
* Display a **thumbnail** of your recent posts! (see the [FAQ section](https://github.com/cabrerahector/recently/wiki/3.-FAQ#how-does-recently-pick-my-posts-thumbnails) for more details.)
* Use **your own layout**! Recently is flexible enough to let you customize the look and feel of your recent posts list! (see [customizing Recently's HTML markup](https://github.com/cabrerahector/recently/wiki/3.-FAQ#how-can-i-use-my-own-html-markup-with-your-plugin) and [How to style Recently](https://github.com/cabrerahector/recently/wiki/4.-Styling-the-list) for more.)
* **Localizable** to your own language (See [translating Recently into your language](https://github.com/cabrerahector/recently/wiki/3.-FAQ#i-want-to-translate-your-plugin-into-my-language--help-you-update-a-translation-what-do-i-need-to-do) for more info).
* **[WP-PostRatings](https://wordpress.org/plugins/wp-postratings/) support**. Show your visitors how your readers are rating your posts!


## Requirements

* WordPress 5.7 or newer.
* PHP 7.2 or newer.
* Mbstring PHP Extension.
* Either the [ImageMagik](https://www.php.net/manual/en/intro.imagick.php) or [GD](https://www.php.net/manual/en/intro.image.php) library installed and enabled on your server (not really required, but needed to create thumbnails).


## Installation

### Automatic installation ###

1. Log in into your WordPress dashboard.
2. Go to Plugins > Add New.
3. In the "Search Plugins" field, type in **Recently** and hit Enter.
4. Find the plugin in the search results list and click on the "Install Now" button.

### Manual installation ###

1. [Download the plugin](https://wordpress.org/plugins/recently/) and extract its contents.
2. Upload the `recently` folder to the `/wp-content/plugins/` directory.
3. Activate **Recently** through the 'Plugins' menu in WordPress.

### Done! What's next? ###

1. In your admin console, go to Appearance > Widgets, drag the Recently widget to your sidebar and click on Save.
2. If you have a caching plugin installed on your site you may need to adjust some settings to make sure Recently can work: [Is Recently compatible with caching plugins?](https://github.com/cabrerahector/recently/wiki/3.-FAQ#can-recently-work-with-caching-plugins-such-as-wp-super-cache) Also, if you're using a JS minifying plugin you may need to add recently.min.js to its exclusion list (see [Is Recently compatible with plugins that minify / bundle JavaScript code?](https://github.com/cabrerahector/recently/wiki/3.-FAQ#is-recently-compatible-with-plugins-that-minify--bundle-javascript-code))

## Support

Before submitting an issue, please:

1. Read the documentation, it's there for a reason. Links: [Requirements](https://github.com/cabrerahector/recently#requirements) | [Installation](https://github.com/cabrerahector/recently#installation) | [Wiki](https://github.com/cabrerahector/recently/wiki) | [Frequently asked questions](https://github.com/cabrerahector/recently/wiki/3.-FAQ).
2. If it's a bug, please check the [issue tracker](https://github.com/cabrerahector/recently/issues) first make sure no one has reported it already.

When submitting an issue, please answer the following questions:

1. WordPress version?
2. Plugin version?
3. Describe what the issue is in detail (include steps to reproduce it, if necessary).


## Contributing

* If you'd like to support my work and efforts to creating and maintaining more open source projects your donations and messages of support mean a lot! [Ko-fi](https://ko-fi.com/cabrerahector) | [PayPal](https://www.paypal.com/paypalme/cabrerahector)
* If you have any ideas/suggestions/bug reports, and if there's not an issue filed for it already (see [issue tracker](https://github.com/cabrerahector/recently/issues), please [create an issue](https://github.com/cabrerahector/recently/issues/new) so I can keep track of it.
* Developers can send [pull requests](https://help.github.com/articles/using-pull-requests) to suggest fixes / improvements to the source.
* You can also [help translate Recently into your language / help update an existing translation](https://github.com/cabrerahector/recently/wiki/3.-FAQ#i-want-to-translate-your-plugin-into-my-language--help-you-update-a-translation-what-do-i-need-to-do).


## License

[GNU General Public License version 2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

Copyright (C) 2025  Héctor Cabrera - https://cabrerahector.com

The Recently plugin is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

The Recently plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the Recently plugin; if not, see [https://www.gnu.org/licenses](https://www.gnu.org/licenses/).