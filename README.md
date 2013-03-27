#WP-PJAX - PJAX for Wordpress Plugin.
=======


======
Makes your site faster and saves you a lot of bandwidth and CPU power by making your Wordpress site PJAX powered!

## What is PJAX?
PJAX is a technique that uses ajax and pushState to deliver a fast browsing experience by only loading and updating parts of the page HTML. PushState makes it possible to simulate with real permalinks, page titles, and a working back button so that your visitors won't be able to tell the difference between PJAX page loads and ordinary full page loads, except for the increased speed of course. 

## Why PJAX?
It makes your site significantly faster and saves you both processing power and bandwidth!

## Setting up the plugin

1. In order for this to work you first need to make some changes to your theme.

1.1. Header. Put this line of code in the top of every header file(any header*.php file) in your theme(before any code or output)

    if(function_exists( 'get_pjax_header' )) if(get_pjax_header()) return FALSE;

1.2. Footer. Put this line of code in the top of every footer file(any footer*.php file) in your theme(before any code or output)

    if(function_exists( 'get_pjax_footer' )) if(get_pjax_footer()) return FALSE;

1.3. Sidebar. Put this line of code in the top of every sidebar file(any sidebar*.php file) in your theme(before any code or output)

    if(function_exists( 'get_pjax_sidebar' )) if(get_pjax_sidebar()) return FALSE;

2. Install and activate the plugin 

3. Configure the plugin and enable it. The configuration page can be found under WP-PJAX in the settings menu in WP-Admin. Instructions on how to configure is provided on the configuration page.

## Compatibility and requirements
The goal is to make the plugin compatible with any theme with the minor modifications found under Setting up above. If you run into problems, please contact me and I will look info you problem to find a solution. The plguin is thoroughly tested on the TwentyTwelve theme and a few custom themes. 
This plugins is tested to run on PHP 5.2+ and WordPress 3.5+ but will probably run on older versions as well. Please report if your testing! 

## Actions & fitlers
All actions in order of typical execution: 

wp_pjax_before_render - Triggered in the begining of get_pjax_header. Only runs on non-cached requests(the built in page cache).
Parameters: Current post object.

wp_pjax_header - Triggered before the start of page template execution. Replaces wp_head. Typically for manupilating headers and page cache behaviour. 
Parameters (2): wp_pjax options.

wp_pjax_title - (filter) Used for generating the title

wp_pjax_footer - Triggered after page template execution. Replaces wp_footer.
Parameters (2): current post object, wp_pjax options.

Please not that these actions are only triggered for PJAX/partial responses.

## To-Do
- Test in more themes
- Add more multiple link/menu selectors with attached active CSS clases
- Add support for several content areas. For example main content, sidebar, header and footer. Also support for custom updates of subsets(for example shopping cart).
- Add support for search and form submit
- Optimize performance on both client(JS) and server(content rendering). Revisit Cache implementation. Refresh/flush cache on page content update. Using local storage for client side cache?

## Changelog

### `v0.1a`

- 2013-02-07: Initial commit - Still in early development - Very experimental!


## Credits

This plugin is based on code form [jQuery PJAX](https://github.com/defunkt/jquery-pjax/) and [Thematic PJAX](https://github.com/wayoutmind/thematic-pjax/)

Inspiration from: [Nathan Kontny's PJAX fork](https://github.com/n8/jquery-pjax/tree/localcache_firebase) and [PJAX Menu](https://github.com/nikolas/pjax-menu/)

