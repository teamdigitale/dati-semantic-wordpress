jQuery(document).ready(oxygen_lightbox);
function oxygen_lightbox($) {
    
   let extrasLightbox = function ( container ) {

        $('.oxy-lightbox').each(function(i, OxyLightBox) {
            
        let lightboxID = $(OxyLightBox).attr('ID'),
            lightboxIDgroup = lightboxID,
            lightboxInner = $(OxyLightBox).children('.oxy-lightbox_inner'),
            lightboxType = lightboxInner.data('type'),
            lightboxLink = $(OxyLightBox).children('.oxy-lightbox_link'),
            lightboxSrc = $(OxyLightBox).children('.oxy-lightbox_inner').html(),
            lightboxClick = lightboxInner.data('click-selector'),
            lightboxFilter = lightboxInner.data('filter'),
            inlineSelector = lightboxInner.data('inline-selector'),
            inlineContent = lightboxInner.data('inner-content'),
            maybeMultiple = lightboxInner.data('multiple'),
            maybeLoop = lightboxInner.data('loop'),
            lightboxCaption = lightboxInner.data('caption'),
            fancyboxOpt = '',
            fancyboxOpts = '',
            clickSelector = '',
            lbSmallBtn = lightboxInner.data('small-btn'),
            lbPreload = lightboxInner.data('iframe-preload'),
            lbToolbar = lightboxInner.data('toolbar'),
            lbThumbs = lightboxInner.data('thumbs'),
            lbFullscreen = lightboxInner.data('fullscreen'),
            lbSlideshow = lightboxInner.data('slideshow'),
            lbautofocus = lightboxInner.data('autofocus'),
            lbbackfocus = lightboxInner.data('backfocus'),
            lbtrapfocus = lightboxInner.data('trapfocus'),
            lbcss = lightboxInner.data('lightbox-css'),
            //lbeffect = lightboxInner.data('effect'),
            lbforcetype = lightboxInner.data('force-type'),
            lbCloseIcon = lightboxInner.data('close-icon'),
            lbSmallCloseIcon = lightboxInner.data('small-close-icon'),
            lbNavIcon = lightboxInner.data('nav-icon'),
            lbZoomIcon = lightboxInner.data('zoom-icon'),
            lbDownloadIcon = lightboxInner.data('download-icon'),
            lbIframeSelector = lightboxInner.data('iframe-selector'),
            lbDuration = lightboxInner.data('duration'),
            lbclassList = $(OxyLightBox).attr("class").split(/\s+/),
            lightboxparentEl = '[data-lightbox-id="' + lightboxIDgroup + '"]',
            maybeSwipe = lightboxInner.data('swipe');


        $(OxyLightBox).attr('data-lightbox-id', lightboxID); 
        lightboxInner.attr('id', '' + lightboxID + '-inner');
        lightboxInner.attr('data-idx', '' + lightboxID);      

        if ($(OxyLightBox).closest('.oxy-dynamic-list').length) {

            lightboxIDgroup = $(OxyLightBox).attr('data-id');
            lightboxparentEl = '[data-lightbox-id="' + lightboxIDgroup + '-' + i + '"]';

            var elem = $(OxyLightBox).closest('.oxy-dynamic-list > .ct-div-block').find(lightboxClick);

                elem.attr('data-link', 'link' + lightboxIDgroup + '-' + i);

                $(OxyLightBox).attr('data-lightbox-id', lightboxIDgroup + '-' + i); 
                lightboxInner.attr('id', '' + lightboxIDgroup + '-' + i + '-inner' );
                lightboxInner.attr('data-idx', '' + lightboxIDgroup + '-' + i);

        } else {

            if ('custom' !== lightboxType) {

                $(lightboxClick).attr('data-link', 'link' + lightboxIDgroup + '-' + i); 

            }

        }
        
        
        
        // If we're doing modal functionality, no need for the link
        if (('inline' === lightboxType) && (true === inlineContent)) {
           // nothing
        } else {
             lightboxLink.attr('data-link', 'link' + lightboxIDgroup + '-' + i);
        }

        if ('inline' === lightboxType) {
            
            if ($(OxyLightBox).closest('.oxy-dynamic-list').length) {
                lightboxSrc = (true !== inlineContent) ? inlineSelector : '[data-idx="' + lightboxIDgroup + '-' + i + '"]';
            } else {
                lightboxSrc = (true !== inlineContent) ? inlineSelector : '[data-idx="' + lightboxIDgroup + '"]';
            }
            
        } else if ('ajax' === lightboxType) {

            lightboxSrc = lightboxInner.data('src') + ' ' + lightboxFilter;

        } else {

            lightboxSrc = lightboxInner.data('src');

        }
        
        clickSelector = ('custom' === lightboxType) ? lightboxClick : '[data-link="link' + lightboxIDgroup + '-' + i + '"]';
        
        let fancyclickSelector = ('custom' === lightboxType) ? clickSelector : '';

        lbprepend = (false == lightboxInner.data('prepend')) ? lightboxparentEl : 'body';
        lbbaseClass = (true == lightboxInner.data('prepend')) ? lbclassList : '';
        
        fancyboxOpts = {
            parentEl: lbprepend,
            selector : fancyclickSelector,
            autoSize: false,
            "iframe": {
                preload: lbPreload,
            },
            baseClass: lbbaseClass,
            hash: false,
            touch: 0,
            caption : lightboxCaption,
            preventCaptionOverlap: false,
            infobar: true,
            wheel: "auto",
            btnTpl: {
                
            download:
              '<a download data-fancybox-download class="fancybox-button fancybox-button--download" title="{{DOWNLOAD}}" href="javascript:;">' +
              '<svg viewBox="0 0 40 40">' +
              '<use xlink:href="#' + lbDownloadIcon + '"></use>' +
              "</svg>" +
              "</a>",

            zoom:
              '<button data-fancybox-zoom class="fancybox-button fancybox-button--zoom" title="{{ZOOM}}">' +
              '<svg viewBox="0 0 40 40">' +
              '<use xlink:href="#' + lbZoomIcon + '"></use>' +
              "</svg>" +
              "</button>",    
                
            arrowLeft:
              '<button data-fancybox-prev class="fancybox-button yes fancybox-button--arrow_left" title="{{PREV}}">' +
              '<svg viewBox="0 0 40 40">' +
              '<use xlink:href="#' + lbNavIcon + '"></use>' +
              "</svg>" +   
              "</button>",

            arrowRight:
              '<button data-fancybox-next class="fancybox-button fancybox-button--arrow_right" title="{{NEXT}}">' +
              '<svg viewBox="0 0 40 40">' +
              '<use xlink:href="#' + lbNavIcon + '"></use>' +
              "</svg>" +
              "</button>",
                
            close:
              '<button data-fancybox-close class="fancybox-button fancybox-button--close" title="{{CLOSE}}">' +
              '<svg viewBox="0 0 24 24">' +
              '<use xlink:href="#' + lbCloseIcon + '"></use>' +
              "</svg>" +
              "</button>", 

              smallBtn:
                '<button type="button" data-fancybox-close class="fancybox-button fancybox-close-small" title="{{CLOSE}}">' +
                '<svg viewBox="0 0 24 24">' +
                '<use xlink:href="#' + lbSmallCloseIcon + '"></use>' +
                "</svg>" +
                "</button>",
            },
            
            autoFocus: lbautofocus,
            backFocus: lbbackfocus,
            trapFocus: lbtrapfocus,
            thumbs: false,
            fullScreen: false,
            slideShow: lbSlideshow,
            animationEffect: 'extras',
            transitionEffect: 'slide',
            animationDuration: lbDuration,
            loop: maybeLoop,

            afterLoad: function( instance, slide, current ){
                
                
                if (current.type === 'video') {
                
                    slide.$slide.find(".fancybox-iframe")
                    
                }
                
                
                if (current.type === 'iframe') {
                
                    $(".fancybox-slide--video > .fancybox-content .fancybox-iframe").wrap('<div class=fancybox-content_wrapper></div>');

                    // iframe
                    slide.$slide.find(".fancybox-iframe").contents().find("#wpadminbar").hide();
                    slide.$slide.find(".fancybox-iframe").contents().find("html").append('<style type="text/css">html { margin-top: 0!important; } </style>'); 
                    slide.$slide.find(".fancybox-iframe").contents().find("html").addClass('extras-inside-lightbox');
                
                    if (typeof lbIframeSelector !== "undefined") {
                
                        let slideBody = slide.$slide.find(".fancybox-iframe").contents().find('body');

                        slideBody.find(lbIframeSelector).prependTo(slideBody);

                        slideBody.find(' > *:not(script):not(style):not(svg):not(' + lbIframeSelector + ')').hide(); 
                        
                    } 
                
                }
                
                if ((current.type === 'ajax') && (lbcss)) {
                    
                    slide.$slide.append('<link rel="stylesheet" type="text/css" href="'+ localize_extras_plugin.oxygen_directory + lbcss +' ">');
                    
                }

                $(OxyLightBox).trigger('extras_lightbox:after_load');
                
            },
           
            beforeShow: function( instance, slide, current ) {
                if (typeof(Event) === 'function') {
                     window.dispatchEvent(new Event('resize'));
                } else {
                    $(window).trigger('resize');
                }
                $(OxyLightBox).trigger('extras_lightbox:visible');
                if (typeof doExtrasReadmore == 'function') {
                    
                    setTimeout(function(){ 
                        if (slide.$slide.find(".oxy-read-more-less").length) {
                            slide.$slide.find(".oxy-read-more-inner").removeAttr("style");
                            doExtrasReadmore(slide.$slide);
                        }
                        $('.oxy-read-more-link:not(:nth-child(2)').remove();
                    }, 50);
                    
                }
            },

            beforeClose : function( instance, current, e ) {
                $(OxyLightBox).trigger('extras_lightbox:close');
                
                if ( ( 'custom' === lightboxType ) && ( lightboxInner.attr('data-click-selector').indexOf('.oxy-carousel-builder_gallery-image') > -1 ) ) {
                   
                    let $carousel = $(lightboxInner.attr('data-click-selector')).closest('.oxy-carousel-builder_gallery-images');
                    if (typeof Flickity == 'function') {
                        $carousel.flickity( 'selectCell', $.fancybox.getInstance().currIndex );	
                    }
                }

                setTimeout(function() {
                    $(clickSelector + ' .oxy-lightbox_trigger').focus();
                }, 30);
                
            },

            toolbar: lbToolbar,
            smallBtn: lbSmallBtn,
            
            buttons: [
                "zoom",
                "slideShow",
                "download",
                "close"
              ],
            
            
            tpl: {
                closeBtn: '<a title="Close" class="fancybox-item fancybox-close myClose" href="javascript:;"></a>'
            },
            
        };

        if ( true === maybeSwipe ) {
            
            fancyboxOpts.touch = {
                vertical: false,
                momentum: true
            };
        } 

        if ( true  === lbThumbs ) {

            fancyboxOpts.thumbs = {
                autoStart : true,
                axis      : 'x'
            };

        }

        let lightboxTypeFinal = ('video' === lightboxType) ? '' : lightboxType;

        fancyboxOpt = [{
            src: lightboxSrc,
            type: lightboxTypeFinal,
            opts: fancyboxOpts,
        }];
            
        if ('custom' !== lightboxType) {
            
            //not grouping
            if (true !== maybeMultiple) {
                
                $(clickSelector).off('click');
                $(clickSelector).on('click', function(e) {    
                        if (e.target === this) {
                            e.stopPropagation();
                            e.preventDefault();
                        }

                        if (e.target !== this) {
                            e.preventDefault();
                        }
                        
                        $.fancybox.open(fancyboxOpt);

                        $(e.target).addClass('oxy-lightbox_trigger')

                });
                
            } else { // grouped
                
                let uniiiq = '[data-link="link' + lightboxIDgroup + '-' + i + '"]';
                
                if ('inline' === lightboxType) {
                    
                    $(uniiiq).attr('data-fancybox', lightboxIDgroup );

                    if (true === inlineContent) {

                        if ( $(OxyLightBox).closest('.oxy-dynamic-list').length) {

                            $(clickSelector).attr('data-src', '#' + lightboxIDgroup + '-' + i + '-inner' );
                            $(clickSelector).attr('data-fancybox', lightboxIDgroup );
                            $(clickSelector).data('src', '#' + lightboxIDgroup + '-' + i + '-inner' );
                            $(clickSelector).data('fancybox', lightboxIDgroup );

                        } else {
                            $(clickSelector).attr('data-src', '#' + lightboxIDgroup + '-inner' );
                            $(clickSelector).attr('data-fancybox', lightboxIDgroup );
                            $(clickSelector).data('src', '#' + lightboxIDgroup + '-inner' );
                            $(clickSelector).data('fancybox', lightboxIDgroup );
                        }

                        $(lightboxClick).fancybox(fancyboxOpts);

                    } else {
                        console.log('OxyExtras | Not yet support for grouping multiple inline elements');
                    }
                    
                } else if ('ajax' === lightboxType) {

                    if (lightboxInner.data('src')) {    
                       $(uniiiq).attr('data-fancybox', lightboxIDgroup );
                       $(uniiiq).data('fancybox', lightboxIDgroup );
                       $('.oxy-lightbox_link[data-fancybox="' + lightboxIDgroup + '"]').fancybox(fancyboxOpts);
                    }
                    
                } else if ('iframe' === lightboxType) {
                    
                    if (lightboxSrc) {
                        $(uniiiq).attr('data-fancybox', lightboxIDgroup );
                        $('.oxy-lightbox_link[data-fancybox="' + lightboxIDgroup + '"]').fancybox(fancyboxOpts);
                    }
                    
                } else if ('video' === lightboxType) {
                    
                    if (lightboxSrc) {
                        $(uniiiq).attr('data-fancybox', lightboxIDgroup );
                        $(uniiiq).attr('data-src', lightboxSrc);
                        $('.oxy-lightbox_link[data-fancybox="' + lightboxIDgroup + '"]').fancybox(fancyboxOpts);
                    }
                    
                } else {
                    if (lightboxSrc) {
                        $(uniiiq).attr('data-fancybox', lightboxIDgroup );
                        $(uniiiq).attr('data-type', lightboxType); 
                        $(uniiiq).attr('data-src', lightboxSrc);
                        $('.oxy-lightbox_link[data-fancybox="' + lightboxIDgroup + '"]').fancybox(fancyboxOpts);
                    }
                    
                }
               
                
            }
            
            
        } else { // going manual
            
            $(container).find(clickSelector).each(function(i, OxyLightBoxClick) {
                    
                    var attr = $(OxyLightBoxClick).attr('href');
                    
                    if (typeof attr !== typeof undefined && attr !== false) {
                        
                        
                        let src = $(OxyLightBoxClick).data('src');
                        
                        if (typeof src !== typeof undefined && src !== false) { // we have a data-src, it's a custom link
                            
                            let filter = $(OxyLightBoxClick).data('filter');
                            if (filter) {
                                $(OxyLightBoxClick).data('type', 'ajax');
                            } else {
                                $(OxyLightBoxClick).data('type', 'iframe');
                            }
                            
                        } else { // we don't have a src. ie it's from another component
                            
                            if ('auto' !== lbforcetype) {
                                
                                $(OxyLightBoxClick).data('type', lbforcetype);
                                
                                $(OxyLightBoxClick).data('src', attr);
                                
                                if ('ajax' === lbforcetype) {
                                    
                                    $(OxyLightBoxClick).data('filter', lightboxFilter);
                                }
                                
                            } 
                            
                        }
                        
                    }
                    
                });
            
            
            if (true === maybeMultiple) {
                
                var attr = $(clickSelector).attr('href');
                if (typeof attr !== typeof undefined && attr !== false) {
                    
                    $(clickSelector).attr('data-fancybox', lightboxID );
                    
                    $(clickSelector).fancybox(fancyboxOpts);
                    
                } else {
                    // Give user error
                    console.log('OxyExtras | Selector needs to be a link');
                }
                
            } else {
                
                var attr = $(clickSelector).attr('href');
                if (typeof attr !== typeof undefined && attr !== false) {
                    
                    // it's a link
                    $(container).find(clickSelector).on('click', function(event) {
                        
                        let linksrc = $(this).attr('href');
                        let linktype = '';
                        
                        let src = $(this).data('src');
                        if (src) {
                            
                            linksrc = src;
                            
                            let filter = $(this).data('filter');
                            
                            if (filter) {
                                linktype = 'ajax';
                            } else {
                                linktype = 'iframe';
                            }
                            
                            linksrc = src;
                            
                        
                        event.preventDefault();    
                        event.stopPropagation();  
                            
                          // Has it been forced?    
                          let fancylinksrc = ('ajax' === linktype) ? linksrc + ' ' + filter : linksrc;
                            
                           $.fancybox.open({
                                src  : fancylinksrc,
                                type  : linktype,
                                opts : fancyboxOpts
                            });
                            
                        } else {

                            event.preventDefault();    
                            event.stopPropagation(); 
                            
                            $.fancybox.open({
                                src  : linksrc,
                                opts : fancyboxOpts
                            });
                        }

                    });
                
                } 
                
                else { // Element isnt link
                    
                    console.log('OxyExtras | Selector needs to be a link');
                    
               }     
                
            }

        }

    });
       
   }
   
   extrasLightbox('body');
                
    // Expose function
    window.doExtrasLightbox = extrasLightbox;

}