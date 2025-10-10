jQuery(document).ready(oxygen_init_toc);
function oxygen_init_toc($) {


    $('.oxy-table-of-contents').each(function(i, oxyToc){

        var toc = $(oxyToc),
            tocID = toc.attr('ID'),
            inner = toc.children('.oxy-table-of-contents_inner'),
            dropdownIcon = inner.data('dropdown'),
            contentSelector = inner.data('content'),
            contextIcon = inner.data('context-icon'),
            headingSelector = inner.data('headings');
        
        // For removing the focus styles on the headings.
        $(contentSelector).attr('data-toc-container',tocID);

        
        // context icon
        inner.find('.oxy-table-of-contents_link').prepend('<span class=\"oxy-table-of-contents_context-icon\"><svg><use xlink:href=\"#' + dropdownIcon + '\"></use></svg></span>');
        
        
        // If icon
        if (typeof dropdownIcon !== 'undefined') {

            inner.find('.oxy-table-of-contents_list').prev('.oxy-table-of-contents_link').append('<span class=\"oxy-table-of-contents_dropdown-icon\"><svg><use xlink:href=\"#' + dropdownIcon + '\"></use></svg></span>')
              
        };
        
        
        $(this).children('.oxy-table-of-contents_title').on( 'click', function(e) {  

            if (!$('body').hasClass('.oxygen-builder-body')) {
                
                toc.toggleClass('oxy-table-of-contents_toggled');  
                    
                inner.slideToggle(inner.data('animation-duration'));
                
            }
        } );


        // Add ID's dynamically for headings inside content.
        if ( true === inner.data('autoid') ) {

              let headingsArray = headingSelector.split(',');            
              let contentHeadingSelector = headingsArray.map(i => contentSelector + ' ' + i);
              let contentHeadingSelectors = $(contentHeadingSelector.join(", "));

                let tocIDNumbers = tocID.replace(/[^0-9]/g,'');

                let currentIDs = [];
            
                $.each(contentHeadingSelectors , function(index, val) { 

                    if (!val.hasAttribute("id")) {

                        if ( 'text' !== inner.data('autoid-type') ) {
                         
                            $(val).attr('id', inner.data('prefix') + tocIDNumbers + '-' + index);

                        } else {
                            
                            let headingText = val.innerText.toLowerCase().replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '').replace(/ +/g, '-');
            
                            if ( !currentIDs.includes(headingText) ) {
                                val.setAttribute('id', headingText );
                                currentIDs.push(headingText);
                              } else {

                                let newID = headingText + '-' + (currentIDs.filter((v) => (
                                    v === headingText)).length
                                );
                                val.setAttribute('id', newID);
                                currentIDs.push(headingText)

                              }
                            
            
                        }

                    }
                    
                });     
            
        }
        
        // Add Links dynamically for headings inside content.
        if ( true === inner.data('autolink') ) {

            let headingsArray = headingSelector.split(',');            
            let contentHeadingSelector = headingsArray.map(i => contentSelector + ' ' + i);
            let contentHeadingSelectors = $(contentHeadingSelector.join(", "));
            let linkIcon = inner.data('linkicon');
          
            $.each(contentHeadingSelectors , function(index, val) { 
                
                let id = $(val).attr('id');
                let link = '#' + id;
                $(val).addClass('oxy-tbc-copy-link');

                if ($('html').attr('ng-app') == 'CTFrontendBuilder') return;    

                if (!$(val).has( ".oxy-tbc-copy-id" ).length) {
                    $(val).append( "<button class='oxy-tbc-copy-id'><svg class=\"oxy-table-of-contents_heading-icon\"><use xlink:href=\"#" + linkIcon +  "\"></use></svg></button>" );
                }
                
            });     
          
          
            function copyIDToClipboard(element) {
               var $temp = $("<input>");
               $("body").append($temp);
               $temp.val(element).select();
               document.execCommand("copy");
               $temp.remove();
              }
          
                  
            $( contentSelector ).on( "click", ".oxy-tbc-copy-id", function() {
                
                let hashID = window.location.origin + window.location.pathname + '#' + $(this).closest('.oxy-tbc-copy-link').attr('id');
                copyIDToClipboard(hashID);
                
              });
          
        }

        if ( $(inner.data('content')).length ) {
        
            tocbot.init({
                tocSelector: '#' + tocID + ' .oxy-table-of-contents_inner',
                contentSelector: inner.data('content'),
                headingSelector: inner.data('headings'),
                ignoreSelector: inner.data('ignore'),
                collapseDepth: 6 - inner.data('collapse'),
                scrollSmooth: inner.data('scroll'),
                scrollSmoothDuration: inner.data('scroll-duration'),
                scrollSmoothOffset: 0 - inner.data('scroll-offset'),
                headingsOffset: inner.data('scroll-offset'),
                hasInnerContainers: true,
                throttleTimeout: 20,
                positionFixedSelector: ('scroll' === inner.data('positioning')) ? '#' + tocID : null, 
                positionFixedClass: 'oxy-table-of-contents_fixed',
                listClass: 'oxy-table-of-contents_list',
                listItemClass: 'oxy-table-of-contents_list-item',
                linkClass: 'oxy-table-of-contents_link',
                includeHtml: false,
            
            }); 

        } else {
            tocbot.destroy();
        }
        
        //add context area
        inner.find('.oxy-table-of-contents_link').prepend('<span class="oxy-table-of-contents_context-icon"><svg class=""><use xlink:href="#' + contextIcon + '"></use></svg></span>');
        
    });

}