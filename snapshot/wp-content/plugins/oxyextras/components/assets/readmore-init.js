jQuery(document).ready(oxygen_init_readmore);
function oxygen_init_readmore($) {
    
    let extrasReadmore = function ( container ) {
        
        $(container).find('.oxy-read-more-inner').each(function(i, oxyReadMore){

            let readMore = $(oxyReadMore),
                readMoreID = readMore.attr('ID'),
                openText = readMore.data( 'open' ),
                closeText = readMore.data( 'close' ),
                speed = readMore.data( 'speed' ),
                heightMargin = readMore.data( 'margin' ), 
                icon = ('enable' === readMore.data( 'icon' )) ? '<span class="oxy-read-more-link_icon"><svg class="oxy-read-more-link_icon-svg"><use xlink:href="#' + readMore.data( 'iconopen' ) + '"></use></svg></span>' : '',

                moreText = '<a href=# class=oxy-read-more-link><span class="oxy-read-more-link_text">' + openText + '</span>' + icon +'</a>',
                lessText = '<a href=# class=oxy-read-more-link><span class="oxy-read-more-link_text">' + closeText + '</span>' + icon +'</a>';

                readMore.attr('id', readMoreID + '_' + i);
            
                if ($(oxyReadMore).closest('.oxy-dynamic-list').length) {
                    readMore.attr('id', readMoreID + '_' + $(oxyReadMore).closest('.oxy-dynamic-list > .ct-div-block').index() + 1);
                }
        
            function doReadMore() {

                new Readmore(readMore, {
                        speed: speed,
                        moreLink: moreText,
                        lessLink: lessText,
                        embedCSS: false,
                        collapsedHeight: parseInt(readMore.css('max-height')),
                        heightMargin: heightMargin,
                        beforeToggle: function(trigger, element, expanded) {
                        if(!expanded) { // The "Close" link was clicked
                            $(element).addClass('oxy-read-more-less_expanded');
                            readMore.parent('.oxy-read-more-less').trigger('extras_readmore:expand');
                        } else {  
                            $(element).removeClass('oxy-read-more-less_expanded');
                            readMore.parent('.oxy-read-more-less').trigger('extras_readmore:collapse');
                        }
                        },
                        afterToggle: function(trigger, element, expanded) {
                        if(expanded) {
                            readMore.parent('.oxy-read-more-less').trigger('extras_readmore:expanded');
                        } else {
                            readMore.parent('.oxy-read-more-less').trigger('extras_readmore:collapsed');
                        }
                        },
                        blockProcessed: function(element, collapsable) {
                        if(! collapsable) {
                            readMore.addClass('oxy-read-more-less_not-collapsable');
                            readMore.parent('.oxy-read-more-less').find('.oxy-read-more-link').remove();
                        }
                        readMore.parent('.oxy-read-more-less').trigger('extras_readmore:processed');
                        }
                    });

                    $('.oxy-read-more-link + .oxy-read-more-link').remove();

                }

            doReadMore();

            if (readMore.closest('.oxy-tabs-contents').length) {
                $('.oxy-tab').on('click', function() {
                    readMore.css('max-height', '')
                    setTimeout(function() {
                        doReadMore();
                        readMore.siblings('.oxy-read-more-link + .oxy-read-more-link').remove();
                        window.dispatchEvent(new Event('resize'));
                    }, 10);
                });
            }

            if (readMore.closest('.oxy-pro-accordion').length) {
                readMore.closest('.oxy-pro-accordion').on('extras_pro_accordion:toggle', function() {
                    doReadMore();
                    readMore.siblings('.oxy-read-more-link + .oxy-read-more-link').remove();
                });
            }

        }); 
        
        window.dispatchEvent(new Event('resize'));

        /* Force resize again after everything loaded (for Safari fix) */
        jQuery(window).on('load', function(){
            setTimeout(function(){
                window.dispatchEvent(new Event('resize'));
            }, 1000);
        });
        
    }
    
    extrasReadmore('body');
    
    // Expose function
    window.doExtrasReadmore = extrasReadmore;
    
    
}