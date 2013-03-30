(function($){
    // TODO: adjust this selector based on the available menus
    var menuLinkSelector = '.nav-menu a';

    $(document).ready(function() {
        $(<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'menu-selector']; ?>).pjax('<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'content-selector']; ?>').live('click', function(e) {       
            alert('asdasda');
            $('<?php echo $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'menu-active-class']; ?>')<?php foreach($active_class_array AS $ac ){ echo '.removeClass'; } ?>removeClass('');
            $(this).parent().addClass('current-menu-item').parent().parent('li').addClass('current-menu-item');
        });
        
        
        $('body').bind('pjax:start', function() {
            $(this).ajaxSuccess(function(event, request, settings) {
                // CSS template
                var classes = $(this).attr('class');
                var template = request.getResponseHeader('X-Thematic-Template');
                if (classes.indexOf(template) === -1) {
                    $(this).attr('class', classes.replace(/page-template-([^\s]+)/, template));
                }
                var link = {
                    canonical: request.getResponseHeader('X-Link-Canonical'),
                    previous: request.getResponseHeader('X-Link-Previous'),
                    next: request.getResponseHeader('X-Link-Next')
                }
                var title = {
                    previous: request.getResponseHeader('X-Title-Previous'),
                    next: request.getResponseHeader('X-Title-Next')
                }
                // Canonical link
                $('link[rel="canonical"]').attr('href', link.canonical);
                // Previous link
                $('link[rel="prev"]').attr('href', link.previous);
                $('link[rel="prev"]').attr('title', title.previous);
                if ($('link[rel="prev"]').length === 0) {
                    $('head').append('<link rel="previous" title="' + title.previous + '" href="' + link.previous + '" />');
                }
                // Next link
                $('link[rel="next"]').attr('href', link.next);
                $('link[rel="next"]').attr('title', title.next);
                if ($('link[rel="next"]').length === 0) {
                    $('head').append('<link rel="previous" title="' + title.next + '" href="' + link.next + '" />');
                }
            });
        });
        
        $('body').bind('pjax:end', function(event, request, settings) {

            console.log(event);
            console.log(request);
            console.log(request.readyState);
            console.log(request.responseText);
            
            <?php if( $wp_pjax_options[WP_PJAX_CONFIG_PREFIX.'show-notice'] == 1 ) : ?>
            
            
            jQuery.noticeAdd(
                text: 'test',
            );
            
            <?php endif; ?>

        });
    });
})(jQuery);