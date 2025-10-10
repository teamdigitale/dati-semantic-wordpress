jQuery(document).ready(oxygen_init_megamenu);
function oxygen_init_megamenu($) {

    var touchEvent = 'ontouchstart' in window ? 'click' : 'click';

    var url = window.location;
    var pathname = window.location.pathname;

    $(".oxy-mega-dropdown_link").filter(function() {
        return (this.href == url || this.href + '/' == url || this.href == pathname || this.href + '/' == pathname );
    }).addClass('oxy-mega-dropdown_link-current');

    $('.oxy-mega-dropdown_flyout').has('.current-menu-item').siblings('.oxy-mega-dropdown_link').addClass('oxy-mega-dropdown_link-current-ancestor');

    $(".oxy-mega-menu").each(function(i, oxyMegaMenu){

        var $oxyMegaMenu = $( oxyMegaMenu ),
            inner = $oxyMegaMenu.children('.oxy-mega-menu_inner'),
            oxyMegaMenuID = $( oxyMegaMenu ).attr('id'),
            clicktrigger = inner.data('trigger'),
            oDelay = inner.data('odelay'),
            cDelay = inner.data('cdelay'),
            flyMenu = inner.find('.oxy-mega-dropdown_flyout-click-area').parent('.oxy-mega-dropdown_link'),
            slideDuration = inner.data('duration'),
            mouseoverReveal = inner.data('mouseover'),
            preventScroll = inner.data('prevent-scroll'),
            hashlinkClose = inner.data('hash-close'),
            slide_trigger_selector = clicktrigger,
            slideClickArea = $oxyMegaMenu.find('.oxy-mega-dropdown_icon'),
            auto_aria = inner.data('auto-aria');

            inner.find('.oxy-mega-dropdown_link').css("cursor","pointer");

              
            function ariaExpandToggle($state) {
                if ( true === auto_aria ) {
                    $(slide_trigger_selector).each(function(i,trigger) {
                        if ( $(trigger).hasClass('oxy-burger-trigger') && $(trigger).children('.hamburger').length ) {
                            $(trigger).children('.hamburger').attr('aria-expanded', $state);
                        } else {
                            $(trigger).attr('aria-expanded', $state);
                            $(trigger).attr('role','button');
                        }
                    });
                }
            }

              if ( true === auto_aria ) {

                $(slide_trigger_selector).each(function(i,trigger) {

                    if ( $(trigger).hasClass('oxy-burger-trigger') && $(trigger).children('.hamburger').length ) {
                        $(trigger).children('.hamburger').attr('aria-controls', oxyMegaMenuID);
                    } else {
                        $(trigger).attr('aria-controls', oxyMegaMenuID);
                    }
                });

                ariaExpandToggle('false');
             }

              $(slide_trigger_selector).on( touchEvent, function(e) { 

                 e.stopPropagation();
                 e.preventDefault();  
                 $oxyMegaMenu.slideToggle(slideDuration);

                 if ( true === auto_aria ) {

                    if ( 'true' === $(slide_trigger_selector).attr('aria-expanded') || 'true' === $(slide_trigger_selector).children('.hamburger').attr('aria-expanded') ) {
                        ariaExpandToggle('false');
                    } else {
                        ariaExpandToggle('true');
                    }

                 }

                 $oxyMegaMenu.children('.oxy-mega-menu_inner').toggleClass('oxy-mega-menu_mobile');
                  
                 if (true === preventScroll) {
                    $('body,html').toggleClass( 'oxy-nav-menu-prevent-overflow' );
                 }  
              } );

        
        var megaStatus = false;
        var mobileStatus = false;
        var megaInitialised = false;
        
        // --> Trigger accessible menu just 1x...
            var triggerMegaMenu = function() {

              if (!megaStatus) {

                megaStatus = true; // so only fires 1x

                if (!megaInitialised) {

                      $(oxyMegaMenu).accessibleMegaMenu({
                        uuidPrefix: oxyMegaMenuID,
                        menuClass: "oxy-mega-menu_inner",
                        topNavItemClass: "oxy-mega-dropdown",
                        panelClass: "oxy-mega-dropdown_inner",
                        panelGroupClass: "mega-column",
                        hoverClass: "oxy-mega-dropdown_inner-hover",
                        focusClass: "oxy-mega-dropdown_inner-focus",
                        openClass: "oxy-mega-dropdown_inner-open",
                        toggleButtonClass: "oxy-burger-trigger",
                        openDelay: oDelay,
                        closeDelay: cDelay,
                        openOnMouseover: mouseoverReveal
                    });

                    megaInitialised = true;

                } else {
                    $(oxyMegaMenu).data('plugin_accessibleMegaMenu').init();
                }
                  
                $('.oxy-mega-dropdown_just-link').off( "click" ); 
                  
                if (true === inner.data('hovertabs')) {

                    $oxyMegaMenu.find('.oxy-tab').attr('tabindex','0');

                    $oxyMegaMenu.find('.oxy-tab').on('mouseenter focus', function() { 
                        $(this).click(); 
                    }); 
                    
                }

              }

            }

       // Only fire the megamenu function is menu is visible (meaning we're not on mobile)
        var checkMegaDisplay = function() {
                
            // Desktop
            if ( 'hidden' === $( oxyMegaMenu ).css("backface-visibility") ) {
                $(oxyMegaMenu).removeAttr("style");
                $(oxyMegaMenu).find('.oxy-mega-dropdown_inner').removeAttr("style");
                triggerMegaMenu();
                $( oxyMegaMenu ).off( touchEvent );
                mobileStatus = false;
                $('body,html').removeClass( 'oxy-nav-menu-prevent-overflow' );
                $(oxyMegaMenu).find('.oxy-mega-menu_inner').removeClass('oxy-mega-menu_mobile');
              } 
            // Mobile
            else {

                  // if MegaMenu already init, let's remove it
                  if (megaStatus) {  
                        $( oxyMegaMenu).find('.oxy-mega-dropdown_link').removeClass('oxy-mega-dropdown_inner-open');  
                        $( oxyMegaMenu ).data('plugin_accessibleMegaMenu').destroy();
                        $oxyMegaMenu.find('.oxy-tab').off('mouseenter focus');
                        megaStatus = false; 
                  }
                
                  if (!mobileStatus) {
                      
                      mobileStatus = true; // so only fires 1x

                    if ($(slide_trigger_selector).hasClass('oxy-burger-trigger')) {
                        $(slide_trigger_selector).children('.hamburger').removeClass('is-active');
                    }
                      
                      $( oxyMegaMenu).find('.oxy-mega-dropdown_link[data-expanded=enable]').addClass('oxy-mega-dropdown_inner-open');
                      
                      $( oxyMegaMenu ).off( touchEvent );
                      $( oxyMegaMenu ).on( touchEvent, '.oxy-mega-menu_mobile li > a', function(e) {


                          if ($(e.target).closest('.oxy-mega-dropdown_flyout-click-area').length > 0) { 
                                
                                e.preventDefault();
                                e.stopPropagation();
                                oxy_subMenu_toggle(this, slideDuration);
                            }
                          
                            else if ($(e.target).attr("href") === "#" && $(e.target).parent().hasClass('menu-item-has-children')) {
                               
                                var subflyoutButton = $(e.target).find('.oxy-mega-dropdown_flyout-click-area');
                                e.preventDefault();
                                e.stopPropagation();
                                oxy_subMenu_toggle(subflyoutButton, slideDuration);
                                
                            } else if ($(e.target).closest('.oxy-mega-dropdown_link:not(.oxy-mega-dropdown_just-link) .oxy-mega-dropdown_icon').length > 0) {
                          
                                e.stopPropagation();
                                e.preventDefault();
                                oxy_megaMenu_toggle(this, slideDuration);

                            } else if ($(e.target).closest('.oxy-mega-dropdown_link').is('a[href^="#"]') && $(e.target).closest('.oxy-mega-dropdown_link').parent().hasClass('oxy-mega-dropdown') && !$(e.target).closest('.oxy-mega-dropdown_link').hasClass('oxy-mega-dropdown_just-link') ) {
                                
                                e.stopPropagation();
                                e.preventDefault();
                                oxy_megaMenu_toggle(this, slideDuration);
                                
                            } else if ($(e.target).closest('.oxy-mega-dropdown_link').data('disable-link') === 'enable') {
                                
                                e.stopPropagation();
                                e.preventDefault();
                                oxy_megaMenu_toggle(this, slideDuration);
                                
                            } else if ( $(e.target).closest('.oxy-mega-dropdown_link').hasClass('oxy-mega-dropdown_just-link') && $(e.target).closest('.oxy-mega-dropdown_link').is('a[href^="#"]') ) {

                                e.stopPropagation();
                               setTimeout(function() {
                                    $(slide_trigger_selector).trigger('click')
                                }, 0);

                            }
                              
                          });
                      
                  }
                      
                
            }

        }

        
        checkMegaDisplay();
        $(window).on("load resize orientationchange",function(e){
          checkMegaDisplay();
        });

        if (true === hashlinkClose) {

            inner.on('click', '.oxy-mega-dropdown_inner a[href*="#"]:not(.menu-item-has-children > a), a.oxy-mega-dropdown_just-link[href*="#"]', function() {

                if ('hidden' === $oxyMegaMenu.css("backface-visibility")) { // If desktop
        
                    $oxyMegaMenu.find('.oxy-mega-dropdown_inner-open').removeClass('oxy-mega-dropdown_inner-open');
        
                } else {

                    if ( $(this).closest('.oxy-mega-dropdown_inner').siblings('.oxy-mega-dropdown_inner-open').length ) {
                        $(this).closest('.oxy-mega-dropdown_inner').siblings('.oxy-mega-dropdown_inner-open').trigger('click');
                    }

                    $(slide_trigger_selector).trigger('click');

                }
        
            });
            

        }


    });  // each


    
    function oxy_subMenu_toggle(trigger, durationData) {
        $(trigger).closest('.menu-item-has-children').children('.sub-menu').slideToggle( durationData );
        $(trigger).closest('.menu-item-has-children').siblings('.menu-item-has-children').children('.sub-menu').slideUp( durationData );
        $(trigger).children('.oxy-mega-dropdown_flyout-click-area').attr('aria-expanded', function (i, attr) {
            return attr == 'true' ? 'false' : 'true'
        });

        $(trigger).children('.oxy-mega-dropdown_flyout-click-area').attr('aria-pressed', function (i, attr) {
            return attr == 'true' ? 'false' : 'true'
        });
    }

    function oxy_megaMenu_toggle(trigger, durationData) {

            var othermenus = $(trigger).parent('.oxy-mega-dropdown').siblings('.oxy-mega-dropdown');
                             othermenus.find( '.oxy-mega-dropdown_inner' ).slideUp( durationData );
                             othermenus.find( '.oxy-mega-dropdown_link' ).removeClass( 'oxy-mega-dropdown_inner-open' );

            $(trigger).next('.oxy-mega-dropdown_inner').slideToggle( durationData );
            $(trigger).toggleClass( 'oxy-mega-dropdown_inner-open' );

            $(trigger).attr('aria-expanded', function (i, attr) {
                return attr == 'true' ? 'false' : 'true'
            });

            $(trigger).attr('aria-pressed', function (i, attr) {
                return attr == 'true' ? 'false' : 'true'
            });

            $(trigger).next('oxy-slide-menu_open');
        
            
         // Resize carousel as opened if found
            if ($(trigger).next('.oxy-mega-dropdown_inner').has('.flickity-enabled')) {
                setTimeout(function() {
                    var carousel = $(trigger).next('.oxy-mega-dropdown_inner').find('.flickity-enabled');
                    if (carousel.length) {
                    var flkty = Flickity.data( carousel[0] );
                        flkty.resize();
                    }
                }, 100);

            }
            

        }  


    $('.oxy-mega-dropdown_just-link').parent('.oxy-mega-dropdown').addClass('oxy-mega-dropdown_no-dropdown')


    var options = {
          attributes: true,
          attributeFilter: ['class'], 
          subtree: true
        },
        observer = new MutationObserver(mCallback);

        function mCallback (mutations) {
          for (var mutation of mutations) {

              if (mutation.type === 'attributes') {

                        if (($(mutation.target).closest('.oxy-mega-menu_inner').has( ".oxy-mega-dropdown_inner-open" ).length) && !($(mutation.target).closest('.oxy-mega-menu_inner').has( ".oxy-mega-dropdown_inner-open.oxy-mega-dropdown_just-link" ).length) ) {
                            $(mutation.target).closest('.oxy-mega-menu_inner').addClass('oxy-mega-menu_active');
                        } else {
                            $(mutation.target).closest('.oxy-mega-menu_inner').removeClass('oxy-mega-menu_active');
                        }

                    }
              }
        }

        var MegaMenus = document.querySelectorAll('.oxy-mega-menu_inner[data-type=container]'); 
        MegaMenus.forEach(MegaMenu => {
            observer.observe(MegaMenu, options);
        }); 


     $(".oxy-mega-dropdown_flyout").each(function(i, oxyDropdown){

        var icon = $(oxyDropdown).data('icon');

        $(oxyDropdown).find('.menu-item-has-children > a').append('<button tabindex="-1" aria-expanded=\"false\" aria-pressed=\"false\" class=\"oxy-mega-dropdown_flyout-click-area\"><svg class=\"oxy-mega-dropdown_flyout-icon\"><use xlink:href=\"#'+ icon + '\"></use></svg><span class=\"screen-reader-text\">Submenu</span></button>');

    });


}