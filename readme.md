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

Recently is a highly customizable [widget](http://wordpress.org/plugins/recently/) to showcase the most recent entries from your [WordPress](http://wordpress.org/) powered site.


## Features

* **Multi-widget capable**. That is, you can have several Recently widgets on your blog - each with its own settings!
* **WPML support**.
* **WordPress Multisite support**.
* **WordPress Popular Posts support** - Display the views count of your recent posts!
* **Custom Post-type support**. Wanna show other stuff than just posts and pages?
* Display a **thumbnail** of your recent posts! (see [technical requirements](https://github.com/cabrerahector/recently/wiki/5.-FAQ#im-unable-to-activate-the-display-post-thumbnail-option-why)).
* Use **your own layout**! [Control how your recent posts are shown on your theme](https://github.com/cabrerahector/recently/wiki/5.-FAQ#how-can-i-use-my-own-html-markup-with-your-plugin).
* **Shortcode support** - use the `[wpp]` shortcode to showcase your most popular posts on pages, too! (see "[Using WPP on posts & pages](https://github.com/cabrerahector/recently/wiki/1.-Using-WPP-on-posts-&-pages)").
* **Localizable** to your own language (See [here](https://github.com/cabrerahector/recently/wiki/5.-FAQ#i-want-to-translate-your-plugin-into-my-language--help-you-update-a-translation-what-do-i-need-to-do) for more info).
* **[WP-PostRatings](http://wordpress.org/extend/plugins/wp-postratings/) support**. Show your visitors how your readers are rating your posts!


## Requirements

* WordPress 3.8 or above.
* PHP 5.2+ or above.
* Either the [ImageMagik](http://www.php.net/manual/en/intro.imagick.php) or [GD](http://www.php.net/manual/en/intro.image.php) library installed and enabled on your server (not really required, but needed to create thumbnails).


## Installation

1. [Download the plugin](http://wordpress.org/plugins/recently/) and extract its contents.
2. Upload the `recently` folder to the `/wp-content/plugins/` directory.
3. Activate **Recently** through the 'Plugins' menu in WordPress.
4. In your admin console, go to Appeareance > Widgets, drag the Recently widget to your sidebar and click on Save.
5. If you have a caching plugin installed on your site, flush its cache now so Recently can be displayed on your site.

## Support

Before submitting an issue, please:

1. Read the documentation, it's there for a reason. Links: [Requirements](https://github.com/cabrerahector/recently#requirements) | [Installation](https://github.com/cabrerahector/recently#installation) | [Wiki](https://github.com/cabrerahector/recently/wiki) | [Frequently asked questions](https://github.com/cabrerahector/recently/wiki/5.-FAQ).
2. If it's a bug, please check the [issue tracker](https://github.com/cabrerahector/recently/issues) first make sure no one has reported it already.

When submitting an issue, please answer the following questions:

1. WordPress version?
2. Plugin version?
3. Describe what the issue is in detail (include steps to reproduce it, if necessary).


## Contributing

* If you have any ideas/suggestions/bug reports, and if there's not an issue filed for it already (see [issue tracker](https://github.com/cabrerahector/recently/issues), please [create an issue](https://github.com/cabrerahector/recently/issues/new) so I can keep track of it.
* Developers can send [pull requests](https://help.github.com/articles/using-pull-requests) to suggest fixes / improvements to the source.
* Want to translate Recently into your language or update a current translation? Check if it's [already supported](https://github.com/cabrerahector/recently/tree/master/lang) or download [this file](https://github.com/cabrerahector/recently/blob/master/lang/recently.pot) to translate the strings (see "[I want to translate your plugin into my language / help you update a translation. What do I need to do?](https://github.com/cabrerahector/recently/wiki/5.-FAQ#i-want-to-translate-your-plugin-into-my-language--help-you-update-a-translation-what-do-i-need-to-do)" for more).


## License

[GNU General Public License version 2 or later](http://www.gnu.org/licenses/gpl-2.0.html)

Copyright (C) 2015-2018  Héctor Cabrera - https://cabrerahector.com

The Recently plugin is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

The Recently plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the Recently plugin; if not, see [http://www.gnu.org/licenses](http://www.gnu.org/licenses/).