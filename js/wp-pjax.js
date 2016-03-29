/*
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
define('WP_DEBUG', true);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );
ini_set("display_errors", 1);
* /

$config = wp_pjax_config()->get();

if (empty($config['menu-selector']) || empty($config['content-selector']) )
{
    trigger_error('ERROR: WP-PJAX Not set up correctly. Not loading JS. '.__FILE__ . ' (' .__LINE__ . ')', E_USER_NOTICE );
    return '';
}

if( !empty( $config['menu-active-class'] ) )
{
    $active_classes = explode(' ',  $config['menu-active-class']);

    $active_classes = '.'.implode(', .', $active_classes);
}

?>

<script type = "text/javascript" charset = "UTF-8">
*/
console.log(wp_pjax_config);

jQuery.cookie=function(a,b,c){if(arguments.length>1&&String(b)!=="[object Object]"){c=jQuery.extend({},c);if(b===null||b===undefined)c.expires=-1;if(typeof c.expires=="number"){var d=c.expires,e=c.expires=new Date;e.setDate(e.getDate()+d)}b=String(b);return document.cookie=[encodeURIComponent(a),"=",c.raw?b:encodeURIComponent(b),c.expires?"; expires="+c.expires.toUTCString():"",c.path?"; path="+c.path:"",c.domain?"; domain="+c.domain:"",c.secure?"; secure":""].join("")}c=b||{};var f,g=c.raw?function(a){return a}:decodeURIComponent;return(f=(new RegExp("(?:^|; )"+encodeURIComponent(a)+"=([^;]*)")).exec(document.cookie))?g(f[1]):null}

var time;
var localtorage;

jQuery(function($){

    console.log($( wp_pjax_config.menu_selector ));
    console.log($( wp_pjax_config.content_selector ));


    if( $( wp_pjax_config.menu_selector ).length == 0 ||  $( wp_pjax_config.content_selector).length == 0 )
    {
        //'ERROR: WP-PJAX Not set up correctly. Not loading JS.
        alert('asdasd');
        return;
    }

    /*
    if( $( wp_pjax_config.menu_active_class ) )
    {
        $active_classes = explode(' ',  $config['menu_active_class']);

        $active_classes = '.'.implode(', .', $active_classes);
    }
    */

    console.log(wp_pjax_config.menu_selector, wp_pjax_config.content_selector);


    $(document).pjax(wp_pjax_config.menu_selector, wp_pjax_config.content_selector);//, {
    //fragment: '<?php echo $config['content-selector']; ?>',
    //timeout: 4000
    //))});
    /*
     if ($.support.pjax) {
     $(document).on('click', '<?php echo $config['menu-selector']; ?>', function(event) {
     //var container = $(this).closest('[data-pjax-container]')
     $.pjax.click(event, {container: '<?php echo $config['content-selector']; ?>'})
     })
     }
     */

    if( wp_pjax_config.load_timeout > 0 )
    {
        $.pjax.defaults.timeout = wp_pjax_config.load_timeout;
    }
    else
    {
        $.pjax.defaults.timeout = false;
    }


    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault()
    })

    $(document).on('pjax:beforeSend', function(event, xhr, settings) {

        //Prevent double loading and load chaining
        if (xhr)
        {
            //console.log('xhr');
            //console.log(xhr);
            //alert('asdasd');

            //xhr.abort();
            //return true;
            //return false;
        }


        //deleteAllCookies();

        //Remove old link active classes
        $( wp_pjax_config.menu_active_class.replace(" ", ",") ).removeClass(wp_pjax_config.menu_active_class);
        //$('a').parent().removeClass('<?php echo $config['menu-active-class']; ?>');
    });

    $(document).on('pjax:start', function(event, xhr, settings) {

        Array.prototype.map.call(document.querySelector( wp_pjax_config.content_selector ).querySelectorAll("iframe"), function(iframe) { iframe.src = "about:blank"; });

        /*
        <?php
        if( isset($config['pre-handler']) )
            print $config['pre-handler'];
        ?>
        */

        $( document ).trigger('wp-pjax-pre-handler');

        if( wp_pjax_config.show_extended_notice )
        {
            var d = new Date();
            time = d.getTime()
        }

        /*
        <?php if( $config['show-extended-notice'] ) : ?>
        var d = new Date();
        time = d.getTime()
        <?php endif; ?>
        */

        /*
        <?php if( $config['content-fade'] )
            $('<?php echo $config['content-selector']; ?>').animate({opacity: 0.1}, <?php echo $config['content-fade-timeout-out']; ?>);
        <?php endif; ?>
        */

        if(  wp_pjax_config.content_fade )
        {
            $( wp_pjax_config.content_selector ).animate({opacity: 0.1}, wp_pjax_config.content_fade_timeout_out );
        }

    });

    $(document).on('pjax:end', function(event, request, settings) {

        console.log(event);
        console.log(request);
        console.log(settings);
        /*
        <?php
        if( isset($config['post-handler']) )
            print $config['post-handler'];
        ?>
        */
        $( document.body ).trigger('wp-pjax-post-handler', [event, request, settings] );

        //Hack to get a location object from url string
        url = document.createElement('a');
        url.href = settings.url;

        var protocol = url.protocol;
        var hash = url.hash;

        //add link active classes
        $("a[href$='"+url.pathname+"']").parent().addClass( wp_pjax_config.menu_active_class );//.css('background-color','red');

        if( wp_pjax_config.content_fade ) {
            $( wp_pjax_config.content_selector ).animate( {opacity: 1}, wp_pjax_config.content_fade_timeout_in );
        }

        //fadeIn(<?php echo $config['content-fade-timeout-in']; ?>)

        /*
        <?php if( $config['show-notice'] == 1 ) : ?>

        var noticeText;
        <?php if( $config['show-extended-notice'] ) : ?>
        var cacheHit = request.getResponseHeader('PJAX-Page-Cache');
        var XCacheHit = request.getResponseHeader('X-Cache-Hit');
        var resource = request.getResponseHeader('PJAX-loaded-resource');

        var d = new Date();
        time = d.getTime() - time;

        noticeText = resource + '<br /> Loaded with PJAX! <br /> Load time: ' + time + 'ms (total front end) <br /> PJAX page cache: ' + cacheHit + '<br /> Varnish cache: ' + XCacheHit;

        <?php else : ?>

        noticeText = 'Loaded with PJAX!';

        <?php endif; ?>



        $.noticeAdd({
            text: noticeText,
        <?php if( $config['notice-sticky'] ) : ?>
        stay: true
        <?php else : ?>
        stayTime: <?php echo $config['notice-timeout']; ?>
        <?php endif; ?>
    });

        <?php endif; ?>
        */


        if( request.status != 0 && wp_pjax_config.show_notice == 1 ) {
            var noticeText;

            if( wp_pjax_config.show_extended_notice ) {
                var cacheHit = request.getResponseHeader('PJAX-Page-Cache');
                var XCacheHit = request.getResponseHeader('X-Cache-Hit');
                //var resource = request.getResponseHeader('PJAX-loaded-resource');
                var resource = window.location.href ;

                var d = new Date();
                time = d.getTime() - time;

                noticeText = resource + '<br /> Loaded with PJAX! <br /> Status: ' + request.status + '<br /> Load time: ' + time + 'ms (total front end) <br /> PJAX page cache: ' + cacheHit + '<br /> Varnish cache: ' + XCacheHit;
            }
            else {
                noticeText = 'Loaded with PJAX!';
            }

            $noticeData = {
                text: noticeText
            };

            if( wp_pjax_config.notice_sticky ) {
                $noticeData.stay = true
            } else {
                $noticeData.stayTime = wp_pjax_config.notice_timeout
            }

            $.noticeAdd($noticeData);
        }
    });


    $(document).ready(function() {

        if( wp_pjax_config.show_notice == 1 ) {
            if( !$.support.pjax ) {
                $('#wp-pjax-toggle-container').html('<p style="color: red">PJAX is not supported in this browser. Se <a href="http://caniuse.com/#search=pushstate">compatibility list</a></p>');
            }

            $('.notice-item').on('click', function() {
                jQuery.noticeRemove($(this), 400);
            });

            $('#wp-pjax-toggle').change( function() {

                if( $(this).prop('checked') )
                {
                    $('#wp-pjax-toggle-status').html('Enabled').css('color', 'green');
                }
                else
                {
                    $(document).pjax.disable();
                    $('#wp-pjax-toggle-status').html('Disabled').css('color', 'red');
                }
            });
        }

    });

    function getLocation(href) {
        var l = document.createElement("a")
        l.href = href
        return l
    }

});

function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}
