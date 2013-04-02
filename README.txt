=== Plugin Name ===
Contributors: pekz0r
Tags: Performance, Cache, PJAX, Speed, Optimization
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 0.0.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Makes WordPress a lot faster using PJAX (PushState + AJAX) for loading content.

== Description ==

= THIS PLUGIN IS STILL EXPERIMENTAL - USE AT YOUR OWN RISK! =
The plugin is under development. Please try it and contact me if you encounter any bugs or have any questions or suggestions. If you want to help me develop this further send a pull request on [GitHub](https://github.com/pelmered/wp-pjax "WP-PJAX on GitHub"). 
The plugin is not ready for production yet, but I will soon release version 0.1 that will be production ready.

= WP-PJAX - PJAX for Wordpress Plugin. =
Makes your site faster and saves you a lot of bandwidth and CPU power by making your Wordpress site PJAX powered!

= What is PJAX? =
PJAX is a technique that uses AJAX and PushState to deliver a faster browsing experience by only loading and updating parts of the page HTML each page load. PushState makes it possible to add real permalinks, page titles, and a working back button so that your visitors won't be able to tell the difference between PJAX page load and ordinary full page loads, except for the increased speed of course :).

= Why PJAX? =
It makes your site significantly faster in most cases and saves you both processing power and bandwidth!

= Features =
* Speeds up any website. With the built in page cache enabled you can expect 10-50+% faster page loads. 
* Supports 4 Levels of application cache! This plugin uses browser cache and page cache. It is also enabling reverse proxy(i.e. Varnish) cache by (optionally) striping sessions and cookies and it works very well with underlaying object and database cache using third party cache plugins such as [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/ "W3 Total Cache") (recommended). 
* Works well with [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/ "W3 Total Cache").
* Configurable. No need to dig into the code. You will still need basic knowledge about HTML and how Wordpress works to make this plugin reach it's full potential.
* Live notices with a report for every page load (Load time, page cache hit or miss, Reverse proxy/Varnish cache miss or hit etc - See screenshot). This is great for debugging and testing and is of course only visible for admins.
* Only a few changes in your theme and you are ready to go. Should not take more than 5 minutes and requires only basic knowledge.

= Browser support =
PJAX is not supported in Internet Explorer 9 and earlier(IE 10+ supports this), but the plugin handles this gracefully by falling back on regular page loads for unsupported browsers. [Details on browser support](http://caniuse.com/#search=pushstate "Details on browser support")

= Development =
All development of this plugin occurs on [GitHub](https://github.com/pelmered/wp-pjax "WP-PJAX on GitHub"). Please help me develop this by forking and sending pull requests.


= Planned features / To-Do =
* Better handling of menu active classes. Will probably use regex for this. Support for marking parent pages active would be nice but I don't know any good ways to accomplish that.
* Better control over exceptions for when to disable PJAX, when you use Page cache, and when and what cookies and sessions that should be striped. Regex support will be added.
* Refresh cache on publish/update.
* Separate loading of sidebars with separate cache.
* Page loading notice / icon. This should also be customizable with css, text(localizable) and it should be possible to upload your own icon.
* Page cache prefetch needs to be revisited. WP-Cron is a bit tricky and its hard to handle timeouts gracefully and reliably cross different environments. 
* Remove all debug code and general code cleanup. This will be finished before the 0.1 release.
* Add an optional menu to the admin bar for clearing cache(all and current page).
* Optimize execution flow for better performance
* Maybe: Better way to generate the configurable javascript. I think of two options for this, ether use `wp_localize_script` to inject variables into javascript or to generate a javascript with PHP file when settings are saved.

= Known issues =
* The PJAX toggle checkbox does not work. `$.pjax.disable()` does not seam to work as it should. Maybe I need to set a cookie with AJAX to set this for the current user and then handle it on the server side.
* 


== Installation ==

The plugin needs to control whether the header and footer should fire or not for every request. Therefore you need to make some small changes in your theme for this plugin to work properly. This what you need to do:

1. **Header.** Put this line of code in the top of every header file(any header*.php file) in your theme(before any code or output)

`<?php if(function_exists( 'get_pjax_header' )) if(get_pjax_header()) return FALSE; ?>`

2. **Footer.** Put this line of code in the top of every footer file(any footer*.php file) in your theme(before any code or output)

`<?php if(function_exists( 'get_pjax_footer' )) if(get_pjax_footer()) return FALSE; ?>`

3. **Sidebar.** Put this line of code in the top of every sidebar file(any sidebar*.php file) in your theme(before any code or output). This is currently not used, but it will probably be used in later versions. So for safe upgrades in the future, I recommend that you do this.

`<?php if(function_exists( 'get_pjax_sidebar' )) if(get_pjax_sidebar()) return FALSE; ?>`

4. Install and activate the plugin as usual.

5. Configure the plugin and enable it. The configuration page can be found under 'WP-PJAX' in the settings menu in WP-Admin. Basic instructions on how to configure is provided on the configuration page.

6. That should be it! I Hope you will enjoy the plugin and the performance boost!

== Frequently Asked Questions ==
Your questions goes here. Feel free to contact me!

= Foo? =

Bar!

== Screenshots ==
1. The plugin is activated with the loading notices to the right. Check out the load times!
2. Admin page.

== Changelog ==

= 0.0.4.1=
* Added screenshots to Assets folder.
* Fixed bug with the sitemap URL in the default settings.

= 0.0.4=
* Code  clean up. Some code removed and some debug code commented.
* URL parsing fix for finding element to add active class.

= 0.0.3 =
* Added default settings for easier first-time configurations (Not super sexy, but it works).

= 0.0.2 =
* Hack to make sure this plugin runs first for even better performance on cache hits(No need to touch the other plugins if we have a cached page).

= 0.0.1 =
* The first version!
* This is still experimental and I can't give you any guarantees.

== Development ==

All development of this plugin occurs on [GitHub](https://github.com/pelmered/wp-pjax "WP-PJAX on GitHub"). Please help me develop this by forking and sending plull requests.
