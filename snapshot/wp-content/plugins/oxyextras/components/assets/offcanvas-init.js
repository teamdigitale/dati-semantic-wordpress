jQuery(document).ready(oxygen_init_offcanvas);
function oxygen_init_offcanvas($) {
    
    let touchEvent = 'click';
    let previousFocus = false;

    'use strict';

    $('.oxy-off-canvas .offcanvas-inner').each(function() {

        var offCanvas = $(this),
            triggerSelector = offCanvas.data('trigger-selector'),
            offCanvasOutside = offCanvas.data('click-outside'),
            offCanvasEsc = offCanvas.data('esc'),
            offCanvasStart = offCanvas.data('start'),
            offCanvasBackdrop = offCanvas.data('backdrop'),
            offCanvasFocusSelector = offCanvas.data('focus-selector'),
            backdrop = offCanvas.prev('.oxy-offcanvas_backdrop'),
            menuHashLink = offCanvas.find('a[href*=\\#]').not(".menu-item-has-children > a"),
            reset = offCanvas.data('reset'),
            mediaPlayer = offCanvas.find('.oxy-pro-media-player vime-player'),
            otherOffcanvas = offCanvas.data('second-offcanvas'),
            maybeHashClose = offCanvas.data('hashclose'),
            offcanvasPush = offCanvas.data('content-push'),
            offcanvasPushContent = offCanvas.data('content-selector'),
            offcanvasPushDuration = offCanvas.data('content-duration'),
            offcanvasID = offCanvas.parent('.oxy-off-canvas').attr('ID'),
            burgerSync = offCanvas.data('burger-sync'),
            maybeOverflow = offCanvas.data('overflow');

            
        if (false !== offCanvas.data('stagger-menu')) {
            offCanvas.find('.oxy-slide-menu_list > .menu-item').addClass('aos-animate');
            offCanvas.find('.oxy-slide-menu_list > .menu-item').attr( { "data-aos": offCanvas.data('stagger-menu') } );
        }

        if (! $(this).hasClass('oxy-off-canvas-toggled') ) {
            $(this).find(".aos-animate:not(.oxy-off-canvas-toggled .aos-animate)").addClass("aos-animate-disabled").removeClass("aos-animate");
        }

        ariaExpandToggle('false');

        if ( true === offCanvas.data('auto-aria') ) {

            $(triggerSelector).each(function(i,trigger) {

                if ( $(trigger).hasClass('oxy-burger-trigger') && $(trigger).children('.hamburger').length ) {
                    $(trigger).children('.hamburger').attr('aria-controls', offCanvas.attr('id'));
                } else {
                    $(trigger).attr('aria-controls', offCanvas.attr('id'));
                }
            });
        }

        if ( true === offCanvas.data('focus-trap') ) {

            const keyboardfocusableElements = offCanvas.find(
                'a[href], button, input, textarea, select, details, [tabindex]'
              );

              if (keyboardfocusableElements.length) {
                 
                keyboardfocusableElements[keyboardfocusableElements.length - 1].addEventListener('keydown', (e) => {

                    if (!e.shiftKey && e.key === 'Tab') {
                        e.preventDefault()
                        keyboardfocusableElements[0].focus()
                    }

                })

                keyboardfocusableElements[0].addEventListener('keydown', (e) => {

                    if (e.shiftKey && e.key === 'Tab') {
                        e.preventDefault()
                        keyboardfocusableElements[keyboardfocusableElements.length - 1].focus()
                    }

                })

              }
        }
        
        function doOffCanvas(triggerSelector) { 
            
            if ($(triggerSelector).hasClass('oxy-close-modal')) {
                oxyCloseModal();
            } 
            
            if (!offCanvas.parent().hasClass('oxy-off-canvas-toggled')) {
                openOffCanvas();
            } else {
                closeOffCanvas();
            }
        }
        
        if ( $(triggerSelector).hasClass('oxy-burger-trigger') && $(triggerSelector).children('.hamburger').length ) {
            
            let triggerSelectorTouch = $( triggerSelector ).children('.hamburger').data('touch');
            let touchEvent = 'ontouchstart' in window ? triggerSelectorTouch : 'click'; 
            
            $(triggerSelector).on(touchEvent, function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                if (true === burgerSync) {
                    $(triggerSelector).not('#' + $(this).attr('ID')).children('.hamburger').toggleClass('is-active');
                }   

                if ( !e.target.closest('.oxy-off-canvas') ) {
                    previousFocus = e.target.closest(triggerSelector);
                }

                doOffCanvas(triggerSelector);
                
            });
        } else {
            
            let triggerSelectorTouch = touchEvent;
            $(triggerSelector).on(triggerSelectorTouch, function(e) {
                e.stopPropagation();
                e.preventDefault();
                doOffCanvas(triggerSelector);
            });
        }


        // Backdrop Clicked
        offCanvas.siblings('.oxy-offcanvas_backdrop').on(touchEvent, function(e) {
            e.stopPropagation();
            if (offCanvasOutside === true) {
                closeBurger();
                closeLottie();
                closeOffCanvas();
            }

        });

        // Pressing ESC from inside the offcanvas will close it
        offCanvas.keyup(function(e) {
            if (offCanvasEsc === true) {
                if (e.keyCode === 27) {
                    closeBurger();
                    closeLottie();
                    closeOffCanvas();
                }
            }
        });
        
        if (maybeHashClose === true) {

            $(document).on("click", '#' + offcanvasID + ' a[href*=\\#]', function (e) {
                
                e.stopPropagation();
                
                if ( ( !$(this).not(".menu-item-has-children > a") ) || $(this).is('[href="#"]' ) ) {
                    return;
                }

                if ( $(this).is(".oxy-table-of-contents_list-item > a") ) {
                    return;
                }

                if ( $(this).is(".mm-btn") || $(this).is(".mm-navbar__title") ) {
                    return;
                }

                
                
                if (this.pathname === window.location.pathname) {
                    closeBurger();
                    closeLottie();
                    closeOffCanvas();
                }
            });
            
        }   
        
        if (offcanvasPush) {
            $(offcanvasPushContent).attr('data-offcanvas-push', '#' + offcanvasID);
            $(offcanvasPushContent).css({
                                        "--offcanvas-push" : offcanvasPush + "px",
                                        "--offcanvas-push-duration" : offcanvasPushDuration + 's',
                                    });
            
        }

        function ariaExpandToggle($state) {
            if ( true === offCanvas.data('auto-aria') ) {
                $(triggerSelector).each(function(i,trigger) {
                    if ( $(trigger).hasClass('oxy-burger-trigger') && $(trigger).children('.hamburger').length ) {
                        $(trigger).children('.hamburger').attr('aria-expanded', $state);
                    } else {
                        $(trigger).attr('aria-expanded', $state);
                        $(trigger).attr('role','button');
                    }
                });
            }
        }

        function inertToggle($state) {
            if ( false !== offCanvas.data('inert') ) {
                if ('true' === $state) {
                    offCanvas.attr('inert','');
                } else {
                    offCanvas.removeAttr('inert');
                }
            }
        }

        function openOffCanvas() {
            offCanvas.parent().addClass('oxy-off-canvas-toggled');
            $(offcanvasPushContent).addClass('oxy-off-canvas-toggled');
            $('body,html').addClass('off-canvas-toggled');
            $('body,html').addClass('toggled' + offcanvasID);

            if (true === offCanvasStart) {
                doClose();
            } else {
                doOpen()
            }
            
        }

        function closeOffCanvas() {
            $('body,html').removeClass('off-canvas-toggled');
            $('body,html').removeClass('toggled' + offcanvasID);
            offCanvas.parent().removeClass('oxy-off-canvas-toggled');
            $(offcanvasPushContent).removeClass('oxy-off-canvas-toggled');

            if (true === offCanvasStart) {
                doOpen()
            } else {
                doClose();
            }
            
        }

        function doOpen() {
                offCanvas.find(".aos-animate-disabled").removeClass("aos-animate-disabled").addClass("aos-animate");
                offCanvas.parent('.oxy-off-canvas').trigger('extras_offcanvas:open');
                
                inertToggle('false')
                setTimeout(function() {
                    offCanvas.attr('aria-hidden','false');
                }, 0);
                
                ariaExpandToggle('true');

                
                
                if (offCanvasFocusSelector) {
                    setTimeout(function() {
                        offCanvas.parent().find(offCanvasFocusSelector).eq(0).focus();
                    }, 0);
                }
        }

        function doClose() {
                inertToggle('true');
                offCanvas.attr('aria-hidden','true');
               
                setTimeout(function(){
                    offCanvas.find(".aos-animate").removeClass("aos-animate").addClass("aos-animate-disabled");
                }, reset); // wait before removing the animate class 
                
                $(offCanvas).parent('.oxy-off-canvas').trigger('extras_offcanvas:close');
                if (otherOffcanvas) {
                    $(otherOffcanvas).children('.offcanvas-inner').attr('aria-hidden','true');
                    $(otherOffcanvas).children('.offcanvas-inner').removeAttr('inert');
                    $(otherOffcanvas).removeClass('oxy-off-canvas-toggled');
                }

                ariaExpandToggle('false');
                
                mediaPlayer.each(function() {
                    $(this)[0].pause(); // turn off any pro media players
                });

                setTimeout(function() {

                    if (!!previousFocus) {

                        if ( $(previousFocus).find('.hamburger') ) {
                            $(previousFocus).find('.hamburger').focus();
                        } else {
                            $(previousFocus).focus();
                        }
                    }
                    
                }, 0);
        }
        
        function closeBurger() {
            
            if ( ( $(triggerSelector).children('.hamburger').length > 0) && ($(this).children('.hamburger').data('animation') !== 'disable')) {
                $(triggerSelector).children('.hamburger').removeClass('is-active');
            }
        }

        function closeLottie() {
            
            if ( ( $(triggerSelector).children('lottie-player').length > 0) ) {
                $(triggerSelector).children('lottie-player').trigger('click');
            }
        }

        /* For programmatically opening */
        function extrasOpenOffcanvas($extras_offcanvas) {
            
            var thisOffcanvas = $($extras_offcanvas);
            var thisoffcanvasPushContent = thisOffcanvas.children('.offcanvas-inner').data('content-selector');
            var thisoffCanvasFocusSelector = thisOffcanvas.children('.offcanvas-inner').data('focus-selector');

            thisOffcanvas.addClass('oxy-off-canvas-toggled');
            $(offcanvasPushContent).addClass('oxy-off-canvas-toggled');
            offCanvas.attr('aria-hidden','false');
            $('body,html').addClass('off-canvas-toggled');
            thisOffcanvas.find(".aos-animate-disabled").removeClass("aos-animate-disabled").addClass("aos-animate");
            thisOffcanvas.trigger('extras_offcanvas:open');
            
            if (thisoffCanvasFocusSelector) {
                thisOffcanvas.find(thisoffCanvasFocusSelector).eq(0).focus();
            }
            
        }

        if (true !== offCanvasStart) {
            offCanvas.attr('aria-hidden','true');
            inertToggle('true');
        } else {
            offCanvas.attr('aria-hidden','false');
        }

        // Expose function
        window.extrasOpenOffcanvas = extrasOpenOffcanvas;
    
            var stagger = offCanvas.data('stagger');
                
            // Make sure AOS animations are reset for elements inside after everything has loaded
            setTimeout(function(){
                if (! offCanvas.hasClass('oxy-off-canvas-toggled') ) {
                    offCanvas.find(".aos-animate:not(.oxy-off-canvas-toggled .aos-animate)").addClass("aos-animate-disabled").removeClass("aos-animate");
                }
            }, 40); // wait
            
            if (stagger != null) {
            
                var delay = offCanvas.data('first-delay');
                offCanvas.find('[data-aos]').not('.not-staggered').each(function() {
                    delay = delay + stagger;
    
                    $(this).attr('data-aos-delay', delay);
                });
    
            }

    });
}

