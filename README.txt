=== Plugin Name ===
Contributors: pekz0r
Tags: Performance, Cache, PJAX, Speed, Optimization
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 0.0.1

Makes WordPress a lot faster using PJAX (PushState + AJAX) for loading content.

== Description ==

= WP-PJAX - PJAX for Wordpress Plugin. =
Makes your site faster and saves you a lot of bandwidth and CPU power by making your Wordpress site PJAX powered!

= What is PJAX? =
PJAX is a technique that uses ajax and pushState to deliver a fast browsing experience by only loading and updating parts of the page HTML. PushState makes it possible to simulate with real permalinks, page titles, and a working back button so that your visitors won't be able to tell the difference between PJAX page loads and ordinary full page loads, except for the increased speed of course.

= Why PJAX? =
It makes your site significantly faster and saves you both processing power and bandwidth!


== Installation ==

In order for this to work you need to make some changes to your theme. This what you should do.

1. In order for this to work you need to make some changes to your theme. This what you should do.

1.1. **Header.** Put this line of code in the top of every header file(any header*.php file) in your theme(before any code or output)

`<?php if(function_exists( 'get_pjax_header' )) if(get_pjax_header()) return FALSE; ?>`

1.2. **Footer.** Put this line of code in the top of every footer file(any footer*.php file) in your theme(before any code or output)

`<?php if(function_exists( 'get_pjax_footer' )) if(get_pjax_footer()) return FALSE; ?>`

1.3. Sidebar. Put this line of code in the top of every sidebar file(any sidebar*.php file) in your theme(before any code or output)

`<?php if(function_exists( 'get_pjax_sidebar' )) if(get_pjax_sidebar()) return FALSE; ?>`

2. Install and activate the plugin as usual

3. Configure the plugin and enable it. The configuration page can be found under 'WP-PJAX' in the settings menu in WP-Admin. Basic instructions on how to configure is provided on the configuration page.

4. That should be it! I Hope you will enjoy the plugin and the performance boost!

== Frequently Asked Questions ==
Your questions goes here. Feel free to contact me!

= Q =

A

== Screenshots ==

1. The plugin is activated with the loading noticies to the right. Check out the load times!
2. Admin page.

== Changelog ==

= 0.0.2 =
First Alpha release
* Hack to make sure this plugin runs first for even better perfomance on cache hits(No need to touch the other plugins if we have a cached page).

= 0.0.1 =
First Alpha release
* The first version!
* This is still experimmental and I can't give you any guarantees.

== Development ==

All develeopment of this plugin occures on [GitHub](https://github.com/pelmered/wp-pjax "WP-PJAX on GitHub"). Please help me develop this by forking and sending plull requests.
