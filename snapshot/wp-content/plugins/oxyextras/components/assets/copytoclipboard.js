jQuery(document).ready(oxygen_extras_copy);
        function oxygen_extras_copy($) {

            let extrasCopyToClipboard = function ( container ) {
            
                $(container).find('.oxy-copy-to-clipboard').each(function( i, copytoclipboard ){

                    let copyText, 
                        changeText = false,
                        copyButton = copytoclipboard.querySelector('.oxy-copy-to-clipboard_marker');

                    let containerEl = document.body;
                    let buttonDelay = copyButton.getAttribute('data-x-delay');

                    
                    if ( copytoclipboard.closest('.oxy-dynamic-list > .ct-div-block') ) {
                        containerEl = copytoclipboard.closest('.oxy-dynamic-list > .ct-div-block');
                    }

                    if ( copytoclipboard.querySelector('[data-x-copied]') ) {
                        changeText = true;
                        copytoclipboard.querySelector('[data-x-copied]').setAttribute('data-x-copy', copytoclipboard.querySelector('[data-x-copied]').innerHTML )
                    }

                    if ( copyButton.hasAttribute('data-x-copy-text') ) {
                        copyText = copyButton.getAttribute('data-x-copy-text')
                    } else {
                        const copyEl = copyButton.hasAttribute('data-x-copy-selector') && null != copyButton.getAttribute('data-x-copy-selector') ? containerEl.querySelector(copyButton.getAttribute('data-x-copy-selector')) : false;
                        if ( !copyEl && !document.querySelector('.oxygen-builder-body') ) { 
                            console.log('OxyExtras: Element to copy not found, check the selector is correct..')
                            copytoclipboard.dispatchEvent(new Event('x_copy:failed'))
                            return
                        }
                        if ( !document.querySelector('.oxygen-builder-body') ) {

                            if ( copyEl.tagName == 'TEXTAREA') {
                                copyText = copyEl.value
                            } else if ( copyEl.tagName == 'INPUT') {
                                copyText = copyEl.value
                            } else {
                                copyText = copyEl.textContent
                            }
                        }
                    }

                    
                    if ( copyText && copyButton.hasAttribute('data-x-hide') ) {
                        copyButton.style.display = 'flex';
                    }

                
                    copytoclipboard.querySelector('button').addEventListener('click', (e) => {

                        e.preventDefault()

                        if ( document.querySelector('.oxygen-builder-body') ) {
                            copytoclipboard.querySelector('button').setAttribute('aria-pressed', 'true');
                            copytoclipboard.querySelector('button').classList.add('oxy-copy-to-clipboard_copied')
                        }

                        if ( copyButton.hasAttribute('data-x-copy-text') ) {
                            copyText = copyButton.getAttribute('data-x-copy-text')
                        } else {
                            
                            let copyEl = copyButton.getAttribute('data-x-copy-selector') ? containerEl.querySelector(copyButton.getAttribute('data-x-copy-selector')) : false;

                            if ( !copyEl && !document.querySelector('.oxygen-builder-body') ) { 
                                console.log('OxyExtras: Element to copy not found, check the selector is correct.')
                                copytoclipboard.dispatchEvent(new Event('x_copy:failed'))
                                return
                            }
                            if ( !document.querySelector('.oxygen-builder-body') ) {

                                if ( copyEl.tagName == 'TEXTAREA') {
                                    copyText = copyEl.value
                                } else if ( copyEl.tagName == 'INPUT') {
                                    copyText = copyEl.value
                                } else {
                                    copyText = copyEl.textContent
                                }
                            }
                        }
                        
                        const textArea = document.createElement("textarea");
                        textArea.value = copyText;
                        textArea.select();
                        textArea.setSelectionRange(0, 99999); 

                        if(navigator.clipboard) {
                            navigator.clipboard.writeText(copyText).then(() => {
                                if (copyText) {
                                    copytoclipboard.querySelector('button').setAttribute('aria-pressed', 'true');
                                    copytoclipboard.querySelector('button').classList.add('oxy-copy-to-clipboard_copied')
                                    copytoclipboard.dispatchEvent(new Event('x_copy:copied'))
                                    
                                    if ( changeText && copytoclipboard.querySelector('[data-x-copied]') ) {
                                        copytoclipboard.querySelector('[data-x-copied]').innerHTML = copytoclipboard.querySelector('[data-x-copied]').getAttribute('data-x-copied');
                                    }
                                    if ( copyButton.hasAttribute('data-x-select') && copyButton.getAttribute('data-x-copy-selector') ) {
                                        window.getSelection().selectAllChildren(containerEl.querySelector(copyButton.getAttribute('data-x-copy-selector')));
                                    }
                                } else {
                                    copytoclipboard.dispatchEvent(new Event('x_copy:empty'))
                                    console.log('OxyExtras: Copy text empty')
                                }
                            }, () => {
                                copytoclipboard.dispatchEvent(new Event('x_copy:failed'))
                                console.log('OxyExtras: Copy failed')
                            });
                        } else {
                            copytoclipboard.dispatchEvent(new Event('x_copy:failed'))
                                console.log('OxyExtras: Copy failed')
                        }

                        setTimeout(() => {
                            copytoclipboard.querySelector('button').setAttribute('aria-pressed', 'false');
                            copytoclipboard.querySelector('button').classList.remove('oxy-copy-to-clipboard_copied')
                            copytoclipboard.dispatchEvent(new Event('x_copy:reset'))
                            if ( changeText && copytoclipboard.querySelector('[data-x-copied]') ) {
                                copytoclipboard.querySelector('[data-x-copied]').innerHTML = copytoclipboard.querySelector('[data-x-copy]').getAttribute('data-x-copy');
                            }
                        }, buttonDelay);

                        
                    })

                    if ( copyButton.hasAttribute('data-x-tooltip' ) ) {

                        let popData = $(copytoclipboard).find('.oxy-copy-to-clipboard_popup');
                        let popoverPlacement = popData.data('placement');

                        let trigger = 'hocus' === copyButton.getAttribute('data-x-tooltip-show' ) ? 'mouseover focus' : 'manual';
                        
                        let elem = $(copytoclipboard).find('.oxy-copy-to-clipboard_marker')[0];
                        
                        let tippyInstance = tippy(elem, {
                            content: $(copytoclipboard).find('.oxy-copy-to-clipboard_popup-inner')[0], 
                            allowHTML: true,     
                            interactive: false, 
                            arrow: true,
                            trigger: trigger,    
                            appendTo: $(copytoclipboard).find('.oxy-copy-to-clipboard_popup')[0],
                            placement: popoverPlacement,
                            maxWidth: 'none',    
                            inertia: true,
                            theme: 'copy-clipboard',     
                            touch: true,
                            showOnCreate: popData.data('show'),  
                            moveTransition: 'transform ' + popData.data('move-transition') + 'ms ease-out', 
                            offset: [parseInt( popData.data('offsetx') ), parseInt( popData.data('offsety') )], 
                            onShown(instance) {
                                tippy.hideAll({exclude: instance});
                                document.addEventListener('keydown', function(e) {
                                    if((e.key === "Escape" || e.key === "Esc")){
                                        tippy.hideAll()
                                    }
                                });
                            }
                        });

                        if ( popData.attr('data-copy-text') && popData.attr('data-copied-text') ) {

                            copytoclipboard.addEventListener('x_copy:copied', () => {
                                tippyInstance.setContent( popData.attr('data-copied-text') )
                                tippyInstance.show()
                            })
            
                            copytoclipboard.addEventListener('x_copy:reset', () => {
                                tippyInstance.hide()
                                setTimeout(() => {
                                 tippyInstance.setContent( popData.attr('data-copy-text') )
                                }, 500);
                            })

                        }

                        

                    }

                });

            }

            extrasCopyToClipboard('body');
                
            // Expose function
            window.doCopyToClipboard = extrasCopyToClipboard;

        }