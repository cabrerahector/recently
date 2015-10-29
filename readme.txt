=== Recently ===
Contributors: hcabrera
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PASXEM2E7JUVC
Tags: recent, posts, widget
Requires at least: 3.9
Tested up to: 4.3.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A highly customizable Recent Posts widget.

== Description ==

Let's face it: WordPress' default Recent Posts widget does the job as described, but it's not very flexible. Things like excluding certain entries from the list or displaying recent posts by category can't be done with the stock Recent Posts widget. Therefore, let me introduce Recently.

Recently is a WordPress widget that displays your site's most recent posts. And it doesn't stop there:

= Main Features =
* **Thumbnails support!** (*see the [FAQ section](http://wordpress.org/extend/plugins/recently/faq/) for technical requirements*).
* **Use your own layout**! Control how your recent posts are shown on your theme.
* **Custom Post-type support**. Wanna show other stuff than just posts and pages?
* **Categories, tags, and custom taxonomies support!**
* **[WordPress Popular Posts](https://wordpress.org/extend/plugins/wordpress-popular-posts/)** / **[WP-PostViews](https://wordpress.org/extend/plugins/wp-postratings/)** / **[Top 10](https://wordpress.org/plugins/top-10/)** **support**: display the number of page views of your posts!
* **[WPML](https://wpml.org/) support**!
* **[WP-PostRatings](http://wordpress.org/extend/plugins/wp-postratings/) support**. Show your visitors how your readers are rating your posts!
* **WordPress Multisite support**!

== Installation ==

1. Download the plugin and extract its contents.
2. Upload the `recently` folder to the `/wp-content/plugins/` directory.
3. Activate **Recently** plugin through the 'Plugins' menu in WordPress.
4. In your admin console, go to *Appearance > Widgets*, drag the *Recently* widget onto your sidebar, configure it, and when you're done click on *Save*.
5. If you have a caching plugin installed on your site, flush its cache now. Then, go to *Settings > Recently* and enable the *Ajaxify widget* feature.

That's it!

== Frequently Asked Questions ==

#### I need help with your plugin! What should I do?
First thing to do is read all the online documentation available ([Installation](http://wordpress.org/plugins/recently/installation/), and of course this section) as they should address most of the questions you might have about this plugin.

If you're having problems with Recently, my first suggestion would be try disabling all other plugins and then re-enable each one to make sure there are no conflicts. Also, try switching to a different theme and see if the issue persists. Checking the [Support Forum](http://wordpress.org/support/plugin/recently/) is also a good idea as chances are that someone else may have posted something about it already. **Remember:** *read first*. It'll save you (and me) time.

= -FUNCTIONALITY- =

= My current theme does not support widgets (booooo!). Can I show my recent posts in any other way? =
For the time being, no.

= How can I use my own HTML markup with your plugin? =
Activate the *Use custom HTML markup* option and set your desired configuration and *Content Tags* (see *wp-admin > Settings > Recently > Parameters* for more).

A more advanced way to customize the HTML markup is via [WordPress filters](http://code.tutsplus.com/articles/the-beginners-guide-to-wordpress-actions-and-filters--wp-27373 "The Beginner's guide to WordPress actions and filters") by hooking into *recently_custom_html*.

= I'm unable to activate the "Display post thumbnail" option. Why? =
Please check that either the [ImageMagick](http://www.php.net/manual/en/intro.imagick.php) or [GD](http://www.php.net/manual/en/intro.image.php) extension is installed *and* enabled by your host.

= I'm unable to activate the "Display views" option. Why? =
For this feature you need to install and enable any of the supported plugins mentioned on the [Description](http://wordpress.org/plugins/recently/description/) section.

= How does Recently pick my posts' thumbnails? =
Recently has three different thumbnail options to choose from available at *wp-admin > Settings > Recently > Tools*: *Featured Image* (default), *First image on post*, or [*custom field*](http://codex.wordpress.org/Custom_Fields). If no images are found, a default thumbnail will be displayed instead.

= I'm seeing a "No thumbnail" image, where's my post thumbnail? =
Make sure you have assigned one to your posts (see previous question).

= Is there any way I can change that ugly "No thumbnail" image for one of my own? =
Fortunately, yes. Go to *wp-admin > Settings > Recently > Tools* and check under *Thumbnail source*. Ideally, the thumbnail you're going to use should be set already with your desired width and height - however, the uploader will give you other size options as configured by your current theme.

= I want to have a recent list of my custom post type. How can I do that? =
Simply add your custom post type to the Post Type field in the widget.

= Can Recently run on WordPress Multisite? =
While I have not tested it, Recently should work just fine under WPMU (and if it doesn't please let me know).

= -CSS AND STYLESHEETS- =

= Does your plugin include any CSS stylesheets? =
Yes, *but* there are no predefined styles (well, almost). Recently will first look into your current theme's folder for the recently.css file and use it if found so that any custom CSS styles made by you are not overwritten, otherwise will use the one bundled with the plugin.

= Each time Recently is updated the recently.css stylesheet gets reset and I lose all changes I made to it. How can I keep my custom CSS? =
Move your modified recently.css file into your theme's folder, otherwise my plugin will use the one bundled with it by default.

= How can I style my list to look like [insert your desired look here]? =
Since this plugin does not include any predefined designs, it's up to you to style your recent posts list as you like (you might need to hire someone for this if you don't know HTML/CSS, though).

= I want to remove Recently's stylesheet. How can I do that? =
You can disable the stylesheet via *wp-admin > Settings > Recently > Tools*.

= -OTHER STUFF THAT YOU (PROBABLY) WANT TO KNOW- =

= Does Recently support other languages than english? =
Yes, currently the plugin supports the following languages: Spanish.

= I want to translate your plugin into my language / help you update a translation. What do I need to do? =
First thing you need to do is get a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) to translate all texts into your language. You'll find a .POT file bundled with the plugin under the *lang* folder. Grab *recently.pot* and rename it to add the proper suffix for your language (eg. recently-es_ES.po, for Spanish). Make sure you also rename the extension from .POT to .PO too. Open the .PO file using Poedit (or your preferred gettext editor) and update the strings there. It sounds complicated, I know, but it's not.

Check this handy and more detailed [guide](http://www.gabsoftware.com/tips/a-guide-for-wordpress-plugins-translators-gettext-poedit-locale/ "A guide for translating WordPress plugins") in case you get lost at some point.

= I want your plugin to have X or Y functionality. Can it be done? =
If it fits the nature of my plugin and it sounds like something others would like to have, there's a pretty good chance that I will implement it (and if you can provide some sample code with useful comments, even better).

= Your plugin seems to conflict with my current Theme / another plugin. Can you please help me? =
If the theme/plugin you're talking about is a free one that can be downloaded from somewhere, sure I can try and take a look into it. Premium themes/plugins are out of discussion though, unless you're willing to grant me access to your site (or get me a copy of this theme/plugin) so I can check it out.

= ETA for your next release? =
Updates will come depending on my work projects (I'm a full-time web developer) and the amount of time I have on my hands. Quick releases will happen only when/if critical bugs are spotted.

= I posted a question at the Support Forum and got no answer from the developer. Why is that? =
Chances are that your question has been already answered either at the [Support Forum](http://wordpress.org/support/plugin/recently/), the [Installation section](http://wordpress.org/plugins/recently/installation/) or even here in the FAQ section, so I've decided not to answer. It could also happen that I'm just busy at the moment and haven't been able to read your post yet, so please be patient.

= Is there any other way to contact you? =
For the time being, the [Support Forum](http://wordpress.org/support/plugin/recently/) is the only way to contact me. Please do not use my email to get in touch with me *unless I authorize you to do so*.

== Screenshots ==

1. Widgets Control Panel.
2. Recently Widget.
3. Recently Widget with custom HTML.

== Changelog ==

= 1.0.1 =
* Adds the recently_no_data filter hook
* Minor bug fixes and improvements

= 1.0.0 =
* Public release

== Upgrade Notice ==

