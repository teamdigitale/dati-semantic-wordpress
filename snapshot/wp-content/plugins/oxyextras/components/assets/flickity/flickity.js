
jQuery(document).ready(function($) {
    
   // Add extra classes for prev and next cells
    Flickity.createMethods.push('_createPrevNextCells');

    Flickity.prototype._createPrevNextCells = function() {
      this.on( 'select', this.setPrevNextCells );
    };

    Flickity.prototype.setPrevNextCells = function() {
      // remove classes
      changeSlideClasses( this.previousSlide, 'remove', 'is-previous' );
      changeSlideClasses( this.nextSlide, 'remove', 'is-next' );
      // set slides
      var previousI = fizzyUIUtils.modulo( this.selectedIndex - 1, this.slides.length );
      var nextI = fizzyUIUtils.modulo( this.selectedIndex + 1, this.slides.length );
      this.previousSlide = this.slides[ previousI ];
      this.nextSlide = this.slides[ nextI ];
      // add classes
      changeSlideClasses( this.previousSlide, 'add', 'is-previous' );
      changeSlideClasses( this.nextSlide, 'add', 'is-next' );
    };

    function changeSlideClasses( slide, method, className ) {
      if ( !slide ) {
        return;
      }
      slide.getCellElements().forEach( function( cellElem ) {
        cellElem.classList[ method ]( className );
      });
    }    
    
    
        // Prevent user from scrolling horizontally in edit mode
        $('.ct-section:has(.oxy-carousel-builder)').css({overflow: 'hidden'});

        $('#%%ELEMENT_ID%%').find('.dot:first').addClass('is-selected');

            let preview = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('preview'),
                prev = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('prev'),
                next = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('next'),
                carouselselector = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('carousel'),
                flickitySelector = '#%%ELEMENT_ID%% ' + carouselselector; 
    
    
    
             
         function buildCarousel() {  
             
                let cellselector = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('cell');
                let carouselselector = $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('carousel');
                let groupCellsOption = '';
             
                if ('.oxy-dynamic-list' === carouselselector) {
                    cellselector = '#' + $('#%%ELEMENT_ID%%').find(carouselselector).children('.ct-div-block').attr('id');
                } 
             
                if ('true' === '%%maybe_group%%') {
                     groupCellsOption = 'true';
                } else if ('percent' === '%%maybe_group%%') {
                     groupCellsOption = '%%group_percent%%' + '%';
                } else if ('false' === '%%maybe_group%%') {
                     groupCellsOption = false;
                } else {
                     groupCellsOption = %%group_cells%%;
                }
             
               

              // get the settings    
              var settings = {
                    contain: %%contain%%,
                    freeScroll: %%free_scroll%%,
                    wrapAround: %%wrap_around%%,
                    groupCells: groupCellsOption,
                    initialIndex: %%initial_index%% - 1,
                    cellAlign: '%%cell_align%%',
                    cellSelector: cellselector,
                    rightToLeft: %%right_to_left%%,
                    pageDots: true,
                    percentPosition: true,
                    autoplay: parseInt('%%autoplay%%'),
                    pauseAutoPlayOnHover: %%maybe_pause_autoplay%%,
                    accessibility: false,
                    draggable: false,
                    prevNextButtons: false,
                    imagesLoaded: true,
                    //adaptiveHeight: adaptheight, //todo make work inside builder
                    adaptiveHeight: false,
                    fullscreen: %%maybe_fullscreen%%,
                    fade: %%maybe_fade%%
                }     

              var flickityCarousel = new Flickity( flickitySelector, settings);

                      $('#%%ELEMENT_ID%%').on( 'click', function(e) { 
                            e.preventDefault();
                            e.stopPropagation();
                              flickityCarousel.next();

                            });

                          $(next).on( 'click', function(e) {
                              e.preventDefault();
                              e.stopPropagation();
                              flickityCarousel.next();

                            });

                        $(prev).on( 'click', function(e) {
                              e.preventDefault();
                              e.stopPropagation();
                              flickityCarousel.previous();
                        }); 


                    /* Parallax Background Images only if not wraparound */
                    if ((true === $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('parallaxbg')) && (false === $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('wraparound'))  ) {

                            
                        
                           if ('.oxy-dynamic-list' === carouselselector) {
                                var $parallaxCells = $('#%%ELEMENT_ID%%').find('.flickity-slider').children('.ct-div-block');
                            } else {
                                var $parallaxCells = $($('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('cell'));
                            }


                            var docStyle = document.documentElement.style;
                            var transformProp = typeof docStyle.transform == 'string' ?
                              'transform' : 'WebkitTransform';


                            function parallaxbg() {     
                                  flickityCarousel.slides.forEach( function( slide, i ) { 

                                    var $parallaxCell = $parallaxCells[i];
                                    var $parallaxElem = $($parallaxCell).find('[data-speed]');

                                    var x = ( slide.target + flickityCarousel.x ); // Cell transform

                                      $parallaxElem.each(function(){ 

                                          var $parallaxSpeed = $(this).attr('data-speed');
                                          var $parallaxElemDom = $(this)[0];
                                          var trans = x * (-1/$parallaxSpeed); // Cell transform * paralax speed

                                          $parallaxElemDom.style[ transformProp ] = 'translateX(' + trans + 'px)';

                                      });      

                                  });
                              }

                            parallaxbg(); // On page load

                            flickityCarousel.on( 'scroll', function() {   
                              parallaxbg(); // When we scroll flickity
                            });

                        } 


                    let $previousButton = $(prev);
                    let $nextButton = $(next);


                     flickityCarousel.on( 'cellSelect', function( event, index ) {
                           disable_nav();
                        });   


                    function disable_nav() {

                        if (false === $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('wraparound')) {

                            if ( !flickityCarousel.cells[ flickityCarousel.selectedIndex - 1 ] ) {
                              $(prev).addClass('oxy-carousel-builder_icon_disabled');
                              $(next).removeClass('oxy-carousel-builder_icon_disabled');
                            } else if ( !flickityCarousel.cells[ flickityCarousel.selectedIndex +1 ] ) {
                              $(next).addClass('oxy-carousel-builder_icon_disabled');
                              $(prev).removeClass('oxy-carousel-builder_icon_disabled');
                            } else {
                              $(prev).removeClass('oxy-carousel-builder_icon_disabled');
                              $(next).removeClass('oxy-carousel-builder_icon_disabled');
                            }
                        }

                    }


                     

                // make sure it's the correct size after inner elements have finished loading
                setTimeout(function(){ 
                        flickityCarousel.resize();
                    }, 1000);
                

            };
    
    
           // Only do any of this if we're in preview mode.
           if ( $('#%%ELEMENT_ID%%').find('.oxy-inner-content').data('preview') === true ) {
               
            var counter = 40;  // prevent indefinite  
            var checkCarouselExist = setInterval(function() { 
                counter--
                
                let flickitySelector = '#%%ELEMENT_ID%% ' + carouselselector;
                
                if (($(flickitySelector).length ) || counter === 0 ) {
                    
                    clearInterval(checkCarouselExist);
                
                    if (('.oxy-inner-content' === carouselselector) || ('.oxy-dynamic-list' === carouselselector)) {

                        setTimeout(function(){ 
                            buildCarousel();
                        }, 2000);
                    } 
                    
                    else {
                        buildCarousel();
                    }
                    
               
                  } // only if the carousel is found
                
                    
                }, 100); // check every 100ms until found, until 4s.    
 
            
          }      //end preview condition      
                    
});