<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="wp-pjax-admin" class="wrap">
    <h2><?php _e('WP PJAX Settings'); ?></h2>

    <form method="post" action=""><?php wp_nonce_field('update-options'); ?>
   
        
    <p class="submit"> 
        <input type="submit" name="pe-wp-pjax-clear-cache" class="pe-wp-pjax-button-save button-primary" value="Load default settings" <?php echo ( empty($wp_pjax_options) ? 'style="background: red;padding: 20px;font-size: 40px;height: auto;"' : '' ) ?> />
        
        <?php if( !empty($wp_pjax_options)) : ?>
        <input type="submit" name="pe-wp-pjax-clear-cache" class="pe-wp-pjax-button-save button-primary" value="Clear cache" />
        <?php endif; ?>
    </p>
        
        
    <div id="pjax-general" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>General</span></h3><div class="inside">        
            <p></p>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Enable plugin:</th>
                        <td>
                            <label for="pe-wp-pjax-enable">
                                <input name="pe-wp-pjax-enable" id="pe-wp-pjax-enable" value="1" type="checkbox" <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'enable'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Enable plugin features acording to these settings or disable all functionality for the plugin.'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjax-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
        
    <h2>Basic setup</h2>
    
    <div id="pjax-selectors" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>Selectors</span></h3><div class="inside">        
            <p></p>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Menu selector:</th>
                        <td>
                            <input name="pe-wp-pjax-menu-selector" id="pe-wp-pjax-menu-selector" size="70" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'menu-selector']; ?>" type="text" />
                            <p class="description"><?php _e('jQuery Selecor that selects all anchor tags you want to enable PJAX on. Example: "#main-menu a" '); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Content selector:</th>
                        <td>
                            <input name="pe-wp-pjax-content-selector" id="pe-wp-pjax-content-selector" size="70" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-selector']; ?>" type="text" />
                            <p class="description"><?php _e('jQuery Selecor that selects the content container that should be rendered by the page tempaltes. This usually includes the sidebar(s). Example: "#main-content" '); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Menu active class:</th>
                        <td>
                            <input name="pe-wp-pjax-menu-active-class" id="pe-wp-pjax-menu-active-class" size="70" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'menu-active-class']; ?>" type="text" />
                            <p class="description"><?php _e('CSS classes that should be added to the active menu item. Example: ",current-menu-item" '); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjaxc-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    
    <h2>Advanced settings</h2>
       
    <div id="pjax-advanced-general" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>General</span></h3><div class="inside">        
            <p>.</p>
            
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Fallback timeout:</th>
                        <td>
                            <input name="pe-wp-pjax-load-timeout" id="pe-wp-pjax-load-timeout" size="4" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'load-timeout']; ?>" type="text" /> milliseconds
                            <p class="description"><?php _e('Time to wait for PJAX response before reverting to fallback, i.e. full page reload. Set to 0 to disable timeout.<br />Recomended value is around 1-2 seconds(1000-2000 ms) for most configrations.'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjax-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    
    <div id="pjax-notises" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>PJAX Notices</span></h3><div class="inside">        
            <p>Notices for testing, debuging and benchmarking.</p>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Show PJAX toggle:</th>
                        <td>
                            <label for="pe-wp-pjax-show-toggle">
                                <input name="pe-wp-pjax-show-toggle" id="pe-wp-pjax-show-toggle" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'show-toggle'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show a toggle for turning on and off PJAX. Great for testing the difference in page load time.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show PJAX notice:</th>
                        <td>
                            <label for="pe-wp-pjax-show-notice">
                                <input name="pe-wp-pjax-show-notice" id="pe-wp-pjax-show-notice" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'show-notice'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show a notice when the page is loaded with PJAX. Great for testing.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show extended info in notice:</th>
                        <td>
                            <label for="pe-wp-pjax-show-extended-notice">
                                <input name="pe-wp-pjax-show-extended-notice" id="pe-wp-pjax-show-extended-notice" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'show-extended-notice'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show XHR/AJAX load time, page cahce info etc in notice.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Notice timeout or sticky:</th>
                        <td>
                            <input name="pe-wp-pjax-notice-timeout" id="pe-wp-pjax-notice-timeout" size="5" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'notice-timeout']; ?>" type="text"  /> ms
                            <p  class="description" style="margin: 0;"><?php _e('How long the notices should be visible(in miliseconds) <br /><strong>OR</strong> make them sticky and close them manually by flicking or by refreshing the page'); ?></p>
                            <label for="pe-wp-pjax-notice-sticky">
                                <input name="pe-wp-pjax-notice-sticky" id="pe-wp-pjax-notice-sticky" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'notice-sticky'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable sticky notices</strong>
                            </label>
                            <p class="description">Note: Sticky will override timeout if checked.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show extended info in notice:</th>
                        <td>
                            <label for="pe-wp-pjax-show-extended-notice">
                                <input name="pe-wp-pjax-show-extended-notice" id="pe-wp-pjax-show-extended-notice" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'show-extended-notice'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show XHR/AJAX load time, page cahce info etc in notice.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show notice for:</th>
                        <td>
                            Roles (not ready)
                            <select>
                                <option value="0">Everyone</option>
                                <option value="0">Subscriber</option>
                                <option value="0">Contributor</option>
                                <option value="0">Editor</option>
                                <option value="0">Author</option>
                                <option value="0">Administrator</option>
                            </select>
                            <p class="description"><?php _e(''); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjax-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    <div id="pjax-page-cache" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>Partial Page Cache</span></h3><div class="inside">        
            <p>
                Enable page caching to decrease the response time of the site. <br />
                For optimal performance, use a cache plugin that supports transient cache, preferably memory based cache. This plugin is mostly tested with <a href="http://wordpress.org/extend/plugins/w3-total-cache/">W3 Total Cache</a>, but should work with any other cache plugin as well.
            </p>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Enable partial page cache:</th>
                        <td>
                            <label for="pe-wp-pjax-page-cache">
                                <input name="pe-wp-pjax-page-cache" id="pe-wp-pjax-page-cache" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Partial page cache. Best used in combination with some other cache plugin with object cache, for example W3 Totla Cache.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable browser cache for partial page cache:</th>
                        <td>
                            <label for="pe-wp-pjax-browser-page-cache">
                                <input name="pe-wp-pjax-browser-page-cache" id="pe-wp-pjax-page-cache" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'browser-page-cache'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Partial page cache. Best used in combination with some other cache plugin with object cache, for example W3 Totla Cache.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Cache lifetime:</th>
                        <td>
                            <input name="pe-wp-pjax-page-cache-lifetime" id="pe-wp-pjax-page-cache-lifetime" size="4" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-lifetime']; ?>" type="text" /> seconds
                            <p class="description"><?php _e('Cache TTL '); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Cache exceptions:</th>
                        <td>
                            <textarea name="pe-wp-pjax-page-cache-exceptions" id="pe-wp-pjax-page-cache-exceptions" rows="10" cols="60" ><?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-exceptions']; ?></textarea>
                            <p class="description"><?php _e('One per line '); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Strip cookies:</th>
                        <td>
                            <label for="pe-wp-pjax-page-cache">
                                <input name="pe-wp-pjax-strip-cookies" id="pe-wp-pjax-strip-cookies" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'strip-cookies'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Partial page cache. Best used in combination with some other cache plugin with object cache, for example W3 Totla Cache.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Strip cookies:</th>
                        <td>
                            <textarea name="pe-wp-pjax-strip-cookies-list" id="pe-wp-pjax-strip-cookies-list" rows="10" cols="60" ><?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'strip-cookies-list']; ?></textarea>
                            <p class="description"><?php _e('One per line '); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table> 

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjax-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    <div id="pjax-page-cache" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
            <h3>Prefetch cache</h3>
            <p>
                Prefetch cache to make sure that the users hit warm caches and get much faster page loads.
            </p>
            

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Enable cache prefetch:</th>
                        <td>
                            <label for="pe-wp-pjax-page-cache-prefetch">
                                <input name="pe-wp-pjax-page-cache-prefetch" id="pe-wp-pjax-page-cache-prefetch" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('desc.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Prefetch interval:</th>
                        <td>
                            <input name="pe-wp-pjax-page-cache-prefetch-interval" id="pe-wp-pjax-page-cache-prefetch-interval" size="4" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-interval']; ?>" type="text" /> seconds
                            <p class="description"><?php _e(''); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Pages per interval:</th>
                        <td>
                            <input name="pe-wp-pjax-page-cache-prefetch-pages-per-interval" id="pe-wp-pjax-page-cache-prefetch-pages-per-interval" size="4" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-pages-per-interval']; ?>" type="text" /> seconds
                            <p class="description"><?php _e(''); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Sitemap URL:</th>
                        <td>
                            <input name="pe-wp-pjax-page-cache-prefetch-sitemap-url" id="pe-wp-pjax-page-cache-prefetch-sitemap-url" size="60" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-url']; ?>" type="text" />
                            <p class="description"><?php _e(''); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Refresh sitemap URLs interval:</th>
                        <td>
                            <input name="pe-wp-pjax-page-cache-prefetch-sitemap-refresh-interval" id="pe-wp-pjax-page-cache-prefetch-sitemap-refresh-interval" size="4" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-refresh-interval']; ?>" type="text" /> seconds
                            <p class="description"><?php _e('This is the interval for parsing and indexing all the sitemap URLs'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Refresh sitemap URLs on publish:</th>
                        <td>
                            <label for="pe-wp-pjax-page-cache-prefetch">
                                <input name="pe-wp-pjax-page-cache-prefetch-sitemap-refresh-on-publish" id="pe-wp-pjax-page-cache-prefetch-sitemap-refresh-on-publish" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-refresh-on-publish'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><strong>Not implemented yet. Coming soon. </strong><?php _e('Automaticly refresh the sitemap URLs when you publish a new post or page.'); ?></p>
                        </td>
                    
                </tbody>
            </table>

            
            
            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="wp-pjax-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    
    
    <h2>Effects</h2>
    <div id="pjax-contnet-fading" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>Loading notice / -spinner</span></h3><div class="inside">        
            <p>Display a loading notice or spinner while loading content.</p>
            <h3>Not ready yet</h3>
            
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Choose a loading icon:</th>
                        <td>
                            <input name="pe-wp-pjax-content-fade-timeout-in" id="pe-wp-pjax-content-fade-timeout-in" size="3" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-in']; ?>" type="text" /> ms
                            <p class="description"><?php _e(''); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Upload you own custom icon:</th>
                        <td>
                            <label for="pe-wp-pjax-content-fade">
                                <input name="pe-wp-pjax-content-fade" id="pe-wp-pjax-content-fade" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show a toggle for turning on and off PJAX. Great for testing the difference in page load time.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Content fade in speed:</th>
                        <td>
                            <input name="pe-wp-pjax-content-fade-timeout-in" id="pe-wp-pjax-content-fade-timeout-in" size="3" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-in']; ?>" type="text" /> ms
                            <p class="description"><?php _e('Duration for the fade in effect in milliseconds'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Content fade out speed:</th>
                        <td>
                            <input name="pe-wp-pjax-content-fade-timeout-out" id="pe-wp-pjax-content-fade-timeout-out" size="3" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-out']; ?>" type="text" /> ms
                            <p class="description"><?php _e('Duration for the fade out effect in milliseconds'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjaxc-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    
    <div id="pjax-contnet-fading" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>Fading</span></h3><div class="inside">        
            <p>Note: Fade content out and in on page load. This delays the perceived page load time(the time until the new page is fully visible) and makes the page load inconsistent between browsers that support PJAX and browsers that do not. Because of this the recommended setting is disabled.</p>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Content fading:</th>
                        <td>
                            <label for="pe-wp-pjax-content-fade">
                                <input name="pe-wp-pjax-content-fade" id="pe-wp-pjax-content-fade" value="1" type="checkbox"  <?php echo ($wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade'] == 1 ? 'CHECKED=CHECKED' : ''); ?> />
                                <strong>Enable</strong>
                            </label>
                            <p class="description"><?php _e('Show a toggle for turning on and off PJAX. Great for testing the difference in page load time.'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Content fade in speed:</th>
                        <td>
                            <input name="pe-wp-pjax-content-fade-timeout-in" id="pe-wp-pjax-content-fade-timeout-in" size="3" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-in']; ?>" type="text" /> ms
                            <p class="description"><?php _e('Duration for the fade in effect in milliseconds'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Content fade out speed:</th>
                        <td>
                            <input name="pe-wp-pjax-content-fade-timeout-out" id="pe-wp-pjax-content-fade-timeout-out" size="3" value="<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-out']; ?>" type="text" /> ms
                            <p class="description"><?php _e('Duration for the fade out effect in milliseconds'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit"> 
                <input type="submit" name="pe-wp-pjax-save-options" class="pe-wp-pjaxc-button-save button-primary" value="Save all settings">
            </p>
        </div>
    </div>
    
    </form>
</div>